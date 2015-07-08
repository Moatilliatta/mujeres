<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include_once('../../inc/header.php');

//Incluimos modelo 'Usuario'
include_once($_SESSION['model_path'].'usuario_grupo.php');

//obtenemos la varible id_usuario
$id_usuario=$_REQUEST["id_usuario"];


//update desactivar grupo a usuario
if(intval($_REQUEST["id_usuario_grupo"])>0){
        
        //Obtenemos el id_usuario_grupo
       $id_usuario_grupo = $_REQUEST["id_usuario_grupo"];

        //Editamos registro
        $resp = UsuarioGrupo::activaUsuarioGrupo($id_usuario_grupo);
        
        //echo $resp;
    }
    
    //insert para actualizar registros 
     if(intval($_POST["id_grupo"])>0){  
       
       //Obtenemos el id_grupo
       $id_grupo =$_POST["id_grupo"];
       
       //Guardamos registro
        $resp = UsuarioGrupo::saveUsuarioGrupo($id_grupo,$id_usuario);                   
     }

    //Obtenemos los grupos que el usuario NO tiene asiganados para evitar duplicados
    $grupo = UsuarioGrupo::listaFiltraGrupo($id_usuario);
    
    //Obtenemos listado de Grupos que tiene el usuario
    list($lista,$p) = UsuarioGrupo::listaUsuarioGrupo($id_usuario,1);
    
    $db->where('id',$id_usuario);
    $usuario_seleccionado= $db->getOne('usuario');
    
                //sacamos solo el la primer posiscion de la arreglo
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
$("table").tablesorter({widgets: ['zebra']});
});
</script>
<div id="principal">
 <div id="contenido">
    
    <h2 class="centro">Listado de grupos de Usuarios  '<?php echo $usuario_seleccionado['nombres'].' '.$usuario_seleccionado['paterno'].' '.$usuario_seleccionado['materno'];?>' </h2>
     <div>
        <?php
         //Si enviamos respuesta
         if(isset($_GET['r'])){
            //Variable de respuesta
            $respuesta = $_GET['r'];
            
            //Mensaje respuesta
            list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);            

            //Registro exitoso, caso contrario mostramos el formulario de usuario 
            if($respuesta != 1){

            }
         }
        ?>

        <?php if($respuesta > 0){?>
    
          <div class="mensaje <?php echo $clase ?>"><?php echo $mensaje;?></div>
    
        <?php } ?>

     </div>

      <div id="page_list"> 
      <form id='formUsr_Gpo' method="post" action='lista_usuario_grupo.php'>
      <table> 
        <tr>
            <td><label>Grupos</label></td>
            <td>
                <select id="id_grupo" name="id_grupo">
                    <option value='0'>Seleccione Grupo</option>
                    <?php foreach($grupo as $g):?>
                    <option value='<?php echo $g['id'] ?>'> <?php echo $g['nombre'];?></option>
                    <?php endforeach; ?>
                </select>

            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario?>" />
            </td>

        </tr>
        <tr>
			<td>&nbsp;</td>
			<td><input type = 'submit'  id = 'enviar' name = 'enviar' value = 'Enviar' /></td>
        </tr>
    </table> 
    </form>      
      
      <p align='center'>
        <?php
        //Si tenemos listado
        if($lista != NULL){                
            // Listado de páginas del paginador
            echo $p->display();
        ?>
        </p>
      
      <table class="tablesorter">
        <thead>
        <th>Grupos</th>
        <th>Acci&oacute;n</th>
        </thead>
        
        <tbody>
        <?php foreach($lista as $l ): ?>
        <tr>
        <td><?php echo $l['nombre']; ?></td>
        <td> 
            <form id='principal' method="post" action='lista_usuario_grupo.php'>
                <input type="hidden" name="id_usuario_grupo" value="<?php echo $l['id_usuario_grupo']?>" />
                <input type="hidden" name="id_usuario" value="<?php echo $id_usuario?>" />
                <input type = 'submit'  id = 'enviar' name = 'enviar' value ='Eliminar' />
            </form>
        </td> 
        </tr>
        
        <?php endforeach; ?>
        </tbody>
        </table>
        
        <?php } ?>
        
      </div>
   </div>      
</div>
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>