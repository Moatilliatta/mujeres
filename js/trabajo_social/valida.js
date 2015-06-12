jQuery(document).ready(function ($){ 
/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/
//PONEMOS PREDETERMINADAMENTE JALISCO COMO ESTADO Y MEXICO COMO PAIS
var CVE_EDO_RES = $("#CVE_EDO_RES").val();
//alert(CVE_EDO_RES);

if(CVE_EDO_RES == 'undefined' || CVE_EDO_RES == 0){
 $("#CVE_EDO_RES").val('14');   
} 
//PONEMOS PREDETERMINADAMENTE ENFERMEDAD (NINGUNA)
var id_enfermedad = $("#id_enfermedad").val();

if(id_enfermedad == 'undefined' || id_enfermedad == 0){
  $("#id_enfermedad").val('1');    
}
//PONEMOS PREDETERMINADAMENTE TIPO DE DISCAPACIDAD (N.A.)
var id_tipo_discapacidad = $("#id_tipo_discapacidad").val();

if(id_tipo_discapacidad == 'undefined' || id_tipo_discapacidad == 0){
  $("#id_tipo_discapacidad").val('8');    
}

//PONEMOS PREDETERMINADAMENTE TIPO DE DISCAPACIDAD (N.A.)
var condicion = $("#condicion").val();

if(condicion == 'undefined' || condicion == 0){
  $("#condicion").val('ABIERTO');    
}
//////////////////////////////////////////////////////////
var condicion = $("#condicion").val();

if(condicion != 'CERRADO'){
        
        $("#id_motivo_cierre").attr('disabled','disabled');  
        $("#observacion_cierre").attr('disabled','disabled');
       
       }else{
        $(".condicion").attr('disabled','disabled');
        
       }
       
 
//Validamos fecha
$(".fecha" ).datepicker({ 
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
$("#btnToday").click(function() {
  var today = new Date();
  $(fecha_recibido).datepicker('setDate', TodaysDate()); 
}); 

//Ponemos fecha en input
$("#btnToday1").click(function() {
  var today = new Date();
  $(fecha_registro).datepicker('setDate', TodaysDate()); 
}); 
//Ponemos fecha en input
$("#btnToday_a").click(function() {
  var today = new Date();
  $(fecha_autorizacion).datepicker('setDate', TodaysDate()); 
});  
//Ponemos fecha en input
$("#btnToday_e").click(function() {
  var today = new Date();
  $(fecha_entrega).datepicker('setDate', TodaysDate()); 
});  
//Ponemos fecha en input
$("#btnToday_v").click(function() {
  var today = new Date();
  $(fecha_verificacion).datepicker('setDate', TodaysDate()); 
});
//Ponemos fecha en input
$("#btnToday_p").click(function() {
  var today = new Date();
  $(fecha_pago).datepicker('setDate', TodaysDate()); 
});      
    
//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

//Mes y Año actuales
if($('#mes').val() === '' && $('#axo_padron').val() === ''){

	var dt = new Date();
	var m = dt.getMonth() + 1; //Arreglo de meses inicia en 0;
	var y = dt.getFullYear();

	$('#mes').val(m);
	$('#axo_padron').val(y);

}
//Validación datos de trabajo social
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.validator.format("Please enter exactly {0} characters."));

$("#formtb").validate({

    rules: { 
            fecha_recibido: 'required',
            fecha_registro: 'required',
            nombres: {required: true, minlength: 2, maxlength: 40},
            genero: 'required',
            id_tcat_programa: {required: true,range: [1, 10000]},
            mes: {required: true,range: [1, 12]},
            axo_padron: {required: true,range: [2000, 3000]},
            numero_documento: 'required',
            id_instancia: {required: true,range: [1, 10000]},
            id_problematica: {required: true,range: [1, 10000]},
            id_enfermedad: {required: true,range: [1, 10000]},
            id_motivo_cierre: {required: true,range: [1, 10000]},
            id_tipo_apoyo: {required: true,range: [1, 10000]},
            id_tipo_discapacidad: {required: true,range: [1, 10000]},
            condicion: 'required',
            id_producto_servicio: {required: true,range: [1, 10000]},
            id_tipo_apoyo_solicitado: {required: true,range: [1, 10000]} 
            
           // ciudadano_mex : 'required'            
        },

    messages: {
            fecha_recibido: 'Ingrese Fecha de Recibido',
            fecha_registro: 'Ingrese Fecha de Registro',
            nombres: 'Ingrese Nombre(s) Propios',
            genero: 'Seleccione Genero',
            id_tcat_programa: 'Selecciona el programa',
            mes: 'Seleccione el mes',
            axo_padron: 'Seleccione el a&ntildeo',
            numero_documento: 'Ingresa N\u00famero De Documento',
            id_instancia: 'Seleccione Instancia',
            id_problematica: 'Seleccione Problematica',
            id_enfermedad: 'Seleccione Enfermedad',
            id_motivo_cierre: 'Seleccione el motivo del cierre',
            id_tipo_apoyo: 'Seleccione el Tipo De Apoyo',
            id_tipo_discapacidad: 'Seleccione el Tipo de Discapacidad',
            condicion: 'Seleccione la condici\u00f3n',
            id_producto_servicio: 'Seleccione el Servicio',
            id_tipo_apoyo_solicitado: 'Seleccione el tipo de apoyo solicitado'
            
            
        }
    }); 
    $("#formApo").validate({

        rules: {              
            fecha_autorizacion: 'required',
            cantidad: {required: true, range: [1,999999],
                      number: true  },
            id_tipo_apoyo: {required: true,range: [1,10000]},
            vale: 'required',
            proveedor: 'required',
            costo_total: {required: true, range: [1,999999],
                      number: true  },
            aportacion_dif: {required: true, range: [1,999999],
                      number: true  },
            dif_municipal: {required: false, range: [1,999999],
                      number: true  },
            familia: {required: false, range: [1,999999],
                      number: true  }, 
            otros: {required: false, range: [1,999999],
                      number: true  },                              
            fecha_entrega: 'required',
            fecha_verificacion: 'required',
            contra_recibo: 'required',
            numero_factura: 'required',
            partida_presupuestal: 'required',
            numero_transferencia: 'required',
            fecha_pago: 'required',
            id_producto_servicio:{required: true,range: [1,10000]},
            id_tipo_servicio:{required: true,range: [1,10000]},
            id_servicio_especifico:{required: true,range: [1,10000]}
        },

        messages: {
            fecha_autorizacion: 'Ingrese Fecha de Autorizaci\u00f3n',
            cantidad: 'Ingrese Cantidad ',
            id_tipo_apoyo: 'Seleccione el tipo de apoyo',
            vale : 'Ingrese el n\u00famero de vale',
            proveedor: 'Ingre Proveedor',
            costo_total: 'Ingrese Costo Total',
            aportacion_dif: 'Ingrese Aportacion Dif',
            dif_municipal: 'Ingrese Aportacion Correcta',
            familia: 'Ingrese Aportacion Correcta',
            otros: 'Ingrese Aportacion Correcta',
            fecha_entrega: 'Ingrese Fecha emtrega',
            fecha_verificacion: 'Ingrese Fecha Verificacion',
            contra_recibo: 'Ingrese  n\u00famero de Contra Recibo',
            numero_factura: 'ingrese n\u00famero de Factura',
            partida_presupuestal:'Ingrese Partida Presupuestal',
            numero_transferencia: 'Ingrese  n\u00famero cheque',
            fecha_pago: 'Ingrese fecha pago',
            id_producto_servicio: 'Seleccione el programa',
            id_tipo_servicio: 'Seleccione el tipo de servicio',
            id_servicio_especifico: 'Seleccione el servicio especifico'
            
            
        }

    });
  
});
