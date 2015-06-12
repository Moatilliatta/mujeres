<?php 
session_start();//Habilitamos uso de variables de sesión

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");
include_once($_SESSION['model_path'].'beneficiario.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

//variables
$mensaje = '';
$clase = '';

if(isset($_POST['paterno']) && isset($_POST['nombres'])){
    
    //Obtenemos variables
    $nombres = $_POST['nombres'];
    $paterno = $_POST['paterno'];
    $materno = $_POST['materno'];   
    $mensaje = NULL;
    
    //Obtenemos listado de beneficiarios
    list($lista,$p) = Beneficiario::listaBeneficiario(NULL,
                                             NULL,
                                             1,
                                             $nombres,
                                             $paterno,
                                             $materno,
                                             $curp);     

    ///si la lista nula enviamos mensaje de que no hay registro en la busqueda
    if($lista == NULL && count($_POST) > 1){
        //No existen registros con el criterio de búsqueda
        list($mensaje,$class) = Permiso::mensajeRespuesta(8);
    }elseif ($lista == NULL) {
        //No hay registros para mostrar
        list($mensaje,$class) = Permiso::mensajeRespuesta(17);
    }
           
}else{
    
   exit;
    
}
 

    //Si tenemos listado
    if($lista != NULL){
    // Listado de páginas del paginador
    //echo $p->display();
        ?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

    <?php if($mensaje != NULL){?>    
    <div class="mensaje <?php echo $class ?>">
      <?php echo $mensaje;?>
    </div>        
    <?php } ?>

    <div id="page_list" align="center">        
    <p>
        <label>Posibles Coincidencias de Beneficiario</label> 
    </p>
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Estado</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <th>Seleccionar</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombres'];?></td>
            <td><?php echo $l['paterno'];?></td>
            <td><?php echo $l['materno'];?></td>
            <td><?php echo $l['estado'];?></td>
            <td><?php echo $l['municipio'];?></td>
            <td><?php echo $l['localidad'];?></td>
            <td>            
                <input id="<?php echo $l['id'];?>" class='agrega_ben' name="beneficiario" type="button" value="Agregar" />            
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    </div>
    <?php } ?>