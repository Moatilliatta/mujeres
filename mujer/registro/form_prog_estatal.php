<?php
session_start();
include('../../inc/header.php');
include_once($_SESSION['model_path'].'beneficiario_prog_estatal.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');
$id_actualizar=$_POST['actualizar'];

//echo $id_actualizar;
//exit;
if($id_actualizar > 0){
  
//Buscamos Servicios
list($msg_no,$arreglo) = BeneficiarioProgEstatal::searchprogEstatal();

}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<div id="principal">
   <div id="contenido">
    <div>
    <h2 class="centro">Alta de Beneficiario</h2>
    
    <h1>Estadistica: <?php //echo $msg_no.'<br>';
                          echo $arreglo['total_registrados'].'<br>'; 
                          echo $arreglo['total_incompletos'].'<br>';
                          echo $arreglo['errores'].'<br>';
                          ?>
                          
                          </h1>
    
     <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"   />
    </div> 
	<form id='form_prog_estatal' method="post" action='form_prog_estatal.php'>
     <tr>
       <td>
         <input type = 'text' id = 'actualizar' name = 'actualizar'  value="<?php //echo $mujeres_avanzando['calle']; ?>" /> 
       </td>
     </tr>
      <tr>
        <td>
            <input type="submit" value="Guardar"  />
        </td>
    </tr>   
	</form>
    </div>
</div>   
