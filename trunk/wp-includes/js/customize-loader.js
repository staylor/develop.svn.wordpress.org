if(typeof wp==="undefined"){var wp={}}(function(a,b){var c={initialize:function(){this.body=b(document.body);this.element=b("#customize-container");this.base=b(".admin-url",this.element).val();this.element.on("click",".close-full-overlay",function(){c.close();return false});this.element.on("click",".collapse-sidebar",function(){c.element.toggleClass("collapsed");return false})},open:function(d){d.customize="on";this.iframe=b("<iframe />",{src:this.base+"?"+jQuery.param(d)}).appendTo(this.element);this.element.fadeIn(200,function(){c.body.addClass("customize-active full-overlay-active")})},close:function(){this.element.fadeOut(200,function(){c.iframe.remove();c.iframe=null;c.body.removeClass("customize-active full-overlay-active")})}};b(function(){c.initialize();b("#current-theme, #availablethemes").on("click",".load-customize",function(d){var e=b(this);d.preventDefault();c.open({template:e.data("customizeTemplate"),stylesheet:e.data("customizeStylesheet")})})});a.CustomizeLoader=c})(wp,jQuery);