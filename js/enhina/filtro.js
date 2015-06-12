/* Vocales en unicode
\u00e1 = �
\u00e9 = �
\u00ed = �
\u00f3 = �
\u00fa = �
\u00c1 = �
\u00c9 = �
\u00cd = �
\u00d3 = �
\u00da = �
\u00f1 = �
\u00d1 = �  
*/

jQuery(document).ready(function ($) {
    
    
/*
Refrescamos select de caravana o comunidad
*/

$("#id_tipo_lugar").change(function(){

		$("select[name='id_tipo_lugar'] option:selected").each(function () {

           id_tipo_lugar = $(this).attr("value");
           //alert(id_tipo_lugar);
        

        var parametros = {
          'id_tipo_lugar' : id_tipo_lugar
        }

        frmAjax(parametros,'tipo_lugar','filtra_tipo_lugar');

   })

	});
    
  $(document).on("change","#id_caravana", function () {

    $("select[name='id_caravana'] option:selected").each(function () {

        id_caravana = $(this).attr("value");
           
        var parametros = {
          'id_caravana' : id_caravana
        }

        frmAjax(parametros,'no_visita','visita_reciente');

   })

  });
    
   //Form Ajax Gen�rico
   function frmAjax(parametros,div,accion,tipo,ruta,selector){

    //Valores predeterminados
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/enhina/';
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

    
});