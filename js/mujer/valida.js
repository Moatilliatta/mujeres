jQuery(document).ready(function ($) {           
/* Vocales en unicode
\u00e1 = á
\u00e9 = é
\u00ed = í
\u00f3 = ó
\u00fa = ú
*/

//Máscara 
//$(".tel_casa").mask("01-99-99-99-99-99");      
//$(".tel_office").mask("(01-99-99-99-99-99");
//$(".tel_movil").mask("(044) 99-99-99-99-99");

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

//borramos fecha al activar/desactivar una capacitacion
  $(document).on("click",".val_check_1", function () { 
  
  //Obtenemos ID previo al botón
  var fecha_capacitacion = $(this).closest('tr').find("input:text").attr('id');
   //alert(fecha_capacitacion);
    if($(this).is(':checked')) {  
        //alert("Está activado");   
        $("#"+fecha_capacitacion).attr('disabled',false);
      
      }else{
        //alert("No está activado");    
        $("#"+fecha_capacitacion).attr('disabled',true);
      
      }

  });   
   

//Mujer
jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, jQuery.format("Please enter exactly {0} characters."));


var folio_foto = $("#folio").val();
//si tenemos folio mostramos form_foto
verificafolio(folio_foto);

$(document).on("change","#folio", function () { 
    
  verificafolio($(this).attr('value'));  

     });
 function verificafolio(folio){
 //alert(folio); 
  if(folio !='' && typeof folio !== 'undefined'){
     $('#foto').css('display','block') 
  }  
    
 }    





$("#formBen").validate({

    rules: {  
            nombres: {required: true, maxlength: 40},
            paterno: {required: true, maxlength: 40},
            materno: {required: false, maxlength: 40},
            escolaridad:{required: true, minlength: 2},
            ocupacion:{required: true, minlength: 2},
            fecha_nacimiento: 'required',
            //fecha_aproxim : 'required',
            genero: 'required',
            //CVE_TIPO_VIAL: {required: true,range: [1, 1000000]},
            //CVE_VIA: {required: true,range: [1, 1000000]},
            //indigena: 'required',
            //CVE_EDO_RES: 'required',
            id_cat_municipio: {required: true,range: [1, 10000]},
            id_cat_localidad: {required: true,range: [1, 10000]},
            //id_cp_sepomex: {required: true,range: [1, 10000]},
            //CVE_ASEN: {required: true,range: [1, 10000]},
            //id_escolaridad: {required: true,range: [1, 10000]},
            //id_ocupacion: {required: true,range: [1, 10000]},
            id_grado: {required: true,range: [1, 10000]},
            folio: {required: true, minlength: 2},
            calle: {required: true, minlength: 2},
            colonia: {required: true, minlength: 2},
            num_ext: {required: true},
            nivel: {required: true,range: [1, 10000]},
            calidad_dieta: {required: true,range: [1, 10000]},
            diversidad: {required: true,range: [1, 10000]},
            variedad: {required: true,range: [1, 10000]},
            elcsa: {required: true,range: [1, 10000]},
           // id_estado_civil: {required: true,range: [1, 10000]},
            //comunidad_indigena: 'required',
            //dialecto: 'required',
            //CVE_TIPO_VIAL_CALLE1:{
              //  required: {
                //    depends: function(element) {
                       // return $('#entre_calle1').val().length > 0;
                  //  }
                //}
            //},
            /*   
            CVE_TIPO_VIAL_CALLE2:{
                required: {
                    depends: function(element) {
                        return $('#entre_calle2').val().length > 0;
                    }
                }
            },
            CVE_TIPO_VIAL_CALLEP:{
                required: {
                    depends: function(element) {
                        return $('#calle_posterior').val().length > 0;
                    }
                }
            },   
            entre_calle1:{
                required: {
                    depends: function(element) {
                        return $('#CVE_TIPO_VIAL_CALLE1').val().length > 0;
                    }
                }
            },
            entre_calle2:{
                required: {
                    depends: function(element) {
                        return $('#CVE_TIPO_VIAL_CALLE2').val().length > 0;
                    }
                }
            },   
            calle_posterior:{
                required: {
                    depends: function(element) {
                        return $('#CVE_TIPO_VIAL_CALLEP').val().length > 0;
                    }
                }
            },     
            num_ext: {required: true, maxlength: 5},
            num_int: {required: false, maxlength: 8},
           
            */
            CODIGO: 'required',
            ife: {exactlength: 13, digits:true},       
            id_pais : 'required',
            grado : 'required',
            cartilla : 'required'
           // ciudadano_mex : 'required'            
        },

    messages: {
            nombres: 'Ingrese Nombre(s) Propios',
            paterno: 'Ingrese Apellido Paterno',
            materno: 'Ingrese Apellido Materno',
            ocupacion: 'Ingrese Ocupacion',
            escolaridad: 'Ingrese Escolaridad',
            id_grado: 'Seleccione el Grado',
            folio: 'Ingrese Folio',
            calle: 'Ingrese La Calle',
            colonia: 'Ingrese La Colonia',
            num_ext: 'Ingrese n\u00famero de domicilio v\u00e1lido', 
            fecha_nacimiento: 'Ingrese Fecha de Nacimiento',
            nivel: 'Selecciona Nivel Socioeconomico',
            calidad_dieta: 'Selccion Calidad De Dieta',
            diversidad: 'Selecciona Diversidad',
            variedad: 'Seleccione Variedad',
            elcsa: 'Seleccione Elcsa',
            
            
            //fecha_aproxim : 'Seleccione si la fecha es aproximada',
            genero: 'Seleccione Genero',
            //CVE_TIPO_VIAL: 'Seleccione Tipo de Vialidad',
           // CVE_VIA: 'Seleccione Vialidad',
           // CVE_TIPO_VIAL_CALLE1: 'Seleccione Tipo Vialidad',
           // CVE_TIPO_VIAL_CALLE2: 'Seleccione Tipo Vialidad',
          //  CVE_TIPO_VIAL_CALLEP: 'Seleccione Tipo Vialidad Posterior',
           // entre_calle1: 'Seleccione Vialidad',
          //  entre_calle2: 'Seleccione Vialidad',
          //  entre_posterior: 'Seleccione Vialidad',
            //indigena: 'Seleccione si es ind\u00edgena o no',
           //CVE_EDO_RES: 'Seleccione Estado de Residencia',
           id_cat_municipio: 'Seleccione Municipio',
           id_cat_localidad: 'Seleccione Localidad',
          //  id_cp_sepomex: 'Seleccione Asentamiento',
          //  CVE_ASEN: 'Seleccione Asentamiento',
            id_escolaridad: 'Seleccione Escolaridad',
            id_ocupacion: 'Seleccione Ocupaci\u00f3n',
           // id_estado_civil: 'Seleccione Estado Civil',                        
            curp: 'Verifique que su CURP est\u00e9 correcta',
            pasaporte : 'Ingrese Pasaporte',
            comunidad_indigena : 'Ingrese Comunidad Ind\u00edgena',
            dialecto : 'Ingrese Dialecto',                
            num_ext: 'Ingrese n\u00famero de domicilio v\u00e1lido',            
            num_int : 'Ingrese n\u00famero de domicilio v\u00e1lido',
            desc_ubicacion : 'Ingrese Descripci\u00f3n de Ubicaci\u00f3n',
            //entre_calle1 : 'Seleccione Calle 1',
           /// entre_calle2 : 'Seleccione Calle 2',
            //entre_calle1 : 'Seleccione Asentamiento',
           // entre_calle1 : 'Seleccione V\u00eda',
            CODIGO : 'Seleccione C\u00f3digo',
            ife : 'Numero de IFE Invalido', 
            calle_posterior : 'Seleccione Calle Posterior',
            id_pais : 'Seleccione Pa\u00eds',
            //ciudadano_mex : 'Seleccione si es ciudadano mexicano',
            esHomonimo : 'Verificar que es hom\u00f3nimo',
            grado: 'Ingrese el Grado De Insuficiencia Alimentaria',
            cartilla: 'Inserte n&uacute;mero de Cartilla'           
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
    $(document).on("change",".arma_curp, input[name=genero]:radio", function () { 

        //Obtenemos curp
        var curp = $("#curp").val(); 

        //alert('cambio');

        //Si tiene tamaño completo, verificamos dicha curp
        if(curp.length == 18){
            $('#curp').click();
        }        

     });

    //Llenamos curp
   $(document).on("click",".curp", function (){ 

        //Obtenemos variables
        var nombres = $("#nombres").val();
        var paterno = $("#paterno").val();
        var materno = $("#materno").val();
        var fecha_nacimiento = $("#fecha_nacimiento").val();        
        //var genero = $("#genero").val();
        var genero = $('input[name=genero]:radio:checked').val();
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
    

        if(nombres != '' && paterno != '' /*&& materno != ''*/ && fecha_nacimiento != '' &&

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

                        if(curp.length == 18 && respuesta.length == 16){
                            digitos = curp.substring(16,18);
                            //alert(digitos);
                            respuesta += digitos;
                        }
                        //alert('RES:'+respuesta);
                       $("#curp").val(respuesta);
                    },

                   error: function(){
                       $("#curp").val('');
                   }                

            });

        }

   });   

});