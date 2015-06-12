<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include($_SESSION['inc_path']. 'header.php');

/*Si se va a editar la clave de un usuario, la tomamos como id_edición
caso contrario tomamos el id del usuario logueado*/

if(isset($_GET['id_edicion'])){
    $id_edicion = $_GET['id_edicion'];
}else{
    $id_edicion = $_SESSION['usr_id'];
}

?>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

<div id="principal">
<div id="contenido">    

<h2 class="centro">Aqu&iacute; podr&aacute; cambiar su contrase&ntilde;a</h2>
    <div  align="center">       
        <div>
        <?php        
        if(isset($_GET['r'])){

            //Variable de respuesta
            $respuesta = $_GET['r'];

            unset($_GET['r']);

            //Mensaje respuesta
            list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);            
            ?>

            <div class="mensaje <?php echo $clase; ?>">
                <?php echo $mensaje;?>
            </div>

            <?php } ?>            

        <form id='formCve' method="post" action='save_usuario.php' autocomplete="off" >        
        <table>
        <tr>
            <td><?php if(isset($edita_otro)){echo 'Usuario';}else{echo '&nbsp;';}?></td>
            <td>
            <?php if(isset($edita_otro)){
                include($_SESSION['model_path'].'usuario.php');
                
                $usuarios = Usuario::listadoUsuarios();
                
                ?>
                
                <select id="id_edicion" name="id_edicion">
                    <option value="">Seleccione Usuario</option>
                    <?php foreach($usuarios as $u):?>                
                    <option value='<?php echo $u['id'] ?>' > <?php echo $u['nombre_completo'];?></option>
                    <?php endforeach; ?>
                </select>
                    
            <?php }else{ ?>
            <input type = 'hidden' id = 'id_edicion' name = 'id_edicion' value="<?php echo $id_edicion ?>"/>
            <?php } ?>
            </td>                                 
        </tr>
        <tr>
            <td>
               <label for="clave_actual"> Clave Actual</label>
            </td>
            <td><input type = 'password' id = 'clave_actual' name = 'clave_actual'/></td>
        </tr>
        <tr>
        
            <td>
               <label for="clave"> Clave Nueva</label>
            </td>
            <td><input type = 'password' id = 'clave' name = 'clave'/></td>
        </tr>
        <tr>
            <td>
               <label for="clave_conf"> Confirma Clave Nueva</label>
            </td>
            <td><input type = 'password' id = 'clave_conf' name = 'clave_conf'/></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" id="boton"  value="Cambiar" /></td>
        </tr>
        </table>
        </form>
        </div>    	
    </div>
</div>
</div>
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>