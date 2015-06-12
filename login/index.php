<?php
session_start();

//phpinfo();

include ("../inc/conecta.php"); 

//Obtenemos la respuesta al momento de querer loguearnos
$respuesta = $_GET["r"];

include_once('../inc/libs/Permiso.php');

//Nos ayudará a determinar el navegador del cliente
include_once('../inc/libs/Navegador.php');

//Mensaje respuesta
list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
//Validamos Respuesta
if($respuesta == 1) {
		
    //Actualizamos la variable de sesión del módulo
    
    Permiso::updateModule();	
	
    header('Location: log/menu.php');    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
    <meta charset="UTF-8"/>
    <title>Login</title>
    
    <link href="../css/benefits.css" rel="stylesheet" type="text/css" />
	<link href="../css/modulo/login.css" rel="stylesheet" type="text/css"/>
    
    <script lang="javascript" type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>        
     <!-- jqueryui -->
     <script lang="javascript" type="text/javascript" src="../js/jquery-ui-1.10.3.custom.min.js"></script>             
     <script lang="javascript" type="text/javascript" src="../js/jquery.validate.js"></script>
     <script lang="javascript" type="text/javascript" src="../js/inicio.js"></script>       
    </head>
    <body>    
    
    <div id="pagina">
    <div id="header">
    	<div id="header_superior">
            <div id="logo_dif"><img src="../img/logo_superior.png" /></div>
            <div id="titulo_header">Sistema de Mujeres Avanzando</div>
            <div id="logo_bienestar"><img src="../img/logo_bienestar.png" width="204" height="58" /></div>
        </div>        
    </div>
    
    <div id="principal">
    	   <?php if($respuesta > 0){?>    
            <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>    
            <?php } ?>                       
            
        	<div  align="center">                
                <?php
                    //Obtenemos datos del navegador
                    $Nav = new Navegador();

                    if($Nav->Name == 'msie'){
                        echo '
                        <div class="mensaje">
                        Para el correcto el uso del sistema, le recomendamos el uso de otro navegador,
                        como <a target="_blank" href="https://www.mozilla.org/">Firefox</a> &Oacute;  
                        <a target="_blank" href="https://www.google.com/chrome/">Chrome</a>. 
                        Evite usar Internet Explorer.
                        </div>';
                    }                    

                    //Si el registro no es exitoso mostramos el formulario de usuario 
                    if($respuesta != 1){        
                        include_once("log/form_login.php");    
                    }
                ?>
        	</div>
    </div>   
    
     <div id="footer">
    	<div id="footer_superior">
        
         <ul class="enlaces_externos">
              <li class="link_ce"><a href="http://ce.jalisco.gob.mx/" target="_blank">Contralor&iacute;a del Estado</a></li>
              <li class="link_fge"><a href="http://fge.jalisco.gob.mx/" target="_blank">Fiscal&iacute;a General del Estado</a></li>
              <li class="link_prosoc"><a href="http://prosoc.jalisco.gob.mx/" target="_blank">Procuradur&iacute;a Social del Estado</a></li>
              <li class="link_cultura"><a href="http://www.cultura.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Cultura</a></li>
              <li class="link_sedeco"><a href="http://sedeco.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Desarrollo Econ&oacute;mico</a></li>
              <li class="link_seder"><a href="http://seder.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Desarrollo Rural</a></li>
              <li class="link_sedis"><a href="http://sedis.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Desarrollo e Integraci&oacute;n Social</a></li>
              <li class="link_se"><a href="http://se.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Educaci&oacute;n</a></li>
              <li class="link_secyt"><a href="http://sicyt.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Innovaci&oacute;n, Ciencia y Tecnolog&iacute;a</a></li>
              <li class="link_semadet"><a href="http://semadet.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Medio Ambiente y Desarrollo Territorial</a></li>
              <li class="link_semov"><a href="http://semov.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Movilidad</a></li>
              <li class="link_sepaf"><a href="http://sepaf.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Planeaci&oacute;n, Administraci&oacute;n y Finanzas</a></li>
              <li class="link_ssj"><a href="http://ssj.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Salud</a></li>
              <li class="link_stps"><a href="http://stps.jalisco.gob.mx/" target="_blank">Secretar&iacute;a del Trabajo y Previsi&oacute;n Social</a></li>
              <li class="link_secturjal"><a href="http://secturjal.jalisco.gob.mx/" target="_blank">Secretar&iacute;a de Turismo</a></li>
              <li class="link_ijs"><a href="http://ijm.jalisco.gob.mx/" target="_blank">Instituto Jalisciense de la Mujer</a></li>
              <li class="link_code"><a href="http://www.codejalisco.gob.mx/" target="_blank">Consejo Estatal para el Fomento Deportivo Jalisco</a></li>
              <li class="link_sistemadif"><a href="http://sistemadif.jalisco.gob.mx" target="_blank">Sistema DIF del Estado de Jalisco</a></li>
            </ul>
        
        	<div id="logo_dif_footer"><img src="../img/logo_inferior.png" /></div>
            <div id="titulo_footer">Sistema de Mujeres Avanzando</div>
            
           
            
            
        </div>
        <div id="footer_inferior">
             
             
             
        	&copy; Sistema para el Desarrollo Integral de la Familia del Estado de Jalisco
             
        </div>
    </div>     
	
    </div>        
    </body>
</html>