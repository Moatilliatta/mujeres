<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include_once('../../inc/header.php');    

//Incluimos modelo 'Usuario'
include_once($_SESSION['model_path'].'usuario.php');

//Valores de la búsqueda
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];

//Obtenemos listado de usuarios
list($lista,$p) = Usuario::listaUsuarios($busqueda,$tipo_filtro);
//imprimos respuesta en caso de enviarse
if($respuesta !=null){
    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
}


 if($lista == NULL){
   //No existen registros
   list($mensaje,$clase) = Permiso::mensajeRespuesta(8);
}

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
$("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
    <div id="contenido">
    <div class="centro">       
        <div  align="center">
        <form id='formbusqueda' method="get" action='lista_usuario.php'>        
        <table>
        <tr>
         <td>
             <label for="tipo_filtro"> Buscar Por:</label>
         </td>
            <td>
                <select id="tipo_filtro" name="tipo_filtro">
                    <option value="usuario">Usuario</option> 
                    <option value="nombres">Nombre completo</option>                                        
                </select>
            </td>
               <td><label for="busqueda">Palabra Clave</label>
            </td>
            <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
            <td><input type="submit" id="boton"  value="Buscar" /></td></td>
        </tr>
        </table>
        </form>
        </div>    	
    </div>
    <h2 class="centro">Listado de Usuarios</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>">
        <?php echo $mensaje;?>
    </div>            
                            
     <?php } ?>
   
   <div id="page_list" align="center">
     <p>    
    <?php if(array_key_exists('alta_usuario',$central)){ ?>    
    <p><a  href="alta_usuario.php">Agregar</a></p> 
    <?php } ?>
    </p>
    <p>
    <?php
     //Si tenemos listado
   if($lista != NULL){                
       // Listado de páginas del paginador
       echo $p->display();
       }
    ?>
    </p>
    <table class="tablesorter">
    <thead>
        <th>Usuario</th>
        <th>Nombre Completo</th>
        <th>Perfil</th>
        <th>Estatus</th>
        <th>Fecha Ingreso</th>
    <!--<th>&Uacute;ltima IP</th>-->
        <th>Fecha Creado</th>
    <!--<th>Fecha Modif.</th>-->
        <th>Opciones</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['usuario']; ?></td>
            <td><?php echo $l['nombre_completo'];?></td>
            <td><?php echo $l['nombre_perfil'];?></td>
            <td><?php echo $l['estatus'];?></td>
            <td><?php echo $l['fecha_ingreso'];?></td>
        <!--<td><?php //echo $l['ip_ultimo_ing'];?></td>-->
            <td><?php echo $l['fecha_creado'];?></td>
            <!--<td><?php //echo $l['fecha_mod'];?></td>-->
            <td>
            <?php if(array_key_exists('lista_usuario_grupo',$central)){ ?>
            <div title="Grupo" class="ui-state-default ui-corner-all lista">
                <a class="ui-icon ui-icon-person" href="lista_usuario_grupo.php?id_usuario=<?php echo $l['id']; ?>">Grupo</a>
            </div>
            <?php } ?>
            <?php if($l['activo']==1){ ?>
            <?php if(array_key_exists('edita_usuario',$central)){ ?>
            <div title="Editar" class="ui-state-default ui-corner-all lista">
                <a class="ui-icon ui-icon-note" href="edita_usuario.php?id_edicion=<?php echo $l['id']; ?>">Editar</a>
            </div>
            <?php } ?>
            <?php }?> 
            
            <?php if(array_key_exists('activa_usuario',$central)){ ?>
            <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
               <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> acci&oacute;n?" 
                       href="activa_usuario.php?id_activo=<?php echo $l['id']; ?>"></a>
            </div>
            <?php } ?>
            
           
            
            <div  title="Cambiar Clave" class="ui-state-default ui-corner-all lista">
             <?php if($l['id'] == $_SESSION['usr_id']){ ?>
           
            <a class="ui-icon ui-icon-locked" href="clave_usuario.php?id_edicion=<?php echo $l['id']; ?>">Cambiar Clave</a>
             <?php }else{?>
               
            <a class="ui-icon ui-icon-locked" href="cambia_clave.php?id_edicion=<?php echo $l['id']; ?>">Cambiar Clave</a>     
             <?php }  ?>
             
             
            </div>
             
              
          
           
            
            </div>
            
          
            </td>
        </tr>
        <?php endforeach; ?>    
         
    </tbody>
    </table>            
</div>
</div>
</div>


<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>