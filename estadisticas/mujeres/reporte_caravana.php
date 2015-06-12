<?php
session_start();//Habilitamos uso de variables de sesión

//Si requerimos obtener totales por fecha
$fecha_creacion_ini = ($_GET['fecha_creacion_ini'] != NULL)? $_GET['fecha_creacion_ini']: NULL;
$fecha_creacion_fin = ($_GET['fecha_creacion_fin'] != NULL)? $_GET['fecha_creacion_fin']: NULL;
$excel = ($_POST['excel'] != NULL)? $_POST['excel']: NULL;

//Imprimimos o no cabecera de excel
if($excel != NULL){
    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");
    //Librería de permisos
    include($_SESSION['inc_path'].'libs/Permiso.php'); 

    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=Datos_por_caravana.xls");
}else{    
    //Incluimos cabecera
    include('../../inc/header.php');    
}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos totales por caravana
$lista = mujeresAvanzando::repCarGrado($fecha_creacion_ini,$fecha_creacion_fin);
$tot_car = mujeresAvanzando::totalRepCarGrado($fecha_creacion_ini,$fecha_creacion_fin);

if($excel == NULL){ ?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<?php } ?>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">Reporte de encuestas por caravana</h2>
    
    <div class="centro">              
    Este reporte indica a cu&aacute;ntas personas se les ha realizado la encuesta ENHINA 
    en el programa Mujeres Avanzando por caravana. La informaci&oacute;n contenida en este 
    reporte se obtiene a partir de los reportes importados en archivos de Excel desde el sistema SIIA.
    <div>   

    <?php if($excel == NULL){ ?>
    <form method="POST" id="exportar" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <div class="centro">
        <input type="hidden" id="excel" name="excel" value="1" />
        <input type="submit" value ="Exportar a Excel"/>
    </div>        
    </form> 

    <div id="spinner" class='mensaje_carga'>Generando reporte. Por favor, sea paciente.<img src="<?php echo $_SESSION['css_path'] ?>/img/loader_sug.gif"></div>

    <?php } ?>

    <table id="totales_caravana" class="tablesorter">
    <thead>
        <th>Caravana</th>
        <th>Fecha</th>
        <th>Segura</th>
        <th>Leve</th>
        <th>Moderada</th>
        <th>Severa</th>
        <th>Total</th>        
    </thead>

    <tbody>
    
    <?php foreach ($lista as $c):?>
        <tr>
            <td><?php echo ($excel == 1)? utf8_decode($c['caravana']) : $c['caravana']; ?></td>
            <td><?php echo $c['fecha_instalacion']; ?></td>
            <td><?php echo $c['segura'];?></td>
            <td><?php echo $c['leve'];?></td>
            <td><?php echo $c['moderada'];?></td>
            <td><?php echo $c['severa'];?></td>
            <td><?php echo $c['total'];?></td>            
        </tr>
    <?php endforeach;?>   

        <tr>
            <th colspan="2">Totales</th>
            <td><?php echo $tot_car['segura']; ?></td>
            <td><?php echo $tot_car['leve'];?></td>
            <td><?php echo $tot_car['moderada'];?></td>
            <td><?php echo $tot_car['severa'];?></td>
            <td><?php echo $tot_car['total'];?></td>
        </tr>     
    </tbody>
    </table>    

<?php if($excel == NULL){ ?>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <table>
        <tr>
            <th>Consultar desde Fecha Creaci&oacute;n</th>            
        </tr>
        <tr>
            <td>
                <label>Desde</label> 
                <input type="text" id="from" name="fecha_creacion_ini" value="<?php echo $fecha_creacion_ini; ?>" />
            </td>
            <td>
                <label>Hasta</label> 
                <input type="text" id="to" name="fecha_creacion_fin" value="<?php echo $fecha_creacion_fin; ?>" />
            </td>
            <td>&nbsp;</td>
            <td><input type="submit" value ="Filtrar"/></td>
        </tr>
        </table>
    </form>     

    </div>
 </div>
</div>
 
<?php 
    //Incluimos pie
    include($_SESSION['inc_path'].'/footer.php');
}
?>