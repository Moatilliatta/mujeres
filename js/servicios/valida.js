jQuery(document).ready(function ($) {	        

/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

/*
//Máscara 
$(".tel_casa").mask("(01 99) 99-99-99-99");      
$(".tel_office").mask("(01 99) 99-99-99-99");
$(".tel_movil").mask("(044) 99-99-99-99-99");
*/

//Beneficiario_pys
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.format("Please enter exactly {0} characters."));

      $("#formBen_pys").validate({
        rules: {  
            id_beneficiario: {required: true,range: [1, 10000]},
            cod_prog: {required: true,range: [1, 10000]},
            cod_rpys: {required: true,range: [1, 10000]},
            fecha_asignado : 'true'                    
        },

        messages: {

            id_beneficiario: 'Seleccione el Beneficiario',
            cod_prog: 'Seleccione Programa',
            cod_ropys: 'Seleccione Servicios',
            fecha_asignado : 'Ingrese Fecha de Asignac\u00f3in'                    
        }

    });

    $('#mod_pys').click(function() {

        window.location.href = '../../servicios/serv/lista_mujer_serv.php';
        return false;
    });  

    $('#mod_beneficiario').click(function() {

        window.location.href = '../../mujer/registro/lista_mujer.php';
        return false;
    });  

});