jQuery(function($) {
	var options = document.forms['addcat'].category_parent.options;

	var addAfter = function( r, settings ) {
		var name = $("<span>" + $('name', r).text() + "</span>").html();
		var id = $('cat', r).attr('id');
		options[options.length] = new Option(name, id);
	}

	var delAfter = function( r, settings ) {
		var id = $('cat', r).attr('id');
		for ( var o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	}

	var a = $('#the-list').wpList( { addAfter: addAfter, delAfter: delAfter } );
});
