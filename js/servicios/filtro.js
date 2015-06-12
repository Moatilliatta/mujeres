jQuery(document).ready(function ($) {	
	
	 //Filtra componentes (programas)
    $(document).on("change",".filtra_programa", function () {

    id_mujeres_avanzando = $('#id_mujeres_avanzando').val();    

        if(id_mujeres_avanzando > 0){

        filtra_programas();

        }else{
            alert('Seleccione una beneficiaria para verificar sus servicios');
        }    		

	});
    
     //Filtramos dependencias segun su grado 
    $(document).on("change","#id_grado", function () {

    id_mujeres_avanzando = $('#id_mujeres_avanzando').val();            

         $("select[name='id_grado'] option:selected").each(function () {

		      id_grado = $(this).attr("value");
          //alert(ID_C_DEPENDENCIA);

            var parametros = {		  
            'id_grado' : id_grado,
            'id_mujeres_avanzando' : id_mujeres_avanzando
            }
            
            frmAjax(parametros,'dependencias','filtra_dependencia');            

		    })
        	
	});
    
    //Filtra programas de una dependencia 
    $(document).on("change",".filtra_programa_busqueda", function () {

    id_mujeres_avanzando = $('#id_mujeres_avanzando').val();           
        filtra_programas();
         
	});
    
    //funcion para filtrar los programas de una dependencia
    function filtra_programas(){
      
      $("select[name='ID_C_DEPENDENCIA'] option:selected").each(function () {

		      ID_C_DEPENDENCIA = $(this).attr("value");

            var parametros = {		  
            'ID_C_DEPENDENCIA' : ID_C_DEPENDENCIA,
            'id_mujeres_avanzando' : id_mujeres_avanzando
            }
            //alert(ID_C_DEPENDENCIA);
            frmAjax(parametros,'programas','filtra_programa');
                      

		    })  
       
        
    }
     //Filtramos los servicios de una dependencia 
    $(document).on("change","#ID_C_DEPENDENCIA", function () {

    id_mujeres_avanzando = $('#id_mujeres_avanzando').val();            

         $("select[name='ID_C_DEPENDENCIA'] option:selected").each(function () {

		     ID_C_DEPENDENCIA = $(this).attr("value"); 
             //alert(id_mujeres_avanzando);

            var parametros = {		  
            'ID_C_DEPENDENCIA' : ID_C_DEPENDENCIA,
            'id_mujeres_avanzando' : id_mujeres_avanzando
            }
            
            frmAjax(parametros,'servicioss','filtra_servicio');            

		    })
        	
	});
	/*
    //Filtra programas (servicios)
    $(document).on("change","#ID_C_PROGRAMA", function () {

    id_mujeres_avanzando = $('#id_mujeres_avanzando').val();            

         $("select[name='ID_C_PROGRAMA'] option:selected").each(function () {

		      ID_C_PROGRAMA = $(this).attr("value");
          //alert(ID_C_DEPENDENCIA);

            var parametros = {		  
            'ID_C_PROGRAMA' : ID_C_PROGRAMA,
            'id_mujeres_avanzando' : id_mujeres_avanzando
            }
            
            frmAjax(parametros,'servicios','filtra_servicio');            

		    })
        	
	});
*/
    //Eliminamos elementos del "carrito" de Artículos
    $(document).on("click","#elimina_art", function () {

        ID_C_SERVICIO = $(this).attr("name");

        var parametros = {
          "ID_C_SERVICIO" : ID_C_SERVICIO,
          'accion': 'eliminar'
        };

        frmAjax(parametros,'servicios','servicios_mujer');

    });

    //Actualizamos listado de productos y servicios del beneficiario
    $(document).on("submit","#formServ", function (e){ 

        //alert('aquí');        

        //Obtenemos id del beneficiario
        id_mujeres_avanzando = $('#id_mujeres_avanzando').val();
        ID_C_SERVICIO = $('#ID_C_SERVICIO').val();
        observaciones = $('#observaciones').val();

        //Arreglo de fechas
        var arreglo_fechas = $('.fecha').map(function () {
          return this.value;
          }).get();        

        //Armamos lista de parámetros
        var parametros = {            
          'id_mujeres_avanzando' : id_mujeres_avanzando,
          'ID_C_SERVICIO' : ID_C_SERVICIO,
          'observaciones' : observaciones,
          'ajax' : 'ajax',          
          'arreglo_fechas' : JSON.stringify(arreglo_fechas)
        };

        //Ruta para guardar los servicios
        var ruta_guarda = '../../servicios/serv/';

        //Guardamos los servicios
        frmAjax(parametros,'page_list','save_mujer_serv','POST',ruta_guarda);
        
        //Actualizamos lista de productos y servicios
        frmAjax(parametros,'page_list','lista_serv');

        //Ocultamos carrito
        $('#tbl_servicios').html('');   

        //Reiniciamos opciones del select
        reiniciaOpc();

        e.preventDefault();

    });
  
  function reiniciaOpc(){
  
  //Seleccionamos primer opción
  $("#id_grado").val($("#id_grado option:first").val());
  //Vaciamos select y solo dejamos primer opción
  $('#ID_C_SERVICIO')[0].options.length = 1;
  $('#ID_C_DEPENDENCIA')[0].options.length = 1;

  }

  //Form Ajax Genérico
   function frmAjax(parametros,div,accion,tipo,ruta,selector){       

       //Valores predeterminados
       ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/servicios/';
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

  //Tabla de productos y servicios seleccionados
  $(document).on("change","#ID_C_SERVICIO", function () {

    $("select[name='ID_C_SERVICIO'] option:selected").each(function () {

        ID_C_SERVICIO = $(this).attr("value");
        id_mujeres_avanzando = $('#id_mujeres_avanzando').val();

        var parametros = {      
            'ID_C_SERVICIO' : ID_C_SERVICIO,
            'accion': 'agregar',
            'id_mujeres_avanzando': id_mujeres_avanzando
            }

        //alert(parametros.toSource());

        frmAjax(parametros,'tbl_servicios','servicios_mujer');

    })

  });  


//FILTRA PROGRAMA_GENERAL
  $(document).on("change","#cod_programa_g",function(){

//var CVE_EDO_RES = $("#CVE_EDO_RES").val();

  $("select[name='cod_programa_g'] option:selected").each(function () {

    cod_programa_g = $(this).attr("value");

    var parametros={
      'cod_programa_g' : cod_programa_g
      //'CVE_EDO_RES' : CVE_EDO_RES
    }

    //Localidad
    frmAjax(parametros,'pys_g','filtra_programa_g');

    })

});

});    