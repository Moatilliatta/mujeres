<?php

session_start();//Habilitamos uso de variables de sesión



if(isset($_POST['id_cat_municipio'])){

    

    //Obtenemos conexión

    include ($_SESSION['inc_path'] . "conecta.php");

    

    //Obtenemos el municipio

    $CVE_EDO_RES = $_POST["CVE_EDO_RES"];

    

    //Obtenemos las localidades

    $id_cat_municipio = $_POST["id_cat_municipio"];

    $sql = 'SELECT CVE_LOC, NOM_LOC FROM `cat_localidad` where cve_ent_mun = ? ORDER BY NOM_LOC ASC';

    

    $params = array($CVE_EDO_RES.$id_cat_municipio);    

    $localidad = $db->rawQuery($sql,$params);

       

}else{

    exit;

}

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>

<select id="id_cat_localidad" class="combobox" name="id_cat_localidad">

    <option value=''>Seleccione localidad</option>

    <?php foreach($localidad as $l): 



        if($l['CVE_LOC'] == $beneficiario['id_cat_localidad']){

            $selected = "selected";

        }else{

            $selected = "";

        }

    ?>                

    <option value='<?php echo $l['CVE_LOC'] ?>' <?php echo $selected;?> > <?php echo $l['NOM_LOC'];?></option>

    <?php endforeach; ?>

</select>                