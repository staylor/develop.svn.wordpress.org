jQuery(document).ready(function(c){var b,e,a,d=false;e=function(){b=c("#media-items").sortable({items:"div.media-item",placeholder:"sorthelper",axis:"y",distance:2,handle:"div.filename",stop:function(i,h){var g=c("#media-items").sortable("toArray"),f=g.length;c.each(g,function(k,l){var j=d?(f-k):(1+k);c("#"+l+" .menu_order input").val(j)})}})};sortIt=function(){var g=c(".menu_order_input"),f=g.length;g.each(function(j){var h=d?(f-j):(1+j);c(this).val(h)})};clearAll=function(f){f=f||0;c(".menu_order_input").each(function(){if(this.value=="0"||f){this.value=""}})};c("#asc").click(function(){d=false;sortIt();return false});c("#desc").click(function(){d=true;sortIt();return false});c("#clear").click(function(){clearAll(1);return false});c("#showall").click(function(){c("#sort-buttons span a").toggle();c("a.describe-toggle-on").hide();c("a.describe-toggle-off, table.slidetoggle").show();return false});c("#hideall").click(function(){c("#sort-buttons span a").toggle();c("a.describe-toggle-on").show();c("a.describe-toggle-off, table.slidetoggle").hide();return false});e();clearAll();if(c("#media-items>*").length>1){a=wpgallery.getWin();c("#save-all, #gallery-settings").show();if(typeof a.tinyMCE!="undefined"&&a.tinyMCE.activeEditor&&!a.tinyMCE.activeEditor.isHidden()){wpgallery.mcemode=true;wpgallery.init()}else{c("#insert-gallery").show()}}});jQuery(window).unload(function(){tinymce=tinyMCE=wpgallery=null});var tinymce=null,tinyMCE,wpgallery;wpgallery={mcemode:false,editor:{},dom:{},is_update:false,el:{},I:function(a){return document.getElementById(a)},init:function(){var d=this,a,f,c,e,b=d.getWin();if(!d.mcemode){return}a=(""+document.location.search).replace(/^\?/,"").split("&");f={};for(c=0;c<a.length;c++){e=a[c].split("=");f[unescape(e[0])]=unescape(e[1])}if(f.mce_rdomain){document.domain=f.mce_rdomain}tinymce=b.tinymce;tinyMCE=b.tinyMCE;d.editor=tinymce.EditorManager.activeEditor;d.setup()},getWin:function(){return window.dialogArguments||opener||parent||top},restoreSelection:function(){var a=this;if(tinymce.isIE){a.editor.selection.moveToBookmark(a.editor.windowManager.bookmark)}},setup:function(){var f=this,c,d=f.editor,i,e,h,b,j;if(!f.mcemode){return}f.restoreSelection();f.el=d.selection.getNode();if(f.el.nodeName!="IMG"||!d.dom.hasClass(f.el,"wpGallery")){if((i=d.dom.select("img.wpGallery"))&&i[0]){f.el=i[0]}else{if(getUserSetting("galfile")=="1"){f.I("linkto-file").checked="checked"}if(getUserSetting("galdesc")=="1"){f.I("order-desc").checked="checked"}if(getUserSetting("galcols")){f.I("columns").value=getUserSetting("galcols")}if(getUserSetting("galord")){f.I("orderby").value=getUserSetting("galord")}jQuery("#insert-gallery").show();return}}c=d.dom.getAttrib(f.el,"title");c=d.dom.decode(c);if(c){jQuery("#update-gallery").show();f.is_update=true;e=c.match(/columns=['"]([0-9]+)['"]/);h=c.match(/link=['"]([^'"]+)['"]/i);b=c.match(/order=['"]([^'"]+)['"]/i);j=c.match(/orderby=['"]([^'"]+)['"]/i);if(h&&h[1]){f.I("linkto-file").checked="checked"}if(b&&b[1]){f.I("order-desc").checked="checked"}if(e&&e[1]){f.I("columns").value=""+e[1]}if(j&&j[1]){f.I("orderby").value=j[1]}}else{jQuery("#insert-gallery").show()}},update:function(){var b=this,a=b.editor,d="",c;if(!b.mcemode||!b.is_update){c="[gallery"+b.getSettings()+"]";b.getWin().send_to_editor(c);return}if(b.el.nodeName!="IMG"){return}d=a.dom.decode(a.dom.getAttrib(b.el,"title"));d=d.replace(/\s*(order|link|columns|orderby)=['"]([^'"]+)['"]/gi,"");d+=b.getSettings();a.dom.setAttrib(b.el,"title",d);b.getWin().tb_remove()},getSettings:function(){var a=this.I,b="";if(a("linkto-file").checked){b+=' link="file"';setUserSetting("galfile","1")}if(a("order-desc").checked){b+=' order="DESC"';setUserSetting("galdesc","1")}if(a("columns").value!=3){b+=' columns="'+a("columns").value+'"';setUserSetting("galcols",a("columns").value)}if(a("orderby").value!="menu_order"){b+=' orderby="'+a("orderby").value+'"';setUserSetting("galord",a("orderby").value)}return b}};