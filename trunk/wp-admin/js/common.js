var showNotice,adminMenu,columns,validateForm,screenMeta;(function(a){adminMenu={init:function(){var b=a("#adminmenu");a(".wp-menu-toggle",b).each(function(){var c=a(this),d=c.siblings(".wp-submenu");if(d.length){c.click(function(){adminMenu.toggle(d)})}else{c.hide()}});this.favorites();a("#collapse-menu",b).click(function(){if(a("body").hasClass("folded")){adminMenu.fold(1);deleteUserSetting("mfold")}else{adminMenu.fold();setUserSetting("mfold","f")}return false});if(a("body").hasClass("folded")){this.fold()}},restoreMenuState:function(){},toggle:function(b){b.slideToggle(150,function(){var c=b.removeAttr("style").parent().toggleClass("wp-menu-open").attr("id");if(c){a("li.wp-has-submenu","#adminmenu").each(function(f,g){if(c==g.id){var d=a(g).hasClass("wp-menu-open")?"o":"c";setUserSetting("m"+f,d)}})}});return false},fold:function(b){if(b){a("body").removeClass("folded");a("#adminmenu li.wp-has-submenu").unbind()}else{a("body").addClass("folded");a("#adminmenu li.wp-has-submenu").hoverIntent({over:function(j){var d,c,g,k,i;d=a(this).find(".wp-submenu");c=a(this).offset().top+d.height()+1;g=a("#wpwrap").height();k=60+c-g;i=a(window).height()+a(window).scrollTop()-15;if(i<(c-k)){k=c-i}if(k>1){d.css({marginTop:"-"+k+"px"})}else{if(d.css("marginTop")){d.css({marginTop:""})}}d.addClass("sub-open")},out:function(){a(this).find(".wp-submenu").removeClass("sub-open")},timeout:220,sensitivity:8,interval:100})}},favorites:function(){a("#favorite-inside").width(a("#favorite-actions").width()-4);a("#favorite-toggle, #favorite-inside").bind("mouseenter",function(){a("#favorite-inside").removeClass("slideUp").addClass("slideDown");setTimeout(function(){if(a("#favorite-inside").hasClass("slideDown")){a("#favorite-inside").slideDown(100);a("#favorite-first").addClass("slide-down")}},200)}).bind("mouseleave",function(){a("#favorite-inside").removeClass("slideDown").addClass("slideUp");setTimeout(function(){if(a("#favorite-inside").hasClass("slideUp")){a("#favorite-inside").slideUp(100,function(){a("#favorite-first").removeClass("slide-down")})}},300)})}};a(document).ready(function(){adminMenu.init()});columns={init:function(){var b=this;a(".hide-column-tog","#adv-settings").click(function(){var d=a(this),c=d.val();if(d.prop("checked")){b.checked(c)}else{b.unchecked(c)}columns.saveManageColumnsState()})},saveManageColumnsState:function(){var b=this.hidden();a.post(ajaxurl,{action:"hidden-columns",hidden:b,screenoptionnonce:a("#screenoptionnonce").val(),page:pagenow})},checked:function(b){a(".column-"+b).show();this.colSpanChange(+1)},unchecked:function(b){a(".column-"+b).hide();this.colSpanChange(-1)},hidden:function(){return a(".manage-column").filter(":hidden").map(function(){return this.id}).get().join(",")},useCheckboxesForHidden:function(){this.hidden=function(){return a(".hide-column-tog").not(":checked").map(function(){var b=this.id;return b.substring(b,b.length-5)}).get().join(",")}},colSpanChange:function(b){var d=a("table").find(".colspanchange"),c;if(!d.length){return}c=parseInt(d.attr("colspan"),10)+b;d.attr("colspan",c.toString())}};a(document).ready(function(){columns.init()});validateForm=function(b){return !a(b).find(".form-required").filter(function(){return a("input:visible",this).val()==""}).addClass("form-invalid").find("input:visible").change(function(){a(this).closest(".form-invalid").removeClass("form-invalid")}).size()};showNotice={warn:function(){var b=commonL10n.warnDelete||"";if(confirm(b)){return true}return false},note:function(b){alert(b)}};screenMeta={links:{"screen-options-link-wrap":"screen-options-wrap","contextual-help-link-wrap":"contextual-help-wrap"},init:function(){a(".screen-meta-toggle").click(screenMeta.toggleEvent)},toggleEvent:function(c){var b;c.preventDefault();if(!screenMeta.links[this.id]){return}b=a("#"+screenMeta.links[this.id]);if(b.is(":visible")){screenMeta.close(b,a(this))}else{screenMeta.open(b,a(this))}},open:function(b,c){a(".screen-meta-toggle").not(c).css("visibility","hidden");b.slideDown("fast",function(){c.addClass("screen-meta-active")})},close:function(b,c){b.slideUp("fast",function(){c.removeClass("screen-meta-active");a(".screen-meta-toggle").css("visibility","")})}};a(document).ready(function(){var g=false,b,e,d,c,f;a("div.wrap h2:first").nextAll("div.updated, div.error").addClass("below-h2");a("div.updated, div.error").not(".below-h2, .inline").insertAfter(a("div.wrap h2:first"));screenMeta.init();f={doc:a(document),element:a("#user_info"),open:function(){if(!f.element.hasClass("active")){f.element.addClass("active");f.doc.one("click",f.close);return false}},close:function(){f.element.removeClass("active")}};f.element.click(f.open);a("tbody").children().children(".check-column").find(":checkbox").click(function(h){if("undefined"==h.shiftKey){return true}if(h.shiftKey){if(!g){return true}b=a(g).closest("form").find(":checkbox");e=b.index(g);d=b.index(this);c=a(this).prop("checked");if(0<e&&0<d&&e!=d){b.slice(e,d).prop("checked",function(){if(a(this).closest("tr").is(":visible")){return c}return false})}}g=this;return true});a("thead, tfoot").find(".check-column :checkbox").click(function(j){var k=a(this).prop("checked"),i="undefined"==typeof toggleWithKeyboard?false:toggleWithKeyboard,h=j.shiftKey||i;a(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){if(a(this).closest("tr").is(":hidden")){return false}if(h){return a(this).prop("checked")}else{if(k){return true}}return false});a(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){if(h){return false}else{if(k){return true}}return false})});a("#default-password-nag-no").click(function(){setUserSetting("default_password_nag","hide");a("div.default-password-nag").hide();return false});a("#newcontent").bind("keydown.wpevent_InsertTab",function(m){if(m.keyCode!=9){return true}var j=m.target,o=j.selectionStart,i=j.selectionEnd,n=j.value,h,l;try{this.lastKey=9}catch(k){}if(document.selection){j.focus();l=document.selection.createRange();l.text="\t"}else{if(o>=0){h=this.scrollTop;j.value=n.substring(0,o).concat("\t",n.substring(i));j.selectionStart=j.selectionEnd=o+1;this.scrollTop=h}}if(m.stopPropagation){m.stopPropagation()}if(m.preventDefault){m.preventDefault()}});a("#newcontent").bind("blur.wpevent_InsertTab",function(h){if(this.lastKey&&9==this.lastKey){this.focus()}})});a(document).bind("wp_CloseOnEscape",function(c,b){if(typeof(b.cb)!="function"){return}if(typeof(b.condition)!="function"||b.condition()){b.cb()}return true})})(jQuery);