<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

  <fieldset>
	<form id='formCaravana' method="post" action='save_callcenter.php'>        	
  <table> 
    <tr>
      <td>
        <label for="activo">Estatus De Llamada</label>
      </td>
    </tr>
    <tr>
      <td>
        <input type="hidden" name="id_mujeres_avanzando" value="<?php echo $id_mujeres_avanzando; ?>" />
        <input type="hidden" name="id_callcenter" value="<?php echo $id_callcenter; ?>" />
        <input type="hidden" name="id_callcenter_grupo" value="<?php echo $callcenter['id_callcenter_grupo']; ?>" />

        <select id="id_status_llamada" name="id_status_llamada">
          <option value="">Seleccione estatus </option>
          <?php foreach($estatus as $e){                         
              $selected = ($e['id'] == $callcenter['id_status_llamada'])? "selected" : '' ;
              ?>
          <option value='<?php echo $e['id'] ?>' <?php echo $selected;?> > 
          <?php echo $e['estatus'];?>
          </option>
          <?php } ?>
        </select>
      </td>                     
    </tr>
    <tr>
      <td>
        <input type="submit" value="Guardar"  />
      </td>
    </tr>   
  </table>
  </fieldset>    
	</form>