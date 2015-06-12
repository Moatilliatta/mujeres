<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['CODIGO'])){
    
    //Obtenemos código
    $codigo = $_POST['CODIGO'];
    
    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Filtramos asentamientos por código postal
    $sql = 'SELECT id,d_codigo,d_asenta,d_tipo_asenta FROM `cp_sepomex` where d_codigo = ?';
    
    $params = array($codigo);
    
    $localidad = $db->rawQuery($sql,$params);
       
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>
<select  id="id_cp_sepomex" name="id_cp_sepomex">
    <option value=''>Seleccione Asentamiento</option>
    <?php foreach($localidad as $l): 

        if($l['id'] == $beneficiario['id_cp_sepomex']){
            $selected = "selected";
        }else{
            $selected = "";
        }
    ?>                
    <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > <?php echo $l['d_asenta'];?></option>
    <?php endforeach; ?>
</select>                