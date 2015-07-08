<?php
session_start();   

    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");

    //Incluimos librería 'Artículos' para obtener todo lo relacionado al servicio
    include_once($_SESSION['inc_path'].'libs/Articulos.php');

    //Variable de mensaje
    $mensaje = "";

    //Verificamos que se nos envíen las variables necesarias
    if($_POST["accion"]){

        $mensaje = "";

        //Recibimos variables
        $accion = $_POST["accion"]; 
        $ID_C_SERVICIO = $_POST["ID_C_SERVICIO"];
        $id_mujeres_avanzando = $_POST["id_mujeres_avanzando"];

        //Dependiendo la acción, buscamos la función
        switch($accion){

            case 'agregar':
                        $mensaje = agregarArticulo($ID_C_SERVICIO,$id_mujeres_avanzando);
                        break;

            case 'eliminar':
                        $mensaje = eliminarArticulo($ID_C_SERVICIO);
                        break;

            case 'listado':
                        $mensaje = listadoArticulo($ID_C_SERVICIO);
                        break;

        }

    }else{

        $mensaje = "No se seleccionó ninguna acción";

    }    

    //Agregamos artículos al carrito de artículos
    function agregarArticulo($id_articulo = 0,$id_mujeres_avanzando = 0){

    //Preparamos variables

        $A = "";
        $mensaje = "";

        if (!$_SESSION['arrayArt']) {
            $A = new Articulos();
            //echo ("Instancia");
        } else {
            $A = unserialize($_SESSION['arrayArt']);
            //echo ("Deserializar");
        }

        //Si recibimos un artículo
        if($id_articulo){

            //Agregamos 1 artículo
            $mensaje = $A->agregar($id_articulo,$id_mujeres_avanzando);                

            //Si obtenemos mensaje de error en el carrito, lo mostramos  
            if($mensaje){
                return $mensaje;
            }else{
                //No hubo error, serializamos el objeto y mostramos mensaje de agregado
                $_SESSION['arrayArt'] = serialize($A);
                //$mensaje = 'Servicio agregado';   
            }

        }else{

            $mensaje = "No se agreg&oacute; el servicio, seleccione uno";
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

            $mensaje = "Servicio dado de baja";

        } else {

            $mensaje = "Error con el arreglo";

        }

        return $mensaje;

    }

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<?php if($mensaje){ ?>
<div class="mensaje">
    <?php echo $mensaje; ?>
</div>
<?php } ?>

<?php
    
    if($_SESSION['arrayArt']){

        //Obtenemos el carrito
        $articulos = unserialize($_SESSION['arrayArt']);

        /*Si el objeto tiene elementos, mostramos la tabla, caso contrario
        notificamos que no hay artículos por mostrar*/
        if(isset($articulos)&& isset($articulos->articulo_id)){
    ?>

<table class="tablesorter">             
    <thead> 
        <tr>
            <th style="width: 50px;">&nbsp;</th>
            <th>Servicio</th>
            <th>Fecha Asignado</th>                        
        </tr> 
    </thead>
    <tbody>
    <?php foreach($articulos->articulo_id as $key => $value):?>         
        <tr class="zebra"> 
            <td><input id="elimina_art" type="button" name="<?php echo $key;?>" value="Eliminar" /></td>
            <td><?php echo $articulos->nombre[$key];?></td>
            <td>
                <input type = 'text' id ='fecha_asignado<?php echo $value; ?>' class="fecha date"/>
                <input type="button"  value="Hoy" id="<?php echo $value; ?>" class="today"  />
            </td>
        </tr>
      <?php endforeach;?>
    </tbody>
</table>

<?php }else{

    echo "No existen servicios guardados";

} ?>

<?php } ?>