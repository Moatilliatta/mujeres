<?php session_start(); 

//Incluimos librerías necesarias
include_once($_SESSION['inc_path']."conecta.php");
include_once($_SESSION['inc_path'].'libs/Permiso.php'); 
include_once($_SESSION['inc_path'].'libs/cartilla_carrito.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Obtenemos arreglo de carrito
$articulos = unserialize($_SESSION['arrayArt']);
$mujer = NULL;

if($articulos != NULL){
    //Obtenemos datos
    $mujer = mujeresAvanzando::datos_cartilla($articulos); 
}else{
    echo $_SERVER['HTTP_REFERER'];
    //Si no existe el arreglo, volvemos a la página anterior
    header("Location: lista_mujer.php");
}

?>            

<?php if($mujer != NULL){?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
    <meta charset="UTF-8"/>
    <title>Credencial</title>
    <script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>jquery-1.10.2.min.js"></script>        
    <link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'credencial.css' ?>" type="text/css"/>    
    </head>    
    <body onload="window.print();return false;">  

    <div id="contenedor">
        
    <?php foreach($mujer as $key => $m): ?>
                
        <?php 
        $folios[] = $m['folio'];
        include($_SESSION['inc_path']."mujer/cred.php"); ?>
    
    <?php endforeach; ?> 

    </div>    

    <script type='text/javascript'>
    <?php
    if($folios){
        $js_array = json_encode($folios);
        echo "var folios = ". $js_array . ";";
    }    
    ?>
    </script>
    <script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>imprimir.js"></script>    
    </body>
</html>

<?php } ?>
