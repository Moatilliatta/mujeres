<?php
session_start(); 

//Cargamos el modelo de grupo
include_once($_SESSION['model_path'].'grupo.php');

    //Cargamos listado de estatus
    $estatus = $db->get('estatus');

    //Inicializamos arreglos
    $grupo_mun_area = NULL;
    //$grupo_ciclo_escolar = NULL;
    //$grupo_axo = NULL;

    //Si editamos el registro
    if(intval($id_edicion)>0){

        //buscamos el registro del usuario
        $db->where('id',$id_edicion);
        $grupo = $db->getOne('grupo');  

        //Obtenemos municipios/áreas ligadas
        //$grupo_mun_area = Grupo::munAreaGrupo($id_edicion);

        //Obtenemos ciclos escolares
        //$grupo_ciclo_escolar = Grupo::cicloEscolarGrupo($id_edicion);

        //Obtenemos Años
        //$grupo_axo = Grupo::axoGrupo($id_edicion);
    }

    //Obtenemos listado de municipios para asignar
    $municipios = Grupo::lista_municipios();

    //Obtenemos listado de áreas para asignar
    $area = Grupo::lista_area();

    //Obtenemos los ciclos escolares disponibles
    $ciclos_escolares = Permiso::ciclosEscolares();

    //Obtenemos años del padrón
    $axo_padron = Permiso::axosPadron();
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>

	<form id='formGpo' method="post" action='save_grupo.php'>
	<table>
        <tr>
			<td>
                <label for="nombre">Grupo</label>
            </td>
			<td>
                <input type="hidden" id="id_edicion_grupo" name="id_edicion" value="<?php echo $id_edicion; ?>" />
                <input type="hidden" name="id_grupo" value="<?php echo $grupo['id']; ?>" />
                <input type = 'text' id = 'nombre' name = 'nombre' class="descripcion" value="<?php echo $grupo['nombre'];?>" />
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
                        if($e['valor'] === $grupo['activo']){
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
      <!--   
        <tr>
            <td>
                <label for="ciclo_escolar">Ciclo Escolar</label>
            </td>
            <td>                
                <?php //foreach ($ciclos_escolares as $key => $value): ?>
                <div>                    
                <input id="<?php //echo $value?>" 
                <?php //if($grupo_ciclo_escolar!= NULL){

                    //echo (in_array($value,$grupo_ciclo_escolar) === true)? 'checked': '';

                    //}else{



                      //  }  ?> 
                value="<?php //echo $value?>" class="" name="ciclo_escolar[]" type="checkbox">

                <?php //echo $value?>
                </div>                
            <?php //endforeach;?>
            </td>
        </tr>
         -->
         <!--      
        <tr>
            <td>
                <label for="axo_padron">A&ntilde;o</label>
            </td>
            <td>                
                <?php //foreach ($axo_padron as $key => $value): ?>
                <div>                    
                <input id="<?php //echo $value?>" <?php // if($grupo_axo != NULL) echo (in_array($value,$grupo_axo) === true)? 'checked': ''; ?> value="<?php //echo $value?>" class="" name="axo_padron[]" type="checkbox">
                <?php //echo $value?>
                </div>                
            <?php //endforeach;?>
            </td>
        </tr>    
        <tr>
        -->
        <!--  
            <td colspan="2">
                <label for="nombre">Municipio que atender&aacute;</label>
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <div>                    
                <input id="todos_mun" value="0" name="id_municipio_area[]" type="checkbox">
                Marcar todos los municipios
                </div>                

            <?php //foreach ($municipios as $key => $value): ?>
                <div>                    
                <input id="<?php //echo $value['id']?>" <?php //if($grupo_mun_area != NULL) echo (in_array($value['id'],$grupo_mun_area) === true)? 'checked': ''; ?> value="<?php // echo $value['id']?>" class="municipio" name="id_municipio_area[]" type="checkbox">
                <?php //echo $value['nombre']?>
                </div>                
            <?php //endforeach;?>
                
            </td>
        </tr>
        -->
        <!--  
        <tr>
            <td colspan="2">
                <label for="nombre">&Aacute;rea que atender&aacute;</label>
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <div>                    
                <input id="todos_area" value="0" name="id_municipio_area[]" type="checkbox">
                Marcar todas las &aacute;reas 
                </div>                

            <?php// foreach ($area as $key => $value): ?>
                <div>                    
                <input id="<?php // echo $value['id']?>" <?php //if($grupo_mun_area != NULL) echo (in_array($value['id'],$grupo_mun_area) === true)? 'checked': ''; ?> class='area' value="<?php ///echo $value['id']?>" class="area" name="id_municipio_area[]" type="checkbox">
                <?php //echo $value['nombre']?>
                </div>                
            <?php //endforeach;?>
                
            </td>
        </tr>
        -->
      	<tr>
			<td>&nbsp;</td>
			<td><input type = 'submit'  id = 'enviar'value = 'Enviar' /></td>
		</tr>
	</table>
	</form>