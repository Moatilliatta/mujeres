<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['CVE_ENT_MUN'])){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Obtenemos las localidades
    $id_municipio = $_POST["CVE_ENT_MUN"];

    $sql =
    'SELECT
     ct.CVE_EST_MUN_LOC,
     CONCAT(ct.CVE_EST_MUN_LOC,?,loc.NOM_LOC) as localidades  
     FROM `cat_localidad` ct
     left join cat_localidad loc on ct.CVE_EST_MUN_LOC = loc.CVE_EST_MUN_LOC
     where ct.CVE_ENT_MUN = ? '; 

     $params = array(' - ',$id_municipio);
     $localidad = $db->rawQuery($sql,$params);

}else{

    exit;
}
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>
<select id="CVE_EST_MUN_LOC" class="combobox" name="CVE_EST_MUN_LOC">
    <option value=''>Seleccione localidad</option>
    <?php foreach($localidad as $l): 
        $selected = ($l['CVE_EST_MUN_LOC'] == $centro_atencion['CVE_EST_MUN_LOC'])? "selected" : "";        
    ?>                
    <option value='<?php echo $l['CVE_EST_MUN_LOC'] ?>' <?php echo $selected;?> > 
        <?php echo $l['localidades'];?>
    </option>
    <?php endforeach; ?>
</select>      