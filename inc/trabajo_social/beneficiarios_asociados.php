<?php
session_start();   

    //Librería de conexión
    include($_SESSION['inc_path']."conecta.php");

    //Incluimos librería 'CarBeneficiario' para obtener todo lo relacionado al servicio
    include_once($_SESSION['inc_path'].'libs/CarBeneficiario.php');

    //Variable de mensaje
    $mensaje = "";

    //Verificamos que se nos envíen las variables necesarias
    if($_POST["accion"]){

        $mensaje = "";

        //Recibimos variables
        $accion = $_POST["accion"]; 
        $id_articulo = $_POST['id_beneficiario'];
        $id_beneficiario_pivote = $_POST['id_beneficiario_pivote'];
        $id_trab_expediente = $_POST['id_trab_expediente'];

        //Dependiendo la acción, buscamos la función
        switch($accion){

            case 'agregar':
                        $mensaje = agregarArticulo($id_articulo,
                                                   $id_beneficiario_pivote,
                                                   $id_trab_expediente);
                        break;

            case 'eliminar':
                        $mensaje = eliminarArticulo($id_articulo);
                        break;

            case 'listado':
                        $mensaje = listadoArticulo($id_articulo);
                        break;

        }

    }else{

        $mensaje = "No se seleccionó ninguna acción";

    }    

    //Agregamos artículos al carrito
    function agregarArticulo($id_articulo = 0,$id_beneficiario_pivote = 0,
        $id_trab_expediente = 0)
    {

    //Preparamos variables

        $Carro = "";
        $mensaje = "";

        if (!$_SESSION['arrayCarro']) {
            $Carro = new CarBeneficiario();
            //echo ("Instancia");
        } else {
            $Carro = unserialize($_SESSION['arrayCarro']);
            //echo ("Deserializar");
        }

        //Si recibimos un artículo
        if($id_articulo){

            //Agregamos 1 artículo
            $mensaje = $Carro->agregar($id_articulo,$id_beneficiario_pivote,$id_trab_expediente);                

            //Si obtenemos mensaje de error en el carrito, lo mostramos  
            if($mensaje){
                return $mensaje;
            }else{
                //No hubo error, serializamos el objeto y mostramos mensaje de agregado
                $_SESSION['arrayCarro'] = serialize($Carro);
                //$mensaje = 'Servicio agregado';   
            }

        }else{

            $mensaje = "No se agreg&oacute; al beneficiario, seleccione uno";
        }                   

        return $mensaje;              

    }

    //Eliminamos artículos del carrito    
    function eliminarArticulo($posicion){

        //Quitamos de cada arreglo el valor que corresponde con el $id, quitando 1 producto en total
        if ($Carro = unserialize($_SESSION['arrayCarro'])) {

            $Carro->dilete($posicion);

            /*Si todavía tenemos un artículo, serializamos el objeto, 
            caso contrario, eliminamos la variable de sesión*/            
            if (count($Carro->articulo_id)) {
               $_SESSION['arrayCarro'] = serialize($Carro);
            } else{
                unset($_SESSION['arrayCarro']);
            }                

            $mensaje = "Beneficiario dado de baja";

        } else {

            $mensaje = "Error con el arreglo";

        }

        return $mensaje;

    }

?>


<?php if($mensaje){ ?>
<div class="mensaje">
    <?php echo $mensaje; ?>
</div>
<?php } ?>

<?php
    
    if($_SESSION['arrayCarro']){

        //Obtenemos el carrito
        $articulos = unserialize($_SESSION['arrayCarro']);

        /*Si el objeto tiene elementos, mostramos la tabla, caso contrario
        notificamos que no hay artículos por mostrar*/
        if(isset($articulos)&& isset($articulos->articulo_id)){
    ?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<table class="tablesorter">             
    <thead> 
        <tr>
            <th style="width: 50px;">&nbsp;</th>
            <th>Beneficiario</th>
        </tr> 
    </thead>
    <tbody>
    <?php foreach($articulos->articulo_id as $key => $value):?>         
        <tr class="zebra"> 
            <td><input id="elimina_art" type="button" name="<?php echo $key;?>" value="Eliminar" /></td>
            <td><?php echo $articulos->nombre[$key];?></td>            
        </tr>
      <?php endforeach;?>
    </tbody>
</table>

<?php }else{

    echo "No existen beneficiarios asignados guardados";

} ?>

<?php } ?>