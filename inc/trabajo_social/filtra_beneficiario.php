<?php 
session_start();//Habilitamos uso de variables de sesión

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
include_once($_SESSION['model_path'].'aspirantes.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');

if(isset($_POST['paterno']) && isset($_POST['nombres'])){
    
    //Obtenemos variables
    $nombres = $_POST['nombres'];
    $paterno = $_POST['paterno'];
    $materno = $_POST['materno'];   
    $mensaje = NULL;
    
    //Obtenemos listado
    $lista = Aspirantes::verificaDuplicado_beneficiario($nombres,$paterno,$materno,NULL);     

    ///si la lista nula enviamos mensaje de que no hay registro en la busqueda
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
    <div class="mensaje <?php echo $clase; ?>">
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
                <label><?php echo $l['municipio'].', '.$l['estado'];?></label>
                <label>Fecha de Nacimiento: <?php echo Fechas::fechalarga($l['fecha_nacimiento']); echo ($l['fecha_aproxim'] =='SI')?' (aproximada) ':''; ?></label>
            </div>
            <div class="resultado_opcion">
                <label>&Eacute;sta es la persona que busco</label>
                <input type="radio" id="<?php echo $l['id'];?>" name="beneficiario" value="<?php echo $l['id'];?>"/>
                <input type="button" name="beneficiario" value="Quitar" class="quitar" id="<?php echo $l['id'];?>" />            
            </div>        
        <?php endforeach;?>
    </div>

<!--
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Estado</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <th>Seleccionar</th>
    </thead>
    <tbody>
        <?php /* foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombres'];?></td>
            <td><?php echo $l['paterno'];?></td>
            <td><?php echo $l['materno'];?></td>
            <td><?php echo $l['estado'];?></td>
            <td><?php echo $l['municipio'];?></td>
            <td><?php echo $l['localidad'];?></td>
            <td>
            <input type="radio" id="<?php echo $l['id'];?>" name="beneficiario" value="<?php echo $l['id'];?>"/>
            <input type="button" name="beneficiario" value="Quitar" class="quitar" id="<?php echo $l['id'];?>" />            
            </td>
        </tr>
        <?php endforeach; */?>      
    </tbody>
    </table>   
    -->     
    </div>
    <?php } ?>