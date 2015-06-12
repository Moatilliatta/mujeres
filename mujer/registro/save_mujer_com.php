<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//para poder utilizar las funciones de la clase modelo
include_once($_SESSION['model_path'].'seg_activacion_com.php');

//Tipo de Archivo que lo direcciona hasta abajo agregando la extension "".php
$tipo = 'asigna_serv_mujer';

//Respuesta al intentar guardar datos
$resp = 0;

//Obtenemos información del formulario
$seg_activacion_com = $_POST;
$id_edicion = $_POST['id_edicion'];

//Procesamos información del formulario
if($id_edicion){
    $resp = SegActivacionCom::saveActivacionCom($seg_activacion_com,$id_edicion);
}else{
    //Campos incompletos
    $resp = 2;
    echo $id_edicion;
    exit;
}

    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if($resp == 1){         
        $tipo = 'asigna_serv_mujer';         
    }
    if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }    

//echo $resp;

//Redireccionamos con el tipo de respuesta
header('Location:'.$tipo.'.php?r=' . $resp);    
?>