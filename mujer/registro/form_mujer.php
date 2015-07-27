<?php
session_start();

include_once($_SESSION['model_path'].'familiares_mujer.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'usuario_grupo.php');
include_once($_SESSION['model_path'].'grupo.php');

//Obtenemos el id del usuario creador
$id_usuario = $_SESSION['usr_id'];

//Obtenemos ID del grupo admin
$id_admin = Grupo::getId('admin');

//Permisos de Admin
$grupos_usuario=UsuarioGrupo::gruposUsr($id_usuario);

//print_r($grupos_usuario);
//exit;

//Arreglos para los select
$municipio_nacimiento = array();
$municipios_residencia = array();
$estatus = $db->get('estatus');

//Obtemos los estados
$sql = 'SELECT CVE_ENT, NOM_ENT from cat_estado';
$estado = $db->query($sql);
array_pop($estado);

//Obtemos los estados de nacimiento
$sql = 'SELECT CVE_ENT, NOM_ENT from cat_estado';     
$estado_nacimiento = $db->query($sql);

//Obtenemos municipios (predeterminado los de jalisco)
$sql='SELECT CVE_MUN, NOM_MUN FROM cat_municipio WHERE CVE_ENT = 14';
$municipio_nacimiento = $db->query($sql);

//Municipios de residencia tendrán los de Jalisco
$municipios_residencia = $municipio_nacimiento;

//Obtenemos arreglos de los demás select
$ocupacion = $db->get('ocupacion');
$estado_civil = $db->get('estado_civil');
$nivel_socieconomico = $db->get('nivel_socioeconomico');
$calidad_dieta = $db->get('calidad_dieta');
$diversidad = $db->get('diversidad');
$variedad = $db->get('variedad'); 
$elcsa = $db->get('elcsa'); 
$modulo = $db->get('c_modulo');
$grado = $db->get('grado');
$sql = 'SELECT id, nombre, clave from pais ORDER BY nombre ASC';
$pais = $db->query($sql);

//Inicializamos variable
$disabled = NULL;

//Obtenemos acciones del menú
$central = Permiso::arregloMenu('lista_mujer','center');

//si precargaamlos datos de familiares de titular de cartilla
if($id_familiar > 0){
            
  $mujeres_avanzando = FamiliaresMujer::datos_cartilla($id_familiar); 
  //print_r($mujeres_avanzando);
  //exit;    
}

//Si editamos el registro
if(intval($id_edicion)>0 || intval($id_aspirante)>0){

    $disabled = ' disabled = "disabled" ';

    if($id_edicion>0){
        
        //Obtenemos el registro del usuario
        $mujeres_avanzando = mujeresAvanzando::get_by_id($id_edicion);
        
         //print_r($mujeres_avanzando);
         //exit;                 
    }  
    
    $CVE_EDO_RES = $mujeres_avanzando['CVE_EDO_RES'];
    $id_cat_municipio = $mujeres_avanzando['id_cat_municipio'];
    $id_cat_estado = $mujeres_avanzando['id_cat_estado'];
    $CODIGO = $mujeres_avanzando['CODIGO'];
    
        //$municipios_residencia
        if($CVE_EDO_RES && $CVE_EDO_RES != 14){
            
            $sql = 'SELECT CVE_MUN, NOM_MUN FROM `cat_municipio` where CVE_ENT = ?';
            $params = array($CVE_EDO_RES);
            $municipios_residencia = $db->rawQuery($sql,$params);
        }

        //$municipios_nacimiento
        if($id_cat_estado && $id_cat_estado != 14){
            $sql = 'SELECT CVE_MUN, NOM_MUN FROM `cat_municipio` where CVE_ENT = ?';
            $params = array($id_cat_estado);
            $municipio_nacimiento = $db->rawQuery($sql,$params);
        }

}        	    

    
    //ruta raiz
    $ruta_raiz = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS']; 
    
    //ruta de imagen
    $ruta = $_SESSION['img_path']."mujeres/";   

    if(isset($mujeres_avanzando['folio_compuesto'])){
      $folio = $mujeres_avanzando['folio_compuesto'];
    }

    if($id_familiar != NULL && $mujeres_avanzando != NULL){
      $num_folio = mujeresAvanzando::getNumFolio($folio);
      $folio .= '-'.$num_folio;
    }       
    
    //Verificamos si la imagen existe, de no estarlo ponemos imagen default
    $ruta_imagen = (file_exists($ruta_raiz.$folio.".png"))? $ruta.$folio.".png" : $ruta."default.png";              

    //Verificamos si podemos modificar ciertos campos
    $readonly = (in_array($id_admin,$grupos_usuario))? NULL : "readonly";
?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>jquery.maskedinput.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>
<style type="text/css">
  .foto_cred {
    display: block;
    height: 2.75cm;
    left: 0.74cm;
    top: 1.6cm;
    width: 2.37cm;
}
</style>
<div class="mensaje">
    <label class="obligatorio"> Le recordamos que los campos con asterisco (*) son campos obligatorios<?php //echo $num_folio; ?></label>
</div>
<?php if ($mujeres_avanzando['folio'] !=null && $mujeres_avanzando['num_folio'] !=null) { ?>
<div id="foto" style="display: none;">
<fieldset>
  <legend>
    <label>Fotograf&iacute;a</label> 
  </legend>
  <table>
    <tr>
    <td>
       <div class="foto_cred" 
            style="background: url(<?php echo $ruta_imagen; ?>) center center ; background-size: auto 100%;">
        </div>
    </td>
      <td>
        <?php if(array_key_exists('subir_foto',$central)){ ?> 
        <form name="guarda_imagen" id="guarda_imagen" action="" method="post" enctype="multipart/form-data">
          <!--  <input type="hidden" id='folio' name="folio" value="<?php echo $folio; ?>" />  -->
          <input type="file" id="img<?php echo $folio; ?>" name="img<?php echo $folio; ?>"  />
          <input type="submit" name="button" id="button" value="Subir Imagen" />
        </form>       
        <?php } ?> 
      </td>
    </tr>
  </table>  
</fieldset>
</div>
<?php } ?>
<form id='formBen' method="post" action='<?php echo $_SESSION["app_path_p"]; ?>mujer/registro/save_mujer.php'> 
<fieldset>
      <table>
        <legend>
            <label>
              Datos Caravana
            </label>
        </legend> 

      <tr>
   <td>
   
      <label class="obligatorio">*</label><label for="grado">Grado</label>
          
   </td>
   <td>
   
      <label class="obligatorio">*</label><label for="Folio">Folio</label>
          
   </td>
   <td>
    <?php if($mujeres_avanzando['num_folio'] > 0  ) {  ?> 
     <label class="obligatorio">*</label><label for="num_folio">Consecutivo</label> 
    <?php } ?> 
   </td>
 </tr>
 <tr>
   <td>
     <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?> 
       <select id="grado" name="id_grado">
                <option value=''>Seleccione el Grado</option>
                <?php foreach($grado as $g): 
                $selected = ($g['id'] == $mujeres_avanzando['id_grado'])? 'selected': '';?>
                <option value='<?php echo $g['id'] ?>' <?php echo $selected;?> > 
                <?php echo $g['grado'];?>
                </option>
                <?php endforeach; ?>
        </select>  
        <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
           <input type="hidden" name="id_grado" value="<?php echo $mujeres_avanzando['id_grado']; ?>" />
           <label><?php echo $mujeres_avanzando['grado'] ?></label>
     <?php } ?>    
   </td>   
   <td>
    <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?>
     <input type = 'text' id = 'folio' name = 'folio' <?php echo $readonly; ?> 
     value="<?php echo $mujeres_avanzando['folio']; ?>" />
     <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?> 
     <input type="hidden" id="folio" name="folio" value="<?php echo $mujeres_avanzando['folio']; ?>" /> 
     <label><?php echo $mujeres_avanzando['folio_compuesto'];?> </label> 
    <!-- echo $mujeres_avanzando['folio']--> 
    <?php } ?>
   </td>
   <td>
     <?php if(in_array($id_admin,$grupos_usuario) == true && $mujeres_avanzando['num_folio'] > 0 ) {  ?>
      <input type = 'text' id = 'num_folio' name = 'num_folio' class="" value="<?php echo $mujeres_avanzando['num_folio']; ?>" />
     <?php } ?> 
   </td>
 </tr>
 </table>     
