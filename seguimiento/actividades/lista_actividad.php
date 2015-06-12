<?php
session_start();//Habilitamos uso de variables de sesión

 //set headers to NOT cache a page
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

//Incluimos cabecera
include('../../inc/header.php'); 

//Incluimos modelo "seg_actividad"
include_once($_SESSION['model_path'].'seg_actividad.php');

//Valores de la búsqueda
$fecha_creado=$_GET['fecha_creado'];
$nombre=$_GET['nombre'];
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];

//echo 'r '.$respuesta;
//exit;

//Obtenemos listado de actividades
list($lista,$pm) = SegActividad::listaActividad($nombre,$fecha_creado);

//imprimos respuesta en caso de enviarse
if($respuesta !=null){
    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
}

//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL && $respuesta == NULL){
    //No existen registros
    list($mensaje,$respuesta) = Permiso::mensajeRespuesta(8);
    $respuesta = 1;
}
$db->where ('activo', 1);
$caravanas = $db->get('caravana');

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

//Guardamos última búsqueda
if(strlen($_SERVER['QUERY_STRING']) > 5){
    $_SESSION['last_search'] = $_SERVER['QUERY_STRING'];
}else{
    $_SESSION['last_search'] = '';
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
    <div id="contenido">
        <div class="centro">
            
            <div  align="center">
            <h2 class="centro">Listado de Actividades</h2>
            
            <?php if ($lista !=null){ ?>
            <h3 class="centro"> Resultados Encontrados: <?php echo count($lista);?></h3> 
            <?php }?>                        

            <div style="float: right;">
                <form action="lista_mujer.php">
                    <input type="submit" value="REINICIAR"/>
                </form>
                <!-- 
                <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"/>
                -->                
            </div>            
                        
            <form id='formbusqueda' method="get" action='lista_mujer.php'>
            <table>
            <tr>
              <td>
                <label for="tipo_filtro"> Buscar Por: </label>
              </td>
              <td>
                <select id="tipo_filtro" name="tipo_filtro">
                       <option value="nombre">Nombre</option>                                
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
     
    <div id="page_list" align="center">        
    <p>
        <?php if(array_key_exists('alta_actividad',$central)){ ?>
        <a  id = 'enviar' href="alta_actividad.php">Agregar</a>
        <?php } ?>
    </p> 
    <p>
    
    <?php
    //Si tenemos listado
    if($lista != NULL){                
        // Listado de páginas del paginador
        echo $pm->display();
    ?>
    </p>
    <table class="tablesorter">
    <thead>
        <th>ID</th>
        <th>Nombre</th>
        <th>Fecha Creado</th>
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['id'];?></td>
            <td><?php echo $l['nombre']; ?></td>
            <td><?php echo $l['fecha_creado'];?></td>
            <td>
             <?php if($l['activo']==1){ ?>
                <?php if(array_key_exists('edita_actividad',$central)){ ?>
            
                <div title="Editar" class="ui-state-default ui-corner-all lista">                
                    <a class="ui-icon ui-icon-note" href="edita_actividad.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>
            
                <?php } ?>
             <?php }?> 

                <?php if(array_key_exists('activa_actividad',$central)){ ?>                
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> secretar&iacute;a?" 
                       href="activa_actividad.php?id_activo=<?php echo $l['id']; ?>"></a>
                </div>
                <?php } ?>
                
                <?php if(array_key_exists('asigna_apoyo',$central)){ ?>                
                <div title="Agregar Apoyo" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-circle-triangle-e" href="asigna_apoyo.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php } ?>
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>
    
    <?php } ?>
    
    </div>
  </div>
</div> 

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>