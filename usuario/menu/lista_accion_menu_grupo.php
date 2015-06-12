<?php
session_start();
//Incluimos cabecera
include('../../inc/header.php');
//Incluimos modelo 'Usuario'
include_once($_SESSION['model_path'].'menu_accion_grupo.php');
//Obtenemos valores de variables
$id_menu_accion_grupo = intval($_POST["id_menu_accion_grupo"]);
$id_accion_grupo = intval($_POST["id_accion_grupo"]);
$id_menu = intval($_REQUEST["id_menu"]);
$eliminar = $_POST['eliminar'];

//echo 'id_accion_grupo: '.$id_accion_grupo.' id_menu: '.$id_menu.' id_eliminar:'.$eliminar.'<br>';
//Cambiamos estatus de registro a Inactivo

if($eliminar != NULL){ 
    //Cambiamos estatus de la acción a Inactivo
    $respuesta = MenuAccionGrupo::activaMenuAccionGrupo($id_menu_accion_grupo);
}else if(intval($id_accion_grupo)>0){  

        /*Verificamos que el grupo y la acción estén previamente
        en la tabla, sólo camibiaríamos su estatus*/

        //buscamos el registro del usuario
        $db->where('id_accion_grupo',$id_accion_grupo);
        $db->where('id_menu',$id_menu);
        $existe = $db->getOne('menu_accion_grupo'); 

       /*Si encontramos un registro con la acción y el grupo
        cambiamos su estatus a Activo, caso contrario insertamos*/
        if($existe['id']){
            //Cambiamos estatus de la acción a Inactivo
            $db->where('id',$existe['id']);
            
            //datos a actualizar
            $updateData = array('activo' => 1);

            if(!$db->update('menu_accion_grupo',$updateData)){
                //'Error al guardar, NO se guardo el registro'
                $msg_no = 3; 
            }else{
                //Campos guardados correctamente
                $msg_no = 1;                    
            } 

    	}else{
            
            //echo $id_accion_grupo.' '.$id_menu;

            //Arreglo con los datos a guardar
            $menu_accion_grupo = array('id_accion_grupo' => $id_accion_grupo,
                                        'id_menu' => $id_menu);

           //Insertamos registro 
           $respuesta = MenuAccionGrupo::saveMenuAccionGrupo($menu_accion_grupo);
        }
 }

    

//consulta de modulos
$db->where('activo',1);
$grupo= $db->get('grupo');


//hacemos un inner join para mostrar una tabla con el nombre de la accion, descripcion y el id del grupo_accion
  list($lista,$p) = MenuAccionGrupo::listaMenuAccionGrupo($id_menu);
    
  $Menu = $db->where('id',$id_menu)
             ->getOne('menu');
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
    <h2 class="centro">Listado de Acciones del Men&uacute; "<?php echo $Menu['nombre']; ?>"</h2>
    
    
    <?php
           //Mensaje respuesta
           list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);         
       ?>
        
       <?php
       //Si enviamos respuesta
       if($respuesta > 0){ ?>
    
        <div class="mensaje <?php echo $clase ?>">
            <?php echo $mensaje;?>
        </div>
    
        <?php } ?>        
    
    <div id="page_list"> 
    
        <form id='formAcc_Mnu' method="post" action='lista_accion_menu_grupo.php'>
        <table>
        <tr>
            <td><label>Grupos</label></td>
            <td>
                <select id="id_grupo" name="id_grupo">
                    <option value='0'>Seleccione Grupo</option>
    
                    <?php foreach($grupo as $g ): ?>
    
                    <option value='<?php echo $g['id'] ?>'> 
    
                        <?php echo $g['nombre'];?></option>
    
                   <?php endforeach; ?>
                </select>
            </td>
        </tr>
    
        <tr>
            <td><label>Acci&oacute;n Grupo</label></td>
            <td id="accion_grupo">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                 <input id="id_menu" type="hidden" name="id_menu" value="<?php echo $id_menu?>" />
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
                <th>Acci&oacute;n</th>    
                <th>Descripci&oacute;n</th>
                <th>Grupo</th>
                <th>&nbsp;</th>
            </thead>
            <tbody>
            <?php foreach($lista as $l ): ?>
             <tr>
                 <td><?php echo $l['desc_modulo']; ?></td>
                 <td><?php echo $l['desc_submodulo']; ?></td> 
                 <td><?php echo $l['nombre_accion']; ?></td>
                 <td><?php echo $l['desc_accion']; ?></td>
                 <td><?php echo $l['nombre_grupo']; ?></td>
                 <td> 
                    <form id='formUsr' method="post" action='lista_accion_menu_grupo.php'>
                        <input type="hidden" name="id_menu_accion_grupo" value="<?php echo $l['id_menu_accion_grupo']?>" />
                        <input type="hidden" name="id_menu" value="<?php echo $id_menu?>" />
                        <input type="hidden" name="id_accion_grupo" value="<?php echo $id_accion_grupo?>" />
                        <input type = 'submit' id = 'enviar' name = 'eliminar'  value = 'Eliminar' />
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

