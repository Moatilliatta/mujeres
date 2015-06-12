<?php
session_start();//Habilitamos uso de variables de sesión

//Obtenemos id_callcenter_filtro
$id_callcenter_filtro = ($_POST['id_callcenter_filtro'] > 0 )? 
                                $_POST['id_callcenter_filtro']: null;

//Verificamos que sí sea la opción 3 de seg_capacitación
if($id_callcenter_filtro == 3){
    
    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");    
    
    $seg_capacitacion = $db->get("seg_capacitacion",null,"id, nombre");
       
}else{
    exit;
}
?>

<select class="combobox" id="id_seg_capacitacion" name="id_seg_capacitacion">
    <option value=''>Seleccione Capacitaci&oacute;n</option>
    <?php foreach($seg_capacitacion as $l): ?>
    <option value='<?php echo $l['id'] ?>'> 
        <?php echo $l['nombre'];?></option>
    <?php endforeach; ?>
</select>   