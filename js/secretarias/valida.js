jQuery(document).ready(function ($) {   


//Ponemos fecha en input
$("#btnToday").click(function() {

  //Obtenemos curp
  var curp = $("#curp").val(); 

  //alert('cambio');

  //Si tiene tamaño completo, verificamos dicha curp
  if(curp.length == 18){
      $('#curp').click();
    }  

});     

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