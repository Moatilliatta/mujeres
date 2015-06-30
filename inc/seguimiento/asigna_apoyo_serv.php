<?php
session_start();//Habilitamos uso de variables de sesión

//Modelos a usar
include_once($_SESSION['model_path'].'usuario_secretaria.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_secretaria_apoyos.php');
include_once($_SESSION['model_path'].'seg_apoyos_serv.php');

//Variable de respuesta
//$respuesta = intval($_GET['r']);

//Obtenemos ID de mujer
$id_mujeres_avanzando = intval($_GET['id_edicion']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Arreglos
$mujeres_avanzando = NULL;
$secretarias = UsuarioSecretaria::listadoSecretariaUsr(null,$_SESSION['usr_id']);

//Listado de apoyos
$lista_seg_secretaria_apoyos = SegSecretariaApoyos::listadoSecApoyo($secretarias);        
//print_r($lista_seg_secretaria_apoyos);

//Si tenemos el ID de una mujer
if($id_mujeres_avanzando){

    //Obtenemos datos de mujer
    $mujeres_avanzando = mujeresAvanzando::get_by_id($id_mujeres_avanzando);

    //Si la mujer existe obtendremos los servicios
    if($mujeres_avanzando != NULL){

        //Listado de apoyos del usuario
        $lista_seg_secretaria_apoyos_usr = SegApoyosServ::listadoApoyoServ(null,$id_mujeres_avanzando);
        //print_r($lista_seg_secretaria_apoyos_usr);

    }    
    
}

?>
<div class="mensaje">Asignar Apoyo/Servicio a Mujer</div>

<?php if($lista != NULL){?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida_asign.js"></script>

<div align="center"> 
    <form id='formApoyoServ' method="post" action='save_mujer_serv.php'>
    <table>
        <tr>
            <td><label for="usuario">Seleccione Apoyo(s)</label></td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="id_edicion" value="<?php echo $id_mujeres_avanzando; ?>" />
                <?php foreach ($lista_seg_secretaria_apoyos as $key => $value): ?>
                <div>       
                    <input id="<?php echo $value['id']?>" 
                    <?php if($lista_seg_secretaria_apoyos_usr != NULL) 
                    echo (in_array($value['id'],$lista_seg_secretaria_apoyos_usr) === true)? 'checked': ''; ?> 
                    class='apoyo' value="<?php echo $value['id']?>" 
                    name="id_seg_secretaria_apoyos[]" 
                    type="checkbox"><?php echo $value['nombre_apoyo']?>
                </div>
                <?php endforeach;?>    
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><input type = 'submit' 
                class="boton confirmation" 
                title="&iquest;Est&aacute; seguro de guardar estos datos?" 
                id = 'Guardar'
                value = 'Guardar' />
            </td>
        </tr>
    </table>
    </form>
</div>

<?php }else{ ?>
<div class="mensaje">
    No hay talleres disponibles
</div> 
<?php } ?>