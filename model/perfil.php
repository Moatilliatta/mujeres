<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla perfil
 * **/ 

//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Perfil extends MysqliDb{

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
     * Cambiamos el estatus del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaPerfil($id_perfil){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla grupo
        $sql = 'SELECT activo from `perfil` where id = ?'; 
        $params = array($id_perfil);

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
        self::getInstance()->where('id',$id_perfil);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('perfil',$updateData)){
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
     * Obtenemos listado de los perfiles. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param $busqueda Cadena a buscar 
     * @param $tipo_filtro Filtro (campo) con el que realizaremos la búsqueda
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function listaPerfiles($busqueda = NULL,$tipo_filtro=Null, $activo = NULL){

        $sql = 
        'SELECT
        p.*,
        u.usuario,
        e.nombre as estatus
        FROM `perfil` p
        left join usuario u on u.id = p.id_usuario_creador
        left join estatus e on e.valor = p.activo
        where ?
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda
       if ($busqueda !==NULL && $tipo_filtro !==NULL){

           //switch($tipo_filtro){
               //case :
                $sql .= ' AND p.'.$tipo_filtro.' LIKE ? ';
                $params[] = '%'.$busqueda.'%';        
                   //break;
           //}
       }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= 'and p.activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
        return Paginador::paginar($sql,$params);          
    }

    /**
     * Guardamos registro en la tabla perfil
     * @param array $perfil Arreglo con los campos a guardar
     * @param int $id del perfil a editar (opcional)
     * 
     * @return int No. de mensaje
     * */

    public static function savePerfil($perfil,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($perfil as $key => $p):
        ${$key} = self::getInstance()->real_escape_string($p);
        endforeach;
        
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `perfil` where nombre = ? ';
        $params = array($nombre);
        
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
                if ($nombre){
                
                include_once($_SESSION['inc_path'].'libs/Permiso.php');
                
                    $insertData = array(
                        'nombre' => mb_strtoupper($nombre, "UTF-8"),
                        'activo' => $activo,
                        'id_usuario_creador' => $id_usuario_creador,
                        'fecha_creado' => date('Y-m-d H:i:s')
                        );
                        
                        //Quitamos del arreglo los valores vacíos
                        //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));                        //Si recibimos id para editar
                        
                        if(intval($id)>0){
                            //Indicamos que haremos un update
                            $accion = 'update';
        
                            //Agregamos condición para indicar qué id se actualiza
                            self::getInstance()->where('id',$id);                                        
                        }
                        
                        //Iniciamos transacción
                        self::getInstance()->startTransaction();
                        
                        if(! self::getInstance()->{$accion}('perfil', $insertData)){
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
                
        }        

        return $msg_no;
    }

}
?>