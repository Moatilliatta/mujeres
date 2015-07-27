<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
include ($_SESSION['inc_path']."libs/Permiso.php");
//Modelo de log_mujeres_avanzando
include_once($_SESSION['model_path'].'log_mujeres_cart.php');

//Contador que nos indica el total de reposiciones exitosas
$rep = 0;

//Obtenemos valores
$reposiciones = array_filter($_POST['reposiciones'], create_function('$a','return preg_match("#\S#", $a);'));

if(count($reposiciones)){

	foreach ($reposiciones as $key => $value):
	
	if($key && $value){
		$obj['motivo'] = $value;
		$obj['id_mujeres_avanzando'] = $key;
		$obj['id_usuario_creador'] = $_SESSION['usr_id'];
		
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

