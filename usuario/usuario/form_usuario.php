<?php session_start();     

    //Siempre obtendremos los perfiles activos
    $db->where('activo',1);
    
    //deshailtamos campo usuario
    $disabled = 'disabled="disabled"';
    
    //Siempre obtendremos los perfiles activos    
    $perfil = $db->get('perfil');
    
    //Siempre obtendremos los estatis activos
    $estatus = $db->get('estatus');

    //Incluimos modelos
    include_once($_SESSION['model_path'].'caravana.php');
    include_once($_SESSION['model_path'].'seg_secretaria.php');
    include_once($_SESSION['model_path'].'usuario_caravana.php');
    include_once($_SESSION['model_path'].'usuario_secretaria.php');
    include_once($_SESSION['model_path'].'usuario_grupo.php');
    include_once($_SESSION['model_path'].'grupo.php');
    include_once($_SESSION['model_path'].'seg_punto_rosa.php');

    //Caravanas disponibles
    $caravanas = Caravana::listadoCaravana();

    //Puntos Rosas Disponibles
    $puntos = SegPuntoRosa::listaPuntos(1);
    
    //Secretarias Disponibles
    $secretarias = SegSecretaria::listadoSec();

    //Caravanas del usuario
    $caravanas_usr = NULL;

    //Secretarías del usuario
    $secretarias_usr = NULL;

    //Si editamos el registro
    if(intval($id_edicion)>0){

        //Obtenemos el registro del usuario
        $db->where('id',$id_edicion);
        $usuario = $db->getOne('usuario');                

        //Obtenemos caravanas del usuario
        $caravanas_usr = UsuarioCaravana::caravanasUsuario($id_edicion);        

        //Obtenemos las secretarías del usuario
        $secretarias_usr = UsuarioSecretaria::secUsuario($id_edicion);        
    }

//Obtenemos ID del grupo admin
$id_admin = Grupo::getId('admin');

//Permisos de Admin
$grupos_usuario=UsuarioGrupo::gruposUsr($id_usuario);
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

    <form id='formUsr' method="post" action='save_usuario.php'>
	<table>
        <tr>
			<td>
                <label for="usuario">Usuario</label>
            </td>
			<td>
                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
                <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>" />
      
                
                <input type = 'text' <?php echo(in_array($id_admin,$grupos_usuario))? '':$disabled; ?>id = 'usuario' name = 'usuario' class="usuario" value="<?php echo $usuario['usuario']; ?>" />
            </td>
		</tr>

        <?php if(!isset($id_edicion)){ ?>
        <tr>
			<td>
                <label for="clave">Contrase&ntilde;a</label>
            </td>
			<td><input type = 'password' id = 'clave' name = 'clave'/></td>
		</tr>
        <tr>
        	<td> 
                <label for="clave_conf">Confirma Contrase&ntilde;a</label> 
            </td>
			<td><input type = 'password' id = 'clave_conf' name = 'clave_conf'/></td>
        </tr>
        <?php } ?>        
		<tr>
			<td> 
                <label for="nombres">Nombre(s)</label>
            </td>
			<td><input type = 'text' id = 'nombres' name = 'nombres' class="nombre" value="<?php echo $usuario['nombres']; ?>" /></td>
		</tr>
        <tr>
			<td>
                <label for="paterno">Apellido Paterno</label>
            </td>
			<td><input type = 'text' id = 'paterno' name = 'paterno' class="nombre" value="<?php echo $usuario['paterno']; ?>" /></td>
		</tr>
        <tr>
			<td>
               <label for="materno">Apellido Materno</label>  
            </td>
			<td><input type = 'text' id = 'materno' name = 'materno' class="nombre"value="<?php echo $usuario['materno']; ?>" /></td>
		</tr>
		<tr>
			<td>
                <label for="correo">Correo</label> 
            </td>
			<td><input type = 'text' id = 'correo' name = 'correo' class="email" value="<?php echo $usuario['correo']; ?>" /></td>
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
                        
                    $selected = ($e['valor'] === $usuario['activo'])? "selected" : '' ;
                        
                    ?>                
                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>
                    <?php endforeach; ?>
                </select>
            </td>
             <?php } ?>
        </tr>

        <tr>
            <td>
                 <label for="caravana">Caravana</label> 
            </td>
            <td>                
                <select multiple id="caravana" name="caravana[]" style="height: auto;">
                    <option value=''>Quitar Caravana(s)</option>
                    <?php foreach($caravanas as $c): 
                        $selected = ( in_array($c['id'], $caravanas_usr) )? "selected" : '' ;                        
                    ?>                
                    <option value='<?php echo $c['id'] ?>' <?php echo $selected;?> > 
                        <?php echo $c['descripcion'];?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                 <label for="secretaria">Secretar&iacute;a</label> 
            </td>
            <td>                
                <select multiple id="secretaria" name="secretaria[]" style="height: auto;">
                    <option value=''>Quitar Secretar&iacute;a(s)</option>
                    <?php foreach($secretarias as $s): 
                        $selected = ( in_array($s['id'], $secretarias_usr) )? "selected" : '' ;                        
                    ?>                
                    <option value='<?php echo $s['id'] ?>' <?php echo $selected;?> > 
                        <?php echo $s['nombre'];?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                 <label for="id_punto_rosa">Punto Rosa</label> 
            </td>
            <td>
                <select id="id_punto_rosa" name="id_punto_rosa">
                    <option value=''>Seleccione Punto Rosa</option>
                    <?php foreach($puntos as $p): 
                        $selected = ($p['id'] == $usuario['id_punto_rosa'])? "selected" : '' ;                        
                    ?>                
                    <option value='<?php echo $p['id'] ?>' <?php echo $selected;?> > 
                        <?php echo $p['descripcion'];?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <!--
        <tr>
            <td>
                 <label for="perfil">Perfil</label> 
            </td>
            <td>
                <select id="perfil" name="id_perfil">
                    <option value=''>Seleccione Perfil</option>
                    <?php /* foreach($perfil as $p): 
                        $selected = ($p['id'] == $usuario['id_perfil'])? "selected" : '' ;                        
                    ?>                
                    <option value='<?php echo $p['id'] ?>' <?php echo $selected;?> > <?php echo $p['nombre'];?></option>
                    <?php endforeach; */?>
                </select>
            </td>
        </tr>
        -->
         <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type = 'submit' class="boton confirmation" title="&iquest;Est&aacute; seguro de guardar estos datos?" id = 'enviar' value = 'Enviar' /></td>
		</tr>
	</table>
	</form>