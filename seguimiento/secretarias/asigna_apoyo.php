<?php session_start();     
    
    //Incluimos cabecera
    include('../../inc/header.php'); 

    //deshailtamos campo usuario
    $disabled = 'disabled="disabled"';    

    //Incluimos modelos    
    include_once($_SESSION['model_path'].'seg_apoyo.php');
    include_once($_SESSION['model_path'].'seg_secretaria_apoyos.php');
    
    //Obtenemos el id_mujeres_avanzando a editar
    $id_seg_secretaria = $_GET['id_edicion'];

    //Si editamos el registro
    if(intval($id_seg_secretaria)>0){

        //Listado de apoyos
        $apoyos = SegApoyo::listadoApoyo();

        //Obtenemos los apoyos de la secretaría
        $sec_apoyo = SegSecretariaApoyos::listaSecApoyoId($id_seg_secretaria);
    }
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<!--
<script lang="JavaScript" type="text/javascript" src="<?php //echo $_SESSION['js_path']?>/<?php //echo $_SESSION['module_name']?>/valida.js"></script>
-->

<div id="principal">
    <div id="contenido">
        <div align="center">

        <form id='formUsr' method="post" action='save_sec_apoyo.php'>
        <table>
            <tr>
                <td>
                    <label for="usuario">Seleccione Apoyo(s)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" name="id_seg_secretaria" value="<?php echo $id_seg_secretaria; ?>" />
                    <div>   
                        <input id="todos_componente" value="0" name="id_componente[]" type="checkbox">
                        Marcar todos los apoyos
                    </div>                

                    <?php foreach ($apoyos as $key => $value): ?>
                    <div>                    
                        <input id="<?php echo $value['id']?>" 
                            <?php if($sec_apoyo != NULL) echo (in_array($value['id'],$sec_apoyo) === true)? 'checked': ''; ?> class='componente' value="<?php echo $value['id']?>" name="id_seg_apoyo[]" type="checkbox">
                        <?php echo $value['nombre']?>
                    </div>
                    <?php endforeach;?>                
                </td>
            </tr>

            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><input type = 'submit' class="boton confirmation" title="&iquest;Est&aacute; seguro de guardar estos datos?" id = 'Guardar' value = 'Guardar' /></td>
            </tr>
        </table>
        </form>

        </div>
    </div>
</div>    

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>