<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de capacitaciones
 * **/ 

//Incluimos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos librería de fecha
include_once($_SESSION['inc_path'].'libs/Fechas.php');
include_once($_SESSION['model_path'].'seg_punto_rosa_capacitacion.php');

class SegCapacitacionMujer extends MysqliDb{

    public function __construct(){}
        
      /**
      * Ejecutamos sentencia sql con parámetros
      * @param string $sql Sentencia SQL
      * @param array $params Cada uno de los parámetros de la sentencia
      * 
      * @return int Resultado
      **/   
    private static function executar($sql,$params){

      //Ejecutamos
      $resultado = self::getInstance()->rawQuery($sql, $params);

      //Regresamos resultado
      return $resultado;        

    }

    public static function buscaReg($id_seg_punto_rosa_capacitacion = NULL, $id_mujeres_avanzando = NULL,
      $id_seg_capacitacion = NULL, $id_seg_punto_rosa = NULL)
    {

       //Sentencia para obtener el campo activo de la tabla seg_capacitacion_mujer
        $sql = 'SELECT id, activo 
                FROM `seg_capacitacion_mujer` 
                WHERE ? '; 

        $params = array(1);

        if($id_seg_punto_rosa_capacitacion != NULL){
          $sql .= ' AND id_seg_punto_rosa_capacitacion = ? ';
          $params[] = $id_seg_punto_rosa_capacitacion;
        } 

        if($id_mujeres_avanzando != NULL){
          $sql .= ' AND id_mujeres_avanzando = ? ';
          $params[] = $id_mujeres_avanzando;
        }
        
        if($id_seg_capacitacion != NULL){
          $sql .= ' AND id_seg_capacitacion = ? ';
          $params[] = $id_seg_capacitacion;
        }

        if($id_seg_punto_rosa != NULL){
          $sql .= ' AND id_seg_punto_rosa = ? ';
          $params[] = $id_seg_punto_rosa;
        }

        //Verificamos el estatus del usuario        
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        return $registro;

    }
    
       /**
     * Cambiamos el estatus del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     **/
    public static function activaSegCapacitacionMujer($id_seg_punto_rosa_capacitacion = NULL,
      $id_mujeres_avanzando = NULL,$fecha_capacitacion = NULL,$id_seg_capacitacion = NULL,
      $id_seg_punto_rosa = NULL)
    {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Obtenemos el registro de la tabla seg_capacitacion_mujer
        $registro = self::buscaReg($id_seg_punto_rosa_capacitacion, 
                                   $id_mujeres_avanzando,
                                   $id_seg_capacitacion, 
                                   $id_seg_punto_rosa);
        
        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;
        }else if($registro['activo'] == 1){
         //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;
        }
        
        //Obtenemos id del registro
        $id_seg_capacitacion_mujer = $registro['id'];
        
        //Preparamos update
        self::getInstance()->where('id',$id_seg_capacitacion_mujer);                                                

        //Datos a actualizar
        $updateData = array('activo' => $estatus,
                            'fecha_capacitacion' => $fecha_capacitacion);                            
        
        if($id_seg_capacitacion != NULL){
          $updateData['id_seg_capacitacion'] = $id_seg_capacitacion;         
        }

