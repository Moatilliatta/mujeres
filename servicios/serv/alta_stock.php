<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include_once('../../inc/header.php'); 

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
?>
<div id="principal">
   <div id="contenido">
    <h2 class="centro">Stock</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div  align="center">                
        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            if($respuesta != 1){        
                include_once("form_stock.php");    
            }
        ?>
    </div>
    </div>
</div>


<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>