</fieldset>
<table>
   <div id="mujeres_duplicados"></div>
</table>

<fieldset>
<table>
 <legend>
   <label>Datos de Identificaci&oacute;n</label>  
 </legend>
 <tr>
    <td>
        <label class="obligatorio">*</label> <label for="nombres">Nombre(s)</label>
    </td>
    <td>
        <label class="obligatorio">*</label><label for="paterno">Apellido Paterno</label>
    </td>
    <td>
        <label for="materno">Apellido Materno</label>
    </td>
</tr>
<tr>    
    <td>
          <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />
          <input type="hidden" name="id_mujeres_avanzando" value="<?php echo $mujeres_avanzando['id']; ?>" />
          <input type="hidden" name="id_caravana" value="<?php echo $mujeres_avanzando['id_caravana']; ?>" />
          <input type="hidden" name="id_entrevista" value="<?php echo $mujeres_avanzando['id_entrevista']; ?>" />
          <input type="hidden" name="id_familiar" value="<?php echo $id_familiar; ?>" />
          <input type="hidden" name="desc_ubicacion" value="<?php echo $mujeres_avanzando['desc_ubicacion']; ?>" />
          <input type="hidden" name="programa" value="<?php echo $mujeres_avanzando['programa']; ?>" />
          <input type="hidden" name="visita" value="<?php echo $mujeres_avanzando['visita']; ?>" />
        <input type = 'text' id = 'nombres' name = 'nombres' class="nombre_ arma_curp_ cambia_mujer" value="<?php echo $mujeres_avanzando['nombres']; ?>" />
    </td>
    <td>
        <input type = 'text' id = 'paterno' name = 'paterno' class="nombre_ arma_curp_ cambia_mujer" value="<?php echo $mujeres_avanzando['paterno']; ?>" />
    </td>
        <td>
            <input type = 'text' id = 'materno' name = 'materno' class="nombre_ arma_curp_ cambia_mujer" value="<?php echo $mujeres_avanzando['materno']; ?>" />
        </td>
    </tr>
     <tr>
    <td>
       <label class="obligatorio">*</label>
       <label for="calle">Calle</label>
    </td>
     <td>
            <label class="obligatorio">*</label>
            <label for="num_ext">N&uacute;mero exterior</label>
        </td>
        <td>
            <label for="num_int">N&uacute;mero interior</label>
        </td>
    
  </tr>
  <tr>
    <td>
      <input type = 'text' id = 'calle' name = 'calle'  value="<?php echo $mujeres_avanzando['calle']; ?>" /> 
    </td>
    <td>
            <input type = 'text' id = 'num_ext' name = 'num_ext' class="nomnum_2" value="<?php echo $mujeres_avanzando['num_ext']; ?>" />
        </td>
        <td>
            <input type = 'text' id = 'num_int' name = 'num_int' class="nomnum_2" value="<?php echo $mujeres_avanzando['num_int']; ?>" />
        </td>
  </tr>
  <tr>
    <td>
     <label class="obligatorio">*</label>
     <label form="colonia">Colonia</label>
   </td>
  </tr>
  <tr>
    <td>
      <input type = 'text' id = 'colonia' name = 'colonia'  value="<?php echo $mujeres_avanzando['colonia']; ?>" /> 
    </td>
  </tr>
    <tr>
        <td>
            <label class="obligatorio">*</label>
            <label for="CVE_EDO_RES">Estado de Residencia</label>
        </td>

        <td>
            <label class="obligatorio">*</label>
            <label for="id_cat_municipio">Municipio de Residencia</label>
        </td>
    </tr>

    <tr>
        <td>
            <select id="CVE_EDO_RES" name="CVE_EDO_RES">
                <option value=''>Seleccione Estado</option>
                    <?php foreach($estado as $est): 

                       $selected = ($est['CVE_ENT'] == $mujeres_avanzando['CVE_EDO_RES'] )? 'selected' : ''; 
                    ?>
                    <option value='<?php echo $est['CVE_ENT'] ?>' <?php echo $selected;?> > 
                        <?php echo $est['NOM_ENT'];?>
                    </option>
                    <?php endforeach; ?>
            </select>
        </td>
        <td colspan="2" id="municipio">

            <select class="combobox" id="id_cat_municipio" name="id_cat_municipio">
                <option value=''>Seleccione Municipio</option>
                    <?php foreach($municipios_residencia as $mu):
                        
                        $selected = ($mu['CVE_MUN'] == $mujeres_avanzando['id_cat_municipio'])? 'selected' :'';?>                

                    <option value='<?php echo $mu['CVE_MUN'] ?>' <?php echo $selected;?> > 
                        <?php echo $mu['NOM_MUN'];?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    
    <tr>
      <td>
        <label class="obligatorio">*</label>
        <label for="CODIGO">C&oacute;digo Postal</label>
      </td>    
      <td>
        <label class="obligatorio">*</label>
        <label for="estado_civil">Estado Civil</label>
      </td>
      <td>
            <label for="telefono">Tel&eacute;fono</label>
      </td>
    </tr>

    <tr>
    <!--  
        <td id="cp">
            <select class="combobox" id="CODIGO" name="CODIGO">
                <option value=''>Seleccione C&oacute;digo Postal</option>
                <?php //foreach($codigo as $c):
                //$selected = ($c['d_codigo'] == $mujeres_avanzando['CODIGO'])? "selected":''; ?>                
                <option value='<?php //echo $c['d_codigo'] ?>' <?php //echo $selected;?> > <?php //echo $c['d_codigo'];?></option>
                <?php //endforeach; ?>
            </select>
        </td>
        -->
        <td>
          <input type = 'text' id = 'CODIGO' name = 'CODIGO' class="digits" value="<?php echo $mujeres_avanzando['CODIGO']; ?>" /> 
        </td>        
      <td>
        <select id="estado_civil" name="id_estado_civil">
            <option value=''>Seleccione Estado Civil</option>
                <?php foreach($estado_civil as $ec): 
                $selected = ($ec['id'] == $mujeres_avanzando['id_estado_civil'])? 'selected': '';?>
                <option value='<?php echo $ec['id'] ?>' <?php echo $selected;?> > 
                <?php echo $ec['nombre'];?>
                </option>
                <?php endforeach; ?>
            </select>
      </td>
      <td>
          <input type = 'text' id = 'telefono' name = 'telefono' class="" value="<?php echo $mujeres_avanzando['telefono']; ?>" />
      </td>
    </tr>   
  </table>