        if($id_seg_punto_rosa != NULL){
          $updateData['id_seg_punto_rosa'] = $id_seg_punto_rosa;
        }
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('seg_capacitacion_mujer',$updateData)){
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
     * Función que nos generará un listado de capacitaciones(platicas) 
     * que tiene ligados la beneficiaria.
     * @param  [type]  $id_mujeres_avanzando [description]
     * @param  [type]  $id_seg_punto_rosa        [description]
     * @param  integer $activo               [description]
     * @return [type]                        [description]
     */
    private static function listCapacitacionMujer($id_mujeres_avanzando = NULL,
      $id_seg_punto_rosa = NULL,$tipo = NULL,$activo = 1) {
        
        //echo $tipo;
        //exit;
        
         $sql = 
         'SELECT
          scm.id,
          scm.id_mujeres_avanzando,
          concat(ifnull(m.nombres,?),?,ifnull(m.paterno,?),?,ifnull(m.materno,?)) as nombre_completo,
          scm.fecha_capacitacion,
          sprc.id as id_seg_punto_rosa_capacitacion,
          sprc.id_seg_capacitacion,
          sprc.id_seg_punto_rosa,
          p.descripcion as punto_rosa,
          sc.nombre as capacitacion
          FROM seg_capacitacion_mujer scm
          LEFT JOIN mujeres_avanzando m on m.id = scm.id_mujeres_avanzando
          LEFT JOIN seg_punto_rosa_capacitacion sprc on sprc.id = scm.id_seg_punto_rosa_capacitacion 
           -- LEFT JOIN punto_rosa p on p.id = sprc.id_seg_punto_rosa
          LEFT JOIN seg_punto_rosa p on p.id = sprc.id_seg_punto_rosa
          INNER JOIN seg_capacitacion sc on sc.id = sprc.id_seg_capacitacion 
          where 1';

          //Parámetros de la sentencia
          $params = array('',' ','',' ','');
          
           if($id_mujeres_avanzando !=null){
                    
          $sql .= ' AND sc.tipo = ? ';
          $params[] = $tipo;

        }

        //Buscamos por ID mujer avanzando           
        if($id_mujeres_avanzando !=null){
                    
          $sql .= ' AND scm.id_mujeres_avanzando = ? ';
          $params[] = $id_mujeres_avanzando;

        }

         //Buscamos por id_seg_punto_rosa           
        if($id_seg_punto_rosa !=null){
                    
          $sql .= ' AND sprc.id_seg_punto_rosa = ? ';
          $params[] = $id_seg_punto_rosa;

        }
        
        //Buscamos por activo           
        if($activo !=null){
                    
          $sql .= ' AND scm.activo = ? ';
          $params[] = $activo;

        }        

        return array($sql,$params);          
     }

    /**
     * Función para obtener el listado PAGINADO de la tabla seg_capacitacion_mujer
     * @param  [type] $id_mujeres_avanzando [description]
     * @return [type]                       [description]
     */
    public static function listaCapacitacionMujer($id_mujeres_avanzando = NULL,
      $id_seg_punto_rosa = NULL,$tipo = NULL){
        
        list($sql,$params) = self::listCapacitacionMujer($id_mujeres_avanzando,
                                                         $id_seg_punto_rosa,
                                                         $tipo);

        return Paginador::paginar($sql,$params);   
    }

    /**
     * Función para obtener un arreglo con los campos activo de la tabla seg_capacitacion_mujer
     * @param  [type] $id_mujeres_avanzando [description]
     * @return [type]                       [description]
     */
    public static function listadoCapacitacionMujer($id_mujeres_avanzando = NULL,
      $id_seg_punto_rosa = NULL,$tipo = NULL){
        
      list($sql,$params) = self::listCapacitacionMujer($id_mujeres_avanzando,
                                                         $id_seg_punto_rosa,
                                                         $tipo);

       
        $obj = self::executar($sql,$params);
        
        //print_r($obj);
        //exit;
        
        $l = array();

        foreach ($obj as $key => $value):
          $l[] = $value['id_seg_punto_rosa_capacitacion'];
        endforeach;

        //Regresamos resultado
        return  $l;
         
    }
     
    /**
     * Obtenemos listado de las fechas de cada capacitacion
     * @param  [type] $id_mujeres_avanzando ID de la mujer
     * @return [type]                       Listado de Fechas
     */
    public static function listadoFechasCapacitacion($id_mujeres_avanzando = NULL){

      //Preparamos sentencia para buscar las fechas
      $sql = 'SELECT 
              id_seg_punto_rosa_capacitacion,
              fecha_capacitacion
              from seg_capacitacion_mujer 
              where 1 
              AND id_mujeres_avanzando = ? ';

      $params = array($id_mujeres_avanzando);

      $obj = self::executar($sql,$params);
        
      $l = array();

      foreach ($obj as $key => $value):
          $l[$value['id_seg_punto_rosa_capacitacion']] = $value['fecha_capacitacion'];
      endforeach;

      //Regresamos resultado
      return  $l;

    }
    /**
     * Revisamos duplicados de folios de titulares e integrantes
     */
     
     public static function foliosDuplicados($folio = null){
        
        //$folio = array('4050','4051','4052','4053','4054','4055','4056','4050','4050');
        
        $f = array_replace($folio,array_fill_keys(array_keys($folio, null),''));

        $folios_procesados = array_count_values($f);
        $unicos = array();
        $duplicados = array(); 
         
        foreach ($folios_procesados as $key => $value):
        
        if($value != 1){
            
         $duplicados[$key]=$value-1;  
        
        }
        endforeach;
        $unicos = array_unique($folio);
         
       
        //Retornamos array(s)
        
        return array($duplicados,$unicos);
        
    
     }
           
    /**
     * [saveSegpuntorosaCapacitacion description]
     * @param  [type] $seg_capacitacion_mujer         [description]
     * @param  [type] $id_mujeres_avanzando           [description]
     * @return [type]                                 [description]
     */
     public static function saveSegCapacitacionMujer($seg_capacitacion_mujer = NULL,$id_mujeres_avanzando = null){
     
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;       

        //Validamos que se nos envíe un arreglo
        if($seg_capacitacion_mujer != NULL)
        {

          //Variables con arreglos de checkbox
          $id_seg_punto_rosa_capacitacion = (isset($seg_capacitacion_mujer['id_seg_punto_rosa_capacitacion']))? $seg_capacitacion_mujer['id_seg_punto_rosa_capacitacion'] : NULL ;
          $fecha_capacitacion = (isset($seg_capacitacion_mujer['fecha_capacitacion']))? $seg_capacitacion_mujer['fecha_capacitacion'] : NULL ;
          $tipo = (isset($seg_capacitacion_mujer['tipo']))? $seg_capacitacion_mujer['tipo'] : NULL ;

          //Limpiamos tabla poniendo los valores de 'activo' = 0
          self::limpiaCap($id_mujeres_avanzando,null,$tipo);
          
          //Mensaje de Respuesta
          $msg_no = 1;

           //Iniciamos transacción
          self::getInstance()->startTransaction();
          
          //Verificamos que sea un arreglo
          if(is_array($id_seg_punto_rosa_capacitacion)){

              foreach($id_seg_punto_rosa_capacitacion as $key => $value):

                $sprc = Seg_punto_rosa_capacitacion::get_by_id($value);

               //Complementamos arreglo para guardar el servicio
                $insertData['id_mujeres_avanzando'] = $id_mujeres_avanzando;
                $insertData['id_seg_punto_rosa_capacitacion'] = $value;
                $insertData['id_seg_capacitacion'] = $sprc['id_seg_capacitacion'];
                $insertData['id_seg_punto_rosa'] = $sprc['id_seg_punto_rosa'];
                $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
                $insertData['id_usuario_ultima_mod'] = $_SESSION['usr_id'];
                $insertData['fecha_creado'] = date('Y-m-d H:i:s');
                $insertData['fecha_capacitacion'] = $fecha_capacitacion[$key];               
                
                //Buscamos si ya está este registro pero como inactivo               
                $sql = 'SELECT 
                        id, 
                        activo 
                        from `seg_capacitacion_mujer` 
                        where 1 
                        AND id_seg_punto_rosa_capacitacion = ? 
                        AND id_mujeres_avanzando = ? AND activo = 0';

                $params = array($value,$id_mujeres_avanzando);        
                $seg_capacitacion_mujer = self::executar($sql,$params);
                $seg_capacitacion_mujer = $seg_capacitacion_mujer[0];
               // print_R($insertData);
               // exit;

                    /*Previamente había un registro inactivo de este servicio, 
                    sólo será activado de nuevo, caso contrario, agregamos un nuevo
                    registro en la tabla*/
                    if($seg_capacitacion_mujer != NULL){
                        $msg_no = self::activaSegCapacitacionMujer($value,
                                                                   $id_mujeres_avanzando,
                                                                   $fecha_capacitacion[$key],
                                                                   $sprc['id_seg_capacitacion'],
                                                                   $sprc['id_seg_punto_rosa']);
                    }else{                            

                            //Guardamos cada registro, en caso de haber error, cancelamos los registros
                            if(!self::getInstance()->insert('seg_capacitacion_mujer', $insertData)){
                                //No se pudo guardar uno de los servicios
                                $msg_no = 3;                                              
                            }
                          
                            
                    }

                    //Si tenemos mensaje de error
                    if($msg_no == 3){
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();               
                    }

            endforeach;
            
            
              if($msg_no == 1){
                //Guardamos campos afectados en la tabla
                self::getInstance()->commit();
            }   

          }            

        }else{
          echo "AQUI";
          exit;
        }
        
           return $msg_no;  
       
       }     

      public static function saveSegCapMujer($seg_capacitacion_mujer,$id_mujeres_avanzando,$H,$HD)
      {
       // print_R($H);
        
        //print_R($HD);
        //exit;
                
        //Validamos que se nos envíe un arreglo
        if($seg_capacitacion_mujer != NULL)
        {

          //Variables con arreglos de checkbox
          $id_seg_capacitacion = (isset($seg_capacitacion_mujer['id_seg_capacitacion']))? $seg_capacitacion_mujer['id_seg_capacitacion'] : NULL ;
          $id_seg_punto_rosa = (isset($seg_capacitacion_mujer['id_seg_punto_rosa']))? $seg_capacitacion_mujer['id_seg_punto_rosa'] : NULL ;
          $fecha_capacitacion = (isset($seg_capacitacion_mujer['fecha_capacitacion']))? $seg_capacitacion_mujer['fecha_capacitacion'] : NULL ;
           //echo $fecha_capacitacion;
           //exit;
          //Obtenemos el tipo
          $tipo = (isset($seg_capacitacion_mujer['tipo']))? $seg_capacitacion_mujer['tipo'] : NULL ;            
              
          //Limpiamos tabla poniendo los valores de 'activo' = 0
          self::limpiaCap($id_mujeres_avanzando,$id_seg_punto_rosa,$tipo);
                               
          //Mensaje de Respuesta
          $msg_no = 1;

          //Iniciamos transacción
          self::getInstance()->startTransaction();

          //Verificamos que sea un arreglo
          if(is_array($id_seg_capacitacion)){

            foreach($id_seg_capacitacion as $key => $value):
             
             if($H[$key] !=NULL){
                $tipo = 'H';
             }elseif($HD[$key] !=NULL){
                $tipo = 'HD';
             }else{
                $tipo = null;
             }
             /*
             if ($id_seg_punto_rosa == null && ''){
                $id_seg_punto_rosa = 99;
             }
              */
              //Complementamos arreglo para guardar el servicio
              $insertData['id_mujeres_avanzando'] = $id_mujeres_avanzando;
              $insertData['id_seg_capacitacion'] = $value;
              $insertData['id_seg_punto_rosa'] = $id_seg_punto_rosa;
              $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
              $insertData['id_usuario_ultima_mod'] = $_SESSION['usr_id'];
              $insertData['fecha_creado'] = date('Y-m-d H:i:s');
              $insertData['tipo_entrega'] = $tipo;
              
              //print_R($insertData);
              //exit;
              
              
              //La obtenemos mediante el punto rosa y la capacitación
              $segPuntoRosaCap = Seg_punto_rosa_capacitacion::get_by_punto_cap($id_seg_punto_rosa,$value);
              
              //print_R($segPuntoRosaCap);
              //exit;
              
              //Si no obtenemos directamente la fecha de capacitación
              //$segPuntoRosaCap['fecha_creado'];
              //exit;
              
              if($fecha_capacitacion == null && ''){                  
                   $insertData['fecha_capacitacion'] = $fecha_capacitacion;
                   
                }elseif($segPuntoRosaCap != NULL){
                   $insertData['fecha_capacitacion'] = $segPuntoRosaCap['fecha_creado'];
                      
              }
              
               //print_R($insertData);
               //exit;
              if($segPuntoRosaCap != NULL){
                  $insertData['id_seg_punto_rosa_capacitacion'] = $segPuntoRosaCap['id'];  
              }              
              
              //Buscamos si ya está este registro pero como inactivo               
              $sql = 'SELECT 
                      id, 
                      activo 
                      from `seg_capacitacion_mujer` 
                      where 1 
                      AND id_seg_capacitacion = ?
                      AND id_seg_punto_rosa = ? 
                      AND id_mujeres_avanzando = ? AND activo = 0';

              $params = array($value,$id_seg_punto_rosa,$id_mujeres_avanzando);        

              $seg_capacitacion_mujer = self::executar($sql,$params);
              $seg_capacitacion_mujer = $seg_capacitacion_mujer[0];

              /*Previamente había un registro inactivo de este servicio, 
              sólo será activado de nuevo, caso contrario, agregamos 
              un nuevo registro en la tabla*/
                  
              if($seg_capacitacion_mujer != NULL){
                
                //Verificamos qué fecha de capacitación tenemos disponible
                $fecha_cap = ($fecha_capacitacion == NULL)? $segPuntoRosaCap['fecha_creado'] : 
                                                                     $fecha_capacitacion ;

                $msg_no = self::activaSegCapacitacionMujer(NULL,
                                                           $id_mujeres_avanzando,
                                                           $fecha_cap,
                                                           $value,
                                                           $id_seg_punto_rosa);
              }else{          

                //Guardamos cada registro, en caso de haber error, cancelamos los registros
                if(!self::getInstance()->insert('seg_capacitacion_mujer', $insertData)){
                    //No se pudo guardar uno de los servicios
                    $msg_no = 3;                                              
                }

              }
                //Si tenemos mensaje de error
                if($msg_no == 3){
                  //Cancelamos los posibles campos afectados
                  self::getInstance()->rollback();
                }

            endforeach;
            
            }             

          if($msg_no == 1){
            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();
          }   


        }else{
          //campos incompletos
          $msg_no = 2;
        }

        return $msg_no;  

       }

       private static function limpiaCap($id_mujeres_avanzando = NULL,
        $id_seg_punto_rosa = NULL,$tipo = NULL){
            
            
            //echo $id_seg_punto_rosa;
            //exit;

        $sql = 'UPDATE seg_capacitacion_mujer scm
                    LEFT JOIN seg_capacitacion sc on sc.id = scm.id_seg_capacitacion
                    SET scm.activo = ? 
                    WHERE 1 ';

        $params = array(0);

        if($id_mujeres_avanzando != NULL){
          $sql .= ' AND scm.id_mujeres_avanzando = ? ';
          $params[] = $id_mujeres_avanzando;
        }

        if($id_seg_punto_rosa != NULL){
          $sql .= ' AND scm.id_seg_punto_rosa = ? ';
          $params[] = $id_seg_punto_rosa;
        }

        if($tipo != NULL){
          $sql .= ' AND sc.tipo = ? ';
          $params[]= $tipo;
        }                                  

        /*
        echo $sql;
        print_r($params);
        exit;
        */
       
        return self::executar($sql,$params);

       }

}
?>