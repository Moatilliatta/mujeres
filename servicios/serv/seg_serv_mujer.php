<?php
session_start();  

//Incluimos cabecera
include($_SESSION['inc_path'].'header.php'); 

//Obtenemos listado de permiso en menu central
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

//Incluimos modelos
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos id del beneficiario
$id_mujeres_avanzando=$_GET['id_edicion'];

//Obtenemos listado de servicios
//list($lista_pys,$p) = Beneficiario_pys::listaPagBeneficiarioServ($id_mujeres_avanzando);

//Obtenemos datos del beneficiario
$lista= mujeresAvanzando::expedienteMujer($id_mujeres_avanzando);
$lista = $lista[0];
?>
 
  <div id="principal">

  <div id="contenido">
   <h2 class="centro">Seguimiento de Servicios Otorgados</h2>    

      <div id="page_list" align="center">        
        <?php include($_SESSION['inc_path']."servicios/lista_serv.php");?>
      </div>

      <!-- form agregar productos y servicios a beneficiario -->
      <div>
        <h2 class="centro">Otorgar Nuevo Servicio</h2>
          <div  id='nuevo_servicio'>
            <?php include("form_mujer_serv.php");?>
          </div>    
     </div>       
  </div>    

  </div>       
  
  <?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>      