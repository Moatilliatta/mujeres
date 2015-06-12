<?php
session_start();//Habilitamos uso de variables de sesión
  
$actualiza_comunidad = $_POST['actualiza_comunidad'];

if(isset($_POST['axo_padron']) && isset($_POST['nom_reporte']) 
    && isset($_POST['id_municipio_caratula'])){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Obtenemos municipio
    $id_municipio_caratula = $_POST['id_municipio_caratula'];    

    //Obtenemos las comunidades
    $sql = 'SELECT CVE_ENT_MUN_LOC, nombre_comunidad 
            FROM `comunidad` 
            WHERE CVE_ENT = 14 AND CVE_MUN = ? ';

    $params = array($id_municipio_caratula);

    $comunidades = $db->rawQuery($sql,$params);

}else{

    exit;
}

?>

<td>
    <label>Seleccione Comunidad</label>
</td>
<td>
    <select id="<?php echo ($actualiza_comunidad!='NO')?'CVE_ENT_MUN_LOC':'' ?>" name="CVE_ENT_MUN_LOC">
    <option value=''>Seleccione Comunidad</option>
    <?php foreach($comunidades as $l): 
    
        /*
        if($l['CVE_ENT_MUN_LOC'] == $centro_atencion['CVE_ENT_MUN_LOC']){

            $selected = "selected";

        }else{

            $selected = "";

        }*/

    ?>                
    <option value='<?php echo $l['CVE_ENT_MUN_LOC'] ?>' <?php echo $selected;?> > <?php echo $l['nombre_comunidad'];?></option>
    <?php endforeach; ?>
    </select>      
</td>