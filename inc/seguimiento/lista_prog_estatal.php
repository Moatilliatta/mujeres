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

include_once($_SESSION['model_path'].'prog_estatal_mujeres.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

//echo $id_mujeres_avanzando;
//exit;

if($id_mujeres_avanzando != null){
//Buscamos Servicios
$msg_no = ProgEstatalMujeres::searchprogEstatal($id_mujeres_avanzando);
//Obtenemos listado y paginador
list($lista,$p) = ProgEstatalMujeres::listaProgEstatales($id_mujeres_avanzando);

//print_R($lista);
//exit;

}

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
     Programas Estatales
     </div>
     
     <p>
         <?php 
     // Listado de páginas del paginador
        echo $p->display();
     ?>
     </p>     

    <table class="tablesorter">
    <thead>
        <th>Programa</th>
        
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['NOMBRE']; ?></td>
                  
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table> 

     <?php }else{ ?>

    <div class="mensaje">
      No tiene programas estatales asignados
    </div>

<?php } ?>