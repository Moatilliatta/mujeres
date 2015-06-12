<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla CallcenterGrupoOperador
 * **/ 

//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

class CallcenterGrupoOperador extends MysqliDb{
//tenemos que crear un constructor vacio por q hay variables
//a inicializar solo funciones pra cada model de clase 
//si no tomaria los valores del constructor de MysqliDb 
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
    public static function ultimoQuery(){
      //Ejecutamos
      $resultado = "Última Sentencia: ".self::getInstance()->getLastQuery();
      
      //Regresamos resultado
      return $resultado;
    }

    /**
     * Cambiamos el estatus del submódulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_submodulo Submódulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */

    public static function activaCallcenterGrupoOperador($id_callcenter_grupo_operador){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;        

        //Verificamos el estatus del Modulo        
        $registro = self::get_by_id($id_callcenter_grupo_operador);

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;
        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;
        }

        //Preparamos update
        self::getInstance()->where('id',$id_callcenter_grupo_operador);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('callcenter_grupo_operador',$updateData)){
            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 
            
            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback();
                        
        }else{
            //Campos guardados correctamente
            $msg_no = 1;           
            
            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();         
        } 

        return $msg_no;
    } 

    /**
    * Obtenemos los datos del aspirante por su id de entrevista
    *@param int $id_mujeres_avanzando id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function get_by_id($id_callcenter_grupo){

        $datos = self::getInstance()->where('id', $id_callcenter_grupo)
                                    ->getOne('callcenter_grupo_operador');

        return $datos;
    }

    /**
     * Buscamos un operador (y su grupo relacionado)
     * @param  [type] $id_usuario [description]
     * @param  [type] $id_grupo   [description]
     * @return [type]             [description]
     */
    public function buscaOperador($id_usuario,$id_callcenter_grupo,$activo = null){
        
        self::getInstance()->where('id_usuario', $id_usuario)
                                  ->where('id_callcenter_grupo', $id_callcenter_grupo);
        if($activo != NULL){
         self::getInstance()->where('activo', $activo );
        }
        
        $usr = self::getInstance()->getOne('callcenter_grupo_operador');

        return $usr;
    }

    public static function saveCallcenterGrupoOperador($CallcenterGrupoOperador,$id = null){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($CallcenterGrupoOperador as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;

        //Evitamos duplicidad de registros        
        $obj = self::buscaOperador($id_usuario,$id_callcenter_grupo);
        
        if($obj != null){            

            if($obj){
                $id_callcenter_grupo_operador = $obj["id"];            
                $msg_no = self::activaCallcenterGrupoOperador($id_callcenter_grupo_operador);
            }
            
        }else{

            //Obtenemos el id del usuario creador
            $id_usuario_creador = $_SESSION['usr_id'];
                
            /*Si no esta creada la variable activo 
            predeterminadamente la guardamos = 1*/
            if(!isset($activo) ){
                $activo = 1 ;            
            }        
                
            //Campos obligatorios
            if ($id_callcenter_grupo && $id_usuario){

                $insertData = array(
                'id_callcenter_grupo' => $id_callcenter_grupo,
                'id_usuario' => $id_usuario,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
                );                
                    
                    //Quitamos del arreglo los valores vacíos
                    //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
                    
                    //Si recibimos id para editar
                    if(intval($id)>0){
                        //Indicamos que haremos un update
                        $db_accion = 'update';
        
                        //Agregamos condición para indicar qué id se actualiza
                        self::getInstance()->where('id',$id);                                        
                    }
                    
                    //Iniciamos transacción
                    self::getInstance()->startTransaction();
                    
                    if(! self::getInstance()->{$db_accion}('callcenter_grupo_operador', $insertData)){
                        //'Error al guardar, NO se guardo el registro'
                        $msg_no = 3; 
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();
                        
                        }else{
                        //Campos guardados correctamente
                        $msg_no = 1;     
                        
                        //Guardamos campos afectados en la tabla
                        self::getInstance()->commit();               
                        } 
        
                }else{
                
                //'Campos Incompletos'
                $msg_no = 2;             
                
                }        

        }                
        
        return $msg_no;        
    }

    /**
     * Listado de operadores en determinado grupo
     * @param  integer $id_callcenter_grupo [description]
     * @return [type]                       [description]
     */
    public static function listado_operador($id_callcenter_grupo = 0){
        
        $sql = "SELECT 
                cgo.id,
                cg.id as id_callcenter_grupo,
                cg.nombre as grupo,
                cgo.activo,
                CONCAT(u.nombres,?,u.paterno,?,ifnull(u.materno,?)) as nombre_completo
                FROM `callcenter_grupo_operador` cgo
                LEFT JOIN usuario u on u.id = cgo.id_usuario
                LEFT JOIN callcenter_grupo cg on cg.id = cgo.id_callcenter_grupo 
                WHERE 1 ";

        $params = array(' ',' ','');

       //Sólo si se edita el mismo registro puede 'repetir el aspirante'
        if($id_callcenter_grupo > 0){
            $sql.=' AND cg.id = ? ';
            //$sql.=' and SOUNDEX(a.materno) = SOUNDEX(?) ';
            $params[] = $id_callcenter_grupo;
        }                          

        /*
        echo $sql;
        print_r($params);
        */
        
        //Ejecutamos sentencia
        return self::getInstance()->rawQuery($sql,$params);
    }

    public static function listaOpDisp($id_callcenter_grupo = 0){
        
        $sql=
        'SELECT 
        u.id,
        CONCAT(u.nombres,?,u.paterno,?,ifnull(u.materno,?)) as nombre_completo
        FROM usuario_grupo ug
        LEFT JOIN usuario u on u.id = ug.id_usuario
        where 1 
        and ug.id_grupo = 13
        and u.id NOT IN (
        SELECT cgo.id_usuario
        FROM callcenter_grupo_operador cgo
        INNER JOIN callcenter_grupo cg on cg.id = cgo.id_callcenter_grupo
        and cgo.id_callcenter_grupo = ?
        and cgo.activo = 1
        ) ';
        
        //Parámetros de la sentencia
        $params = array(' ',' ','',$id_callcenter_grupo);

        return self::executar($sql,$params); 
        
    }

}