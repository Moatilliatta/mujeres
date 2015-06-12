<?php
session_start();//Habilitamos uso de variables de sesión
include($_SESSION['inc_path']."libs/Fechas.php");
//Obtenemos la caravana
$id_caravana = (isset($_REQUEST['id_caravana']))? $_REQUEST['id_caravana']: NULL;
$fecha_caravana = (isset($_REQUEST['fecha_caravana']))? $_REQUEST['fecha_caravana']: NULL;
$excel = ($_POST['excel'] != NULL)? $_POST['excel']: NULL;

//Imprimimos o no cabecera de excel
if($excel != NULL){
    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");
    //Librería de permisos
    include($_SESSION['inc_path'].'libs/Permiso.php'); 

    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=reporte_cartillas_entregadas.xls");
}else{    
    //Incluimos cabecera
    include('../../inc/header.php');    
}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos caravanas disponibles
//$db->where ('activo', 1);
$caravanas = $db->get('caravana');
$C = NULL;
if($id_caravana){
    $C = $db->where('id', $id_caravana)
                      ->getOne('caravana');
}

if($excel == NULL){ 

//Obtenemos totales por caravana (paginado)
list($lista,$p) = mujeresAvanzando::repCartillasEntrPag($id_caravana,$fecha_caravana);

}else{

//Obtenemos totales por caravana
$lista = mujeresAvanzando::repCartillasEntr($id_caravana,$fecha_caravana);
//print_R($lista);
//exit;
}


if($excel == NULL){ ?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
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
    <h2 class="centro">Reporte de Cartillas Entregadas <?php 
    if($id_caravana){
        echo ($excel == 1)? "'".utf8_decode($C['descripcion'])."'" : "'".$C['descripcion']."'"; 
    }    

    if($fecha_caravana){
        echo ' del d&iacute;a '.Fechas::fechalarga($fecha_caravana);
    }
    ?>
    </h2>

    <?php if($excel == NULL){ ?>

    <div class="centro">
    Este reporte indica el n&uacute;mero de personas a las que se les ha entregado su cartilla del programa 
    Mujeres Avanzando por caravana. La informaci&oacute;n contenida en este reporte es generada a partir de 
    los beneficiarios a quienes se les tom&oacute; fotograf&iacute;a en los eventos de entrega de cartillas.              
    <div>   
    
    <div class="centro">
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <table>
        <tr>
            <th>Seleccione Caravana</th>            
        </tr>
        <tr>
            <td>
                <label>Caravana</label>                    
            </td>            
            <td>
                <select id="id_caravana" name="id_caravana">
                    <option value="">Seleccione Caravana</option>
                    <?php foreach($caravanas as $c): 
                    $selected = ($c['id'] == $id_caravana )? 'selected' : ''; 
                    ?>
                        <option value='<?php echo $c['id'] ?>'  <?php echo $selected;?> > 
                            <?php echo $c['descripcion'];?>
                        </option>
                    <?php endforeach; ?>                       
                </select>                           
            </td>
        </tr>
        <tr>
            <td>
                <label>Fecha de Caravana</label>            
            </td>
            <td>
                <input type="text" id='fecha_caravana' name='fecha_caravana' class='fecha' value="<?php echo $fecha_caravana; ?>" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input type="submit" value ="Filtrar"/></td>
        </tr>
        </table>
    </form>     
    </div>    

    <div class="centro">        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <div>
            <input type="hidden" id="excel" name="excel" value="1" />
            <input type="hidden" id="id_caravana" name="id_caravana" value="<?php echo $id_caravana;?>">
            <input type="hidden" id="fecha_caravana" name="fecha_caravana" value="<?php echo $fecha_caravana;?>">
            <input type="submit" value ="Exportar a Excel"/>
        </div>        
        </form> 
    </div>
    <?php } ?>


    <?php if($lista != NULL){ ?>

    <?php
    //Si tenemos listado
    if(isset($p) && $excel == NULL){ ?>
    <p>
        <?php
        // Listado de páginas del paginador
        echo $p->display();
        ?>
    </p>  
    <?php } ?>

    <table id="totales_caravana" class="tablesorter">
    <thead>
        <th>Folio</th>
        <!-- <th>ID Entrevista</th> -->
        <!-- <th>ID Grado</th> -->
        <th>Grado</th>
        <th>Fecha Foto</th>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>        
        <!-- <th>Fecha de Nacimiento</th> -->
        <th>Tel&eacute;fono</th>
        <th>Fecha de Entrega</th>
        <th>Domicilio</th>        
    </thead>

    <tbody>
    
    <?php foreach ($lista as $c):
    $fechas = Fechas::extraeymd($c['fecha_foto']);
    $fecha_1 = Fechas::fechaymdAdmy($fechas); 
    //$fechas = Fechas::extraeymd($c['fecha_foto']);
    ?>
          
        <tr> 
            <td><?php echo $c['folio']; ?></td>
            <!-- <td><?php //echo $c['id_entrevista'];?></td> -->
            <!-- <td><?php //echo $c['id_grado'];?></td> -->
            <td><?php echo $c['grado'];?></td>
            <td><?php echo Fechas::fechacorta("/",$fecha_1);?></td>
            <td><?php echo ($excel == 1)? utf8_decode($c['nombres']) : $c['nombres'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($c['paterno']) : $c['paterno'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($c['materno']) : $c['materno'];?></td>
            <!-- <td><?php //echo $c['fecha_nacimiento'];?></td> -->
            <td><?php echo $c['telefono'];?></td>
            <td><?php echo Fechas::fechacorta("/",$fecha_1);?></td>
            <td><?php 
            $domicilio = 'CALLE: '.$c['calle'].' No. '.$c['num_ext'].' ';
            $domicilio .= ($c['num_int'])? 'INTERIOR: '.$c['num_int'].' ' : ' ';
            $domicilio .= 'COLONIA: '.$c['colonia'].' CP: '.$c['CODIGO'].' '.$c['municipio'];
            echo ($excel == 1)? utf8_decode($domicilio) : $domicilio;?></td>            
        </tr>
    <?php endforeach;?>   
    </tbody>
    </table>
    <?php }else{ ?>
    <div class="mensaje">No se encontraron registros</div>
    <?php } ?>    

    </div>
 </div>
</div>
<?php if($excel == NULL){ 

    //Incluimos pie
    include($_SESSION['inc_path'].'/footer.php');
}
?>