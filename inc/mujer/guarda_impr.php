<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
include ($_SESSION['inc_path']."libs/Permiso.php");
//Modelo de log_mujeres_avanzando
include_once($_SESSION['model_path'].'log_mujeres_avanzando.php');

//Obtenemos valores
$folio = $_POST['folio'];

//Armamos arreglo
$data = Array('folio' => $folio,
			  'fecha_impresion' => date('Y-m-d h:i:s'));	

//Guardamos fecha en que fue impresa la foto
$msg = logMujeresAvanzando::saveLogMujeresAvanzando($data);

//En caso de no guardarse en el log mostramos mensaje
	if($msg != 1){
			echo 'Mensaje: '.$msg;
	}
?>