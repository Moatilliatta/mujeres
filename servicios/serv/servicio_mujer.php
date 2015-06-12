<?php

session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php'); 

//Incluimos modelo 'Acción'
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//Valores de la búsqueda
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];
$id_beneficiario = $_GET['id_beneficiario'];

//Obtenemos listado de acciones
//Beneficiario:: nombre de la clase beneficiario que esta en el archivo beneficiario 
//list($lista,$p) = Beneficiario::listaAccion($busqueda,$tipo_filtro);
list($lista,$clase) = mujeresAvanzandoDetalle::listaPagMujerServ($id_beneficiario);

//imprimos respuesta en caso de enviarse
if($respuesta !=null){

    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

}

//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL){

    //No existen registros

    $mensaje = Permiso::mensajeRespuesta(8);

}

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

//print_r($central);
?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
$("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
        <div id="contenido">
             <h2 class="centro">Listado de Servicios del Beneficiario</h2>

             <?php if($respuesta > 0 || isset($mensaje)){?>

                <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>

            <?php } ?>

     <div id="page_list" align="center">

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
        <th>Programa</th>
        <th>Servicio</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>

        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['programa']; ?></td>
            <td><?php echo $l['servicio']; ?></td>
            <td>
            <?php  if(array_key_exists('activa_servicio_beneficiario_pys',$central)){ ?>                

                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">                
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> servicio?" 
                       href="activa_servicio_beneficiario_pys.php?id_beneficiario=<?php echo $id_beneficiario; ?>&id_activo=<?php echo $l['cod_rpys']; ?>"></a>
                </div>

                <?php }  ?>
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        

    <?php } ?>

        </div>
     </div>

<?php 

//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>