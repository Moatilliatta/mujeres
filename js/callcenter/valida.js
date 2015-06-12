jQuery(document).ready(function ($) {           
/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

//Máscara 
/*$(".tel_casa").mask("01-99-99-99-99-99");
$(".tel_office").mask("(01-99-99-99-99-99");
$(".tel_movil").mask("(044) 99-99-99-99-99");*/

//Validamos fecha
$( ".fecha" ).datepicker({ 
    dayNames: [ "Domingo", "Lunes", "Martes", "Mi\u00e9rcoles", "Jueves", "Viernes", "S\u00e1bado"],
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],     
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    dateFormat: "yy-mm-dd",
    yearRange: "1900:2030",
    changeYear: true,
    changeMonth: true 
    //maxDate:new Date()
    //showMonthAfterYear: true
    });

//función para fecha
function TodaysDate() {

 var currentTime = new Date()
 var year = currentTime.getFullYear()
 var month = currentTime.getMonth() + 1
 var day = currentTime.getDate()
 return year + "-" + month + "-" + day;

}

//Ponemos fecha en input
$("#btnToday").click(function() {
  var today = new Date();
  $(fecha_instalacion).datepicker('setDate', TodaysDate());
});     

//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

$("#formGrupoCallcenter").validate({

    rules: {  
            nombre: {required: true, minlength: 3},
            id_caravana: {required: true,range: [1, 10000]},
            id_callcenter_filtro: {required: true,range: [1, 10000]}
                       
        },

    messages: {
            descripcion: 'Nombre Incorrecto',
            fecha_instalacion: 'Seleccione una Caravana',
            direccion:'Seleccione un Filtro'
            
        }
    });


});