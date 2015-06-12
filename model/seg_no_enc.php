<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Acción
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');

class SegNoEnc extends MysqliDb{
//oooommmmaarrr
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
    
     public static function listaNoencontradoGenerico($cadena){
        
        $sql = '
        SELECT
        sn.folio, 
        sn.id,
        sn.nombres,
        sn.paterno,
        sn.materno,
        concat(ifnull(sn.nombres,?),?,ifnull(sn.paterno,?),?,ifnull(sn.materno,?)) as nombre_completo
        from seg_no_enc sn
        where sn.id in ('.$cadena.')
    ';
        $params = array('',' ','',' ','');
        return array($sql,$params);
        
    }
    
    
     public static function listadoArray($cadena){

        list($sql,$params) = self::listaNoencontradoGenerico($cadena);

        //Regresamos resultado
        return  self::executar($sql,$params);
    }

    /**
     * Obtenemos listado de mujeres
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */
    public static function listadoPaginado($cadena)
    {
        list($sql,$params) = self::listaNoencontradoGenerico($cadena);

        return Paginador::paginar($sql,$params);           
    
    }

    /**
     * Guardamos registro en la tabla SegNoEnc
     * @param array $submodulo Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveSegNoEnc($seg_no_enc,$id = null){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($seg_no_enc as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;
                                
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id 
                FROM `seg_no_enc` 
                where 1
                AND nombres = ? 
                AND paterno = ? 
                AND materno = ? 
                and colonia = ? 
                ';
        $params = array($nombres,$paterno,$materno,$colonia);
                             
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
            if ($nombres && $paterno) 
            {                                        
                $insertData = array(
                'folio' => $folio,
                'nombres' => mb_strtoupper($nombres, "UTF-8"),
                'paterno' => mb_strtoupper($paterno, "UTF-8"),
                'materno' => mb_strtoupper($materno, "UTF-8"),
                'colonia' => mb_strtoupper($colonia, "UTF-8"),
                'telefono' => $telefono,                
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
                );                
                
                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
                //print_r($insertData);
                //exit;
                //Si recibimos id para editar
                if(intval($id)>0){
                    //Indicamos que haremos un update
                    $db_accion = 'update';
    
                    //Agregamos condición para indicar qué id se actualiza
                    self::getInstance()->where('id',$id);                                        
                }
                
            
                //Iniciamos transacción
                self::getInstance()->startTransaction();
                
                if(! self::getInstance()->{$db_accion}('seg_no_enc', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();
                    
                    }else{

                    //Campos guardados correctamente
                    $msg_no = 1;     
                    
                    
                    //Obtenemos el id del registro creado o editado
                    $id = ($db_accion == 'insert')?self::getInstance()->getInsertId():$id;
                    //echo $id;
                    //exit;
                    
                    //Guardamos campos afectados en la tabla
                    self::getInstance()->commit();               
                    } 
    
            }else{
            //'Campos Incompletos'
            $msg_no = 2;             
            }
                
        }        
        
        return array($msg_no,$id);        
    }

}