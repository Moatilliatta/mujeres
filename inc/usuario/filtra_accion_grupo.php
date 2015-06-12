<?php
session_start();
include ($_SESSION['inc_path'].'conecta.php'); 
include_once($_SESSION['model_path'].'accion_grupo.php');

//Obtenemos valores
$id_grupo = $_POST['id_grupo'];
$id_menu = $_POST['id_menu'];

//Obtenemos listado de acciones por grupo
$lista = AccionGrupo::listadoAccionGrupo($id_grupo,$id_menu);
?>
    <tr>            
        <td>
            <select id="id_accion_grupo" name="id_accion_grupo">
                <option value="">Seleccione Acci&oacute;n</option>
                <?php foreach($lista as $l):?>         
                <option value='<?php echo $l['id'] ?>'> <?php echo $l['desc_accion'] .' ('.$l['nombre_accion'].')';?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>