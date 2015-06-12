<?php
session_start();//Habilitamos uso de variables de sesión

if( isset($_POST['nombres'])&&
	isset($_POST['paterno'])&&
	isset($_POST['materno'])&&
	isset($_POST['fecha_nacimiento'])&&
	isset($_POST['genero'])&&
	isset($_POST['id_cat_estado'])){

	//Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Incluimos clase CURP
    include_once($_SESSION['inc_path'] . 'libs/Curp.php');

    //Incluimos librería de permiso
    include_once($_SESSION['inc_path'].'libs/Permiso.php');

    //Obtenemos parametros    
	$nombres = mb_strtoupper(Permiso::limpiar(utf8_decode($_POST['nombres'])),'UTF-8');
	$paterno = mb_strtoupper(Permiso::limpiar(utf8_decode($_POST['paterno'])),'UTF-8');
	$materno = mb_strtoupper(Permiso::limpiar(utf8_decode($_POST['materno'])),'UTF-8');
	$fecha_nacimiento =  $newDate = date("d/m/Y", strtotime($_POST['fecha_nacimiento']));
	$genero = $_POST['genero'];
	$id_cat_estado = $_POST['id_cat_estado'];	
	//parametro para comparar CURP
	$curp = $_POST['curp'];
    
    //Ejecutamos función para verificar
    $curp_generada = Curp::generar_CURP(
    							$paterno,
    							$materno,
    							$nombres,
    							$fecha_nacimiento,
    							$genero,
    							$id_cat_estado,
    							true);    

    //Si queremos comparar curp
    if($curp != NULL &&strlen($curp) == 18){

    	//Quitamos dígitos finales al curp
    	$curp_sin_digitos = substr($curp, 0, -2);

    	//Verificamos la curp que se anotó con la que generamos    	
    	echo ($curp_sin_digitos == $curp_generada)? $curp : $curp_generada;

    }else{

		//Imprimimos si se generó la clave curp
    	echo ($curp_generada != NULL)? $curp_generada : '';
        //echo count($curp);

    }
    

}else{

    exit;

}
?>