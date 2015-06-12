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
    
    
/*
Refrescamos select de caravana o comunidad
*/

$("#id_tipo_lugar").change(function(){

		$("select[name='id_tipo_lugar'] option:selected").each(function () {

        id_tipo_lugar = $(this).attr("value");
           
        var parametros = {
          'id_tipo_lugar' : id_tipo_lugar
        }

        frmAjax(parametros,'tipo_lugar','filtra_tipo_lugar');

   })

	});
    
  
    //Definimos valores de variables
    var percentComplete = 0;
    var total = 0;
    var total_ok = 0;
    var percentVal = 0;
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
    var id_mujeres_tot = [];
    var id_mujeres_proc = [];

    //Verificamos si hay homónimos
    $(document).on("submit","#carga_archivo", function (e) {     
      
      //disable the default form submission
      e.preventDefault();

      //Obtenemos datos del formulario
      var formData = new FormData($(this)[0]);      

      //Mandamos llamar función de porcentaje
      frmAjaxPct(formData,'resultado','arreglo');
         

   });
  
  //Redondeamos a 2 digitos
  function roundToTwo(num) {    
    return +(Math.round(num + "e+2")  + "e-2");
  }

  //Función para llenar la barra de porcentaje 
  function pct(respuesta){

    //Obtenemos respuesta, si es diferente de 0
    //guardamos la respuesta (en este caso, es el id_mujeres_avanzando)
    if(parseInt(respuesta) != 0){
      total_ok++;
      id_mujeres_proc.push(parseInt(respuesta));
    }    

    //Hacemos cálculos de porcentaje
    percentComplete = percentComplete + 1;
    percentVal = roundToTwo((percentComplete/total)*100);
    text = percentVal + '% ';
    bar.width(text);

    text = text +'('+percentComplete+' de '+total+')';
    percent.html(text);        

    //Mensaje de éxito
    mensaje = "<div class='mensaje info_msg'>Se han procesado "+total+
    " registros, de los cuales "+total_ok+" fueron exitosos</div>";

    //Si llegamos al 100%, mostramos mensaje de carga completa
    if(percentVal == 100){
        alert('Carga Exitosa: '+id_mujeres_tot.length+' - '+id_mujeres_proc.length);
                        
        //Obtenemos los id_mujeres_avanzando que NO fueron procesados
        no_coincide = $.grep(id_mujeres_tot,function(x) {return $.inArray(x, id_mujeres_proc) < 0});

        $('#resultado').html(mensaje);

        //Obtenemos caravana actual
        var id_caravana = $('#id_caravana').val();

        //Llenamos parámetros
        var parametros = {
          'id_caravana' : id_caravana,
          'no_coincide':no_coincide
        };

        //Listamos total de beneficiarios guardados
        frmAjax(parametros,"listado","listado_prog_est");

        //reiniciamos valores
        percentComplete = 0;
        total = 0;
        total_ok = 0;
        percentVal = 0;
        id_mujeres_tot = [];
        id_mujeres_proc = [];

    }

  }

  //Form Ajax Genérico con callback
   function frmAjaxCallBack(parametros,div,accion,tipo,ruta,selector){
    
    //Valores predeterminados    
    tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/estadisticas/';
    selector = typeof selector !== 'undefined' ? selector : '#';

    //Regresamos instancia ajax para que pueda ser manipulada
    return $.ajax({
      type: tipo,
      url: ruta+accion+".php",
      data: parametros,
      beforeSend: function(){},
      success: function(respuesta){},
      error: function(){}
    });
    
   }

   //Función ajax para obtener los datos del modelo (usando json)
   //para posteriormente enviarlos al web service
   //y cargar los datos de cada mujer
   function frmAjaxPct(parametros,div,accion,tipo,ruta,selector,act){

     //Valores predeterminados    
    tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/estadisticas/';
    selector = typeof selector !== 'undefined' ? selector : '#';

        $.ajax({
              processData: false,
              contentType: false,
              type: "POST",
              url: ruta+accion+".php",
              data: parametros,

              beforeSend: function() {
                $(selector+div).html('<img src="../../css/img/loader_sug.gif"/>Procesando');                
              },
            
              success: function(datos){
              
              //Obtenemos el arreglo impreso en json desde PHP
              //en una variable para procesarla
              var dataJson = eval(datos);

              //Obtenemos total de elementos en el arreglo
              for(_obj in dataJson) total++;

              //Recorremos arreglo de beneficiario_caravana  
              for(var i in dataJson){
                    
              //alert(dataJson[i].nombres + " _ " + dataJson[i].paterno + " _ " + dataJson[i].materno);

              //Obtenemos datos para ser enviados al webservice
              var parametros = {
                  'nombres' : dataJson[i].nombres,
                  'paterno' : dataJson[i].paterno,
                  'materno' : dataJson[i].materno,
                  'id_mujeres_avanzando': dataJson[i].id
                }

              id_mujeres_tot.push(dataJson[i].id);

              //Mandamos llamar al webservice
              frmAjaxCallBack(parametros,'resultado','llama_webservice').done(pct);

              }
                    
            },
                    
            error: function(){},  
             
            //Evento se ejecuta al subir un archivo al servidor       
            uploadProgress: function(event, position, total, percentComplete) {
                percentVal = percentComplete;
                text = percentVal + '%';
                bar.width(text);
                percent.html(text);
            },            
            
            //Evento al completarse la carga
            complete: function(xhr) {
                //status.html(xhr.responseText);
            }
        }); 
   }
    
   //Form Ajax Genérico
   function frmAjax(parametros,div,accion,tipo,ruta,selector){

    //Valores predeterminados
    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/estadisticas/';
    tipo = typeof tipo !== 'undefined' ? tipo : 'POST';
    selector = typeof selector !== 'undefined' ? selector : '#';
    
    $.ajax({
      type: tipo,
      url: ruta+accion+".php",
      data: parametros,
      beforeSend: function(){
        $(selector+div).html('<img src="../../css/img/loader_sug.gif"/>Buscando');  
        //alert(parametros.tipo+' before');
      },
      success: function( respuesta ){                
          $(selector+div).html(respuesta);        
      },
      error: function(){                
          $(selector+div).html(' ');            
      }
    });

   }

    
});