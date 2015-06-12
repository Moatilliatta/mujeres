<?php

 $estatus = $db->get('estatus');
    //Si editamos el registro
    if(intval($id_edicion)>0){
        //Obtenemos el registro del menu
        $db->where('id',$id_edicion);
        $menu = $db->getOne('menu');                
    }     
    
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
 
<form id='formMnu' method="post" action='save_menu.php'>
 <table>
     <tr>
			<td>
                <label for="nombre">Men&uacute;</label>
            </td>
			<td>
                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
                <input type="hidden" name="id_menu" value="<?php echo $menu['id']; ?>" />
                <input type = 'text' id = 'nombre' name = 'nombre' class="nombre" value="<?php echo $menu['nombre']; ?>" />
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
                        if($e['valor'] === $menu['activo']){
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