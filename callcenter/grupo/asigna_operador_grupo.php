<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

include_once($_SESSION['model_path'].'callcenter_grupo_operador.php');

//Borramos variable por si provenimos de otro grupo
unset($_SESSION['arrayArt']);

//Obtenemos grupo que obtendrá operador
$id_callcenter_grupo = $_GET['id_callcenter_grupo'];

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Obtenemos listado de usuarios
$usuarios = CallcenterGrupoOperador::listaOpDisp($id_callcenter_grupo);

//Obtenemos listado de operadores según el id_callcenter_grupo_operador
$listado = CallcenterGrupoOperador::listado_operador($id_callcenter_grupo);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

<div id="principal">
  <div id="contenido">
    <h2 class="centro">Asignar operadores a grupo</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div  align="center">      

      <table>
           <tr> 
           <td>
             <label for="id_usuario">Seleccionar Operador Disponible</label>  
           </td>
            <td>
             <input type="hidden" id="id_callcenter_grupo" name="id_callcenter_grupo" value="<?php echo $id_callcenter_grupo;?>" />
             <select id="id_usuario" name="id_usuario">
                <option value=''>Seleccione Operador</option>
                <?php foreach($usuarios as $u): ?>
                  <option value='<?php echo $u['id'] ?>' <?php echo $selected;?> > 
                    <?php echo $u['nombre_completo'];?>
                  </option>
                <?php endforeach; ?>
             </select>
            </td>
          </tr>
      </table>

      <div id="car_operador">
        
      </div>

      <?php if($listado){ ?>
      <div id="operadores">
     
      <h2 class="centro">Operadores ya asignados</h2>

        <table class="tablesorter">             
            <thead> 
                <tr>
                    <th>Grupo</th>
                    <th>Nombre</th>
                    <th>Acci&oacute;n</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($listado as $key => $l):?>
                <tr class="zebra"> 
                    <td><?php echo $l['grupo'];?></td>
                    <td><?php echo $l['nombre_completo'];?></td>
                    <td>
                <?php  
                //Verificamos si tiene permiso de activar/desactivar servicio
                if(Permiso::accesoAccion('activa_operador', 'grupo', 'callcenter')){ ?>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">                
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> operador?" 
                       href="activa_operador_grupo.php?id_activo=<?php echo $l['id']; ?>&id_callcenter_grupo=<?php echo $l['id_callcenter_grupo'];?>"></a>
                </div>
                <?php } ?>

                    </td>
                </tr>
              <?php endforeach;?>
            </tbody>
        </table>

      </div>
      <?php } ?>

    </div>

  </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>