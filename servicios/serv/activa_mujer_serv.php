<?php
session_start();//Indicamos el uso de variables de sesión

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");

//Librería de permisos
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//Obtenemos variables
$ID_MUJERES_AVANZANDO_DETALLE = $_GET["ID_MUJERES_AVANZANDO_DETALLE"];

//Respuesta al intentar guardar datos
$resp = 0;

//si recibimos el id activo entonces lo mandamos a activaBeneficiario
if($ID_MUJERES_AVANZANDO_DETALLE){
    //Editamos registro
    $resp = mujeresAvanzandoDetalle::activaServicioMujer($ID_MUJERES_AVANZANDO_DETALLE);
    }

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="lista_accion.php?r=' . $resp .'"</script>';
//header('Location:servicio_beneficiario_pys.php?r=' . $resp . '&id_beneficiario='. $id_beneficiario);
//header('Location:../../beneficiario/registro/seguimiento_beneficiario.php?r=' . $resp . '&id_beneficiario='. $id_beneficiario);
header('Location:' . getenv('HTTP_REFERER'));

?>