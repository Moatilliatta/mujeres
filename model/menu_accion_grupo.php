<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de menu_accion_grupo
 * **/ 

//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class MenuAccionGrupo extends MysqliDb{

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

    public static function activaMenuAccionGrupo($id_menu_accion_grupo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `menu_accion_grupo` where id = ?'; 

        $params = array($id_menu_accion_grupo);

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
        self::getInstance()->where('id',$id_menu_accion_grupo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        if(!self::getInstance()->update('menu_accion_grupo',$updateData)){

            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 

        }else{
            //Campos guardados correctamente
            $msg_no = 1;                    
        } 

        return $msg_no;
    } 


    /**
     * Obtenemos listado de los menus que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_menu id de la tabla menú
     * @param int $id_grupo id de la tabla grupo
     * @param int id_accion id de la tabla acción
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function listaMenuAccionGrupo($id_menu = NULL,$id_grupo=NULL,
        $id_accion= NULL,$activo = NULL){

        $sql = 
        'SELECT
         a.nombre as nombre_accion,
         a.descripcion as desc_accion,
         s.descripcion as desc_submodulo,
         mo.descripcion as desc_modulo,
         g.nombre as nombre_grupo,
         m.nombre as nombre_menu,
         ag.id as id_accion_grupo,
         mag.id as id_menu_accion_grupo
         FROM `menu_accion_grupo` mag
         left join menu m on m.id = mag.id_menu and m.activo = 1
         left join accion_grupo ag on ag.id= mag.id_accion_grupo and ag.activo = 1
         left JOIN accion a ON a.id=ag.id_accion AND a.activo =1
         left join submodulo s on s.id = a.id_submodulo and s.activo = 1
         left join modulo mo on mo.id = s.id_modulo and m.activo = 1
         left JOIN grupo g ON g.id=ag.id_grupo AND g.activo =1
         where mag.activo = ? ';        

        //Parámetros de la sentencia
        $params = array(1);
          
        
        if($id_grupo !== NULL){
            $sql .= ' AND g.id = ?';
            $params[] = $id_grupo;
        }
        if($id_menu !== NULL){
            $sql .= ' AND m.id = ?';
            $params[] = $id_menu;
        }
        if($id_accion !== NULL){
            $sql .= ' AND a.id = ?';
            $params[] = $id_accion;
        }
        
        //Regresamos resultado
        return Paginador::paginar($sql,$params);
        //return self::executar($sql,$params);           
    }

    /**
     * Guardamos registro en la tabla usuario_grupo
     * @param int $id_accion_grupo id de la tabla accion_grupo
     * @param int $id del usuario_grupo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
     public static function saveMenuAccionGrupo($menu_accion_grupo,$id=null)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

         /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($menu_accion_grupo as $key => $g):
        ${$key} = self::getInstance()->real_escape_string($g);
        endforeach;

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];              

        //Campos obligatorios
        if ($id_accion_grupo && $id_menu) 
        {
                $insertData = array(
                'id_accion_grupo' => $id_accion_grupo,
                'id_menu' => $id_menu,
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
                
                if(! self::getInstance()->{$accion}('menu_accion_grupo', $insertData)){
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

        return $msg_no;
    }

}
?>