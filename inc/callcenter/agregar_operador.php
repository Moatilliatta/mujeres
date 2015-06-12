<?php
session_start();  

    if(!isset($db)){
        //Librería de conexión
        include($_SESSION['inc_path']."conecta.php");
    }    

    //Incluimos librería 'CarOperador' para obtener todo lo relacionado a
    //la asignación de operadores al grupo
    include_once($_SESSION['inc_path'].'libs/CarOperador.php');
    
    //Variable de mensaje
    $msg = "";
    $mensaje = "";
    
    
    //Verificamos que se nos envíen las variables necesarias
    if($_POST["accion"]){

        //Recibimos variables
        $accion = $_POST["accion"]; 
        $id = $_POST["id"];
        $id_callcenter_grupo = $_POST["id_callcenter_grupo"];        

        //Dependiendo la acción, buscamos la función
        switch($accion){

            case 'agregar':
                        $msg = agregarArticulo($id,$id_callcenter_grupo);
                        break;
            case 'eliminar':
                        $msg = eliminarArticulo($id);
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
    function agregarArticulo($id_articulo = 0,$id_callcenter_grupo = 0){

    //Preparamos variables

        $A = "";

        if (!$_SESSION['arrayArt']){
            $A = new CarOperador();
            //echo ("Instancia");
        } else {
            $A = unserialize($_SESSION['arrayArt']);
            //echo ("Deserializar");
        }

        //Si recibimos un artículo
        if($id_articulo){

            //Agregamos 1 artículo
            $mensaje = $A->agregar($id_articulo,$id_callcenter_grupo);                

            //Si obtenemos mensaje de error en el carrito, lo mostramos  
            if($mensaje){
                return $mensaje;
            }else{
                //No hubo error, serializamos el objeto y mostramos mensaje de agregado
                $_SESSION['arrayArt'] = serialize($A);
                $mensaje = 'Registro agregado';   
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


<?php if($mensaje){ ?>
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
<?php } ?>

<?php if($_SESSION['arrayArt']){

    //Obtenemos el carrito
    $articulos = unserialize($_SESSION['arrayArt']);

    /*Si el objeto tiene elementos, mostramos la tabla, caso contrario
    notificamos que no hay artículos por mostrar*/
    if(isset($articulos)&& isset($articulos->articulo_id)){
        ?>
     
    <h2 class="centro">Selecci&oacute;n de Operadores</h2>         

    <table class="tablesorter">             
        <thead> 
            <tr>
                <th>Nombre</th>
                <th>Acci&oacute;n</th> 
            </tr> 
        </thead>
        <tbody>
        <?php foreach($articulos->articulo_id as $key => $value):?>
            <tr class="zebra"> 
                <td><?php echo $articulos->nombre[$key];?></td>
                <td>
                  <input id="elimina_art" type="button" name="<?php  echo $key;?>" value="Quitar Del Listado" />
                </td>      
            </tr>
          <?php endforeach;?>
        </tbody>
    </table>
    
    <h2 class="centro" style="clear:both">
        <button id="borra_lista" style=" position: relative;right: 40%;">Vaciar Listado</button>
        <button id="vista" style=" position: relative; left: 40%;">Guardar</button>   
    </h2>
    

    <?php }else{ ?>

        <div class="mensaje info_msg">No existen servicios guardados</div> 

    <?php } ?>

<?php } ?>