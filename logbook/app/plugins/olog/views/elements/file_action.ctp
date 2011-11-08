<?php
echo $this->Html->script('addUpload.js');
echo $this->Html->script('FileUpload/jquery-ui-1.8.13.custom.min');
echo $this->Html->script('FileUpload/jquery.iframe-transport');
echo $this->Html->script('FileUpload/jquery.fileupload');
echo $this->Html->script('FileUpload/jquery.fileupload-ui');
echo $this->Html->script('FileUpload/jquery.application');
echo $this->Html->script('FileUpload/jquery.tmpl.min');
?>

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