</fieldset>


    
    <fieldset>
    <table>
      <legend>
        <label>Datos de Nacimiento y Acreditaci&oacute;n de Identidad</label>  
      </legend>
    <tr>
        <td>
            <label class="obligatorio">*</label>
            <label for="fecha_nacimiento">Fecha Nacimiento</label>
        </td>
        
        <td>
            <label class="obligatorio">*</label>
            <label for="genero">Sexo</label>
        </td>
     </tr>

    <tr> 
        <td>
            <input type = 'text' id = 'fecha_nacimiento' class="fecha date arma_curp_" name = 'fecha_nacimiento'value="<?php echo $mujeres_avanzando['fecha_nacimiento']; ?>" />
            <!-- <input type="button"   value="Hoy" id="btnToday" class="today"  />-->
        </td>        
        <td>
            <input type="radio" id="MUJER" name="genero" class="arma_curp_"  value="MUJER"<?php echo( ($mujeres_avanzando['genero'] == 'MUJER')? 'checked': 'checked'); ?>/><label for="MUJER">MUJER</label><br />
            <input type="radio" id="HOMBRE" name="genero" class="arma_curp_" value="HOMBRE"<?php echo( ($mujeres_avanzando['genero'] == 'HOMBRE')? 'checked': null); ?>/><label for="HOMBRE">HOMBRE</label>
        </td>
    <tr>
      <td>
        <label for="id_pais">Pa&iacute;s de Nacimiento</label>
      </td>
      <td>
        <label for="id_cat_estado">Estado de Nacimiento</label>
      </td>
    </tr>
    <tr>
      <td>
        <select class="combobox arma_curp_" id="id_pais" name="id_pais">
            <option value=''>Seleccione Pa&iacute;s</option>
            <?php foreach($pais as $pa): 
            $selected = ($pa['id'] == $mujeres_avanzando['id_pais'])? 'selected': ''; ?>
            <option value='<?php echo $pa['id'] ?>' <?php echo $selected;?> > <?php echo trim($pa['nombre']);?></option>
            <?php endforeach; ?>
        </select>
      </td>

      <td id="estado_origen">
        <select class="combobox arma_curp_" id="id_cat_estado" name="id_cat_estado">
            <option value=''>Seleccione Estado</option>
            <?php foreach($estado_nacimiento as $est): 
            $selected = ($est['CVE_ENT'] == $mujeres_avanzando['id_cat_estado'] )? 'selected': ''; ?>
            <option value='<?php echo $est['CVE_ENT'] ?>' <?php echo $selected;?> > <?php echo $est['NOM_ENT'];?></option>
            <?php endforeach; ?>
            </select>
      </td>
    </tr>
    <tr>
      <td>
        <label for="id_cat_municipio">Municipio de Nacimiento</label>
      </td>      
    </tr>

    <tr>
        <td id="municipio_nacimiento">
        <select class="combobox arma_curp_" id="id_municipio_nacimiento" name="id_municipio_nacimiento">
            <option value=''>Seleccione Municipio Nacimiento</option>
            <?php foreach($municipio_nacimiento as $mu):
            $selected = ($mu['CVE_MUN'] == $mujeres_avanzando['id_municipio_nacimiento'])? 'selected' : '';?>
            <option value='<?php echo $mu['CVE_MUN'] ?>' <?php echo $selected;?> > <?php echo $mu['NOM_MUN'];?></option>
            <?php endforeach; ?>
            </select>
        </td>        
    </tr>     
    </table>
