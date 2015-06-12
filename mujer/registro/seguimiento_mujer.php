<?php
session_start();  
//Incluimos cabecera
include('../../inc/header.php'); 
//Obtenemos listado de permiso en menu central
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');
//Incluimos modelos
//include_once($_SESSION['model_path'].'beneficiario_pys.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
//Obtenemos id del beneficiario
$id_mujeres_avanzando=$_GET['id_edicion'];
//Obtenemos listado de servicios
//list($lista_pys,$p) = Beneficiario_pys::listaPagBeneficiarioServ($id_beneficiario);
//Obtenemos datos del beneficiario

$lista= mujeresAvanzando::expedienteMujer($id_mujeres_avanzando);
$lista = $lista[0];

?>
<div id="principal">
  <div id="contenido">
    <h2 class="centro">Seguimiento de Mujer</h2>    
    <table>
      <tr>        
        <td colspan="4">
          <button id='mod_beneficiario'>Volver a M&oacute;dulo de Mujer</button>
          <button id='mod_pys'>Volver a M&oacute;dulo de Programas y Servicios</button>  
        </td>
      </tr>
    </table>


    <fieldset>
      <table>
      <legend><label>Datos de identificaci&oacute;n</label></legend>
      <tr>        
        <td colspan="4">&nbsp;</td>
      </tr>

      <tr>
        <td><label>Nombre(s)</label></td>
        <td><label>Apellido Paterno</label></td>
        <td><label>Apellido Materno</label></td>
      </tr>

      <tr>    
        <td><?php echo $lista['nombres']; ?></td>
        <td><?php echo $lista['paterno']; ?></td>
        <td><?php echo $lista['materno']; ?></td>
      </tr>

      <tr>
        <td><label>Fecha Nacimiento <?php if ($lista['fecha_a']=='SI'){ ?>
        (APROXIMADA)
        <?php }?></label>
        </td>
          
        <td><label>CURP <?php echo ($lista['es_curp_generada']=='SI')? '(GENERADA)': '' ; ?>
        </label></td>        
      </tr>

      <tr>    
        <td><?php echo $lista['fecha_nacimiento'];?></td>
        <td><?php echo $lista['curp']; ?></td>
      </tr>
    </table>
    </fieldset>

    <fieldset>
    <table>
    <legend><label>Domicilio</label></legend>

    <tr>
      <td><label>Municipio</label></td>
    </tr>

    <tr>
      <td><?php echo $lista['municipio_residencia']; ?></td>
    </tr>

    <tr>
      <td>
        <label>Direcci&oacute;n</label>
      </td>
    </tr> 

    <tr>        
      <td><?php echo $lista['tipo_via_prin'].' '.$lista['via_prin'].' No #'.$lista['numero_exterior'];
            
            if($lista['numero_interior'] && strtoupper($lista['numero_interior']) != 'S/N'){
                echo  ' Interior # '.$lista['numero_interior'].' ';
            }       
            echo ' '.$lista['tipo_asentamiento'].' '.$lista['asentamiento']; ?>
      </td>      
    </tr>
    </table>

</fieldset>

<fieldset>
    <table>
    <legend><label>Datos Adicionales</label></legend>
    <tr>
       <td><label>Escolaridad</label></td>
       <td><label>Ocupaci&oacute;n</label></td>
    </tr>
    <tr>
       <td><?php echo $lista['escolaridad']; ?></td>
       <td><?php echo $lista['ocupacion']; ?></td>
    </tr>
    <tr>
       <td><label>Ind&iacute;gena</label></td>
       <?php if($lista['indigena'] == 'SI') { ?>
       <td><label>Comunidad Ind&iacute;gena</label></td>
       <td><label>Dialecto</label></td>  
       <?php } ?>
    </tr>
    <tr>
       <td><?php echo $lista['indigena']; ?></td>
       <td><?php echo $lista['comunidad_indigena']; ?></td>
       <td><?php echo $lista['dialecto']; ?></td>
    </tr>
    <tr>
      <td><label>Pa&iacute;s de Nacimiento</label></td>
      <td><label>Estado de Nacimiento</label></td>
    </tr>
    <tr>
       <td><?php echo $lista['pais']; ?></td>
       <td><?php echo $lista['estado_de_nacimiento']; ?></td>
    </tr>
    <tr>
      <td><label>Municipio de Nacimiento</label></td>
      <td><label>&iquest;Es ciudadano mexicano?</label></td>
    </tr>
    <tr>
      <td><?php echo $lista['municipio_nacimiento']; ?></td>
      <td><?php echo $lista['ciudadano']; ?></td>
    </tr>
    <tr>
      <td><label>Fecha de Creaci&oacute;n</label></td>
      <td><label>&Uacute;ltima Modificaci&oacute;n</label></td>
    </tr>
    <tr>
      <td><?php echo $lista['fecha_creado']; ?></td>
      <td><?php echo $lista['fecha_ultima_mod']; ?></td>
    </tr>
    <tr>
      <td colspan="8">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" >
      <?php
          //Verificamos si tiene permiso de edición
          if(Permiso::accesoAccion('seguimiento_mujer', 'registro', $_SESSION['module_name'])){ ?>                
              <a href="edita_mujer.php?id_edicion=<?php echo $lista['id']; ?>">
                <input type="submit" class="button" value="Editar"/>
              </a>
      <?php } ?>   
      </td>
    </tr>
</table>
</fieldset>

  <!-- programas y servicios del beneficiario -->
  <!--
   <h2 class="centro">Seguimiento de Servicios Otorgados</h2>    
     <div id="page_list" align="center">        
     <?php //include($_SESSION['inc_path']."servicios/lista_pys.php");?>
    </div>
  -->
</div>

</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>

