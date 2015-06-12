jQuery(document).ready(function ($) {           
/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

//Selecciona todos los checkbox del area
    $("#todos_componente").click(function(){
         $('.componente').prop('checked', this.checked);
    });

});