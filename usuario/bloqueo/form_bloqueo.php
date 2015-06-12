<?php
session_start();//Habilitamos uso de variables de sesión 

   //Obtenemos Bloqueos Activos
     $db->where('activo',1);
     $usr = $db->get('usuario');

   //Obtenemos Grupos Activos
     $db->where('activo',1);
     $grupo_Qry = $db->get('grupo');

   //Obtenemos los Módulos Activos
     $db->where('activo',1);
     $modQry= $db->get('modulo');

   //Obtenemos los Submódulos Activos
     $db->where('activo',1);
     $submodQry = $db->get('submodulo');

   //Obtenemos las acciones activas
     $db->where('activo',1);
     $accionQry= $db->get('accion');

////////////////////////////////////////////////////////////////////////////////

   //Si editamos el registro verificamos si vamos a editar
    if(intval($id_edicion)>0){

    //Buscamos el registro
    $db->where('id',$id_edicion);
    $bloq= $db->getOne('bloqueo'); 

    }
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

<form id='formBlq' method="post" action='save_bloqueo.php'>
 <table>
   <tr>
       <td>
            <label for="id_usuario">Usuarios</label>
       </td>
       <td>
          <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
          <input type="hidden" name="id_bloqueo" value="<?php echo $bloq['id'];  ?>" />
          <select id="id_usuario" name="id_usuario">
            <option value='0'>Seleccione Usuario</option>
             <?php foreach($usr as $u): 
                        if($u['id'] === $bloq['id_usuario']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    ?>       
            <option value='<?php echo $u['id'] ?>' <?php echo $selected;?>  ><?php echo $u['usuario'];?> </option>
           <?php endforeach; ?>
         </select>
       </td>
    </tr>

    <tr>
        <td>
            <label for="id_grupo">Grupos</label>
        </td>        
        <td id="grupo">
           <select id="id_grupo" name="id_grupo">
                <option value=''>Seleccione Grupo</option>
                     <?php foreach($grupo_Qry as $g): 

                        if($g['id'] === $bloq['id_grupo']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                      ?>    
                <option value='<?php echo $g['id'] ?>' <?php echo $selected;?> >
                        <?php echo $g['nombre'];?></option>

                     <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="id_modulo">M&oacute;dulos</label>
        </td>
        <td>
            <select id="id_modulo" name="id_modulo">
                <option value=''>Seleccione el Modulo</option>
                     <?php foreach($modQry as $m): 

                        if($m['id'] === $bloq['id_modulo']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                      ?>
                <option value='<?php echo $m['id'] ?>' <?php echo $selected;?> >
                        <?php echo $m['nombre'];?></option>
                        
                   <?php endforeach; ?>
            </select>
        </td>
   </tr>
    <tr>
        <td>
            <label for="id_submodulo">Subm&oacute;dulos</label>
        </td>
        <td>
            <select id="id_submodulo" name="id_submodulo" >
                <option value=''>Seleccione el SubModulo</option>
                   <?php foreach($submodQry as $s): 

                        if($s['id'] === $bloq['id_submodulo']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                      ?>
                <option value='<?php echo $s['id'] ?>' <?php echo $selected;?> >
                        <?php echo $s['nombre'];?></option>

                  <?php endforeach; ?>
                </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="id_accion">Acci&oacute;n</label>
        </td>
        <td>
            <select id="id_accion" name="id_accion">
                <option value=''>Seleccione la Acci&oacute;n</option>

                    <?php foreach($accionQry as $a): 

                        if($a['id'] === $bloq['id_accion']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    ?>

                <option value='<?php echo $a['id'] ?>' <?php echo $selected;?> >
                        <?php echo $a['nombre'];?></option>

                    <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td><input type = 'submit'  id = 'enviar'value = 'Enviar' /></td>
    </tr>
	</table>
	</form>