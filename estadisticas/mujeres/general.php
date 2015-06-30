<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'log_mujeres_avanzando.php');
include_once($_SESSION['model_path'].'log_mujeres_cart.php');

//Variables
$p = NULL;
$lista = NULL;
$folios_caravana = NULL;
$total_folios_car = NULL;
$cartillas_car = NULL;
$total_cartillas_car = NULL;
$cartillas_en_car = NULL;
$total_cart_en_car = NULL;
$cartillas_ext = NULL;
$total_cart_ext = NULL;
$impresionesCaravana = NULL;
$totalesImp = NULL;
$reposicionesCart = NULL;
$repLogMujeresCart = NULL;

//Si requerimos obtener totales por fecha
$fecha_creacion = ($_GET['fecha_creacion'] != NULL)? $_GET['fecha_creacion']: NULL;
$id_caravana = ($_GET['id_caravana'] != NULL)? $_GET['id_caravana'] : NULL;

//Listamos los programas del beneficiario
list($lista,$p) = logMujeresAvanzando::listaLog(null,null,$id_caravana,$fecha_creacion);

//Obtenemos caravanas disponibles
//$db->where ('activo', 1);
$caravanas = $db->get('caravana');

//Obtenemos totales por caravana
$cartillas_car = mujeresAvanzando::cartillas_car($fecha_creacion);
$total_cartillas_car = mujeresAvanzando::total_cartillas_car();

//Obtenemos totales de cartillas entregadas en caravana
$cartillas_en_car = mujeresAvanzando::cartillas_en_car($fecha_creacion);//Rev
$total_cart_en_car = mujeresAvanzando::total_cartillas_en_car();

//Obtenemos totales de cartillas entregadas de forma extratemporal
$cartillas_ext = mujeresAvanzando::cartillas_ext($fecha_creacion);//Rev
$total_cart_ext = mujeresAvanzando::total_cartillas_ext();

//Obtenemos totales por folio
$folios_caravana = mujeresAvanzando::folio_car($fecha_creacion);
$total_folios_car = mujeresAvanzando::total_folios_car();


//Obtenemos total de impresiones
$impresionesCaravana = logMujeresAvanzando::impresionesCaravana();
$totalesImp = logMujeresAvanzando::totalImpresiones();

//Obtenemos total de reposiciones
$reposicionesCart = logMujeresAvanzando::reposicionesCartilla();

//Obtenemos reposiciones de la tabla log_mujeres_cart
$repLogMujeresCart = logMujeresCart::listaLogMujeresCart();
$totalesRep = logMujeresCart::total_rep();


//Mensaje respuesta
$respuesta = ($_GET['r'] == NULL && $lista == NULL)? 8 : $_GET['r'];
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

