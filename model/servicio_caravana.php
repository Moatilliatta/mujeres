<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de servicios
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos modelo de usuario_caravana
include_once($_SESSION['model_path'].'usuario_caravana.php');

class ServicioCaravana extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}

    /**
    * Buscamos un servicio en una caravana determinada
    * @param int $ID_C_SERVICIO ID del servicio
    * @param int $id_caravana ID de la tabla caravana
    *
    * @return 
    **/
    public function buscaServCaravana($ID_C_SERVICIO,$id_caravana){

            self::getInstance()->join("c_servicio s", "s.ID_C_SERVICIO = c.ID_C_SERVICIO", "LEFT")
                               ->where('c.id_caravana', $id_caravana)
                               ->where('c.ID_C_SERVICIO', $ID_C_SERVICIO);

            $results = self::getInstance()->getOne('servicio_caravana c',
                                                    'c.*,s.ES_CONTABLE');

            return $results;

    }

      private static function listaGenerica($id_mujeres_avanzando = NULL, 
        $ID_C_PROGRAMA = NULL,$ID_C_DEPENDENCIA = NULL,$ID_C_SERVICIO = NULL,
        $id_grado = NULL,$resto = NULL,$filtra_caravanas = NULL,$activo = 1)
     {
        
       $sql = 
        'SELECT 
            s.ID_C_SERVICIO,
            s.NOMBRE as servicio, 
    	      p.NOMBRE as programa,
            p.ID_C_PROGRAMA,
            d.ID_C_DEPENDENCIA,
            s.STOCK as stock_pred,
            sc.STOCK_INICIAL,
            sc.STOCK_FINAL as stock_caravana,
            c.descripcion as caravana,           
    	      d.NOMBRE as dependencia,
            s.ID_GRADO,
            s.ES_CONTABLE
            FROM  c_servicio s
            INNER JOIN c_programa p on p.ID_C_PROGRAMA = s.ID_C_PROGRAMA
            INNER JOIN c_dependencia d on d.ID_C_DEPENDENCIA = p.ID_C_DEPENDENCIA
            LEFT JOIN servicio_caravana sc on sc.ID_C_SERVICIO = s.ID_C_SERVICIO
            LEFT JOIN  caravana c on c.id = sc.id_caravana
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
        if($lista_comp != NULL){
            $codigo = implode(',',$lista_comp);
            $sql .= " AND c.codigo IN (?) ";
            $params[] = $codigo;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND s.CVE_ESTATUS = ? ';
            $params[] = $activo;
        }

        //Verificamos si filtraremos por grado de insuficiencia
        if($id_grado != NULL){

            $op = ($resto === true)? ' != ' : ' = ';

            $sql .= ' AND s.ID_GRADO '.$op.' ? ';

            $params[] = $id_grado;
        }

        //Filtramos los servicios por las caravanas que tiene el usuario
        if($filtra_caravanas === true){

          $id_usuario = $_SESSION['usr_id'];

          //Obtenemos caravanas del usuario
          $caravanas = UsuarioCaravana::listadoCaravanaUsr(null,$id_usuario);

          $car = implode(',', $caravanas);

          $sql .= ' AND c.id IN (?)';

          $params[] = $car;

        }
        

        //Regresamos resultado        
        return array($sql,$params);           
        
      }
      
      public static function listado($id_mujeres_avanzando = NULL, 
        $ID_C_PROGRAMA = NULL,$ID_C_DEPENDENCIA = NULL,$ID_C_SERVICIO = NULL,
        $id_grado = NULL,$resto = NULL,$filtra_caravanas = NULL,$activo = 1)
      {
        list($sql,$params) = self::listaGenerica($id_mujeres_avanzando, 
        $ID_C_PROGRAMA,$ID_C_DEPENDENCIA,$ID_C_SERVICIO,$id_grado,$resto,
        $filtra_caravanas,$activo = 1);
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
        $id_grado = NULL,$resto = NULL,$filtra_caravanas = NULL,$activo = 1)
    {

       list($sql,$params) = self::listaGenerica($id_mujeres_avanzando, 
        $ID_C_PROGRAMA,$ID_C_DEPENDENCIA,$ID_C_SERVICIO,$id_grado,$resto,
        $filtra_caravanas,$activo);
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
 private static function UpdateServCaravana($id_servicio,$id_caravana,$stock){
    //echo $id_caravana;
    //exit;
     $msg_no = 0;
    
       $stock_data = array(
        'ID_C_SERVICIO' => $id_servicio,
        'id_caravana' => $id_caravana,
        'stock' => $stock
        );

       
       self::getInstance()->where('ID_C_SERVICIO',$id_servicio);
       self::getInstance()->where('id_caravana',$id_caravana);
       $results = self::getInstance()->getOne('servicio_caravana');
       
       if($results != null)
       {
                $id_servicio_caravana = $results['id'];
               $msg_no = self::saveServicioCaravana($stock_data, $id_servicio_caravana);
       }else{
        
          $msg_no = self::saveServicioCaravana($stock_data);
        
       }
        
       return  $msg_no;
       
   } 
   
    public static function UpdateServicios($datos_serv_caravana){
        //print_r($datos_serv_caravana);
        //exit;
    $msg_no = 0 ;
     //Creamos arreglo que contendra stocks y el id del servicio
     $id_servicio = $datos_serv_caravana['id_servicio'];
     $stock = $datos_serv_caravana['stock'];
     $id_caravana = $datos_serv_caravana['id_caravana'];
     //print_R($id_servicio);
     //exit;
     foreach($id_servicio as $k => $v):
     
     $msg_no = self::UpdateServCaravana($v,$id_caravana,$stock[$k]);
      if($msg_no != 1){
       //echo $msg_no.'<br/>';
       //print_r($v);
       //print_r($k);
       //exit; 
    break;    
   }                    
     endforeach;
     //exit;
    return $msg_no;   
   } 

    public static function incServ($ID_C_SERVICIO,$id_caravana){
        
        $sql = 'UPDATE servicio_caravana 
                SET STOCK_FINAL = STOCK_FINAL + 1
                WHERE 1
                and ID_C_SERVICIO = ?
                and id_caravana = ? ';

        $params = array($ID_C_SERVICIO,$id_caravana);

        return self::executar($sql,$params);
    }  

    public static function decServ($ID_C_SERVICIO,$id_caravana){
        
        $sql = 'UPDATE servicio_caravana 
                SET STOCK_FINAL = STOCK_FINAL - 1
                WHERE 1
                and ID_C_SERVICIO = ?
                and id_caravana = ?
                and STOCK_FINAL > 0 ';

        $params = array($ID_C_SERVICIO,$id_caravana);

        return self::executar($sql,$params);
    }   

   public static function saveServicioCaravana($stock_data,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        //PRINT_R($stock_data);
        //EXIT;
        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($stock_data as $key => $p):
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
               // $id_caravana = $_SESSION['id_caravana'];
                /*Si no esta creada la variable activo 
                predeterminadamente la guardamos = 1*/        
                if(!isset($activo) ){
                    $activo = 1 ;            
                }
                
                //Campos obligatorios
                //if ($stock){
                
                                
                    $insertData = array(
                        'stock' => $stock,
                        'STOCK_FINAL' => $stock,
                        'STOCK_INICIAL' => $stock,
                        'ID_C_SERVICIO' => $ID_C_SERVICIO,
                        'id_usuario_creador' => $id_usuario_creador,
                        'id_caravana' => $id_caravana,
                        'fecha_modif' => date('Y-m-d H:i:s')
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
                        
                        if(! self::getInstance()->{$accion}('servicio_caravana', $insertData)){
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
        
                /*}else{
                    //'Campos Incompletos'
                    $msg_no = 2;             
                }*/
                           
        return $msg_no;
    }
}
?>