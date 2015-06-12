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

//Cargamos modelo de seg_actividad_com
include_once($_SESSION['model_path'].'seg_activacion_com.php');

//Obtenemos id del beneficiario, en caso de no tenerlo previamente
if($id_mujeres_avanzando == NULL){
    $id_mujeres_avanzando=$_REQUEST['id_mujeres_avanzando'];
}

//Obtenemos listado y paginador
list($lista,$p) = SegActivacionCom::listaActivacionCom(null,$id_mujeres_avanzando);
?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

    <?php
    //Si tenemos listado
    if($lista != NULL){                ?>
    
     <div class="mensaje">
     Activaci&oacute;n Comunitaria
     </div>
     
     <p>
         <?php 
     // Listado de páginas del paginador
        echo $p->display();
     ?>
     </p>     

    <table class="tablesorter">
    <thead>
        <th>Nombre de la Actividad</th>
        <th>Colonia</th>
        <th>Observaciones</th>
        <th>Entregado Por</th>
        <th>Fecha</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre_actividad']; ?></td>
            <td><?php echo $l['nombre_colonia']; ?></td>
            <td><?php echo $l['observaciones']; ?></td>
            <td><?php echo $l['usuario_ultima_mod']; ?></td>
            <td><?php echo Fechas::fechalarga($l['fecha_mod']).' '.substr($l['fecha_mod'], -8).' Hrs.'; ?></td>           
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table> 

     <?php }else{ ?>

    <div class="mensaje">
      No tiene actividades asignadas
    </div>

<?php } ?>