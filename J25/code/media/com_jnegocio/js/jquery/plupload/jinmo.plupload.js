/**
 * jInmo mootools Class
 * 
 * @package		Joomla
 * @subpackage	jInmo
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

	function handleUpStatus(up, file, info, chunk) {
        // Called when a file or chunk has finished uploading
        var rspObj = jQuery.parseJSON(info.response);
        var statusMsg = '';
        var fileString = '';
        var spanClass = '';

        if(rspObj.error == 1) {
        	jQuery('#' + file.id).attr('class', 'plupload_failed');
            file.hint = rspObj.msg;
            file.status = plupload.FAILED;
            file.percent = 0;

            up.total.size-= file.size;
            up.total.percent-=100;
            up.total.uploaded-=1;
            spanClass = 'failed_uploading';
        } else {
        	jQuery('#' + file.id).attr('class', 'plupload_done');
            file.status = plupload.DONE;
            spanClass = 'success_uploading';
        }
        
        statusMsg+= '<span class="' + spanClass + '">';
        statusMsg+= ' Status: ';
        statusMsg+= (file.status == plupload.DONE) ? 'DONE' : 'FAILED ';
        statusMsg+= ' Code: ' + rspObj.code + ' : '+ rspObj.msg;
        statusMsg+= '</span>';

        fileString+= ' Id: ' + file.id + ' Name: ' + file.name + ' Size: ' + file.size + ' Loaded: ' + file.percent + '% ';
        fileString+= statusMsg;
        if(!chunk){
            jinmo_js_log('<b>[FileUploaded]</b> ' + fileString);
        } else {
        	jinmo_js_log('<b>[ChunkUploaded]</b> File:' + fileString);
        }
        
    }

	function ajaxReq(dataString, action) {
		var msgCont = jQuery('#system-message-container');
		msgCont.html('<span class="loading"></span>');

		jQuery.ajax({
			type: 'POST',  
			url: action, 
			data: dataString,
			dataType : 'json',
			success: function(response) {
                msgCont.html(' ');
                var msgHTML = '';
				if(response.error == 1) {
					msgHTML+= '<dl id="system-message">';
					msgHTML+= '<dt class="error">Error</dt>';
					msgHTML+= '<dd class="error message"><ul><li>' + response.msg + '</li></ul></dd>';
					msgHTML+= '</dl>';
				} else {
					msgHTML+= '<dl id="system-message">';
					msgHTML+= '<dt class="message">Error</dt>';
					msgHTML+= '<dd class="message message"><ul><li>' + response.msg + '</li></ul></dd>';
					msgHTML+= '</dl>';
				}
				msgCont.html(msgHTML);
			}
		});
	}
	
	//log events
	function jinmo_js_log() {
		var str = "";
		plupload.each(arguments, function(arg) {
            var row = "";
            if (typeof(arg) != "string") {
                plupload.each(arg, function(value, key) {
                    // Convert items in File objects to human readable form
                    if (arg instanceof plupload.File) {
                        // Convert status to human readable
                        switch (value) {
                            case plupload.QUEUED:
                                value = 'QUEUED';
                                break;

                            case plupload.UPLOADING:
                                value = 'UPLOADING';
                                break;

                            case plupload.FAILED:
                                value = 'FAILED';
                                break;

                            case plupload.DONE:
                                value = 'DONE';
                                break;
                        }
                    }

                    if (typeof(value) != "function") {
                    	row += (row ? ', ' : '') + key + ' = ' + value; 
                    }
                });
                str += row + " ";
            } else {
            	str += arg + " ";
            }
		});

		jQuery('#log').prepend(str + '<span class="log_sep"></span>');
	}	
