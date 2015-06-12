jQuery(document).ready(function ($) {
     
     //Código General Jquery       
    $("#CVE_EDO_RES").change(function(){

		$("select[name='CVE_EDO_RES'] option:selected").each(function () {

		    CVE_EDO_RES = $(this).attr("value");
        //alert(CVE_EDO_RES);

        var parametros = {
          'CVE_EDO_RES' : CVE_EDO_RES
        }

        frmAjax(parametros,'municipio','filtra_municipio');

        //Localidad
        //  frmAjax(parametros,'localidad','filtra_localidad');

        //Código Postal
        //frmAjax(parametros,'cp','filtra_cp');

        //Ponemos valores predeterminados
        reiniciaSelVialidades();

      })

	});
    
 $(document).on("change","#id_cat_municipio", function () {

  var CVE_EDO_RES = $("#CVE_EDO_RES").val();

  $("select[name='id_cat_municipio'] option:selected").each(function () {

    id_cat_municipio = $(this).attr("value");

    var parametros = {
      'id_cat_municipio' : id_cat_municipio,
      'CVE_EDO_RES' : CVE_EDO_RES
    }

    //Localidad
    frmAjax(parametros,'localidad','filtra_localidad');

    //Código Postal
    frmAjax(parametros,'cp','filtra_cp');

    //Ponemos valores predeterminados
    reiniciaSelVialidades();
    
		})

	}); 
    
    //Asentamiento Sepomex
$(document).on("change","#CODIGO", function () {    

		$("select[name='CODIGO'] option:selected").each(function () {

		    CODIGO = $(this).attr("value");

        var parametros = {
          'CODIGO' : CODIGO
        }

        //Localidad
        frmAjax(parametros,'asen_sepomex','filtra_asen_sepomex');

      })

	});
  //filtra tipo de vialidad y vialidades  
 $(document).on("change","#id_cat_localidad", function () {
  
  var CVE_EDO_RES = $("#CVE_EDO_RES").val();
  var id_cat_municipio = $("#id_cat_municipio").val();

  $("select[name='id_cat_localidad'] option:selected").each(function (){

	   id_cat_localidad = $(this).attr("value");
     //alert(id_cat_localidad);
      
      var parametros = {		  
            'id_cat_municipio' : id_cat_municipio,
            'CVE_EDO_RES' : CVE_EDO_RES,
            'id_cat_localidad' : id_cat_localidad
            }

    //Asentamiento
    //frmAjax(parametros,'asentamiento','filtra_asentamiento','filtra_vialidad');

    //Vialidad
    parametros.tipo = 'vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');
    
    //Calle 1
    parametros.tipo = 'calle1';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');
    
    //Calle 2
    parametros.tipo = 'calle2';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');

    //Calle Posterior    
    parametros.tipo = 'posterior';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');

    var link = new Array();    

    //Si ha sido seleccionada la localidad
    if(id_cat_localidad > 0){      

      resto = 'class="formulario_new_via" value="Agregar nueva vialidad" ></input><label>Si no encuentra la vialidad que está buscando, haga clic en este botón para agregarla</label>';
      
      
      link.push('<div class = cuadroinfo><input type="button" title="nueva_vialidad" '+resto+'<div>');
      link.push('<div class = cuadroinfo><input type="button" title="nueva_calle1" '+resto+'<div>');
      link.push('<div class = cuadroinfo><input type="button" title="nueva_calle2" '+resto+'<div>');
      link.push('<div class = cuadroinfo><input type="button" title="nueva_posterior" '+resto+'<div>');      
      
    
    }else{
          
        $('.formulario_new_via').html('');

    }

    //Ponemos link para agregar vialidad
    $('#agrega_link_via').html(link[0]);
    //Ponemos link para agregar vialidad
    $('#agrega_link_calle1').html(link[1]);
    //Ponemos link para agregar vialidad
    $('#agrega_link_calle2').html(link[2]);
    //Ponemos link para agregar vialidad
    $('#agrega_link_posterior').html(link[3]);


    //filtrado de tipo de vialidadades principal
    var CVE_EST_MUN_LOC = CVE_EDO_RES + id_cat_municipio + id_cat_localidad;

    var parametros = {
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC 
      }

    //Tipo de vialidad en Vialidad
    parametros.tipo = 'tipo_vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Tipo de vialidad en Calle 1
    parametros.tipo = 'tipo_vialidad_calle1';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Tipo de vialidad en Calle 2
    parametros.tipo = 'tipo_vialidad_calle2';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Calle Posterior
    parametros.tipo = 'tipo_vialidad_calle3';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

		})

	});
   
   //Cargamos formulario de Nueva Vialidad
$(document).on("click",".formulario_new_via", function () {

  var CVE_EDO_RES = $("#CVE_EDO_RES").val();
  var id_cat_municipio = $("#id_cat_municipio").val();
  var id_cat_localidad_nueva = $("#id_cat_localidad").val();
  var actualizar = $(this).attr('title');

  var parametros = {		  
    'id_cat_municipio_nueva' : id_cat_municipio,
    'CVE_EDO_RES_NUEVA' : CVE_EDO_RES,
    'id_cat_localidad_nueva' : id_cat_localidad_nueva,
    'actualizar' : actualizar
  }
    frmAjax(parametros,actualizar,'agrega_vialidad');

});

//Guardamos una vialidad
$(document).on("click","#guarda_vialidad", function(e){

  e.preventDefault();
  
  var r=confirm("\u00BFEst\u00e1s seguro de agregar una vialidad?");

  if(r==true){
    var id_edicion_nueva = $("#id_edicion_nueva").val();
    var CVE_EDO_RES_NUEVA = $("#CVE_EDO_RES_NUEVA").val();
    var id_cat_municipio_nueva = $("#id_cat_municipio_nueva").val();
    var CVE_TIPO_VIAL_NUEVA = $("#CVE_TIPO_VIAL_NUEVA").val();
    var NOM_VIA_NUEVA = $("#NOM_VIA_NUEVA").val();
    var id_cat_localidad_nueva = $("#id_cat_localidad_nueva").val();

    var parametros = {
      'id_edicion' : id_edicion_nueva,
      'CVE_EDO_RES' : CVE_EDO_RES_NUEVA,
      'id_cat_municipio' : id_cat_municipio_nueva,
      'CVE_TIPO_VIAL' : CVE_TIPO_VIAL_NUEVA,
      'NOM_VIA'  : NOM_VIA_NUEVA,
      'id_cat_localidad' : id_cat_localidad_nueva,
      'ajax' : 'ajax'
    }

    //Obtenemos valor a actualizar
    actualizar = $(this).attr('name');

    //alert('edso: '+CVE_EDO_RES_NUEVA+' mun: '+id_cat_municipio_nueva+' loc: '+id_cat_localidad_nueva+' via: '+NOM_VIA_NUEVA+' tipo:'+CVE_TIPO_VIAL_NUEVA);
    frmAjax(parametros,actualizar,'save_vialidad','POST',"../../beneficiario/registro/");
        
    //Refrescamos los campos de vialidad
    var parametros = {
      'id_cat_municipio' : id_cat_municipio_nueva,
      'CVE_EDO_RES' : CVE_EDO_RES_NUEVA,
      'id_cat_localidad' : id_cat_localidad_nueva
    }

    //Vialidad
    parametros.tipo = 'vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');
            
    //Ponemos valores predeterminados
    //reiniciaSelVialidades();

    //actualizar los tipos de vialidades nuevas 
    var CVE_EST_MUN_LOC = CVE_EDO_RES_NUEVA + id_cat_municipio_nueva + id_cat_localidad_nueva;

    var parametros = {
      'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC 
    }

    //Vialidad
    parametros.tipo = 'tipo_vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Calle 1
    parametros.tipo = 'tipo_vialidad_calle1';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Calle 2
    parametros.tipo = 'tipo_vialidad_calle2';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Calle Posterior
    parametros.tipo = 'tipo_vialidad_calle3';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    }
                       
})   
    
     function reiniciaSelVialidades(){

        //Ponemos valores predeterminados
        
       // $('#lForm select[id!="makeClick"] select[id!="stateClick"] select[id!="cityClick"] option:not(:first)').remove().end()                
            
            $("#CVE_TIPO_VIAL option:not(:first)").remove().end();
            $("#CVE_TIPO_VIAL").val($("#CVE_TIPO_VIAL option:first").val());
            
            $("#CVE_TIPO_VIAL_CALLE1 option:not(:first)").remove().end();
            $("#CVE_TIPO_VIAL_CALLE1").val($("#CVE_TIPO_VIAL_CALLE1 option:first").val());

            $("#CVE_TIPO_VIAL_CALLE2 option:not(:first)").remove().end();
            $("#CVE_TIPO_VIAL_CALLE2").val($("#CVE_TIPO_VIAL_CALLE2 option:first").val());

            $("#CVE_TIPO_VIAL_CALLEP option:not(:first)").remove().end();
            $("#CVE_TIPO_VIAL_CALLEP").val($("#CVE_TIPO_VIAL_CALLEP option:first").val());

              
            $("#CVE_VIA option:not(:first)").remove().end();
            $("#CVE_VIA").val($("#CVE_VIA option:first").val());

            $("#entre_calle1 option:not(:first)").remove().end();
            $("#entre_calle1").val($("#entre_calle1 option:first").val());

            $("#entre_calle2 option:not(:first)").remove().end();
            $("#entre_calle2").val($("#entre_calle2 option:first").val());

            $("#calle_posterior option:not(:first)").remove().end();
            $("#calle_posterior").val($("#calle_posterior option:first").val());
            
            $("#CODIGO option:not(:first)").remove().end();
            $("#CODIGO").val($("#CODIGO option:first").val());
            
            $("#id_cp_sepomex option:not(:first)").remove().end();
            $("#id_cp_sepomex").val($("#id_cp_sepomex option:first").val());
            
            $("#id_cat_localidad option:not(:first)").remove().end();
            $("#id_cat_localidad").val($("#id_cat_localidad option:first").val()); 
                                   

    }   
   
   //Form Ajax Genérico
   function frmAjax(parametros,div,accion,tipo,ruta,selector){

    //Valores predeterminados
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/trabajo_social/';
    tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
    selector = typeof selector !== 'undefined' ? selector : '#';

    //alert(selector+div);

    $.ajax({
      type: tipo,
      url: ruta+accion+".php",
      data: parametros,
      beforeSend: function(){
        $(selector+div).html('<img src="../../css/img/loader_sug.gif"/>Buscando');
        //alert(parametros.tipo+' before');
      },
      success: function( respuesta ){
        $(selector+div).html(respuesta);    
        
        if(respuesta.length <= 0){
          $(".datos_aspirante, .ui-combobox-input").removeAttr('disabled');
          $('#agregar_programa').html('');
        }
      },
      error: function(){
        $(selector+div).html(' ');
        alert('Error');
      }
    });
    
   }
   
   //TIPO_VIALIDAD
 $(document).on("change","#CVE_TIPO_VIAL", function () {

    $("select[name='CVE_TIPO_VIAL'] option:selected").each(function () {

      CVE_TIPO_VIAL = $(this).attr("value");
      CVE_EST_MUN_LOC = $("#CVE_EDO_RES").val() + 
                        $("#id_cat_municipio").val() + 
                        $("#id_cat_localidad").val();

      //alert(CVE_EST_MUN_LOC);

      var parametros = {
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC
      }

      frmAjax(parametros,'vialidad','filtra_tipo_vialidad');

		})

	});

