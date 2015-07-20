jQuery(document).ready(function ($) {	

var ruta = '../../inc/servicios/';

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
            
            envia.frmAjax(parametros,'dependencias','filtra_dependencia',ruta);  

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
            envia.frmAjax(parametros,'programas','filtra_programa',ruta);
                      

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
            
            envia.frmAjax(parametros,'servicioss','filtra_servicio',ruta);

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
            
            envia.frmAjax(parametros,'servicios','filtra_servicio',ruta);

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

        envia.frmAjax(parametros,'servicios','servicios_mujer',ruta);

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
        envia.frmAjax(parametros,'page_list','save_mujer_serv',ruta_guarda);
        
        //Actualizamos lista de productos y servicios
        envia.frmAjax(parametros,'page_list','lista_serv',ruta);

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

        envia.frmAjax(parametros,'tbl_servicios','servicios_mujer',ruta);

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
    envia.frmAjax(parametros,'pys_g','filtra_programa_g',ruta);

    })

});

});    