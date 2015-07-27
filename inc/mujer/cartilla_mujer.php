<?php
session_start();  

    if(!isset($db)){
        //Librería de conexión
        include($_SESSION['inc_path']."conecta.php");
    }    

    //Incluimos librería 'Artículos' para obtener todo lo relacionado al servicio
    include_once($_SESSION['inc_path'].'libs/cartilla_carrito.php');

    //Incluimos librería de permiso
    include_once($_SESSION['inc_path'].'libs/Permiso.php');
    include_once($_SESSION['inc_path'].'libs/Fechas.php');
    include_once($_SESSION['model_path'].'mujeres_avanzando.php');

    //Variable de mensaje
    $msg = "";
    $mensaje = "";
    
    //ruta de imagen
    $ruta = $_SESSION['img_path']."mujeres/";   

    //ruta raiz
    $ruta_raiz = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS']; 

    /*
    echo $_POST["accion"];
    echo $_POST["id"];
    */

    //Verificamos que se nos envíen las variables necesarias
    if($_POST["accion"]){

        //Recibimos variables
        $accion = $_POST["accion"]; 
        $id = $_POST["id"];
       
 
      
        //Dependiendo la acción, buscamos la función
        switch($accion){

            case 'agregar':
                        $msg = agregarArticulo($id);
                        break;

            case 'eliminar':
                        $msg = eliminarArticulo($id);
                        break;

            case 'listado':
                        $msg = listadoArticulo($ID_C_SERVICIO);
                        break;
            case 'vaciar':
                        $msg = vaciarCarrito();
                        break;
        }

    }else{

        //$mensaje = "No se seleccionó ninguna acción";

    }    

    //Si obtenemos un código de mensaje
    if($msg){
        //Obtenemos mensaje y clase
        list($mensaje,$clase) = Permiso::mensajeRespuesta($msg);
    }    

    //Agregamos artículos al carrito de artículos
    function agregarArticulo($id_articulo = 0){

    //Preparamos variables

        $A = "";

        if (!$_SESSION['arrayArt']){
            $A = new Cartilla();
            //echo ("Instancia");
        } else {
            $A = unserialize($_SESSION['arrayArt']);
            //echo ("Deserializar");
        }

        //Si recibimos un artículo
        if($id_articulo){

            //Agregamos 1 artículo
            $mensaje = $A->agregar($id_articulo);                

            //Si obtenemos mensaje de error en el carrito, lo mostramos  
            if($mensaje){
                return $mensaje;
            }else{
                //No hubo error, serializamos el objeto y mostramos mensaje de agregado
                $_SESSION['arrayArt'] = serialize($A);
                //$mensaje = 'Servicio agregado';   
            }

        }else{
            //No se agreg&oacute; a la beneficiar&iacute;a, seleccione uno
            $mensaje = 25;
        }                   

        return $mensaje;              

    }

    //Eliminamos artículos del carrito    
    function eliminarArticulo($posicion){

        //Quitamos de cada arreglo el valor que corresponde con el $id, quitando 1 producto en total
        if ($A = unserialize($_SESSION['arrayArt'])) {

            $A->dilete($posicion);

            /*Si todavía tenemos un artículo, serializamos el objeto, 
            caso contrario, eliminamos la variable de sesión*/            
            if (count($A->articulo_id)) {
               $_SESSION['arrayArt'] = serialize($A);
            } else{
                unset($_SESSION['arrayArt']);
            }                
            
            //Beneficiaria descartada
            $mensaje = 26;

        } else {
            
            //Error con el arreglo
            $mensaje = 27;

        }

        return $mensaje;

    }

    //Vaciamos Carrito
    function vaciarCarrito(){
        unset($_SESSION['arrayArt']);
        return 28;
    }

?>



<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>
<style type="text/css">
    .foto_cred{
        /*position: absolute;*/
        display: block;
        top: 1.6cm;
        left: 0.74cm;
        width: 2.37cm;
        height: 2.75cm;                
    }
</style>

<?php if($mensaje){ ?>
<div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
<?php } ?>

