<?php
session_start();

//Eliminamos variable de sesión de carrito
unset($_SESSION['arrayArt']);

/* Al utilizar ajax y refrescar este formulario
necesitaremos obtener tanto el objeto $db (instancia de conexión)
como el id del beneficiario. Verificamos que, si no están creadas
las variables, las obtendremos*/

if(!isset($db)){

  //Incluimos librería de permiso
  include ($_SESSION['inc_path'] . "conecta.php");

}

//Obtemos las dependencias
include_once($_SESSION['model_path'].'dependencia.php');
$dependencia = Dependencia::listaDependencias();

$sql = 'Select id, grado from grado';
$grado = $db->query($sql);


?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/combobox.js"></script>

<form id='formServ' method="post" action='save_mujer_serv.php'>
 <tr>
  <td>
   <label class="obligatorio">* Campos OBLIGATORIOS</label>
  </td>
 </tr>
 <fieldset>
  <table>
   <legend>
    <label>
     Selecci&oacute;n de Servicio
    </label>  
   </legend>
    <tr>        
        <td colspan="4">&nbsp;</td>        
    </tr>
   <tr>
     <td>
        <label class="obligatorio">*</label><label for="grado">Grado</label>
     </td>
   </tr>
   <tr>
      <td id="grado">
       <input type="hidden" name="id_mujeres_avanzando" id="id_mujeres_avanzando" value="<?php echo (isset($id_mujeres_avanzando))? $id_mujeres_avanzando: '';?>"/>
        <select  id="id_grado" name="id_grado">
            <option value=''>Seleccione el Grado</option>
            <?php foreach($grado as $g): 

            $selected = ($g['id'] == $mujeres_avanzando['id_grado'])? 'selected': '' ;?>                

                    <option value='<?php echo $g['id'] ?>' <?php echo $selected;?> > 
                        <?php echo $g['grado'];?>
                    </option>
            <?php endforeach; ?>
        </select>
      </td>
   </tr>
    <tr>
      <td>
        <label class="obligatorio">*</label>
        <label for="ID_C_DEPENDENCIA">Dependencia</label>
      </td>
    </tr>

    <tr>
      <td id="dependencias">
     
        <select class="combobox filtra_programa_busqueda" id="ID_C_DEPENDENCIA" name="ID_C_DEPENDENCIA">
            <option value=''>Seleccione Dependencia</option>
        </select>
        </td>
    </tr>
<!--  
    <tr>
      <td>
        <label for="ID_C_PROGRAMA">Programas</label>
      </td>
    </tr>
    <tr>
      <td id="programas">
        <select class="combobox" id="ID_C_PROGRAMA" class="filtra_programa" name="ID_C_PROGRAMA">
            <option value=''>Seleccione Programa</option>                   
        </select>
      </td>
    </tr>
 -->
    <tr>
      <td>
        <label for="ID_C_SERVICIO">Servicio</label>
      </td>
    </tr>
    <tr>
      <td id="servicioss">
        <select class="combobox" id="ID_C_SERVICIO" name="ID_C_SERVICIO">
            <option value=''>Seleccione el Servicio</option>                   
        </select>
      </td>
    </tr>
    <tr>
      <td><label>Observaciones</label></td>      
    </tr>
    <tr>
      <td>
        <textarea id='observaciones' name = 'observaciones' class="nomnum" cols="50" rows="5"></textarea>
      </td>
    </tr>    
    <tr>
      <td id='tbl_servicios'></td>
    </tr>
    <tr>
    <td colspan="4" >
    &nbsp;
    </td>
    </tr>
    <tr>
    <td>&nbsp;</td>
        <td colspan="4">
          <input type="submit" value="Guardar" id="guardar" />
        </td>
    </tr>  
  </table>
 </fieldset>
</form>