<?php
session_start();//Habilitamos uso de variables de sesión
//Incluimos cabecera
include('../../inc/header.php'); 
//Incluimos modelo 'Acción'
//include_once($_SESSION['model_path'].'servicio_caravana.php');
include_once($_SESSION['model_path'].'servicio.php');

//Obtemos las dependencias
include_once($_SESSION['model_path'].'dependencia.php');

//Obtenemos variables
$dependencia = Dependencia::listaDependencias();
$ID_C_DEPENDENCIA = $_GET['ID_C_DEPENDENCIA'];
$ID_C_PROGRAMA = $_GET['ID_C_PROGRAMA'];

//Obtenemos listado de los servicios
/*list($lista,$p) = ServicioCaravana::listaServicio(null,$ID_C_PROGRAMA,$ID_C_DEPENDENCIA,NULL,
      NULL, NULL, true); */

//list($lista,$p) = ServicioCaravana::listaServicio(null,$ID_C_PROGRAMA,$ID_C_DEPENDENCIA); 

list($lista,$p) = Servicio::listaServicio(NULL,$ID_C_PROGRAMA,$ID_C_DEPENDENCIA);

//Imprimimos respuesta en caso de enviarse
if($respuesta !=null){
    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

    //Se guardó exitosamente, mostramos la id_dif/curp generada
    if($respuesta == 1 && $id_dif){
        
        //Vemos si fue generada la curp
        $es_generada = substr($id_dif, 0,2);
        //Obtenemos curp
        $curp = substr($id_dif, 2);

        $mensaje .= ($es_generada == "SI")? '. ID DIF: '.$curp : '. CURP: '.$curp;

    }

}

//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL && $respuesta == NULL){
    //No existen registros
    list($mensaje,$clase) = Permiso::mensajeRespuesta(8);
}

//Obtenemos acciones del menú
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>servicios/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>servicios/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>


     <div id="principal">
        <div id="contenido">
            <div class="centro">       
                <div  align="center">
                 <h2 class="centro">Listado de Servicios</h2>
            <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"   />                
                <form id='formfiltro' method="get" action='listado_servicio.php'>        
                <table>
                <tr>
                  <td>
                    <label class="obligatorio">*</label><label for="ID_C_DEPENDENCIA">Dependencia</label>
                  </td>
                  <td>
                    <label for="ID_C_PROGRAMA">Programas</label>
                  </td>
                  
                </tr>
                <tr>
                  <td>
                    <select class="filtra_programa_busqueda" id="ID_C_DEPENDENCIA" name="ID_C_DEPENDENCIA">
                    <option value=''>Seleccione Dependencia</option>
                      <?php foreach($dependencia as $d): ?>
                       <option value='<?php echo $d['ID_C_DEPENDENCIA'] ?>' >
                          <?php echo $d['NOMBRE'];?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td id="programas">
                    <select class="combobox" id="ID_C_PROGRAMA" name="ID_C_PROGRAMA">
                    <option value=''>Seleccione Programa</option>                   
                    </select>
                 </td>
                <td><input type="submit" id="boton"  value="Buscar" /></td></td> 
                </tr>
                </table>
                </form>
                </div>
            </div>
            
            

        
        <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
     
     <div id="page_list" align="center">        
    <p>
        <?php if(array_key_exists('alta_beneficiario',$central)){ ?>
        <a  id = 'enviar' href="alta_beneficiario.php">Agregar</a>
        <?php } ?>
    </p> 
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
        <th>Dependencia</th>
        <th>Programa</th>
        <th>Servicio</th>
        <th>Stock Predeterminado</th>
        <!--
        <th>Stock Inicial</th>
        <th>Stock Actual Caravana</th>
        <th>Caravana</th>
         -->
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['dependencia']; ?></td>
            <td><?php echo $l['programa']; ?></td>
            <td><?php echo $l['servicio'];?></td>
            <td><?php echo ($l['ES_CONTABLE']=='NO')? 'No aplica' : $l['STOCK'];?></td>
            <!--
            <td><?php /* echo ($l['ES_CONTABLE']=='NO')? 'No aplica' : $l['STOCK_INICIAL'];?></td>
            <td><?php echo ($l['ES_CONTABLE']=='NO')? 'No aplica' : $l['stock_caravana'];?></td>
            
            <td><?php echo $l['caravana'];*/?></td>
            -->
            <td>                            
                <?php //if($l['CVE_ESTATUS']==1){ ?>
                <?php //if(array_key_exists('pys_beneficiario',$central)){ ?>
                <?php if($l['ES_CONTABLE']=='SI'){?>
                  <div title="Edita Stock" class="ui-state-default ui-corner-all lista">
                    <a class="ui-icon ui-icon-circle-triangle-e" href="<?php echo $_SESSION['app_path_p'].'servicios/serv/edita_stock.php'?>?id_edicion=<?php echo $l['ID_C_SERVICIO']; ?>"></a>
                  </div>
                <?php } ?>                 
                <?php //} ?>
                <?php //}?>                       
            </td>
        </tr>

        <?php endforeach; ?>      
        
    </tbody>
    </table>        
    <?php } ?>
        </div>
     </div>  
       </div>       
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>

