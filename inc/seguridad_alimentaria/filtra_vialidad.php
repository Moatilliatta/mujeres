<?php

session_start();//Habilitamos uso de variables de sesión



if(isset($_POST['CVE_EDO_RES']) && isset($_POST['id_cat_municipio']) && isset($_POST['id_cat_localidad'])){

    

    //Obtenemos conexión

    include ($_SESSION['inc_path'] . "conecta.php");

    

    //Obtenemos el estado

    $CVE_EDO_RES = $_POST["CVE_EDO_RES"];

    

    //Obtenemos el municipio

    $id_cat_municipio = $_POST["id_cat_municipio"];

    

    //Obtenemos la localidad

    $id_cat_localidad = $_POST["id_cat_localidad"];

    

    //Obtenemos la localidad

    $tipo = $_POST["tipo"];

    

    //Determinamos el tipo        

    switch($tipo){

        case 'calle1':

                        $llave = 'entre_calle1';

                        $mensaje = 'Calle 1';

                        break;

        case 'calle2':

                        $llave = 'entre_calle2';

                        $mensaje = 'Calle 2';

                        break;

        case 'posterior':

                        $llave = 'calle_posterior';

                        $mensaje = 'Calle Posterior';

                        break;

        case 'vialidad':

                        $llave = 'CVE_VIA';

                        $mensaje = 'Vialidad';

                        break;

    }

    

    $sql = 'SELECT

            v.CVE_VIA, 

            concat(c.DESCRIPCION,?,v.NOM_VIA) as nombre

            from `vialidad` v

            LEFT JOIN cat_vialidad c on v.CVE_TIPO_VIAL = c.CVE_TIPO_VIAL            

            where v.CVE_EST_MUN_LOC = ?

            GROUP BY v.NOM_VIA ORDER BY v.NOM_VIA

            ';

    

    $params = array(' ',$CVE_EDO_RES.$id_cat_municipio.$id_cat_localidad);    

    $vialidad = $db->rawQuery($sql,$params);

           

}else{

    exit;

    //echo 'aqui';

}

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>

<select class="combobox" id="<?php echo $llave; ?>" name="<?php echo $llave; ?>">

    <option value=''>Seleccione <?php echo $mensaje; ?></option>

    <?php foreach($vialidad as $l): 



    if($l['CVE_VIA'] == $beneficiario[$llave]){

        $selected = "selected";

    }else{

        $selected = "";

    }

    ?>                

    <option value='<?php echo $l['CVE_VIA'] ?>' <?php echo $selected;?> > <?php echo $l['nombre'];?></option>

    <?php endforeach; ?>

</select>                