<?php
session_start(); 
    //Cargamos listado de estatus
    $estatus = $db->get('estatus');
    
    //Si editamos el registro
    if(intval($id_edicion)>0){
     //buscamos el registro del usuario
        $db->where('id',$id_edicion);
        $perfil = $db->getOne('perfil');    
    }
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
	<form id='formPrf' method="post" action='save_perfil.php'>
	<table>
        <tr>
			<td>
               <label for="nombre">Perfil</label> 
            </td>
			<td>
                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
                <input type="hidden" name="id_perfil" value="<?php echo $perfil['id']; ?>" />
                <input type = 'text' id = 'nombre' name = 'nombre' class="nomnum" value="<?php echo $perfil['nombre']; ?>" />
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
                        if($e['valor'] === $perfil['activo']){
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
			<td><input type = 'submit'  id = 'enviar'value = 'Enviar' /></td>
		</tr>
	</table>
	</form>