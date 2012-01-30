<?php
$dbinfo = get_class_vars('DATABASE_CONFIG');
$service = $dbinfo['irmis']['service'];
?>

<div id="modalContainer"><div id="appletContainer"></div>
    <table width="500" border="0" cellspacing="0" cellpadding="0" style="display:none">
        <tr>
            <td><input name="componentLogId" disabled id="componentLogId"></td>
            <td><input name="componentType" disabled id="componentTypeId"></td>
            <td><input name="serialNumber" disabled id="serialNumberId"></td>
            <td><input name="fieldName" disabled id="fieldNameId"></td>
            <td><input name="hierarchy" disabled id="hierarchyId"></td>
        </tr>
    </table>
</div>

<script type="text/javascript" >
    function register(count){
        var minus; //count-- is not supported?
        try {
            document.applet1.registerEvent("valueChanged","componentChanged");
        }catch (ex) {
            if (count > 0) {
                minus = count-1;
                setTimeout(function(){register(minus);},2000);
            } else {
                alert(ex);
            }
        }
    }
    function componentChanged(res1) {
        $('#componentTypeId').val(res1.getNewValue().getComponentType().getName());
        $('#serialNumberId').val(res1.getNewValue().getSerialNumber());
        $('#fieldNameId').val(res1.getNewValue().getFieldName());
	    
        var housing = document.applet1.Packages.gov.bnl.irmis.components.api.RelType.valueOf('HOUSING');
        var str = res1.getNewValue().getFieldName();
        if (str == null) str = res1.getNewValue().getComponentType().getName();
        var list = res1.getNewValue().getParents(housing).iterator();
        var current;
        while(list.hasNext()){
            current = list.next();
            if (current.getParent().getFieldName() == null){
                str = current.getParent().getComponentType().getName()+":"+str;
            } else {
                str = current.getParent().getFieldName()+":"+str;
            }
            list = current.getParent().getParents(housing).iterator();
        }
        $('#hierarchyId').val(str);
    }
    
    $(function() {
        $( 'span[id^="componentAdd_"]' ).click(function() {
            if(window.opera){
                var appletContainer=document.getElementById('modalContainer');
                appletContainer.innerHTML='<div title="Add Components"><applet code="MyComponentBrowserApplet"'+
                    'archive="<?php echo $service; ?>IRMISApplets/myComponentBrowserApplet.jar,<?php echo $service; ?>IRMISApplets/IRMISComponentsApplets.jar,<?php echo $service; ?>IRMISApplets/IRMISComponentsAPI.jar"'+
                    'width="230"'+
                    'height="300"'+
                    'id="applet1"'+
                    '>'+
                    '<param name="irmis.service" value="<?php echo $service; ?>IRMISComponentsService" />'+
                    '<param name="java.util.logging.config.file" value="<?php echo $service; ?>IRMISComponents/logging.properties" />'+
                    '<param name="progressbar" value="true" />'+
                    '<param name="draggable" value="true" />'+
                    '<param name="separate_jvm" value="true">'+
                    '<param name="image" value="img/image--plus.png">'+
                    '<param name="boxborder" value="false">'+
                    '<param name="centerimage" value="true">'+
                    '</applet></div>';
            }else{
                document.getElementById('modalContainer').removeChild(document.getElementById('appletContainer'));
                var div=document.createElement('div');
                div.setAttribute('title','Add Components');
                div.setAttribute('id','appletContainer')
                var a=document.createElement('applet');
                a.setAttribute('code','MyComponentBrowserApplet');
                a.setAttribute('archive','<?php echo $service; ?>IRMISApplets/myComponentBrowserApplet.jar,<?php echo $service; ?>IRMISApplets/IRMISComponentsApplets.jar,<?php echo $service; ?>IRMISApplets/IRMISComponentsAPI.jar');
                a.setAttribute('width','265');
                a.setAttribute('height','300');
                a.setAttribute('id','applet1');
                param1=document.createElement('param');
                param1.setAttribute('name','irmis.service');
                param1.setAttribute('value','<?php echo $service; ?>IRMISComponentsService');
                param2=document.createElement('param');
                param2.setAttribute('name','java.util.logging.config.file');
                param2.setAttribute('value','<?php echo $service; ?>IRMISComponents/logging.properties');
                param3=document.createElement('param');
                param3.setAttribute('name','progressbar');
                param3.setAttribute('value','true');
                param4=document.createElement('param');
                param4.setAttribute('name','draggable');
                param4.setAttribute('value','true');
                param5=document.createElement('param');
                param5.setAttribute('name','separate_jvm');
                param5.setAttribute('value','true');
                param6=document.createElement('param');
                param6.setAttribute('name','boxborder');
                param6.setAttribute('value','false');
                param7=document.createElement('param');
                param7.setAttribute('name','centerimage');
                param7.setAttribute('value','true');
                a.appendChild(param1);
                a.appendChild(param2);
                a.appendChild(param3);
                a.appendChild(param4);
                a.appendChild(param5);
                a.appendChild(param6);
                a.appendChild(param7);
                div.appendChild(a);
                document.getElementById('modalContainer').appendChild(div);
            }
            $('#modalContainer').dialog({
                modal:true,
                title: "add component",
                buttons:[{
                        id:"addComponentButton",
                        text:"add component",
                        click: function(){
                            var logId = $('#componentLogId').val()
	       
                            var readNumber = $('#log_'+logId+' .maxComponent').html();
                            var componentNumber;
                            if (isNaN(parseInt(readNumber))){
                                componentNumber = 1;
                            } else {
                                componentNumber = parseInt(readNumber)+1;
                            }
                            var params = {};
                            params['propName'] = "Component"; 
                            params["Field Name"] = $('#fieldNameId').val();
                            params["Serial Number"] = $('#serialNumberId').val();
                            params["Hierarchy"] = $('#hierarchyId').val();
                            params["Type"] = $('#componentTypeId').val();
                            params["logId"] = $('#componentLogId').val();
                            $.post('<?php echo $base . "/" . $this->params['plugin'] . "/" . $this->params['controller'] . "/addproperty" ?>', params)
                            .success(function(data, textStatus) {
                                $(this).dialog('close');
                                //alert(data);
                                window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/' . $args; ?>');
                            })
                            .fail(function() {
                                alert("fail");
                            });
                            // above redirect causes error to be thrown
                            //.error(function(jqXHR, textStatus, errorThrown) { alert("error: component was not saved"+errorThrown);});
                        }
                    }]
            });
            register(10);
            $('#componentLogId').val($(this).prop('title'));
            return false;
        });
    });
</script>