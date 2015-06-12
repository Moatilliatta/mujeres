<?php
session_start();

//Incluimos cabecera
include('../../inc/header.php');

include_once($_SESSION['model_path'].'callcenter.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//traemos los status
$estatus = $db->get('status_llamada');

//Obtenemos id_mujeres_avanzando
$id_mujeres_avanzando = $_GET['id_mujeres_avanzando'];

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

<div id="principal">
   <div id="contenido">
   
   
    <h2 class="centro">Alta de Mujer a Callcenter</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div class="centro">       
        <div align="center">

    <?php
        //Si el registro no es exitoso mostramos el formulario de usuario 
        if($id_mujeres_avanzando > 0){
          include_once($_SESSION['inc_path'] . "callcenter/datos_mujer.php");
        }
    ?>

  <script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

  <form id='formCaravana' method="post" action='save_callcenter.php'>         
  <table> 
    <tr>
      <td>
        <input type="hidden" name="id_mujeres_avanzando" value="<?php echo $id_mujeres_avanzando; ?>" />
        <input type="hidden" name="id_callcenter" value="<?php echo $id_callcenter; ?>" />
        <input type="hidden" name="id_callcenter_grupo" value="<?php echo $callcenter['id_callcenter_grupo']; ?>" />        
        <input type="submit" value="Guardar"  />
      </td>
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
