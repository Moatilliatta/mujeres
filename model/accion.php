<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Acción
 * **/ 

//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Accion extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
        
    /**
     * Cambiamos el estatus del submódulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_submodulo Submódulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */

    public static function activaAccion($id_accion){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `accion` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_accion);                

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
        self::getInstance()->where('id',$id_accion);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('accion',$updateData)){
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
     * Obtenemos listado de las Acciones 
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */

    public static function listaAccion($busqueda=NULL,$tipo_filtro=NULL,$activo = NULL){

        //if($tipo_filtro != NULL){

        $sql = '
        SELECT
        a.*,
        u.usuario,
        sm.descripcion as submodulo,
        m.descripcion as modulo,
        e.nombre as estatus
        FROM `accion` a
        left join usuario u on u.id = a.id_usuario_creador
        left join submodulo sm on sm.id = a.id_submodulo
        left join modulo m on m.id = sm.id_modulo
        left join estatus e on e.valor = a.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){
            
            if($tipo_filtro == 'modulo') {
                $alias='m';
                $tipo_filtro='descripcion';
            }elseif($tipo_filtro == 'submodulo'){
                $alias='sm';
                 $tipo_filtro='descripcion';
                
            }else{
                $alias='a';                
            }

            //switch($tipo_filtro){

                //case :

                 $sql .=  ' AND '.$alias.'.'.$tipo_filtro.' LIKE ? ';

                 $params[] = '%'.$busqueda.'%';       

                    //break;
            //}
        }

        if($busqueda == NULL){
            $sql .= " order by a.id desc";
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND a.activo = ?';
            $params[] = $activo;
        }

        //echo $sql;

        //Regresamos resultado
        // self::executar($sql,$params);

        return Paginador::paginar($sql,$params);

        /*
        }else{
            return NULL;
        }
        */
    }

    /**
     * Guardamos registro en la tabla Submódulo
     * @param array $submodulo Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveAccion($accion,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($accion as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;
                                
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `accion` where nombre = ? and id_submodulo = ? ';
        $params = array($nombre,$id_submodulo);
        
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
                $insertData = array(
                'id_submodulo' => $id_submodulo,
                'nombre' => strtolower($nombre),
                'descripcion' => mb_strtoupper($descripcion, "UTF-8"),
                'activo' => $activo,
                'id_usuario_creador' => $id_usuario_creador,
                'orden' => $ordent,
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
                
                if(! self::getInstance()->{$db_accion}('accion', $insertData)){
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
}