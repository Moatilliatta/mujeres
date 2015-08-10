<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
include ($_SESSION['inc_path']."libs/Permiso.php");

//Modelo de log_mujeres_avanzando
include_once($_SESSION['model_path'].'log_mujeres_cart.php');
include_once($_SESSION['model_path'].'caravana.php');

//Contador que nos indica el total de reposiciones exitosas
$rep = 0;

//Obtenemos valores
$reposiciones = json_decode($_POST['reposiciones'], true);

if(count($reposiciones)){

	$caravana = Caravana::caravanaActual();

	$id_caravana = (isset($caravana['id']))? $caravana['id'] : NULL;

	foreach ($reposiciones as $key => $value):
	
	if($key && $value){
		$obj['motivo'] = $value;
		$obj['id_mujeres_avanzando'] = $key;
		$obj['id_usuario_creador'] = $_SESSION['usr_id'];
		$obj['id_caravana'] = $id_caravana;
		
		//Guardamos datos referentes a la reposición
		$msg = logMujeresCart::saveLogMujeresCart($obj);

		if($msg == 1){
			$rep++;
		}
	}

	endforeach;

	if($rep == count($reposiciones)){
			$mensaje = "Se guardaron todas las reposiciones";
			$clase = "info_msg";
		}else{
			$mensaje = "NO se guardaron todas las reposiciones";
			$clase = "error_msg";
	}

}

?>

<div class="mensaje <?php echo $clase; ?>">
	<?php echo $mensaje;?>
</div>

