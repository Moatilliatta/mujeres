<?php
session_start();  
//Incluimos cabecera
include('../../inc/header.php'); 
//Obtenemos listado de permiso en menu central
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');
//Incluimos modelos
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
//Obtenemos id del beneficiario
$id_mujer=$_GET['id_mujer'];
//Obtenemos datos del beneficiario
$lista = mujeresAvanzando::listadoMujer(null,null,null,null,null,null,null,$id_mujer);
//print_r($lista);
$lista = $lista[0];
?>
<div id="principal">

  <div id="contenido">

    <h2 class="centro">Datos Cargados Con &Eacute;xito</h2>    

   
<!-- 
      <tr>        
  
        <td colspan="4">

          <button id='mod_beneficiario'>Volver a M&oacute;dulo de Beneficiario</button>
          <button id='mod_pys'>Volver a M&oacute;dulo de Programas y Servicios</button>  

        </td>

      </tr>
 -->
  
    	<form id='formCurp' method="post" action='save_mujer.php'>
    <fieldset>
      <tr>
         <td>
          <label class="centro" for="curp">
            <?php echo ($mujeres_avanzando['es_curp_generada'] == 'SI')? 'ID DIF': 'CURP'; ?>
          </label>
         </td>
       </tr>
       <tr>
         <td>
           <input type = 'text' id = 'curp' name = 'curp' class="curp" value="<?php echo $mujeres_avanzando['curp']; ?>" />
         </td>
       </tr>
       <tr>
        <td>&nbsp;</td>
        <td colspan="4">
            <input type="submit" value="Guardar" id="guardar" />
        </td>
    </tr>  
      </form>
    </fieldset>
    <fieldset>

      <table>

      <legend><label>Datos de Identificaci&oacute;n de la Beneficiario</label></legend>

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
      <!-- 
      <tr>

        <td><label>Fecha Nacimiento <?php //if ($lista['fecha_a']=='SI'){ ?>
        (APROXIMADA)
        <?php //}?></label>
        </td>
        <td><label>CURP <?php // if($lista['es_curp_generada']=='SI'){ ?>
        (ES ID DIF)
       <?php //} ?>
        </label></td>
  
      </tr>
       -->
      <tr>    

        <td><?php echo $lista['fecha_nacimiento']; 
        
        ?></td>

        <td><?php echo $lista['curp']; ?></td>


      </tr>

    </table>

    </fieldset>
  <fieldset>

    <table>

    <legend><label>Domicilio</label></legend>

    <tr>

      <td><label>Municipio</label></td>

      <td><label>Localidad</label></td>      

    </tr>

    <tr>

      <td><?php //echo $lista['municipio_residencia']; ?></td>

      <td><?php //echo $lista['localidad']; ?></td>      

    </tr>
    <tr>
      <td>
        <label>Direcci&oacute;n</label>
      </td>
    </tr> 

    <tr>        
      <td><?php echo $lista['tipo_via_prin'].' '.$lista['via_prin'].' No #'.$lista['numero_exterior'];
            
            if($lista['numero_interior']){
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

</table>
</fieldset>
    </div>   
</div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>
