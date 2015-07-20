<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario
 * **/ 
 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Bloqueo extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    

    /**
     * Obtenemos listado de las registro bloqueados 
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */     
    public static function listaBloqueos($busqueda,$tipo_filtro,$activo = NULL){

        $sql = 
        'SELECT
        b.*,
        u.usuario,
        g.nombre as nombre_grupo,
        sm.nombre as nombre_submodulo,
        m.nombre as nombre_modulo,
        a.nombre as nombre_accion, 
        Concat(IFNULL(u1.nombres,?),?,IFNULL(u1.paterno,?),?,IFNULL(u1.materno,?)) as nombre_usuario,   
        e.nombre as estatus    
        FROM `bloqueo` b    
        left join usuario u on u.id = b.id_usuario_creador
        LEFT JOIN usuario u1 on u1.id = b.id_usuario   
        left join accion a on a.id = b.id_accion    
        left join submodulo sm on sm.id = b.id_submodulo    
        left join modulo m on m.id = sm.id_modulo   
        left join grupo g on g.id = b.id_grupo    
        left join estatus e on e.valor = a.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(' ',' ',' ',' ',' ',1);

        //Filtro de búsqueda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){

            //switch($tipo_filtro){

                //case :
                 $sql .= ' AND a.'.$tipo_filtro.' LIKE ? ';
                 $params[] = '%'.$busqueda.'%';       
                    //break;
            //}
        }

        //Regresamos resultado
        //return self::executar($sql,$params);

        return Paginador::paginar($sql,$params);           
    }


    /**
     * Guardamos registro en la tabla Usuario
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveBloqueo($bloqueo,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($bloqueo as $key => $b):
         ${$key} =  self::getInstance()->real_escape_string($b);
         //${$key} = ($b);
         endforeach;
          
        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];

        //Campos obligatorios
        if ($id_usuario || $id_grupo || $id_modulo || $id_submodulo || $id_accion )
        {

                $insertData = array(                
                'id_usuario_creador' => $id_usuario_creador,                
                'fecha_creado' => date('Y-m-d H:i:s')
                );
                
                $insertData['id_usuario'] = ($id_usuario > 0) ? $id_usuario : NULL;
                $insertData['id_grupo'] = ($id_grupo > 0) ? $id_grupo : NULL;
                $insertData['id_modulo'] = ($id_modulo > 0) ? $id_modulo : NULL;
                $insertData['id_submodulo'] = ($id_submodulo > 0) ? $id_submodulo : NULL;
                $insertData['id_accion'] = ($id_accion > 0) ? $id_accion : NULL;
                
                //var_dump($insertData);
                
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
                
                if(! self::getInstance()->{$accion}('bloqueo', $insertData)){
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
            //'Debe seleccionar al menos un campo'
            $msg_no = 12;             
        }
        return $msg_no;
    }
}