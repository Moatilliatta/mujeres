var fechas = (function(){
  
  //Private members
      /**
       * Determinamos si es un año bisiesto
       * @param  {[type]}  year [description]
       * @return {Boolean}      [description]
       */
    var isLeapYear = function (year) {
      var d = new Date(year, 1, 28);
      d.setDate(d.getDate() + 1);
      return d.getMonth() == 1;
    };

     /**
       * Obtenemos la edad de una persona
       * @param  {[type]} date [description]
       * @return {[type]}      [description]
       */
    var getAge = function(date) {
          var d = new Date(date), now = new Date();
          var years = now.getFullYear() - d.getFullYear();
          d.setFullYear(d.getFullYear() + years);
          if (d > now) {
              years--;
              d.setFullYear(d.getFullYear() - 1);
          }
          var days = (now.getTime() - d.getTime()) / (3600 * 24 * 1000);
          return years + days / (isLeapYear(now.getFullYear()) ? 366 : 365);
    };

    var elems = {
      changeMonth: true,
      changeYear: true,
      dayNames: [ "Lunes","Martes","Miércoles","Jueves","Viernes","Sabado","Domingo"],
      dayNamesMin: [ "Lun","Mar","Mie","Jue","Vie","Sab","Dom" ],
      monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec" ],
      //appendText: " (aaaa-mm-dd)",
      nextText: "Sig",
      dateFormat: "yy-mm-dd"
    };

    //Fechas de nacimiento de 1900 a 1996
    var birth = {
      minDate: new Date(1900, 1 - 1, 1),
      yearRange: "1900:1996"  
    };

    //El rango es desde 1900 hasta la fecha actual    
    var todate = {
      minDate: new Date(1900, 1 - 1, 1),
      yearRange: "1900:" + new Date().getFullYear(),
      maxDate: "+0m +0w"
    };

    //El rango es de 1900 hasta 2030
    var datefree = {
      yearRange : "1900:2030"
    };


    //Fechas con rango de inicio y fin  (rango de 1900 a fecha actual)
    var from = {
      onClose : function( selectedDate ) { 
                  $( "#to" ).datepicker( "option", "minDate", selectedDate );
                }
    };


    var to = {
      onClose : function( selectedDate ) {
                  $( "#from" ).datepicker( "option", "maxDate", selectedDate );
                }
    };

    //función para fecha actual
    var todaysDate = function () {
        
         var currentTime = new Date()
         var year = currentTime.getFullYear()
         var month = currentTime.getMonth() + 1
         var day = currentTime.getDate()
         return year + "-" + month + "-" + day;

    };

    var today = function () {

          //Obtenemos instancia de fecha
          var today = new Date();        

          //Obtenemos el input PREVIO al botón, así modificaremos la fecha
          var input_id = $(this).prevAll('input:text').attr("id");

          //console.log('#'+input_id);

          //Agregamos fecha actual al input correspondiente
          $('#'+input_id).focus();
          $('#'+input_id).datepicker('setDate', todaysDate());

      };

      var limpiar = function () {
      
            //Obtenemos ID previo al botón
            var id = $(this).closest('tr').find("input:text").attr('id');

            //Obtenemos el input PREVIO al botón, así modificaremos la fecha
            var input_id = $(this).prevAll('input').attr("id");

            //Borramos el valor de la fecha 
            $("#"+id).val(''); 
      
      };

      //Public members

      return{            
            /**
            * Función para validar la fecha y ver si es menor de 18 años
            * @param  {[type]} fecha [description]
            * @return {[type]}       [description]
            */
          validaFecha : function (fecha){

            var date = fecha;
            var axos = parseInt(getAge(date)); 
            
            //alert( axos  + ' años');
            
            if(axos < 18){
                $(".tut").css('display' , 'inline' );
                $("#nombre_tutor").attr('required' , '' );
                $("#paterno_tutor").attr('required' , '' );        
                $('#estado_civil').val('1'); 
            }else{
                $(".tut").css('display' , 'none' );
                
                var nombre_tutor = $("#nombre_tutor");
                var paterno_tutor = $("#paterno_tutor");

                nombre_tutor.removeAttr( "required" );
                nombre_tutor.removeClass( "error");
                paterno_tutor.removeAttr( "required" );
                paterno_tutor.removeClass( "error");

            }

          },

          onReady : function(){
            
            //Fecha normal, hasta el día de hoy
            $(".fecha").datepicker($.extend({},elems,todate));    

            //Fecha con rango libre, hasta 2030
            $(".fecha_libre").datepicker($.extend({},elems,datefree));
                
            //Establecemos arreglos
            $( "#from" ).datepicker($.extend({},elems,from,todate));

            //console.log($.extend(elems,from,datefree));
            $( "#to" ).datepicker($.extend({},elems,to,todate));

            //Ponemos fecha en input
            $(document).on("click",".today", today); 

            //borramos fecha al activar/desactivar una capacitacion
            $(document).on("click",".limpiar", limpiar);

            //Campo de fecha sólo de lectura
            $(".fecha, #from, #to").attr('readOnly' , 'true' );

          }
        
    };  
    
})();

//Obtenemos funciones
jQuery(document).ready(fechas.onReady);