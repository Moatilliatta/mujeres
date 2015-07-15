<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'callcenter_grupo.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Obtenemos valores de búsqueda
$nombre = ($_GET['nombre'])? $_GET['nombre'] : NULL;
$fecha_instalacion = ($_GET['fecha_instalacion'])? $_GET['fecha_instalacion'] : NULL;
$id_caravana = ($_GET['id_caravana'])? $_GET['id_caravana'] : NULL;

//Obtenemos listado de grupos                                      
list($lista,$p) = CallCenterGrupo::listaCallCenterGrupo($nombre,
                                                        $fecha_instalacion,
                                                        $id_caravana);

if(count($lista) == 0){
    $respuesta = 8;    
}

//Mensaje respuesta
list($mensaje,$class) = Permiso::mensajeRespuesta($respuesta);

$caravanas = $db->get('caravana');

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>

<script language="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>jquery.timers-1.0.0.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">Grupos</h2>

    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div class="centro">       
        <div  align="center">
     
       <form id='formbusqueda' method="get" action='lista_grupo_callcenter.php'>
            <fieldset>
            <table>
            <legend>
                   <label>
                     Busqueda  
                   </label>  
            </legend>

            <tr>
                <td class="centro" colspan="6">
                <label>Formulario de B&uacute;squeda</label>
                </td>                
            </tr>            

            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>

            <tr>
              <td colspan="2">
                <label for="nombre">Nombre</label>
              </td>
              <td colspan="2">
                <label for="fecha_instalacion">Fecha Instalaci&oacute;n</label>
              </td>
              <td colspan="2">
                <label for="id_caravana">Caravana</label>
              </td>              
            </tr>

            <tr>          
              <td colspan="2">
                <input type = 'text' id = 'nombre' name = 'nombre' />
              </td>
              <td colspan="2">
                <input type = 'text' id = 'fecha_instalacion' name = 'fecha_instalacion' class="fecha date"/>
              </td>     
              <td colspan="2">
                <select id="id_caravana" name="id_caravana" class="combobox">
                    <option value="">Seleccione Caravana</option>
                    <?php foreach($caravanas as $c): ?>
                        <option value='<?php echo $c['id'] ?>'  <?php echo $selected;?> > 
                            <?php echo $c['descripcion'];?>
                        </option>
                    <?php endforeach; ?>                       
                </select>     
              </td>              
              <td colspan="6">
                <input type="submit" id="boton"  value="Buscar" />
              </td>
            </tr>              
          </table> 
          </fieldset>           

          </form>                              
        </div>
    <div id="page_list" align="center">
    
    <p>
        <?php //if(array_key_exists('alta_grupo_callcenter',$central)){ ?>
        <a  id = 'enviar' class="btn" href="alta_grupo_callcenter.php">Agregar</a>
        <?php //} ?>
    </p> 
   <p>
   <?php 
   if($lista !=null){

    echo $p->display();
   
    ?>
    </p>

    <table class="tablesorter">
    <thead>
        <th>Grupo</th>
        <th>Caravana</th>
        <th>Filtro</th>
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre_grupo']; ?></td>
            <td><?php echo $l['caravana'] ?></td>
            <td><?php echo $l['filtro'] ?></td>
            
            <td>

                <?php //if($l['activo']==1){ ?>
                
                <?php if(array_key_exists('edita_grupo_callcenter',$central)){ ?>
                <div title="Edita Grupo" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-note" href="edita_grupo_callcenter.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>
                <?php } ?>
                
                <?php //}?>

                <?php //if(array_key_exists('activa_caravana',$central)){ ?>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> grupo?" 
                       href="activa_grupo_callcenter.php?id_activo=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php //} ?>
               
               <?php if(array_key_exists('edita_grupo_callcenter',$central)){ ?>
                <div title="Asigna operadores al grupo" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-circle-triangle-e" href="asigna_operador_grupo.php?id_callcenter_grupo=<?php echo $l['id']; ?>"></a>
                </div>
                <?php } ?>
                


                <!--
                <a class="confirmation"  href="activa_beneficiario.php?id_activo=<?php //echo $l['id']; ?>">
                 -->

                  <?php /* if($l['activo'] == 1){

                    echo 'Eliminar';

                }else if($l['activo'] == 0){

                    echo 'Activar';

                }*/?>

                <!--
                </a>
                 -->

            </td>
        </tr>

        <?php endforeach; ?>

    </tbody>
    </table>
    <?php } ?>
    </div>

  </div>
 </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>s