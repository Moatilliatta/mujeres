jQuery(document).ready(function ($) {   

//Validamos fecha
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

 var currentTime = new Date()
 var year = currentTime.getFullYear()
 var month = currentTime.getMonth() + 1
 var day = currentTime.getDate()
 return year + "-" + month + "-" + day;

}

//Ponemos fecha en input
$("#btnToday").click(function() {
  var today = new Date();
  $("#fecha_creado").datepicker('setDate', TodaysDate()); 

  //Obtenemos curp
  var curp = $("#curp").val(); 

  //alert('cambio');

  //Si tiene tamaño completo, verificamos dicha curp
  if(curp.length == 18){
      $('#curp').click();
    }  

});     

//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

$("#formSec").validate({

    rules: {
            nombre: 'required',
            fecha_creado: 'required'
        },
    
    messages: {
            nombre: 'Seleccione Tipo de Vialidad',
            descripcion: 'Ingrese Vialidad Nueva',
            fecha_creado : 'Ingrese Fecha de Creaci\u00f3n'              
        }

    });


});