//buscador sensible a acentos    
var accentMap = {
"á": "a",
'é': 'e',
'í': 'i',
"ó": "o",
'ú': 'u',
'Á': 'A',
'É': 'E',
'Í': 'I',
'Ó': 'O',
'Ú': 'U'
};

var normalize = function( term ) {
var ret = "";
for( var i = 0; i < term.length; i++ ){
	ret += accentMap[ term.charAt(i) ] || term.charAt(i);
}
return ret;
};

/*
$(".combobox" ).autocomplete({
    source: function( request, response ){
        response($.ui.autocomplete.filterAI(data, request.term));
    })
});
*/

(function( $ ) {
$.widget( "ui.combobox", {
_create: function() {
this.wrapper = $( "<span>" )
.addClass( "ui-combobox" )
.insertAfter( this.element );

this._createAutocomplete();
this._createShowAllButton();

},

_createAutocomplete: function() {

var selected = this.element.children( ":selected" ),
select = this.element,
self = this,
value = selected.val() ? selected.text() : "Click para buscar...";

this.input = $( "<input>" )
.appendTo( this.wrapper )
.val( value )
.attr( "title", "" )
.css( "display", "block" )
.addClass( "ui-state-default ui-combobox-input ui-widget ui-widget-content ui-corner-left" )
.autocomplete({
	delay: 0,
	minLength: 0,
	source: $.proxy( this, "_source" ),
	select: function( event, ui ) {
	    ui.item.option.selected = true;
	    self._trigger( "selected", event, {
	        item: ui.item.option
	    });
	    select.trigger("change");                            
	},
})
.tooltip({
	tooltipClass: "ui-state-highlight"
})
.css("width",this.element.css("width"))
.on("click",this.input, function () {
	//.click(function() {
	if(this.value=='Click para buscar...') this.value='';
})

.on("blur",this.input, function () {
	//.blur(function() {
	if(this.value=='') this.value='Click para buscar...';
});

this._on( this.input, {
autocompleteselect: function( event, ui ) {
	ui.item.option.selected = true;
	this._trigger( "select", event, {
	item: ui.item.option
});
},

autocompletechange: "_removeIfInvalid"

});

},

_createShowAllButton: function() {

var wasOpen = false;
/*

$( "<a>" )



.attr( "tabIndex", -1 )



.attr( "title", "Show All Items" )



.tooltip()



.appendTo( this.wrapper )



.button({



icons: {



primary: "ui-icon-triangle-1-s"



},



text: false



})



.removeClass( "ui-corner-all" )



.addClass( "ui-corner-right ui-combobox-toggle" )



.mousedown(function() {



wasOpen = input.autocomplete( "widget" ).is( ":visible" );



})



.click(function() {



input.focus();



// Close if already visible



if ( wasOpen ) {



return;



}



// Pass empty string as value to search for, displaying all results



input.autocomplete( "search", "" );



});



 */



},

_source: function( request, response ) {

var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );

response( this.element.children( "option" ).map(function() {

		var text = $( this ).text();

		if(this.value&&(!request.term||matcher.test(text)||matcher.test(normalize(text)))){
			
			var vals = {
					label: text,
					value: text,
					option: this
					};

			
			return vals;
		}			

		}) 

	);

   // response($.ui.autocomplete.filterAI(data, request.term));

},

_removeIfInvalid: function( event, ui ) {
// Selected an item, nothing to do

if ( ui.item ) {
	return;
}

// Search for a match (case-insensitive)

var value = this.input.val(),
valueLowerCase = value.toLowerCase(),

valid = false;

this.element.children( "option" ).each(function() {



if ( $( this ).text().toLowerCase() === valueLowerCase ) {



this.selected = valid = true;



return false;



}



});



// Found a match, nothing to do



if ( valid ) {



return;



}



// Remove invalid value







this.input



.val( "" )



.attr( "title", value + " no hay ninguna coincidencia" )



/*.tooltip( "open" );*/



this.element.val( "" );



this._delay(function() {



this.input.tooltip( "close" ).attr( "title", "" );



}, 2500 );

 



this.input.data( "ui-autocomplete" ).term = "";



},



_destroy: function() {



this.wrapper.remove();



this.element.show();



}



});



})( jQuery );



 

$(function() {



	$( ".combobox" ).combobox();

	$( document ).on("click",".toogle", function () {

		$( ".combobox" ).toggle();

	});



});