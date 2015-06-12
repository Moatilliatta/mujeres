<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');
include_once($_SESSION['model_path'].'servicio_caravana.php');
include_once($_SESSION['model_path'].'dependencia.php');
include_once($_SESSION['model_path'].'programa.php');
include_once($_SESSION['model_path'].'servicio.php');

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Obtenemos ID de mujer
$id_mujeres_avanzando = intval($_GET['id_edicion']);

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

//Arreglos
$mujeres_avanzando = NULL;
$servicios_grado = NULL;
$dependencias_grado = NULL;
$dependencias_resto = NULL;
$programas = NULL;

//GIA
$id_grado = NULL;

//Servicio de la beneficiaria
$id_servicio = NULL;

//Si tenemos el ID de una mujer
if($id_mujeres_avanzando){

    //Obtenemos datos de mujer
    $mujeres_avanzando = mujeresAvanzando::get_by_id($id_mujeres_avanzando);

    //Si la mujer existe obtendremos los servicios
    if($mujeres_avanzando != NULL){
        
        /*
        //Listado de Dependencias
        $dependencias_grado = Dependencia::listaDependencias(NULL,$id_mujeres_avanzando);
        //Listado de Dependencias
        $dependencias_resto = Dependencia::listaDependencias(NULL,$id_mujeres_avanzando,true);
        */

        //Listado de Dependencias
        $dependencias_grado = Dependencia::listaDependencias();
        //Listado de Dependencias
        $dependencias_resto = Dependencia::listaDependencias();

        //Obtenemos el grado
        $id_grado = $mujeres_avanzando['id_grado'];

    }    

    //Obtenemos el servicio de la mujer
    $c_mujeres_avanzando_detalle = mujeresAvanzandoDetalle::get_servicio($id_mujeres_avanzando);

    if($c_mujeres_avanzando_detalle != NULL){
        $id_servicio = $c_mujeres_avanzando_detalle['ID_C_SERVICIO'];
    }
}

?>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery-ui-1.10.3.custom.min.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida_asign.js"></script>

