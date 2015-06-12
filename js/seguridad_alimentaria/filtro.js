jQuery(document).ready(function ($) {

  //Filtramos localidades    
  $(document).on("change","#CVE_ENT_MUN", function () {

  	$("select[name='CVE_ENT_MUN'] option:selected").each(function () {

		    CVE_ENT_MUN = $(this).attr("value");

            var parametros = {		  
            'CVE_ENT_MUN' : CVE_ENT_MUN,
              }

            //Localidad
            frmAjax(parametros,'localidad','filtra_localidad_centro');
		});
     
 	});   

  //filtramos tipo de caratulas    
  $(document).on("change","#id_municipio_caratula", function () {

   //alert('hla');  

		$("select[name='id_municipio_caratula'] option:selected").each(function () {

		    id_municipio_caratula = $(this).attr("value");
            
            var parametros = {		  
            'CVE_MUN' : id_municipio_caratula,
              }

            //Localidad
            frmAjax(parametros,'tipo_caratula','filtra_caratula');
		});
     
 	});   


  //Filtramos comunidades y centros
  $(document).on("change",".comunidad", function () {
    
    //Obtenemos variables
    id_municipio_caratula = $('#id_municipio_caratula').val();
    axo_padron = $('#axo_padron').val();
    nom_reporte = $('#nom_reporte').val();
    id_centro_atencion = $('id_centro_atencion').val();
    CVE_ENT_MUN_LOC = $('CVE_ENT_MUN_LOC').val();

    var parametros = {     
        'id_municipio_caratula' : id_municipio_caratula,
        'axo_padron' : axo_padron,
        'nom_reporte' : nom_reporte,
        'id_centro_atencion' : id_centro_atencion,
        'CVE_ENT_MUN_LOC' : CVE_ENT_MUN_LOC
        
    }

    //alert('mun|'+id_municipio_caratula+'| '+axo_padron+' '+nom_reporte);

    if(axo_padron != '' && nom_reporte !== '' && id_municipio_caratula !== ''){
    
      switch(nom_reporte){
         case 'selecciona_centro_proalimne': case 'selecciona_centro_firmas': case 'selecciona_centro_firmas_frutas':
         
              //Comunidad y Centro de Atención
              frmAjax(parametros,'com','filtra_comunidad');          
              break;
              case 'selecciona_centro':
              parametros.actualiza_comunidad= 'NO';
              //alert(parametros);
              frmAjax(parametros,'com','filtra_comunidad');
              $('#centro').html('')   
              break;
         default:
              $('#com').html('');
              $('#centro').html('');
              break;
      }                    

    }else{
      $('#enviar').click();
    }    

  });  
  
  //Filtramos comunidades y centros
  $(document).on("change","#CVE_ENT_MUN_LOC", function () {
    
      $("select[name='CVE_ENT_MUN_LOC'] option:selected").each(function () {

          CVE_ENT_MUN_LOC = $(this).attr("value");

            var parametros = {      
            'CVE_ENT_MUN_LOC' : CVE_ENT_MUN_LOC,
              }

          //Localidad
          frmAjax(parametros,'centro','filtra_centro');

      });
   
  });
    

  //Form Ajax Genérico
   function frmAjax(parametros,div,accion,tipo,ruta,selector){       

       //Valores predeterminados
       ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/seguridad_alimentaria/';
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

 //Filtro de vialidad
 $(document).on("change","#CVE_TIPO_VIAL", function () {

    $("select[name='CVE_TIPO_VIAL'] option:selected").each(function () {

        CVE_TIPO_VIAL = $(this).attr("value");
        CVE_EST_MUN_LOC = $("#CVE_EST_MUN_LOC").val();
        //alert(CVE_EST_MUN_LOC);
        
        var parametros = {      
            'CVE_TIPO_VIAL' : CVE_TIPO_VIAL,
            'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC
            }        

        frmAjax(parametros,'vialidad','filtra_tipo_vialidad');          

    })

  });
 

 //Filtro tipo vialidad, asentamiento, vialidad
 $(document).on("change","#CVE_EST_MUN_LOC", function () {
    
  $("select[name='CVE_EST_MUN_LOC'] option:selected").each(function (){

     CVE_EST_MUN_LOC = $(this).attr("value");
     
     CVE_EDO_RES = CVE_EST_MUN_LOC.substring(1,2);
     id_cat_municipio = CVE_EST_MUN_LOC.substring(2,3);
     id_cat_localidad = CVE_EST_MUN_LOC.substring(5,4);
      
      var parametros = {      
            'CVE_EDO_RES' : CVE_EDO_RES,
            'id_cat_municipio' : id_cat_municipio,     
            'id_cat_localidad' : id_cat_localidad
            }

    //Asentamiento
    frmAjax(parametros,'asentamiento','filtra_asentamiento','filtra_vialidad');

    //Vialidad
    parametros.tipo = 'vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_vialidad');
    
    /*
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
    */

    
    var parametros = {
        'CVE_EST_MUN_LOC' : CVE_EST_MUN_LOC 
      }

    //Tipo de vialidad en Vialidad
    parametros.tipo = 'tipo_vialidad';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');
/*
    //Tipo de vialidad en Calle 1
    parametros.tipo = 'tipo_vialidad_calle1';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Tipo de vialidad en Calle 2
    parametros.tipo = 'tipo_vialidad_calle2';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');

    //Calle Posterior
    parametros.tipo = 'tipo_vialidad_calle3';
    frmAjax(parametros,parametros.tipo,'filtra_municipio_tipo_vialidad');
*/
    })

  });



});    