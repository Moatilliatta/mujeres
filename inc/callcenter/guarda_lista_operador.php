<?php
session_start();  

    if(!isset($db)){
        //Librería de conexión
        include($_SESSION['inc_path']."conecta.php");
    } 

include ($_SESSION['inc_path']."libs/Permiso.php");
include_once($_SESSION['inc_path'].'libs/CarOperador.php');

$lista_operadores = unserialize($_SESSION['arrayArt']);

//Contador que nos indica el total de reposiciones exitosas
$rep = 0;

if(count($lista_operadores)){

	foreach($lista_operadores->articulo_id as $key => $value):
	
	if($value){

		$obj['id_usuario'] = $value;
		$obj['id_callcenter_grupo'] = $lista_operadores->id_callcenter_grupo;
		
		//print_r($obj);

		//Guardamos datos referentes a la reposición
		$msg = CallcenterGrupoOperador::saveCallcenterGrupoOperador($obj);

		if($msg == 1){
			$rep++;
		}
	}

	endforeach;

	if($rep == count($lista_operadores)){
			$mensaje = "Se guardaron todos los operadores";
			$clase = "info_msg";
			unset($_SESSION['arrayArt']);
			
		}else{
			$mensaje = "NO se guardaron todas los operadores";
			$clase = "error_msg";
	}

}

?>

<div class="mensaje <?php echo $clase; ?>">
	<?php echo $mensaje;?>
</div>
