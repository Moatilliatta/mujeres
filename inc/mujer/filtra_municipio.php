<?php

session_start();//Habilitamos uso de variables de sesión



if(isset($_POST['CVE_EDO_RES'])){

    

    //Obtenemos conexión

    include ($_SESSION['inc_path'] . "conecta.php");

    

    //Obtenemos los municipios

    $CVE_EDO_RES = $_POST["CVE_EDO_RES"];    

    $sql = 'SELECT CVE_MUN, NOM_MUN FROM `cat_municipio` where CVE_ENT = ? ORDER BY NOM_MUN ASC' ;

    

    $params = array($CVE_EDO_RES);

    

    $municipio= $db->rawQuery($sql,$params);

       

}else{

    exit;

}

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>

<select id="id_cat_municipio" class="combobox" name="id_cat_municipio">

    <option value=''>Seleccione Municipio</option>

    <?php foreach($municipio as $l): 



        if($l['CVE_MUN'] == $beneficiario['id_cat_municipio']){

            $selected = "selected";

        }else{

            $selected = "";

        }

    ?>                

    <option value='<?php echo $l['CVE_MUN'] ?>' <?php echo $selected;?> > <?php echo $l['NOM_MUN'];?></option>

    <?php endforeach; ?>

</select>                