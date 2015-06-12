jQuery(document).ready(function ($) {	        

/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

//Máscara 
$(".tel_casa").mask("(01 99) 99-99-99-99");      
$(".tel_office").mask("(01 99) 99-99-99-99");
$(".tel_movil").mask("(044) 99-99-99-99-99");

//$(".decimal").mask("999.99");


//Validamos fecha
$( ".fecha" ).datepicker({ 
    dayNames: [ "Domingo", "Lunes", "Martes", "Mi\u00e9rcoles", "Jueves", "Viernes", "S\u00e1bado"],
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],     
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    dateFormat: "yy-mm-dd",
    yearRange: "1900:2014",
    changeYear: true,
    changeMonth: true, 
    maxDate:new Date(),
    showMonthAfterYear: true
    });
    
    //función para fecha

function TodaysDate() {
 var currentTime = new Date()
 var year = currentTime.getFullYear()
 var month = currentTime.getMonth() + 1
 var day = currentTime.getDate()
 
 return year + "-" + month + "-" + day;

}

//Ponemos fecha en input
$("#btnToday1").click(function() {
  var today = new Date();
  $(fecha_pasaje_1).datepicker('setDate', TodaysDate()); 
});

//Ponemos fecha en input
$("#btnToday2").click(function() {
  var today = new Date();
  $(fecha_pasaje_2).datepicker('setDate', TodaysDate()); 
});     

//Ponemos fecha en input
$("#btnToday3").click(function() {
  var today = new Date();
  $(fecha_pasaje_3).datepicker('setDate', TodaysDate()); 
});     

//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

//Centros de Atención        
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.validator.format("Please enter exactly {0} characters."));

      $("#formCent").validate({

        rules: {  
            CVE_EST_MUN_LOC: {required: true,range: [1, 1000000000]},
            nombre:{required: true, minlength: 2, maxlength: 40},
            direccion: {required: true,minlength: 2, maxlength: 40},
            id_tipo_centro: {required: true,range: [1, 1000000]},
        },

        messages: {
            CVE_EST_MUN_LOC: 'Seleccione Localidad',
            nombre: 'Ingrese nombre valido',
            direccion: 'Ingrese direccion',
            id_tipo_centro : 'Ingrese el tipo de centro'
        }

    });
    
    //Datos beneficiario nutricion
    
    $("#form_nutricion").validate({

        rules: {  
            //CVE_EST_MUN_LOC: {required: true,range: [1, 1000000000]},
            nombres:{required: true, minlength: 2, maxlength: 40},
            paterno: {required: true,minlength: 2, maxlength: 40},
            materno: {required: true,minlength: 2, maxlength: 40},
            id_tipo_familia: {required: true,range: [1, 1000]},
            id_vulnerabilidad: {required: true,range: [1, 1000]},
            id_vulnerabilidad_familia: {required: true,range: [1, 1000]},
            talla_1: {required: true, range: [20,200], digits: true},
            peso_1: {required: true, range: [1,200],
                      number: true  },
            fecha_pesaje_1: 'required',           
            talla_2: {required: true, range: [20,200],   
                      digits: true  },
            peso_2: {required: true,  range: [1,200],
                      number: true  },
            fecha_pesaje_2: {required: true} ,           
            talla_3: {required: true, range: [20,200],   
                      digits: true  },
            peso_3: {required: true,  range: [1,200],
                      number: true  }, 
            fecha_pesaje_3:{required: true} ,                                           
            ingreso: {required: true,
                      digits: true}           
        },

        messages: {
            
            //CVE_EST_MUN_LOC: 'Seleccione Localidad',
            nombres: 'Ingrese nombre valido',
            paterno: 'Ingrese apellido paterno valido',
            materno: 'Ingrese apellido materno valido',
            id_tipo_familia : 'Ingrese el tipo de familia',
            id_vulnerabilidad : 'Ingrese el tipo de vulnerabilidad',
            id_vulnerabilidad_familia : 'Ingrese el tipo de vulnerabilidad',
            talla_1: 'ingrese talla correcta',
            peso_1: 'ingrese peso correcto',
            fecha_pesaje_1: 'ingrese fecha',
            talla_2: 'ingrese talla correcta',
            peso_2: 'ingrese peso correcto',
            fecha_pesaje_2: 'ingrese fecha',
            talla_3: 'ingrese talla correcta',
            peso_3: 'ingrese peso correcto',
            fecha_pesaje_3: 'ingrese fecha',
            ingreso: 'ingrese cantidad'
                      
        }

    });
    
     $("#formReporte").validate({

        rules: {              
            id_municipio_caratula: {required: true},
            ciclo_escolar:{required: true},
            axo_padron: {required: true,range: [2000,2040]},
            CVE_ENT_MUN_LOC: {required:true},
            id_centro_atencion: {required:true}
        },

        messages: {
            id_municipio_caratula: 'Seleccione Municipio',
            ciclo_escolar: 'Seleccione Ciclo Escolar',
            axo_padron: 'Seleccione A&ntilde;o',
            CVE_ENT_MUN_LOC : 'Seleccione Comunidad',
            id_centro_atencion : 'Seleccione Centro de Atenci&oacute;n'
        }

    });

});