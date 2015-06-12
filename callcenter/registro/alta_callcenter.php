<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Valores de la búsqueda
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];
$id_dif=$_GET['id_dif'];

list($lista,$p) = mujeresAvanzando::listaProgMujer($busqueda,$tipo_filtro);      
  
 
?>
<div id="principal">
   <div id="contenido">
   
   
    <h2 class="centro">Alta de Mujer a Callcenter</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje"><?php echo $mensaje;?></div>
    
    <?php } ?>
    <div class="centro">       
        <div  align="center">
            <form id='formbusqueda' method="get" action='alta_callcenter.php'>        
            <table>
            <tr>
                <td class="centro" colspan="6">
                <label>B&uacute;squeda de Mujer</label>
                </td>                
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            <td><label for="tipo_filtro"> Buscar Por: </label></td>
            <td>
                <select id="tipo_filtro" name="tipo_filtro">
                <option value="nombre">Nombre</option> 
                <option value="curp">CURP</option>
                </select>
            </td>
            <td><label for="busqueda"> Palabra Clave</label></td>
            <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
            <td><input type="submit" id="boton"  value="Buscar" /></td></td>            
            </tr>
            </table>            
            </form>
            <?php 
            //Verificamos si tiene permiso de alta de beneficiario
            if(Permiso::accesoAccion('alta_mujer', 'registro', 'mujer')){ ?>                
            
            <table>
                <tr>
                    <td>
                    <form action="../../mujer/registro/alta_mujer.php">
                        <input type="submit" id="agregar"  value="Agregar Mujer" />
                    </form>
                    </td>
                </tr>
            </table>

            <?php } ?>                           
        </div>
    
    <div>
    <?php 
    if($lista == NULL && $tipo_filtro != NULL){?>
    <div class="mensaje <?php echo $clase; ?>">
        No se encontraron resultados con el criterio de b&uacute;squeda seleccionado
    </div>
    <?php } elseif($lista !=null){
                
      include_once("listado_call.php");
             
    }?>
    </div>
    
   </div>
            
    
    
    
    </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>