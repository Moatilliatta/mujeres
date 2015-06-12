jQuery(document).ready(function ($) {   
    $("table").tablesorter({widgets: ['zebra']});
    $( "#tabs" ).tabs();

    //Quitamos tablas vacías
    $('table').each(function () {
        var tds = $(this).children('tbody').children('tr').children('td').length;

        if(tds == 0){
            //Quitamos datos vacíos            
            $(this).closest("h4 + *").prev().remove();
            $(this).closest("h3 + *").prev().remove();
            $(this).remove();            
        }
    });

/*
    $(document).on("click","input[name=ID_C_SERVICIO]", function (event) {    

      var radio = $('input[name=ID_C_SERVICIO]:checked').val();
      alert(radio);

     });
*/

    $(document).on("click","#guardar", function (event) {     
        //Detenemos comportamiento por default
        event.preventDefault();
        event.stopPropagation();

       var radio = $('input[name=ID_C_SERVICIO]:checked').val();       

   	   if(radio == null){
   	   	alert('Seleccione un Servicio');
   	   }else{
   	   	$("#formAsig").submit();
   	   }

   });
   
});