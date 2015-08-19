var envia = (function(){

  //Private
  var confirmar = function () {

    mensaje = $(this).attr("title");

    if(mensaje == null){
      mensaje = '\u00BFEst\u00e1s Seguro?'; 
    }
          
    return  confirm(mensaje);
          
  };

  //Public
  return{

    normalize : function(str) {
  
        var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç ", 
            to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc_",
            mapping = {};
       
        for(var i = 0, j = from.length; i < j; i++ )
            mapping[ from.charAt( i ) ] = to.charAt( i );
        
        var ret = [];
        
        for( var i = 0, j = str.length; i < j; i++ ) {
          
          var c = str.charAt( i );
          
          if( mapping.hasOwnProperty( str.charAt( i ) ) ){
            ret.push( mapping[ c ] );
          }else{
            ret.push( c );
          }
                    
        }      
        
        return ret.join( '' );
 
    },

    ajax : function(action,update){

      $.ajax({ 
              url: action,
              success: function(data){
                      selector = '#'+update;
                      $(selector).html(data);
                      }
              }); 
    },

    //Form Ajax Genérico
    frmAjax : function (parametros,update,accion,ruta,tipo,selector,destino,redirect,multipart)
    {

      //Valores predeterminados
      ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/mujer/';    
      tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
      
      selector = typeof selector !== 'undefined' ? selector : '#';
      destino = typeof destino !== 'undefined' ? destino : ruta+accion+".php";
      redirect = typeof redirect !== 'undefined' ? redirect : false;
      multipart = typeof multipart !== 'undefined' ? multipart : false;    
      
      //Armamos objeto
      var obj = {
        type: tipo,
        url: destino,
        data: parametros,
        cache: false,      
        beforeSend: function(){
          $(selector+update).html('<img src="/escolar/img/sping.gif"/>Buscando');
          //alert(parametros.tipo+' before');
        },
        success: function( respuesta ){
          $(selector+update).html(respuesta);    
          //alert(parametros.tipo+' success');                
        },
        error: function(){
          $(selector+update).html(' ');
        },      
        done: function(){
          if(redirect === true){
            window.location.href = destino;
          }
        }
      }

      //Al ser multipart, agregamos 2 opciones
      if(multipart === true){
        obj.contentType = false;
        obj.processData = false;      
      }    

      //Ejecutamos función Ajax
      $.ajax(obj);
    
   },

   //Form Ajax Genérico con callback
   frmAjaxCallBack : function(parametros,div,accion,ruta,tipo,selector){
    
      //Valores predeterminados    
      tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
      ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/estadisticas/';
      selector = typeof selector !== 'undefined' ? selector : '#';

      //Regresamos instancia ajax para que pueda ser manipulada
      return $.ajax({
        type: tipo,
        url: ruta+accion+".php",
        data: parametros,
        beforeSend: function(){},
        success: function(respuesta){},
        error: function(){}
      });
    
   },

   onReady : function(){
            //Código de confirmación
            $('.confirmation').click(confirmar);
   },

  };

})();

//Obtenemos funciones
jQuery(document).ready(envia.onReady);