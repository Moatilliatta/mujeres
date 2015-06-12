<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Submodulo
 * **/ 
//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Submodulo extends MysqliDb{

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
     * Cambiamos el estatus del submódulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_submodulo Submódulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaSubmodulo($id_submodulo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `submodulo` where id = ?'; 
        
        //parámetros para la consulta
        $params = array($id_submodulo);                

        //Verificamos el estatus del Modulo        
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
        self::getInstance()->where('id',$id_submodulo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('submodulo',$updateData)){
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
     * Obtenemos listado de los Submódulos. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param $busqueda Cadena a buscar 
     * @param $tipo_filtro Filtro (campo) con el que realizaremos la búsqueda
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function listaSubmodulos($busqueda = NULL,$tipo_filtro=Null, $activo = NULL){

        $sql = 
        'SELECT
        s.*,
        u.usuario,
        m.descripcion as nombre_modulo,
        e.nombre as estatus
        FROM `submodulo` s
        left join usuario u on u.id = s.id_usuario_creador
        left join modulo m on m.id= s.id_modulo
        left join estatus e on e.valor = s.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(1);
         if ($busqueda !==NULL && $tipo_filtro !==NULL){
           
           //switch($tipo_filtro){
            
            if($tipo_filtro == 'modulo'){
                $alias='m';
                $tipo_filtro='descripcion';
            }else{
                $alias='s';                
            }
               
               //case :
                $sql .= ' AND '.$alias.'.'.$tipo_filtro.' LIKE ? ';
                $params[] = '%'.$busqueda.'%';        
                   //break;
           //}
                                               
       }

        if($busqueda == NULL){
            $sql .= " order by s.id desc";
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND s.activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
      return Paginador::paginar($sql,$params);             
    }


    /**
     * Guardamos registro en la tabla Submódulo
     * @param array $submodulo Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */

    public static function saveSubmodulo($submodulo,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($submodulo as $key => $value):

        ${$key} = self::getInstance()->real_escape_string($value);

        endforeach;
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `submodulo` where nombre = ? and id_modulo = ? ';
        $params = array($nombre,$id_modulo);
        
        //Sólo si se edita el mismo registro puede 'repetir el nombre'
        if($id !=null){
            $sql.=' and id not in (?)';
            $params[] = $id;
            
        }
                                
        //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){
            $msg_no = 6;
            //Nombre duplicado
        }else{
                        
            //Obtenemos el id del usuario creador
            $id_usuario_creador = $_SESSION['usr_id'];
            
            /*Si no esta creada la variable activo 
            predeterminadamente la guardamos = 1*/        
            if(!isset($activo) ){
                $activo = 1 ;            
            }        
            
            //Campos obligatorios
            if ($nombre && $descripcion) 
            {   
                include_once($_SESSION['inc_path'].'libs/Permiso.php');
                
                $insertData = array(
                'id_modulo' => $id_modulo,
                'nombre' => strtolower($nombre),
                'descripcion' => mb_strtoupper($descripcion, "UTF-8"),
                'activo' => $activo,
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
                
                if(! self::getInstance()->{$accion}('submodulo', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                    
                    //Cancelamos los posibles campos afectados en la tabla
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
        
        }        
        
        return $msg_no;
        
    }
}
?>