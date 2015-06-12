<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['CVE_ENT_MUN_LOC']) && $_POST['CVE_ENT_MUN_LOC'] != '' ){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Obtenemos municipio
    $CVE_ENT_MUN_LOC = $_POST['CVE_ENT_MUN_LOC'];    

    //Obtenemos las comunidades
    $sql = 'SELECT id, nombre 
            FROM `centros_atencion` 
            WHERE CVE_EST_MUN_LOC = ? ';

    $params = array($CVE_ENT_MUN_LOC);

    $centros = $db->rawQuery($sql,$params);

}else{

    exit;
}
?>
<td>
    <label>Centro de Atenci&oacute;n</label>
</td>
<td>
    <select id="id_centro_atencion" name="id_centro_atencion">
    <option value=''>Seleccione Centro de Atenci&oacute;n</option>
    <?php foreach($centros as $l): 
    
        /*
        if($l['id_centros_atencion'] == $centro_atencion['id_centros_atencion']){

            $selected = "selected";

        }else{

            $selected = "";

        }*/

    ?>                
    <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > 
        <?php echo $l['nombre'];?>
    </option>
    <?php endforeach; ?>
    </select>      
</td>