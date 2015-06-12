<?php
session_start();//Habilitamos uso de variables de sesión

//Variable de respuesta

//Si requerimos obtener totales por fecha
$fecha_creacion = ($_GET['fecha_creacion'] != NULL)? $_GET['fecha_creacion']: NULL;
$id_caravana = ($_REQUEST['id_caravana'] != NULL)? $_REQUEST['id_caravana'] : NULL;
$excel = ($_POST['excel'] != NULL)? $_POST['excel']: NULL;

//Imprimimos o no cabecera de excel
if($excel != NULL){
    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");
    //Librería de permisos
    include($_SESSION['inc_path'].'libs/Permiso.php'); 

    header("Content-Type: application/vnd.ms-excel"); 
    header("content-disposition: attachment;filename=reporte_cartillas_entregadas.xls");
}else{    
    //Incluimos cabecera
    include('../../inc/header.php');    
}

$C = NULL;
if($id_caravana){
    $C = $db->where('id', $id_caravana)
                      ->getOne('caravana');
}

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'historico_gia.php');

//Obtenemos caravanas disponibles
$caravanas = $db->get('caravana');

if($id_caravana){

    //Obtenemos totales de visitas
    $total_visitas = HistoricoGIA::totalVisitas($id_caravana);

    //Obtenemos total de visitas
    foreach ($total_visitas as $key => $value):
        $visitas[$value] = HistoricoGIA::listaBenGIA($id_caravana,$value);
    endforeach;

    //Arreglo base (actual, paginado)
    if($excel == NULL){ 

    list($beneficiariasCambio,$p) = mujeresAvanzando::listadoCambioGIA($id_caravana);

    }else{

    $beneficiariasCambio = mujeresAvanzando::listaCambioGIA($id_caravana);

    }

}

//Mensaje respuesta
if($_GET['id_caravana'] != NULL && $beneficiariasCambio == NULL){
    $respuesta =  8;
}

list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);


if($excel == NULL){ ?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>


<?php } ?>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">
        Cambio de GIA en beneficiarias
        <?php if($id_caravana){
            echo ($excel != NULL)? "'".utf8_decode($C['descripcion'])."'" : '';
        } ?>
    </h2>

    <?php if($excel == NULL){ ?>

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
            <td>&nbsp;</td>
            <td><input type="submit" value ="Filtrar"/></td>
        </tr>
        </table>
    </form> 

     <div class="centro">        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
        <div>
            <input type="hidden" id="excel" name="excel" value="1" />
            <input type="hidden" id="id_caravana" name="id_caravana" value="<?php echo $id_caravana;?>">
            <input type="submit" value ="Exportar a Excel"/>
        </div>        
        </form> 
    </div>

    <?php } ?>

    <?php if($beneficiariasCambio != NULL){ ?>

    <?php
    //Si tenemos listado
    if(isset($p) && $excel == NULL){ ?>
    <p>
        <?php
        // Listado de páginas del paginador
        echo $p->display();
        ?>
    </p>  
    <?php } ?>


    <div style="width: inherit; margin: 3em; overflow-y: auto;">   

    <table class="tablesorter">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre(s)</th>        
        <th>A. Paterno</th>
        <th>A. Materno</th>
        <th>Folio Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>Grado Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>Nivel Soc. Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>Calidad Dieta Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>Diversidad Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>Variedad Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>

        <th>ELCSA Original</th>

        <?php foreach ($total_visitas as $key => $value):?>
            <th>Visita <?php echo $value; ?></th>
        <?php endforeach; ?>
    </tr>        
    </thead>

    <tbody>
    <?php foreach ($beneficiariasCambio as $k => $l):?>
        <tr>
            <td><?php echo $l['id_mujeres_avanzando']; ?></td>                        
            <td><?php echo $l['nombres']; ?></td>
            <td><?php echo $l['paterno']; ?></td>
            <td><?php echo $l['materno']; ?></td>

            
            <?php 

            foreach ($total_visitas as $key => $v):?>
            <td>
            <?php             
            //Si tenemos el folio correspondiente al id_mujeres_avanzando
            //asignamos el mismo valor
            
            $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['folio']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['folio'] == NULL){
                //Si estamos en la última visita, obtenemos el folio de la visita anterior
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['folio'] == NULL){
                //Si no es la última visita, obtenemos el folio siguiente
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['folio'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['folio']; ?></td>

            <?php 

            foreach ($total_visitas as $key => $v):?>
            <td>
            <?php $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['grado']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['grado'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['grado'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['grado'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['grado']; ?></td>

            <?php 

            foreach ($total_visitas as $key => $v):?>
            <td>
            <?php $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['nivel']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['nivel'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['nivel'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['nivel'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['nivel']; ?></td>

            <?php foreach ($total_visitas as $key => $v):?>
            <td>
            <?php     

            $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['calidad_dieta']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['calidad_dieta'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['calidad_dieta'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['calidad_dieta'] : '';
            ?>
            </td>

            <?php endforeach; ?>
            <td><?php echo $l['calidad_dieta']; ?></td>

            <?php foreach ($total_visitas as $key => $v):?>
            <td>
            <?php     

            $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['diversidad']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['diversidad'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['diversidad'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['diversidad'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['diversidad']; ?></td>

            <?php foreach ($total_visitas as $key => $v):?>
            <td>
            <?php     

            $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['variedad']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['variedad'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['variedad'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['variedad'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['variedad']; ?></td>

            <?php foreach ($total_visitas as $key => $v):?>
            <td>
            <?php     

            $i = NULL;

            if($visitas[$v][$l['id_mujeres_avanzando']]['elcsa']){
                $i = $v;                
            }elseif(($key + 1) == (count($visitas)) && $visitas[$v][$v]['elcsa'] == NULL){
                $i = $v - 1;                
            }elseif($key >= 0 && $visitas[$v][$v]['elcsa'] == NULL){
                $i = $v + 1;                
            }   
            echo ($i != NULL)? $visitas[$i][$l['id_mujeres_avanzando']]['elcsa'] : '';
            ?>
            </td>
            <?php endforeach; ?>
            <td><?php echo $l['elcsa']; ?></td>
            
        </tr>
    <?php endforeach;?>                    
    </tbody>
    </table>    

   
    </div>
    <?php } ?>    
  
  </div>
 </div>

</div>
 
<?php if($excel == NULL){ 

    //Incluimos pie
    include($_SESSION['inc_path'].'/footer.php');
}
?>