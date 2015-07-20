<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla seg_activacion_com
 * **/ 

//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class SegActivacionCom extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
        
    /**
    * Obtenemos los datos del aspirante por su id
    *@param int $id_beneficiario id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function get_by_id($id_seg_activacion_com){

        $datos = self::getInstance()->where('id', $id_seg_activacion_com)
                                    ->getOne('caravana');

        return $datos;
    }

   /**
    *Activamos o desactivamos un apoyo de la mujer en cuestión
    *@param int $id_mujeres_avanzando
    *@param int $id_seg_actividad id del apoyo
    *
    *@return int $msg_no Mensaje
    **/
    public static function activaActivacionCom($id_mujeres_avanzando,$id_seg_actividad,
      $id_seg_colonia,$fecha_activacion,$observaciones,$id = NULL)
    {

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;


        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla seg_activacion_com
        $sql = 'SELECT id, activo from `seg_activacion_com` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

            $sql .= ' AND id = ?';

            //parámetros para la consulta
            $params = array($id);

        }else{

            $sql .= ' AND id_seg_actividad = ? AND id_mujeres_avanzando = ?'; 

            //parámetros para la consulta
            $params = array($id_seg_actividad,$id_mujeres_avanzando);

        }        
        
        /*echo $sql;
        print_r($params);*/

        //Obtenemos el registro
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;

        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;

        }
        
        //Obtenemos id del registro
        $id_seg_activacion_com = $registro['id'];

        //Preparamos update
        self::getInstance()->where('id',$id_seg_activacion_com);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus,
                            'id_seg_colonia' => $id_seg_colonia, 
                            'observaciones' => $observaciones,
                            'fecha_activacion' => $fecha_activacion,
                            'id_usuario_ultima_mod' => $_SESSION['usr_id']);

        /*
        echo 'id_seg_activacion_com: '.$id_seg_activacion_com;
        print_r($updateData);
        */
       
        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('seg_activacion_com',$updateData)){

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
     * Obtenemos listado genérico de las actividades
     * @param  varchar  $id_seg_actividad            [description]
     * @param  datetime $id_mujeres_avanzando[description]
     * @param  integer $activo            [description]
     * @return Array                     [description]
     */
    private function listActivacionCom($id_seg_actividad=null,$id_mujeres_avanzando=null,
      $id_seg_activacion_com = NULL,$activo=1){

        $sql = 
        'SELECT s.*,
        sa.nombre as nombre_actividad,
        c.descripcion as nombre_colonia,
        u.nombres as usuario_creador,
        u2.nombres as usuario_ultima_mod
        FROM `seg_activacion_com` s
        LEFT JOIN mujeres_avanzando m on s.id_mujeres_avanzando = m.id
        LEFT JOIN seg_actividad sa on sa.id = s.id_seg_actividad
        LEFT JOIN seg_colonia c on c.id = s.id_seg_colonia
        LEFT JOIN usuario u on u.id = s.id_usuario_creador
        LEFT JOIN usuario u2 on u2.id = s.id_usuario_ultima_mod
        where ? ';

        //Parámetros de la sentencia
        $params = array(1);

        //Buscamos por id_seg_activacion_com           
        if($id_seg_activacion_com !=null){
                    
          $sql .= ' AND s.id = ? ';
          $params[] = $id_seg_activacion_com;

        }
        
        //Buscamos por id_seg_actividad           
        if($id_seg_actividad !=null){
                    
          $sql .= ' AND s.id_seg_actividad = ? ';
          $params[] = $id_seg_actividad;

        }

        //Buscamos por id_mujeres_avanzando
        if($id_mujeres_avanzando!=null){
   
          $sql .= ' AND s.id_mujeres_avanzando= ? ';
          $params[] = $id_mujeres_avanzando;

        }

        //Buscamos por Activo
        if($activo !=null){

          $sql .= ' AND s.activo = ? ';
          $params[] = $activo;

        }        
        /*
        echo $sql;
        print_r($params);
        */
       
        //Regresamos consulta y parámetros
        return array($sql,$params);

    }

    /**
     * Obtenemos un arreglo con el listado de los apoyos
     * @param  integer  $id_seg_actividad ID de la tabla seg_secretaria_apoyo
     * @param  integer  $id_mujeres_avanzando    ID de la tabla mujeres_avanzando
     * @param  integer  $id_seg_activacion_com      ID de la tabla seg_activacion_com
     * @param  integer $activo                  Estatus del registro
     * @return array  $l                        Listado de apoyos
     */
    public static function listadoActivacionCom($id_seg_actividad=null,$id_mujeres_avanzando=null,
      $id_seg_activacion_com = NULL,$activo=1)
    {
        list($sql,$params) = self::listActivacionCom($id_seg_actividad,$id_mujeres_avanzando,
          $id_seg_activacion_com,$activo);

        $obj = self::executar($sql,$params);
        
        $l = array();

        foreach ($obj as $key => $value):
          $l[] = $value['id_seg_actividad'];
        endforeach;

        //Regresamos resultado
        return  $l;
    }

    /**
     * Obtenemos listado de secretaría PAGINADO
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */
    public static function listaActivacionCom($id_seg_secretaria_apoyo=null,$id_mujeres_avanzando=null,
      $id_seg_activacion_com = NULL,$activo=1)
    {
        list($sql,$params) = self::listActivacionCom($id_seg_secretaria_apoyo,$id_mujeres_avanzando,
          $id_seg_activacion_com,$activo);

        return Paginador::paginar($sql,$params);           
    }


    public static function saveActivacionCom($seg_activacion_com,$id_mujeres_avanzando){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
      
        //Validamos que se nos envíe un arreglo y una secretaría a asignar
        if($id_mujeres_avanzando != NULL)
        {
          
          //Obtenemos información del formulario
          $id_seg_actividad = $seg_activacion_com['id_seg_actividad'];
          $id_seg_colonia = $seg_activacion_com['id_seg_colonia'];
          $fecha_activacion = $seg_activacion_com['fecha_activacion'];
          $observaciones = $seg_activacion_com['observaciones'];
                              
          //Pondremos con estatus de inactivo
          $updateData = array(
            'activo' => 0
          );
          
          self::getInstance()->where('id_mujeres_avanzando', $id_mujeres_avanzando);
          $results = self::getInstance()->update('seg_activacion_com', $updateData);
          
          //Mensaje de Respuesta
          $msg_no = 1;

          //Iniciamos transacción
          self::getInstance()->startTransaction();

          //Verificamos que sea un arreglo
          if(is_array($id_seg_actividad)){

            foreach($id_seg_actividad as $key => $value):
                        
            //Complementamos arreglo para guardar el servicio
            $insertData['id_mujeres_avanzando'] = $id_mujeres_avanzando;
            $insertData['id_seg_actividad'] = $value;
            $insertData['id_seg_colonia'] = $id_seg_colonia[$key];
            $insertData['observaciones'] = $observaciones[$key];
            $insertData['fecha_activacion'] = $fecha_activacion[$key];
            $insertData['fecha_creado'] = date('Y-m-d H:i:s');
            $insertData['fecha_mod'] = date('Y-m-d H:i:s');
            $insertData['id_usuario_creador'] = $_SESSION['usr_id'];            
            $insertData['id_usuario_ultima_mod'] = $_SESSION['usr_id'];
                
                //Buscamos si ya está este registro pero como inactivo
                $sql = 'SELECT 
                        id, activo 
                        from `seg_activacion_com` 
                        where 1 
                        AND id_mujeres_avanzando = ? AND id_seg_actividad = ? AND activo = 0';

                $params = array($id_mujeres_avanzando,$value);        
                $id_seg_actividad = self::executar($sql,$params);
                $id_seg_actividad = $id_seg_actividad[0];

                /*Previamente había un registro inactivo de este servicio, 
                  sólo será activado de nuevo, caso contrario, agregamos un nuevo
                  registro en la tabla*/
                if($id_seg_actividad != NULL){

                 $msg_no = self::activaActivacionCom($id_mujeres_avanzando,
                                                      $value,
                                                      $id_seg_colonia[$key],
                                                      $fecha_activacion[$key],
                                                      $observaciones[$key]);
                                    
                  /*
                  print_r($insertData);
                  exit;
                  */
                 
                }else{      

                  //Guardamos cada registro, en caso de haber error, cancelamos los registros
                  if(!self::getInstance()->insert('seg_activacion_com', $insertData)){
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
        
          //Si no hubo error al insertar algún servicio
          if($msg_no == 1){

            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();

          }
        }else{
          echo "AQUI";
          exit;
        }

        return $msg_no;        

    }     

    /**
     * Obtenemos listado de las fechas de cada actividad del usuario
     * @param  [type] $id_mujeres_avanzando ID de la mujer
     * @return [type]                       Listado de Fechas
     */
    public static function listadoFechasUsr($id_mujeres_avanzando = NULL){

      //Preparamos sentencia para buscar las fechas
      $sql = 'SELECT 
              id_seg_actividad,fecha_activacion
              from `seg_activacion_com` 
              where 1 
              AND id_mujeres_avanzando = ?';

      $params = array($id_mujeres_avanzando);

      $obj = self::executar($sql,$params);
        
      $l = array();

      foreach ($obj as $key => $value):
          $l[$value['id_seg_actividad']] = $value['fecha_activacion'];
      endforeach;

      //Regresamos resultado
      return  $l;

    }

    /**
     * Obtenemos listado de las colonias de cada actividad del usuario
     * @param  int $id_mujeres_avanzando ID de la mujer
     * @return Array                     Listado de Colonias
     */
    public static function listadoColoniaUsr($id_mujeres_avanzando = NULL){

      //Preparamos sentencia para buscar las colonias
      $sql = 'SELECT 
              id_seg_actividad,id_seg_colonia
              from `seg_activacion_com` 
              where 1 
              AND id_mujeres_avanzando = ?';

      $params = array($id_mujeres_avanzando);

      $obj = self::executar($sql,$params);
        
      $l = array();

      foreach ($obj as $key => $value):
          $l[$value['id_seg_actividad']] = $value['id_seg_colonia'];
      endforeach;

      //Regresamos resultado
      return  $l;

    }

     /**
     * Obtenemos listado de las observaciones de cada actividad del usuario
     * @param  int $id_mujeres_avanzando ID de la mujer
     * @return Array                     Listado de Colonias
     */
    public static function listadoObsUsr($id_mujeres_avanzando = NULL){

      //Preparamos sentencia para buscar las colonias
      $sql = 'SELECT 
              id_seg_actividad,observaciones
              from `seg_activacion_com` 
              where 1 
              AND id_mujeres_avanzando = ?';

      $params = array($id_mujeres_avanzando);

      $obj = self::executar($sql,$params);
        
      $l = array();

      foreach ($obj as $key => $value):
          $l[$value['id_seg_actividad']] = $value['observaciones'];
      endforeach;

      //Regresamos resultado
      return  $l;

    }
}
?>