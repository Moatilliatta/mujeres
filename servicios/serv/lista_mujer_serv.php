<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');

//municipios
$sql='SELECT CVE_MUN, NOM_MUN FROM cat_municipio WHERE CVE_ENT = 14';
$municipio = $db->query($sql);

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Valores de la búsqueda
$nombre_propio=$_GET['nombre_propio'];
$paterno=$_GET['paterno'];
$materno=$_GET['materno'];
$curp=$_GET['curp'];
$id_cat_municipio=$_GET['id_cat_municipio'];
$id_cat_localidad=$_GET['id_cat_localidad'];
$cod_programa_g=$_GET['cod_programa_g'];
$respuesta=$_GET['r'];
$id_dif=$_GET['id_dif'];

//print_r($_GET);
//Listamos los programas del beneficiario
list($lista,$p) = mujeresAvanzando::listaProgMujer(null,
                                                      null,
                                                      $nombre_propio,
                                                      $paterno,
                                                      $materno,
                                                      $id_cat_municipio,
                                                      $id_cat_localidad,
                                                      $cod_programa_g,                                                    
                                                      $curp);      

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">Seguimiento de Mujer</h2>

    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div class="centro">       
        <div  align="center">
      <?php 
        
        //Verificamos si tiene permiso de alta de mujer
        if(Permiso::accesoAccion('alta_mujer', 'registro', 'mujer')){ ?>                
        <table>
            <tr>
              <td colspan="6" style="text-align:center;">
              <form action="../../mujer/registro/alta_mujer.php">
              <input type="submit" id="agregar"  value="Agregar Nuevo Beneficiario" />
              </form>
              </td>
            </tr>
        </table>

        <?php } ?>   

            <form id='formbusqueda' method="get" action='lista_mujer_serv.php'>        
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

                <label for="nombres">Nombre(s)</label>

              </td>

              <td colspan="2">

                <label for="paterno">Apellido Paterno</label>

              </td>

               <td colspan="2">

                <label for="materno">Apellido Materno</label>

              </td>
              <td>
                 <label for="Curp">Curp</label>
              </td>

            </tr>

            <tr>

              <td colspan="2">

                <input type="hidden" id="CVE_EDO_RES" name="CVE_EDO_RES" value="14"/>              

                <input type = 'text' id = 'nombre_propio' name = 'nombre_propio' class="nombre"/>

              </td>

              <td colspan="2">

                <input type = 'text' id = 'paterno' name = 'paterno' class="nombre"/>

              </td>

              <td colspan="2">

                <input type = 'text' id = 'materno' name = 'materno' class="nombre"/>

              </td>
              
              <td>
                 <input type = 'text' id='curp' name='curp' class="nom_num"/>
              </td>
              <td colspan="6"><input type="submit" id="boton"  value="Buscar" /></td></td> 
              </tr>              

          
          </table> 
          </fieldset>           

          </form>

                                    

        </div>

    <div>
    <?php if($lista == NULL && count($_GET)){?>

    <div class="mensaje">
        No se encontraron resultados con el criterio de b&uacute;squeda seleccionado
    </div>
    
    <?php } elseif($lista !=null){

      include_once("listado_mujer_serv.php");

    }?>
    </div>

  </div>
 </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>