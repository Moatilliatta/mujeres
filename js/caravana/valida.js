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

//PONEMOS PREDETERMINADAMENTE JALISCO COMO ESTADO Y MEXICO COMO PAIS
var CVE_EDO_RES = $("#CVE_EDO_RES").val();
//alert(CVE_EDO_RES);

if(CVE_EDO_RES == 'undefined' || CVE_EDO_RES == 0){
 $("#CVE_EDO_RES").val('14');   
}  

var id_pais = $("#id_pais").val();

if(id_pais == 'undefined' || id_pais == 0){
  $("#id_pais").val('90');   
}

var id_cat_estado = $("#id_cat_estado").val();

if(id_cat_estado == 'undefined' || id_cat_estado == 0){
  $("#id_cat_estado").val('14');    
}

//Validamos fecha
$( ".fecha" ).datepicker({ 
    dayNames: [ "Domingo", "Lunes", "Martes", "Mi\u00e9rcoles", "Jueves", "Viernes", "S\u00e1bado"],
    dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
    monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],     
    monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    dateFormat: "yy-mm-dd",
    yearRange: "1900:2030",
    changeYear: true,
    changeMonth: true 
    //maxDate:new Date()
    //showMonthAfterYear: true
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
  $(fecha_instalacion).datepicker('setDate', TodaysDate());
});     

//Campo de fecha sólo de lectura
$(".fecha").attr('readOnly' , 'true' );

//Mujer
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.format("Please enter exactly {0} characters."));

$("#formCaravana").validate({

    rules: {  
            descripcion: {required: true, minlength: 4, maxlength: 100},
            fecha_instalacion: 'required',
            direccion: {required: true, minlength: 2, maxlength: 150},
            status: 'required'
                       
        },

    messages: {
            descripcion: 'Descripci\u00f3n',
            fecha_instalacion: 'Fecha de Instalaci\u00f3n',
            direccion:'Direcci\u00f3n',
            status: 'Estatus'
            
        }
    });

$("#form_nueva_vialidad").validate({

    rules: {  
            CVE_TIPO_VIAL_NUEVA: {required: true,range: [1, 1000000]},
            vialidad_nueva: 'required'            
        },
        messages: {
            CVE_TIPO_VIAL_NUEVA: 'Seleccione Tipo de Vialidad',
            vialidad_nueva: 'Ingrese Vialidad Nueva'              
        }

    });


    //Revisamos dinámicamente si es homónima la persona en cuestión
    function revisaHomonimo(){

        //Obtenemos variables
        var nombres = $("#nombres").val();
        var paterno = $("#paterno").val();
        var materno = $("#materno").val();
        var fecha_nacimiento = $("#fecha_nacimiento").val();
        var id_municipio_nacimiento = $("#id_municipio_nacimiento").val();
        var id_cat_estado = $("#id_cat_estado").val();
        var esHomonimo = $("#esHomonimo").val();

        if(typeof nombres !== 'undefined' &&
            typeof paterno !== 'undefined' &&
            typeof materno !== 'undefined' &&
            typeof fecha_nacimiento !== 'undefined' &&
            typeof id_municipio_nacimiento !== 'undefined' &&
            typeof id_cat_estado !== 'undefined'){

            //Llenamos arreglo de parámetros
            var parametros = {
            'nombres': nombres,
            'paterno': paterno,
            'materno': materno,
            'fecha_nacimiento': fecha_nacimiento,
            'id_municipio_nacimiento': id_municipio_nacimiento,
            'id_cat_estado': id_cat_estado,
            'esHomonimo' : esHomonimo
            };                

            $.ajax({
                   type: "POST",
                   url: "../../inc/mujer/guardar_homonimo.php",
                   data: parametros,
                   /* beforeSend: function(){
                       $("#homonimo").html('<img src="../../css/img/loader_sug.gif"/>Verificando');
                    },*/
                   success:                     
                   function( respuesta ){                                                          
                        //Si la respuesta NO es vacía, cancelamos envío
                        if($.isEmptyObject(respuesta) === false){
                            //cancelaEnvio = true;
                            $("#esHomonimo").attr( "required", "required" );   
                        }else{
                            $("#esHomonimo").removeAttr("required");
                        }                  
                       $("#homonimo").html(respuesta);
                    },
                   error: function(){
                       $("#homonimo").html('');
                   }                

            });

        }    

    }

    //Verificamos si hay homónimos
   $(document).on("click","#guardar", function (event) {     
        //Detenemos comportamiento por default
        event.preventDefault();
        event.stopPropagation();

        //Validamos datos

        revisaHomonimo();
        //$('#guardar').unbind('click');
        //$("#guardar").click();
        $("#formBen").submit();

   });

   //Al dar click en la fecha de nacimiento, validamos que no sea homónimo
    $(document).on("change","#id_municipio_nacimiento", function () { 

        //Validamos datos
        revisaHomonimo();

     });

    //El cambio de fecha aproximada o no, altera el curp
