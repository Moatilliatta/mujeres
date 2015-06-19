<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelo familiares_mujer
include_once($_SESSION['model_path'].'familiares_mujer.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);
//obtenemos datos de caravana
$db->where ('activo', 1);
$caravana = $db->get('caravana');
//Obtemos datos de tipo_lugar
$tipo_lugar = $db->get('tipo_lugar');
//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Obtenemos los totales despues de la carga
$totales = (isset($_SESSION['totales']))? $_SESSION['totales'] : NULL ;

//Arreglo de posibles personas que NO fueron registradas en el sistema
$posibles = array();

$entrevista_noc = NULL;

//Si los totales no son nulos
if($totales != NULL){
  
  //Obtenemos arreglo
  $entrevista_noc = $totales['id_entrevista_noc'];

  //Verificamos que tenemos entrevistas que no coinciden
  if($entrevista_noc!= NULL){
    
    //Obtenemos datos de la tabla de familiares mujer
    $posibles = FamiliaresMujer::listaPosiblesFam($entrevista_noc);

  }

}

unset($_SESSION['totales']);
 
?>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>
<div id="principal">
   <div id="contenido">
    <div>
    <h2 class="centro">Subir Archivo</h2>
     <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"   />
    </div> 
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
    <?php if($totales != NULL){?>

      <?php if($totales['total_duplicados']){ ?>
      <div class="mensaje adv_msg">
        Hubo <?php echo $totales['total_duplicados']; ?> registros que previamente habían sido 
        agregados y no fueron tomados en cuenta en esta carga.
      </div>
      <?php } ?>

    <table class="tablesorter">
     <thead>
    <tr>
      <th>
         Descripci&oacute;n
      </th>
      <th>
       Total
      </th>
    </tr>
    </thead>
    <tbody>
      <tr>
      <td>Total Encuestados:</td>
      <td><?php echo $totales['total_encuestados'];?></td>
    </tr>
    <tr>
       <td>Total Encuestas Completas:</td> 
       <td><?php echo $totales['total_enc_completo'];?></td> 
    </tr> 
    <tr>
       <td>Total Encuestas Incompletas:</td> 
       <td><?php echo $totales['total_enc_inc'];?></td>
    </tr> 
    <tr>
      <td>Total de Personas Registradas:</td>
      <td><?php echo $totales['total_familiares'];?></td>
    </tr>
    
    <?php if($totales['total_prog_mac'] > 0){ ?>
    <tr>
      <td>Total Encuestados en Programa MAC:</td> 
      <td><?php echo $totales['total_prog_mac'];?></td>
    </tr>
    <?php } ?>

    <?php if($totales['total_prog_map'] > 0){ ?>
    <tr>
      <td>Total Encuestados en Programa MAP:</td>
      <td><?php echo $totales['total_prog_map'];?></td>
    </tr>
    <?php } ?>

    <?php if($totales['total_prog_mas'] > 0){ ?>
    <tr>
      <td>Total Encuestados en Programa MAS:</td>
      <td><?php echo $totales['total_prog_mas'];?></td>
    </tr>
    <?php } ?>

    <?php if($totales['total_prog_sol'] > 0){ ?>
    <tr>
      <td>Total Encuestados en Programa SOL:</td> 
      <td><?php echo $totales['total_prog_sol'];?></td>
    </tr>
    <?php } ?>

    <?php if($totales['total_prog_pio'] > 0){ ?>
    <tr>
      <td>Total Encuestados en Programa PIO:</td> 
      <td><?php echo $totales['total_prog_pio'];?></td>
    </tr>
    <?php } ?>

    <tr>
       <td>Total Duplicados:</td>
       <td><?php echo $totales['total_duplicados']?></td>
    </tr>
    <tr>
      <td>Total Entrevistas no coinciden:</td>
      <td><?php echo $totales['total_no_coinciden']?></td>
    </tr>
   <tr>
     <td>Total de Encuestados Registrados:</td> 
      <td><?php echo $totales['total_registrados']?></td>
   </tr>
   <tr>
     <td>Total de Grado de Inseguridad Severo:</td> 
      <td><?php echo $totales['total_severa']?></td>
   </tr><tr>
     <td>Total de Grado de Inseguridad Moderado:</td> 
      <td><?php echo $totales['total_moderada']?></td>
   </tr><tr>
     <td>Total de Grado de Inseguridad Leve:</td> 
      <td><?php echo $totales['total_leve']?></td>
   </tr>
   <tr>
     <td>Total de Grado de Inseguridad Seguro:</td> 
      <td><?php echo $totales['total_segura']?></td>
   </tr>
   
   <?php if($totales['total_otra'] > 0){?>
   <tr>
     <td>Total de Otro Grado de Inseguridad:</td> 
      <td><?php echo $totales['total_otra']?></td>
   </tr>
   <?php }?>
    </tbody>
  </table>

    <?php if($entrevista_noc){?>

    <div class="mensaje adv_msg">
    Hay <?php echo count($entrevista_noc) ?> entrevista(s) que no coincide(n).
    Los folios son los siguientes: <?php echo implode(array_keys($entrevista_noc), ','); ?> 
    </div>
  
      <?php if($posibles != NULL){?>

      <div class="mensaje info_msg">
        Se muestran a continuaci&oacute;n las personas ligadas a cada uno de los folios.        
      </div>

      <table class="tablesorter">
      <thead>
      <tr>
        <th colspan="3">Entrevistas que no coincidieron</th>
      </tr>
      <tr>
        <th>ID entrevista</th>
        <th>Folio</th>
        <th>Nombre</th>
      </tr>      
      </thead>
      <tbody>
      <?php 
      foreach ($posibles as $key => $value):?>
      <tr>
        <td><?php echo $value['id_entrevista'];?></td>
        <td><?php echo array_search($value['id_entrevista'], $entrevista_noc);?></td>
        <td><?php echo $value['nombres'].' '.$value['paterno'].' '.$value['materno'];?></td>
      </tr>
      <?php endforeach;?>          
      </tbody>
      </table>

      <?php }else{ ?>
        <div class="mensaje adv_msg">
        No se encontraron familiares ligados a los folios. Revisar en el excel los folios que no
        coincidieron.
        </div>
      <?php } ?>
  

    <?php } ?>

    <?php } ?>

	<div align="center">                
       <form action="carga_archivo.php" id="carga_archivo" method="post" enctype="multipart/form-data">
        <table>
          <tr>
            <td>
             <label>
                Tipo De Importación
             </label> 
            </td>
            <td>
             <select id="id_tipo_lugar" name="id_tipo_lugar">
                <option value=''>Seleccione Tipo De Importación</option>
                <?php foreach($tipo_lugar as $t): ?>
                <option value='<?php echo $t['id'] ?>'> <?php echo $t['tipo_importacion'];?></option>
                <?php endforeach; ?>
             </select>
            </td>
          </tr>
          <tr>
            <td>
              <label>
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
              <label>No. de Visita</label>
            </td>
            <td>
              <input type="text" id='visita' name="visita" class="digits" maxlength="2" size="2" value="1" />
            </td>
          </tr>

            <tr>
            <td><label>Archivo Excel (.xlsx)</label></td>
            
            <td>
             <input type="file" id="file" name="archivo"/>
             <input id="enviar" type="submit" value="Enviar"/>
             </td>
            </tr> 
        </table>
       </form>
        <p>
         Seleccione un archivo .XLSX desde su computadora para importar encuestas ENHINA. Este archivo es proporcionado por el Sistema de Informaci&oacute;n de Inseguridad Alimentaria de DIF Nacional.
        Recuerde que &uacute;nicamente se importar&aacute;n los registros capturados desde Caravanas (MAC) y Puntos Rosas (MAP) que est&eacute;n marcadas como COMPLETAS.                        
         </p>        
      <div id="spinner" class='mensaje_carga'>
            La carga del archivo puede tomar algunos minutos. Por favor, sea paciente.
          <img src="<?php echo $_SESSION['css_path'] ?>/img/loader_sug.gif">
      </div>
    </div>
    </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>