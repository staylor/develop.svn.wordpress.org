jQuery(document).ready(function(d){var b=false,f,e,c,a;f=function(i,h){var g=d("<span>"+d("name",i).text()+"</span>").html(),j=d("tag",i).attr("id");b[b.length]=new Option(g,j)};e=function(g,i){var h=d(i.parsed.responses[0].data);if(h.length==1){inlineEditTax.addEvents(d(h.id))}};a=function(h,g){var j=d("tag",h).attr("id"),i;for(i=0;i<b.length;i++){if(j==b[i].value){b[i]=null}}};c=function(g){g.data.taxonomy=d('input[name="taxonomy"]').val();if("undefined"!=showNotice){return showNotice.warn()?g:false}return g};if(b){d("#the-list").wpList({addAfter:f,delBefore:c,delAfter:a})}else{d("#the-list").wpList({addAfter:e,delBefore:c})}d('.delete a[class^="delete"]').click(function(){return false})});