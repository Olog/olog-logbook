/*
 * jQuery File Upload Plugin JS Example 5.0.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: false */
/*global $ */

$(function () {

    // Initialize the jQuery File Upload widget:
    $('div[id^="fileupload_"]').each(function(index,element){
        $(this).fileupload({dropZone: $(this).parent(),
                            type: "POST",
                            beforeSend: function(xhr){
                                    xhr.withCredentials = true;
                                    xhr.setRequestHeader("Authorization",
                                    "Basic "+basic());
                                }
        });
    });
    // Load existing files:
    $('div[id^="fileupload_"]').each(function(index,element){
        $.ajax({
            url: $('div',element).prop('title'),
            type: 'GET', 
            dataType: 'json',
            success: function (response) {
                var fu = $(element).data('fileupload');
                var files = new Array();      
                for(i in response.attachment){
                    var file = {};
                    file.name = response.attachment[i].fileName;
                    file.size = response.attachment[i].fileSize;
                    file.url = $('div',element).prop('title')+"\/"+encodeURIComponent(response.attachment[i].fileName);
                    if (response.attachment[i].thumbnail == true)
                        file.thumbnail_url = $('div',element).prop('title')+"\/"+encodeURIComponent(response.attachment[i].fileName)+":thumbnail";
                    file.delete_url = $('div',element).prop('title')+"\/"+encodeURIComponent(response.attachment[i].fileName);
                    file.delete_type = "DELETE";
                    files.push(file);
                }
                fu._adjustMaxNumberOfFiles(-files.length);
                fu._renderDownload(files)
                    .appendTo($('.files',element))
                   .fadeIn(function () {
                        // Fix for IE7 and lower:
                       $(this).show();
                    });
            }
        });
    });

    // Open download dialogs via iframes,
    // to prevent aborting current uploads:
    $('div[id^="fileupload_"]').each(function(index,element){
        $('.files a:not([target^=_blank])',element).live('click', function (e) {
            e.preventDefault();
            $('<iframe style="display:none;"></iframe>')
                .prop('src', this.href)
                .appendTo('body');
        });
    });
});
