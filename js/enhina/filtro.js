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
    
   //Form Ajax Genérico
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