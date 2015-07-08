<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario
 * **/ 
include_once($_SESSION['inc_path'].'libs/Paginador.php');
class UsuarioGrupo extends MysqliDb
{

    public function __construct(){}

    /**
     * Ejecutamos sentencia sql con parámetros
     * @param string $sql Sentencia SQL
     * @param array $params Cada uno de los parámetros de la sentencia
     * 
     * @return int Resultado
     * */
    private static function executar($sql,$params){
        //Ejecutamos
        $resultado = self::getInstance()->rawQuery($sql, $params);

        //Regresamos resultado
        return $resultado;        
    }

    /**
     * Obtenemos la última sentencia ejecutada
     * @return string $sql con parámetros
     */
    private static function ultimoQuery(){
      //Ejecutamos
      $resultado = "Última Sentencia: ".self::getInstance()->getLastQuery();
      
      //Regresamos resultado
      return $resultado;
    }
    
    /**
     * Cambiamos el estatus del grupo del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario_grupo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */ 
    public static function activaUsuarioGrupo($id_usuario_grupo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `usuario_grupo` where id = ?'; 

        $params = array($id_usuario_grupo);

        //Verificamos el estatus del usuario        
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;
        }else if($registro['activo'] == 1){
            
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;
        }

        //Preparamos update
        self::getInstance()->where('id',$id_usuario_grupo);
                                                        
        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('usuario_grupo',$updateData)){
            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 
            
            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback(); 
            
        }else{
            //Campos guardados correctamente
            $msg_no = 1;    
            
            //Guardamos los campos afectados en la tabla
            self::getInstance()->commit();                
        } 
        return $msg_no;
    } 


    /**
     * Obtenemos listado de los grupos que NO tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaFiltraGrupo($id_usuario,$activo = NULL){

        $sql = 
        'SELECT id, nombre FROM `grupo` where id not in (        
            SELECT
            g.id 
            FROM `usuario_grupo` ug
            inner join usuario u on u.id = ug.id_usuario and u.activo = 1
            inner join  grupo g on g.id= ug.id_grupo and g.activo = 1
            where ug.activo= 1 and ug.id_usuario= ? group by 1
        )';        

        //Parámetros de la sentencia
        $params = array($id_usuario);

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
        return self::executar($sql,$params);           
    }

    /**
     * Creamos consulta y parámetros delos grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Consulta y parámetros de la consulta
     * */
    private static function listadoGruposUsuario($id_usuario,$activo = NULL){
        
        $sql = 
        'SELECT
         g.nombre,
         g.id,
         ug.id as id_usuario_grupo
         FROM `usuario_grupo` ug
         inner join usuario u on u.id = ug.id_usuario and u.activo = 1
         inner join  grupo g on g.id= ug.id_grupo and g.activo = 1
         where ug.id_usuario = ? ';

        //Parámetros de la sentencia
        $params = array($id_usuario);

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND ug.activo = ?';
            $params[] = $activo;
        }

        return array($sql,$params);

    }

     /**
     * Obtenemos listado (arreglo) de los grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Listado de grupos de un usuario en un arreglo
     * */
    public static function listUsuarioGrupo($id_usuario,$activo = NULL){

        //Obtenemos consulta y parámetros para realizar la consulta
        list($sql,$params) = self::listadoGruposUsuario($id_usuario,$activo);

        //Regresamos resultado
        return self::executar($sql,$params);              
    }

    /**
     * Obtenemos listado PAGINADO de los grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaUsuarioGrupo($id_usuario,$activo = NULL){

        //Obtenemos consulta y parámetros para realizar la consulta
        list($sql,$params) = self::listadoGruposUsuario($id_usuario,$activo);

        //Regresamos resultado
        return Paginador::paginar($sql,$params);           
    }


    /**
     * Guardamos registro en la tabla usuario_grupo
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario_grupo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveUsuarioGrupo($id_grupo,$id_usuario,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Falta validar SQL injection 
        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];              

        //Campos obligatorios
        if ($id_grupo && $id_usuario ) 
        {
                $insertData = array(
                'id_grupo' => $id_grupo,
                'id_usuario' => $id_usuario,
                'activo' => 1,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
                );
                
                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
                
                //Si recibimos id para editar
                if(intval($id)>0){
                    //Indicamos que haremos un update
                    $accion = 'update';

                    //Agregamos condición para indicar qué id se actualiza
                    self::getInstance()->where('id',$id);                                        
                }
                
                //Iniciamos transacción
                self::getInstance()->startTransaction();
                
                if(! self::getInstance()->{$accion}('usuario_grupo', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3;
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback(); 
                     
                }else{
                    //Campos guardados correctamente
                    $msg_no = 1;     
                    
                    //Guardamos los campos afectados en la tabla
                    self::getInstance()->commit();                 
                }             
        }else{
            //'Campos Incompletos'
            $msg_no = 2;             
        }
        return $msg_no;
    }

    /**
     * Obtenemos un listado de los grupos a los que pertenece un usuario
     * @param  int $id_usuario ID de la tabla Usuario
     * @return array $array Arreglo con los grupos del usuario             
     */
    public static function gruposUsr($id_usuario){
        //Permisos de Admin
        $array = UsuarioGrupo::listUsuarioGrupo($id_usuario);
        $array = $array[0];

        return $array;
    }
       /**
     * Obtenemos un listado de los Usuarios que pertenece a un grupo
     * @param  int $id_grupo 
     * @return array $array Arreglo con los usuarios de un grupo             
     */
    public static function UsuariosGrupoGenerica($id_grupo = null){
    
        $sql=
        'SELECT
        u.id,
        u.nombres,
        u.paterno,
        u.materno,
        concat(ifnull(u.nombres,?),?,ifnull(u.paterno,?),?,ifnull(u.materno,?)) as nombre_completo,
        g.nombre as grupo
        FROM `usuario_grupo` ug
        LEFT JOIN grupo g on g.id = ug.id_grupo
        LEFT JOIN usuario u on u.id = ug.id_usuario
        where 1 ';
        
        //Parámetros de la sentencia
        $params = array('',' ','',' ','');
        
        if($id_grupo !=null){
            $sql .= ' AND ug.id_grupo = ?';
            $params[] = $id_grupo;
        }
                
        return self::executar($sql,$params); 
    
    }    

}