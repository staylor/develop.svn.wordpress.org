(function(a){a.ui.dialog.prototype.options.closeOnEscape=false;a.widget("wp.wpdialog",a.ui.dialog,{options:{closeOnEscape:false},open:function(){var b;if(tinyMCEPopup&&typeof tinyMCE!="undefined"&&(b=tinyMCE.activeEditor)&&!b.isHidden()){tinyMCEPopup.init()}if(this._isOpen||false===this._trigger("beforeOpen")){return}a.ui.dialog.prototype.open.apply(this,arguments);this.element.focus();this._trigger("refresh")}})})(jQuery);