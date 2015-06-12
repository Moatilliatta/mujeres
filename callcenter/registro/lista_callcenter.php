<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);
$id_usuario = $_SESSION['usr_id'];

include_once($_SESSION['model_path'].'callcenter.php');

//Mensaje respuesta
list($mensaje,$class) = Permiso::mensajeRespuesta($respuesta);

$status_llamada = $db->get('status_llamada');

//actualizamos el estado que tenga el usuario
$msg_no = Callcenter::actualizaEstatus($id_usuario);

?>
<script language="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>jquery.timers-1.0.0.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>

<script type="text/javascript">
jQuery(document).ready(function ($) {

  $("#lista_call").everyTime(1000,function(i){

    ruta = typeof ruta !== 'undefined' ? ruta : '../../inc/callcenter/';

    var search = ((location.search).substr(1) != undefined)? '?'+(location.search).substr(1) : '';

    $.ajax({

      url: ruta+"listado_call.php"+search,
      cache: false,
      success: function(html){
        
        $("#lista_call").html(html);
        
      }
    })
  });

});
  
</script>

<div id="principal">
    <div id="contenido">
        <div class="centro">
            
            <div  align="center">
            <h2 class="centro">Lista Call Center</h2>
            
            <?php if ($lista !=null){ ?>
            <h3 class="centro"> Resultados Encontrados: <?php echo count($lista);?></h3> 
            <?php }?>            
            
            <form id='formbusqueda' method="get" action='lista_callcenter.php'>
            <table>
            <tr>
              <td>
                <label for="tipo_filtro"> Buscar Por: </label>
              </td>
              <td>
                <select id="tipo_filtro" name="tipo_filtro">
                       <option value="nombre">Nombre</option>
                       <option value="telefono">Tel&eacute;fono</option>
                </select>
              </td>
              <td><label for="busqueda"> Palabra Clave</label></td>
              <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
              <td><label for="busqueda">Estatus</label></td>
              <td>
                <select id="id_status_llamada" name="id_status_llamada">
                    <option value="">Seleccione Estatus</option>
                    <?php foreach($status_llamada as $s): ?>
                        <option value='<?php echo $s['id'] ?>' > 
                            <?php echo $s['estatus'];?>
                        </option>
                    <?php endforeach; ?>
                </select>
              </td>
              <td><input type="submit" id="boton"  value="Buscar" /></td></td>
            </tr>
            
            </table>
            </form>
            </div>
        </div>
                                
        <?php if($respuesta > 0){ ?>
        
        <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
                
        <?php } ?>
         
        <div id="lista_call" align="center">
        <?php include($_SESSION['inc_path'].'callcenter/listado_call.php');?>
        </div>

        </div>  
</div> 
             
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>

