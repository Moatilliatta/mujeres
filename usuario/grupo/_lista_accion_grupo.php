<?php
session_start();

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelo 'accion_grupo'
include_once($_SESSION['model_path'].'accion_grupo.php');

//Obtenemos valores de variables
$id_accion = intval($_POST["id_accion"]);
$id_accion_grupo = intval($_POST["id_accion_grupo"]);
$id_grupo = intval($_REQUEST["id_grupo"]);
$id_menu = intval($_POST["id_menu"]);
$eliminar = $_POST['eliminar'];

/*
echo 'id_accion: '.$id_accion.
     ' id_accion_grupo: '.$id_accion_grupo.
     ' id_grupo: '.$id_grupo. 
     ' id_menu: '. $id_menu. 
     ' eliminar '.$eliminar.'<br>';
     */

//Cambiamos estatus de registro a Inactivo

if($eliminar == 'SI'){ 
        //Cambiamos estatus de la acción a Inactivo
        $resp = AccionGrupo::activaAccionGrupo($id_accion_grupo);

    }else if(intval($id_accion)>0 || intval($id_accion_grupo) > 0){  

        /*Verificamos que el grupo y la acción estén previamente
        en la tabla, sólo camibiaríamos su estatus*/
        //buscamos el registro del usuario
        
        if($id_accion){
            $db->where('id_accion',$id_accion);
            $db->where('id_grupo',$id_grupo);
            $existe = $db->getOne('accion_grupo');    
        }else{ //Si no tenemos acción buscamos por el id de la tabla accion_grupo
            $db->where('id',$id_accion_grupo);
            $existe = $db->getOne('accion_grupo');
        }
        
       /*Si encontramos un registro con la acción y el grupo
        cambiamos su estatus a Activo, caso contrario insertamos
        nuevo registro*/

        if($existe['id'] > 0){
            
  			 //Cambiamos estatus de la acción a Inactivo
            $db->where('id',$existe['id']);

            //datos a actualizar
            $updateData = array('activo' => 1);

            if(!$db->update('accion_grupo',$updateData)){
                //'Error al guardar, NO se guardo el registro'
                $msg_no = 3;
                 
            }else{
                    //Campos guardados correctamente
                $msg_no = 1;                                                                              
            } 
            
            //Si actualiaremos el menú
            if($id_menu > 0){
                  
                  //Buscamos en la tabla menu_accion_grupo               
                  $db->where('id_accion_grupo',$id_accion_grupo);
                  $db->where('id_menu',$id_menu);
                  $existe = $db->getOne('menu_accion_grupo');
                  
                  //Si existe registro, lo actualizamos caso contrario lo creamos
                  if($existe){
                     
                     //Actualizamos en menu_accion_grupo
                    $db->where('id_accion_grupo',$existe['id']);
                
                    //datos a actualizar
                    $updateData = array('id_menu' => $id_menu);
                    
                        if(!$db->update('menu_accion_grupo',$updateData)){
                            //'Error al guardar, NO se guardo el registro'
                            $msg_no = 3;
                                 
                        }else{
                            //Campos guardados correctamente
                            $msg_no = 1;
                        }
                  }else{
                     //Incluimos modelo 'accion_grupo'
                     include_once($_SESSION['model_path'].'menu_accion_grupo.php');
                    
                     //Creamos registro
                     $resp = MenuAccionGrupo::saveMenuAccionGrupo($id_accion_grupo,$id_menu);
                  }                                    
                                        
            }
            

    	}else if($id_accion){//Para hacer un nuevo registro, necesitamos el $id_accion

           //Insertamos registro (y tambien agregamos a menu_accion_grupo)
           $resp = AccionGrupo::saveAccionGrupo($id_grupo,$id_accion,null,$id_menu);                      

    	}

    }

    //consulta de modulos
    $db->where('activo',1);

     $modulo= $db->get('modulo');

    //Obtenemos el listado de las acciones ligadas a un grupo
    $lista = AccionGrupo::listaAccionGrupo($id_grupo);
    
    //Obtenemos listado de menúes activos
    $db->where('activo',1);
    $menu= $db->get('menu');
    
    //Obtenemos listado de menúes activos
    $db->where('id',$id_grupo);
    $grupo_seleccionado= $db->getOne('grupo');

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>

<div id="form_general">
<p>Listado de acciones del grupo '<?php echo $grupo_seleccionado['nombre']; ?>'</p>
   <div>
   <?php
   //Si enviamos respuesta
   if(isset($_GET['r'])){

        //Variable de respuesta
       $respuesta = $_GET['r'];

       //Mensaje respuesta
       list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);                 
   }          
   ?>
</div>

<?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
<?php } ?>

<div id="page_list" align="center"> 
        
    <table>
    <thead>
        <th>Acciones asociada</th>          
        <th>Descripci&oacute;n</th>
        <th>Subm&oacute;dulo</th>
        <th>Men&uacute; Asociado</th>
        <th>Eliminar</th>
    </thead>
    <tbody>
    <?php foreach($lista as $l ): ?>    
    <tr>
    
      <td><?php echo $l['nombre_accion']; ?></td>
      <td><?php echo $l['desc_accion']; ?></td>
      <td><?php echo $l['desc_submodulo']; ?></td>
      <td>
      <form id='formUsr' method="post" action='lista_accion_grupo.php'>
            <select id="id_menu" name="id_menu">
                <option value='0'>Seleccione Men&uacute;</option>
                <?php foreach($menu as $m ): ?>
                
                <?php if($m['id'] == $l['id_menu']){
                            $selected = "selected";
                    }else{
                            $selected = "";
                    }?>                                
                <option value='<?php echo $m['id'] ?>'  <?php echo $selected;?> > 
                    <?php echo $m['nombre'];?></option>
               <?php endforeach; ?>
            </select>
            <select id="eliminar" name="eliminar">
            <option value=''>Eliminar?</option>
                <option value='NO'>No</option>
                <option value='SI'>S&iacute;</option>
            </select>
        <input type="hidden" name="id_accion_grupo" value="<?php echo $l['id']?>" />
        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo?>" />
        <input type="hidden" name="id_accion" value="<?php echo $id_accion?>" />
        <input type = 'submit' id = 'enviar' value = 'Actualizar' />
        </form>    
      </td>       
    </tr>    
    <?php endforeach; ?>
    </tbody>
    </table>
    

    <form id='formUsr' method="post" action='lista_accion_grupo.php'>
    <table>
    <tr>
        <td>M&oacute;dulos</td>
        <td>
            <select id="id_modulo" name="id_modulo">
                <option value='0'>Seleccione M&oacute;dulo</option>
                <?php foreach($modulo as $m ): ?>
                <option value='<?php echo $m['id'] ?>'> 
                    <?php echo $m['nombre'];?></option>
               <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>SubM&oacute;dulos</td>
        <td id="submodulo">&nbsp;</td>
    </tr>
    <tr>
        <td>Acciones</td>
        <td id="accion">&nbsp;</td>         
    </tr>
    <tr>
        <td>Men&uacute;</td>
        <td>
            <select id="id_menu" name="id_menu">
                <option value='0'>Seleccione Men&uacute;</option>
                <?php foreach($menu as $m ): ?>
                <option value='<?php echo $m['id'] ?>'> 
                    <?php echo $m['nombre'];?></option>
               <?php endforeach; ?>
            </select>
        </td>
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
    </div>
</div>
<?php
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>