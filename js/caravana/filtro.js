/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
\u00c1 = Á
\u00c9 = É
\u00cd = Í
\u00d3 = Ó
\u00da = Ú
\u00f1 = ñ
\u00d1 = Ñ  
*/

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

//Estado nacimiento
$(document).on("change","#id_cat_estado", function () {

		$("select[name='id_cat_estado'] option:selected").each(function () {
		    id_cat_estado = $(this).attr("value");
            //alert(CVE_EDO_RES);

            var parametros = {		  
            'id_cat_estado' : id_cat_estado
            }

            frmAjax(parametros,'municipio_nacimiento','filtra_municipio_nacimiento');
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

      resto = 'class="formulario_new_via" value="Agregar nueva vialidad" ></input>';

      link.push('<input type="button" title="nueva_vialidad" '+resto);
      link.push('<input type="button" title="nueva_calle1" '+resto);
      link.push('<input type="button" title="nueva_calle2" '+resto);
      link.push('<input type="button" title="nueva_posterior" '+resto);      
    
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
    frmAjax(parametros,actualizar,'save_vialidad','POST',"../../mujer/registro/");
        
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
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/mujer/';
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
        //alert(parametros.tipo+' success');                
      },
      error: function(){
        $(selector+div).html(' ');
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

  //TIPO_VIALIDAD CALLE 1
  $(document).on("change","#CVE_TIPO_VIAL_CALLE1", function () {

		$("select[name='CVE_TIPO_VIAL_CALLE1'] option:selected").each(function () {

      CVE_TIPO_VIAL = $(this).attr("value");
      CVE_EST_MUN_LOC = $("#CVE_EDO_RES").val() + 
                        $("#id_cat_municipio").val() + 
                        $("#id_cat_localidad").val();

      //alert(CVE_EST_MUN_LOC);

      var parametros = {
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC
      }

      frmAjax(parametros,'calle1','filtra_tipo_vialidad_calle1');
    })

	}); 

    
  ///TIPO_VIALIDAD CALLE 2
  $(document).on("change","#CVE_TIPO_VIAL_CALLE2", function () {

    $("select[name='CVE_TIPO_VIAL_CALLE2'] option:selected").each(function () {

      CVE_TIPO_VIAL = $(this).attr("value");
      CVE_EST_MUN_LOC = $("#CVE_EDO_RES").val() + 
                        $("#id_cat_municipio").val() + 
                        $("#id_cat_localidad").val();

      //alert(CVE_EST_MUN_LOC);

      var parametros = {		  
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC
      }

      frmAjax(parametros,'calle2','filtra_tipo_vialidad_calle2');

    })

	});

  //TIPO_VIALIDAD CALLE POSTERIOR
  $(document).on("change","#CVE_TIPO_VIAL_CALLEP", function () {

    $("select[name='CVE_TIPO_VIAL_CALLEP'] option:selected").each(function () {

      CVE_TIPO_VIAL = $(this).attr("value");
      CVE_EST_MUN_LOC = $("#CVE_EDO_RES").val() + 
                        $("#id_cat_municipio").val() + 
                        $("#id_cat_localidad").val();

      //alert(CVE_EST_MUN_LOC);

      var parametros = {
        'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC
      }

      frmAjax(parametros,'posterior','filtra_tipo_vialidad_calle_posterior');

    })

	}); 

 //Filtrado de estados por país
 $(document).on("change","#id_pais", function () {

    $("select[name='id_pais'] option:selected").each(function () {

      id_pais = $(this).attr("value");                        

      //alert(id_pais);

      /*   
        if(id_pais != 90){
          valor = "<option value=''>Seleccione Estado de Origen</option><option value='33'>OTRO</option>";
          $('#id_cat_estado').html(valor);
        }else{*/
               
              var parametros = {
                'id_pais' : id_pais
              }

              frmAjax(parametros,'estado_origen','filtra_estado_origen');

            //}

          })

 }); 

/*
//filtra comunidad y dialecto indigena
$(document).on("change","#indigena", function () {

    //alert('hola'); 

		$("select[name='indigena'] option:selected").each(function () {

		    indigena= $(this).attr("value");
            //alert(indigena);
            input_comunidad= "<input type = 'text' id = 'comunidad_indigena' name = 'comunidad_indigena' class='nombre'/>";
            input_dialecto= "<input type = 'text' id = 'dialecto' name ='dialecto' class='nombre'/>";

            if(indigena == 'SI'){              
              $('#comunidad').html(input_comunidad);
              $('#dialecto').html(input_dialecto);
            }else{
              $('#comunidad').html('');
              $('#dialecto').html(''); 
            }

		})

	});
*/

//filtra comunidad y dialecto indigena
$(document).on("click","#indigena", function () {
    
  input_comunidad="<input type ='text' id='comunidad_indigena' name='comunidad_indigena' class='nombre'/>";
  input_dialecto="<input type ='text' id='dialecto' name ='dialecto' class='nombre'/>";
            
  if ($(this).is(":checked") === true){    
      $('#comunidad').html(input_comunidad);
      $('#dialecto').html(input_dialecto);
  } else {
      $('#comunidad').html('');
      $('#dialecto').html(''); 
  }
            
            
  });    
    
});