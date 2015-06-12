<?php
session_start();

//Incluimos cabecera
include('../../inc/header.php');

    //Obtenemos id del usuario
    $id_usuario = $_SESSION['usr_id'];

    //Obtenemos listado de Módulos que tiene el usuario
    $modulos = Permiso::getModulos($id_usuario);
       
?>

<style>
#contenido {
-moz-box-sizing: border-box;
    background: url("../../img/hojas.jpg") no-repeat scroll right top #FFFFFF;
    box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.3);
}
</style>

<div id="principal">
   <div id="contenido">
   
   <div class="mensaje">Listado de M&oacute;dulos</div>
   
    <div class="nivel">
    <?php         
    if($modulos){
    
        foreach($modulos as $m):?>
        <div class="subnivel">
        <a href="<?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].'/ini/index.php' ?>">
            <img src="<?php echo $_SESSION['img_path'].'img_'.$m['nombre_modulo']?>.jpg" />
        </a>            
        <label>
            <a href="<?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].'/ini/index.php' ?>">
            <?php echo $m['descripcion_modulo'];?></a>
        </label>
        </div>        
        
        <?php endforeach;
            }
        ?>
    </div>
   		
   </div>
</div>
 		 
<?php 
//Incluimos pie
include($_SESSION['inc_path']. 'footer.php'); 		
?>  