<div id="principal">
   <div id="contenido">
    <div>
    <h2 class="centro">Asignar Servicio a Mujer</h2>
     
     <!-- <input style="" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"   />-->
    
    </div> 
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div align="center">                
        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            if($respuesta != 1 && $mujeres_avanzando != NULL){ ?>
                
            <table class='tablesorter'>
                <thead>
                <tr>
                    <th>Nombre:</th>
                    <th>Grado de Insuficiencia Alimentaria:</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                    <td><?php echo $mujeres_avanzando['nombres'].' '.
                              $mujeres_avanzando['paterno'].' '.
                              $mujeres_avanzando['materno']; ?>
                    </td>
                    <td><?php echo $mujeres_avanzando['grado']; ?></td>
                </tr>
                </tbody>
            </table>

            <form id='formAsig' method="post" action='save_mujer_serv.php'>
                <input type="hidden" name="id_edicion" value="<?php echo $c_mujeres_avanzando_detalle['ID_MUJERES_AVANZANDO_DETALLE']; ?>" />
                <input type="hidden" name="ID_MUJERES_AVANZANDO" value="<?php echo $id_mujeres_avanzando; ?>" />
                <div id="tabs">
                <ul>
                <li><a href="#tabs-1">Recomendado</a></li>
                <li><a href="#tabs-2">Resto</a></li>
                </ul>
                            
                <div id="tabs-1">

                <?php 
                //Listamos las dependencias del grado de la beneficiaria
                if($dependencias_grado != NULL){ ?>

                <?php foreach ($dependencias_grado as $key => $value):?>                
                        <h3><?php echo $value['NOMBRE'];?></h3>

                        <?php 
                        //Listado de Programas por dependencia
                        $programas = Programa::listaPrograma(NULL,$value['ID_C_DEPENDENCIA']);                    

                        foreach ($programas as $k => $v) {?>
                        
                        <h4><?php echo $v['programa'];?></h4>

                        <?php 

                        //Listado de Servicios, si el servicio corresponde al grado que tiene
                        //el beneficiario, lo resaltaremos con algún color y se le asigna la
                        //clase 'recomendado'
                        $servicios_grado = ServicioCaravana::listado(NULL,$v['ID_C_PROGRAMA'],NULL,NULL,$id_grado);
                        ?>

                        <table class='tablesorter'>
                            <thead>
                            <tr>
                                <th style="width:20px;">&nbsp;</th>
                                <th>Servicio</th>
                                <th>Stock</th>                    
                            </tr>                        
                            </thead>
                            <tbody>
                            <?php foreach ($servicios_grado as $llave => $valor):?>
                            
                            <tr class="<?php echo ($valor['ID_GRADO'] == $id_grado)? 'recomendado' : ''  ?>">                                                                 
                                <td>
                                <?php if($valor['ES_CONTABLE'] == 'SI' && $valor['stock_caravana'] > 0 ||
                                $valor['ES_CONTABLE'] == 'NO'){ ?>

                                <input type="radio" name="ID_C_SERVICIO" 
                                 value="<?php echo $valor['ID_C_SERVICIO']?>" 
                                <?php echo ($id_servicio == $valor['ID_C_SERVICIO'])? 'checked' : '' ?>
                                >
                                <?php }?>
                                </td>                                
                                <td><?php echo $valor['servicio']?></td>
                                <td>
                                <?php if($valor['ES_CONTABLE'] == 'SI' && $valor['stock_caravana'] > 0 ||
                                $valor['ES_CONTABLE'] == 'NO'){
                                    echo (intval($valor['stock_caravana']) > 0)? intval($valor['stock_caravana']) : '';
                                    }else{
                                        echo '0 (Sin stock disponible)';
                                        }  ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>                    

                        <?php } ?>                
                <?php endforeach;?>                        
                

                <?php }else{ ?>

                    <div class="mensaje"><?php echo 'No hay servicios disponibles';?></div>

                <?php } ?>

                </div>

                <div id="tabs-2">

                    <?php 
                //Listamos las dependencias del grado de la beneficiaria
                if($dependencias_resto != NULL){ ?>

                <?php foreach ($dependencias_resto as $key => $value):?>                
                        <h3><?php echo $value['NOMBRE'];?></h3>

                        <?php 
                        //Listado de Programas por dependencia
                        $programas = Programa::listaPrograma(NULL,$value['ID_C_DEPENDENCIA']);                    

                        foreach ($programas as $k => $v) {?>
                        
                        <h4><?php echo $v['programa'];?></h4>

                        <?php 

                        //Listado de Servicios
                        $servicios_resto = ServicioCaravana::listado(NULL,
                                                                     $v['ID_C_PROGRAMA'],
                                                                     NULL,
                                                                     NULL,
                                                                     $id_grado,
                                                                     true);

                        if($servicios_resto != NULL){                        
                        ?>

                        <table class='tablesorter'>
                            <thead>
                            <tr>
                                <th style="width:20px;">&nbsp;</th>
                                <th>Servicio</th>
                                <th>Stock</th>                    
                            </tr>                        
                            </thead>
                            <tbody>
                            <?php foreach ($servicios_resto as $llave => $valor):?>
                            
                            <tr>
                                <td>
                                <?php if($valor['ES_CONTABLE'] == 'SI' && $valor['stock_caravana'] > 0 ||
                                $valor['ES_CONTABLE'] == 'NO'){ ?>

                                <input type="radio" name="ID_C_SERVICIO" 
                                value="<?php echo $valor['ID_C_SERVICIO']?>"
                                <?php echo ($id_servicio == $valor['ID_C_SERVICIO'])? 'checked' : '' ?>
                                >
                                <?php }?>
                                </td>
                                <td><?php echo $valor['servicio']?></td>
                                <td>
                                <?php if($valor['ES_CONTABLE'] == 'SI' && $valor['stock_caravana'] > 0 ||
                                $valor['ES_CONTABLE'] == 'NO'){
                                    echo (intval($valor['stock_caravana']) > 0)? intval($valor['stock_caravana']) : '';
                                    }else{
                                        echo '0 (Sin stock disponible)';
                                        }  ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>                    

                        <?php }else{ ?>

                        <div class="mensaje"><?php echo 'No hay servicios disponibles';?></div>
                            
                        <?php } ?>

                        <?php } ?>                
                <?php endforeach;?>                        
                

                <?php }else{ ?>

                    <div class="mensaje"><?php echo 'No hay servicios disponibles';?></div>

                <?php } ?>

                </div>
            </div>            
            
            <div>
                <input type="submit" value="Guardar" id="guardar" />
            </div>
                
            </form>            

        <?php }else{ ?>

        <div class="mensaje"><?php echo 'Registro Inexistente';?></div>

        <?php } ?>
    </div>
    </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>