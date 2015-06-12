<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de accion_grupo
 * **/ 

//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos modelo de menu_accion_grupo
include_once($_SESSION['model_path'].'menu_accion_grupo.php');

class AccionGrupo extends MysqliDb{

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
     * Cambiamos el estatus del grupo del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_accion_grupo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */ 

    public static function activaAccionGrupo($id_accion_grupo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `accion_grupo` where id = ?'; 

        $params = array($id_accion_grupo);

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
        self::getInstance()->where('id',$id_accion_grupo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('accion_grupo',$updateData)){

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
     * Creamos consulta y parámetros de las acciones ligadas a un grupo       
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    private static function listAccionGrupo($id_grupo,$no_id_menu=null,$activo = NULL){
        
        $sql = 
        'SELECT
         ag.id,
         a.nombre as nombre_accion,
         a.descripcion as desc_accion,
         s.descripcion as desc_submodulo,
         mo.descripcion as desc_modulo,
         g.nombre as nombre_grupo,
         IFNULL(m.nombre,?) as nombre_menu,
         m.id as id_menu,
         mag.id as id_menu_accion_grupo
         FROM `accion_grupo` ag
         inner join grupo g on g.id = ag.id_grupo and g.activo = 1
         inner join accion a on a.id = ag.id_accion and a.activo = 1
         inner join submodulo s on s.id = a.id_submodulo and s.activo = 1
         left join modulo mo on mo.id = s.id_modulo and mo.activo = 1
         left JOIN menu_accion_grupo mag on mag.id_accion_grupo = ag.id AND mag.activo = 1 ';
        
        //Parámetros de la sentencia
        $params = array(utf8_encode('Sin Menú Asociado'));
        
        if($no_id_menu){
            $sql .= ' AND mag.id_menu not in ( ? ) ';
            $params[] = $no_id_menu;
        }         
        
        $sql .= ' LEFT JOIN menu m on mag.id_menu = m.id 
         where ag.activo = 1 and ag.id_grupo = ? ';        

        //Parámetros de la sentencia
        $params[] = $id_grupo;

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND ag.activo = ?';
            $params[] = $activo;
        }
        //Verificamos si se quieren filtrar los activos/inactivos
        if($no_id_menu !== NULL){
            $sql .= ' AND ag.id NOT IN 
            ( SELECT id_accion_grupo FROM `menu_accion_grupo` WHERE id_menu = ? and activo = 1)';
            $params[] = $no_id_menu;
        }
        
        
        //echo $sql;
        //var_dump($params);
        //exit;
        
        
        return array($sql,$params);

    }

     /**
     * Obtenemos listado SIN PAGINAR de las acciones ligadas a un grupo       
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function listadoAccionGrupo($id_grupo,$no_id_menu=null,$activo = NULL){

        //Obtenemos consulta y parámetros
        list($sql,$params) = self::listAccionGrupo($id_grupo,$no_id_menu,$activo);

        //Regresamos resultado
        return self::executar($sql,$params);
    }

    /**
     * Obtenemos listado paginado de las acciones ligadas a un grupo       
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function listaAccionGrupo($id_grupo,$no_id_menu=null,$activo = NULL){

        //Obtenemos consulta y parámetros
        list($sql,$params) = self::listAccionGrupo($id_grupo,$no_id_menu,$activo);
        
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
     public static function saveAccionGrupo($id_grupo,$id_accion,$id = null,$id_menu = NULL)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];              

        //Campos obligatorios
        if ($id_grupo && $id_accion ) 
        {
                $insertData = array(
                'id_grupo' => $id_grupo,
                'id_accion' => $id_accion,
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
                
                if(! self::getInstance()->{$accion}('accion_grupo', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();
                    
                }else{
                    //obtenemos el id recién insertado
                    $id_accion_grupo = self::getInstance()->getInsertId();
                    
                    //Campos guardados correctamente
                    $msg_no = 1;             
                    
                    //Guardamos campos afectados en la tabla
                    self::getInstance()->commit(); 
                    
                    //Si se nos envía el id_menu, actualizamos en la tabla menu_accion_grupo
                    if(intval($id_menu) > 0){                        
                        
                        //Arreglo con los datos a guardar
                        $menu_accion_grupo = array('id_accion_grupo' => $id_accion_grupo,
                                                   'id_menu' => $id_menu);

                        //Insertamos registro 
                        $msg_no = MenuAccionGrupo::saveMenuAccionGrupo($menu_accion_grupo);
                
                    }
                           
                }             

        }else{
            //'Campos Incompletos'
            $msg_no = 2;             
        }

        return $msg_no;
    }

}
?>
