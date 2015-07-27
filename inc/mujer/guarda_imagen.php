<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
include ($_SESSION['inc_path']."libs/Permiso.php");
//Modelo de log_mujeres_avanzando
include_once($_SESSION['model_path'].'log_mujeres_avanzando.php');
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

$img = $_FILES["img"];
$folio = $_POST["folio"];
$clase = 'info_msg';
$mensaje = "";
$ruta = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS'];

$allowedExts = array("jpeg", "jpg", "png","JPEG","JPG","PNG");
$allowedTypes = array("image/jpeg","image/jpg","image/pjpeg","image/x-png","image/png");
//$allowedExts = array("png");
//$allowedTypes = array("image/png");

$temp = explode(".", $img["name"]);
$extension = end($temp);
$nombre = $folio . '.' .$extension;

$file = $ruta . $nombre;

if(in_array($img["type"], $allowedTypes) && in_array($extension, $allowedExts)){  

  if($img["error"] > 0){
    $mensaje = "Código de error al guardar imagen : " . $img["error"];
    $clase = 'adv_msg';
  }elseif(move_uploaded_file($img["tmp_name"],$file)){

      //Nombre del archivo final
      $nuevo = $ruta.$folio.'.png';
      
      //Convertimos a png
      Permiso::imageToPng($file,$nuevo);

      //Eliminamos archivo original (en caso de no ser png)
      if(strtolower($extension) != 'png'){
        unlink($file);
      }

      $mensaje = 'Imagen subida con éxito';

      //Guardamos fecha en que fue tomada la foto en el log
      $data = Array('folio' => $folio,
                    'fecha_foto' => date('Y-m-d h:i:s')); 
      
      $msg = logMujeresAvanzando::saveLogMujeresAvanzando($data);
      
      if($msg != 1){
        echo 'Mensaje: '.$msg;
      }
    
    $porciones = explode("-", $folio);

	if(count($porciones == 2)){
		$folio = $porciones[0];
		$num_folio = $porciones[1];
	}
    
      //Guardamos fecha en que fue tomada la foto en la tabla de mujeres_avanzando
      $msg = mujeresAvanzando::actualizaFoto($folio,$num_folio);
      
      if($msg != 1){
        echo 'Mensaje: '.$msg;
      } 

  }else{
    $mensaje = 'Imagen '.$nombre.' no se pudo copiar al destino '.$file;
    $clase = 'error_msg';  
  }

}else{
  $mensaje = 'Imagen con formato inválido: '.$extension.' . El formato debe ser .png';
  $clase = 'error_msg';
}

?> 
<div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>