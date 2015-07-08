jQuery(document).ready(function ($) {           
/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

//Máscara 
/*$(".tel_casa").mask("01-99-99-99-99-99");
$(".tel_office").mask("(01-99-99-99-99-99");
$(".tel_movil").mask("(044) 99-99-99-99-99");*/

$("#formGrupoCallcenter").validate({

    rules: {  
            nombre: {required: true, minlength: 3},
            id_caravana: {required: true,range: [1, 10000]},
            id_callcenter_filtro: {required: true,range: [1, 10000]}
                       
        },

    messages: {
            descripcion: 'Nombre Incorrecto',
            fecha_instalacion: 'Seleccione una Caravana',
            direccion:'Seleccione un Filtro'
            
        }
    });


});