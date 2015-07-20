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
    
var ruta = '../../inc/enhina/';

  //Refrescamos select de caravana o comunidad
    $("#id_tipo_lugar").change(function(){

    		$("select[name='id_tipo_lugar'] option:selected").each(function () {

            id_tipo_lugar = $(this).attr("value");
            //alert(id_tipo_lugar);
            
            var parametros = {
              'id_tipo_lugar' : id_tipo_lugar
            }

            envia.frmAjax(parametros,'tipo_lugar','filtra_tipo_lugar',ruta);
       })

    });
        

    $(document).on("change","#id_caravana", function () {

        $("select[name='id_caravana'] option:selected").each(function () {

            id_caravana = $(this).attr("value");
               
            var parametros = {
              'id_caravana' : id_caravana
            }

            envia.frmAjax(parametros,'no_visita','visita_reciente',ruta);

       })

    });
    
});