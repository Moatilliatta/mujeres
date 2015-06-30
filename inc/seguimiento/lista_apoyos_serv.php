<?php
session_start();  

/* Al utilizar ajax y refrescar este formulario
necesitaremos obtener tanto el objeto $db (instancia de conexión)
como el id del beneficiario. Verificamos que, si no están creadas
las variables, las obtendremos*/
if(!isset($db)){

  //Incluimos librería de permiso
  include ($_SESSION['inc_path'] . "conecta.php");

}

//Eliminamos variable de sesión de carrito
//unset($_SESSION['arrayArt']);

//Incluimos librerías
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');

//Cargamos modelo de c_mujeres_avanzando_detalle
include_once($_SESSION['model_path'].'seg_apoyos_serv.php');

//Obtenemos id del beneficiario, en caso de no tenerlo previamente
if($id_mujeres_avanzando == NULL){
    $id_mujeres_avanzando=$_REQUEST['id_mujeres_avanzando'];
}

//Obtenemos listado y paginador
list($lista,$p) = SegApoyosServ::listaApoyoServ(null,$id_mujeres_avanzando);
?>

<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

    <?php
    //Si tenemos listado
    if($lista != NULL){                ?>

     <div class="mensaje">
     Servicios y Apoyos Entregados
     </div>

     <p>
    <?php 
        // Listado de páginas del paginador
        echo $p->display();?>
     </p>     

    <table class="tablesorter">
    <thead>
        <th>Nombre del Apoyo</th>
        <th>Secretar&iacute;a</th>
        <th>Entregado Por</th>
        <th>Fecha</th>
        <!--<th>Estatus</th>
        <th>Acci&oacute;n</th> -->
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre_apoyo']; ?></td>
            <td><?php echo $l['nombre_sec']; ?></td>
            <td><?php echo $l['usuario_ultima_mod']; ?></td>
            <td><?php echo Fechas::fechalarga($l['fecha_mod']).' '.substr($l['fecha_mod'], -8).' Hrs.'; ?></td>
            <!--
            <td><?php //echo ($l['activo'] == 1)? 'Activo' : 'Baja'; ?></td>
            <td>
            <?php  
            //Verificamos si tiene permiso de activar/desactivar servicio
            /*
            if(Permiso::accesoAccion('activa_mujer_serv', 'serv', 'servicios')){ ?>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">                
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> servicio?" 
                       href="activa_mujer_serv.php?ID_MUJERES_AVANZANDO_DETALLE=<?php echo $l['id']; ?>"></a>
                </div>

                <?php }else{
                    //echo 'NO';
                    }  */?>
            </td>
            -->
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table> 

     <?php }else{ ?>

    <div class="mensaje">
      No tiene servicios asignados
    </div>

<?php } ?>