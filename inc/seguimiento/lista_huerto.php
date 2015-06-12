<?php
if(!isset($db)){

  //Incluimos librería de permiso
  include ($_SESSION['inc_path'] . "conecta.php");

}
//Modelos a usar
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_punto_rosa_capacitacion.php');
include_once($_SESSION['model_path'].'seg_capacitacion_mujer.php');

//Obtenemos id del beneficiario, en caso de no tenerlo previamente
if($id_mujeres_avanzando == NULL){
    $id_mujeres_avanzando=$_REQUEST['id_mujeres_avanzando'];
}

//Obtemos las capacitaciones(platicas) ligadas a la mujer
list($lista,$p) = SegCapacitacionMujer::listaCapacitacionMujer($id_mujeres_avanzando,null,'huerto');

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
    $("table").tablesorter({widgets: ['zebra']});
    });
    </script>
<?php if($lista !=null ){ ?>
<div class="mensaje"> Huertos Otorgados </div>
<?php } ?>

<?php if($lista !=null ){ ?> 

  <p>
    <?php 
        // Listado de páginas del paginador
        echo $p->display();?>
     </p>  

<table class="tablesorter">
    <thead>
       
        <th>Comunidad (Caravana)</th>
        <th>Capacitaci&oacute;n</th>
        <th>Fecha</th>
    </thead>
    
    <tbody>
        <?php
        foreach($lista as $key => $l): ?>
        <tr>
            
            <td><?php echo $l['punto_rosa'];?></td>
            <td><?php echo $l['capacitacion'];?></td>
            <td><?php echo $l['fecha_capacitacion'];?></td>
        </tr>

        <?php endforeach; ?> 
        
    </tbody>
    
    </table>
    <?php }else{ ?>
    <div class="mensaje">
        No tiene Talleres de Huerto Asignados
    </div>
        
   <?php } ?>