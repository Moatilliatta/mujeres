<?php
//Incluimos cabecera
session_start();

$excel = $_GET['excel'];

//Imprimimos o no cabecera de excel
if($excel != NULL){
    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");
    //Librería de permisos
    include($_SESSION['inc_path'].'libs/Permiso.php'); 

    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=Datos_por_caravana.xls");
}else{    
    //Incluimos cabecera
    include('../../inc/header.php');    
}

include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'familiares_mujer.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

$id_caravana=$_GET['id_caravana'];
$mujeres_avanzando=null;
$caravana = null;

if($id_caravana !=null){

//Obtenemos datos
$mujeres_avanzando = mujeresAvanzando::datos_caravana($id_caravana);
$familiares = FamiliaresMujer::datosCaravanaFam($id_caravana);

$db->where('id',$id_caravana);
$caravana = $db->getOne('caravana');
}

//Beneficiarios que coinciden con el cruce
$beneficiarios = array();

$i = 0;

//De mujeres avanzando
 if($mujeres_avanzando != NULL){ 
     foreach ($mujeres_avanzando as $key => $value):
     $objeto=Permiso::buscaNombreWS($value['nombres'],$value['paterno'],$value['materno']);
     
     if($objeto->Fpu){
        $beneficiarios[]=$objeto;
        //$i++; if($i == 10) break;
     }     

     endforeach;
  }   

$i = 0;
//De familiares
 if($familiares != NULL){ 
     foreach ($familiares as $key => $value):
     $objeto=Permiso::buscaNombreWS($value['nombres'],$value['paterno'],$value['materno']);
     
     if($objeto->Fpu){
        $beneficiarios[]=$objeto;
        //$i++; if($i == 10) break;
     }

     endforeach;
  }    

$lista_caravanas = $db->get('caravana'); 

if($excel == NULL){ ?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
   <div id="contenido">
    <h2 class="centro">Caravana</h2>

<?php } ?>


<?php if($beneficiarios != NULL) { ?>

    <table id="totales_caravana" class="tablesorter">
    <th>
    <tr>
      <th colspan="10"><?php echo $caravana['descripcion'] ?></th>
    </tr>
    <tr>
        <th>ID(FPU)</th>
        <th>CURP</th>
        <th>NOMBRE</th>
        <th>APELLIDO PATERNO</th>
        <th>APELLIDO MATERNO</th>
        <th>SEXO</th>
        <th>CALLE</th>
        <th>NUM EXT</th>
        <th>MUNICIPIO</th>
        <th>C.P.</th>
        <!-- <th>Programas</th> -->
    </tr>  
    </th>
    <tbody>
      <?php foreach($beneficiarios as $k => $v):?>
      <tr>
          <td><?php echo $v->Fpu; ?></td>
          <td><?php echo $v->Curp; ?></td>
          <td><?php echo utf8_decode($v->Nombre); ?></td>
          <td><?php echo utf8_decode($v->Apaterno); ?></td>
          <td><?php echo utf8_decode($v->Amaterno); ?></td>
          <td><?php echo $v->Sexo; ?></td>
          <td><?php echo utf8_decode($v->Calle); ?></td>
          <td><?php echo $v->NumExt; ?></td>
          <td><?php echo utf8_decode($v->NomLocalidad); ?></td>
          <td><?php echo $v->Cp; ?></td>
          <!--- <td><?php //print_r($v->programas); ?></td> -->
      </tr>
      <?php endforeach;?>
    </tbody>    
</table>
<?php }?>

<?php if($excel == NULL){ ?>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <table>
       <tr>
      <td>
        <label for="activo">Caravana<?php echo $id_caravana ?></label>
      </td>
    </tr>
    <tr>
      <td>
        <input type="hidden" id="excel" name="excel" value="1" />
        <select id="id_caravana" name="id_caravana">
          <option value="">Seleccione Caravana</option>
          <?php foreach($lista_caravanas as $c){ ?>                        
          <option value='<?php echo $c['id'] ?>' <?php echo $selected;?> > 
          <?php echo $c['descripcion'];?>
          </option>
          <?php } ?>
        </select>
      </td>                     
      <td>
        <input type="submit" value="Generar Excel"  />
      </td>
    </tr>
    </table>
    </form>      
     
      </div>
</div>
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');

}

?>
