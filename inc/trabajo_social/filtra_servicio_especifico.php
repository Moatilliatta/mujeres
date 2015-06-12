<?php
session_start();//Habilitamos uso de variables de sesión
if(isset($_POST['id_tipo_servicio'])){
    

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Obtenemos los servicios especificos
    $id_tipo_servicio = $_POST["id_tipo_servicio"];   
    //echo  $id_tipo_servicio;
    //exit;
     
    $sql = '
    Select 
    id,
    nombre
    FROM servicios_especificos
    where padre = ?    
    ' ;
    $params = array($id_tipo_servicio);
    $servicio_especifico = $db->rawQuery($sql,$params);

}else{
  //  echo $id_tipo_servicio;
    // exit;

}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<select id="id_servicio_especifico" class="combobox" name="id_servicio_especifico">
    <option value=''>Seleccione el Servicio Especifico</option>

    <?php foreach($servicio_especifico as $l): 

      
    ?>                

    <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > <?php echo $l['nombre'];?></option>

    <?php endforeach; ?>
</select>