//muestra posibles duplicados de aspirantes
$(document).on("change",".cambia_asp", function () {

      //Obtenemos valores
      nombres = $("#nombres").val();
      paterno = $('#paterno').val();      
      materno = $("#materno").val();

      var parametros = {
        'nombres' : nombres,
        'paterno' : paterno,        
        'materno' : materno
      }

  //Solo buscamos si se tiene un nombre y apellido
  if(nombres !== 'undefined' && nombres != '' &&
    paterno !== 'undefined' && paterno != '' ){

        frmAjax(parametros,'aspirantes_duplicados','filtra_aspirante');
        frmAjax(parametros,'beneficiarios_duplicados','filtra_beneficiario');   
  }    

	});

//muestra posibles duplicados de aspirantes por domicilio
$(document).on("change",".domicilio", function () {

      //Obtenemos valores
      
      CVE_TIPO_VIAL = $('#CVE_TIPO_VIAL').val();
      CVE_VIA = $('#CVE_VIA').val();
      num_ext = $('#num_ext').val();
        
     
      var parametros = {
        
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_VIA' : CVE_VIA,        
        'num_ext' : num_ext
        
        
      }
    //Solo buscamos si se tiene un nombre y apellido
  if(CVE_TIPO_VIAL !== 'undefined' && CVE_TIPO_VIAL != '' &&
    CVE_VIA !== 'undefined' && CVE_VIA != '' ){

        frmAjax(parametros,'domicilio_duplicado','filtra_domicilio');
          
  }  

	}); 
