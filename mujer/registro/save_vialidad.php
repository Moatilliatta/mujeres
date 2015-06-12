<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Libreria de permisos
include_once($_SESSION['model_path'].'vialidad.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'alta_vialidad';

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];
$ajax = $_POST['ajax'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );
unset($_POST["ajax"] );

//Validamos si editaremos o no
 if($id_edicion > 0){
    
    //Tipo de archivo
    $tipo = 'edita_vialidad';        

    //Editamos registro
    //$_POST cacha todos los names del form
    //Beneficiario:: para poder utilizar lla funcion saveBneficiario del modelo 
    $resp = Vialidad::saveVialidad($_POST,$id_edicion);

    }else{
        
    //Creamos registro        
     $resp = Vialidad::saveVialidad($_POST);    
    }
    
    //echo $resp;
    
    //Si estamos usando ajax, mostramos mensaje aquí mismo    
    if($ajax){
        
        //Importamos librería de permiso
        include ($_SESSION['inc_path']."libs/Permiso.php");        
        
        //Obtenemos mensaje
        list($mensaje,$clase) = Permiso::mensajeRespuesta($resp);
        
        //Si se guardó correctamente el registro, personalizamos mensaje de registro correcto
        if($resp == 1){
            $mensaje = 'Vialidad agregada correctamente';
        }

        echo '<div class="mensaje '.$clase.' ">'.$mensaje.'</div>';
        
    }else{
        
        /*Si la respuesta es exitosa enviamos al listado
        caso contrario (y si estamos editando) restauramos
        los datos que se querían modificar*/
        
        if($resp == 1){ 
            $tipo = 'lista_vialidad'; 
        }else if($id_edicion > 0){
            $resp .= '&id_edicion='.$id_edicion;
        }
        
        //Redireccionamos con el tipo de respuesta
        //echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
        header('Location:'.$tipo.'.php?r=' . $resp);
            
    }
            
?>