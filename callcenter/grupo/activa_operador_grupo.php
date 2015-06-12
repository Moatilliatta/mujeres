<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'callcenter_grupo_operador.php');

//Obtenemos variables
$id_activo = $_GET["id_activo"];
//Obtenemos grupo que obtendrá operador
$id_callcenter_grupo = $_GET['id_callcenter_grupo'];

//Respuesta al intentar guardar datos
$resp = 0;

if($id_activo){
    //Editamos registro
    $resp = CallcenterGrupoOperador::activaCallcenterGrupoOperador($id_activo);
}
    
//Redireccionamos con el tipo de respuesta
header('Location:asigna_operador_grupo.php?r=' . $resp .
		'&id_callcenter_grupo='.$id_callcenter_grupo);
?>