//muestra posibles duplicados de beneficiario por domicilio
$(document).on("change",".domicilio", function () {

      //Obtenemos valores
      
      CVE_TIPO_VIAL = $('#CVE_TIPO_VIAL').val();
      CVE_VIA = $('#CVE_VIA').val();
      num_ext = $('#num_ext').val();
        
     
      var parametros = {
        
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_VIA' : CVE_VIA,        
        'num_ext' : num_ext
        
        
      }
    //Solo buscamos si se tiene un nombre y apellido
  if(CVE_TIPO_VIAL !== 'undefined' && CVE_TIPO_VIAL != '' &&
    CVE_VIA !== 'undefined' && CVE_VIA != '' ){

        frmAjax(parametros,'domicilio_beneficiario','filtra_domicilio_beneficiario');
          
  }  

	});     
       
    
  
  //Elección de Beneficiario
 $(document).on('change','input[type=radio]',function(){

    //Obtenemos valor
    valor = $(this).val();    
    name = $(this).attr('name');


    /*En caso de ser un beneficiario válido, quitamos los datos del formulario
    caso contrario, los restauramos*/
    if(valor > 0){      
      
      //Deshabilitamos valores del formulario
      $(".datos_aspirante, .ui-combobox-input").attr('disabled','disabled');  

      //Solo se puede seleccionar un tipo de radio button
      $('input[type=radio][name!='+name+']').attr('checked',false);

      //Seleccionamos el tipo de radio deseado
      $('#id_'+name).attr('value',valor);

      //Mostramos los programas disponibles
      frmAjax(null,'agregar_programa','agregar_programa');
    }

 });

