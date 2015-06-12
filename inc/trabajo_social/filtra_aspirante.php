<?php 
session_start();//Habilitamos uso de variables de sesión
//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
include_once($_SESSION['model_path'].'aspirantes.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

if(isset($_POST['paterno']) && isset($_POST['nombres'])){
    
    //Obtenemos variables
    $nombres = $_POST['nombres'];
    $paterno = $_POST['paterno'];
    $materno = $_POST['materno'];
    $mensaje = NULL;
   
    $lista = Aspirantes::verificaDuplicado($nombres,$paterno,$materno);     
    
//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL && count($_POST) > 1){
    //No existen registros con el criterio de búsqueda
    list($mensaje,$clase) = Permiso::mensajeRespuesta(8);
}elseif ($lista == NULL) {
    //No hay registros para mostrar
    list($mensaje,$clase) = Permiso::mensajeRespuesta(17);
}
    
    //Obtenemos acciones del menú
$central = Permiso::arregloMenu('edita_trabajo_social ','center');
       
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
            </div>
            <div class="resultado_opcion">                
                <?php if(array_key_exists('edita_trabajo_social',$central)){ ?>                
                <div title="Ver Aspirante" class="ui-state-default ui-corner-all lista">                
                    <label>
                        <a class="botonEnlace" href="edita_trabajo_social.php?id_edicion=<?php echo $l['id_expediente'];?>&id_aspirante=<?php echo $l['id_aspirante'];?>">
                        &Eacute;sta es la persona que busco
                        </a>
                    </label>
                </div>                                        
                <?php } ?>
            </div>        
        <?php endforeach;?>   
    </div>
<!--
    <p>
     <label>
        Posibles Coincidencias de Aspirante
    </label> 
    </p>
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Estado</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>
        <?php /* foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombres']; ?></td>
            <td><?php echo $l['paterno']; ?></td>
            <td><?php echo $l['materno'];?></td>
            <td><?php echo $l['estado'];?></td>
            <td><?php echo $l['municipio'];?></td>
            <td><?php echo $l['localidad'];?></td>
            <td>
             <?php// if($l['activo']==1){ ?>
                <?php if(array_key_exists('edita_trabajo_social',$central)){ ?>                
                <div title="Ver Aspirante" class="ui-state-default ui-corner-all lista">                
                    <a class="ui-icon ui-icon-note" 
                    href="edita_trabajo_social.php?id_edicion=<?php echo $l['id_expediente'];?>&id_aspirante=<?php echo $l['id_aspirante'];?>"></a>
                </div>                                        
                <?php } ?>
             <?php // }?> 
            </td>
        </tr>
        <?php endforeach; */?>              
    </tbody>
    </table> 
    -->           
    </div>
    <?php } ?>