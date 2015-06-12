<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'submodulo.php');

//Obtenemos variables
$id_activo = $_GET["id_activo"];

//Respuesta al intentar guardar datos
$resp = 0;

if($id_activo){
    //Editamos registro
    $resp = Submodulo::activaSubmodulo($id_activo);
    }
    
//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="lista_submodulo.php?r=' . $resp .'"</script>';
header('Location:lista_submodulo.php?r=' . $resp);
?>