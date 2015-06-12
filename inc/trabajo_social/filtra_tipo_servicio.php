<?php
session_start();//Habilitamos uso de variables de sesión
if(isset($_POST['id_producto_servicio'])){
    

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");

    //Obtenemos los tipos de servicio
    $id_producto_servicio = $_POST["id_producto_servicio"];   
    //echo  $id_tipo_servicio;
    //exit;
     
    $sql = '
    SELECT nombre, id from servicios_especificos where id in (
    select 
    se.padre
    FROM relacion_servicios rs
    LEFT JOIN servicios_especificos se on se.id = rs.id_servicios_especificos
    where id_producto_servicio = ?
    GROUP BY se.padre
)
    ' ;
    $params = array($id_producto_servicio);
    $tipo_servicio = $db->rawQuery($sql,$params);

}else{
  //  echo $id_tipo_servicio;
    // exit;

}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<select id="id_tipo_servicio" class="combobox" name="id_tipo_servicio">
    <option value=''>Seleccione el tipo de servicio</option>

    <?php foreach($tipo_servicio as $l): 

        if($l['id'] == $aspirantes['']){

            $selected = "selected";

        }else{

            $selected = "";
        }

    ?>                

    <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > <?php echo $l['nombre'];?></option>

    <?php endforeach; ?>
</select>




