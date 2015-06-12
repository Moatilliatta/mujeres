<?php
session_start();

include_once($_SESSION['model_path'].'caravana.php');

//Obtenemos los correspondientes listados
$estatus = $db->get('estatus');

$caravanas = Caravana::listadoCaravana();

$tipo_lugar = $db->get('tipo_lugar');
$filtros = $db->get('callcenter_filtro');
$seg_capacitacion = $db->get("seg_capacitacion",null,"id, nombre");

if(intval($id_edicion)>0){
       
        //Obtenemos el registro del caravana
        $db->where('id',$id_edicion);
        $grupo = $db->getOne('callcenter_grupo');
       
       
        }

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

	<form id='formGrupoCallcenter' method="post" action='save_grupo_callcenter.php'>
        <tr>
         <td>
           <label class="obligatorio">Le recordamos que los campos con asterisco (*) son campos obligatorios</label>
         </td>
        </tr>
        
        <fieldset>
        <legend>
          <label>Grupo Callcenter</label>  
       </legend>
	     <table>
        <tr>
          <td>
            <label class="obligatorio">*</label> <label for="nombre">Nombre Del Grupo</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
            <input type = 'text'  id = 'nombre' name = 'nombre' value="<?php echo $grupo['nombre']; ?>" />
          </td>
        </tr>
        
        <tr>
         <td>
           <label class="obligatorio">*</label>
           <label for="id_caravana">Seleccionar Caravana</label>
         </td>
        </tr>
        <tr>
          <td>
            <select id="id_caravana" name="id_caravana" class="combobox">
                <option value="">Seleccione Lugar</option>
                <?php foreach($caravanas as $c): 
                $selected = ($c['id'] == $grupo["id_caravana"] )? 'selected' : ''; ?>
                    <option value='<?php echo $c['id'] ?>'  <?php echo $selected;?> > 
                        <?php echo $c['descripcion'].' ('.$c['tipo_importacion'].')';?>
                    </option>
                <?php endforeach; ?>
            </select>
          </td>
        </tr>        

    <?php if($id_edicion > 0){ ?>
    <tr>
     <td>
       <label>Estatus</label>
     </td>
    </tr>
    <tr>
     <td>
        <select id="activo" name="activo">
        <option value="">Seleccione</option>
        <?php foreach($estatus as $e): 
        $selected = ($e['valor'] === $grupo['activo'])? 'selected' : '' ;?>                
        <option value='<?php echo $e['id'] ?>' <?php echo $selected;?> > 
        <?php echo $e['nombre'];?>
        </option>
        <?php endforeach; ?>
        </select>
     </td>
    </tr>
    <?php } ?>       
    <tr>
      <td>
        <label class="obligatorio">*</label><label for="filtro">Filtro</label>
      </td>
    </tr>
    <tr>
      <td>
        <select id="id_callcenter_filtro" name="id_callcenter_filtro">
          <option value=''>Seleccione Filtro</option>
          <?php foreach($filtros as $f): 
            $selected = ($f['id'] === $grupo['id_callcenter_filtro'])? 'selected' : '' ;?>  
            <option value='<?php echo $f['id'] ?>' <?php echo $selected;?> >
            <?php echo $f['estatus']?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>  
    <tr id="titulo_seg_cap" 
    style="display: <?php echo ($grupo['id_seg_capacitacion'] > 0)? 'block' : 'none'; ?>">
      <td>
        <label class="obligatorio">*</label>
        <label for="filtro">Capacitaci&oacute;n de Punto Rosa</label>
      </td>
    </tr>
    <tr>      
      <td id="seg_capacitacion">
        <?php if($grupo['id_seg_capacitacion'] > 0){ ?>
        <select class="combobox" id="id_seg_capacitacion" name="id_seg_capacitacion">
        <option value=''>Seleccione Capacitaci&oacute;n</option>
        <?php foreach($seg_capacitacion as $l): 
        $selected = ($l['id'] === $grupo['id_seg_capacitacion'])? 'selected' : '' ;?>
        <option value='<?php echo $l['id'] ?>' <?php echo $selected;?> > 
            <?php echo $l['nombre'];?></option>
        <?php endforeach; ?>
        </select>
        <?php } ?>
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