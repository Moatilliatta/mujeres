<?php
session_start();
//Librería de conexión
include($_SESSION['inc_path']."conecta.php");
include_once('../../inc/libs/Permiso.php');
include_once($_SESSION['model_path'].'prog_estatal_mujeres.php');

//Obtenemos variables enviadas
$nombres = trim($_POST['nombres']);
$paterno = trim($_POST['paterno']);
$materno = trim($_POST['materno']);
$id_mujeres_avanzando = $_POST['id_mujeres_avanzando'];
$msg_no = 0;

//echo $nombres.'-'.$paterno.'-'.$materno.'-'.$id_mujeres_avanzando;
//exit;

//Procedemos a buscar vía web service
$prog_estatal = Permiso::buscaNombreWS($nombres,$paterno,$materno); 

//Si obtenemos arreglo
if (is_array($prog_estatal)){

	//Obtenemos objeto
	$prog_estatal = $prog_estatal[0];
    
    //Obtenemos la dependencia y el programa
    $dependencia = $prog_estatal->programas->Programa->CdDependencia;
 	$programa = $prog_estatal->programas->Programa->CdPrograma;

 	//Armamos arreglo
 	$programas = array('dependencia'=>$dependencia,
 						'programa'=>$programa);  
    
    //Guardamos en tabla correspondiente          
	$msg_no = ProgEstatalMujeres::saveProgEstatal($programas,$id_mujeres_avanzando);    
        
}

//Procesamos resultado      
$res = ($msg_no == 1)? $id_mujeres_avanzando : 0;                                
           
?>
<?php echo $res; ?>