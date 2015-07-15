<?php
/**
 * Clase que nos permite administrar variables globales, restricciones y demás
 * **/
class Permiso extends MysqliDb{

    /**
     * Archivos js generales para la aplicación
     * */     

    public static function js(){

        list($path, $modulo, $submodulo, $app, $root, $app_path_p) = self::dirPath();

         $app_path_p = preg_replace('/\/\/+/', '/', $app_path_p.'/');

        $js = '
        <script lang="javascript" type="text/javascript" src="'. $app_path_p  .'js/jquery-1.10.2.min.js"></script>        
        <script lang="javascript" type="text/javascript" src="'. $app_path_p  .'js/jquery-migrate-1.0.0.js"></script>
        <!-- jqueryui -->        
        <script lang="javascript" type="text/javascript" src="'. $app_path_p  .'js/jquery-ui-1.10.3.custom.min.js"></script>             
        <script lang="javascript" type="text/javascript" src="'. $app_path_p .'js/jquery.validate.js"></script>
        <script lang="javascript" type="text/javascript" src="'. $app_path_p .'js/inicio.js"></script>
        ';

        return $js;

    }

    /**
     * Reemplazamos todos los caracteres latinos por letras del
     * alfabeto 'normal'
     * @param string $cadena Cadena a cambiar
     *
     * @return string $cadena Cadena cambiada
     */

    public static function quitaSaltos($cadena){
    //La posición corresponde con la letra que será sustituida del arreglo 

      $caracteres = array('\n','\r','\r\n');
    	$sustitucion = array('','',''); 

      return str_replace($caracteres, $sustitucion, $cadena);
    }

    public static function limpiar($String){

       $String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
       $String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
       $String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
       $String = str_replace(array('í','ì','î','ï'),"i",$String);
       $String = str_replace(array('é','è','ê','ë'),"e",$String);
       $String = str_replace(array('É','È','Ê','Ë'),"E",$String);
       $String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
       $String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
       $String = str_replace(array('ú','ù','û','ü'),"u",$String);
       $String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
       $String = str_replace(array('[','^','´','`','¨','~',']'),"",$String);
       $String = str_replace("ç","c",$String);
       $String = str_replace("Ç","C",$String);
       $String = str_replace("ñ","n",$String);
       $String = str_replace("Ñ","N",$String);
       $String = str_replace("Ý","Y",$String);
       $String = str_replace("ý","y",$String);
       
       return $String;
   }

    public static function cambiaChar($cadena){

    //La posición corresponde con la letra que será sustituida del arreglo 'latin' al 'normal'
    $normal = array("a","e","i","o","u","a","e","i","o","u","a","e","i","o","u","n",
      "A","E","I","O","U","A","E","I","O","U","N",'/','\\','\n','\r','\r\n');

    $latin = array("á","é","í","ó","ú","ä","ë","ï","ö","ü","à","è","ì","ò","ù","ñ",
      "Á","É","Í","Ó","Ú","À","È","Ì","Ò","Ù","Ñ",'//','\\\\','','',''); 

        return str_replace($latin, $normal, $cadena);
    }

     /**
     * Validamos la respuesta despues de ejecutarse determinada acción
     * 
     * @param int $opc Opción a procesar
     * @return string $respuesta Respuesta procesada
     * */
    public static function mensajeRespuesta($opc,$param = ""){

        switch ($opc) {

            case 1: $respuesta = 'El registro se guardó correctamente.';
                    break;
            case 2: $respuesta = 'Error al guardar el registro, campos incompletos.';
                    break;
            case 3: $respuesta = 'Error al guardar el registro, verifique que estén completos todos los campos obligatorios y dependientes de otros.';
                    break;
            case 4: $respuesta = 'Error al guardar, las claves no coinciden';
                    break;
            case 5: $respuesta = 'Error, correo no válido';
                    break;
            case 6: $respuesta = 'La CURP que especificó ya está en uso. Verifique si el beneficiario ya estaba registrado.';
                    break;
            case 7: $respuesta = 'Usuario y/o clave no encontrado';
                    break;
            case 8: $respuesta = 'No hay registros para mostrar con el criterio elegido';
                    break;
            case 9: $respuesta = 'Su cuenta de usuario no tiene permisos para llevar a cabo esta operación.';
                    break;
            case 10: $respuesta = 'Clave incorrecta, favor de verificar.';
                     break;
            case 11: $respuesta = 'Cambio de clave correcto';
                     break;
            case 12: $respuesta = 'Debe seleccionar al menos un campo';
                     break;
            case 13: $respuesta = 'Debe iniciar sesión para continuar.';
                     break;
            case 14: $respuesta = 'No se actualizó el registro porque no hubo cambios en ningún campo. 
                                   Verifique los datos si desea modificarlos.';
                     break;
            case 15: $respuesta = 'CURP errónea, no corresponde a los datos de la persona 
                                   (nombre completo, fecha de nacimiento, estado de origen y sexo)';
                     break;
            case 16: $respuesta = 'Beneficiario ya asignado a un centro de atención';
                     break;
            case 17: $respuesta = 'El número de expediente indicado ya está en uso.';
                     break; 
            case 18: $respuesta = 'El nombre del aspirante ya está registrado, compruebe si no tiene asignado otro expediente.';
                     break;
            case 19: $respuesta = 'Faltan hojas en el excel de ENHINA';                 
                     break;
            case 20: $respuesta = 'Número incorrecto de columnas y/o datos incorrectos';
                     break;
            case 21: $respuesta = 'Número ID de entrevista duplicado.';
                     break;
            case 22: $respuesta = 'Números ID de entrevista no coinciden en ambas hojas de excel';
                     break;
            case 23: $respuesta = 'Error al subir el archivo: '.$_FILES["file"]["error"];
                     break;
            case 24: $respuesta = 'Archivo con formato inválido';
                     break;
            case 25: $respuesta = "No se agregó a la beneficiaría, seleccione de nuevo.";
                     break;
            case 26: $respuesta = "Beneficiaria descartada";
                     break;
            case 27: $respuesta = "Error con el arreglo";
                     break;
            case 28: $respuesta = "Se ha vaciado el listado exitosamente";
                     break;
            case 29: $respuesta = "Beneficiaria ya agregada previamente";            
                     break;
            case 30: $respuesta = "Beneficiario no encontrado o inexistente";
                     break;
            case 100 : $respuesta = "Archivo cargado con éxito";
                        break;
            case 101 : $respuesta = "El archivo excede el límite de la directiva upload_max_filesize en php.ini";
                        break;
            case 102 : $respuesta = "El archivo excede el límite de la directiva MAX_FILE_SIZE especificada en el formulario HTML" ;
                        break;
            case 103 : $respuesta = "El archivo fue subido parcialmente intente de nuevo";
                        break;
            case 104 : $respuesta = "No se subió ningún archivo";
                        break;
            case 105 : $respuesta = "No se subió ningún archivo, disco lleno no se puede guardar";
                        break;
            case 106 : $respuesta = "Falta directorio temporal disponible" ;
                        break;
            default: $respuesta = $opc;
                     break;
        }

        switch ($opc) {

            case 1: case 11: case 28: case 100:
                    $clase = 'info_msg';
                    break;
            case  2: case 3: case 4: case 5: case 6: case 7: case 9: 
            case 10: case 12: case 15: case 17: case 18: case 19: case 20:
            case 21: case 22: case 23: case 24: case 25: case 27: case 101:
            case 102:case 103: case 104:case 105:case 106:
                    $clase = 'error_msg';
                    break;
            
            case 8: case 13: case 14: case 16: case 26: default:
                    $clase = 'adv_msg';
                    break;

        }        

        return array(utf8_encode($respuesta),$clase);

    }


