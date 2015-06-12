<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla log_mujeres_avanzando
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');

class logMujeresAvanzando extends MysqliDb{

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
     * Guardamos registro en la tabla log_mujeres_avanzando
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id de la tabla log_mujeres_avanzando a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
     public static function saveLogMujeresAvanzando($logMujeresAvanzando,$id = null)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];  

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
         a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($logMujeresAvanzando as $key => $value):
           ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;            

        //Campos obligatorios
        if ($folio) 
        {
                $insertData = array(
                'folio' => $folio,
                'fecha_foto' => $fecha_foto,
                'fecha_impresion' => $fecha_impresion,
                'fecha_creacion' => date('Y-m-d h:i:s'),
                'id_usuario_creador' => $id_usuario_creador
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
                
                if(! self::getInstance()->{$accion}('log_mujeres_avanzando', $insertData)){
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

    /**
     * Obtenemos listado de los perfiles. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param  string $busqueda       [description]
     * @param  string $tipo_filtro    [description]
     * @param  int $id_caravana    [description]
     * @param  string $fecha_creacion [description]
     * @return array Resultado de la consulta
     */
    public static function listaLog($busqueda = NULL,$tipo_filtro='nombre', $id_caravana = NULL,
        $fecha_creacion = NULL){

        $sql = 
        'SELECT 
        l.folio,
        m.nombres,
        m.paterno,
        m.materno,
        c.descripcion as caravana,
        l.fecha_creacion,
        l.fecha_foto,
        l.fecha_impresion
        FROM `log_mujeres_avanzando` l 
        LEFT JOIN mujeres_avanzando m on m.folio = l.folio
        LEFT JOIN caravana c on m.id_caravana = c.id
        where ?             
        ';
        
        /*
        -- and  = 1
        -- and l.fecha_creacion BETWEEN "2014-07-24" AND "2014-07-25"
        -- and l.fecha_foto is not null
        -- and l.fecha_creacion is not null   
         */
        
        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda
       if ($busqueda !==NULL && $tipo_filtro !==NULL){

           switch($tipo_filtro){

                case 'nombre':
                 $alias=' concat(ifnull(m.nombres,?),?,ifnull(m.paterno,?),?,ifnull(m.materno,?)) ';
                 $params = array_merge($params,array('',' ','',' ',''));
                 $sql .=  ' AND '.$alias.' LIKE ? ';
                 $params[] = '%'.$busqueda.'%';
                 break;
           }
       }

        //Verificamos si se quieren filtrar por caravana
        if($id_caravana !== NULL){
            $sql .= ' AND m.id_caravana = ?';
            $params[] = $id_caravana;
        }

        //Verificamos si se quieren filtrar por fecha de creacion
        if($fecha_creacion !== NULL){
            $sql .= ' AND date(l.fecha_creacion) = ?';
            $params[] = $fecha_creacion;
        }

        $sql .= ' order by fecha_creacion DESC ';

        /*
        echo $sql;
        print_r($params);
        */
       
        //Regresamos resultado
        return Paginador::paginar($sql,$params);          
    }

    /**
     * Obtenemos el total de impresiones de la tabla log_mujeres_avanzando
     * @param  $fecha_creacion Fecha en que fue creada la impresión
     * @return Integer Total de Impresiones
     */
    public static function totalImpresiones($fecha_creacion = NULL){

        $sql = "SELECT
                count(DISTINCT(l.id)) as total_impresiones
                FROM `log_mujeres_avanzando` l
                LEFT JOIN mujeres_avanzando m on m.folio = l.folio
                where ? AND l.fecha_impresion is not null ";

        //Parámetros de la sentencia
        $params = array(1);

        //Verificamos si se quieren filtrar por fecha de creación
        if($fecha_creacion !== NULL){
            $sql .= ' AND date(m.fecha_foto) = ? ';
            $params[] = $fecha_creacion;
        }

        $result = self::executar($sql,$params);                
        
        $result = $result[0]['total_impresiones'];

        return $result;
    }

    /**
     * Obtenemos el total de impresiones agrupadas por caravana
     * @return  Array Caravanas con su número correspondiente de impresiones
     */
    public static function impresionesCaravana(){
        
        $sql = "SELECT
                c.id,
                IFNULL(c.descripcion,'FUERA DE CARAVANA') as caravana,
                COUNT(DISTINCT(l.id)) as total_imp
                FROM `log_mujeres_avanzando` l
                LEFT JOIN mujeres_avanzando m on m.folio = l.folio 
                                             and DATE(m.fecha_foto) = DATE(l.fecha_impresion)
                LEFT JOIN caravana c on m.id_caravana = c.id
                where l.fecha_impresion is not null
                GROUP BY c.id; ";    

        return self::executar($sql,$params);
    }
}
?> 