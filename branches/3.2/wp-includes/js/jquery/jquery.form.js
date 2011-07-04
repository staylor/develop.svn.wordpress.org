/*
 * jQuery Form Plugin
 * version: 2.73 (03-MAY-2011)
 * @requires jQuery v1.3.2 or later
 *
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function(b){b.fn.ajaxSubmit=function(t){if(!this.length){a("ajaxSubmit: skipping submit process - no element selected");return this}if(typeof t=="function"){t={success:t}}var h=this.attr("action");var d=(typeof h==="string")?b.trim(h):"";if(d){d=(d.match(/^([^#]+)/)||[])[1]}d=d||window.location.href||"";t=b.extend(true,{url:d,success:b.ajaxSettings.success,type:this[0].getAttribute("method")||"GET",iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},t);var u={};this.trigger("form-pre-serialize",[this,t,u]);if(u.veto){a("ajaxSubmit: submit vetoed via form-pre-serialize trigger");return this}if(t.beforeSerialize&&t.beforeSerialize(this,t)===false){a("ajaxSubmit: submit aborted via beforeSerialize callback");return this}var f,p,m=this.formToArray(t.semantic);if(t.data){t.extraData=t.data;for(f in t.data){if(t.data[f] instanceof Array){for(var i in t.data[f]){m.push({name:f,value:t.data[f][i]})}}else{p=t.data[f];p=b.isFunction(p)?p():p;m.push({name:f,value:p})}}}if(t.beforeSubmit&&t.beforeSubmit(m,this,t)===false){a("ajaxSubmit: submit aborted via beforeSubmit callback");return this}this.trigger("form-submit-validate",[m,this,t,u]);if(u.veto){a("ajaxSubmit: submit vetoed via form-submit-validate trigger");return this}var c=b.param(m);if(t.type.toUpperCase()=="GET"){t.url+=(t.url.indexOf("?")>=0?"&":"?")+c;t.data=null}else{t.data=c}var s=this,l=[];if(t.resetForm){l.push(function(){s.resetForm()})}if(t.clearForm){l.push(function(){s.clearForm()})}if(!t.dataType&&t.target){var r=t.success||function(){};l.push(function(n){var k=t.replaceTarget?"replaceWith":"html";b(t.target)[k](n).each(r,arguments)})}else{if(t.success){l.push(t.success)}}t.success=function(w,n,x){var v=t.context||t;for(var q=0,k=l.length;q<k;q++){l[q].apply(v,[w,n,x||s,s])}};var g=b("input:file",this).length>0;var e="multipart/form-data";var j=(s.attr("enctype")==e||s.attr("encoding")==e);if(t.iframe!==false&&(g||t.iframe||j)){if(t.closeKeepAlive){b.get(t.closeKeepAlive,o)}else{o()}}else{b.ajax(t)}this.trigger("form-submit-notify",[this,t]);return this;function o(){var v=s[0];if(b(":input[name=submit],:input[id=submit]",v).length){alert('Error: Form elements must not have name or id of "submit".');return}var D=b.extend(true,{},b.ajaxSettings,t);D.context=D.context||D;var G="jqFormIO"+(new Date().getTime()),A="_"+G;var x=b('<iframe id="'+G+'" name="'+G+'" src="'+D.iframeSrc+'" />');var B=x[0];x.css({position:"absolute",top:"-1000px",left:"-1000px"});var y={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(n){var O=(n==="timeout"?"timeout":"aborted");a("aborting upload... "+O);this.aborted=1;x.attr("src",D.iframeSrc);y.error=O;D.error&&D.error.call(D.context,y,O,O);K&&b.event.trigger("ajaxError",[y,D,O]);D.complete&&D.complete.call(D.context,y,O)}};var K=D.global;if(K&&!b.active++){b.event.trigger("ajaxStart")}if(K){b.event.trigger("ajaxSend",[y,D])}if(D.beforeSend&&D.beforeSend.call(D.context,y,D)===false){if(D.global){b.active--}return}if(y.aborted){return}var J=0,C;var z=v.clk;if(z){var H=z.name;if(H&&!z.disabled){D.extraData=D.extraData||{};D.extraData[H]=z.value;if(z.type=="image"){D.extraData[H+".x"]=v.clk_x;D.extraData[H+".y"]=v.clk_y}}}function I(){var Q=s.attr("target"),O=s.attr("action");v.setAttribute("target",G);if(v.getAttribute("method")!="POST"){v.setAttribute("method","POST")}if(v.getAttribute("action")!=D.url){v.setAttribute("action",D.url)}if(!D.skipEncodingOverride){s.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"})}if(D.timeout){C=setTimeout(function(){J=true;F(true)},D.timeout)}var P=[];try{if(D.extraData){for(var R in D.extraData){P.push(b('<input type="hidden" name="'+R+'" value="'+D.extraData[R]+'" />').appendTo(v)[0])}}x.appendTo("body");B.attachEvent?B.attachEvent("onload",F):B.addEventListener("load",F,false);v.submit()}finally{v.setAttribute("action",O);if(Q){v.setAttribute("target",Q)}else{s.removeAttr("target")}b(P).remove()}}if(D.forceSync){I()}else{setTimeout(I,10)}var M,N,L=50,w;function F(T){if(y.aborted||w){return}if(T===true&&y){y.abort("timeout");return}var S=B.contentWindow?B.contentWindow.document:B.contentDocument?B.contentDocument:B.document;if(!S||S.location.href==D.iframeSrc){if(!J){return}}B.detachEvent?B.detachEvent("onload",F):B.removeEventListener("load",F,false);var P=true;try{if(J){throw"timeout"}var U=D.dataType=="xml"||S.XMLDocument||b.isXMLDoc(S);a("isXml="+U);if(!U&&window.opera&&(S.body==null||S.body.innerHTML=="")){if(--L){a("requeing onLoad callback, DOM not available");setTimeout(F,250);return}}y.responseText=S.body?S.body.innerHTML:S.documentElement?S.documentElement.innerHTML:null;y.responseXML=S.XMLDocument?S.XMLDocument:S;if(U){D.dataType="xml"}y.getResponseHeader=function(W){var V={"content-type":D.dataType};return V[W]};var R=/(json|script|text)/.test(D.dataType);if(R||D.textarea){var O=S.getElementsByTagName("textarea")[0];if(O){y.responseText=O.value}else{if(R){var Q=S.getElementsByTagName("pre")[0];var n=S.getElementsByTagName("body")[0];if(Q){y.responseText=Q.textContent}else{if(n){y.responseText=n.innerHTML}}}}}else{if(D.dataType=="xml"&&!y.responseXML&&y.responseText!=null){y.responseXML=E(y.responseText)}}M=k(y,D.dataType,D)}catch(T){a("error caught:",T);P=false;y.error=T;D.error&&D.error.call(D.context,y,"error",T);K&&b.event.trigger("ajaxError",[y,D,T])}if(y.aborted){a("upload aborted");P=false}if(P){D.success&&D.success.call(D.context,M,"success",y);K&&b.event.trigger("ajaxSuccess",[y,D])}K&&b.event.trigger("ajaxComplete",[y,D]);if(K&&!--b.active){b.event.trigger("ajaxStop")}D.complete&&D.complete.call(D.context,y,P?"success":"error");w=true;if(D.timeout){clearTimeout(C)}setTimeout(function(){x.removeData("form-plugin-onload");x.remove();y.responseXML=null},100)}var E=b.parseXML||function(n,O){if(window.ActiveXObject){O=new ActiveXObject("Microsoft.XMLDOM");O.async="false";O.loadXML(n)}else{O=(new DOMParser()).parseFromString(n,"text/xml")}return(O&&O.documentElement&&O.documentElement.nodeName!="parsererror")?O:null};var q=b.parseJSON||function(n){return window["eval"]("("+n+")")};var k=function(S,Q,P){var O=S.getResponseHeader("content-type")||"",n=Q==="xml"||!Q&&O.indexOf("xml")>=0,R=n?S.responseXML:S.responseText;if(n&&R.documentElement.nodeName==="parsererror"){b.error&&b.error("parsererror")}if(P&&P.dataFilter){R=P.dataFilter(R,Q)}if(typeof R==="string"){if(Q==="json"||!Q&&O.indexOf("json")>=0){R=q(R)}else{if(Q==="script"||!Q&&O.indexOf("javascript")>=0){b.globalEval(R)}}}return R}}};b.fn.ajaxForm=function(c){if(this.length===0){var d={s:this.selector,c:this.context};if(!b.isReady&&d.s){a("DOM not ready, queuing ajaxForm");b(function(){b(d.s,d.c).ajaxForm(c)});return this}a("terminating; zero elements found by selector"+(b.isReady?"":" (DOM not ready)"));return this}return this.ajaxFormUnbind().bind("submit.form-plugin",function(f){if(!f.isDefaultPrevented()){f.preventDefault();b(this).ajaxSubmit(c)}}).bind("click.form-plugin",function(j){var i=j.target;var g=b(i);if(!(g.is(":submit,input:image"))){var f=g.closest(":submit");if(f.length==0){return}i=f[0]}var h=this;h.clk=i;if(i.type=="image"){if(j.offsetX!=undefined){h.clk_x=j.offsetX;h.clk_y=j.offsetY}else{if(typeof b.fn.offset=="function"){var k=g.offset();h.clk_x=j.pageX-k.left;h.clk_y=j.pageY-k.top}else{h.clk_x=j.pageX-i.offsetLeft;h.clk_y=j.pageY-i.offsetTop}}}setTimeout(function(){h.clk=h.clk_x=h.clk_y=null},100)})};b.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")};b.fn.formToArray=function(q){var p=[];if(this.length===0){return p}var d=this[0];var g=q?d.getElementsByTagName("*"):d.elements;if(!g){return p}var k,h,f,r,e,m,c;for(k=0,m=g.length;k<m;k++){e=g[k];f=e.name;if(!f){continue}if(q&&d.clk&&e.type=="image"){if(!e.disabled&&d.clk==e){p.push({name:f,value:b(e).val()});p.push({name:f+".x",value:d.clk_x},{name:f+".y",value:d.clk_y})}continue}r=b.fieldValue(e,true);if(r&&r.constructor==Array){for(h=0,c=r.length;h<c;h++){p.push({name:f,value:r[h]})}}else{if(r!==null&&typeof r!="undefined"){p.push({name:f,value:r})}}}if(!q&&d.clk){var l=b(d.clk),o=l[0];f=o.name;if(f&&!o.disabled&&o.type=="image"){p.push({name:f,value:l.val()});p.push({name:f+".x",value:d.clk_x},{name:f+".y",value:d.clk_y})}}return p};b.fn.formSerialize=function(c){return b.param(this.formToArray(c))};b.fn.fieldSerialize=function(d){var c=[];this.each(function(){var h=this.name;if(!h){return}var f=b.fieldValue(this,d);if(f&&f.constructor==Array){for(var g=0,e=f.length;g<e;g++){c.push({name:h,value:f[g]})}}else{if(f!==null&&typeof f!="undefined"){c.push({name:this.name,value:f})}}});return b.param(c)};b.fn.fieldValue=function(h){for(var g=[],e=0,c=this.length;e<c;e++){var f=this[e];var d=b.fieldValue(f,h);if(d===null||typeof d=="undefined"||(d.constructor==Array&&!d.length)){continue}d.constructor==Array?b.merge(g,d):g.push(d)}return g};b.fieldValue=function(c,j){var e=c.name,p=c.type,q=c.tagName.toLowerCase();if(j===undefined){j=true}if(j&&(!e||c.disabled||p=="reset"||p=="button"||(p=="checkbox"||p=="radio")&&!c.checked||(p=="submit"||p=="image")&&c.form&&c.form.clk!=c||q=="select"&&c.selectedIndex==-1)){return null}if(q=="select"){var k=c.selectedIndex;if(k<0){return null}var m=[],d=c.options;var g=(p=="select-one");var l=(g?k+1:d.length);for(var f=(g?k:0);f<l;f++){var h=d[f];if(h.selected){var o=h.value;if(!o){o=(h.attributes&&h.attributes.value&&!(h.attributes.value.specified))?h.text:h.value}if(g){return o}m.push(o)}}return m}return b(c).val()};b.fn.clearForm=function(){return this.each(function(){b("input,select,textarea",this).clearFields()})};b.fn.clearFields=b.fn.clearInputs=function(){return this.each(function(){var d=this.type,c=this.tagName.toLowerCase();if(d=="text"||d=="password"||c=="textarea"){this.value=""}else{if(d=="checkbox"||d=="radio"){this.checked=false}else{if(c=="select"){this.selectedIndex=-1}}}})};b.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=="function"||(typeof this.reset=="object"&&!this.reset.nodeType)){this.reset()}})};b.fn.enable=function(c){if(c===undefined){c=true}return this.each(function(){this.disabled=!c})};b.fn.selected=function(c){if(c===undefined){c=true}return this.each(function(){var d=this.type;if(d=="checkbox"||d=="radio"){this.checked=c}else{if(this.tagName.toLowerCase()=="option"){var e=b(this).parent("select");if(c&&e[0]&&e[0].type=="select-one"){e.find("option").selected(false)}this.selected=c}}})};function a(){if(b.fn.ajaxSubmit.debug){var c="[jquery.form] "+Array.prototype.join.call(arguments,"");if(window.console&&window.console.log){window.console.log(c)}else{if(window.opera&&window.opera.postError){window.opera.postError(c)}}}}})(jQuery);