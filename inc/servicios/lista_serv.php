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
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//Obtenemos id del beneficiario, en caso de no tenerlo previamente
if($id_mujeres_avanzando == NULL){
    $id_mujeres_avanzando=$_REQUEST['id_mujeres_avanzando'];
}

//Obtenemos listado y paginador
list($lista_pys,$p) = mujeresAvanzandoDetalle::listaPagServMujer($id_mujeres_avanzando);
?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

    <?php
    //Si tenemos listado
    if($lista_pys != NULL){                

        // Listado de páginas del paginador
        echo $p->display();
    ?>
    </p>

    <table class="tablesorter">
    <thead>
        <th>Programa</th>
        <th>Servicio</th>
        <th>Grado</th>
        <th>Fecha Alta</th>
        <th>Estado Actual</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>
        <?php foreach($lista_pys as $l): ?>
        <tr>
            <td><?php echo $l['nombre_programa']; ?></td>
            <td><?php echo $l['nombre_servicio']; ?></td>
             <td><?php echo $l['grado']; ?></td>
            <td><?php echo Fechas::fechalarga($l['fecha_alta']); ?></td>
            <td><?php echo ($l['activo'] == 1)? 'Activo' : 'Baja'; ?></td>
            <td>
            <?php  
            //Verificamos si tiene permiso de activar/desactivar servicio
            if(Permiso::accesoAccion('activa_mujer_serv', 'serv', 'servicios')){ ?>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">                
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> servicio?" 
                       href="activa_mujer_serv.php?ID_MUJERES_AVANZANDO_DETALLE=<?php echo $l['id']; ?>"></a>
                </div>

                <?php }else{
                    //echo 'NO';
                    }  ?>
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table> 

     <?php }else{ ?>

    <div class="mensaje">
      No tiene servicios asignados
    </div>

<?php } ?>