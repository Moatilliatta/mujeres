jQuery(document).ready(function ($) {	        
    
    //Código General Jquery
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