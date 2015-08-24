<?php
//Habilitamos uso de variables de sesión
session_start();
if(isset($_POST['id_tipo_lugar'])){

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
//Obtenemos los tipos de lugar
$id_tipo_lugar = $_POST["id_tipo_lugar"];    

$sql = 'SELECT id, descripcion FROM `caravana` where id_tipo_lugar = ? ' ;
$params = array($id_tipo_lugar);
$caravana= $db->rawQuery($sql,$params);

}else{

    exit;

}

?>

<select id="id_caravana"  name="id_caravana">
   <option value=''>Seleccione Tipo De Importaci&oacute;n</option>
   <?php foreach($caravana as $c):?>
<option value='<?php echo $c['id'] ?>' > <?php echo $c['descripcion'];?></option>
    <?php endforeach; ?>
</select>   



             