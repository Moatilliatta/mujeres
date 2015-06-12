<?php
session_start();
//Cargamos la conexión
include ('../../inc/conecta.php');
//Cargamos clase de permiso
include_once('../../inc/libs/Permiso.php');

//Obtenemos variables post (faltaría validar injección de código)
$usuario = $_POST["usuario"];
$clave = $_POST["clave"];
$caravana = $_POST['id_caravana'];

//Respuesta al Verificar usuario
$resp = 0;

// Campos de momento obligatorios
if ($usuario && $clave) {

    //Convertimos clave a MD5
    $clave = Permiso::encripta($clave);
    
    //usamos function get_first
    /*Como nos regresa array multidimencional,
    obtenemos el único arreglo*/
    $usr =$db
    ->where('usuario', $usuario)
    ->where('clave', $clave)
    ->where('activo', 1 )
    ->getOne('usuario');
    
    // Se inició sesión correctamente
    if (is_array($usr)) {
        //Respuesta de éxito
        $resp = 1;
       
        //Variables de identificación del usuario
        $_SESSION["login"] = 1;
        $_SESSION["usr_id"] = $usr['id'];
        $_SESSION["usuario"] = $usuario;
        $_SESSION['id_caravana'] = $caravana;
        $_SESSION["nombres"]=$usr['nombres'].' '.$usr['paterno'].' '.$usr['materno'];  
        $_SESSION['LAST_ACTIVITY'] = time();                                      
        
        //Obtenemos IP del equipo
        $ip = Permiso::obtenerIP();
        
        //Guardamos la IP y la fecha de su ingreso
        $updateData = array(
    	'ip_ultimo_ing' => $ip,
    	'fecha_ingreso' => date('Y-m-d H:i:s')
        );
        $db->where('id', $usr['id']);
        $resultado = $db->update('usuario', $updateData);
        
        //Error Desconocido al guardar el registro
        if ($resultado == 0) {
            $resp = 3;
        }
                        
        //Inicializamos variables de sesión
        Permiso::varRutas();                

    }else{
        
        //Usuario no encontrado
        $resp = 7;
    }

} else {

    //Error: campos incompletos
    $resp = 2;

}

// Redirecciono a index con el parametro $r que es el que trae la condicion de la Insercion

//echo $resp;

//echo '<script language="JavaScript">location.href="../usuario/index.php?r='.$resp .'"</script>';
echo header('Location: ../index.php?r='.$resp );
exit;
?>