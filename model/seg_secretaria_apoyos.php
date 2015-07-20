<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla seg_secretaria_apoyos
 * **/ 

//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class SegSecretariaApoyos extends Db{

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
    public static function get_by_id($id_caravana){

        $datos = self::getInstance()->where('id', $id_caravana)
                                    ->getOne('caravana');

        return $datos;
    }

   /**
    *Activamos o desactivamos un apoyo de cierta secretaría del grupo
    *@param int $id_seg_apoyo id del apoyo
    *@param int $id_seg_secretaria id de la secretaría
    *
    *@return int $msg_no Mensaje
    **/
    public static function activaSecApoyo($id_seg_apoyo,$id_seg_secretaria,$id = NULL){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;


        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla seg_secretaria_apoyos
        $sql = 'SELECT id, activo from `seg_secretaria_apoyos` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

            $sql .= ' AND id = ?';

            //parámetros para la consulta
            $params = array($id);

        }else{

            $sql .= ' AND id_seg_secretaria = ? AND id_seg_apoyo = ?'; 

            //parámetros para la consulta
            $params = array($id_seg_secretaria,$id_seg_apoyo);

        }        

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
        $id_seg_secretaria_apoyos = $registro['id'];

        //Preparamos update
        self::getInstance()->where('id',$id_seg_secretaria_apoyos);         
        
        //datos a actualizar
        $updateData = array('activo' => $estatus,
                            'id_usuario_ultima_mod' => $_SESSION['usr_id']);

        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('seg_secretaria_apoyos',$updateData)){

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
     * Obtenemos listado genérico de apoyos de las secretarías
     * @param  varchar  $id_seg_secretaria            [description]
     * @param  datetime $id_seg_apoyo [description]
     * @param  integer $activo            [description]
     * @return Array                     [description]
     */
    private function listSecApoyo($id_seg_secretaria=null,$id_seg_apoyo=null,
      $id_seg_secretaria_apoyos = NULL,$activo=1){

        $sql = 
         'SELECT 
          s.id,
          s.id_seg_apoyo as id_seg_apoyo,
          a.nombre as nombre_apoyo 
          FROM `seg_secretaria_apoyos` s 
          LEFT JOIN seg_apoyo a on s.id_seg_apoyo = a.id
          where  ? ';

        //Parámetros de la sentencia
        $params = array(1);

        //Buscamos por id_seg_secretaria_apoyos           
        if($id_seg_secretaria_apoyos !=null){
                    
          $sql .= ' AND s.id = ? ';
          $params[] = $id_seg_secretaria_apoyos;

        }
        
        //Buscamos por id_seg_secretaria           
        if($id_seg_secretaria !=null){
                              
          if(is_array($id_seg_secretaria)){

            //Variable para ir guardando las secretarías
            $sec = "";

            //Recorremos listado de secretaría, agregamos una por una
            foreach ($id_seg_secretaria as $key => $value) {
                $sec .= $value.',';
            }
            //Quitamos última coma
            $sec = substr($sec, 0,-1);
            
            if($sec){
              $sql .=  ' AND s.id_seg_secretaria IN('.$sec.') ';
            }            

          }else{
            
            $sql .= ' AND s.id_seg_secretaria = ? ';
            $params[] = $id_seg_secretaria;            
          }
         
        }

        //Apellido paterno
        if($id_seg_apoyo !=null){
   
          $sql .= ' AND s.id_seg_apoyo = ? ';
          $params[] = $id_seg_apoyo;

        }

        //Apellido materno
        if($activo !=null){

          $sql .= ' AND s.activo = ? ';
          $params[] = $activo;

        }        
        /*
        echo $sql;
        print_r($params);
        */
       $sql .= ' ORDER BY s.id_seg_apoyo';
        //Regresamos consulta y parámetros
        return array($sql,$params);

    }

    /**
     * Obtenemos un arreglo con el listado de las secretarías/apoyos
     * @param  [type]  $nombre       [description]
     * @param  [type]  $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return [type]                     [description]
     */
    public static function listaSecApoyoId($id_seg_secretaria=null,$id_seg_apoyo=null,
      $id_seg_secretaria_apoyos = NULL,$activo=1)
    {
        list($sql,$params) = self::listSecApoyo($id_seg_secretaria,$id_seg_apoyo,
          $id_seg_secretaria_apoyos,$activo);

        $obj = self::executar($sql,$params);

        $l = array();

        foreach ($obj as $key => $value):
          $l[] = $value['id_seg_apoyo'];
        endforeach;
        
       
        //Regresamos resultado
        return  $l;
    }

    /**
     * Obtenemos un arreglo con el listado de las secretarías
     * @param  [type]  $nombre       [description]
     * @param  [type]  $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return [type]                     [description]
     */
    public static function listadoSecApoyo($id_seg_secretaria=null,$id_seg_apoyo=null,
      $id_seg_secretaria_apoyos = NULL,$activo=1)
    {
        list($sql,$params) = self::listSecApoyo($id_seg_secretaria,$id_seg_apoyo,
          $id_seg_secretaria_apoyos,$activo);

        $obj = self::executar($sql,$params);
        
        /*
        
        $l = array();

        foreach ($obj as $key => $value):
          $l[$value['id']] = $value['nombre_apoyo'];
        endforeach;
        */
       
        //Regresamos resultado
        return  $obj;
    }

    /**
     * Obtenemos listado de secretaría PAGINADO
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */
    public static function listaSecApoyo($id_seg_secretaria=null,$id_seg_apoyo=null,
      $id_seg_secretaria_apoyos = NULL,$activo=1)
    {
        list($sql,$params) = self::listSecApoyo($id_seg_secretaria,$id_seg_apoyo,
          $id_seg_secretaria_apoyos,$activo);

        return Paginador::paginar($sql,$params);           
    }


    public static function saveSecApoyo($seg_apoyos,$id_seg_secretaria){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
      
        //Validamos que se nos envíe un arreglo y una secretaría a asignar
        if($id_seg_secretaria != NULL)
        {

          //Pondremos con estatus de inactivo
          $updateData = array(
            'activo' => 0
          );
          
          self::getInstance()->where('id_seg_secretaria', $id_seg_secretaria);
          $results = self::getInstance()->update('seg_secretaria_apoyos', $updateData);
          
          //Mensaje de Respuesta
          $msg_no = 1;

          //Iniciamos transacción
          self::getInstance()->startTransaction();

          if(is_array($seg_apoyos)){

            foreach($seg_apoyos as $key => $value):

            //Complementamos arreglo para guardar el servicio
            $insertData['id_seg_secretaria'] = $id_seg_secretaria;
            $insertData['id_seg_apoyo'] = $value;
            $insertData['fecha_creado'] = date('Y-m-d H:i:s');
            $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
                
                //Buscamos si ya está este registro pero como inactivo
                $sql = 'SELECT 
                        id, activo 
                        from `seg_secretaria_apoyos` 
                        where 1 
                        AND id_seg_secretaria = ? AND id_seg_apoyo = ? AND activo = 0';

                $params = array($id_seg_secretaria,$value);        
                $seg_apoyos = self::executar($sql,$params);
                $seg_apoyos = $seg_apoyos[0];

                /*Previamente había un registro inactivo de este servicio, 
                  sólo será activado de nuevo, caso contrario, agregamos un nuevo
                  registro en la tabla*/
                if($seg_apoyos != NULL){

                  $msg_no = self::activaSecApoyo($value,$id_seg_secretaria);

                }else{      

                  //Guardamos cada registro, en caso de haber error, cancelamos los registros
                  if(!self::getInstance()->insert('seg_secretaria_apoyos', $insertData)){
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
}
?>