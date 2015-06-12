<?php

//Incluimos librería de permiso
include_once($_SESSION['inc_path'].'libs/Permiso.php');

//Obtenemos datos enviados
$nombres=$_POST['nombres'];
$paterno=$_POST['paterno'];
$materno=$_POST['materno'];
$tipo_salida=$_POST['tipo_salida'];

$arreglo = array();

$arreglo[] = Permiso::buscaNombreWS($nombres,$paterno,$materno);

?>
 

<?php if(count($arreglo) > 1) {?> 
<fieldset>
<legend>
   <label>Posibles Duplicados</label>  
 </legend>
<div class="lista_coincidencia">
<ul>
    <li class="elemCoincidencia">

    <?php  foreach ($arreglo as $valor):?>

    <a><?php echo $valor->Nombre.' '.$valor->Apaterno.' '.$valor->Amaterno; ?></a>

    <div>
      <?php echo $valor->Fpu.'<br/>';?>
      <?php echo $valor->Curp.'<br/>';?>
      <?php echo ($valor->Sexo == 1)?'HOMBRE':'MUJER'.'<br/>';?>
      <?php echo $valor->Calle.' '.$valor->NumExt.' '.$valor->NomLocalidad.' '.$valor->Cp;?>
    </div>

    <?php endforeach; ?>

    </li>
</ul>
</div>
</fieldset>
<?php } ?>