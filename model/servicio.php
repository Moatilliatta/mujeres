<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de servicios
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Servicio extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
    *Vemos los servicios disponibles de determinada beneficiaria
    * @param int $id_mujeres_avanzando ID de la tabla mujeres_avanznando
    * @param int $ID_C_PROGRAMA ID de la tabla c_programa
    * @param int $ID_C_DEPENDENCIA ID de la tabla c_dependencia
    *
    * @return bool $disp Resultado de ver si se tienen servicios disponibles
    **/
    public static function servDisp($id_mujeres_avanzando = NULL,$ID_C_PROGRAMA = NULL,
        $ID_C_DEPENDENCIA = NULL){        

        $sql = 'SELECT 
                s.ID_C_SERVICIO
                FROM mujeres_avanzando m
                LEFT JOIN c_servicio s on m.id_grado = s.ID_GRADO
                LEFT JOIN c_programa p on p.ID_C_PROGRAMA = s.ID_C_PROGRAMA
                LEFT JOIN c_dependencia d on d.ID_C_DEPENDENCIA = p.ID_C_DEPENDENCIA
                where 1
                AND  m.id = ? ';

        $params = array($id_mujeres_avanzando);

        if($ID_C_DEPENDENCIA != NULL){
            $sql .= ' AND d.ID_C_DEPENDENCIA = ? ';
            $params[] = $ID_C_DEPENDENCIA;
        }

        if($ID_C_PROGRAMA !=NULL){
            $sql .= 'AND p.ID_C_PROGRAMA = ? ';
            $params[] = $ID_C_PROGRAMA;
        } 

        $servicios = self::executar($sql,$params);

        $disp = (count($servicios) >0)? true: false;

        return $disp;  
    }

    /**
    * Obtenemos los datos de un servicio por su id
    *@param int $ID_C_SERVICIO id de la tabla c_servicio.
    *
    *@return Array datos de la tabla sericip
    **/
    public static function get_by_id($ID_C_SERVICIO){

        $datos = self::listado(null,null,null,$ID_C_SERVICIO);
        $datos = $datos[0];

        return $datos;
    }
      
      private static function listaGenerica($id_mujeres_avanzando = NULL, 
        $ID_C_PROGRAMA = NULL,$ID_C_DEPENDENCIA = NULL,$ID_C_SERVICIO = NULL,
        $id_grado = NULL,$activo = 1)
     {
        
       $sql = 
        'SELECT 
        s.ID_C_SERVICIO,
        s.NOMBRE as servicio,
        s.ES_CONTABLE, 
        s.STOCK,
		p.NOMBRE as programa,
        p.ID_C_PROGRAMA,
        d.ID_C_DEPENDENCIA,           
		d.NOMBRE as dependencia
        FROM  c_servicio s
        INNER JOIN c_programa p on p.ID_C_PROGRAMA = s.ID_C_PROGRAMA
        INNER JOIN c_dependencia d on d.ID_C_DEPENDENCIA = p.ID_C_DEPENDENCIA
        WHERE 1';

        //Parámetros de la sentencia
        $params = array();
        
        /*Verificamos si filtraremos por beneficiario y 
        no mostrarle los productos y servicios que ya tiene*/
        
        if($id_mujeres_avanzando != NULL){
            $sql .= " and s.ID_C_SERVICIO not in (
                        SELECT IFNULL(ID_C_SERVICIO,0) 
                        FROM c_mujeres_avanzando_detalle
                        WHERE ID_MUJERES_AVANZANDO = ?) ";
            $params[] = $id_mujeres_avanzando;
        }

        //Verificamos si filtraremos por id de algún producto/servicio
        if($ID_C_SERVICIO != NULL){
            $sql .= ' AND s.ID_C_SERVICIO = ? ';
            $params[] = $ID_C_SERVICIO;
        }

        //Verificamos si filtraremos por algún componentes (programa)
        if($ID_C_PROGRAMA != NULL){
            $sql .= ' AND p.ID_C_PROGRAMA = ? ';
            $params[] = $ID_C_PROGRAMA;
        }

        //Verificamos si filtraremos por id de algún producto/servicio
        if($ID_C_DEPENDENCIA != NULL){
            $sql .= ' AND d.ID_C_DEPENDENCIA = ? ';
            $params[] = $ID_C_DEPENDENCIA;
        }

        //Verificamos si filtraremos por algún listado de componentes (programas)
        /*if($lista_comp != NULL){
            $codigo = implode(',',$lista_comp);
            $sql .= " AND c.codigo IN (?) ";
            $params[] = $codigo;
        }*/

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND s.CVE_ESTATUS = ? ';
            $params[] = $activo;
        }

        //Verificamos si filtraremos por grado de insuficiencia
        if($id_grado != NULL){
            $sql .= ' AND s.ID_GRADO = ? ';
            $params[] = $id_grado;
        }
        
        //echo $sql;
        //print_r($params);
        

        //Regresamos resultado        
        return array($sql,$params);           
        
      }
      
      public static function listado($id_mujeres_avanzando = NULL, 
        $ID_C_PROGRAMA = NULL,$ID_C_DEPENDENCIA = NULL,$ID_C_SERVICIO = NULL,
        $id_grado = NULL,$activo = 1)
      {
        list($sql,$params) = self::listaGenerica($id_mujeres_avanzando, 
        $ID_C_PROGRAMA,$ID_C_DEPENDENCIA,$ID_C_SERVICIO,$id_grado,
        $activo = 1);
        return self::executar($sql,$params);  
      }
    /**
     * Obtenemos listado de los  servicios. Predeterminadamente mostramos
     * los componentes de estatus activo = 1
     * @param int $id_beneficiario id de la tabla beneficiario
     * @param int $id_dependencia id de la tabla c_dependencia 
     * @param int $id_programa id de la tabla c_programa
     * @param int $id_servicio id de la tabla c_servicio
     * @param array $lista_Servicios Listado de servicios
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaServicio($id_mujeres_avanzando = NULL, 
        $ID_C_PROGRAMA = NULL,$ID_C_DEPENDENCIA = NULL,$ID_C_SERVICIO = NULL,
        $id_grado = NULL,$activo = 1)
    {

       list($sql,$params) = self::listaGenerica($id_mujeres_avanzando, 
        $ID_C_PROGRAMA,$ID_C_DEPENDENCIA,$ID_C_SERVICIO,$id_grado,
        $activo = 1);
        //Regresamos resultado        
        return Paginador::paginar($sql,$params);           
    }

    /**
    *Obtenemos los códigos del componente (programa) y producto/servicio
    *@param int $id_producto_servicio Id de la tabla producto_servicio
    *
    *@return Array Resultado de la consulta
    **/
    public static function getCodCompPys($id_producto_servicio = NULL){
        
        $sql = 
        'SELECT 
        p.codigo as codigo_producto,
        c.codigo as codigo_componente,
        c.id as id_componente
        FROM `producto_servicio` p 
        LEFT JOIN departamento d on p.id_departamento = d.id
        LEFT JOIN direcciones di on d.id_direccion = di.id
        LEFT JOIN componente c on di.id_componente = c.id
        where p.id = ? ';

        //Parámetros de la sentencia
        $params = array($id_producto_servicio);

        //Regresamos resultado
        $resultado = self::executar($sql,$params);
        $resultado = $resultado[0];

        return Paginador::paginar($sql,$params); 
    }


   public static function saveStock($stock,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($stock as $key => $p):
        ${$key} = self::getInstance()->real_escape_string($p);
        endforeach;
        
        /*
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
        */ 

        //Verificamos que no haya nombre duplicado
                   
                //Obtenemos el id del usuario creador
                $id_usuario_creador = $_SESSION['usr_id'];
                
                /*Si no esta creada la variable activo 
                predeterminadamente la guardamos = 1*/        
                if(!isset($activo) ){
                    $activo = 1 ;            
                }
                
                //Campos obligatorios
                if ($stock){
                
                                
                    $insertData = array(
                        'STOCK' => $stock,
                        'CVE_ESTATUS' => $activo,
                        'USUARIO_ALTA' => $id_usuario_creador,
                        'FECHA_MODIFICA' => date('Y-m-d H:i:s')
                        );
                        
                        //Quitamos del arreglo los valores vacíos
                        //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));   
                        
                         //Si recibimos id para editar
                        if(intval($id)>0){
                            //Indicamos que haremos un update
                            $accion = 'update';
        
                            //Agregamos condición para indicar qué id se actualiza
                            self::getInstance()->where('ID_C_SERVICIO',$id);                                        
                        }
                        
                        //Iniciamos transacción
                        self::getInstance()->startTransaction();
                        
                        if(! self::getInstance()->{$accion}('c_servicio', $insertData)){
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
}
?>