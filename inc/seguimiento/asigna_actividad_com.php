<?php
session_start();//Habilitamos uso de variables de sesión

//Modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_actividad.php');
include_once($_SESSION['model_path'].'seg_colonia.php');
include_once($_SESSION['model_path'].'seg_activacion_com.php');

//Obtenemos ID de mujer
$id_mujeres_avanzando = intval($_GET['id_edicion']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Arreglos
$mujeres_avanzando = NULL;
$actividades = SegActividad::listadoActividad();
$seg_colonias = SegColonia::listadoColonia();
$lista_seg_activacion_com_usr = array();
$lista_fechas_usr = array();
$lista_seg_colonias_usr = array();
$lista_obs = array();

//Si tenemos el ID de una mujer
if($id_mujeres_avanzando){

    //Obtenemos datos de mujer
    $mujeres_avanzando = mujeresAvanzando::get_by_id($id_mujeres_avanzando);

    //Si la mujer existe obtendremos los servicios
    if($mujeres_avanzando != NULL){

        //Listado de apoyos del usuario
        $lista_seg_activacion_com_usr = SegActivacionCom::listadoActivacionCom(null,$id_mujeres_avanzando);

        //Listado de fechas del usuario
        $lista_fechas_usr = SegActivacionCom::listadoFechasUsr($id_mujeres_avanzando);

        //Listado de colonias del usuario
        $lista_seg_colonias_usr = SegActivacionCom::listadoColoniaUsr($id_mujeres_avanzando);

        //Listado de colonias del usuario
        $lista_obs = SegActivacionCom::listadoObsUsr($id_mujeres_avanzando);
    }    
    
}
?>
  
<div class="mensaje">
    Asignaci&oacute;n de Actividad Comunitaria
</div>
    
<?php if($lista != NULL){?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery-ui-1.10.3.custom.min.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida_asign.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>seguimiento/valida.js"></script>

<div align="center">
    <form id='formActCom' method="post" action='save_mujer_com.php'>
    <table class="tablesorter">
        <thead>
            <tr>
                <th>Actividad</th>
                <th>Colonia</th>
                <th>Observaciones</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actividades as $key => $value): ?>
            <tr>
                <td>
                    <input type="hidden" name="id_edicion" value="<?php echo $id_mujeres_avanzando; ?>" />                    
                    <div>       
                        <input id="<?php echo $value['id']?>" 
                            <?php if($lista_seg_activacion_com_usr != NULL) 
                            echo (in_array($value['id'],$lista_seg_activacion_com_usr) === true)? 'checked': ''; ?> 
                        class='val_check' value="<?php echo $value['id']?>" 
                        name="id_seg_actividad[]" 
                        type="checkbox"><?php echo $value['nombre_actividad']?></div>
                    
                </td> 
                <td>
                    <select 
                    id="id_seg_colonia<?php echo $value['id']?>" 
                    name="id_seg_colonia[]"
                    <?php  echo (in_array($value['id'],$lista_seg_activacion_com_usr) === true)? '': 'disabled'; ?>
                    >
                        <option value=''>Seleccione Colonia</option>
                            <?php foreach($seg_colonias as $sc): 
                               $selected = ($sc['id'] == $lista_seg_colonias_usr[$value['id']] )? 'selected' : ''; 
                            ?>
                            <option value='<?php echo $sc['id'] ?>' <?php echo $selected;?> ><?php echo $sc['descripcion'];?></option>
                            <?php endforeach; ?>
                    </select>
                </td>
                <td>
                <textarea cols="10" rows="3" 
                    id ="observaciones<?php echo $value['id']?>" 
                    name="observaciones[]" 
                     <?php echo (in_array($value['id'],$lista_seg_activacion_com_usr) === true)? '': 'disabled'; ?>
                    class="" ><?php echo $lista_obs[$value['id']]; ?></textarea>
                </td>
                <td>
                    <input type = 'text' 
                    id ="fecha_act<?php echo $value['id']?>" 
                    name="fecha_activacion[]" 
                    class="fecha date" required
                     <?php echo (in_array($value['id'],$lista_seg_activacion_com_usr) === true)? '': 'disabled'; ?>
                    value="<?php echo $lista_fechas_usr[$value['id']]; ?>" />
                    <input type="button" class="btnToday"  value="Hoy" 
                    <?php  echo (in_array($value['id'],$lista_seg_activacion_com_usr) === true)? '': 'disabled'; ?>
                    id="btnHoy<?php echo $value['id']?>"  />
                </td>
            </tr>
            <?php endforeach;?>

            <tr>
                <td colspan="3">&nbsp;</td>
                <td><input type = 'submit' 
                                class="boton confirmation" 
                                title="&iquest;Est&aacute; seguro de guardar estos datos?" 
                                id = 'Guardar' 
                                value = 'Guardar' /></td>
            </tr>            

        </tbody>            
    </table>            
    </form>
</div>

<?php }else{ ?>
<div class="mensaje">
    No hay actividades disponibles
</div> 
<?php } ?>
                                      