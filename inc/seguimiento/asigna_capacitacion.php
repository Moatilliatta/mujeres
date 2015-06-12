<?php
//Modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_punto_rosa_capacitacion.php');
include_once($_SESSION['model_path'].'seg_punto_rosa.php');
include_once($_SESSION['model_path'].'seg_capacitacion_mujer.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Obtenemos ID de mujer
$id_mujeres_avanzando = intval($_GET['id_edicion']);

//Punto Rosa
$id_punto_rosa = NULL;

//Arreglos
$mujer_capacitacion = array();
$lista_fechas_capacitacion = array();

//Si tenemos el ID de una mujer
if($id_mujeres_avanzando){
    
    //Obtenemos datos de la beneficiaria
    $mujeres_avanzando = mujeresAvanzando::get_by_id($id_mujeres_avanzando);

    //Obtenemos capacitaciones y fechas ligadas ala mujer
    $mujer_capacitacion = SegCapacitacionMujer::listadoCapacitacionMujer($id_mujeres_avanzando);
    $lista_fechas_capacitacion = SegCapacitacionMujer::listadoFechasCapacitacion($id_mujeres_avanzando);  
    
    //Obtenemos los puntos rosa que tiene una beneficiaria
    $punto_rosa = SegPuntoRosa::get_by_id_caravana($mujeres_avanzando['id_caravana']);
      
}
//Obtemos datos de tabla capacitación
$lista = Seg_punto_rosa_capacitacion::listaSegPuntoRosaCap($punto_rosa['id']);

?>
<div class="mensaje">
    Seguimiento De Capacitaciones En Punto Rosa <?php echo '"'.$punto_rosa['descripcion'].'"'; ?>
</div> 

<?php if($lista != NULL){?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
    $("table").tablesorter({widgets: ['zebra']});
    });
    </script>
<div align='center'>
<form id='formcapacitacion' method="post" action='save_mujer_cap.php'>    
<input type="hidden" name="tipo" value="seguimiento">
<table class="tablesorter">
    <thead>
        <th>N&uacute;mero</th>
        <th>Asistio</th>
        <th>Capacitaci&oacute;n</th>
        <th>Fecha Capacitaci&oacute;n</th>
    </thead>
    
    <tbody>
        <?php
        //$lista es seg_punto_ros_capacitacion
        //$mujer_capacitacion seg_capacitacion_mujer  
        foreach($lista as $key => $l): ?>
        <tr>
            <td><?php echo $l['id'];?></td>
            <td>
              <div>                    
                <input id="id_seg_punto_rosa_capacitacion" 
                <?php if($mujer_capacitacion != NULL) 
                echo (in_array($l['id'],$mujer_capacitacion) === true )? 'checked': ''; ?> 
                class='val_check_1' 
                value="<?php echo $l['id']?>" 
                name="id_seg_punto_rosa_capacitacion[]" 
                type="checkbox" />
                </div>
            </td>
            <td><?php echo $l['capacitacion']; ?></td>
            <td>
            <input type="hidden" name="id_mujeres_avanzando" value="<?php echo $id_mujeres_avanzando; ?>" />  
            <input type = 'text' 
            		id = 'fecha_capacitacion<?php echo $l['id']; ?>' 
            		class="fecha date" required
            		<?php echo (in_array($l['id'],$mujer_capacitacion) === true )? '': 'disabled'; ?>
            		name = 'fecha_capacitacion[]'
            		value="<?php echo $lista_fechas_capacitacion[$l['id']];?>" />
                    
            <input type="button"  value="Hoy" <?php echo (in_array($l['id'],$lista_fechas_capacitacion) === true)? '': 'disabled';?>
             id="<?php echo $l['id']; ?>" class="btnToday1"/>
            </td>
        </tr>

        <?php endforeach; ?> 
        
             
       
    </tbody>
    
    </table>
    <div>
			<td>&nbsp;</td>
			<td><input type = 'submit'  id = 'enviar'value = 'Enviar' /></td>
</div>		
</form> 
</div>   

<?php }else{ ?>
<div class="mensaje">
    No hay capacitaciones disponibles
</div> 
<?php } ?>