?>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
   <div id="contenido">
        
        <h2 class="centro">Estad&iacute;sticas Generales Mujeres Avanzando</h2>

        <?php if($respuesta > 0){?>
        
        <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
        
        <?php } ?>

        <div class="centro">              
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                <table>
                <tr>
                    <th>Caravana</th>
                    <td>
                        <select id="id_caravana" name="id_caravana">
                            <option value="">Seleccione Caravana</option>
                            <?php foreach($caravanas as $c): 
                            $selected = ($c['id'] == $id_caravana )? 'selected' : ''; 
                            ?>
                                <option value='<?php echo $c['id'] ?>'  <?php echo $selected;?> > 
                                    <?php echo $c['descripcion'];?>
                                </option>
                            <?php endforeach; ?>                       
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Fecha Creaci&oacute;n</th>
                    <td>
                        <input type="text" id="fecha_creacion" class="fecha" name="fecha_creacion"/>
                    </td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value ="Filtrar"/></td>
                </tr>
                </table>
            </form>     

            <div style="text-align:center;">
            
            <?php if($lista != NULL){  
                //Si tenemos listado ?>       
                <table class="tablesorter">
                <thead>
                <tr>
                    <th>Folio</th>
                    <th>Nombres</th>
                    <th>A. Paterno</th>
                    <th>A. Materno</th>
                    <th>Caravama</th>        
                    <th>Fecha Foto</th>
                    <th>Fecha Impresi&oacute;n</th>        
                    <th>Fecha Creaci&oacute;n</th>
                </tr>            
                </thead>

                <tbody>
                    <?php foreach($lista as $l): ?>
                    <tr>
                        <td><?php echo $l['folio']; ?></td>
                        <td><?php echo $l['nombres']; ?></td>
                        <td><?php echo $l['paterno']; ?></td>
                        <td><?php echo $l['materno'];?></td>
                        <td><?php echo $l['caravana'];?></td>            
                        <td><?php echo $l['fecha_foto'];?></td>
                        <td><?php echo $l['fecha_impresion'];?></td>
                        <td><?php echo $l['fecha_creacion'];?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>

                 <p>
                  <?php echo $p->display();?>
                </p> 

             <?php } ?>

            </div>

            <?php if($reposicionesCart != NULL){ ?>
            <div style="float:right; width: auto; margin: 3em">
                <table class="tablesorter" >
                <thead>
                    <tr>
                        <th colspan="2">Reposiciones de Cartillas (Hist&oacute;rico)</th>
                    </tr>
                    <tr>
                        <th>Folio</th>
                        <th>Total de Reposiciones</th>        
                    </tr>        
                </thead>

                <tbody>
                <?php foreach ($reposicionesCart as $key => $l):?>
                    <tr>
                        <td><?php echo $l['folio']; ?></td>
                        <td><?php echo $l['tot_folio'];?></td>            
                    </tr>
                <?php endforeach;?>        
                </tbody>
                </table>   

                <table class="tablesorter">
                    <thead>
                    <tr>
                        <th>Totales</th>
                        <td><?php echo count($reposicionesCart) ?></td>
                    </tr>    
                    </thead>
                    <tbody></tbody>
                </table>   

            </div>    
            <?php } ?>

            <?php if($cartillas_car != NULL){?>

            <div style=" width: 400px; margin: 3em">   
            <table class="tablesorter">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre de Caravana</th>        
                    <th>Cartillas Entregadas</th>
                    <th>Entregada en Caravana</th>
                    <th>Entregado Extratemp</th>
                    <th>Total Folios</th>
                </tr>        
                </thead>

                <tbody>
                <?php foreach ($cartillas_car as $k => $c):?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>                        
                        <td><?php echo $c['caravana']; ?></td>            
                        <td><?php echo $c['cartilla_car']; ?></td>
                        <td><?php echo (isset($cartillas_en_car[$c['id']]['en_car']))? 
                                            $cartillas_en_car[$c['id']]['en_car'] : '0'; ?>
                        </td>
                        <td><?php echo (isset($cartillas_ext[$c['id']]['foto_ext']))? 
                                            $cartillas_ext[$c['id']]['foto_ext'] : '0'; ?>
                        </td>
                        <td><?php echo (isset($folios_caravana[$c['id']]['folios_car']))?
                                            $folios_caravana[$c['id']]['folios_car'] : '0'; ?>
                        </td>
                    </tr>
                <?php endforeach;?>                    
                </tbody>
                </table>    

                <table class="tablesorter">
                    <thead>
                        <tr>
                        <th>Totales</th>
                        <td><?php echo (isset($total_cartillas_car['total_cartilla_car']))?
                            $total_cartillas_car['total_cartilla_car'] : '0'; ?></td>
                        <td><?php echo (isset($total_cart_en_car['total_en_car']))?
                            $total_cart_en_car['total_en_car'] : '0'; ?></td>
                        <td><?php echo (isset($total_cart_ext['total_foto_ext']))?
                            $total_cart_ext['total_foto_ext'] : '0'; ?></td>
                        <td><?php echo (isset($total_folios_car['total_folios_car']))?
                            $total_folios_car['total_folios_car'] : '0'; ?></td>
                    </tr>    
                    </thead> 
                    <tbody></tbody>       
                </table>           
            </div>
            <?php } ?>

            <?php if($impresionesCaravana != NULL){?>
            <div style="width: 400px; margin: 3em">        
                <table class="tablesorter" >
                <thead>
                    <tr>
                        <th>Nombre de Caravana</th>
                        <th>Total Impresiones</th>        
                    </tr>        
                </thead>

                <tbody>
                <?php foreach ($impresionesCaravana as $k => $c):?>
                    <tr>
                        <td><?php echo $c['caravana']; ?></td>
                        <td><?php echo $c['total_imp'];?></td>            
                    </tr>
                <?php endforeach;?>        
                </tbody>
                </table>   

                <table class="tablesorter">
                    <thead>
                    <tr>
                        <th>Totales</th>
                        <td><?php echo $totalesImp ?></td>
                    </tr>    
                    </thead>
                    <tbody></tbody>
                </table>      
            </div>
            <?php } ?>        

            <?php if($repLogMujeresCart != NULL){ ?>
            <div style="width: 400px; margin: 3em">
                <table class="tablesorter" >
                <thead>
                    <tr>
                        <th colspan="3">Reposiciones de Cartillas (desde 22-Junio-2015)</th>
                    </tr>
                    <tr>
                        <th>Folio</th>
                        <th>Nombre Completo</th>
                        <th>Total Reimpresiones</th>        
                    </tr>        
                </thead>

                <tbody>
                <?php foreach ($repLogMujeresCart as $k => $c):?>
                    <tr>
                        <td><?php echo $c['folio']; ?></td>
                        <td><?php echo $c['nombre_completo'];?></td>
                        <td><?php echo $c['num_rep']; ?></td>
                    </tr>
                <?php endforeach;?>        
                </tbody>
                </table>   

                <table class="tablesorter">
                    <thead>
                    <tr>
                        <th>Totales</th>
                        <td><?php echo $totalesRep; ?></td>
                    </tr>    
                    </thead>
                    <tbody></tbody>
                </table>      
            </div>
            <?php } ?>

        </div>
    </div>
</div>
 
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>