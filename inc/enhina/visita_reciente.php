<?php
//Habilitamos uso de variables de sesión
session_start();
if(isset($_POST['id_caravana'])){

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
//Obtenemos los tipos de lugar
$id_car = $_POST["id_caravana"];    

$sql = 'SELECT max(visita) as ultima_visita 
		FROM `historico_gia` 
		where id_caravana = ? ';

$params = array($id_car);

$obj = $db->rawQuery($sql,$params);
$obj = $obj[0];

$ultima_visita = 0;

if($obj != null){
	$ultima_visita = $obj['ultima_visita'];	
}

}else{

    exit;

}

?>

<?php if($ultima_visita != 0){ ?>
<div class="mensaje">
	Le recordamos que se han realizado <?php echo $ultima_visita ?> visita(s) 
	de esta caravana/comunidad
</div>
<?php } ?>