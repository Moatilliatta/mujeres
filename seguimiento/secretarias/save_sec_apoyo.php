<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'seg_secretaria_apoyos.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'asigna_apoyo';

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];
$seg_apoyos = $_POST['id_seg_apoyo'];
$id_seg_secretaria = $_POST['id_seg_secretaria'];

//Creamos registro        
$resp = SegSecretariaApoyos::saveSecApoyo($seg_apoyos,$id_seg_secretaria);

/*Si la respuesta es exitosa enviamos al listado
caso contrario (y si estamos editando) restauramos 
los datos que se querían modificar*/
if($resp == 1){ 
	$tipo = 'lista_secretaria'; 
}else if($id_edicion > 0){
	$resp .= '&id_edicion='.$id_edicion;
}
//echo $resp;

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);
?>