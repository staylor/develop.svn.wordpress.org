jQuery(document).ready(function(b){var a=wpCookies.getHash("TinyMCE_content_size");if(getUserSetting("editor")=="html"){if(a){b("#content").css("height",a.ch-15+"px")}}else{b("#content").css("color","white");b("#quicktags").hide()}});var switchEditors={mode:"",I:function(a){return document.getElementById(a)},edInit:function(){},saveCallback:function(b,c,a){if(tinyMCE.activeEditor.isHidden()){c=this.I(b).value}else{c=this.pre_wpautop(c)}return c},pre_wpautop:function(b){var c,a;b=b.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g,function(d){d=d.replace(/<br ?\/?>[\r\n]*/g,"<wp_temp>");return d.replace(/<\/?p( [^>]*)?>[\r\n]*/g,"<wp_temp>")});c="blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|p";b=b.replace(new RegExp("\\s*</("+c+")>\\s*","mg"),"</$1>\n");b=b.replace(new RegExp("\\s*<(("+c+")[^>]*)>","mg"),"\n<$1>");b=b.replace(new RegExp("(<p [^>]+>.*?)</p>","mg"),"$1</p#>");b=b.replace(new RegExp("<div([^>]*)>\\s*<p>","mgi"),"<div$1>\n\n");b=b.replace(new RegExp("\\s*<p>","mgi"),"");b=b.replace(new RegExp("\\s*</p>\\s*","mgi"),"\n\n");b=b.replace(new RegExp("\\n\\s*\\n","mgi"),"\n\n");b=b.replace(new RegExp("\\s*<br ?/?>\\s*","gi"),"\n");b=b.replace(new RegExp("\\s*<div","mg"),"\n<div");b=b.replace(new RegExp("</div>\\s*","mg"),"</div>\n");b=b.replace(new RegExp("\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*","gi"),"\n\n[caption$1[/caption]\n\n");b=b.replace(new RegExp("caption\\]\\n\\n+\\[caption","g"),"caption]\n\n[caption");a="blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre";b=b.replace(new RegExp("\\s*<(("+a+") ?[^>]*)\\s*>","mg"),"\n<$1>");b=b.replace(new RegExp("\\s*</("+a+")>\\s*","mg"),"</$1>\n");b=b.replace(new RegExp("<li([^>]*)>","g"),"\t<li$1>");if(b.indexOf("<object")!=-1){b=b.replace(/<object[\s\S]+?<\/object>/g,function(d){return d.replace(/[\r\n]+/g,"")})}b=b.replace(new RegExp("</p#>","g"),"</p>\n");b=b.replace(new RegExp("\\s*(<p [^>]+>.*</p>)","mg"),"\n$1");b=b.replace(new RegExp("^\\s*",""),"");b=b.replace(new RegExp("[\\s\\u00a0]*$",""),"");b=b.replace(/<wp_temp>/g,"\n");return b},go:function(i,g){i=i||"content";g=g||this.mode||"";var b,h=this.I("quicktags"),c=this.I("edButtonHTML"),d=this.I("edButtonPreview"),a=this.I(i);try{b=tinyMCE.get(i)}catch(f){b=false}if("tinymce"==g){if(b&&!b.isHidden()){return false}setUserSetting("editor","tinymce");this.mode="html";d.className="active";c.className="";edCloseAllTags();h.style.display="none";a.value=this.wpautop(a.value);if(b){b.show()}else{try{tinyMCE.execCommand("mceAddControl",false,i)}catch(f){}}}else{setUserSetting("editor","html");a.style.color="#000";this.mode="tinymce";c.className="active";d.className="";if(b&&!b.isHidden()){a.style.height=b.getContentAreaContainer().offsetHeight+24+"px";b.hide()}h.style.display="block"}return false},wpautop:function(a){var b="table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]";if(a.indexOf("<object")!=-1){a=a.replace(/<object[\s\S]+?<\/object>/g,function(c){return c.replace(/[\r\n]+/g,"")})}a=a.replace(/<[^<>]+>/g,function(c){return c.replace(/[\r\n]+/g," ")});a=a+"\n\n";a=a.replace(new RegExp("<br />\\s*<br />","gi"),"\n\n");a=a.replace(new RegExp("(<(?:"+b+")[^>]*>)","gi"),"\n$1");a=a.replace(new RegExp("(</(?:"+b+")>)","gi"),"$1\n\n");a=a.replace(new RegExp("\\r\\n|\\r","g"),"\n");a=a.replace(new RegExp("\\n\\s*\\n+","g"),"\n\n");a=a.replace(new RegExp("([\\s\\S]+?)\\n\\n","mg"),"<p>$1</p>\n");a=a.replace(new RegExp("<p>\\s*?</p>","gi"),"");a=a.replace(new RegExp("<p>\\s*(</?(?:"+b+")[^>]*>)\\s*</p>","gi"),"$1");a=a.replace(new RegExp("<p>(<li.+?)</p>","gi"),"$1");a=a.replace(new RegExp("<p>\\s*<blockquote([^>]*)>","gi"),"<blockquote$1><p>");a=a.replace(new RegExp("</blockquote>\\s*</p>","gi"),"</p></blockquote>");a=a.replace(new RegExp("<p>\\s*(</?(?:"+b+")[^>]*>)","gi"),"$1");a=a.replace(new RegExp("(</?(?:"+b+")[^>]*>)\\s*</p>","gi"),"$1");a=a.replace(new RegExp("\\s*\\n","gi"),"<br />\n");a=a.replace(new RegExp("(</?(?:"+b+")[^>]*>)\\s*<br />","gi"),"$1");a=a.replace(new RegExp("<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)","gi"),"$1");a=a.replace(new RegExp("(?:<p>|<br ?/?>)*\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*(?:</p>|<br ?/?>)*","gi"),"[caption$1[/caption]");a=a.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g,function(c){c=c.replace(/<br ?\/?>[\r\n]*/g,"\n");return c.replace(/<\/?p( [^>]*)?>[\r\n]*/g,"\n")});return a}};