<?php
session_start();//Habilitamos uso de variables de sesión
//Incluimos cabecera
include('../../inc/header.php');
//Modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_apoyos_serv.php');
include_once($_SESSION['model_path'].'seg_punto_rosa_capacitacion.php');
//Variable de respuesta
$respuesta = intval($_GET['r']);
//Obtenemos ID de mujer
$id_mujeres_avanzando = intval($_GET['id_edicion']);
//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
//Arreglos
$mujeres_avanzando = NULL;
//GIA
$id_grado = NULL;
//Servicio de la beneficiaria
$id_servicio = NULL;
//ruta raiz
$ruta_raiz = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS']; 
//ruta de imagen
$ruta = $_SESSION['img_path']."mujeres/";
//Si tenemos el ID de una mujer
if($id_mujeres_avanzando){
//Traemos datos de beneficiaria
    $mujeres_avanzando = mujeresAvanzando::get_by_id($id_mujeres_avanzando);
    $folio = $mujeres_avanzando['folio'];
    $num_folio = $mujeres_avanzando ['num_folio'];
    if(strlen($mujeres_avanzando['num_folio']) > 1){
        $folio=$folio.'-'.$num_folio;
    }
}
//print_r($folio);
//exit;
//Obtenemos acciones del menú
$central = Permiso::arregloMenu('asigna_capacitacion','center');

//echo $respuesta;
//exit;

?>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery-ui-1.10.3.custom.min.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida_asign.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>

<div id="principal">
   <div id="contenido">
    <div>
    <h2 class="centro">Seguimiento</h2>
</div> 
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div align="center">                
        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            //if($mujeres_avanzando != NULL){ ?>
            
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>
<style type="text/css">
    .foto_cred{
        /*position: absolute;*/
        display: block;
        top: 1.6cm;
        left: 0.74cm;
        width: 2.37cm;
        height: 2.75cm;                
    }
</style>
 
<table style="font-size:14pt;" class="tablesorter">             
    <thead> 
        <tr>
            <th>Foto</th>
            <th>Beneficiaria</th>
        </tr> 
    </thead>

        <?php  //Verificamos si la imagen existe, de no estarlo ponemos imagen default
    $ruta_imagen = (file_exists($ruta_raiz.$folio.".png"))? $ruta.$folio.".png" : $ruta."default.png"; 
    ?> 
    <tbody>
       <tr class="zebra"> 
            <td width="95">
                <div class="foto_cred" 
                style="background: url(<?php echo $ruta_imagen; ?>) center center ; background-size: auto 100%;">
                </div>
            </td>
            <td>
               <?php echo '<B>FOLIO:</B> '.$mujeres_avanzando['folio_compuesto'].'<br>';?>
               <?php echo '<B>NOMBRE:</B> '.$mujeres_avanzando['nombre_completo'].'<br>';?>
               <?php echo '<B>GRADO DE INSEGURIDAD ALIMENTAR&Iacute;A:</B> '.$mujeres_avanzando['grado'];?>
            </td>
       </tr>    
    </tbody>   
  </table>   
  
            
                <input type="hidden" name="id_edicion" value="<?php echo $c_mujeres_avanzando_detalle['ID_MUJERES_AVANZANDO_DETALLE']; ?>" />
                <input type="hidden" name="id_mujeres_avanzando" value="<?php echo $id_mujeres_avanzando; ?>" />
                <div id="tabs">
                <ul>
                <li><a href="#tabs-1">Servicios/Apoyos Otorgados</a></li>
                <li><a href="#tabs-2">Otorgar Servicios/Apoyos</a></li>
                </ul>
                            
                <div id="tabs-1">
                <div class="centro"> 
                <?php include($_SESSION['inc_path'].'/seguimiento/lista_capacitacion.php'); ?> 
                </div>
                <div>
                <?php include($_SESSION['inc_path'].'/seguimiento/lista_huerto.php'); ?>
                </div>
                <div>
                <?php include($_SESSION['inc_path'].'/seguimiento/lista_apoyos_serv.php'); ?>
                </div>
                <div>
                <!-- listamos las capacitaciones guardadas de la mujer -->
                <?php include($_SESSION['inc_path'].'/seguimiento/lista_actividad_com.php'); ?>
                </div>
                <div>
                <!-- listamos programas estatales -->
                <?php include($_SESSION['inc_path'].'/seguimiento/lista_prog_estatal.php'); ?>
                </div>
                </div>
                

                <div id="tabs-2">
                     <?php //if(array_key_exists('asigna_capacitacion',$central)){ ?> 
                    <div class="centro">
                        <?php include($_SESSION['inc_path'].'/seguimiento/asigna_capacitacion.php');?> 
                    </div>
                    <?php //} ?>
                    
                    <?php if(array_key_exists('asigna_huerto',$central)){ ?> 
                    <div class="centro"> 
                      <?php include($_SESSION['inc_path'].'/seguimiento/asigna_huerto.php'); ?>
                    </div>
                    <?php } ?> 

                    <?php if(array_key_exists('asigna_apoyo_serv',$central)){ ?> 
                    <div class="centro">
                        <?php include($_SESSION['inc_path'].'/seguimiento/asigna_apoyo_serv.php'); ?>    
                    </div>
                    <?php } ?>

                    <?php if(array_key_exists('asigna_actividad_com',$central)){ ?> 
                    <div class="centro"> 
                      <?php include($_SESSION['inc_path'].'/seguimiento/asigna_actividad_com.php'); ?>
                    </div>
                    <?php } ?> 
                </div> 
               
    
        
    </div>
    </div>
</div>


<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>
