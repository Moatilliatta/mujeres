jQuery(document).ready(function ($) {           
/* Vocales en unicode
\u00e1 = �
\u00e9 = �
\u00ed = �
\u00f3 = �
\u00fa = �
*/

//Selecciona todos los checkbox del area
    $("#todos_componente").click(function(){
         $('.componente').prop('checked', this.checked);
    });

});