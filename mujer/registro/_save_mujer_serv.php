<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
//para poder utilizar las funciones de la clase modelo
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//Tipo de Archivo que lo direcciona hasta abajo agregando la extension "".php
$tipo = 'asigna_serv_mujer';

//Respuesta al intentar guardar datos
$resp = 0;

//Obtenemos id a editar
$id_edicion = $_POST['id_edicion'];

//Quitamos del arreglo POST la variable id_edicion
unset($_POST["id_edicion"] );

//Validamos si editaremos o no
 if($id_edicion > 0){        

    //Editamos registro
    $resp = mujeresAvanzandoDetalle::saveMujerServ($_POST,$id_edicion);
    
    }else{
    
    //Creamos registro        
     $resp = mujeresAvanzandoDetalle::saveMujerServ($_POST);    

    }
    
    /*Si la respuesta es exitosa enviamos al listado
    caso contrario (y si estamos editando) restauramos 
    los datos que se querían modificar*/
    if($resp == 1){         
        $tipo = 'lista_mujer';         
    }else if($id_edicion > 0){
        $resp .= '&id_edicion='.$id_edicion;
    }    

//echo $resp;

//Redireccionamos con el tipo de respuesta
header('Location:'.$tipo.'.php?r=' . $resp);    
?>