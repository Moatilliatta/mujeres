<?php
session_start();//Indicamos el uso de variables de sesi�n

//Librer�a de conexi�n
include ($_SESSION['inc_path']."conecta.php");

//Librer�a de permisos
include_once($_SESSION['model_path'].'callcenter.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'alta_callcenter';

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_callcenter'];

//echo $id_callcenter;
//exit;

//Validamos si editaremos o no
 if($id_edicion > 0){

    //Tipo de archivo
    $tipo = 'edita_callcenter';

    //Editamos registro
    $resp = CallCenter::saveCallCenter($_POST,$id_edicion);

    }else{
        
    //Creamos registro        
     $resp = CallCenter::saveCallCenter($_POST);
     //print_r ($resp);
     //exit;
      
    }

    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se quer�an modificar*/
    if($resp == 1){ 
        $tipo = 'lista_callcenter';
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }

//echo $resp;

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);
?>