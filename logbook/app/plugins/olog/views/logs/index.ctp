

<?php
//echo $ajax->remoteTimer( 
//    array( 
//    'url' => array( 'controller' => 'logs', 'action' => 'index'), 
//    'update' => 'test', 'frequency' => 10
//    )
//);
?>
<div id='logsFormAdd' align='right' >
    <a><img id="closeNewLog" style="display:none" src="<?php echo $base; ?>/img/blue-document--minus.png" alt="close new log" class="NewLog_icons" /></a>
    <a><img id="addNewLog" src="<?php echo $base; ?>/img/blue-document--plus.png" alt="add new log" class="NewLog_icons" /></a>
</div>

<?php
echo $this->Html->script('addUpload.js');
echo $this->Html->script('Supa.js');
?>
<?php //echo $this->Html->link(__('Configure application', true), array('controller' => 'searches', 'action' => 'search')); ?>
<div class="logs index">
    <div id="logForm" class="logs form" style="display: none" >


        <?php echo $this->Form->create('log', array('id' => 'mainFormId', 'type' => 'file', 'action' => 'add')); ?>
        <fieldset id='logFormFieldset'>
            <legend><?php __('Add a New Log'); ?></legend>
            <div id='logFormCredentials'>
                <?php if (!$session->check('Auth.User.name')) {
                    ?>
                    <div id='logFormUsername'><?php echo $this->Form->input('username', array('label' => 'Username')); ?></div>
                    <div id='logFormPassword'><?php echo $this->Form->input('password', array('label' => 'Password')); ?></div>
                <?php } ?>
            </div>
            <div id='logFormContainer'>
                <div id='logFormInfo'>

                    <?php echo $this->Form->input('subject', array('type' => 'hidden')); ?>
                    <div id='logFormDescription' style="resize: none">
                        <?php echo $this->Form->input('description', array('type' => 'textarea', 'rows' => '15')); ?>
                    </div>
                </div>
                <div id='logFormSelects'>
                    <div id='logFormLevels'><?php echo $this->Form->input('level'); ?></div>
                    <div id='logFormLogbooks'><?php echo $this->Form->input('logbooks', array('type' => 'select', 'multiple' => true, 'id' => 'logbook_select')); ?></div>
                    <div id='logFormTags'><?php echo $this->Form->input('tags', array('type' => 'select', 'multiple' => true)); ?></div>
                    <div id='logFormSubmit'>
                        <?php echo $form->submit('submit', array('disabled' => true)); ?>
                    </div>
                </div>
            </div>
            <?php echo $form->end(); ?>
            <div style="display:none" class="addFiles" id="fileupload_<?php //echo $log['id'];                        ?>">
                <form action="<?php echo $base; ?>/olog/uploads/index/id:<?php //echo $log['id'];                        ?>" method="POST" enctype="multipart/form-data">
                    <label class="fileinput-button">
                        <span>Add files</span>
                        <input type="hidden" name="id" value="<?php //echo $log['id'];                        ?>" />
                        <input type="file" name="file" />
                    </label>
                </form>
                <div class="fileupload-content">
                    <table class="files"></table>
                    <div class="fileupload-progressbar"></div>
                    <script id="template-upload" type="text/x-jquery-tmpl">
                        <tr class="template-upload{{if error}} ui-state-error{{/if}}">
                            <td class="preview"></td>
                            <td class="name">${name}</td>
                            <td class="size">${sizef}</td>
                            {{if error}}
                            <td class="error" colspan="2">Error:
                                {{if error === 'maxFileSize'}}File is too big
                                {{else error === 'minFileSize'}}File is too small
                                {{else error === 'acceptFileTypes'}}Filetype not allowed
                                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                                {{else}}${error}
                                {{/if}}
                            </td>
                            {{else}}
                            <td class="progress"></td>
                            <td class="start"><button>Start</button></td>
                            {{/if}}
                            <td class="cancel"><button>Cancel</button></td>
                        </tr>
                        </script>
                    </div>
                </div>
            </fieldset>
        </div>
        <script type="text/javascript" >
            // For the Supa screenshot paste client
            function paste(logid) {
                var s = new supa();
                // Call the paste() method of the applet.
                // This will paste the image from the clipboard into the applet :)
                try {
                    var applet = document.getElementById( "SupaApplet" );

                    if( !s.ping( applet ) ) {
                        throw "SupaApplet is not loaded (yet)";
                    }

                    var err = applet.pasteFromClipboard(); 
                    switch( err ) {
                        case 0:
                            /* no error */
                            break;
                        case 1: 
                            alert( "Unknown Error" );
                            break;
                        case 2:
                            alert( "Empty clipboard" );
                            break;
                        case 3:
                            alert( "Clipboard content not supported. Only image data is supported." );
                            break;
                        case 4:
                            alert( "Clipboard in use by another application. Please try again in a few seconds." );
                            break;
                        default:
                            alert( "Unknown error code: "+err );
                    }
                } catch( e ) {
                    alert(e);
                    throw e;
                }

                return false;
            }
            
            function upload(logid) {
                // Get the base64 encoded data from the applet and POST it via an AJAX 
                // request. See the included Supa.js for details
                var s = new supa();
                var applet = document.getElementById( "SupaApplet" );

                try { 
                    $('#message').html('<img src=\'../img/loading.gif\' />Please wait...');
                    var result = s.ajax_post( 
                    applet,       // applet reference
                    "<?php echo $base; ?>/olog/uploads/index/id:" + logid, // call this url
                    "file", // this is the name of the POSTed file-element
                    "<?php echo date('dmyGis'); ?>" + ".jpg" // this is the filename of tthe POSTed file
                );
                    
                    if( result ) {
                        location.reload();
                    } 

                } catch( ex ) {
                    if( ex == "no_data_found" ) {
                        alert( "Please paste an image first" );
                    } else {
                        alert( ex );
                    }
                }

                return result; // prevent changing the page
            }
        </script>

        <script type="text/javascript" >
            $('#logbook_select').click(function(){
                $('input[type=submit]').attr('disabled', false);
            });
            $('#mainFormId').submit(function(){
                $('input[type=submit]', this).attr('enabled', 'disabled');
            });
            $('#addNewLog').click(function() {
                $('#logForm').show('fast');
                $('#addNewLog').hide();
                $('#closeNewLog').show();
            });
            $('#closeNewLog').click(function() {
                $('#logForm').hide();
                $('#closeNewLog').hide();
                $('#addNewLog').show();
            });
    
            $(document).ready(function() {
                $("input[type=file]").filestyle({ 
                    image: "<?php echo $base; ?>/img/image--plus.png",
                    imageheight : 16,
                    imagewidth : 16,
                    width : 16,
                    showFilename : false
                });
            });
        </script>

        <div id="logviews">
            <?php // Todo:  toggle, when Threaded selected, display collapse/expand   ?>
            <?php //echo $this->Html->link(__('Full', true), array('action' => 'add')) . ' | '; ?>
            <?php //echo $this->Html->link(__('Summary', true), array('action' => 'add')) . ' | '; ?>
            <?php //echo $this->Html->link(__('Threaded', true), array('action' => 'threaded')); ?>

            <span id="quickfilters">
                <?php
                //  Todo:  Update logs on change of select box
                //  attach this to something
                //  also check params for current
                $timespans = array('All', 'Last day',
                    'Last 3 Days',
                    'Last week',
                    'Last month',
                    'Last 3 Months',
                    'Last 6 Months',
                    'Last year'
                );

                echo '<input size="100" style="margin-bottom:5px;" type="text" name="search" id="search" />';
                if (isset($this->params['named']['start']) && isset($this->params['named']['end'])) {
                    $seconds = $this->params['named']['start'];
                    if (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 1, date('y'))) <= 60) {  // Last day
                        $timeOption = 1;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 3, date('y'))) <= 60) { // Last 3 days
                        $timeOption = 2;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 7, date('y'))) <= 100) { // Last week
                        $timeOption = 3;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 1, date('d'), date('y'))) <= 100) { // Last month
                        $timeOption = 4;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 3, date('d'), date('y'))) <= 100) { // Last 3 months
                        $timeOption = 5;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 6, date('d'), date('y'))) <= 500) { // Last 6 months
                        $timeOption = 6;
                    } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d'), date('y') - 1)) <= 500) { // Last year
                        $timeOption = 7;
                    }
                    echo $this->Form->select('timespan', $timespans, $timeOption, array('id' => 'timespan'));
                } else {
                    echo $this->Form->select('timespan', $timespans, 0, array('id' => 'timespan'));
                }
                if (isset($this->params['named']['logbook'])) {
                    echo $this->Form->select('logbook', $logbooks, array($this->params['named']['logbook']), array('id' => 'logbook'));
                } else {
                    echo $this->Form->select('logbook', $logbooks, null, array('id' => 'logbook'));
                }
                ?>
            </span>
        </div>
        <table style="border-top:1px solid #ccc;" cellpadding="0" cellspacing="0">

            <?php
            $i = 0;
            $j = 0;

            if (empty($logs['logs']['log'][0])) {
                if (empty($logs['logs']['log'])) {
                    //somehow say there are no logs
                } else {
                    $temp = $logs['logs']['log'];
                    unset($logs);
                    $logs['logs']['log'][0] = $temp;
                }
            }

            foreach ($logs['logs']['log'] as $log):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="subject">
                        <span><?php echo date('d M Y H:i', strtotime($log['createdDate'])) . ', ' . $log['owner']; ?></span>
                        <span class="tag">
                            <?php
                            if (!empty($log['tags']))
                                echo '<img src="' . $base . '/img/tag-medium.png" />&nbsp;';
                            foreach ($log['tags'] as $tags) {
                                if (isset($tags['name'])) {
                                    echo $tags['name'];
                                } else {
                                    foreach ($tags as $tag) {
                                        if (isset($tag['name'])) {
                                            echo $tag['name'] . '&nbsp;,&nbsp;';
                                        }
                                    }
                                }
                            }
                            ?></span>
                        <span class="logbook">
                            <img src="<?php echo $base; ?>/img/17px-Nuvola_apps_bookcase_1_blue.png" />&nbsp;<?php
                        foreach ($log['logbooks'] as $logbooks) {
                            if (isset($logbooks['name'])) {
                                echo $logbooks['name'];
                            } else {
                                foreach ($logbooks as $logbook) {
                                    if (isset($logbook['name'])) {
                                        echo $logbook['name'] . '&nbsp;,&nbsp;';
                                    }
                                }
                            }
                        }
                            ?>,&nbsp; </span>
                        <div class="level"><?php echo $log['level'] ?></div>
                        <div class="edited"><?php if ($log['version'] > 0)
                            echo '[edited] ' . date('d M Y H:i', strtotime($log['modifiedDate'])); ?></div>
                        <div class='description'><?php echo (!empty($log['description']) ? nl2br($log['description']) : ''); ?></div>

                        <div id="fileupload_<?php echo $log['id'] ?>" >
                            <div class="files" title="<?php echo $base; ?>/olog/uploads/index/id:<?php echo $log['id']; ?>"/>
                            <script id="template-download" type="text/x-jquery-tmpl">
                                <tr class="template-download{{if error}} ui-state-error{{/if}}">
                                    {{if error}}
                                    <td></td>
                                    <td class="name">${name}</td>
                                    <td class="size">${sizef}</td>
                                    <td class="error" colspan="2">Error:
                                        {{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
                                        {{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
                                        {{else error === 3}}File was only partially uploaded
                                        {{else error === 4}}No File was uploaded
                                        {{else error === 5}}Missing a temporary folder
                                        {{else error === 6}}Failed to write file to disk
                                        {{else error === 7}}File upload stopped by extension
                                        {{else error === 'maxFileSize'}}File is too big
                                        {{else error === 'minFileSize'}}File is too small
                                        {{else error === 'acceptFileTypes'}}Filetype not allowed
                                        {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                                        {{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
                                        {{else error === 'emptyResult'}}Empty file upload result
                                        {{else}}${error}
                                        {{/if}}
                                    </td>
                                    {{else}}
                                    {{if thumbnail_url}}
                                    <td class="preview">
                                        <a href="${url}" target="_blank"><img src="${thumbnail_url}" /></a>
                                    </td>
                                    {{else}}
                                    <td class="name">
                                        <a href="${url}" target="_blank">${name}</a>
                                    </td>
                                    {{/if}}
                                    <td colspan="2"></td>
                                    {{/if}}

                                </tr>
                                </script>
                            </div>
                            <div id='copypaste_<?php echo $log['id']; ?>'>
                                <div id="placeholder_<?php echo $log['id']; ?>">
                                </div>
                            </div>

                            <div class="actionButton">
                                <form style='padding: 0px' action="<?php echo $base; ?>/olog/uploads/index/id:<?php echo $log['id']; ?>" method="POST" enctype="multipart/form-data">
                                    <input type="file" name="file" id="fileItem">


                                    <input type="hidden" name="id" value="<?php echo $log['id']; ?>" />
                                    <a style="padding: 0px 0px 0px 20px;" href="<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/edit/' . $log['id']; ?>">
                                        <img border="0" src="<?php echo $base; ?>/img/blue-document--pencil.png" alt="edit" />
                                    </a>
                                    <span style="padding: 0px 0px 0px 0px;" id="componentAdd_<?php echo $log['id']; ?>">
                                        <img border="0" src="<?php echo $base; ?>/img/task--plus.png" alt="component" />
                                    </span>
                                    <span style="padding: 0px 0px 0px 0px;" logid="<?php echo $log['id']; ?>" id="imageAdd_<?php echo $log['id']; ?>">
                                        <img border="0" src="<?php echo $base; ?>/img/clipboard--plus.png" alt="paste from clipboard" />
                                    </span>
                                </form>
                            </div>
                            <script type="text/javascript" >
                                $('.edit_log').click(function() {
                                    $('#logForm').show('fast');
                                    $('#addNewLog').hide();
                                    $('#closeNewLog').show();
                                    $.getJSON(this.title);
                                });
                            </script>

                    </tr>
                <?php endforeach; ?>
            </table>
            <p>
                <?php
                echo $this->Paginator->counter(array(
                    'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
                ));
                ?>	</p>

            <div class="paging">
                <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
                | 	<?php echo $this->Paginator->numbers(); ?>
                |
                <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
            </div>
        </div>
        <script type="text/javascript">
            $('#logbook').bind('change', function() {
                var logbookType = $('#logbook').val();
                if(logbookType!=''){
                    logbookType='logbook:'+logbookType;
                }
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'logbook') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/'; ?>' + logbookType + '<?php echo $args; ?>');
    });
    
    $('#timespan').bind('change', function() {
        var newTimeSpan = $('#timespan').val();
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller']; ?>+ /timespanChange/' + newTimeSpan + '<?php echo $argumentString; ?>');
    });
    
    $('#search').bind('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            var search = $('#search').val();
            if(search!=''){
                search='search:'+'*'+search+'*';
            }
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'search') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
            window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/'; ?>' + search + '<?php echo $args; ?>');
        }
    }).watermark('Search...');
        </script>
        <script>
            $(function() {
                $( 'span[id^="componentAdd_"]' ).click(function() {
                    var $div = $('<div title="Add Component"></div>');
                    $div.load('http://localhost/applet.html', function(){
                        $div.dialog({
                            modal: true
                        });
                    });
                    return false;
                });
            });
            
            $(function() {
                $( 'span[id^="imageAdd_"]' ).click(function() {
                    var logid = $(this).attr("logid");
                    
                    document.getElementById('copypaste_' + logid).removeChild(document.getElementById('placeholder_' + logid));
                                
                    var input = document.createElement('input');
                    input.setAttribute('id', 'pastebutton');
                    input.setAttribute('type', 'button');
                    input.setAttribute('value', 'Paste');
                    input.setAttribute('onclick', 'return paste(' + logid + ');');
                    
                    var upload = document.createElement('input');
                    upload.setAttribute('id', 'uploadbutton');
                    upload.setAttribute('type', 'button');
                    upload.setAttribute('value', 'Upload');
                    upload.setAttribute('onclick', 'return upload(' + logid + ');')
                    
                    var message = document.createElement('div');
                    message.setAttribute('id', 'message');

                    var applet = document.createElement('applet');
                    applet.setAttribute('id', 'SupaApplet');
                    applet.setAttribute('archive', '<?php echo $html->url("/applets/Supa.jar"); ?>');
                    applet.setAttribute('code', 'de.christophlinder.supa.SupaApplet');
                    applet.setAttribute('height', '100');
                    applet.setAttribute('width', '150');
                    
                    var param1 = document.createElement('param');
                    param1.setAttribute('name', 'encoding');
                    param1.setAttribute('value', 'base64');
                    
                    var param2 = document.createElement('param');
                    param2.setAttribute('name', 'previewscaler');
                    param2.setAttribute('value', 'fit to canvas');
                    
                    applet.appendChild(param1);
                    applet.appendChild(param2);
                
                    document.getElementById('copypaste_' + logid).appendChild(input);
                    document.getElementById('copypaste_' + logid).appendChild(upload);
                    document.getElementById('copypaste_' + logid).appendChild(applet);
                    document.getElementById('copypaste_' + logid).appendChild(message);
                });
            });
        </script>
        <?php
        echo $this->Html->script('FileUpload/jquery-ui-1.8.13.custom.min');
        echo $this->Html->script('FileUpload/jquery.iframe-transport');
        echo $this->Html->script('FileUpload/jquery.fileupload');
        echo $this->Html->script('FileUpload/jquery.fileupload-ui');
        echo $this->Html->script('FileUpload/jquery.application');
        echo $this->Html->script('FileUpload/jquery.tmpl.min');
        ?>
