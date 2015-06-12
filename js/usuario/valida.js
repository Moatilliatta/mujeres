jQuery(document).ready(function ($) {	        

    //Código General Jquery
    //Usuario
      $("#formUsr").validate({

        rules: {  
            usuario:{required: true, minlength: 3},
            clave: {required: true,
				    minlength: 5},
            clave_conf: {required: true,
				         minlength: 5,
				         equalTo: "#clave"},
            nombres:{required: true, minlength: 3},
            paterno:{required: true, minlength: 3},
            correo: "required",
            activo: {required: true},
            id_perfil: {required: true,range: [1, 100]}
        },

        messages: {                       
            usuario: "Ingrese Nombre de Usuario (m\u00ednimo 3 letras)",
            clave: "Ingrese su password",
            clave_conf: "Confirme su password",
            nombres: "Ingrese Nombre(s) m\u00ednimo 3 letras",
            paterno: "Ingrese Apellido Paterno (m\u00ednimo 3 letras)",
            correo: "Introduzca E-mail",
            activo: "Seleccione status",
            id_perfil: "Seleccione Perfil"
        }

    });
        
        
         //usuario grupo
       $("#formUsr_Gpo").validate({

            rules: {
                id_grupo: {required: true,range: [1, 100]}
             },
            messages: {                       
                id_grupo: "Seleccione Grupo"
            }

        });

        //Submodulo
        $("#formSub").validate({

            rules: {  
                id_modulo: {required: true,range: [1, 100]},
                nombre:{required: true, minlength: 3},
                descripcion:"required",
                activo: {required: true }
            },

            messages: {                       
                id_modulo: "Seleccione M\u00f3dulo",
                nombre: "Ingrese Nombre (m\u00ednimo 3 letras)",
                descripcion: "Ingrese descripci\u00f3n del subm\u00f3dulo",
                activo: "Seleccione status"
            }
        });

        //Perfil
        $("#formPrf").validate({
        rules: {  
            nombre:{required: true, minlength: 3},
            activo: {required: true}
        },
        messages: {                       
            nombre: "Ingrese Nombre (m\u00ednimo 3 letras)",
            activo: "Seleccione status"
        }

    });

    //Modulo

    $("#formMod").validate({

        rules: {  
            nombre:{required: true, minlength: 3},
            descripcion:"required",
            activo: {required: true }
        },

        messages: {                       
            nombre: "Ingrese Nombre (m\u00ednimo 3 letras)",
            descripcion:"Ingrese descripci\u00f3n de m\u00f3dulo",
            activo: "Seleccione status"
        }

    });

    //Grupo
    $("#formGpo").validate({

        rules: {  
            nombre:{required: true, minlength: 3},
            activo: {required: true}
        },

        messages: {                       
            nombre: "Ingrese Nombre (m\u00ednimo 3 letras)",
            activo: "Seleccione status"
        }

    });
    
    //Grupo_Accion

    $("#formGpo_Acc").validate({

        rules: {  
           id_modulo: {required: true,range: [1, 999]},
           id_submodulo: {required: true,range: [1, 999]},
           id_accion: {required: true,range: [1, 999]}
        },

        messages: {                       
            id_modulo: "Seleccione M\u00f3dulo",
            id_submodulo: "Seleccione Subm\u00f3dulo",
            id_accion: "Seleccione Acci\u00f3n"
         }

    });

    //Bloqueo
    /*
    var container = $('#errorContainer');

    $("#formBlq").validate({        
    // We must to force validation of entire form because
    // when we remove value from 2-nd field 
    // the 1-st field becomes invalid.

    onfocusout: function() { $("#formBlq").valid() },
    onkeyup: function() { $("#formBlq").valid() },

        rules: {  
            id_usuario: {require_from_group: [1,".fillone"]},
            id_grupo: {require_from_group: [1,".fillone"]},
            id_modulo: {require_from_group: [1,".fillone"]},
            id_submodulo: {require_from_group: [1,".fillone"]},
            id_accion: {require_from_group: [1,".fillone"]}
        },

        success: function(label) {  
      label.html(" ").addClass("checked"); 
    },
    errorContainer: container,
    errorLabelContainer: $("ol", container),
    wrapper: 'li',
    meta: "validate"
    });

        $("#formBlq").validate({
        rules: {  
            id_usuario: {required: true,range: [1, 100]}
        },
        messages: { 
            id_usuario: "Seleccione Usuario"                           
        }

    });
   */
    

   //Acciones

        $("#formAcc").validate({

        rules: {  
            id_modulo: {required: true,range: [1, 100]},
            id_submodulo: {required: true,range: [1, 100]},
            nombre:{required: true, minlength: 3},
            descripcion:{required: true, minlength: 3},
            activo: {required: true},
            mostrar_menu: {required: true}
        },

        messages: { 
            id_modulo: "Seleccione M\u00f3dulo",
            id_submodulo: "Seleccione Subm\u00f3dulo",
            nombre: "Ingrese nombre m\u00ednimo tres letras",
            descripcion: "Ingrese descripci\u00f3n de la acci\u00f3n",
            activo: "Seleccione status",
            mostrar_menu:"Seleccione men\u00fa"                           
        }

    }); 

    //Cambia clave
    $("#formCve").validate({

        rules: {
            clave_actual: {required: true,
				minlength: 5},
            clave: {required: true,
				minlength: 5},
            clave_conf: {required: true,
				minlength: 5,
				equalTo: "#clave"}
        },

        messages: { 
            clave_actual: "Ingrese clave actual",
            clave: "Ingrese clave nueva",
            clave_conf: "Confirme clave nueva"                           
        }

    });
    
    //Menus

        $("#formMnu").validate({

        rules: {  
            nombre:{required: true, minlength: 3},
            activo: {required: true}
        },

        messages: { 
            nombre: "Ingrese Men\u00fa",
            activo: "Seleccione status"
        }

    });           

    //Acciones0 Menu

        $("#formAcc_Mnu").validate({

        rules: {  
            id_grupo: {required: true,range: [1, 100]},
            id_accion_grupo: {required: true,range: [1, 10000]}
        },

        messages: { 
            id_grupo: "Seleccione Grupo",
            id_accion_grupo: "Seleccione Acci\u00f3n Grupo"
        }

    });           

    $("#caravana").click(function(){

        var valor = $(this).attr("value");
        if(valor == ''){
            $("#caravana option:selected").removeAttr("selected");
        }
    });

});