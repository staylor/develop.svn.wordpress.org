var ajaxWidgets,ajaxPopulateWidgets,quickPressLoad;jQuery(document).ready(function(a){ajaxWidgets=["dashboard_incoming_links","dashboard_primary","dashboard_secondary","dashboard_plugins","dashboard_quick_press"];ajaxPopulateWidgets=function(b){show=function(g,c){var f,d=a("#"+g+" div.inside:visible").find(".widget-loading");if(d.length){f=d.parent();setTimeout(function(){f.load(ajaxurl.replace("/admin-ajax.php","")+"/index-extra.php?jax="+g,"",function(){f.hide().slideDown("normal",function(){a(this).css("display","");if("dashboard_quick_press"==g){quickPressLoad()}})})},c*500)}};if(b){b=b.toString();if(a.inArray(b,ajaxWidgets)!=-1){show(b,0)}}else{a.each(ajaxWidgets,function(c){show(this,c)})}};ajaxPopulateWidgets();postboxes.add_postbox_toggles(pagenow,{pbshow:ajaxPopulateWidgets});quickPressLoad=function(){var b=a("#quickpost-action"),c;c=a("#quick-press").submit(function(){a("#dashboard_quick_press #publishing-action img.waiting").css("visibility","visible");a('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').attr("disabled","disabled");if("post"==b.val()){b.val("post-quickpress-publish")}a("#dashboard_quick_press div.inside").load(c.attr("action"),c.serializeArray(),function(){a("#dashboard_quick_press #publishing-action img.waiting").css("visibility","hidden");a('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').attr("disabled","");a("#dashboard_quick_press ul").next("p").remove();a("#dashboard_quick_press ul").find("li").each(function(){a("#dashboard_recent_drafts ul").prepend(this)}).end().remove();quickPressLoad()});return false});a("#publish").click(function(){b.val("post-quickpress-publish")})}});