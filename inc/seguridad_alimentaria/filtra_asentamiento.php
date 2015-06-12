<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['id_cat_municipio'])){
    
    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Obtenemos el estado
    $CVE_EDO_RES = $_POST["CVE_EDO_RES"];
    
    //Obtenemos el municipio
    $id_cat_municipio = $_POST["id_cat_municipio"];
    
    //Obtenemos la localidad
    $id_cat_localidad = $_POST["id_cat_localidad"];
    
    $sql = '
            SELECT
            a.CVE_ASEN, 
            concat(c.NOMBRE,?,a.NOM_ASEN) as nombre_asentamiento
            FROM `asentamiento` a
            left join cat_tipo_asen c on c.CVE_TIPO_ASEN = a.CVE_TIPO_ASEN
            where a.CVE_EST_MUN_LOC = ?';
    
    $params = array(' ',$CVE_EDO_RES.$id_cat_municipio.$id_cat_localidad);
    
    $localidad = $db->rawQuery($sql,$params);
       
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>
<select id="CVE_ASEN" name="CVE_ASEN">
    <option value=''>Seleccione Asentamiento</option>
    <?php foreach($localidad as $l): 

        if($l['CVE_ASEN'] == $beneficiario['CVE_ASEN']){
            $selected = "selected";
        }else{
            $selected = "";
        }
    ?>                
    <option value='<?php echo $l['CVE_ASEN'] ?>' <?php echo $selected;?> > <?php echo $l['nombre_asentamiento'];?></option>
    <?php endforeach; ?>
</select>                