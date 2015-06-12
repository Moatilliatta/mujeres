<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['ID_C_DEPENDENCIA'])){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Incluimos modelo de producto_servicio
    include_once($_SESSION['model_path'].'programa.php');

    /*Obtenemos los productos y servicios disponibles para el beneficiario,
    (Teniendo en cuenta que no pueden registrarse productos y servicios duplicados)*/
    $id_beneficiario = $_POST["id_beneficiario"];    
    $ID_C_DEPENDENCIA = $_POST["ID_C_DEPENDENCIA"];

    $programa = Programa::listaPrograma($id_beneficiario,$ID_C_DEPENDENCIA);
        
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/combobox.js"></script>
<select id="ID_C_PROGRAMA" class="combobox" name="ID_C_PROGRAMA">
    <option value=''>Seleccione Programa</option>
        <?php foreach($programa as $p):?>
            <option value='<?php echo $p['ID_C_PROGRAMA'] ?>'> 
                <?php echo $p['programa'];?>
            </option>
        <?php endforeach; ?>
</select>        