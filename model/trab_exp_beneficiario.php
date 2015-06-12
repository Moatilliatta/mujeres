<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla trab_exp_beneficiario
 * **/ 
//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['inc_path'].'libs/CarBeneficiario.php');

class Trab_exp_beneficiario extends MysqliDb{

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
    *Activamos o desactivamos un producto/servicio del beneficiario
    *@param int $id_beneficiario_pys id de la tabla beneficiario_pys
    *
    *@return int $msg_no Mensaje
    **/

     public static function activaTrabExpBeneficiario($id = NULL)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla beneficiario_pys
        $sql = 'SELECT id, activo from `trab_exp_beneficiario` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

          $sql .= ' AND id = ?';

          //parámetros para la consulta
          $params = array($id);

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
          self::getInstance()->where('id',$id);                                                

          //datos a actualizar
          $updateData = array('activo' => $estatus);

          //Iniciamos transacción
          self::getInstance()->startTransaction();

                if(!self::getInstance()->update('trab_exp_beneficiario',$updateData)){

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

        //ERROR: Campos incompletos
        $msg_no = 2;                       

        }        
        
        return $msg_no;

    }


    /**
    * Verificamos si el beneficiario está ligado a un expediente
    *@param int $id_beneficiario id de la tabla beneficiario
    *@param int $id_trab_expediente id de la tabla trab_expediente
    *@param int $id_beneficiario_pivote 
    *@return Array Arreglo de los beneficiarios con expediente
    **/
    public static function expedientesLigados($id_beneficiario = NULL,
      $id_trab_expediente = NULL,$id_beneficiario_pivote = NULL)
    {

        $sql = 'SELECT id_beneficiario
                FROM
                (
                  SELECT 
                  id_beneficiario
                  FROM `trab_exp_beneficiario` 
                  where 1 
                  and id_trab_expediente = ?
                  and id_beneficiario = ?

                UNION

                  SELECT 
                  DISTINCTROW(e.id_beneficiario)
                  FROM `trab_exp_beneficiario` t 
                  LEFT JOIN trab_expediente e on e.id = t.id_trab_expediente
                  where 1 
                     and t.id_trab_expediente = ?
                     and e.id_beneficiario = ?  
                )as tabla
                ORDER BY id_beneficiario ';

        $params = array($id_trab_expediente,
                        $id_beneficiario,
                        $id_trab_expediente,
                        $id_beneficiario_pivote);

        /*
        echo $sql;
        print_r($params);
        */

        //Obtenemos datos
        $resultado = self::executar($sql,$params);

        //Arreglo para almacenar los datos
        $serv = array();

        //Recorremos el arreglo
        foreach($resultado as $l):

        $serv[] = $l['id_beneficiario'];

        endforeach;

        return $serv;
        
    }

    /**
    * Obtenemos los datos de un beneficiario con expediente por su id
    *@param int $id_beneficiario id de la tabla beneficiario
    *
    *@return Array datos del beneficiario
    **/
    public static function get_by_id_ben($id_beneficiario = NULL){

        $datos = NULL;

        if($id_beneficiario){
            $datos = self::getInstance()->where('id_beneficiario', $id_beneficiario)
                                        ->getOne('trab_exp_beneficiario');
        }        

        return $datos;
    }

   /**
   * Guardamos registro en la tabla 
   * @param array $centros Arreglo con los campos a guardar
   * @param int $id del Modulo a editar (opcional)
   * 
   * @return int No. de mensaje
   * */  
  public static function saveTrab($trab_exp_beneficiario,$id = null){
         
  //Variable que nos indica el mensaje generado al guardar el registro
  $msg_no = 0;
  
  //Indicamos predeterminadamente que insertaremos un registro
  $db_accion = 'insert';

  /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
    a su respectiva variable. Por ejemplo 
    $id = $_POST['id'], $nombre = $_POST['nombre']*/
  foreach($trab_exp_beneficiario as $key => $value):
    ${$key} = self::getInstance()->real_escape_string($value);
  endforeach;        

    //Obtenemos el id del usuario creador
    $id_usuario_creador = $_SESSION['usr_id'];

    /*Si no esta creada la variable activo
    predeterminadamente la guardamos = 1*/
    if(!isset($activo) ){
        $activo = 1 ;
    }

    //Campos obligatorios
    if ($id_trab_expediente && $id_beneficiario){

      $insertData = array(
                'id_trab_expediente' => $id_trab_expediente,
                'id_beneficiario' => $id_beneficiario,
                'id_usuario_creador' => $id_usuario_creador,                
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

      if(!self::getInstance()->{$db_accion}('trab_exp_beneficiario', $insertData) && $db_accion == 'insert'){

        //'Error al guardar, (sólo si se está creando registro)'
        $msg_no = 3;

        //Cancelamos los posibles campos afectados
        self::getInstance()->rollback();

      }else{

            //Campos guardados correctamente
            $msg_no = 1;

            //Obtenemos el id del aspirante
            $id_aspirante = ($db_accion == 'insert')?self::getInstance()->getInsertId():$id;

            //Guardamos beneficiarios en caso de haber
            if(count($_SESSION['arrayCarro'])){
              $msg_no = self::saveListaBen();  
            }
            
            if($msg_no == 1){
              //Guardamos campos afectados en la tabla
              self::getInstance()->commit();  
            }else{
              //Cancelamos los posibles campos afectados
              self::getInstance()->rollback();              
            }

            
            } 

      }else{

        //'Campos Incompletos'
        $msg_no = 2;            
      } 
            
    return $msg_no;

    }    
  /**
  * Obtenemos listado de beneficiarios mediante su expediente
  *@param int $id_trab_expediente Id de la tabla trab_expediente
  *
  *@return Array resultado paginado
  **/
  public function listaBeneficarios($id_trab_expediente = NULL){
    
    $sql = 'SELECT 
            e.id,
            e.id_beneficiario,
            b.nombres,
            b.paterno,
            b.materno,
            e.fecha_creado,
            e.activo,
            e.id_trab_expediente
            FROM `trab_exp_beneficiario` e
            LEFT JOIN beneficiario b on e.id_beneficiario = b.id
            where 1 ';

     //Verificamos si se quieren filtrar los activos/inactivos
        if($id_trab_expediente !== NULL){
            $sql .= ' AND e.id_trab_expediente = ? ';
            $params[] = $id_trab_expediente;
        }

        return Paginador::paginar($sql,$params);  

  }

  /**
  * Guardamos el listado de beneficiarios seleccionados mediante el "Carrito"
  *
  * @return int No. de mensaje
  **/
  public function saveListaBen(){

   //Obtenemos objeto con los artículos
   $articulos = unserialize($_SESSION['arrayCarro']);
   
   $msg_no = 1;
          
   //Iniciamos transacción
   self::getInstance()->startTransaction();

   //Recorremos arreglo
   foreach($articulos->articulo_id as $key => $value):

     //Complementamos arreglo para guardar el servicio
     $insertData['id_beneficiario'] = $value;
     $insertData['id_trab_expediente'] = $articulos->trab_expediente[$key];
     $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
     $insertData['fecha_creado'] = date('Y-m-d H:i:s');

        //Buscamos si ya está este registro pero como inactivo
        $sql = 'SELECT 
                id, activo 
                from `trab_exp_beneficiario` 
                where 1 
                AND id_trab_expediente = ? 
                AND id_beneficiario = ? AND activo = 0';

        $params = array($articulos->trab_expediente[$key],$value);

        //Verificamos el estatus del Modulo        
        $trab_expediente = self::executar($sql,$params);
        $trab_expediente = $trab_expediente[0];

        /*Previamente había un registro inactivo de esta tabla, 
        sólo será activado de nuevo, caso contrario, agregamos 
        un nuevo registro*/
        if($trab_expediente != NULL){

            $msg_no = self::activaTrabExpBeneficiario($trab_expediente['id']);

        }else{

            //print_r($insertData);
            //exit;

          //Guardamos cada registro, en caso de haber error, cancelamos los registros
          if(!self::getInstance()->insert('trab_exp_beneficiario', $insertData)){

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

        //Si no hubo error al insertar algún servicio
        if($msg_no == 1){

            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();

        }                

        return $msg_no;

    }

}
?>