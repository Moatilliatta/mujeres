jQuery(document).ready(function ($) {	        
    
    //C�digo General Jquery
    //Login
    $("#formLogin").validate({
        rules: {  
            usuario:{required: true},
            clave: {required: true},
            id_caravana: {required: true}
				
        },
        messages: {                       
            usuario: "Escriba usuario",
            clave: "Escriba password",
            id_caravana : 'Seleccione Caravana'
                 
        }
    });    
          
});