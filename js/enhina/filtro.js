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