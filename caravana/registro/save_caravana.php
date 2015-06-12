<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'caravana.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'alta_caravana';

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];
//echo $id_edicion;
//exit;

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );


//Validamos si editaremos o no
 if($id_edicion > 0){
    


    //Tipo de archivo
    $tipo = 'edita_caravana';

    //Editamos registro
    $resp = Caravana::saveCaravana($_POST,$id_edicion);

    }else{
        
    //Creamos registro        
     $resp = Caravana::saveCaravana($_POST);
     //print_r ($resp);
     //exit;
      
    }

    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if($resp == 1){ 
        $tipo = 'listado_caravana';
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }

//echo $resp;

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);
?>