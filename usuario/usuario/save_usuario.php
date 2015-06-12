<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'usuario.php');


//Asignamos el tipo predeterminado de archivo
if(isset($_POST['clave_actual'])){
    
    $tipo = 'clave_usuario';
}else{
    $tipo = 'alta_usuario';
}

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );

//Validamos si editaremos o no
 if($id_edicion > 0 && $id_edicion != NULL){
        
        //Cambio de clave
        if(isset($_POST['clave_actual'])){            
                        
            //Editamos registro
            $resp = Usuario::saveClaveUsuario($_POST,$id_edicion);
            
        }else{//Edición de usuario normal
            
            //Tipo de archivo
            $tipo = 'edita_usuario';        

            //Editamos registro
            $resp = Usuario::saveUsuario($_POST,$id_edicion);
               
        }        

    }else{

    //Creamos registro        
     $resp = Usuario::saveUsuario($_POST);    
    }

    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if( $resp == 1 && $_POST['clave_actual'] == NULL || 
        $resp == 11 && $_POST['clave_actual'] == '1'
        ){ 
        $tipo = 'lista_usuario';
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }
//echo $resp;

//Redireccionamos con el tipo de respuesta
////echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);
?>