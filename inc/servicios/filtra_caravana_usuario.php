<?php
session_start();//Habilitamos uso de variables de sesión

$caravana = NULL;

if(isset($_POST['nombre_usuario'])){

    //Obtenemos conexión
    include ("../../inc/conecta.php");    
    
    //Nombre de usuario
    $nombre_usuario = $_POST["nombre_usuario"];
    
    //Incluimos modelo de producto_servicio
    include_once('../../model/usuario_caravana.php');
    
    //Obtenemos caravanas
    $caravanas = UsuarioCaravana::listaCaravanaUsr($nombre_usuario);
    
}else{
    exit;
}
?>
<?php if($caravanas != NULL){?>
<table>
    <tr>
        <td><label>Caravana</label></td>
    </tr>
    <tr>
        <td>
        <select id="id_caravana" name="id_caravana">
        <option value=''>Seleccione Caravana</option>
            <?php foreach($caravanas as $c):?>
                <option value='<?php echo $c['id'] ?>'> 
                    <?php echo $c['descripcion'];?>
                </option>
            <?php endforeach; ?>
        </select>    
        </td>
    </tr>
    <tr>
        <td>Escoja Caravana</td>
        <td>&nbsp;</td>
    </tr>
</table>
<?php } ?>