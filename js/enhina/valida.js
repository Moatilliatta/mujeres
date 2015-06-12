jQuery(document).ready(function ($) {
    
    //Verificamos si hay homónimos
   $(document).on("submit","#carga_archivo", function (event) { 
        $('#spinner').css('display','block');
    });
    

$("#carga_archivo").validate({

    rules: {  
            id_caravana: {required: true,range: [1, 1000]},
            visita: {required: true}
          //  archivo:{required: true, accept:'application/vnd.ms-excel'}
                       
        },

    messages: {
            id_caravana: 'Seleccione Caravana',
            visita : 'Indicar número de visita'
            //archivo: 'Seleccione Archivo Excel'
           
        }
    });    
    
    
    
    
});
