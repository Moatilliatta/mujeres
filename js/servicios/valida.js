jQuery(document).ready(function ($) {	        

/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

/*
//Máscara 
$(".tel_casa").mask("(01 99) 99-99-99-99");      
$(".tel_office").mask("(01 99) 99-99-99-99");
$(".tel_movil").mask("(044) 99-99-99-99-99");
*/

//Validamos fecha
$(document).on("click focus",".fecha", function () {
     $(this).datepicker({ 
    dayNames: [ "Domingo", "Lunes", "Martes", "Mi\u00e9rcoles", "Jueves", "Viernes", "S\u00e1bado"],
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],     
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    dateFormat: "yy-mm-dd",
    yearRange: "1900:2014",
    changeYear: true,
    changeMonth: true, 
    maxDate:new Date(),
    showMonthAfterYear: true
    });
});

$( ".fecha" ).datepicker({ 
    dayNames: [ "Domingo", "Lunes", "Martes", "Mi\u00e9rcoles", "Jueves", "Viernes", "S\u00e1bado"],
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],     
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    dateFormat: "yy-mm-dd",
    yearRange: "1900:2014",
    changeYear: true,
    changeMonth: true, 
    maxDate:new Date(),
    showMonthAfterYear: true
    });

//función para fecha
function TodaysDate() {
 var currentTime = new Date();
 var year = currentTime.getFullYear()
 var month = currentTime.getMonth() + 1
 var day = currentTime.getDate()
 return year + "-" + month + "-" + day;

}

//Ponemos fecha en input
$(document).on("click",".today", function () {

    //obtener id del botón
    var boton_id = $(this).attr("id");

    //Obtenemos instancia de fecha
    var today = new Date();

    //alert('#fecha_asignado'+boton_id+TodaysDate());

    //Agregamos fecha actual al input correspondiente
    $('#fecha_asignado'+boton_id).focus();
    $('#fecha_asignado'+boton_id).datepicker('setDate', TodaysDate());

});     

//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

//Beneficiario_pys
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.format("Please enter exactly {0} characters."));

      $("#formBen_pys").validate({
        rules: {  
            id_beneficiario: {required: true,range: [1, 10000]},
            cod_prog: {required: true,range: [1, 10000]},
            cod_rpys: {required: true,range: [1, 10000]},
            fecha_asignado : 'true'                    
        },

        messages: {

            id_beneficiario: 'Seleccione el Beneficiario',
            cod_prog: 'Seleccione Programa',
            cod_ropys: 'Seleccione Servicios',
            fecha_asignado : 'Ingrese Fecha de Asignac\u00f3in'                    
        }

    });

    $('#mod_pys').click(function() {

        window.location.href = '../../servicios/serv/lista_mujer_serv.php';
        return false;
    });  

    $('#mod_beneficiario').click(function() {

        window.location.href = '../../mujer/registro/lista_mujer.php';
        return false;
    });  

});