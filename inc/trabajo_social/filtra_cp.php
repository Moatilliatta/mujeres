<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['id_cat_municipio'])){
    
    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Obtenemos el estado
    $CVE_EDO_RES = $_POST["CVE_EDO_RES"];
    
    //Obtenemos el municipio
    $id_cat_municipio = $_POST["id_cat_municipio"];
        
    $sql = 'select d_codigo from cp_sepomex where c_estado = ? and c_mnpio = ? GROUP BY d_codigo';
    
    $params = array($CVE_EDO_RES,$id_cat_municipio);
    
    $codigo = $db->rawQuery($sql,$params);
       
}else{
    exit;
}
?>

<select class="combobox datos_aspirante" id="CODIGO" name="CODIGO">
    <option value=''>Seleccione C&oacute;digo Postal</option>
    <?php foreach($codigo as $l): 

        if($l['d_codigo'] == $aspirantes['CODIGO']){
            $selected = "selected";
        }else{
            $selected = "";
        }
    ?>                
    <option value='<?php echo $l['d_codigo'] ?>' <?php echo $selected;?> > <?php echo $l['d_codigo'];?></option>
    <?php endforeach; ?>
</select>   