    /**
     * Obtenemos listado de acciones de menú POR GRUPO. Mediante el nombre del menú
     * realizamos filtrado de cuales puede ver o no conforme a si está activo o si lo
     * tiene ligado por su grupo
     * 
     * @param int $id_usuario id_usuario del que obtendremos sus grupos disponibles a verificar     
     * @param int $nombre_menu filtramos el tipo de menu mediante el nombre
     * @param string $nombre_accion nombre_accion con la que verificaremos sus "hermanos" (acciones) disponibles
     * @param int $id_submodulo id del submódulo que filtraremos 
     * 
     * @return array[] Arreglo con la información requerida
     **/
    public static function listaMenuAcciones($nombre_menu = null,
      $nombre_accion = null, $id_submodulo = null,$id_grupo = null,$id_usuario = null)
    {

        if($id_usuario == NULL){
            $id_usuario = $_SESSION["usr_id"];
        }

        $sql = 
        'SELECT 
         DISTINCT a.id as id_accion,        
        a.nombre as nombre_accion,
        a.descripcion as descripcion_accion,
        sm.id as id_submodulo,
        sm.nombre as nombre_submodulo,
        sm.descripcion as descripcion_submodulo,
        -- ag.id as accion_grupo,                
        me.nombre as nombre_menu
        FROM `accion_grupo` ag
        INNER JOIN accion a on ag.id_accion = a.id AND a.activo = 1 -- obtenemos acciones disponibles
        INNER JOIN submodulo sm on  a.id_submodulo = sm.id AND sm.activo = 1 -- obtenemos submódulos disponibles
        left JOIN menu_accion_grupo mag on mag.id_accion_grupo = ag.id AND mag.activo = 1
        left join menu me on mag.id_menu = me.id AND me.activo = 1
        WHERE ?      
        and ag.activo = 1        
        AND me.id not in (
            SELECT IFNULL(id_modulo,0)
            FROM bloqueo 
            WHERE id_usuario = ? OR id_grupo = ag.id_grupo OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el módulo no esté bloqueado 
        AND sm.id not in (
            SELECT IFNULL(id_submodulo,0)
            FROM bloqueo 
            WHERE id_usuario = ? OR id_grupo = ag.id_grupo OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el submódulo no esté bloqueado             
        AND a.id not in (
            SELECT IFNULL(id_accion,0)
            FROM bloqueo 
            WHERE id_usuario = ? OR id_grupo = ag.id_grupo OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que la acción no esté bloqueado        
        ';

        //Parámetros de la sentencia
        $params = array(1,$id_usuario,$id_usuario,$id_usuario);

        //Filtramos por el nombre del menú
        if($nombre_menu !== NULL){
            $sql .= ' AND me.nombre = ? ';
            $params[] = $nombre_menu;            
        }

        //Filtramos por el nombre de la acción
        if ($nombre_accion !== null) {//seleccionamos el grupo y el submódulo que filtraremos

            $sql .= ' 
            AND sm.id = (
            	SELECT sub.id 
            	FROM submodulo sub 
            	left JOIN accion acc on acc.id_submodulo = sub.id 
            	WHERE acc.nombre = ?) ';

            $params[] = $nombre_accion;

        } else if ($id_submodulo !== null) { //seleccionamos el submódulo que filtraremos
                $sql .= ' AND sm.id = ? ';
                $params[] = $id_submodulo;
        }

        //Filtramos grupo
        if ($id_grupo !== null) { //seleccionamos el submódulo que filtraremos

                $sql .= ' AND ag.id_grupo = ? ';
                $params[] = $id_grupo;
            }

        if($id_usuario !== NULL){
                $sql .= ' AND ag.id_grupo IN (
                    SELECT IFNULL(id_grupo,0) 
                    FROM usuario_grupo 
                    WHERE id_usuario = ? AND activo = 1) ';
                $params[] = $id_usuario;
            }  
            
        $sql.=' GROUP BY 1 ORDER BY a.orden,a.descripcion asc';
            

        /*
        echo $sql;
        print_r($params);
        exit;
        */

        //Regresamos resultado
        return self::executar($sql, $params);

    }

    /**
     * Verifica que un usuario tenga acceso a determinada accion de un submódulo de un modulo, 
     * 
     * @param string $accion a verificar
     * @param string  a verificar
     * @param string $modulo a verificar
     * @param int $usuario a verificar (opcional)
     * @return object
     **/
    public static function accesoAccion($accion, $submodulo, $modulo, $id_usuario = 0)
    {
        //Predeterminadamente ponemos el id del usuario logueado
        if (!$id_usuario) {
         $id_usuario = $_SESSION['usr_id'];
        }

        //Armamos sentencia
        $sql = 
        'SELECT
        a.id as id_accion,
        a.nombre as nombre_accion,
        a.descripcion as descripcion_accion
        FROM usuario_grupo ug
        INNER JOIN usuario u on u.id = ug.id_usuario AND u.activo = 1 -- usuario activo
        INNER JOIN grupo g on g.id = ug.id_grupo AND g.activo = 1 -- vemos los grupos activos
        INNER JOIN accion_grupo ag on ag.id_grupo = g.id AND ag.activo = 1 -- obtenemos las acciones del grupo
        INNER JOIN accion a on a.id = ag.id_accion AND ag.activo = 1 AND a.activo = 1 -- vemos acciones activas
        INNER JOIN submodulo sm on sm.id = a.id_submodulo AND sm.activo = 1 -- obtenemos los submodulos de las acciones					
        INNER JOIN modulo m on m.id = sm.id_modulo AND m.activo = 1 -- obtenemos los módulos
        WHERE ug.activo = 1
            AND u.id = ? 
            AND sm.nombre = ?
            AND m.nombre = ? 
            AND a.nombre = ? 
            AND g.id not in (
                SELECT IFNULL(id_grupo,0) 
                FROM bloqueo 
                WHERE id_usuario = u.id 
                  OR id_grupo = g.id 
                  OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el grupo no esté bloqueado            
            AND m.id not in (
                SELECT IFNULL(id_modulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el módulo no esté bloqueado 
            AND sm.id not in (
                SELECT IFNULL(id_submodulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el submódulo no esté bloqueado             
            AND a.id not in (
                SELECT IFNULL(id_accion,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que la acción no esté bloqueado
          GROUP BY 1 
          ORDER BY sm.descripcion 
          asc limit 0, 1';

        //Parámetros de la sentencia
        $params = array(
            $id_usuario,
            $submodulo,
            $modulo,
            $accion);

        /*
        echo $sql;
        print_r($params);
        */

        //Regresamos resultado
        return self::executar($sql, $params);

    }

    /**
     * Obtenemos los Módulos a los cuales tiene acceso el usuario
     * @param $id_usuario int Id del Usuario
     * @return mysql array 
     * */
    public static function getModulos($id_usuario = 0){

        //Predeterminadamente ponemos el id del usuario logueado        
        if (!$id_usuario) {
            $id_usuario = $_SESSION['usr_id'];
        }

        //Armamos query
        $sql = 
        'SELECT
        m.id as id_modulo,
        m.nombre as nombre_modulo,
        m.descripcion as descripcion_modulo
        FROM usuario_grupo ug
        INNER JOIN usuario u on u.id = ug.id_usuario AND u.activo = 1 -- usuario activo
        INNER JOIN grupo g on g.id = ug.id_grupo AND g.activo = 1 -- vemos los grupos activos
        INNER JOIN accion_grupo ag on ag.id_grupo = g.id AND ag.activo = 1 -- obtenemos las acciones del grupo
        INNER JOIN accion a on a.id = ag.id_accion AND ag.activo = 1 AND a.activo = 1 -- vemos acciones activas
        INNER JOIN submodulo sm on sm.id = a.id_submodulo AND sm.activo = 1 -- obtenemos los submodulos de las acciones					
        INNER JOIN modulo m on m.id = sm.id_modulo AND m.activo = 1 -- obtenemos los módulos
        WHERE ug.activo = 1 
            AND u.id = ?            
            AND g.id not in (
                SELECT IFNULL(id_grupo,0) 
                FROM bloqueo 
                WHERE id_usuario = u.id 
                    OR id_grupo = g.id 
                    OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el grupo no esté bloqueado
            AND m.id not in (
                SELECT IFNULL(id_modulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                    OR id_grupo = g.id 
                    OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el módulo no esté bloqueado 
        GROUP BY 1 ORDER BY m.orden,sm.descripcion asc';

        //Parámetros de la sentencia        
        $params = array($id_usuario);
        
        //Regresamos resultado
        return self::executar($sql, $params);        
    }

    /**
     * Obtenemos los Submódulos a los cuales tiene acceso el usuario
     * @param $nombre_modulo varchar Nombre del Módulo
     * @param $id_usuario int Id del Usuario
     * @return mysql array 
     * */
    public static function getSubmodulos($nombre_modulo, $id_usuario = 0)
    {

        //Predeterminadamente ponemos el id del usuario logueado
        if (!$id_usuario) {
            $id_usuario = $_SESSION['usr_id'];
        }

        //Armamos sentencia
        $sql = 
        'SELECT
        sm.id as id_submodulo,
        sm.nombre as nombre_submodulo,
        sm.descripcion as descripcion_submodulo
        FROM usuario_grupo ug
        INNER JOIN usuario u on u.id = ug.id_usuario AND u.activo = 1 -- usuario activo
        INNER JOIN grupo g on g.id = ug.id_grupo AND g.activo = 1 -- vemos los grupos activos
        INNER JOIN accion_grupo ag on ag.id_grupo = g.id AND ag.activo = 1 -- obtenemos las acciones del grupo
        INNER JOIN accion a on a.id = ag.id_accion AND ag.activo = 1 AND a.activo = 1 -- vemos acciones activas
        INNER JOIN submodulo sm on sm.id = a.id_submodulo AND sm.activo = 1 -- obtenemos los submodulos de las acciones					
        INNER JOIN modulo m on m.id = sm.id_modulo AND m.activo = 1 -- obtenemos los módulos
        WHERE ug.activo = 1 
            AND u.id = ?
            AND m.nombre = ?
            AND g.id not in (
                SELECT IFNULL(id_grupo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                    OR id_grupo = g.id 
                    OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el grupo no esté bloqueado 
            AND m.id not in (
                SELECT IFNULL(id_modulo,0)
                FROM bloqueo
                WHERE id_usuario = u.id 
                    OR id_grupo = g.id 
                    OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el módulo no esté bloqueado
            AND sm.id not in (                
                SELECT IFNULL(id_submodulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                    OR id_grupo = g.id 
                    OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el submódulo no esté bloqueado
        GROUP BY 1 ORDER BY sm.descripcion asc';

        //Parámetros de la sentencia
        $params = array($id_usuario, $nombre_modulo);        

        /*
        echo $sql;
        print_r($params);
        */

        //Regresamos resultado
        return self::executar($sql, $params);
    }

    /**
     * Obtenemos las Acciones a los cuales tiene acceso el usuario
     * @param $id_submodulo varchar Id del Submódulo
     * @param $id_usuario int Id del Usuario
     * @return mysql array
     * */
    public static function getAcciones($id_submodulo, $id_usuario = 0)
    {
        //Predeterminadamente ponemos el id del usuario logueado
        if (!$id_usuario) {
            $id_usuario = $_SESSION['usr_id'];
        }

        //Armamos query
        $sql = 
        'SELECT
        a.id as id_accion,
        a.nombre as nombre_accion,
        a.descripcion as descripcion_accion
        FROM usuario_grupo ug
        INNER JOIN usuario u on u.id = ug.id_usuario AND u.activo = 1 -- usuario activo
        INNER JOIN grupo g on g.id = ug.id_grupo AND g.activo = 1 -- vemos los grupos activos
        INNER JOIN accion_grupo ag on ag.id_grupo = g.id AND ag.activo = 1 -- obtenemos las acciones del grupo
        INNER JOIN accion a on a.id = ag.id_accion AND ag.activo = 1  
                            AND a.activo = 1 -- vemos acciones activas
        INNER JOIN submodulo sm on sm.id = a.id_submodulo AND sm.activo = 1 -- obtenemos los submodulos de las acciones
        INNER JOIN modulo m on m.id = sm.id_modulo AND m.activo = 1 -- obtenemos los módulos
        WHERE ug.activo = 1 
            AND u.id = ? 
            AND sm.id = ? 
            AND g.id not in (
                SELECT IFNULL(id_grupo,0) 
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el grupo no esté bloqueado            
            AND m.id not in (
                SELECT IFNULL(id_modulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el módulo no esté bloqueado 
            AND sm.id not in (
                SELECT IFNULL(id_submodulo,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que el submódulo no esté bloqueado             
            AND a.id not in (
                SELECT IFNULL(id_accion,0)
                FROM bloqueo 
                WHERE id_usuario = u.id 
                OR id_grupo = g.id 
                OR ( id_usuario is null AND id_grupo is null ) ) -- Vemos que la acción no esté bloqueado
        GROUP BY 1 ORDER BY a.orden, a.descripcion asc';

        //Parámetros de la sentencia
        $params = array($id_usuario, $id_submodulo);        

        //Regresamos resultado
        return self::executar($sql, $params);
    }

    private static function executar($sql, $params)
    {

        //Ejecutamos
        $resultado = self::getInstance()->rawQuery($sql, $params);

        //Regresamos resultado
        return $resultado;
    }

    //Obtenemos los niveles según el directorio físico actual
    public static function niveles()
    {
        //Directorio Físico
        $dir_act = getcwd();

        //echo '<br/ >getcwd: '.$dir_act.'<br/>';

        //Delimitadores de directorio
        $delimitadores = array('\\','/');

        //Obtenemos arreglo con cada uno de los niveles del directorio
        $dir_act = self::multiexplode($delimitadores, $dir_act);

        //print_r($dir_act);

        //Quitamos submódulo
        $submodulo = array_pop($dir_act);  

        //Obtenemos nombre del módulo y la app
        $modulo = array_pop($dir_act);
        $app = array_pop($dir_act);

        return array($app,$modulo,$submodulo);
    }

    /**
    *Obtenemos la ruta del proyecto
    *@return string $app_path_p Ruta del proyecto
    **/
    public static function getRoute(){
        //Ruta del proyecto
        $app_path_p = dirname(dirname(dirname(self::dirActualLimpia()))) . '/';             

        //Evitamos enviar diagonales vacías
        $app_path_p = (strlen($app_path_p) > 2)? $app_path_p : '/';
        
        if($app_path_p){
            //Al no tener el nombre del proyecto en el directorio, quitamos diagnal invertida
            $app_path_p = ($app_path_p == '\\/')? '/': $app_path_p;
        }        

        return $app_path_p;
    }

    /**
    *Obtenemos el directorio raíz subiendo 3 niveles físicos de archivos
    *@return string $root Directorio raíz
    **/
    public static function getRoot(){
        
        //Raíz
        $root = $_SERVER['DOCUMENT_ROOT'];

        //Guardamos directorio actual
        $actual = getcwd();
        
        //Subimos N niveles (submodulo -> modulo -> app)
        chdir("..");
        chdir("..");
        chdir("..");

        //Obtenemos directorio despues de movernos N niveles
        $root = getcwd();

        //Regresamos a directorio inicial
        chdir($actual);

        return $root;
    }

    /**
     * Obtiene la ruta principal
     * @return array($dir,$path)
     * */
    private static function dirPath()
    {

        //Variables de sesión para ubicarnos en los archivos principales        
        $path = dirname(dirname(dirname(__file__)));

        //Obtenemos los niveles
        list($app,$modulo,$submodulo) = self::niveles();        

		//Obtenemos directorio raíz
        $root = self::getRoot();

        //Ruta del proyecto
        $app_path_p = self::getRoute();

        /*
        echo "path : ".$path.
             "<br> root : ".$root.
             "<br> app_path_p : ".$app_path_p.             
             "<br> app : ".$app.
             "<br> modulo : ".$modulo.
             "<br> submodulo : ".$submodulo;
        exit;
        */

        return array($path, $modulo, $submodulo, $app, $root, $app_path_p);

    }

    /**
     * Divide una cadena en un arreglo con múltiples patrones de división
     * @param array $delimiters has to be array
     * @param string $string has to be array
     * 
     * return $array Array*/
    static function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);

    return  $launch;

    }

    /**
     * Actualizamos el módulo en el que nos encontremos
     * */
    public static function updateModule()
    {
        //Variables de sesión para ubicarnos en los archivos principales
        list($path,$modulo,$submodulo,$app,$root,$app_path_p) = self::dirPath();

        //Nombre del módulo
        $_SESSION['module_name'] = $modulo;
        //Nombre del Submodulo
        $_SESSION['submodule_name'] = $submodulo;

        //echo "module_name: " . $_SESSION['module_name'] . "<br>";
        
        //Descripción del Módulo
        $params = array($modulo);

        $mod = self::getInstance()->rawQuery('SELECT descripcion from modulo where nombre = ? limit 1',$params);
        $mod = ($mod!=NULL)? $mod[0]:null;
        
        //Descripcion del submodulo
        $params = array($submodulo,$modulo);
        
        $submod = self::getInstance()->rawQuery('SELECT
                                                s.descripcion as desc_submodulo
                                                FROM `submodulo` s
                                                LEFT JOIN modulo m on m.id = s.id_modulo
                                                where s.nombre = ? and  m.nombre = ?
                                                ',$params);        
         
        
         
        $submod = ($submod != NULL)? $submod[0] : NULL; 

        
        $_SESSION['module_desc'] = ($mod == NULL)? 'Sistema De Mujeres Avanzando' : $mod['descripcion']; 
        //echo "module_name: " . $_SESSION['module_desc'] . "<br>";
        
        //descripcion de submodulo
         $_SESSION['submodule_desc'] = ($submod == NULL)? '' : $submod['desc_submodulo']; 
        //echo "module_name: " . $_SESSION['module_desc'] . "<br>";

        //Ruta del módulo
        $_SESSION['module_path'] = $_SESSION['app_path_p'] . $_SESSION['module_name'] . '/';
        //echo "module_path: " . $_SESSION['module_path'] . "<br>";

        //Ruta del módulo desde raiz
        $_SESSION['module_path_r'] = $root .'/'. $app .'/'. $_SESSION['module_name'] . '/';        
        

        /*
        //Cargamos modelo de grupo
        include_once($_SESSION['model_path'].'grupo.php');  

        //Arreglo de municipios asociados por grupo                         
        $_SESSION['mun_area_grupo'] = Grupo::munAreaGrupo($id_grupo);
        */
    }

    /**
     * Inicializamos Variables de sesión que contienen rutas
     * **/
    public static function varRutas(){

        //Variables de sesión para ubicarnos en los archivos principales
        list($path, $modulo, $submodulo, $app, $root, $app_path_p) = self::dirPath();      			        		

        $_SESSION['DS'] = DIRECTORY_SEPARATOR;

        //proyecto
        $_SESSION['app_path'] = $path;

        //Nombre del proyecto
        $_SESSION['app_name'] = $app;

        //Nombre del módulo
        $_SESSION['module_name'] = $modulo;				

        //Descripción del Módulo
        $params = array($modulo);

        $mod = self::getInstance()->rawQuery('SELECT descripcion from modulo where nombre = ? limit 1',$params);        
        $mod = ($mod != NULL)? $mod[0] : null;

		    //Descripción del módulo
        $_SESSION['module_desc'] = ($mod == NULL)? 'Sin Descripción' : $mod['descripcion']; 				

        //Ruta del proyecto
        //$_SESSION['app_path_p'] =  self::cambiaChar($app_path_p.$app.'/');
        $_SESSION['app_path_p'] =  preg_replace('/\/\/+/', '/', $app_path_p.'/');				

        //Rutas de raíz
        $_SESSION['app_path_r'] = $root. $_SESSION['DS'] . $app . $_SESSION['DS'];

        //Rutas de css 
        $_SESSION['css_path'] = $_SESSION['app_path_p'].'css/';        

		//Ruta de diversos archivos
        $_SESSION['files_path'] = $root. '/' . $app . '/files/';

		 //Ruta de imágenes
        $_SESSION['img_path'] = (!$_SESSION['app_path_p'])?  $_SESSION['app_path'] . '/img/' : $_SESSION['app_path_p'] . 'img/';

        //Ruta de Modelos
        $_SESSION['model_path'] = $root. '/' . $app . '/model/';

		//Ruta de Inc
        $_SESSION['inc_path'] = $root. '/' . $app . '/inc/';

		//Ruta de Js
        $_SESSION['js_path'] = $_SESSION['app_path_p']. 'js/';        		

        //Ruta del módulo
        $_SESSION['module_path'] = $_SESSION['app_path_p'] . $_SESSION['module_name'] . '/';

        //Ruta del módulo desde raiz
        $_SESSION['module_path_r'] = $root .'/'. $_SESSION['app_path_p'] .'/'. $_SESSION['module_name'] . '/';        
        
        //Ruta absoluta del directorio
        //$_SESSION['dir_path'] = $root. "/" . $app . '/';        

        /*
		    echo '<br/ > Root: '.$root.'<br/>';
		    echo "app_path: " . $path . "<br>";
        echo "app_name: " . $_SESSION['app_name'] . "<br>";
        echo "module_name: " . $_SESSION['module_name'] . "<br>";
        echo "module_desc: " . $_SESSION['module_desc'] . "<br>";        
        echo "app_path_p: ".$_SESSION['app_path_p']."<br>";
		    echo "css_path: " . $_SESSION['css_path'] . "<br>";
        echo "files_path: " . $_SESSION['files_path'] . "<br>";
        echo "img_path: " . $_SESSION['img_path'] . "<br>";
		    echo "model_path: " . $_SESSION['model_path'] . "<br>";
        echo "inc_path: " . $_SESSION['inc_path'] . "<br>";
		    echo "js_path: " . $_SESSION['js_path'] . "<br>";
        echo "module_path: " . $_SESSION['module_path'] . "<br>";
		    //echo "dir_path: ".$_SESSION['dir_path']."<br>";
		    echo 'Función: '.dirname(dirname(dirname(self::dirActualLimpia())));		
        exit;
        */
    }

	function obtener_url_raiz($url) {

		$parte1 = explode("/", $url);
		$count = count($parte1);
		$count_array = $count - 1;

		if ($count >= 4) {

			if ($parte1[$count_array] != '') {

				$path = str_replace($parte1[$count_array], '', $url); 

			}else{ 

				$path = $url; 

			}

		} else {

			if(substr($url,-1) != '/') $url .= '/'; 

			$path = $url; 		

		}

	return $path;

	} 

    /**
     * Obtengo directorio actual 'limpia'
     * recibe: dir/de/arch.php?variable1=1&variable2=2...
     * regresa: dir/de/arch.php
     * @return strin ruta completa de la acción
     * */

    public static function dirActualLimpia()
    {

        $recurso = $_SERVER['REQUEST_URI'];

        /*Obtenemos la dirección de la barra de direcciones
        hasta el signo '?' (ignoraremos los parámetros)*/

        if ($rec = strpos($recurso, '?')) {

            $recurso = substr($recurso, 0, $rec);

        }

        return $recurso;

    }

    /**
     * Validamos si se tiene permiso para ver la ruta actual
     * @return string código javascript
     * */
    public static function validaRuta()
    {

        //Valido si ha iniciado sesión, caso contrario no puede ver nada
        if (isset($_SESSION["login"])) {

            //Obtenemos la ruta actual
            $ruta = explode('/', self::dirActualLimpia());

            //print_r($ruta);

            //Obtenemos cada parte del recurso
            $accion = substr(array_pop($ruta), 0, -4); //quitamos .php
            $submodulo = array_pop($ruta);
            $modulo = array_pop($ruta);

            //Verificamos si tiene acceso a dicha ruta
            $obj = self::accesoAccion($accion, $submodulo, $modulo);

            if ($obj) {

                //query para trer la desripcion de la accion sin duplicados

             $sql = 
             'SELECT 
              a.descripcion
              FROM `accion` a 
              LEFT JOIN submodulo s ON a.id_submodulo = s.id
              WHERE a.nombre = ?  AND s.nombre = ?
              LIMIT 1 ';

            $params =array($accion,$submodulo);

            $des_accion = self::getInstance()->rawQuery($sql, $params);

            //sacamos solo el la primer posición de la arreglo
            $des_accion=$des_accion[0];

            return $des_accion['descripcion']; 

            }else{

                //Regresamos al menú principal

                //echo $accion.' '.$submodulo.' '.$modulo;
                header('Location:' . $_SESSION['module_path'] . 'ini/index.php?r=9');

                //echo '<script language="JavaScript">location.href="'.$_SESSION['module_path'].'index.php"</script>';
                exit;
            }

        } else {
            //Regresamos a inicio para que se logueé
            
            //Obtenemos arreglo con cada uno de los niveles del directorio
            $app_path_p = self::getRoute();

            //Redireccionamos con php
            header('Location:' . $app_path_p . 'login/index.php?r=13');
        }
    }

    /**
     * Nos devuelve la IP real del equipo
     * que actualmente está en el sistema
     * @return string ip del equipo
     * */
    public static function obtenerIP()
    {

        $ip = 'No disponible';

        //Verificamos que la variable $_SERVER exista
        if (isset($_SERVER)) {

            if (isset($_SERVER["HTTP_CLIENT_IP"])) {

                $ip = $_SERVER["HTTP_CLIENT_IP"];

            } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {

                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

            } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {

                $ip = $_SERVER["HTTP_X_FORWARDED"];

            } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {

                $ip = $_SERVER["HTTP_FORWARDED_FOR"];

            } elseif (isset($_SERVER["HTTP_FORWARDED"])) {

                $ip = $_SERVER["HTTP_FORWARDED"];

            } elseif (isset($_SERVER['HTTP_VIA'])) {

                $ip = $_SERVER['HTTP_VIA'];

            } else {

                $ip = $_SERVER["REMOTE_ADDR"];

            }

        } else {

            if (getenv('HTTP_X_FORWARDED_FOR')) {

                $ip = getenv('HTTP_X_FORWARDED_FOR');

            } elseif (getenv('HTTP_CLIENT_IP')) {

                $ip = getenv('HTTP_CLIENT_IP');

            } else {

                $ip = getenv('REMOTE_ADDR');

            }

        }

        return $ip;

    }

    /**
     * Obtenemos un arreglo con las acciones 'hermanas' de una acción y su
     * respectivo menú.
     * @param string $accion Nombre de la acción a buscar
     * @param string $menu Nombre del menú
     * 
     * @return arreglo $arreglo Listado de acciones 'hermanas' de una acción
     *                  y su respectivo menú
     * */   
    public static function arregloMenu($accion,$menu){

        //Obtenemos acciones del menú
        $menu_acciones = Permiso::listaMenuAcciones($menu,$accion);
        $arreglo = array();

        //Llenamos arreglo del menú
        foreach($menu_acciones as $a):
            $arreglo[$a['nombre_accion']] = $a['descripcion_accion'];
        endforeach;

        return $arreglo;
    }

    /**
    *Obtenemos los ciclos escolares disponibles
    *
    *@return Array $ciclos_escolares Arreglo de ciclos disponibles
    **/
    public static function ciclosEscolares(){
      
      //Ciclo Inicio
      $ciclo_inicio = 2007;

      //Arreglo con los ciclos disponibles
      $ciclos_escolares = array();

      //Recorremos ciclo
      while ( $ciclo_inicio <= date('Y')) {
        
        //Incrementamos el ciclo de fin
        $ciclo_fin = $ciclo_inicio + 1;

        //Guardamos valor
        $ciclos_escolares[$ciclo_inicio.'-'.$ciclo_fin] = $ciclo_inicio.'-'.$ciclo_fin;

        //Incrementamos valor
        $ciclo_inicio++;

      }

      $ciclos_escolares = array_reverse($ciclos_escolares);

      /*
      //Consultas para obtener los ciclos y años
      $sql = 'SELECT ciclo_escolar 
              FROM `alim_desayunos_escolares` 
              GROUP BY 1 
              ORDER BY ciclo_escolar desc;';

      //Obtenemos los ciclos escolares disponibles
      $ciclos_escolares = self::getInstance()->query($sql);
      */

      return $ciclos_escolares;

    }

    /**
    *Obtenemos los años de padrón disponibles
    *
    *@return Array $axosPadron Arreglo de ciclos disponibles
    **/
    public static function axosPadron(){
      
      //Ciclo Inicio
      $axo_inicio = 2007;

      //Arreglo con los ciclos disponibles
      $axosPadron = array();

      //Recorremos ciclo
      while ( $axo_inicio <= date('Y')) {
                
        //Guardamos valor
        $axosPadron[$axo_inicio] = $axo_inicio;

        //Incrementamos valor
        $axo_inicio++;

      }

      $axosPadron = array_reverse($axosPadron);

      /*
      $sql = 'SELECT DISTINCT(YEAR(fecha_alta)) AS axo_padron 
              FROM alim_desayunos_escolares 
              WHERE fecha_alta IS NOT NULL ORDER BY fecha_alta desc;';

      //Obtenemos años del padrón
      $axosPadron = self::getInstance()->query($sql);
      */
      return $axosPadron;

    }

  /**
  * Estimación de porcentaje
  *@param $fecha_nac date Fecha de nacimiento
  *@param $fecha_peso date Fecha del pesaje
  *@param $sexo string Sexo
  *@param $peso float Peso
  *
  *@return $resultado string
  **/
  function estimador_pct_ajax($fecha_nac,$fecha_peso, $sexo, $peso){

  //Inclumos librería de fechas
  include_once($_SESSION['inc_path'].'libs/Fechas.php');

  $mes='';
  $pct='';

   if ((strlen($peso) < 0 ) or  ($peso < 0) ){
    exit;
   }
   

  //echo 'Fecha Nac: '.$fecha_nac;
  //echo 'Fecha Peso: '.$fecha_peso;

   $fecha_nac=Fechas::invierte_fecha($fecha_nac);
   $fecha_peso=Fechas::invierte_fecha($fecha_peso);
   
   //echo 'Fecha Nac_: '.$fecha_nac;
   //echo 'Fecha Peso_: '.$fecha_peso;

  $mes = Fechas::dias_entre_fechas_utils($fecha_nac,$fecha_peso);
  $mes=floor($mes/30);
   
  $leyenda='INDEFINIDO';
  $resultado='000';
   
  if ((strlen($fecha_nac)<=0) or ( strlen($peso)<=0) or (strlen($fecha_peso)<=0) ){
    $color[0]=167;
    $color[1]=44;
    $color[1]=192;
    $resultado=$color;
    return $resultado;
    exit;
  }
    
   

  if ( ($mes > 60)) {
    $color[0]=167;
    $color[1]=44;
    $color[2]=192;
    $resultado=$color;
    return $resultado;
    exit;
  }

  $sql='SELECT * from pct_adecuacion where mes= ? and sexo = ?';
  $params = array($mes, $sexo);
  $adecuacion = self::getInstance()->rawQuery($sql,$params);
  $adecuacion = $adecuacion[0];

     if ($peso<$adecuacion["des_est_men2"])
      {
       $color[0]=255;
       $color[1]=16;
       $color[2]=0;
       $resultado=$color;
       return $resultado;   
       exit;
      }

     if (($peso>=$adecuacion['des_est_men2']) and  ($peso<$adecuacion['des_est_men1']))
      {
       $color[0]=255;
       $color[1]=255;
       $color[2]=117;
       $resultado=$color;
       return $resultado; 
       exit;
      }
     
     if (($peso>=$adecuacion['des_est_men1']) and  ($peso<$adecuacion['des_est1']))
      {
       $color[0]=0;
       $color[1]=196;
       $color[2]=0;
       $resultado=$color;
      return $resultado; 
       exit;
      }

    if (($peso>=$adecuacion['des_est1']) and  ($peso<$adecuacion['des_est2']))
      {
       $color[0]=254;
       $color[1]=171;
       $color[2]=175;
       $resultado=$color;
       return $resultado; 
       exit;
      }
    
     if ($peso >= $adecuacion['des_est2'])
     {
     $color[0]=241;
       $color[1]=163;
       $color[2]=19;
       $resultado=$color;
       return $resultado; 
       exit;  
     }

 }

    /**
     *Función para encriptar clave 
     *@param string $clave
     * 
     * @return string variable encriptada
     * */      
    public static function encripta($clave){

        $semilla = 'jaliscodif2013';
        return md5($clave.$semilla);
    }
    
    public static function procesa_tel($tel_excel){
        
        $telefono ='';
        $remplazar='';
        $quitar=array('-','(',')','.',',');
       
        $numeros = array('0','1','2','3','4','5','6','7','8','9');
        
        
        $arreglo_tel = explode(' ',$tel_excel);
        
        foreach($arreglo_tel as $Key => $value ):
        $substraer = substr($value,0,1);
        
        //echo '<br>caracter= '.$substraer.' valor entero '.$valor_entero.' Es entero '.$es_Entero;
        
        if(in_array($substraer, $numeros)){
            
            $telefono.=str_replace($quitar,$remplazar,$value).' ';
           
        }
        
        endforeach;
      
        return $telefono;
        
        }
    /** 
     * Estimamos porcentajes en nutricion extrescolar
    */
    
      public static function estimador_pct($fecha_nac,$fecha_peso, $sexo, $peso){
      include_once($_SESSION['inc_path'].'libs/Fechas.php');
      $fecha_nac=Fechas::invierte_fecha($fecha_nac);
      $fecha_peso=Fechas::invierte_fecha($fecha_peso);
    //echo $fecha_nac.'<br>';
    //echo $fecha_peso.'<br>';
    //echo $sexo.'<br>';
    //echo $peso.'<br>';
    //exit;
    $mes='';
    $pct='';
    
     if ((strlen($peso) < 0 ) or  ($peso < 0) ){
       exit;  
     }
    
    //echo alerta($fecha_nac);
    //echo alerta($fecha_peso);
    //echo $fecha_nac;
    //echo  $fecha_peso;
    
     
    //exit; 
     $mes = Fechas::dias_entre_fechas_utils($fecha_nac,$fecha_peso);
     $mes=floor($mes/30);
     
     
     
     
      $leyenda='INDEFINIDO';
      $resultado='INDEFINIDO';
     
    
      if ((strlen($fecha_nac)<=0) || ( strlen($peso)<=0) || (strlen($fecha_peso)<=0) ){
      $resultado='INDEFINIDO';
      return $resultado;
      exit;
      }
      
     
    
     if ( ($mes > 60)) {
      $resultado='INDEFINIDO';
      return $resultado;
      exit;
     }
    
    
      $sql='select * from pct_adecuacion 
      where mes = ? and sexo = ?';
            
      $params = array($mes,$sexo);
      $row = self::getInstance()->rawQuery($sql,$params);
      $row = $row[0];
      
     
      //print_r($row);
      //echo 'peso '.$peso;
      //exit; 
       if ($peso<$row["des_est_men2"])
        {
         $resultado='DESNUTRIDO';
         $leyenda='DESNUTRIDO';
         return $resultado;
         exit;
        }
    
       if (($peso>=$row['des_est_men2']) and  ($peso<$row['des_est_men1']))
        {
         $resultado='RIESGO';
         $leyenda='RIESGO';
         return $resultado;
         exit;
        }
    
       if (($peso>=$row['des_est_men1']) and  ($peso<$row['des_est1']))
        {
         $resultado='NORMAL';
         $leyenda='NORMAL';
         return $resultado;
         exit;
        }
    
      if (($peso>=$row['des_est1']) and  ($peso<$row['des_est2']))
        {
         $resultado='SOBREPESO';
         $leyenda='SOBREPESO';
         return $resultado;
         exit;
        }
      
       if ($peso >= $row['des_est2'])
       {
    	 $resultado='OBESIDAD';
         $leyenda='OBESIDAD';
         return $resultado;
         exit;  
       }
    
    return    $pct2;
}
    /**
     * Buscamos un nombre en el web service
     * @param  varchar $nombres Nombres propios
     * @param  varchar $paterno Apellido Paterno
     * @param  varchar $materno Apellido Materno
     * @return Object  Objeto con los datos
     */
    public static function buscaNombreWS($nombres=null,$paterno=null,$materno=null){

        $direccion = "https://apps.padronunico.jalisco.gob.mx:8080/WsTisa/WSConsultaPU.svc?wsdl";
       
        try {

            $client = new SoapClient($direccion);
                  
           /* $fcs = $client->__getFunctions();
            print_r($fcs);
            exit;
           */
           
            $obj = array('user' => 'DIFweb',
                         'pw' => '53hn5uch7',
                         'apaterno'  => $paterno ,
                         'nombre'  => $nombres ,
                         'amaterno'  =>  $materno);

            $fcs = $client->BuscaBeneficiario($obj);
            $resultado = $fcs->BuscaBeneficiarioResult;
            
            //echo '<br><br><br>';
            //echo $resultado->Mensaje." ". $resultado->Resultado;
            
            $datos = $resultado->beneficiarios;
            //print_r($datos);      
            //echo '<br><br><br>';
            
            //print_r($datos->Beneficiario2);
            //echo 'CURP:'.$datos->Beneficiario2->Curp;
            
            $beneficario = $datos->Beneficiario2;
            

        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $beneficario;

    }

    /**
     * Convertimos una imagen a PNG
     * @param  string  $srcFile Ruta de la imagen
     * @param  integer $maxSize Tamaño Máximo
     * @return [type]           [description]
     */
    function imageToPng($srcFile, $destFile = NULL, $maxSizeW = 300,$maxSizeH = 200) 
    {  
        
        // Output to a temp file
        if($destFile == NULL){
            $destFile = tempnam('files','image');
        }        

        list($width_orig, $height_orig, $type) = getimagesize($srcFile);        
     
        // Get the aspect ratio
        $ratio_orig = $width_orig / $height_orig;
     
        $width  = $maxSizeW; 
        $height = $maxSizeH;
     
        // resize to height (original is portrait) 
        if ($ratio_orig < 1) {
            $width = $height * $ratio_orig;
        } 
        // resize to width (original is landscape)
        else {
            $height = $width / $ratio_orig;
        }
     
        // Temporarily bump up the memory limit to allow for larger images
        ini_set('memory_limit', '32M'); 
     
        switch ($type) 
        {
            case IMAGETYPE_GIF: 
                $image = imagecreatefromgif($srcFile); 
                break;   
            case IMAGETYPE_JPEG:  
                $image = imagecreatefromjpeg($srcFile); 
                break;   
            case IMAGETYPE_PNG:  
                $image = imagecreatefrompng($srcFile);
                break; 
            default:
                throw new Exception('Tipo de imagen no reconocido ' . $type);
        }
     
        // create a new blank image
        $newImage = imagecreatetruecolor($width, $height);
     
        // Copy the old image to the new image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        
        //Create image        
        imagepng($newImage, $destFile);  
     
        // Free memory                           
        imagedestroy($newImage);
     
        if ( is_file($destFile) ) {
            $f = fopen($destFile, 'rb');   
            $data = fread($f,filesize($destFile));       
            fclose($f);
     
            // Remove the tempfile
            //unlink($destFile);    
            return $data;
        }
     
        throw new Exception('Image conversion failed.');
    }

}