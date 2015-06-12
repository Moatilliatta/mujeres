<?php
session_start();  

/* Al utilizar ajax y refrescar este formulario
necesitaremos obtener el objeto $db (instancia de conexión). 
Verificamos que, si no están creadas
las variables, las obtendremos*/
if(!isset($db)){

  //Incluimos librerías
  include ($_SESSION['inc_path'] . "conecta.php");
  include ($_SESSION['inc_path'] . "libs/Permiso.php");

}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'callcenter.php');

//Parametros de búsqueda
$tipo_filtro = ($_GET['tipo_filtro'])? $_GET['tipo_filtro'] : NULL;
$busqueda = ($_GET['busqueda'])? $_GET['busqueda'] : NULL;
$id_status_llamada = ($_GET['id_status_llamada'])? $_GET['id_status_llamada'] : NULL;

//Listamos los beneficiarios que puede ver el operador
list($lista,$p) = CallCenter::listaCallCenter($busqueda,$tipo_filtro,$id_status_llamada);  

if(count($lista) == 0){
    $respuesta = 8;    
}

//Mensaje respuesta
list($mensaje,$class) = Permiso::mensajeRespuesta($respuesta);

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<p>
    
    <?php
    //Si tenemos listado
    if($lista != NULL){                
        // Listado de páginas del paginador
        echo $p->display();
    ?>
    </p>
    
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Tel&eacute;fono</th>
        <th>Estatus Llamada</th>
        <th>Grupo</th>
        <th>Cantidad De Llamadas</th>
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre_completo']; ?></td>
            <td><?php echo $l['telefono']; ?></td>
            <td><?php echo $l['estatus'];?></td>
            <td><?php echo $l['nombre_grupo']; ?></td>
            <td><?php echo $l['total_llamadas']; ?></td>
            <td>
            <?php if(!$l['estatus_reg']){ ?>
            <div title="llamar" class="ui-state-default ui-corner-all lista">
                <?php if($l['id_callcenter']){ ?>
                    <a class="ui-icon ui-icon-note" href="edita_callcenter.php?id_callcenter=<?php echo $l['id_callcenter']; ?>"></a>
                <?php }else{ ?>
                    <a class="ui-icon ui-icon-circle-plus" href="alta_callcenter.php?id_mujeres_avanzando=<?php echo $l['id']; ?>"></a>
                <?php } ?>
             </div>
             <?php }else{ 
                echo "Ocupado por: ".$l['nombres_operador'];
                } ?>
            </td>
        </tr>

        <?php endforeach; ?>      
       
    </tbody>
    
    </table>
    
  <?php }elseif($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
  <?php } ?>
   