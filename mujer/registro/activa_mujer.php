<?php
session_start();//Indicamos el uso de variables de sesión
//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
//Librería de permisos
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
//Obtenemos variables
$id_activo = $_GET["id_activo"];
//Respuesta al intentar guardar datos
$resp = 0;
//si recibimos el id activo entonces lo mandamos a mujeresAvanzando
if($id_activo){
    //Editamos registro
    $resp = mujeresAvanzando::activaMujer($id_activo);
    }
    
//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="lista_accion.php?r=' . $resp .'"</script>';
header('Location:lista_mujer.php?r=' . $resp);
?>