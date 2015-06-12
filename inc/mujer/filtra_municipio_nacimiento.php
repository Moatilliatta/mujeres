<?php

session_start();//Habilitamos uso de variables de sesión



if(isset($_POST['id_cat_estado'])){

    

    //Obtenemos conexión

    include ($_SESSION['inc_path'] . "conecta.php");

    

    //Obtenemos los municipios

    $id_cat_estado = $_POST["id_cat_estado"];    

    $sql = 'SELECT CVE_MUN, NOM_MUN FROM `cat_municipio` where CVE_ENT = ? ORDER BY NOM_MUN ASC';

    

    $params = array($id_cat_estado);

    

    

    //Si el país no es México, sólo ponemos la opción de OTRO

    /*if($id_cat_estado == 33){

        $sql .= ' AND CVE_ENT_MUN = ?';

        $params[] = 32059;

    }

    */

     $municipio_nacimiento = $db->rawQuery($sql,$params);

    // echo $sql;

    

}else{

    exit;

}

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>


<select class="combobox" id="id_municipio_nacimiento" name="id_municipio_nacimiento">

    <option value=''>Seleccione Municipio Nacimiento</option>

    <?php foreach($municipio_nacimiento as $l): 



        if($l['CVE_MUN'] == $beneficiario['id_municipio_nacimiento']){

            $selected = "selected";

        }else{

            $selected = "";

        }

    ?>                

    <option value='<?php echo $l['CVE_MUN'] ?>' <?php echo $selected;?> > <?php echo $l['NOM_MUN'];?></option>

    <?php endforeach; ?>

</select>      