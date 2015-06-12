<?php
//crear y destruir session  
session_start();  
// vaciarla  
$_SESSION = array();  
// destruirla  
session_destroy();  

//Redireccionar a inicio
//echo  '<script language="JavaScript">location.href="login.php"</script>';
header('Location:../index.php');
?>