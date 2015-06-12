<?php
session_start();

include ("../../inc/conecta.php"); 

//Obtenemos la respuesta al momento de querer loguearnos
$respuesta = $_GET["r"];

include_once('../../inc/libs/Permiso.php');

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
//Validamos Respuesta
if($respuesta == 1) {
		
    //Actualizamos la variable de sesión del módulo
    
    Permiso::updateModule();	
	
    header('Location: menu.php');    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
    <meta charset="UTF-8"/>
    <title>Login</title>
	<link href="../../css/modulo/login.css" rel="stylesheet" type="text/css"/>
     <script lang="javascript" type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>        
     <!-- jqueryui -->
     <script lang="javascript" type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>             
     <script lang="javascript" type="text/javascript" src="../../js/jquery.validate.js"></script>
     <script lang="javascript" type="text/javascript" src="../../js/inicio.js"></script>       
    </head>
    <body>    
    
    <div id="pagina">
    <div id="header">
    	<div id="header_superior">
            <div id="logo_dif"><img src="../../img/logo_mujeresav.png" width="104" height="78" /></div>
            <div id="titulo_header">Sistema de Mujeres Avanzando</div>
            <div id="logo_bienestar"><img src="../../img/logo_bienestar.png" width="204" height="58" /></div>
        </div>        
    </div>
 
    	<div id="principal">	
            <?php if($respuesta > 0){?>    
            <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>    
            <?php } ?>
            
        	<div  align="center">                
                <?php
                    //Si el registro no es exitoso mostramos el formulario de usuario 
                    if($respuesta != 1){        
                        include_once("form_login.php");    
                    }
                ?>
        	</div>        
    	</div>
     
     <div id="footer">
    	<div id="footer_superior">
        	<div id="logo_dif_footer"><img src="../../img/logo_dif_inferior.png" width="64" height="48" /></div>
            <div id="titulo_footer">Sistema de Mujeres Avanzando</div>
        </div>
        <div id="footer_inferior">
        	&copy; Sistema para el Desarrollo Integral de la Familia del Estado de Jalisco
        </div>
    </div>    
    
	
    </div>        
    </body>
</html>