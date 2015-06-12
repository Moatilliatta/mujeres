<?php session_start();     
        
    //Siempre obtendremos los módulos activos
    $db->where('activo',1);
    $modulo = $db->get('modulo');
    
    //Si obtenemos los estatus
    $estatus = $db->get('estatus');
    
    //Si editamos el registro
    if(intval($id_edicion)>0){
        //Obtenemos el registro del módulo
        $db->where('id',$id_edicion);
        $submodulo = $db->getOne('submodulo');                
    }
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

	<form id='formSub' method="post" action='save_submodulo.php'>
	<table>
    <tr>
        <td>
          <label for="id_modulo">M&oacute;dulo Padre</label> 
        </td>

        <td>

            <select id="id_modulo" name="id_modulo">

                <option value='0'>Seleccione M&oacute;dulo</option>

                <?php foreach($modulo as $m):

                    if($m['id'] == $submodulo['id_modulo']){

                        $selected = "selected";
                        
                    }else{

                        $selected = "";

                    }
                    ?>                
                <option value='<?php echo $m['id'] ?>' <?php echo $selected;?> >
                    <?php echo $m['descripcion'];?></option>
                    
                <?php endforeach; ?>

            </select>
        </td>
    </tr>

    <tr>
        <td>
            <label for="nombre">Nombre del Subm&oacute;dulo</label> 
        </td>
        <td>
            <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
            <input type = 'text' id = 'nombre' name = 'nombre' value="<?php echo $submodulo['nombre']; ?>" />
        </td>
   </tr>
    <tr>
      <td>
        <label for="orden">Orden</label>
      </td>
      <td>
        <input type = 'text' class="digits" id = 'orden' name = 'ordent' class="nomnum"value="<?php echo $submodulo['orden']; ?>" />
      </td>
    </tr>
    
   
      <tr>
          <td>
             <label for="descripcion">Descripci&oacute;n </label>
          </td> 
          <td>
                 <textarea id='descripcion' name = 'descripcion'  class="nomnum" cols="50" rows="5" ><?php echo $submodulo['descripcion']; ?></textarea>
          </td>
      </tr>

    <tr>
    <?php if($id_edicion > 0) { ?>
        <td>
            <label for="activo">Estatus</label>
        </td>
        <td>
            <select id="activo" name="activo">
                <option value="">Seleccione</option>
                <?php foreach($estatus as $e): 

                        if($e['valor'] === $submodulo['activo']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    ?>                
                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>
                    <?php endforeach; ?>
            </select>
        </td>
           <?php } ?>
    </tr>

      	<tr>
			<td>&nbsp;</td>
			<td><input type = 'submit'  id = 'enviar' value = 'Enviar' /></td>
		</tr>

	</table>
	</form>