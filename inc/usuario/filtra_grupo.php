<?php
session_start();

include ($_SESSION['inc_path'].'conecta.php'); 

//Obtenemos el módulo
$id_usuario=$_POST["id_usuario"];

  //Siempre obtendremos los módulos del id_submodulo ligado
    $db->where('activo',1);
    $db->where('id_modulo',$id_modulo);
    $submodulos = $db->get('submodulo');

$params = array($id_usuario);
$sql='select
 g.id,
 g.nombre 
 from usuario_grupo ug
 inner join grupo g on g.id=ug.id_grupo
 where ug.id_usuario=?';

 $u_grupo= $db->rawQuery($sql, $params);
 //var_dump($u_grupo);
 ?>

<select id="id_grupo" name="id_grupo">
    <option value='0'>Seleccione grupo</option>

    <?php foreach($u_grupo as $u): 

        if($u['id'] == $bloq['id_grupo']){
            $selected = "selected";
        }else{
            $selected = "";
        }?>    
    <option value='<?php echo $u['id'] ?>' <?php echo $selected;?> > <?php echo $u['nombre'];?></option>

    <?php endforeach; ?>
</select>