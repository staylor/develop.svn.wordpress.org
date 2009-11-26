function fileDialogStart(){jQuery("#media-upload-error").empty()}function fileQueued(a){jQuery(".media-blank").remove();if(jQuery("form.type-form #media-items").children().length==1&&jQuery(".hidden","#media-items").length>0){jQuery(".describe-toggle-on").show();jQuery(".describe-toggle-off").hide();jQuery(".slidetoggle").slideUp(200).siblings().removeClass("hidden")}jQuery("#media-items").append('<div id="media-item-'+a.id+'" class="media-item child-of-'+post_id+'"><div class="progress"><div class="bar"></div></div><div class="filename original"><span class="percent"></span> '+a.name+"</div></div>");jQuery(".progress","#media-item-"+a.id).show();jQuery("#insert-gallery").attr("disabled","disabled");jQuery("#cancel-upload").attr("disabled","")}function uploadStart(a){return true}function uploadProgress(e,b,d){var a=jQuery("#media-items").width()-2,c=jQuery("#media-item-"+e.id);jQuery(".bar",c).width(a*b/d);jQuery(".percent",c).html(Math.ceil(b/d*100)+"%");if(b==d){jQuery(".bar",c).html('<strong class="crunching">'+swfuploadL10n.crunching+"</strong>")}}function prepareMediaItem(c,a){var d=(typeof shortform=="undefined")?1:2,b=jQuery("#media-item-"+c.id);jQuery(".bar",b).remove();jQuery(".progress",b).hide();if(isNaN(a)||!a){b.append(a);prepareMediaItemInit(c)}else{b.load("async-upload.php",{attachment_id:a,fetch:d},function(){prepareMediaItemInit(c);updateMediaForm()})}}function prepareMediaItemInit(b){var a=jQuery("#media-item-"+b.id);jQuery(".thumbnail",a).clone().attr("className","pinkynail toggle").prependTo(a);jQuery(".filename.original",a).replaceWith(jQuery(".filename.new",a));jQuery("a.toggle",a).click(function(){jQuery(this).siblings(".slidetoggle").slideToggle(350,function(){var c=jQuery(this).offset();window.scrollTo(0,c.top-36)});jQuery(this).siblings(".toggle").andSelf().toggle();jQuery(this).siblings("a.toggle").focus();return false});jQuery("a.delete",a).click(function(){jQuery.ajax({url:"admin-ajax.php",type:"post",success:deleteSuccess,error:deleteError,id:b.id,data:{id:this.id.replace(/[^0-9]/g,""),action:"trash-post",_ajax_nonce:this.href.replace(/^.*wpnonce=/,"")}});return false});jQuery("a.undo",a).click(function(){jQuery.ajax({url:"admin-ajax.php",type:"post",id:b.id,data:{id:this.id.replace(/[^0-9]/g,""),action:"untrash-post",_ajax_nonce:this.href.replace(/^.*wpnonce=/,"")},success:function(d,e){var c=jQuery("#media-item-"+b.id);if(type=jQuery("#type-of-"+b.id).val()){jQuery("#"+type+"-counter").text(jQuery("#"+type+"-counter").text()-0+1)}if(c.hasClass("child-of-"+post_id)){jQuery("#attachments-count").text(jQuery("#attachments-count").text()-0+1)}jQuery(".filename .trashnotice",c).remove();jQuery("a.undo",c).addClass("hidden");jQuery("a.describe-toggle-on, .menu_order_input",c).show();c.animate({backgroundColor:"#fff"},{queue:false,duration:300,complete:function(){jQuery(this).css({backgroundColor:""})}}).removeClass("trash-undo")}});return false});jQuery("#media-item-"+b.id+".startopen").removeClass("startopen").slideToggle(500).siblings(".toggle").toggle()}function itemAjaxError(c,b){var a=jQuery("#media-item-error"+c);a.html('<div class="file-error"><button type="button" id="dismiss-'+c+'" class="button dismiss">'+swfuploadL10n.dismiss+"</button>"+b+"</div>");jQuery("#dismiss-"+c).click(function(){jQuery(this).parents(".file-error").slideUp(200,function(){jQuery(this).empty()})})}function deleteSuccess(b,d){if(b=="-1"){return itemAjaxError(this.id,"You do not have permission. Has your session expired?")}if(b=="0"){return itemAjaxError(this.id,"Could not be deleted. Has it been deleted already?")}var c=this.id,a=jQuery("#media-item-"+c);if(type=jQuery("#type-of-"+c).val()){jQuery("#"+type+"-counter").text(jQuery("#"+type+"-counter").text()-1)}if(a.hasClass("child-of-"+post_id)){jQuery("#attachments-count").text(jQuery("#attachments-count").text()-1)}if(jQuery("form.type-form #media-items").children().length==1&&jQuery(".hidden","#media-items").length>0){jQuery(".toggle").toggle();jQuery(".slidetoggle").slideUp(200).siblings().removeClass("hidden")}jQuery(".toggle",a).toggle();jQuery(".slidetoggle",a).slideUp(200).siblings().removeClass("hidden");a.css({backgroundColor:"#fff"}).animate({backgroundColor:"#ffffe0"},{queue:false,duration:500}).addClass("trash-undo");jQuery(".filename:empty",a).remove();jQuery(".filename",a).append('<span class="trashnotice"> '+swfuploadL10n.deleted+" </span>").siblings("a.toggle").hide();jQuery(".filename",a).append(jQuery("a.undo",a).removeClass("hidden"));jQuery(".menu_order_input",a).hide();return}function deleteError(c,b,a){}function updateMediaForm(){var b=jQuery("form.type-form #media-items").children(),a=jQuery("#media-items").children();if(b.length==1){jQuery(".slidetoggle",b).slideDown(500).siblings().addClass("hidden").filter(".toggle").toggle()}if(a.not(".media-blank").length>0){jQuery(".savebutton").show()}else{jQuery(".savebutton").hide()}if(a.length>1){jQuery(".insert-gallery").show()}else{jQuery(".insert-gallery").hide()}}function uploadSuccess(b,a){if(a.match("media-upload-error")){jQuery("#media-item-"+b.id).html(a);return}prepareMediaItem(b,a);updateMediaForm();if(jQuery("#media-item-"+b.id).hasClass("child-of-"+post_id)){jQuery("#attachments-count").text(1*jQuery("#attachments-count").text()+1)}}function uploadComplete(a){if(swfu.getStats().files_queued==0){jQuery("#cancel-upload").attr("disabled","disabled");jQuery("#insert-gallery").attr("disabled","")}}function wpQueueError(a){jQuery("#media-upload-error").show().text(a)}function wpFileError(b,a){jQuery("#media-item-"+b.id+" .filename").after('<div class="file-error"><button type="button" id="dismiss-'+b.id+'" class="button dismiss">'+swfuploadL10n.dismiss+"</button>"+a+"</div>").siblings(".toggle").remove();jQuery("#dismiss-"+b.id).click(function(){jQuery(this).parents(".media-item").slideUp(200,function(){jQuery(this).remove()})})}function fileQueueError(c,a,b){if(a==SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){wpQueueError(swfuploadL10n.queue_limit_exceeded)}else{if(a==SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT){fileQueued(c);wpFileError(c,swfuploadL10n.file_exceeds_size_limit)}else{if(a==SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE){fileQueued(c);wpFileError(c,swfuploadL10n.zero_byte_file)}else{if(a==SWFUpload.QUEUE_ERROR.INVALID_FILETYPE){fileQueued(c);wpFileError(c,swfuploadL10n.invalid_filetype)}else{wpQueueError(swfuploadL10n.default_error)}}}}}function fileDialogComplete(b){try{if(b>0){this.startUpload()}}catch(a){this.debug(a)}}function switchUploader(b){var c=document.getElementById(swfu.customSettings.swfupload_element_id),a=document.getElementById(swfu.customSettings.degraded_element_id);if(b){c.style.display="block";a.style.display="none"}else{c.style.display="none";a.style.display="block"}}function swfuploadPreLoad(){if(!uploaderMode){switchUploader(1)}else{switchUploader(0)}}function swfuploadLoadFailed(){switchUploader(0);jQuery(".upload-html-bypass").hide()}function uploadError(b,c,a){switch(c){case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:wpFileError(b,swfuploadL10n.missing_upload_url);break;case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:wpFileError(b,swfuploadL10n.upload_limit_exceeded);break;case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:wpQueueError(swfuploadL10n.http_error);break;case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:wpQueueError(swfuploadL10n.upload_failed);break;case SWFUpload.UPLOAD_ERROR.IO_ERROR:wpQueueError(swfuploadL10n.io_error);break;case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:wpQueueError(swfuploadL10n.security_error);break;case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:jQuery("#media-item-"+b.id).remove();break;default:wpFileError(b,swfuploadL10n.default_error)}}function cancelUpload(){swfu.cancelQueue()}jQuery(document).ready(function(a){a('input[type="radio"]',"#media-items").live("click",function(){var b=a(this).closest("tr");if(a(b).hasClass("align")){setUserSetting("align",a(this).val())}else{if(a(b).hasClass("image-size")){setUserSetting("imgsize",a(this).val())}}});a("button.button","#media-items").live("click",function(){var b=this.className||"";b=b.match(/url([^ '"]+)/);if(b&&b[1]){setUserSetting("urlbutton",b[1]);a(this).siblings(".urlfield").val(a(this).attr("title"))}})});