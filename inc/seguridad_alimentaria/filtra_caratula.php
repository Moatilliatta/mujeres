<?php
session_start();//Habilitamos uso de variables de sesión

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");

//Obtenemos las localidades
$id_municipio_caratula = $_POST["CVE_MUN"];

//Municipios con centros de atención indígenas
if($id_municipio_caratula == '019' || $id_municipio_caratula == '061'){ ?> 
 
  <td><label>Reporte</label></td>
  <td>
   <select id="nom_reporte" name = "nom_reporte" class="comunidad">
    <option value="caratula">Car&aacute;tula</option>
    <option value="reporte_proalimne_indigena">Car&aacute;tula de Centros Ind&iacute;genas</option>
    <option value="selecciona_centro_proalimne">Padr&oacute;n</option>
    <option value="selecciona_centro">Padr&oacute;n para transparencia</option>
    <option value="selecciona_centro_firmas">Reporte Firmas</option>
    <option value="selecciona_centro_firmas_fruta">Reporte Firmas Fruta</option>
 </select>  
  </td>
 
<?php }else if($id_municipio_caratula != NULL){ ?>
 <td><label>Reporte</label></td>
 <td>
   <select id="nom_reporte" name = "nom_reporte" class="comunidad">
    <option value="caratula">Car&aacute;tula</option>
    <option value="selecciona_centro_proalimne">Padr&oacute;n</option>
    <option value="selecciona_centro">Padr&oacute;n para transparencia</option>
    <option value="selecciona_centro_firmas">Reporte Firmas</option>
    <option value="selecciona_centro_firmas_fruta">Reporte Firmas Fruta</option>
 </select> 
 </td>
<?php } ?>  

</td>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>

 