/*
    $(document).on("change","#fecha_aproxim", function () { 
        $("select[name='fecha_aproxim'] option:selected").each(function () {
            es_aproximado = $(this).attr("value");            
            if(es_aproximado == 'SI'){
                $('#curp').val('');
                $('#curp').attr("disabled",'disabled');
            }else{
                $('#curp').removeAttr("disabled");
                $('#curp').click();
            }
        });        
     });
     */
     
     //El cambio de fecha aproximada o no, altera el curp        
     $(document).on("click","#fecha_aproxim", function () { 
  
          if ($(this).is(":checked") === true){
                $('#curp').val('');
                $('#curp').attr("disabled",'disabled');
            }else{
                $('#curp').removeAttr("disabled");
                $('#curp').click();                  
            }

     });

    //comprobamos curp
   $(document).on("focusout","#curp", function (){ 

    //Obtenemos variables
        var nombres = $("#nombres").val();
        var paterno = $("#paterno").val();
        var materno = $("#materno").val();
        var fecha_nacimiento = $("#fecha_nacimiento").val();
        var genero = $("#genero").val();
        var id_cat_estado = $("#id_cat_estado").val();        
        var curp = $("#curp").val();        

        /*
        alert(nombres+'-'+
                paterno+'-'+
                materno+'-'+
                fecha_nacimiento+'-'+
                genero+'-'+
                id_cat_estado+'-'+
                curp);
        */
        if(nombres != '' && paterno != '' && materno != '' && fecha_nacimiento != '' &&

           genero != '' && id_cat_estado != '' && curp != ''){

            //Llenamos arreglo de parámetros

            var parametros = {
            'nombres': nombres,
            'paterno': paterno,
            'materno': materno,
            'fecha_nacimiento': fecha_nacimiento,
            'genero': genero,
            'id_cat_estado': id_cat_estado,
            'curp' : curp
            };                

            $.ajax({
                   type: "POST",
                   url: "../../inc/mujer/obten_curp.php",
                   data: parametros,
                   /* beforeSend: function(){
                       $("#homonimo").html('<img src="../../css/img/loader_sug.gif"/>Verificando');
                    },*/
                   success:                     
                   function( respuesta ){

                        //Si la respuesta es vacía, reiniciamos CURP pues no coincide
                        if($.isEmptyObject(respuesta) === true){

                            //Reiniciamos el curp por ser erróneo
                            $("#curp").click();   
                        } 

                    },

                   error: function(){
                       $("#curp").val('');
                   }                

            });   

        }

   }); 

    /**
    * Al cambiar el valor del nombre, fecha de nacimiento o sexo, verificamos CURP
    * sólo si esta está completa
    **/
   /* $(document).on("change",".arma_curp, input[name=genero]:radio", function () {

        //Obtenemos curp
        var curp = $("#curp").val(); 

        //Si tiene tamaño completo, verificamos dicha curp
        if(curp.length == 18){
            $('#curp').click();
        }        

     });*/

    //Llenamos curp
   $(document).on("click","#curp", function (){ 

        //Obtenemos variables
        var nombres = $("#nombres").val();
        var paterno = $("#paterno").val();
        var materno = $("#materno").val();
        var fecha_nacimiento = $("#fecha_nacimiento").val();        
        //var genero = $("#genero").val();
        var genero = $('input[name=genero]:radio:checked').val();
        var id_cat_estado = $("#id_cat_estado").val();        
        var curp = $("#curp").val();    
        
        /*alert(nombres+'-'+
                paterno+'-'+
                materno+'-'+
                fecha_nacimiento+'-'+
                genero+'-'+
                id_cat_estado+'-'+
                curp);*/
        
    

        if(nombres != '' && paterno != '' && materno != '' && fecha_nacimiento != '' &&

           genero != '' && id_cat_estado != ''){

            //Llenamos arreglo de parámetros
            var parametros = {
            'nombres': nombres,
            'paterno': paterno,
            'materno': materno,
            'fecha_nacimiento': fecha_nacimiento,
            'genero': genero,
            'id_cat_estado': id_cat_estado,
            'curp' : curp
            };                

            $.ajax({
                   type: "POST",
                   url: "../../inc/mujer/obten_curp.php",
                   data: parametros,
                   /* beforeSend: function(){
                       $("#homonimo").html('<img src="../../css/img/loader_sug.gif"/>Verificando');
                    },*/
                   success:                     
                   function( respuesta ){
                        //alert(respuesta);

                        if(curp.length == 18 && respuesta == 16){
                            digitos = curp.substring(16,18);
                            //alert(digitos);
                            respuesta += digitos;
                        }

                       $("#curp").val(respuesta);
                    },

                   error: function(){
                       $("#curp").val('');
                   }                

            });

        }

   });   

});