<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'registro_excel_enhina.php');


//municipios
/*$sql='SELECT CVE_MUN, NOM_MUN FROM cat_municipio WHERE CVE_ENT = 14';
$municipio = $db->query($sql);

//localidad
$sql = 'SELECT CVE_LOC, NOM_LOC FROM `cat_localidad` where CVE_ENT = 14';    
$localidad = $db->query($sql);     */

//Variable de respuesta
$respuesta = (isset($_GET['r']))? intval($_GET['r']) : null;

//Mensaje respuesta
list($mensaje,$class) = Permiso::mensajeRespuesta($respuesta);

//print_r($_GET);
//Listamos los programas del beneficiario
list($lista,$p) = Registro_excel::listaRegistroexcel();                                              

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">Archivos Excel Enhina</h2>

    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div class="centro">       
       

    <div>
   <?php
   
    //Si tenemos listado
    if($lista != NULL){  
    echo $p->display();
    ?>
    </p>

    <table class="tablesorter">
    <thead>
        <th>Nombre de Archivo</th>
        <th>Total Encuestados</th>
        <th>Total Encuestas Completas</th>
        <th>Total Encuestas Incompletas</th>
        <th>Total de Personas Registradas</th>
        <th>Total Del Programa Mac</th>
        <th>Total Del Programa Map</th>
        <th>Total Registrados</th>
        <th>Total Duplicados</th>
        <th>Total No Coinciden</th>
        <th>Fecha Subido</th>
        <th>Usuario</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre']; ?></td>
            <td><?php echo $l['total_encuestados']; ?></td>
            <td><?php echo $l['total_enc_completo'];?></td>
            <td><?php echo $l['total_enc_inc'];?></td>
            <td><?php echo $l['total_familiares'];?></td>
            <td><?php echo $l['total_prog_mac'];?></td>
            <td><?php echo $l['total_prog_map'];?></td>
            <td><?php echo $l['total_registrados'];?></td>
            <td><?php echo $l['total_duplicados'];?></td>
            <td><?php echo $l['total_no_coinciden'];?></td>
            <td><?php echo $l['fecha_subido'];?></td>
            <td><?php echo $l['usuario'];?></td>
            
        </tr>

        <?php endforeach; ?>

    </tbody>
    </table>
   
    </div>
 <?php }else{ ?>
  <div class="mensaje">
   No Hay Archivos Subidos Con Anterioridad 
  </div>
 <?php } ?>
  </div>
 </div>

</div>
 
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>