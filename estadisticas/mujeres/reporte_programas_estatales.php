<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');
include_once('../../inc/libs/Permiso.php');

//Incluimos modelo familiares_mujer
include_once($_SESSION['model_path'].'familiares_mujer.php');

//Obtenemos valores de post
$id_c = $_POST['id_caravana'];

if($id_c > 1){    
  $db->where('id_caravana',$id_c);
  $beneficiario_caravana = $db->get('mujeres_avanzando');
}

//Obtemos datos de tipo_lugar
$tipo_lugar = $db->get('tipo_lugar');

?>
<div id="principal">
   <div id="contenido">
      <div>
      <h2 class="centro">Programas SEDIS</h2>
      
       <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"   />
      </div> 
    
      <?php if($respuesta > 0){?>
      
      <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
      
      <?php } ?>

	    <div align="center">
        <form action="reporte_programas_estatales.php" id="carga_archivo" method="post" enctype="multipart/form-data">
        <table>
          <tr>
            <td>
             <label for="id_tipo_lugar">
                Tipo De Importaci&oacute;n
             </label> 
            </td>
            <td>
             <select id="id_tipo_lugar" name="id_tipo_lugar">
                <option value=''>Seleccione Tipo De Importaci&oacute;n</option>
                <?php foreach($tipo_lugar as $t): ?>
                <option value='<?php echo $t['id'] ?>'> <?php echo $t['tipo_importacion'];?></option>
                <?php endforeach; ?>
             </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="tipo_lugar">
                 Caravana
              </label>
            </td> 
            <td id="tipo_lugar">
            <select  id="id_caravana" name="id_caravana">
                <option value=''>Seleccione Caravana</option>
            </select>
            </td>
          </tr> 
          <tr>
            <td>
              &nbsp;
            </td>
             <td>
               <input id="enviar" type="submit" value="Enviar"/>
             </td>
          </tr> 
        </table>
       </form>

        <div class="progress">
            <div class="bar"></div >
            <div class="percent">0%</div >
        </div>

        <div id="status"></div>
        <div id="resultado" align="center"></div>
        <div id="listado"></div>

      </div>
    
    </div>
</div>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.form.min.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'footer.php');
?>