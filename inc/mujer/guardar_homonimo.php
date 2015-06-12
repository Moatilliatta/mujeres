<?php
session_start();//Habilitamos uso de variables de sesión

if( isset($_POST['nombres'])&&
	isset($_POST['paterno'])&&
	isset($_POST['materno'])&&
	isset($_POST['fecha_nacimiento'])&&
	isset($_POST['id_municipio_nacimiento'])&&
	isset($_POST['id_cat_estado'])){

    //Obtenemos parametros    
	$nombres = $_POST['nombres'];
	$paterno = $_POST['paterno'];
	$materno = $_POST['materno'];
	$fecha_nacimiento = $_POST['fecha_nacimiento'];
	$id_municipio_nacimiento = $_POST['id_municipio_nacimiento'];
	$id_cat_estado = $_POST['id_cat_estado'];
	$esHomonimo = $_POST['esHomonimo'];
    $id_mujeres_avanzando = $_POST['id_mujeres_avanzando'];

	//Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Verificamos función de homónimo
    include_once($_SESSION['model_path'].'mujeres_avanzando.php');
    
    //Ejecutamos función para verificar
    $Homonimo = mujeresAvanzando::verificaHomonimo(
                                $nombres,
                                $paterno,
                                $materno,
                                $fecha_nacimiento,
                                $id_municipio_nacimiento,
                                $id_cat_estado,
                                $id_mujeres_avanzando);

    //En caso de ser homónimo, y no tener confirmación de serlo, mostramos mensaje
   if($Homonimo === true && $esHomonimo != 'SI'){
   	echo '<div class="mensaje">Se ha identificado este registro como posible hom&oacute;nimo. Confirme si lo es en este mismo formulario en la seccion <b>Datos Generales del Beneficiario</b>. Si no puede verlo, contacte a un usuario que tenga este permiso</div>';
   }

}else{

    exit;

}