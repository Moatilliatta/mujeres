<?php
session_start();  

/* Al utilizar ajax y refrescar este formulario
necesitaremos obtener el objeto $db (instancia de conexión). 
Verificamos que, si no están creadas
las variables, las obtendremos*/
if(!isset($db)){

  //Incluimos librerías
  include ($_SESSION['inc_path'] . "conecta.php");
  include ($_SESSION['inc_path'] . "libs/Permiso.php");

}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'callcenter_h.php');

//Valores de la búsqueda
$nom_comp = $_GET['nom_comp'];
$lista = array();
$p = null;

if($id_callcenter != null){
    //Listamos los programas del beneficiario
    list($lista,$p) = CallCenterH::listaCallCenterH($id_callcenter,$id_mujeres_avanzando);  
}

?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<p>
    
    <?php
    //Si tenemos listado
    if($lista != NULL){                
        // Listado de páginas del paginador
        echo $p->display();
    ?>
    </p>
    
    <table class="tablesorter">
    <thead>
        <tr>
            <th colspan="3">Historial de Estatus de Llamada</th>
        </tr>
        <tr>
            <th>Estatus Llamada</th>
            <th>Fecha</th>
            <th>Usuario</th>
        </tr>        
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['estatus'];?></td>
            <td><?php echo $l['fecha_ultima_mod']; ?></td>
            <td><?php echo $l['usuario']; ?></td>
        </tr>

        <?php endforeach; ?>      
       
    </tbody>
    
    </table>

    <?php }else{ ?>

    <div class="mensaje">Mujer sin historial de estatus de llamada.</div>

    <?php } ?>
   