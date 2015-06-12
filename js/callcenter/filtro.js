jQuery(document).ready(function ($) {	

	//Form Ajax Genérico
   	function frmAjax(parametros,div,accion,tipo,ruta,selector){       

       //Valores predeterminados
       ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/callcenter/';
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


	//Listado de operadores
	$(document).on("change","#id_usuario", function () {

		$("select[name='id_usuario'] option:selected").each(function () {
		    
		    id = $(this).attr("value");
            id_callcenter_grupo = $("#id_callcenter_grupo").attr("value");            

            var parametros = {		  
            'id' : id,
            'id_callcenter_grupo' : id_callcenter_grupo,
            'accion': 'agregar'
            }

            //console.log(parametros);

            frmAjax(parametros,'car_operador','agregar_operador');
		})

	});

	 //Eliminamos elementos del "carrito" de Artículos
    $(document).on("click","#elimina_art", function () {

        id = $(this).attr("name");

        var parametros = {
          "id" : id,
          'accion': 'eliminar'
        };

        frmAjax(parametros,'car_operador','agregar_operador');

    });

    //Eliminamos elementos del "carrito" de Artículos
    $(document).on("click","#borra_lista", function () {

        var parametros = {
          'accion': 'vaciar'
        };

        frmAjax(parametros,'car_operador','agregar_operador');
        $('#photo').html('');
    }); 

     //Habilitamos boton de tomar foto
    $(document).on("click","#vista", function () {

    	//Inidicamos ruta del archivo y guardamos datos
		ruta = '../../inc/callcenter/guarda_lista_operador';
		frmAjax(null,'car_operador','guarda_lista_operador');    
        
    }); 
   

    //Listado de operadores
  $(document).on("change","#id_callcenter_filtro", function () {

    $("select[name='id_callcenter_filtro'] option:selected").each(function () {
        
        //Obtenemos el valor
        id = $(this).attr("value");
        var parametros = { 
            'id_callcenter_filtro' : id
        }

        //console.log(parametros);
        
        var tag = (id == 3)? "block" : "none";

        $("#titulo_seg_cap").css("display",tag);

        //enviamos información
        frmAjax(parametros,'seg_capacitacion','filtra_capacitacion');
    })

  });


});