//Quitamos selección
$(document).on("click",".quitar", function () {
            
      $(".datos_aspirante, .ui-combobox-input").removeAttr('disabled');
      $('#agregar_programa').html('');
      //$('#id_aspirante').attr('value','');
      $('#id_beneficiario').attr('value','');      
      $('input[type=radio]').attr('checked',false);

  });

//Cargamos formulario de Nueva Vialidad
$(document).on("click","#busca_beneficiario", function () {

   //Obtenemos valores
      nombres = $("#b_nombres").val();
      paterno = $('#b_paterno').val();      
      materno = $("#b_materno").val();
      curp = $("#b_curp").val();

      var parametros = {
        'nombres' : nombres,
        'paterno' : paterno,        
        'materno' : materno,
        'curp' : curp
      }

    frmAjax(parametros,'busqueda_ben','busca_beneficiario');

});

//Cargamos tabla de beneficiarios ligados a un expediente
$(document).on("click",".agrega_ben", function () {

  var id_beneficiario = $(this).attr('id');
  var id_beneficiario_pivote = $("#id_beneficiario").val();
  var id_trab_expediente = $("#id_edicion").val();

  //alert(id_beneficiario+' '+id_beneficiario_pivote+' '+id_trab_expediente);

  var parametros = {
          'id_beneficiario' : id_beneficiario,
          'id_beneficiario_pivote' : id_beneficiario_pivote,        
          'id_trab_expediente' : id_trab_expediente,
          'accion': 'agregar'
        }

  frmAjax(parametros,'listado_ben','beneficiarios_asociados');

});

//Eliminamos elemento del "carrito" de Artículos
$(document).on("click","#elimina_art", function () {

        id_beneficiario = $(this).attr("name");        

        var parametros = {
          'id_beneficiario' : id_beneficiario,
          'accion': 'eliminar'
        };

  frmAjax(parametros,'listado_ben','beneficiarios_asociados');

});

//En caso de que el campo condicion este en cerrado
 $(document).on("change","#condicion", function () {
   //alert('entro');
    //Obtenemos valor
   $("select[name='condicion'] option:selected").each(function () {
      //Obtenemos valor
      condicion = $(this).attr("value");
      
      //alert(condicion);
       if(condicion == 'CERRADO'){   
          $(".condicion").attr('disabled','disabled');
          $('#id_motivo_cierre').removeAttr('disabled');
          $('#observacion_cierre').removeAttr('disabled');
       }else{
          $(".condicion").removeAttr('disabled');
          $("#id_motivo_cierre").attr('disabled','disabled');
          $("#observacion_cierre").attr('disabled','disabled');
       }

    })

 });

 //Filtrar tipos de servicios especificos de producto/servicio      
 $(document).on('change','#id_producto_servicio',function(){
    
        
		$("select[name='id_producto_servicio'] option:selected").each(function () {

		    id_producto_servicio = $(this).attr("value");
        //alert(id_producto_servicio);

        var parametros = {
          'id_producto_servicio' : id_producto_servicio
        }

        frmAjax(parametros,'tipo_servicio','filtra_tipo_servicio');

        

      })

	});
//Filtramos los servicios especificos.
$(document).on('change','#id_tipo_servicio',function(){
    
        
		$("select[name='id_tipo_servicio'] option:selected").each(function () {

		    id_tipo_servicio = $(this).attr("value");
      //  alert(id_tipo_servicio);

        var parametros = {
          'id_tipo_servicio' : id_tipo_servicio
        }

        frmAjax(parametros,'servicio_especifico','filtra_servicio_especifico');

        

      })

	}); 
    

});