var showNotice,adminMenu,columns,validateForm,screenMeta,autofold_menu;(function(a){adminMenu={init:function(){},fold:function(){},restoreMenuState:function(){},toggle:function(){},favorites:function(){}};columns={init:function(){var b=this;a(".hide-column-tog","#adv-settings").click(function(){var d=a(this),c=d.val();if(d.prop("checked")){b.checked(c)}else{b.unchecked(c)}columns.saveManageColumnsState()})},saveManageColumnsState:function(){var b=this.hidden();a.post(ajaxurl,{action:"hidden-columns",hidden:b,screenoptionnonce:a("#screenoptionnonce").val(),page:pagenow})},checked:function(b){a(".column-"+b).show();this.colSpanChange(+1)},unchecked:function(b){a(".column-"+b).hide();this.colSpanChange(-1)},hidden:function(){return a(".manage-column").filter(":hidden").map(function(){return this.id}).get().join(",")},useCheckboxesForHidden:function(){this.hidden=function(){return a(".hide-column-tog").not(":checked").map(function(){var b=this.id;return b.substring(b,b.length-5)}).get().join(",")}},colSpanChange:function(b){var d=a("table").find(".colspanchange"),c;if(!d.length){return}c=parseInt(d.attr("colspan"),10)+b;d.attr("colspan",c.toString())}};a(document).ready(function(){columns.init()});validateForm=function(b){return !a(b).find(".form-required").filter(function(){return a("input:visible",this).val()==""}).addClass("form-invalid").find("input:visible").change(function(){a(this).closest(".form-invalid").removeClass("form-invalid")}).size()};showNotice={warn:function(){var b=commonL10n.warnDelete||"";if(confirm(b)){return true}return false},note:function(b){alert(b)}};screenMeta={element:null,toggles:null,page:null,padding:null,top:null,init:function(){this.element=a("#screen-meta");this.toggles=a(".screen-meta-toggle a");this.page=a("#wpcontent");this.toggles.click(this.toggleEvent)},toggleEvent:function(c){var b=a(this.href.replace(/.+#/,"#"));c.preventDefault();if(!b.length){return}if(b.is(":visible")){screenMeta.close(b,a(this))}else{screenMeta.open(b,a(this))}},open:function(b,c){a(".screen-meta-toggle").not(c.parent()).css("visibility","hidden");b.parent().show();b.slideDown("fast",function(){c.addClass("screen-meta-active");screenMeta.refresh()})},refresh:function(c,e){var d=a("#contextual-help-wrap").children(),b=0;d.each(function(){var f=a(this).height();if(f>b){b=f}});if(b){d.height(b)}},close:function(b,c){b.slideUp("fast",function(){c.removeClass("screen-meta-active");a(".screen-meta-toggle").css("visibility","");b.parent().hide()})}};a(".contextual-help-tabs").delegate("a","click focus",function(d){var c=a(this),b;d.preventDefault();if(c.is(".active a")){return false}a(".contextual-help-tabs .active").removeClass("active");c.parent("li").addClass("active");b=a(c.attr("href"));a(".help-tab-content").not(b).removeClass("active").hide();b.addClass("active").show();screenMeta.refresh()});a(document).ready(function(){var i=false,c,e,j,h,b=a("#adminmenu"),d=a("input.current-page"),f=d.val(),g;a("#collapse-menu",b).click(function(){var k=a(document.body);if(k.hasClass("folded")){k.removeClass("folded");setUserSetting("mfold","o")}else{k.addClass("folded");setUserSetting("mfold","f")}return false});a("li.wp-has-submenu",b).hoverIntent({over:function(q){var l,n,r,p,k=a(this).find(".wp-submenu");if(!a(document.body).hasClass("folded")&&a(this).hasClass("wp-menu-open")){return}l=a(this).offset().top+k.height()+1;n=a("#wpwrap").height();r=60+l-n;p=a(window).height()+a(window).scrollTop()-15;if(p<(l-r)){r=l-p}if(r>1){k.css({marginTop:"-"+r+"px"})}else{if(k.css("marginTop")){k.css({marginTop:""})}}k.addClass("sub-open")},out:function(){a(this).find(".wp-submenu").removeClass("sub-open")},timeout:200,sensitivity:7,interval:90});a("li.wp-has-submenu").focusin(function(){a(this).addClass("focused")}).focusout(function(){a(this).removeClass("focused")});b.mouseover(function(k){a("li.focused",this).removeClass("focused")});a("div.wrap h2:first").nextAll("div.updated, div.error").addClass("below-h2");a("div.updated, div.error").not(".below-h2, .inline").insertAfter(a("div.wrap h2:first"));screenMeta.init();a("tbody").children().children(".check-column").find(":checkbox").click(function(k){if("undefined"==k.shiftKey){return true}if(k.shiftKey){if(!i){return true}c=a(i).closest("form").find(":checkbox");e=c.index(i);j=c.index(this);h=a(this).prop("checked");if(0<e&&0<j&&e!=j){c.slice(e,j).prop("checked",function(){if(a(this).closest("tr").is(":visible")){return h}return false})}}i=this;return true});a("thead, tfoot").find(".check-column :checkbox").click(function(m){var n=a(this).prop("checked"),l="undefined"==typeof toggleWithKeyboard?false:toggleWithKeyboard,k=m.shiftKey||l;a(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){if(a(this).closest("tr").is(":hidden")){return false}if(k){return a(this).prop("checked")}else{if(n){return true}}return false});a(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){if(k){return false}else{if(n){return true}}return false})});a("#default-password-nag-no").click(function(){setUserSetting("default_password_nag","hide");a("div.default-password-nag").hide();return false});a("#newcontent").bind("keydown.wpevent_InsertTab",function(p){if(p.keyCode!=9){return true}var m=p.target,r=m.selectionStart,l=m.selectionEnd,q=m.value,k,o;try{this.lastKey=9}catch(n){}if(document.selection){m.focus();o=document.selection.createRange();o.text="\t"}else{if(r>=0){k=this.scrollTop;m.value=q.substring(0,r).concat("\t",q.substring(l));m.selectionStart=m.selectionEnd=r+1;this.scrollTop=k}}if(p.stopPropagation){p.stopPropagation()}if(p.preventDefault){p.preventDefault()}});a("#newcontent").bind("blur.wpevent_InsertTab",function(k){if(this.lastKey&&9==this.lastKey){this.focus()}});if(d.length){d.closest("form").submit(function(k){if(a('select[name="action"]').val()==-1&&a('select[name="action2"]').val()==-1&&d.val()==f){d.val("1")}})}a(window).bind("resize.autofold",function(){if(getUserSetting("mfold")=="f"){return}var k=a(window).width();if(k<=800){if(!g){a(document.body).addClass("folded");g=true}}else{if(g){a(document.body).removeClass("folded");g=false}}}).triggerHandler("resize")});a(document).bind("wp_CloseOnEscape",function(c,b){if(typeof(b.cb)!="function"){return}if(typeof(b.condition)!="function"||b.condition()){b.cb()}return true})})(jQuery);