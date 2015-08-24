<?php
//session_start();
include_once($_SESSION['model_path'].'servicio.php');
include_once($_SESSION['model_path'].'caravana.php');
//traemos los status
$estatus = $db->get('estatus');

//traemos los status
$lugar = $db->get('tipo_lugar');

//obtenemos servicios
$servicio = Servicio::listado();

$selected = '';

$caravana = array();

//Si editamos el registro
if(isset($id_edicion) && intval($id_edicion)>0){
       
        //Obtenemos el registro del caravana
        $db->where('id',$id_edicion);
        $caravana = $db->getOne('caravana');
        
        $id_caravana = Caravana::listaCaravana();
        
        
        $sql = 'SELECT  
                servc.id,
                servc.stock
                FROM `servicio_caravana` servc
                where servc.id_caravana = ? ';
        $params = array($id_edicion);
        $stock = $db->rawQuery($sql,$params);
        
        
        }

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>

	<form id='formCaravana' method="post" action='save_caravana.php'>
        <tr>
         <td>
           <label class="obligatorio">Le recordamos que los campos con asterisco (*) son campos obligatorios</label>
         </td>
        </tr>
        <fieldset>
        <legend>
          <label>REGISTRO DE CARAVANAS</label>  
       </legend>
	<table>
        <tr>
          <td>
            <label class="obligatorio">*</label> <label for="descripcion">Descripci&oacute;n de la Caravana</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
            <input type = 'text'  id = 'descripcion' name = 'descripcion' value="<?php echo (isset($caravana['descripcion']))? $caravana['descripcion'] : null; ?>" />
          </td>
        </tr>
        <tr>
        <td>
            <label class="obligatorio">*</label>
            <label for="fecha_instalacion">Fecha de Instalaci&oacute;n de la Caravana</label>
        </td>
        </tr>
         <tr>
        <td>
            <input type = 'text' id = 'fecha_instalacion' class="fecha date" name = 'fecha_instalacion'value="<?php echo (isset($caravana['fecha_instalacion']))? $caravana['fecha_instalacion'] : null; ?>" />
            <input type="button"  value="Hoy" id="btnToday" class="today"  />
        </td>
         </tr>
 <tr>
        <td>
            <label class="obligatorio">*</label>
            <label for="fecha_instalacion">Direcci&oacute;n donde se instal&oacute; la Caravana</label>
        </td>
        </tr>
         <tr>
        <td>
          <textarea name = 'direccion' cols="50" rows="5" ><?php echo (isset($caravana['direccion']))?$caravana['direccion']:null; ?></textarea>
        </td>
         </tr>
    
    <?php //if($id_edicion > 0) { ?>        
    <tr>
      <td>
        <label for="activo">Estatus</label>
      </td>
    </tr>
    <tr>
      <td>
        <select id="activo" name="activo">
          <option value="">Seleccione</option>
          <?php foreach($estatus as $e){                         
              
              if(isset($caravana['activo'])){
                $selected = ($e['valor'] == $caravana['activo'])? "selected" : '' ;
              }

          ?>
          <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > 
          <?php echo $e['nombre'];?>
          </option>
          <?php } ?>
        </select>
      </td>                     
    </tr>
    <?php //} ?>

    <tr>
      <td>
        <label for="id_tipo_lugar">Lugar</label>
      </td>
    </tr>
    <tr>
      <td>
        <select id="id_tipo_lugar" name="id_tipo_lugar">
          <?php foreach($lugar as $l){                         
              if(isset($caravana['id_tipo_lugar'])){
                $selected = ($l['id'] == $caravana['id_tipo_lugar'])? "selected" : '' ;
              }
          ?>
          <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > 
          <?php echo $l['tipo_importacion'];?>
          </option>
          <?php } ?>
        </select>
      </td>                     
    </tr>

    <tr>
          <td>
             <label for="observaciones">Observaciones</label>
          </td>
        </tr>
        <tr>
          <td>
             <textarea  name = 'observaciones'   cols="50" rows="5" ><?php echo (isset($caravana['observaciones']))? $caravana['observaciones'] : null; ?></textarea>
          </td>
        </tr>
        <tr>
          <td>
             <label for="longitud">Longitud de la Ubicaci&oacute;n de la Caravana</label>
          </td>
        </tr>
        <tr>
          <td>
             <input type = 'text' class="nombre texto_largo" id = 'longitud' name = 'longitud' value="<?php echo (isset($caravana['longitud']))? $caravana['longitud'] : null; ?>" />
          </td>
        </tr>
        <tr>
          <td>
             <label for="latitud">Latitud de la Ubicaci&oacute;n de la Caravana</label>
          </td>
        </tr>
        <tr>
          <td>
             <input type = 'text' class="nombre texto_largo" id = 'latitud' name = 'latitud' value="<?php echo (isset($caravana['latitud']))? $caravana['latitud'] : null; ?>" />
          </td>
        </tr>
          
	</table>
        </fieldset>
         <tr>
        <td>
            <input type="submit" value="Guardar"  />
        </td>
    </tr>   
	</form>