<?php
//Librería de conexión
include("conecta.php");
//Librería de permisos
include('libs/Permiso.php'); 

//Si no está creada la variable de sesión, la creamos
//if(!isset($_SESSION['LAST_ACTIVITY'])){
  //$_SESSION['LAST_ACTIVITY'] = time(); 
//}

//Obtenemos tiempo logueado
$tiempo = time() - intval($_SESSION['LAST_ACTIVITY']); 

/*Si no se está logueado se redirecciona a Inicio de Sesión
900 segundos equivalen a 15 minutos
*/
$LIMITE = 3600;

if(!isset($_SESSION["login"]) || (isset($_SESSION['LAST_ACTIVITY']) && $tiempo > $LIMITE) ){
    
    //eliminamos variable de sesión last_activity
    unset($_SESSION['LAST_ACTIVITY']);
    
   
    //Obtenemos arreglo con cada uno de los niveles del directorio
    $app_path_p = Permiso::getRoute();

    //Redireccionamos con php
    header('Location:'.$app_path_p.'login/index.php?r=13');
    
    //echo '<script language="JavaScript">location.href="/'.$app.'/login/index.php?r=4"</script>';
    //echo  'No logueado';
    //exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); 


//Actualizamos la hoja de estilo del módulo
Permiso::updateModule();

if (basename($_SERVER['PHP_SELF']) != 'menu.php'&&
basename($_SERVER['PHP_SELF']) !=  'index.php'&&
basename($_SERVER['PHP_SELF']) !=  'clave_usuario.php'){
        
    //Validamos que el recurso sea válido     
    $title = Permiso::validaRuta();
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DIF - Sistema de Mujeres Avanzando</title>

<?php
//echo time().'<br>'; 
//echo $_SESSION['LAST_ACTIVITY'].'<br>';
//echo $tiempo;
?>
<link href="../../css/benefits.css" rel="stylesheet" type="text/css" />
 
    <meta charset="UTF-8"/>
    <title><?php echo $title?></title>
	  <link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'modulo/'.$_SESSION['module_name'].'.css' ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'paginador.css' ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'validador.css' ?>" type="text/css"/> 
    <link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'tablesorter/style.css' ?>" type="text/css"/>
    <!-- jqueryui -->
    <link rel="stylesheet" href="<?php echo $_SESSION['js_path']?>css/custom-theme/jquery-ui-1.10.3.custom.css" type="text/css"/>
    <?php echo Permiso::js(); ?>    
</head>
    
    <body class="<?php  if(isset($body_class_name)) echo $body_class_name; //imprimimos clase del body ?>">

<div id="pagina">
   
   <div id="header"> 
     <div id="header_superior">
       <div id="logo_dif"><img src="../../img/logo_superior.png" class="logo_superior"  /></div>
       <div id="titulo_header">
       <?php echo ($_SESSION['module_desc']=='')?'Sistema de Mujeres Avanzando':$_SESSION['module_desc'];?>
      
       </div>
       
       <div id="logo_bienestar"><img src="../../img/logo_bienestar.png" width="204" height="58" /></div> 
                   
      </div>
     
      
      
      
      <div id="header_inferior">
        	<ul id="menu_principal">
            
            <li><a href="<?php echo $_SESSION['app_path_p'];?>login/log/menu.php">Listado M&oacute;dulos</a></li>|
            <li><a href="<?php echo $_SESSION['app_path_p'];?>login/log/logout.php">Cerrar Sesi&oacute;n</a></li>|
            <li><a href="<?php echo $_SESSION['app_path_p'];?>usuario/usuario/clave_usuario.php">Cambiar Clave</a></li>
           
            <?php if($_SESSION['module_name'] != 'login'){ ?>
             |<!--<li><a href='javascript:history.back(-1)'>Regresar</a></li>|  -->  
            <li><a href="<?php echo $_SESSION['module_path'];?>ini/index.php">Inicio Subm&oacute;dulos</a></li>                
            <?php } ?>
            
            </ul>
            <div id="bloque_usuario">Bienvenido <?php  echo $_SESSION['nombres'];?></div>
            
        </div>
        
                
    
  </div>
 
   <div id="precontenido">   
   <?php 
    //Mostramos menú de acciones si no estamos en un menú principal
    //if(basename($_SERVER['PHP_SELF']) != 'menu.php'){         
        //include($_SESSION['inc_path'].'menu_acciones2.php');
        include($_SESSION['inc_path'].'menu_acciones.php');
    //}
    ?>    
    <div id="cintillo">
         <label><?php echo $_SESSION['submodule_desc']; ?></label>     
   </div>
   </div>         