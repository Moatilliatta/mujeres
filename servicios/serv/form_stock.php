<?php
session_start();

 //Si editamos el registro

    if(intval($id_edicion)>0){
       
        //Obtenemos el registro del servicio
        $db->where('ID_C_SERVICIO',$id_edicion);
        $servicio = $db->getOne('c_servicio');
        
        }

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

	<form id='formStock' method="post" action='save_stock.php'>
	<table>
        <tr>
          <td>
            <label class="obligatorio">*</label> <label for="stock">Stock</label> 
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
            <input type = 'text' class="digits" id = 'stock' name = 'stock' value="<?php echo $servicio['STOCK']; ?>" />
          </td>
        </tr>
          <tr>
        <td>
            <input type="submit" value="Guardar" id="guardar" />
        </td>
    </tr>    
	</table>
	</form>