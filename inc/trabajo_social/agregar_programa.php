<?php 
session_start();

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
include_once($_SESSION['model_path'].'producto_servicio.php');

//Obtemos los productos y servicios de Trabajo Social
$programa = Producto_servicio::listaPys(null,3); 
?>

<tr>
  <td colspan='2'>
    <label class="obligatorio">*</label><label for="id_producto_servicio">Productos/Servicios de Trabajo Social</label>
  </td>
</tr>      

  <tr>  
    <td colspan='2'>
      <select  id="id_producto_servicio" name="id_producto_servicio" class="datos_aspirante">
        <option value=''>Seleccione el producto/servicio</option>
        <?php foreach($programa as $p): ?>
          <option value='<?php echo $p['id'] ?>' <?php echo ($p['id'] == $trabajo_social['id_producto_servicio'])? 'selected': '' ;?> > 
              <?php echo $p['codigo_producto'].'-'.$p['nombre'];?>
          </option>
        <?php endforeach; ?>
      </select>
    </td> 
</tr>