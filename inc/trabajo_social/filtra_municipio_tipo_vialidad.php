<?php
session_start();//Habilitamos uso de variables de sesión
//Obtenemos conexión
   
   if(isset($_POST['CVE_EST_MUN_LOC'])) {
    
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Obtenemos los municipios
    $CVE_EST_MUN_LOC = $_POST["CVE_EST_MUN_LOC"]; 
    
    //Obtenemos la localidad
    $tipo = $_POST["tipo"];
    
    switch($tipo){
        case 'tipo_vialidad':
                        $llave = 'CVE_TIPO_VIAL';
                        
                        break;
        case 'tipo_vialidad_calle1':
                        $llave = 'CVE_TIPO_VIAL_CALLE1';
                        
                        break;
        case 'tipo_vialidad_calle2':
                        $llave = 'CVE_TIPO_VIAL_CALLE2';
                        
                        break;
        case 'tipo_vialidad_calle3':
                        $llave = 'CVE_TIPO_VIAL_CALLEP';
                       
                        break;
    }
    
    
       
    $sql = '
        SELECT
        c.CVE_TIPO_VIAL,
        c.DESCRIPCION
        FROM `vialidad` v
        LEFT JOIN cat_vialidad c on v.CVE_TIPO_VIAL = c.CVE_TIPO_VIAL
        where v.CVE_EST_MUN_LOC = ? GROUP BY v.CVE_TIPO_VIAL';
    
    $params = array($CVE_EST_MUN_LOC);
    
    $municipios_tipo_vial = $db->rawQuery($sql,$params);

}else{
     exit;
}
?>
<select id="<?php echo $llave; ?>" name="<?php echo $llave; ?>" class="datos_aspirante">
    <option value=''>Seleccione Tipo de Vialidad</option>
       <?php foreach($municipios_tipo_vial as $mtp):?>                 
           <option value='<?php echo $mtp['CVE_TIPO_VIAL'] ?>' > 
       <?php echo $mtp['DESCRIPCION'];?>
           </option>                
       <?php endforeach; ?>
</select>

