<?php
session_start();//Habilitamos uso de variables de sesión

$excel = ($_POST['excel'] != NULL)? $_POST['excel']: NULL;

//Imprimimos o no cabecera de excel
if($excel != NULL){
    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");
    //Librería de permisos
    include($_SESSION['inc_path'].'libs/Permiso.php'); 

    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=reporte_cartillas_entregadas.xls");
}else{    
    //Incluimos cabecera
    include('../../inc/header.php');    
}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'seg_no_enc.php');
//Variable de respuesta
$respuesta = intval($_GET['r']);
//obtenemos datos de caravana
//Obtemos escolaridad
$db->where ('activo', 1);
$caravana = $db->get('caravana');
//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Obtenemos los totales despues de la carga
$totales = (isset($_SESSION['totales']))? $_SESSION['totales'] : NULL ;

//obtenemos a las personas no encontradas en la base de datos
$no_econtrado = (isset($_SESSION['no_encontrado']))? $_SESSION['no_encontrado'] : NULL ;
//Arreglo de posibles personas que NO fueron registradas en el sistema
$posibles = array();

$entrevista_noc = NULL;

//Si los totales no son nulos
if($totales != NULL){
  
  //Obtenemos arreglo
  $entrevista_noc = $totales['id_entrevista_noc'];

}

unset($_SESSION['totales']);


if(!isset($_GET['r'])){

//unset($_SESSION['no_encontrado']);
    
}



if( $no_econtrado !=null){
   

$cadena = implode(",",$no_econtrado);


if($excel == NULL){
    
list($lista,$p) = SegNoEnc::listadoPaginado($cadena);  
    
}else{


$lista =SegNoEnc::listadoArray($cadena);
//print_R($lista);
//exit;
}
    
    
    //echo $cadena;
    //exit;
  
  /*
    
    $sql = '
    SELECT
    sn.folio, 
    sn.id,
    sn.nombres,
    sn.paterno,
    sn.materno,
    concat(ifnull(sn.nombres,?),?,ifnull(sn.paterno,?),?,ifnull(sn.materno,?)) as nombre_completo
    from seg_no_enc sn
    where sn.id in ('.$cadena.')
    ';
    $params = array('',' ','',' ','');
    $beneficiario = $db->rawQuery($sql,$params);
    
    //print_R($beneficiario);
    //exit;
    */
    
}

 
?>
 <?php if($excel == NULL){ ?> 
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
   <div id="contenido">
    <div>
    <h2 class="centro">Subir Capacitaci&oacute;n</h2>
    </div>
    
   
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

 <?php if($totales != NULL){?>

      <?php if($totales['total_duplicados']){ ?>
      <div class="mensaje adv_msg">
        Hubo <?php echo $totales['total_duplicados']; ?> registros que previamente hab&iacute;an sido 
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
      <td>Total Beneficiarios en Excel:</td>
      <td><?php echo $totales['total_encuestados'];?></td>
    </tr>  
    <tr>
     <td>Total de Beneficiarios Registrados (al menos 1 capacitaci&oacute;n):</td> 
      <td><?php echo $totales['total_registrados']?></td>
   </tr>   
   <tr>
      <td>Total Beneficiarios Encontrados sin Capacitaci&oacute;n:</td>
      <td><?php echo $totales['total_sin_cap'];?></td>
    </tr>
    <tr>
      <td>Total Beneficiarios sin Coincidencia:</td>
      <td><?php echo $totales['total_encuestados'] - ( $totales['total_registrados'] + 
                                                       $totales['total_sin_cap'] +
                                                       $totales['total_duplicados']);?></td>
    </tr>
    <tr>
      <td>Total Beneficiarios Duplicados (no tomados en cuenta):</td>
      <td><?php echo $totales['total_duplicados'];?></td>
    </tr>
   <tr>
       <td>Total de Capacitaciones Registradas:</td>
       <td><?php echo $totales['total_cap']?></td>
    </tr>
    </tbody>
  </table>
<?php }?>





<div align="center">                
       <form action="carga_cap.php" id="carga_cap" method="post" enctype="multipart/form-data">
        <table>          
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
         Seleccione un archivo .XLSX desde su computadora para importar capacitaciones de punto rosa.                        
         </p>        
      <div id="spinner" class='mensaje_carga'>
            La carga del archivo puede tomar algunos minutos. Por favor, sea paciente.
          <img src="<?php echo $_SESSION['css_path'] ?>/img/loader_sug.gif">
      </div>
    </div>
    
    <div class="centro">        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <div>
            <input type="hidden" id="excel" name="excel" value="1" />
            <input type="submit" value ="Exportar a Excel"/>
        </div>        
        </form> 
    </div>
    <?php } ?>
    
    <?php if($lista != NULL){ ?>
    
     <div class="mensaje">
     No Encontrados
     </div>
    
    <?php
    //Si tenemos listado
    if(isset($p) && $excel == NULL){ ?>
    <p>
        <?php
        // Listado de páginas del paginador
        echo $p->display();
        ?>
    </p>  
    <?php } ?>
    
    

    <table id="no_enc" class="tablesorter">
    <thead>
        <th>Folio</th>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>        
    </thead>

    <tbody>
    
    <?php foreach ($lista as $b):?>
          
        <tr> 
            <td><?php echo $b['folio']; ?></td>
            <td><?php echo ($excel == 1)? utf8_decode($b['nombres']) : $b['nombres'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($b['paterno']) : $b['paterno'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($b['materno']) : $b['materno'];?></td>
         </tr>
    <?php endforeach;?>   
    </tbody>
    </table>
    <?php } ?>    
    <?php if($excel == NULL){ ?>
    </div>
</div>


<?php
    //Incluimos pie
    include($_SESSION['inc_path'].'/footer.php');
}
?>