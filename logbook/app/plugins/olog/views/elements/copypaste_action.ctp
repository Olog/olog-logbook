<?php
echo $this->Html->script('Supa.js');
        
        $dbinfo = get_class_vars('DATABASE_CONFIG');
        $parse_url = $dbinfo['olog'];
        $service_url = ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
				.((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
				.((isset($parse_url['host'])) ? $parse_url['host'] : '')
				.((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
				.((isset($parse_url['path'])) ? '/'.$parse_url['path'] : '');
        
?>

<div id="copypaste_<?php echo $logid; ?>">
    <div id="placeholder_<?php echo $logid; ?>"></div>
</div>
<script type="text/javascript" >
    $(function() {
        $( 'span[id^="imageAdd_"]' ).click(function() {
            var logid = $(this).attr("logid");
                    
            document.getElementById('copypaste_' + logid).removeChild(document.getElementById('placeholder_' + logid));
                    
            var container = document.createElement('div');        
                    
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
            
            var param3 = document.createElement('param');
            param3.setAttribute('name', 'imagecodec');
            param3.setAttribute('value', 'jpg');
                    
            applet.appendChild(param1);
            applet.appendChild(param2);
            applet.appendChild(param3);
                
            container.appendChild(input);
            container.appendChild(upload);
            container.appendChild(applet);
            container.appendChild(message);
            
            document.getElementById('copypaste_' + logid).appendChild(container);
        });
    });
    
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
        var s = new Supa();
        var applet = document.getElementById( "SupaApplet" );
        if( !s.ping( applet ) ) {
            throw "SupaApplet is not loaded (yet)";
        }

        var encodedData = applet.getEncodedString();
        if (!encodedData) {
            alert(LANG['plugins']['supa']['err_paste_image_first']);
            return;
        }

        options=new Object();
        options.params = new Array();
        options.params["id"]=logid;
        try { 
            $('#message').html('<img src=\'../img/loading.gif\' />Please wait...');
            var result = s.ajax_post( 
            encodedData,       // applet reference
            "<?php echo $service_url; ?>/attachments/" + logid, // call this url
            "file", // this is the name of the POSTed file-element
            "<?php echo date('dmyGis'); ?>" + ".jpg", // this is the filename of tthe POSTed file
            options
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