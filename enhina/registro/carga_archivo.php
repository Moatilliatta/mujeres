<?php
session_start();

//Librería de conexión
include ($_SESSION['inc_path']."conecta.php");
 
//Primer Formato
//include_once($_SESSION['inc_path'].'libs/CargaExcel.php');

//2do formato con encabezados que ocupan más filas
//include_once($_SESSION['inc_path'].'libs/CargaExcel2.php');

//3er formato usado a partir de SEDESOL
include_once($_SESSION['inc_path'].'libs/CargaExcel3.php');

//Obtenemos código de error
$error = $_FILES['archivo']['error'];

//Tipo de Archivo, predeterminado el alta
      $tipo = 'subir_archivo';
      
//Revisamos si el archivo no tiene algún error
if($error == 0) { 

    

      //Obtenemos id de caravana
      $id_caravana = $_POST['id_caravana'];
      //Obtenemos número de visita
      $visita = $_POST['visita'];

      //Eliminamos variable de sesión
      unset($_SESSION['totales']);

      //Respuesta al intentar guardar datos
      $resp = 0;

      //establecemos formato del archivo
      $allowedExts = array("xlsx",'xls');//extensiones de archivo que se permitiran
      $temp = explode(".", $_FILES["archivo"]["name"]);//sacamos el nombre y la extension del archivo
      //print_R($temp);
      //exit;
      $nombre = $temp[0];//nombre_archivo
      $extension = end($temp);//tipo de extension del archivo

      //el tipo de archivo, por ahora xlsx y xls
      $mime[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
      $mime[] = 'application/vnd.ms-excel';
      $mime[] = 'application/msexcell';

      $destino = $_SESSION['files_path'].$_FILES["archivo"]["name"];//obtengo nombre del archivo y tipo de extension y tiene concatenada la ruta donde se guadra el archivo

      /*
      echo '<br>'.$destino;
      exit;
      */

      if ((in_array($_FILES["archivo"]["type"],$mime)) && in_array($extension, $allowedExts)){
        
        if ($_FILES["archivo"]["error"] > 0){
            
            //Error al cargar archivo
            $resp = 'Error al cargar el archivo: '.$_FILES["archivo"]["error"];
          
          }else if(move_uploaded_file($_FILES['archivo']['tmp_name'],$destino)){
              
              list($resp,$totales) = CargaExcel3::carga($nombre,null,$id_caravana,$visita,$extension);

              //Creamos variable de sesión para tener los totales de la carga
              $_SESSION['totales'] = $totales;

              /*Si la respuesta es exitosa enviamos al archivo de
              carga_exitosa, caso contrario regresamos a subir_archivo
              */      
              if($resp == 1){         
                  $tipo = 'subir_archivo';            
              }

          }else{
            $resp = 23;
          }

      }else{
            //Formato no válido
            $resp = 24;
      }

} else { 

    //Error relacionado con archivo
    $resp = $error + 100;

} 

//Redireccionamos con el tipo de respuesta
//echo '<script language="JavaScript">location.href="'. $tipo .'.php?r=' . $resp .'"</script>';
header('Location:'.$tipo.'.php?r=' . $resp);    
?>