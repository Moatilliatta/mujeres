jQuery(document).ready(function ($) {	        
    
    //Código General Jquery
    //Login
    $("#formLogin").validate({
        rules: {  
            usuario:{required: true},
            clave: {required: true},
            id_caravana: {required: true}
				
        },
        messages: {                       
            usuario: "Escriba usuario",
            clave: "Escriba password",
            id_caravana : 'Seleccione Caravana'
                 
        }
    });
    
    //Código de confirmación
    $('.confirmation').click(function () {
        
        mensaje = $(this).attr("title");
        
        if(mensaje == null){
            mensaje = '\u00BFEst\u00e1s Seguro?'; 
        }
        
        return  confirm(mensaje);
        
    });   

/*
    //Verificamos Caravanas del usuario
    $(document).on("blur","#usuario", function () {    
        
        var nombre_usuario = $(this).val();
        
        var ruta = '../inc/servicios/';

        var parametros = {
          'nombre_usuario' : nombre_usuario
        }

        //Localidad
        frmAjax(parametros,'caravana','filtra_caravana_usuario','POST',ruta);

    });   
*/

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
        $(selector+div).html('<img src="../css/img/loader_sug.gif"/>Buscando');
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
          
});