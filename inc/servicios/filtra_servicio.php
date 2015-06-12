<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['ID_C_DEPENDENCIA'])){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Incluimos modelo de producto_servicio
    include_once($_SESSION['model_path'].'servicio.php');

    /*Obtenemos los servicios disponibles de la beneficiaria,
    (Teniendo en cuenta que no pueden registrarse servicios duplicados)*/
    $id_mujeres_avanzando = $_POST["id_mujeres_avanzando"];    
    $ID_C_DEPENDENCIA = $_POST["ID_C_DEPENDENCIA"];
    
    $servicio = Servicio::listado($id_mujeres_avanzando,null,$ID_C_DEPENDENCIA);
    
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/combobox.js"></script>
<select id="ID_C_SERVICIO" class="combobox" name="ID_C_SERVICIO">
    <option value=''>Seleccione Servicio</option>
        <?php foreach($servicio as $p):?>
            <option value='<?php echo $p['ID_C_SERVICIO'] ?>'> 
                <?php echo $p['servicio'];?>
            </option>
        <?php endforeach; ?>
</select>    