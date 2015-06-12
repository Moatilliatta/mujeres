<?php
session_start();

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos 'Usuario' y 'Menú'
include_once($_SESSION['model_path'].'accion_grupo.php');

//Obtenemos valores de variables
$id_accion = intval($_POST["id_accion"]);
$id_menu_accion_grupo = intval($_POST["id_menu_accion_grupo"]);
$id_accion_grupo = intval($_POST["id_accion_grupo"]);
$id_grupo = intval($_REQUEST["id_grupo"]);
$id_menu = intval($_POST["id_menu"]);
$eliminar = $_POST['eliminar'];

$resp = "";

/*
echo 'id_accion: '.$id_accion.
     ' id_accion_grupo: '.$id_accion_grupo.
     ' id_grupo: '.$id_grupo. 
     ' id_menu: '. $id_menu. 
     ' eliminar '.$eliminar.'<br>';
     */

//Cambiamos estatus de registro a Inactivo

if($eliminar){ 

        //Cambiamos estatus de la acción a Inactivo
        //Si tenemos el id_menu_accion_grupo procedemos a eliminar la acción
        //del grupo en su menú correspondiente
        if($id_menu_accion_grupo){
          $resp = MenuAccionGrupo::activaMenuAccionGrupo($id_menu_accion_grupo);
        }else{
          //Como aún no dispone de menú, se eliminan las acciones del grupo
          //que coincidan con ese id
          $resp = AccionGrupo::activaAccionGrupo($id_accion_grupo);
        }        

    }else if(intval($id_accion)>0){  

        /*Verificamos que el grupo y la acción estén previamente
        en la tabla, sólo camibiaríamos su estatus*/
        //buscamos el registro del usuario      
        $db->where('id_accion',$id_accion);
        $db->where('id_grupo',$id_grupo);
        $existe = $db->getOne('accion_grupo');    

       /*Si encontramos un registro con la acción y el grupo
        cambiamos su estatus a Activo, caso contrario insertamos
        nuevo registro*/

        if($existe['id']){
  			 //Cambiamos estatus de la acción a Inactivo
            $db->where('id',$existe['id']);

            //datos a actualizar
            $updateData = array('activo' => 1);

            if(!$db->update('accion_grupo',$updateData)){

                //'Error al guardar, NO se guardo el registro'
                $resp = 3; 

            }else{

                //Campos guardados correctamente
                $resp = 1;                                            

            } 

    	}else if($id_accion){//Para hacer un nuevo registro, necesita

           //Insertamos registro (y tambien agregamos a menu_accion_grupo)
           $resp = AccionGrupo::saveAccionGrupo($id_grupo,$id_accion);                      
    	}

    }

    //consulta de modulos
    $db->where('activo',1);
    $modulo= $db->get('modulo');

    //Obtenemos el listado de las acciones ligadas a un grupo
    list($lista,$p) = AccionGrupo::listaAccionGrupo($id_grupo); 

    //Obtenemos listado de menúes activos
    $db->where('activo',1);
    $menu= $db->get('menu');

    //Obtenemos listado de menúes activos
    $db->where('id',$id_grupo);
    $grupo_seleccionado= $db->getOne('grupo');

    //Mensaje respuesta
    list($mensaje,$clase) = Permiso::mensajeRespuesta($resp);            

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
$("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
  <div id="contenido">
    <h2 class="centro">Listado de acciones del grupo  '<?php echo $grupo_seleccionado['nombre']; ?>' </h2>

   <?php

   //Si enviamos respuesta

   if($resp){ ?>

      <div class="mensaje <?php echo $clase ?>">

       <?php 

       //Imprimimos mensaje
       echo $mensaje.' Recuerde que despu&eacute;s de asignar una acci&oacute;n al grupo, debe asignarla a un <a href="../menu/lista_menu.php">men&uacute; para que pueda ser usada.</a>';
       ?>

       </div>
<?php } ?>

<div id="page_list">     
    <form id='formGpo_Acc' method="post" action='lista_accion_grupo.php'>
    <table>
    <tr>
        <td><label for="id_modulo">M&oacute;dulos</label></td>
        <td>
            <select id="id_modulo" name="id_modulo">
                <option value='0'>Seleccione M&oacute;dulo</option>
                <?php foreach($modulo as $m ): ?>
                <option value='<?php echo $m['id'] ?>'> 
                    <?php echo $m['descripcion'];?></option>
               <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="submodulo">Subm&oacute;dulos</label></td>
        <td id="submodulo">&nbsp;</td>
    </tr>
    <tr>
        <td><label for="accion">Acciones</label></td>
        <td id="accion">&nbsp;</td>         
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
             <input id="id_grupo" type="hidden" name="id_grupo" value="<?php echo $id_grupo?>" />
             <input id="id_accion_grupo" type="hidden" name="id_accion_grupo" value="<?php echo $id_accion_grupo?>" />
             <input type="submit"  value="Guardar" />
        </td>         
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
        <th>M&oacute;dulo</th>
        <th>Subm&oacute;dulo</th>
        <th>Men&uacute;</th>
        <th>Acciones asociada</th>          
        <th>Descripci&oacute;n</th>        
        <th>Eliminar</th>
    </thead>
    <tbody>
    <?php foreach($lista as $l ): ?>    
    <tr>
      <td><?php echo $l['desc_modulo']; ?></td>
      <td><?php echo $l['desc_submodulo']; ?></td>
      <td><?php echo $l['nombre_menu']; ?></td>
      <td><?php echo $l['nombre_accion']; ?></td>
      <td><?php echo $l['desc_accion']; ?></td>      
      <td>
      <form id='principal' method="post" action='lista_accion_grupo.php'>      
        <input type="hidden" name="id_menu_accion_grupo" value="<?php echo $l['id_menu_accion_grupo']?>" />
        <input type="hidden" name="id_accion_grupo" value="<?php echo $l['id']?>" />
        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo?>" />
        <input type="hidden" name="id_accion" value="<?php echo $id_accion?>" />
        <input type = 'submit'  id = 'enviar' name="eliminar" value = 'Eliminar' />
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