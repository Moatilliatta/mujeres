<?php session_start();     

    //Siempre obtendremos los perfiles activos

    $db->where('activo',1);

    $perfil = $db->get('perfil');

        

    $estatus = $db->get('estatus');

    //Si editamos el registro

    

    if(intval($id_edicion)>0){

        //Obtenemos el registro del módulo

        $db->where('id',$id_edicion);

        $modulo = $db->getOne('modulo');                

    }

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>



	<form id='formMod' method="post" action='save_modulo.php'>

	<table>

    <tr>

        <td>

            <label for="nombre">Modulos</label> 

        </td>

        <td>

            <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />

            <input type = 'text' id = 'nombre' name = 'nombre' class="recurso" value="<?php echo $modulo['nombre']; ?>" />

        </td>

    </tr>

    <tr>

      <td>

        <label for="orden">Orden</label>

      </td>

      <td>

        <input type = 'text' class="digits" id = 'orden' name = 'orden' class="nomnum"value="<?php echo $modulo['orden']; ?>" />

      </td>

    </tr>

    

    <tr>

          <td>

             <label for="descripcion">Descripci&oacute;n </label>

          </td> 

          <td>

                 <textarea id='descripcion' name = 'descripcion'  class="nomnum" cols="50" rows="5" ><?php echo $modulo['descripcion']; ?></textarea>

          </td>

      </tr>



    

    <tr>

     <?php if($id_edicion > 0) { ?>

        <td>

           <label for="activo">Estatus</label> 

        </td>

        <td>

            <select id="activo" name="activo">

                <option value="">Seleccione</option>

                <?php foreach($estatus as $e): 



                        if($e['valor'] === $modulo['activo']){

                            $selected = "selected";

                        }else{

                            $selected = "";

                        }

                    ?>                

                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>

                    <?php endforeach; ?>

            </select>

        </td>

           <?php } ?>

    </tr>

    

    <tr>

        <td>&nbsp;</td>

        <td><input type = 'submit'  id = 'enviar' value = 'Enviar' /></td>

    </tr>

	</table>

	</form>