<?php
session_start();//Habilitamos uso de variables de sesión

$CVE_TIPO_VIAL_NUEVA = $_POST['CVE_TIPO_VIAL_NUEVA'];
$NOM_VIA_NUEVA = $_POST['NOM_VIA_NUEVA'];
$CVE_EDO_RES_NUEVA = $_POST['CVE_EDO_RES_NUEVA'];
$id_cat_municipio_nueva = $_POST['id_cat_municipio_nueva'];
$id_cat_localidad_nueva = $_POST['id_cat_localidad_nueva'];
$actualizar = $_POST['actualizar'];
$id_edicion = null;

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");

//obtenemos el tipo vialida de vialidad_nueva
    $sql = 
    'SELECT
     v.CVE_TIPO_VIAL,
     v.DESCRIPCION
     from `cat_vialidad` v ';

    $tipo_vialidad = $db->query($sql);
    //$tipo_vialidad=$tipo_vialidad[0];
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
  <form id="form_nueva_vialidad" class="formulario_new_via"> 
    <table>
        <tr>
          <td>
            <input id="id_edicion_nueva" type="hidden" name="id_edicion_nueva" value="<?php echo $id_edicion; ?>" />
            <input  id="CVE_EDO_RES_NUEVA"type="hidden" name="CVE_EDO_RES_NUEVA" value="<?php echo $CVE_EDO_RES_NUEVA; ?>" />
            <input  id="id_cat_municipio_nueva"type="hidden" name="id_cat_municipio_nueva" value="<?php echo $id_cat_municipio_nueva; ?>" />
            <input id="id_cat_localidad_nueva" type="hidden" name="id_cat_localidad_nueva" value="<?php echo $id_cat_localidad_nueva; ?>" />
            <label for="CVE_TIPO_VIAL_NUEVA">Tipo De Vialidad </label>
          </td>
          <td>
            <label for="vialidad_nueva">Nueva Vialidad </label>
          </td>
        </tr>

        <tr>
          <td id="tipo_vialidad_nueva">
            <select id="CVE_TIPO_VIAL_NUEVA" name="CVE_TIPO_VIAL_NUEVA">
              <option value=''>Seleccione tipo de Vialidad</option>
              <?php foreach($tipo_vialidad as $tp):?>
              <option value='<?php echo $tp['CVE_TIPO_VIAL'] ?>' <?php echo ($tp['CVE_TIPO_VIAL'] == $CVE_TIPO_VIAL_NUEVA)? 'selected' : '';?> > 
                <?php echo $tp['DESCRIPCION'];?>
              </option>                
              <?php endforeach; ?>
            </select>
          </td>

          <td>
            <input type = 'text' style='width: 300px' id = 'NOM_VIA_NUEVA' name = 'NOM_VIA_NUEVA' class="nomnum"value="<?php echo $NOM_VIA; ?>" />
            <input  id="guarda_vialidad" name="<?php echo $actualizar ?>" type="submit" value="Guardar Vialidad Nueva"  />
          </td>          

        </tr>

        <!--
        <tr>
          <td>&nbsp;</td>
          <td colspan="4">
          <input  id="guarda_vialidad"type="submit" value="Guardar Vialidad Nueva"  />          
          </td>
        </tr>
        -->
    </table>
  </form>     