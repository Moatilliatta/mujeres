<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include_once('../../inc/header.php'); 
include_once($_SESSION['model_path'].'callcenter.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');


//Traemos los status
$estatus = $db->get('status_llamada');

//Obtenemos id de callcenter
$id_callcenter = ($_GET['id_callcenter'] != null)? $_GET['id_callcenter'] : 0;

if(intval($id_callcenter)>0){
       
        //Obtenemos el registro del caravana
        $db->where('id',$id_callcenter);
        $callcenter = $db->getOne('callcenter');
        $id_mujeres_avanzando = $callcenter['id_mujeres_avanzando'];
        $id_usuario = $_SESSION['usr_id'];

        //Indicamos que tenemos ocupado este registro
        Callcenter::actualizaEstatus($id_usuario,$id_callcenter);
  }

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
?>
<div id="principal">
   <div id="contenido">
    <h2 class="centro">Edici&oacute;n de Llamada</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div align="center">   

        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            if($respuesta != 1){
                include_once($_SESSION['inc_path'] . "callcenter/datos_mujer.php");
                include_once("form_callcenter.php");
            }
        ?>

        <?php include ($_SESSION['inc_path'] . "callcenter/listado_call_hist.php");?>
    </div>
    </div>
</div>


<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>