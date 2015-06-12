<?php
session_start();  

/* Al utilizar ajax y refrescar este formulario
necesitaremos obtener el objeto $db (instancia de conexión). 
Verificamos que, si no están creadas
las variables, las obtendremos*/
if(!isset($db)){

  //Incluimos librería de permiso
  include ($_SESSION['inc_path'] . "conecta.php");

}

//Incluimos librerías
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');

//Cargamos modelo de c_mujeres_avanzando_detalle
include_once($_SESSION['model_path'].'prog_estatal_mujeres.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos los valores enviados
$id_caravana = ($_POST['id_caravana'] != 0)? $_POST['id_caravana'] : 0;
$excel = ($_POST['excel'] != 0)? $_POST['excel'] : 0;

//Si se nos envía los id de mujeres que no coinciden
//los guardamos en la variable de sesión, en caso
//contrario, eliminamos dicha variable
if($_POST['no_coincide'] != null){

    $_SESSION['no_coincide'] = $_POST['no_coincide'];

}else{
    unset($_SESSION['no_coincide']);
}

//Si queremos generar excel, importamos librerías
if($excel == 1){
    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=beneficiarias_faltantes_".date('Y-m-d h_i_s').".xls");
}

//Inicializamos valores
$lista = null;
$p = null;
$c = null;

//Obtenemos datos de la caravana en cuestión
if($id_caravana != 0){
    $db->where('id',$id_caravana);
    $c = $db->getOne('caravana');
}

//Verificamos si se generará excel o no
if( $excel == 1 && $_SESSION['no_coincide'] != NULL ){
    
    $lista = mujeresAvanzando::listaNoEnc($_SESSION['no_coincide']);

}elseif($_SESSION['no_coincide'] != NULL){

    //Obtenemos listado y paginador
    list($lista,$p) = mujeresAvanzando::listadoNoEnc($_SESSION['no_coincide']);

}

//echo "Total: ";
//print_r($_SESSION['no_coincide']);

?>

<?php if($excel != 1 && count($lista) > 0){ ?>

<div class="mensaje">
Hubo <?php echo count($_SESSION['no_coincide']) ?> personas que no fueron
registradas, para generar un listado con dichas personas, de click en el
bot&oacute;n 'Generar Excel'
</div>

<form method="POST" action="<?php echo $_SESSION['app_path_p'].'inc/estadisticas/listado_prog_est.php' ?>" >
<table>
    <tr>
      <td>
        <input type="hidden" id="excel" name="excel" value="1" />
        <input type="hidden" id="id_caravana" name="id_caravana" value="<?php echo $id_caravana ?>" />
      <td>
        <input type="submit" value="Generar Excel"  />
      </td>
    </tr>
    </table>
</form>      
<?php } ?>

    <?php
    //Si tenemos listado
    if($lista != NULL && $excel == 1){ ?>
    
    <div class="mensaje">
    Personas que no fueron encontradas en la caravana '<?php echo ($excel == 1)? utf8_decode($c['descripcion']) : $c['descripcion'];?>'
    </div>

    <table class="tablesorter">
    <thead>
        <th>Folio</th>
        <th>Nombre</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Caravana</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['folio_compuesto']; ?></td>
            <td><?php echo ($excel == 1)? utf8_decode($l['nombres']) : $l['nombres'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($l['paterno']) : $l['paterno'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($l['materno']) : $l['materno'];?></td>
            <td><?php echo ($excel == 1)? utf8_decode($l['caravana']) : $l['caravana'];?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table> 

     <?php } ?>


    <?php if($lista == NULL){ ?>

    <div class="mensaje">
      Se asignaron todos los beneficiarios
    </div>

    <?php } ?>