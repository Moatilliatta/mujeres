jQuery(document).ready(function ($) {	        

    

    //Código General Jquery

// Conforme se selecciona el grupo actualizamos los datos ligados a él

    $("#id_modulo").change(function(){

		var id_modulo = "";

		

		$("select[name='id_modulo'] option:selected").each(function () {

		    id_modulo = $(this).attr("value");

            

            //alert(id_mod);

            

            var parametros = {		  

            'id_modulo' : id_modulo

            }

            

            

            $.ajax({

            type: "POST",

            url: "../../inc/usuario/filtra_submodulo.php",

            data: parametros,

			beforeSend: function(){

                $('#submodulo').html('<img src="../css/img/loader_sug.gif"/>Buscando');

            },

            success: function( respuesta ){

				$('#submodulo').html(respuesta);

			},

            error: function(){

                $('#submodulo').html(' ');

            }

		});

		})

		

	});

    

    

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //a qui filtro los grupos de un usuario en submodulo de bloqueo

        $("#id_usuario").change(function(){

		var id_usuario = "";

		

		$("select[name='id_usuario'] option:selected").each(function () {

		    id_usuario = $(this).attr("value");

            

            //alert(id_mod);

            

            var parametros = {		  

            'id_usuario' : id_usuario

            }

            

            

            $.ajax({

            type: "POST",

            url: "../../inc/usuario/filtra_grupo.php",

            data: parametros,

			beforeSend: function(){

                $('#grupo').html('<img src="../css/img/loader_sug.gif"/>Buscando');

            },

            success: function( respuesta ){

				$('#grupo').html(respuesta);

			},

            error: function(){

                $('#grupo').html(' ');

            }

		});

		})

		

	});

    

    

      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //Aqui filtro las acciones de un submodulo
    $(document).on("change","#id_submodulo", function () {

		var id_submodulo = $("#id_submodulo").val();

        var id_grupo = $("#id_grupo").val();


		$("select[name='id_submodulo'] option:selected").each(function () {

		    id_submodulo = $(this).attr("value");

            

            //alert(id_submodulo);

            

            var parametros = {		  

            'id_submodulo' : id_submodulo,

            'id_grupo':id_grupo

            }

            

            

            $.ajax({

            type: "POST",

            url: "../../inc/usuario/filtra_accion.php",

            data: parametros,

			beforeSend: function(){

                $('#accion').html('<img src="../../css/img/loader_sug.gif"/>Buscando');

            },

            success: function( respuesta ){

				$('#accion').html(respuesta);

			},

            error: function(){

                $('#accion').html(' ');

            }

		});

		})

		

	});
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //a qui filtro las acciones_grupo de un grupo

        $("#id_grupo").change(function(){
         var id_menu = $("#id_menu").val();
   

		$("select[name='id_grupo'] option:selected").each(function () {

		   id_grupo = $(this).attr("value");

            

            //alert(id_submodulo);

            

            var parametros = {		  

             'id_grupo':id_grupo,
             'id_menu':id_menu

            }

            

            

            $.ajax({

            type: "POST",

            url: "../../inc/usuario/filtra_accion_grupo.php",

            data: parametros,

			beforeSend: function(){

                $('#accion_grupo').html('<img src="../../css/img/loader_sug.gif"/>Buscando');

            },

            success: function( respuesta ){

				$('#accion_grupo').html(respuesta);

			},

            error: function(){

                $('#accion_grupo').html(' ');

            }

		});

		})

		

	});


   $("#id_cat_municipio").change(function(){
        
        var id_cat_estado = $("#id_cat_estado").val();
        
		$("select[name='id_cat_municipio'] option:selected").each(function () {

		    id_cat_municipio = $(this).attr("value");
          
            var parametros = {		  
            'id_cat_municipio' : id_cat_municipio,
            'id_cat_estado' : id_cat_estado
            }

            $.ajax({
            type: "POST",
            url: "../../inc/usuario/filtra_localidad.php",
            data: parametros,
			beforeSend: function(){
                $('#accion').html('<img src="../../css/img/loader_sug.gif"/>Buscando');
            },
            success: function( respuesta ){
				$('#accion').html(respuesta);
			},
            error: function(){
                $('#accion').html(' ');
            }
		});
		})
	});
    
    
    $("#id_cat_estado").change(function(){

		$("select[name='id_cat_estado'] option:selected").each(function () {

		    id_cat_estado = $(this).attr("value");
          
            var parametros = {		  
            'id_cat_estado' : id_cat_estado
            }

            $.ajax({
            type: "POST",
            url: "../../inc/usuario/filtra_municipio.php",
            data: parametros,
			beforeSend: function(){
                $('#accion').html('<img src="../../css/img/loader_sug.gif"/>Buscando');
            },
            success: function( respuesta ){
				$('#accion').html(respuesta);
			},
            error: function(){
                $('#accion').html(' ');
            }
		});
		})
	});

    //Selecciona todos los checkbox de municipio
    $("#todos_mun").click(function(){
         $('.municipio').prop('checked', this.checked);
    });

    //Al seleccionar un municipio, deselecciona el checkbox de 'todos los municipios'
    $('.municipio').click(function(){
         $('#todos_mun').attr('checked', false);
    });

    //Selecciona todos los checkbox del area
    $("#todos_area").click(function(){
         $('.area').prop('checked', this.checked);
    });

    //Al seleccionar un municipio, deselecciona el checkbox de 'todos las areas'
    $('.area').click(function(){
         $('#todos_area').attr('checked', false);
    });

    //Obtenemos variable para ver si estamos editando grupo
    var id_edicion_grupo = $("#id_edicion_grupo").val(); 

    //Como no estamos editando, ponemos el primer elemento seleccionado (año y ciclo escolar actual)
    if(id_edicion_grupo == ""){        
        $("input[name='ciclo_escolar[]']:first").attr('checked', 'checked');
        $("input[name='axo_padron[]']:first").attr('checked', 'checked');
    }

});

