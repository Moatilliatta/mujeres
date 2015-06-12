<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'seg_actividad.php');

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];
$tipo = 'alta_actividad';

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );

//Validamos si editaremos o no
 if($id_edicion > 0 && $id_edicion != NULL){
            
    //Tipo de archivo
    $tipo = 'edita_actividad';

    //Editamos registro
    $resp = SegActividad::saveActividad($_POST,$id_edicion);
               
    }else{

    //Creamos registro        
     $resp = SegActividad::saveActividad($_POST);    

    }

    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if($resp == 1 || ($resp == 14 && $id_edicion > 0)){    
        $resp = 1;     
        $tipo = 'lista_actividad';   
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }
//echo $resp;

//Redireccionamos con el tipo de respuesta
////echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);
?>