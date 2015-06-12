<?php
session_start();//Habilitamos uso de variables de sesión

//Obtenemos conexión
include ($_SESSION['inc_path'] . "conecta.php");

//Obtenemos submódulo
$id_submodulo=$_POST["id_submodulo"];

//Obtenemos grupo
$id_grupo = $_POST['id_grupo'];

   $sql = '
            SELECT id, nombre  
            FROM `accion`  
            WHERE activo = 1
            AND id_submodulo = ? ';

    //Filtramos las acciones disponibles
    $params = array($id_submodulo);

    //Si recibimos el id_grupo, filtramos de la tabla accion_grupo
    if(intval($id_grupo) > 0){
        
    $sql .= ' and id not in (SELECT id_accion FROM `accion_grupo` where id_grupo = ? and activo = 1) ';
    $params[] = $id_grupo;
    }

    //Siempre obtendremos los módulos del id_submodulo ligado
    
     //Ejecutamos 
     $accion = $db->rawQuery($sql, $params);        
?>

<select id="id_accion" name="id_accion">
    <option value='0'>Seleccione Acci&oacute;n</option>    
    <?php foreach($accion as $a): 

            if($a['id'] == $accion['id_accion']){

                $selected = "selected";

            }else{

                $selected = "";

            }?>  

    <option value='<?php echo $a['id'] ?>' <?php echo $selected;?> > 
        <?php echo $a['nombre'];?></option>

        <?php endforeach; ?>

</select>