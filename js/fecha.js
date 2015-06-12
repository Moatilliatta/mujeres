jQuery(document).ready(function ($) { 
//Código general de fechas
    
   $( ".fecha" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dayNames: [ "Lunes","Martes","Miércoles","Jueves","Viernes","Sabado","Domingo"],
      dayNamesMin: [ "Lun","Mar","Mie","Jue","Vie","Sab","Dom" ],
      monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
      //appendText: " (aaaa-mm-dd)",
      dateFormat: "yy-mm-dd",
      minDate: new Date(1900, 1 - 1, 1),
      nextText: "Sig",
      yearRange: "1900:1996"  
    });

    $( "#from" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dayNames: [ "Lunes","Martes","Miércoles","Jueves","Viernes","Sabado","Domingo"],
      dayNamesMin: [ "Lun","Mar","Mie","Jue","Vie","Sab","Dom" ],
      monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
      dateFormat: "yy-mm-dd",
      nextText: "Sig",
        onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
    }
    });

    $( "#to" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dayNames: [ "Lunes","Martes","Miércoles","Jueves","Viernes","Sabado","Domingo"],
      dayNamesMin: [ "Lun","Mar","Mie","Jue","Vie","Sab","Dom" ],
      monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
      dateFormat: "yy-mm-dd",
      nextText: "Sig",
        onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
    }
    });
       
});