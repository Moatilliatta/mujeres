<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
//para poder utilizar las funciones de la clase modelo
include_once($_SESSION['model_path'].'seg_capacitacion_mujer.php');

//print_R($_POST);
//exit;
//Tipo de Archivo, predeterminado el alta
$tipo = 'asigna_serv_mujer';

//Respuesta al intentar guardar datos
$resp = 0;

//Obtenemos id a editar
$id_edicion = $_POST['id_mujeres_avanzando'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );
//Validamos si editaremos o no
 if($id_edicion > 0){    
    //Creamos registro        
    $resp = SegCapacitacionMujer::saveSegCapacitacionMujer($_POST,$id_edicion); 
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
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r='. $resp);    
?>