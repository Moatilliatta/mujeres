<?php
session_start();
//Librería de conexión
include($_SESSION['inc_path']."conecta.php");

$id_c = ($_POST['id_caravana'] != null)? $_POST['id_caravana'] : null;

$db->where('id_caravana',$id_c);
$beneficiario_caravana = $db->get('mujeres_avanzando');
echo json_encode($beneficiario_caravana); 
?>