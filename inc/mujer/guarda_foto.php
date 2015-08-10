<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
//Modelo de log_mujeres_avanzando
include_once($_SESSION['model_path'].'log_mujeres_avanzando.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos valores
$img = (isset($_POST['img']))? $_POST['img'] : null;
$folio = (isset($_POST['folio']))? $_POST['folio'] : null;
$id = (isset($_POST['id']))? $_POST['id'] : null;
$ruta = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS'];
//$ruta = 'C:\\xampp\\htdocs\\mujeres\\img\\mujeres\\';

//Procedemos a convertir
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);

//Guardamos archivo
$file = $ruta . $folio . '.png';
$success = file_put_contents($file, $data);
$clase = "";
$mensaje = "";
$num_folio = NULL;

if($success){
	$mensaje = 'Fotografía guardada exitosamente';
	$clase = 'info_msg';	
	
	//Guardamos fecha en que fue tomada la foto en el log
	$data = Array('folio' => $folio,
				  'id' => $id,
				  'fecha_foto' => date('Y-m-d h:i:s'));	

	$msg = logMujeresAvanzando::saveLogMujeresAvanzando($data);
		if($msg != 1){
			echo 'Mensaje: '.$msg;
		}

	$porciones = explode("-", $folio);

	//Contiene guiones
	if(count($porciones == 2)){
		list($folio,$num_folio) = mujeresAvanzando::obtenFolio($folio);
	}

	//Guardamos fecha en que fue tomada la foto en la tabla de mujeres_avanzando
	$msg = mujeresAvanzando::actualizaFoto($folio,$num_folio);
		if($msg != 1){
			echo 'Mensaje: '.$msg;
		}	

}else{
	$mensaje = 'Todas las beneficiarias seleccionadas deben tener su foto capturada para realizar esta operacio&oacute;n';
	$clase = 'error_msg';
}
?>
<div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>