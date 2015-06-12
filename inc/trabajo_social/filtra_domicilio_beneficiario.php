<?php 
session_start();//Habilitamos uso de variables de sesión
//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
include_once($_SESSION['model_path'].'aspirantes.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

if(isset($_POST['CVE_TIPO_VIAL']) && isset($_POST['CVE_VIA']) && isset($_POST['num_ext'])){
    
    //Obtenemos variables
    $CVE_TIPO_VIAL = $_POST['CVE_TIPO_VIAL'];
    $CVE_VIA = $_POST['CVE_VIA'];
    $num_ext = $_POST['num_ext'];
    $mensaje = NULL;
   
    list($lista,$p) = Aspirantes::verificaDomicilioBeneficiario($CVE_TIPO_VIAL,$CVE_VIA,$num_ext);     
    
//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL && count($_POST) > 1){
    //No existen registros con el criterio de búsqueda
    list($mensaje,$clase) = Permiso::mensajeRespuesta(8);
}elseif ($lista == NULL) {
    //No hay registros para mostrar
    list($mensaje,$clase) = Permiso::mensajeRespuesta(17);
}
    
}else{
   exit;   
}
  
   //Si tenemos listado
    if($lista != NULL){
    // Listado de páginas del paginador
    //echo $p->display();
        ?>

    <?php if($mensaje != NULL){?>    
    <div class="mensaje <?php echo $clase ?>">
      <?php echo $mensaje;?>
    </div>        
    <?php } ?>

    <div id="page_list" align="center">        
    
    <div class="aviso_coincidencia">
        <p><label>Posibles Coincidencias</label></p>    
        <p>Se han encontrado los siguientes registros que podr&iacute;an 
        corresponder a la persona que est&aacute;n capturando. Si el 
        beneficiario ya est&aacute;, selecci&oacute;nelo de la lista.
        </p>   
    </div>    
    
    <div class="resultado_coincidencia">
        <?php foreach($lista as $l): ?>
            <div class="resultado_datos">
                <label><?php echo $l['nombre_completo'];?></label>
                <label>Tipo de Vialidad: <?php echo $l['tipo_vialidad'];?></label>
                <label>Vialidad: <?php echo $l['vialidad']; ?></label>
                <label>Numero Exterior: <?php echo $l['num_ext']; ?></label>
            </div>
            <div class="resultado_opcion">
                <label>&Eacute;sta es la persona que busco</label>
                <input type="radio" id="<?php echo $l['id'];?>" name="beneficiario" value="<?php echo $l['id'];?>"/>
                <input type="button" name="beneficiario" value="Quitar" class="quitar" id="<?php echo $l['id'];?>" />            
            </div>        
        <?php endforeach;?>
    </div>
    
    <?php }  ?>    
    