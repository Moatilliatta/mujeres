<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['CVE_TIPO_VIAL'])){
    
    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Obtenemos vialidades
    $CVE_TIPO_VIAL = $_POST["CVE_TIPO_VIAL"];
    $CVE_EST_MUN_LOC = $_POST["CVE_EST_MUN_LOC"];
       
    $sql = 'SELECT CVE_VIA, NOM_VIA FROM `vialidad` where CVE_TIPO_VIAL = ? AND CVE_EST_MUN_LOC = ? GROUP BY NOM_VIA ORDER BY NOM_VIA';
    
    $params = array($CVE_TIPO_VIAL,$CVE_EST_MUN_LOC);
    $tipo_via = $db->rawQuery($sql,$params);
       
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<select id="CVE_VIA" class="combobox datos_aspirante oculta_select" name="CVE_VIA">
    <option value=''>Seleccione Vialidad</option>
    <?php foreach($tipo_via as $l): 

        if($l['CVE_VIA'] == $aspirantes['CVE_VIA']){
            $selected = "selected";
        }else{
            $selected = "";
        }
    ?>                
    <option value='<?php echo $l['CVE_VIA'] ?>' <?php echo $selected;?> > <?php echo $l['NOM_VIA'];?></option>
    <?php endforeach; ?>
</select>             