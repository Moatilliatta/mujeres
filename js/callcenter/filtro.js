jQuery(document).ready(function ($) {	

//Ruta para usarse en envia.frmAjax
var ruta = '../../inc/callcenter/';

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

            envia.frmAjax(parametros,'car_operador','agregar_operador',ruta);
		})

	});

	 //Eliminamos elementos del "carrito" de Artículos
    $(document).on("click","#elimina_art", function () {

        id = $(this).attr("name");

        var parametros = {
          "id" : id,
          'accion': 'eliminar'
        };

        envia.frmAjax(parametros,'car_operador','agregar_operador',ruta);

    });

    //Eliminamos elementos del "carrito" de Artículos
    $(document).on("click","#borra_lista", function () {

        var parametros = {
          'accion': 'vaciar'
        };

        envia.frmAjax(parametros,'car_operador','agregar_operador',ruta);
        $('#photo').html('');
    }); 

     //Habilitamos boton de tomar foto
    $(document).on("click","#vista", function () {

    	//Inidicamos ruta del archivo y guardamos datos
		ruta = '../../inc/callcenter/guarda_lista_operador';
		envia.frmAjax(null,'car_operador','guarda_lista_operador',ruta);    
        
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
        envia.frmAjax(parametros,'seg_capacitacion','filtra_capacitacion',ruta);
    })

  });


});