<?php
    
    if($_SESSION['arrayArt']){

        //Obtenemos el carrito
        $articulos = unserialize($_SESSION['arrayArt']);

        /*Si el objeto tiene elementos, mostramos la tabla, caso contrario
        notificamos que no hay artículos por mostrar*/
        if(isset($articulos)&& isset($articulos->articulo_id)){
    ?>
 <h2 class="centro">Impresi&oacute;n De Fichas Para Cartilla</h2>
 <h2 class="centro">
    <button id="borra_cartilla">Vaciar Listado</button>
    </h2>
<table style="font-size:14pt;" class="tablesorter">             
    <thead> 
        <tr>
            <th>Foto</th>
            <th style="width: 50px;">&nbsp;</th>
            <th>Beneficiaria</th>
            <th>Acci&oacute;n</th> 
        </tr> 
    </thead>
    <tbody>
    <?php foreach($articulos->articulo_id as $key => $value):
       
       $mujer = mujeresAvanzando::get_by_id($value);
        //Verificamos si la imagen existe, de no estarlo ponemos imagen default
       $ruta_imagen = (file_exists($ruta_raiz.$mujer['folio_compuesto'].".png"))? $ruta.$mujer['folio_compuesto'].".png?i=". filemtime($ruta_raiz.$mujer['folio_compuesto'].'.png') : $ruta."default.png";

    ?>
        <tr class="zebra"> 
            <td width="95">
                <div class="foto_cred" 
                style="background: url(<?php echo $ruta_imagen; ?>) center center ; background-size: auto 100%;">
                </div>
            </td>
            <td>
              <button id="<?php echo $value; ?>" name="<?php echo $mujer['folio_compuesto'];?>" 
              class="foto">Tomar Foto</button>
            </td>
            <td>
                <?php $calle = $mujer['calle'].' No. '.$mujer['num_ext']; ?>
                <?php echo '<B>NOMBRE:</B> '.$mujer['nombre_completo'];?>
                <?php echo '<br><B>FECHA DE NACIMIENTO:</B> '.Fechas::fechacorta('/',Fechas::fechaymdAdmy($mujer['fecha_nacimiento'])).' ('. $mujer['edad'].' a&ntilde;os)';?>
                <?php //echo '<B>DOMICILIO:</B> '.$mujer['calle'].' '.$mujer['num_ext'].' '.'INTERIOR '.$mujer['num_int'].'<br>';?>
                <?php echo '<br><B>DOMICILIO:</B> '.$calle .= (isset($mujer['num_int']) && (strtoupper($mujer['num_int']) != 'S/N') )? ' INTERIOR '.$mujer['num_int'] : '';?>
                <?php echo '<br><B>COLONIA:</B> '.$mujer['colonia'];?>
                <?php echo '<br><B>MUNICIPIO:</B> '.$mujer['NOM_MUN'];?>
                <?php echo '<br><B>C&Oacute;DIGO POSTAL:</B> '.$mujer['CODIGO'];?>
                <?php echo '<br><B>TELEFONO:</B> '.$mujer['telefono'].'<BR>';?>
                <a href="edita_mujer.php?id_edicion=<?php echo $mujer['id']; ?>">
                    <b>[Modificar Datos]</b>
                </a><br/><br/>
                <?php if(mujeresAvanzando::verifImpCartilla($mujer['folio']) != NULL){?>                    
                <label id="lbl<?php echo $mujer['id'];?>" 
                for="txt<?php echo $mujer['id'];?>">
                Motivo de Reposici&oacute;n
                </label><br/>
                <textarea id="txt<?php echo $mujer['id'];?>" 
                name="txt<?php echo $mujer['id'];?>" cols="5" rows="2"></textarea>
                <?php } ?>
            </td>
            
            <td>
              <input id="elimina_art" type="button" name="<?php  echo $key;?>" value="Quitar Del Listado" /><br />
              
              <?php 
              //Aqui debería hacerse la verificación, un match entre mujeres_avanzando
              //y log_mujeres_avanzando (mediante el folio) y que la fecha de impresión
              //NO sea nula 
              if(mujeresAvanzando::verifImpCartilla($mujer['folio']) != NULL){?>
              <input id="chk<?php echo $mujer['id']?>" class='report' 
              value="<?php echo $mujer['id']?>" checked name="reposicion[]" 
               type="checkbox">
               <label for="chk<?php echo $mujer['id']?>">
               Reposici&oacute;n
               </label>
              <?php } ?>
             <!--
              <form name="guarda_imagen" id="guarda_imagen" action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" id='folio' name="folio" value="<?php /* echo $value; ?>" />  
                  <input type="file" id="img<?php echo $value; ?>" name="img<?php echo $value;*/ ?>" />
                  <input type="submit" name="button" id="button" value="Subir Imagen" />
              </form>                
              -->
            </td>      
        </tr>
        
           
        
      <?php endforeach;?>
    </tbody>
</table>
<button id="vista">Imprimir fichas seleccionadas</button>   

<?php }else{ ?>

    <div class="mensaje info_msg" >No existen servicios guardados</div> 

<?php } ?>

<?php } ?>