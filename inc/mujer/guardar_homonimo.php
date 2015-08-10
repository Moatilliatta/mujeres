<?php
session_start();//Habilitamos uso de variables de sesión

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");

//Verificamos función de homónimo
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
    

    //Obtenemos parametros    
	$nombres = (isset($_POST['nombres']))? $_POST['nombres'] : null;
	$paterno = (isset($_POST['paterno']))? $_POST['paterno'] : null;
	$materno = (isset($_POST['materno']))? $_POST['materno'] : null;
	$fecha_nacimiento = (isset($_POST['fecha_nacimiento']))? $_POST['fecha_nacimiento'] : null;
	$id_municipio_nacimiento = (isset($_POST['id_municipio_nacimiento']))? $_POST['id_municipio_nacimiento'] : null;
	$id_cat_estado = (isset($_POST['id_cat_estado']))? $_POST['id_cat_estado'] : null;
	$esHomonimo = (isset($_POST['esHomonimo']))? $_POST['esHomonimo'] : null;
    $id_mujeres_avanzando = (isset($_POST['id_mujeres_avanzando']))? $_POST['id_mujeres_avanzando'] : null;

if( $nombres && $paterno && $materno && $fecha_nacimiento && 
    $id_municipio_nacimiento && $id_cat_estado)
{

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