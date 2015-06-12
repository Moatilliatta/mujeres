<?php session_start();     

    //Si editamos el registro
    if(intval($id_edicion)>0){

        //Obtenemos el registro de la secretaría
        $db->where('id',$id_edicion);
        $secretaria = $db->getOne('seg_secretaria');

    }

//Arreglos para los select
$estatus = $db->get('estatus');

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

<form id='formSec' method="post" action='save_secretaria.php'>
	<table>
        <tr>
			<td>
                <label for="nombre">Nombre</label>
            </td>
			<td>
                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
                <input type = 'text' id = 'nombre' name = 'nombre' value="<?php echo $secretaria['nombre']; ?>" />
            </td>
		</tr>
        
        <tr>
            <td>
                <label for="descripcion">Descripci&oacute;n</label>
            </td>
            <td>
                <input type = 'text' id = 'descripcion' name = 'descripcion' value="<?php echo $secretaria['descripcion']; ?>" />
            </td>
        </tr>

        <!--
        <tr>
            <td>
                <label for="descripcion">Fecha Creaci&oacute;n</label>
            </td>
            <td>
                <input type = 'text' id = 'fecha_creado' class="fecha date" name = 'fecha_creado' value="<?php echo $secretaria['fecha_creado']; ?>" />
            </td>
        </tr>
        -->
        
        <?php if($id_edicion > 0) { ?>
        <tr>
            <td>
               <label for="activo">Estatus</label> 
            </td>
            <td>
                <select id="activo" name="activo">
                    <option value="">Seleccione</option>
                    <?php foreach($estatus as $e): 
                        
                    $selected = ($e['valor'] === $secretaria['activo'])? "selected" : '' ;
                        
                    ?>                
                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php } ?>

         <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type = 'submit' class="boton" title="&iquest;Est&aacute; seguro de guardar estos datos?" id = 'enviar' value = 'Enviar' /></td>
		</tr>
	</table>
</form>