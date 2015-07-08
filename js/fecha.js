jQuery(document).ready(function ($) { 
//Código general de fechas
    
    var elems = {
      changeMonth: true,
      changeYear: true,
      dayNames: [ "Lunes","Martes","Miércoles","Jueves","Viernes","Sabado","Domingo"],
      dayNamesMin: [ "Lun","Mar","Mie","Jue","Vie","Sab","Dom" ],
      monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
      //appendText: " (aaaa-mm-dd)",
      nextText: "Sig",
      dateFormat: "yy-mm-dd"
    }

    //Fechas de nacimiento de 1900 a 1996
    var birth = {
      minDate: new Date(1900, 1 - 1, 1),
      yearRange: "1900:1996"  
    };

    //El rango es desde 1900 hasta la fecha actual    
    var todate = {
      minDate: new Date(1900, 1 - 1, 1),
      yearRange: "1900:" + new Date().getFullYear(),
      maxDate: "+0m +0w"
    }

    //El rango es de 1900 hasta 2030
    var datefree = {
      yearRange : "1900:2030"
    }
    
    //Fecha normal, hasta el día de hoy
    $( ".fecha" ).datepicker($.extend({},elems,todate));    

    //Fecha con rango libre, hasta 2030
    $(".fecha_libre").datepicker($.extend({},elems,datefree));
    
    //Fechas con rango de inicio y fin  (rango de 1900 a fecha actual)
    var from = {
      onClose : function( selectedDate ) { 
                  $( "#to" ).datepicker( "option", "minDate", selectedDate );
                }
    };

    //Establecemos arreglos
    $( "#from" ).datepicker($.extend({},elems,from,todate));

    var to = {
      onClose : function( selectedDate ) {
                  $( "#from" ).datepicker( "option", "maxDate", selectedDate );
                }
    };

    //console.log($.extend(elems,from,datefree));
    $( "#to" ).datepicker($.extend({},elems,to,todate));
    
    //función para fecha actual
    function TodaysDate() {
        
         var currentTime = new Date()
         var year = currentTime.getFullYear()
         var month = currentTime.getMonth() + 1
         var day = currentTime.getDate()
         return year + "-" + month + "-" + day;

    }

    //Ponemos fecha en input
    $(document).on("click",".today", function () {

        //Obtenemos instancia de fecha
        var today = new Date();        

        //Obtenemos el input PREVIO al botón, así modificaremos la fecha
        var input_id = $(this).prevAll('input:text').attr("id");

        //console.log('#'+input_id);

        //Agregamos fecha actual al input correspondiente
        $('#'+input_id).focus();
        $('#'+input_id).datepicker('setDate', TodaysDate());

    }); 

    //borramos fecha al activar/desactivar una capacitacion
    $(document).on("click",".limpiar", function () {
      
       //Obtenemos ID previo al botón
       var id = $(this).closest('tr').find("input:text").attr('id');

       //Obtenemos el input PREVIO al botón, así modificaremos la fecha
       var input_id = $(this).prevAll('input').attr("id");

        //Borramos el valor de la fecha 
        $("#"+id).val(''); 
      
    });


  //Campo de fecha sólo de lectura
  $(".fecha, #from, #to").attr('readOnly' , 'true' );

});