</fieldset>

<fieldset>
    <table>
    <legend>
        <label>Datos Generales</label>
    </legend>

    <tr>
        <td>
            <label class="obligatorio">*</label>
            <label for="ocupacion">Ocupaci&oacute;n</label>
        </td>
        <td>
            <label class="obligatorio">*</label>
            <label for="escolaridad">Escolaridad</label>
        </td>
        <td>
            <?php if(in_array($id_admin,$grupos_usuario) == true){  ?>
            <label class="obligatorio">*</label>
             <?php } ?>
             <label for="elcsa">Elcsa</label>
        </td>  
        <?php if($id_edicion > 0 && array_key_exists('activa_mujer',$central)){ ?>
        <td>
            <label for="activo">Estatus</label>
        </td>
        <?php } ?>        
    </tr>    

    <tr>    
      <td>
        <input type = 'text' id = 'ocupacion' name = 'ocupacion' class="" value="<?php echo $mujeres_avanzando['ocupacion']; ?>" />
      </td>
      <td>
        <input type = 'text' id = 'escolaridad' name = 'escolaridad' class="" value="<?php echo $mujeres_avanzando['escolaridad']; ?>" />
      </td>
      <td>
        <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?>
        <select id="elcsa" name="elcsa">
            <option value=''>Seleccione elcsa</option>
                <?php foreach($elcsa as $esc): 
                $selected = ($esc['id'] == $mujeres_avanzando['elcsa'])? 'selected': '';?>
                <option value='<?php echo $esc['id'] ?>' <?php echo $selected;?> > 
                <?php echo $esc['elcsa'];?>
                </option>
                <?php endforeach; ?>
            </select>  
         <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
       <input type="hidden" id="elcsa" name="elcsa" value="<?php echo $mujeres_avanzando['elcsa']; ?>" />
       <label><?php echo $mujeres_avanzando['desc_elcsa'] ?></label>
         <?php } ?>
          
        
     </td>  

    <?php if($id_edicion > 0 && array_key_exists('activa_mujer',$central)){ ?>
     <td>
        <select id="activo" name="activo">
        <option value="">Seleccione</option>
        <?php foreach($estatus as $e): 
        $selected = ($e['valor'] === $mujeres_avanzando['activo'])? 'selected' : '' ;?>                
        <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > 
        <?php echo $e['nombre'];?>
        </option>
        <?php endforeach; ?>
        </select>
     </td>

    <?php } ?>        
    </tr>    
   <tr>
    <td>
      <?php if(in_array($id_admin,$grupos_usuario) == true){  ?>
       <label class="obligatorio">*</label>
       <?php } ?>
       <label for="nivel">Nivel Socieconomico</label>
    </td>
    <td>
      <?php if(in_array($id_admin,$grupos_usuario) == true){  ?>   
       <label class="obligatorio">*</label>
      <?php } ?> 
      <label for="calidad_dieta">Calidad Dieta</label>
    </td>
    <td>
      <?php if(in_array($id_admin,$grupos_usuario) == true){  ?>
       <label class="obligatorio">*</label>
      <?php } ?>  
      <label for="diversidad">Diversidad</label>
    </td>
    <td>
     <?php if(in_array($id_admin,$grupos_usuario) == true){  ?>
       <label class="obligatorio">*</label>
      <?php } ?> 
      <label for="variedad">Variedad</label>
    </td>  
  </tr>
  <tr>
     <td>
      <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?> 
       <select id="nivel" name="nivel">
                <option value=''>Seleccione Nivel Socieconomico</option>
                <?php foreach($nivel_socieconomico as $nv): 
                $selected = ($nv['id'] == $mujeres_avanzando['nivel'])? 'selected': '';?>
                <option value='<?php echo $nv['id'] ?>' <?php echo $selected;?> > 
                <?php echo $nv['nivel'];?>
                </option>
                <?php endforeach; ?>
        </select>  
        <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
     
     <input type="hidden" id="nivel" name="nivel" value="<?php echo $mujeres_avanzando['nivel']; ?>" />
     <label><?php echo $mujeres_avanzando['nivel_desc'] ?></label>
     <?php } ?> 
     </td>
     <td>
       <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?> 
       <select id="calidad_dieta" name="calidad_dieta">
                <option value=''>Seleccione Calidad De Dieta</option>
                <?php foreach($calidad_dieta as $cd): 
                $selected = ($cd['id'] == $mujeres_avanzando['calidad_dieta'])? 'selected': '';?>
                <option value='<?php echo $cd['id'] ?>' <?php echo $selected;?> > 
                <?php echo $cd['calidad_dieta'];?>
                </option>
                <?php endforeach; ?>
        </select>  
        <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
       <input type="hidden" id="calidad_dieta" name="calidad_dieta" value="<?php echo $mujeres_avanzando['calidad_dieta']; ?>" />
       <label><?php echo $mujeres_avanzando['calidad_desc'] ?></label>
       <?php } ?> 
     </td>
      <td>
        <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?>  
        <select id="diversidad" name="diversidad">
                <option value=''>Seleccione Diversidad</option>
                <?php foreach($diversidad as $dv): 
                $selected = ($dv['id'] == $mujeres_avanzando['diversidad'])? 'selected': '';?>
                <option value='<?php echo $dv['id'] ?>' <?php echo $selected;?> > 
                <?php echo $dv['diversidad'];?>
                </option>
                <?php endforeach; ?>
        </select>   
        <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
        <input type="hidden" id="diversidad" name="diversidad" value="<?php echo $mujeres_avanzando['diversidad']; ?>" />
        <label><?php echo $mujeres_avanzando['diversidad_desc'] ?></label>
        <?php } ?> 
     </td>
     <td>
        <?php if(in_array($id_admin,$grupos_usuario) == true) {  ?>   
        <select id="variedad" name="variedad">
                <option value=''>Seleccione Variedad</option>
                <?php foreach($variedad as $vd): 
                $selected = ($vd['id'] == $mujeres_avanzando['variedad'])? 'selected': '';?>
                <option value='<?php echo $vd['id'] ?>' <?php echo $selected;?> > 
                <?php echo $vd['variedad'];?>
                </option>
                <?php endforeach; ?>
        </select>   
       <?php }elseif($id_edicion > 0 || $readonly != NULL){ ?>
       <input type="hidden" id="variedad" name="variedad" value="<?php echo $mujeres_avanzando['variedad']; ?>" />
        <label><?php echo $mujeres_avanzando['variedad_desc'] ?></label> 
        <?php } ?> 
     </td>
  </tr>
  

  
  <!-- 
  <tr>
    <td>
       <label for="ID_C_MODULO">M&oacute;dulo</label>
    </td>
    <td>
       <label for="PUNTOS_OTORGADOS">Puntos Otorgados</label>  
    </td>
    <td>
       <label for="PUNTOS_LOCALIZADOS">Puntos Localizados</label>  
    </td>
  </tr>
  <tr>
     <td>
       <select id="ID_C_MODULO" name="ID_C_MODULO">
            <option value=''>Seleccione el m&oacute;dulo</option>
            <?php //foreach($modulo as $m):            

                //$selected = ($m['ID_C_MODULO'] == $mujeres_avanzando['ID_C_MODULO'])? "selected" : ""; ?>                
                <option value='<?php //echo $m['ID_C_MODULO'] ?>' <?php //echo $selected;?> > 
                    <?php //echo $m['NOMBRE'];?>
                </option>
            <?php //endforeach; ?>
        </select> 
     </td>
     <td>
        <input type = 'text' id = 'PUNTOS_OTORGADOS' name = 'PUNTOS_OTORGADOS' class="nomnum"value="<?php echo $mujeres_avanzando['PUNTOS_OTORGADOS']; ?>" />
     </td>
     <td>
        <input type = 'text' id = 'PUNTOS_UTILIZADOS' name = 'PUNTOS_UTILIZADOS' class="nomnum"value="<?php echo $mujeres_avanzando['PUNTOS_UTILIZADOS']; ?>" />
     </td>
  </tr>
  -->    

    <tr>
        <td id="homonimo" colspan="4" >&nbsp;</td>
    </tr>        

  <!-- 
   <tr>
        <td>
            <label for="CVE_ASEN">Asentamiento</label>
        </td>
        <td id="asentamiento">
            <select class="combobox" id="CVE_ASEN" name="CVE_ASEN">
                <option value=''>Seleccione Asentamiento</option>
                <?php /* foreach($asentamiento as $a): 

                   $selected = ($a['CVE_ASEN'] == $mujeres_avanzando['CVE_ASEN']) 'selected': ''; ?>                
                    <option value='<?php echo $a['CVE_ASEN'] ?>' <?php echo $selected;?> > 
                        <?php echo $a['nombre_asentamiento'];?>
                    </option>

                    <?php endforeach;*/ ?>

            </select>
        </td>
   </tr>
    -->

    </table>
    </fieldset>
    
    
    
    <tr>
        <td>&nbsp;</td>
        <td colspan="4">
            <input type="submit" value="Guardar" id="guardar" />
        </td>
    </tr>   
</form>
