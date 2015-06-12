<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
//para poder utilizar las funciones de la clase modelo
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Tipo de Archivo, predeterminado el alta
$tipo = 'alta_mujer';

//tipo de accion te lo direcciona hasta abajo agregando la extension "".php

//Respuesta al intentar guardar datos
$resp = 0;

//Obtenemos id a editar
$id_edicion = $_POST['id_edicion'];

//Obtenemos id del expediente
$id_edicion_exp = $_POST['id_edicion_exp'];

//Obtenemos id de aspirante (de existir)
$id_aspirante = $_POST['id_aspirante'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );
//Validamos si editaremos o no
 if($id_edicion > 0){
    
    //Tipo de archivo
    $tipo = 'edita_mujer';        

    //Editamos registro
    list($resp,$curp,$id_beneficiario) = mujeresAvanzando::saveMujer($_POST,$id_edicion);
    
    }else{
        
    //Creamos registro        
     list($resp,$curp,$id_beneficiario)  = mujeresAvanzando::saveMujer($_POST);    
    }
    
    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if($resp == 1 || ($resp == 14 && $id_edicion > 0)){    
        $resp = 1;     
        $tipo = 'lista_mujer';   
    }

    //Si editamos un registro ponemos su id correspondiente
    if($id_edicion > 0 && $resp <> 1){
        $resp .= '&id_edicion='.$id_edicion;
    }

    if(isset($_SESSION['last_search'])){
        $resp.= '&'.$_SESSION['last_search'];
    }
    

//echo $resp;

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r='. $resp);    
?>