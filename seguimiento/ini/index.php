<?php
session_start();

//Incluimos cabecera
include('../../inc/header.php');

?>
<div id="principal">
   <div id="contenido">
        <h1 class="centro">&iexcl;Bienvenido al M&oacute;dulo de <?php echo $_SESSION['module_desc']; ?>!</h1>
        <?php
        //Si enviamos respuesta
        if(isset($_GET['r'])){
        
            //Variable de respuesta
            $respuesta = $_GET['r'];
            
            //Mensaje respuesta
            list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
        
        }                        
        ?>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

   </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path']. 'footer.php'); 		
?>  