<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//para poder utilizar las funciones de la clase c_mujeres_avanzando_detalle
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'alta_mujer_serv';

//Respuesta al intentar guardar datos
$resp = 0;

//obtenemos id a editar
$id_edicion = $_POST['id_edicion'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );

//Si hacemos petición ajax, guardamos en la variable
$ajax = $_POST["ajax"];

//Validamos si editaremos o no
 if($id_edicion > 0){

    //ID's generados de la tabla c_mujeres_avanzando_detalle
    $id_generados = array();

    //Tipo de archivo
    $tipo = 'edita_mujer_serv';
    //Editamos registro
    //$_POST cacha todos los names del form

    list($resp,$id_generados) = mujeresAvanzandoDetalle::saveMujerAvanzandoDet($_POST,$id_edicion);

    }else{
    //Creamos registro        
     list($resp,$id_generados) = mujeresAvanzandoDetalle::saveMujerAvanzandoDet($_POST);    
    }

//echo $resp;

//Si realizamos petición ajax es nula, redireccionamos a listado
if($ajax == NULL){

    //Si la respuesta es exitosa enviamos al listado caso contrario (y si estamos editando) restauramos los datos que se querían modificar
    if($resp == 1){
        $tipo = 'lista_mujer_serv'; 
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }

    //echo $resp;
    //Redireccionamos con el tipo de respuesta
    //echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
    header('Location:'.$tipo.'.php?r=' . $resp);    

}
?>