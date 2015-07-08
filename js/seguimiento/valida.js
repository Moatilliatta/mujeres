jQuery(document).ready(function ($) {   

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

  
  //borramos fecha al activar/desactivar una capacitacion
  $(document).on("click",".val_check", function () { 
  
  //Obtenemos ID previo al botón
  var fecha_activacion = $(this).closest('tr').find("input:text").attr('id');
  var colonia = $(this).closest('tr').find("select").attr('id');
  var observacion = $(this).closest('tr').find("textarea").attr('id');
  var boton = $(this).closest("tr").find("input:button").attr('id');  

      if($(this).is(':checked')) {  
        //alert("Está activado");   
        $("#"+fecha_activacion).attr('disabled',false);
        $("#"+colonia).attr('disabled',false);
        $("#"+observacion).attr('disabled',false);
        $("#"+boton).attr('disabled',false);

      }else{
        //alert("No está activado");    
        $("#"+fecha_activacion).attr('disabled',true);
        $("#"+colonia).attr('disabled',true);
        $("#"+observacion).attr('disabled',true);
        $("#"+boton).attr('disabled',true);
      }

  });
   //borramos fecha al activar/desactivar una capacitacion
  $(document).on("click",".val_check_1", function () { 
  
  //Obtenemos ID previo al botón
  var fecha_capacitacion = $(this).closest('tr').find("input:text").attr('id');
  var boton = $(this).closest("tr").find("input:button").attr('id');  

      if($(this).is(':checked')) {  
        //alert("Está activado");   
        $("#"+fecha_capacitacion).attr('disabled',false);
        $("#"+boton).attr('disabled',false);

      }else{
        //alert("No está activado");    
        $("#"+fecha_capacitacion).attr('disabled',true);
        $("#"+boton).attr('disabled',true);
      }

  });  

  $("#formcapacitacion").validate({

    rules: {
            'fecha_capacitacion[]' : 'required'
            
        },
    
    messages: {
            'fecha_capacitacion[]': 'Seleccione Punto'                          
        }

  });

  //Validamos que solamente los campos que NO sean disabled, sean tomados en cuenta
  //para que cada capacitación tenga su fecha
  $("#formcapacitacion").submit(function(event) {
    if ($("input:text[name='fecha_capacitacion\\[\\]']:enabled", this).filter(function() {
            return $.trim(this.value) == "";
        }).length) {
        window.alert("Debe asignar fecha a cada una de las capacitaciones");
        event.preventDefault();
    }
  });

  $("#formActCom").validate({

    rules: {
            'fecha_activacion[]' : 'required',
            'id_seg_colonia[]' : 'required'
            
        },
    
    messages: {
            'fecha_activacion[]' : 'Seleccione Fecha de Activaci\u00f3n',
            'id_seg_colonia[]' : 'Seleccione Colonia'
        }

  });

  //Validamos que solamente los campos que NO sean disabled, sean tomados en cuenta
  //para que cada capacitación tenga su fecha
  $("#formActCom").submit(function(event) {
    if ($("input:text[name='fecha_activacion\\[\\]']:enabled", this).filter(function() {
            return $.trim(this.value) == "";
        }).length) {
        window.alert("Debe asignar fecha a cada una de las activaciones comunitarias");
        event.preventDefault();
    }

    var options = $("select[name='id_seg_colonia\\[\\]']:enabled option:selected");

    var ron = $.map(options ,function(option) {
        
        if($.trim(option.value) == ""){
          return option.value;  
        }
        
    });

    if (ron.length) {
        window.alert("Debe asignar colonia a cada una de las activaciones comunitarias");
        event.preventDefault();
    }
  });

});