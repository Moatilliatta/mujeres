<?php
session_start();//Habilitamos uso de variables de sesión

if(isset($_POST['id_grado'])){

    //Obtenemos conexión
    include ($_SESSION['inc_path'] . "conecta.php");
    
  
    $id_grado = $_POST["id_grado"];
    
    $sql = '
    SELECT  
        DISTINCT(cd.ID_C_DEPENDENCIA),
        cd.NOMBRE as dependencia,
        g.grado
        FROM c_servicio cs
        INNER JOIN  grado g on g.id = cs.ID_GRADO
        LEFT JOIN c_programa cp on cp.ID_C_PROGRAMA = cs.ID_C_PROGRAMA
        LEFT JOIN  c_dependencia cd on cd.ID_C_DEPENDENCIA = cp.ID_C_DEPENDENCIA  
        WHERE g.id = ? 
    ';

     $params = array($id_grado);    
     $dependencia = $db->rawQuery($sql,$params);
        
}else{
    exit;
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/combobox.js"></script>
<select id="ID_C_DEPENDENCIA" class="combobox" name="ID_C_DEPENDENCIA">
    <option value=''>Seleccione Dependencia</option>
        <?php foreach($dependencia as $d):?>
            <option value='<?php echo $d['ID_C_DEPENDENCIA'] ?>'> 
                <?php echo $d['dependencia'];?>
            </option>
        <?php endforeach; ?>
</select>    