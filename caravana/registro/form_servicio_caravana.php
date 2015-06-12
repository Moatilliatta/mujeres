<?php
session_start();
include_once($_SESSION['model_path'].'servicio.php');
include_once($_SESSION['model_path'].'caravana.php');
$listaStock = null;
//traemos los status
$estatus = $db->get('estatus');
//obtenemos servicios
$servicio = Servicio::listado();
//print_r($servicio);
//Si editamos el registro
if(intval($id_edicion)>0){
       
        //Obtenemos el registro del caravana
        $db->where('id',$id_edicion);
        $caravana = $db->getOne('caravana');
        
        $id_caravana = Caravana::listaCaravana();
        
        
        $sql = 'SELECT  
                servc.id,
                servc.stock,
                servc.id_caravana,
                servc.ID_C_SERVICIO
                FROM `servicio_caravana` servc
                where servc.id_caravana = ? ';
        $params = array($id_edicion);
        $stock = $db->rawQuery($sql,$params);
        
        foreach($stock as $key => $value):
            
            $listaStock[$value['ID_C_SERVICIO']] = $value['stock'];
            
        endforeach;
        
                
        }
        
       
        
//print_r($stock).'<br>';        
//print_R($listaStock);


?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

	<form id='formServCaravana' method="post" action='save_servicio_caravana.php'>
        
        <?php if ($id_edicion > 0) { ?>
        <fieldset>
           <legend>
                <label>
                   STOCK DE SERVICIOS DE ESTA CARAVANA
                </label>
            </legend>
          <table>
            <tr>
              <td>
                <label for="servicios">SERVICIOS</label>
                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
              </td>
            </tr>
            
           <?php foreach($servicio as $key => $s): ?> 
            <tr>
               <td><label><?php echo $s['servicio'];?></label></td>
              <?php if($s['ES_CONTABLE'] == 'SI') { 
                ?>    
               <td>
                 <input type="hidden" name="id_servicio[]" value="<?php echo $s['ID_C_SERVICIO']; ?>" /> 
                 <input type = 'text' name = 'stock[]' value="<?php echo ($listaStock[$s['ID_C_SERVICIO']] != NULL )?$listaStock[$s['ID_C_SERVICIO']]:''?>" /><label for="STOCK">Stock</label>
               </td>
              <?php  } ?> 
            </tr>
               
             <?php endforeach ?> 
            
          </table>
        </fieldset>
         
         <tr>
        <td>
            <input type="submit" value="Guardar"  />
        </td>
    </tr>  
     <?php } ?> 
	</form>