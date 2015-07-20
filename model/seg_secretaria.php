<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla seg_secretaria
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');
class SegSecretaria extends Db{

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
     * Cambiamos el estatus del beneficiario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_Beneficiario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaSecretaria($id_seg_secretaria){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `seg_secretaria` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_seg_secretaria);

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
        self::getInstance()->where('id',$id_seg_secretaria);

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();        

        if(!self::getInstance()->update('seg_secretaria',$updateData)){

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
     * Obtenemos listado genérico de secretarías
     * @param  varchar  $nombre            [description]
     * @param  datetime $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return Array                     [description]
     */
    private function listSec($nombre=null,$fecha_creado=null,$activo=1){

        $sql = 
        'SELECT
        s.*
        FROM `seg_secretaria` s
        where ? ';

        //Parámetros de la sentencia
        $params = array(1);
        
         //Buscamos por nombre           
        if($nombre !=null){
                    
          $sql .= ' AND s.nombre like ? ';
          $params[] = '%'.$nombre.'%';

        }

        //Apellido paterno
        if($fecha_creado !=null){
          //echo $paterno;
          //exit;
            
          $sql .= ' AND s.fecha_creado = ? ';
          $params[] = $fecha_creado;

        }

        //Apellido materno
        if($activo !=null){

          $sql .= ' AND s.activo = ? ';
          $params[] = $activo;

        }        
        
        //Regresamos consulta y parámetros
        return array($sql,$params);

    }

    /**
     * Obtenemos un arreglo con el listado de las secretarías
     * @param  [type]  $nombre       [description]
     * @param  [type]  $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return [type]                     [description]
     */
    public static function listadoSec($nombre=null,$fecha_creado=null,$activo=1){

        list($sql,$params) = self::listSec($nombre,$fecha_creado,$activo);

        //Regresamos resultado
        return  self::executar($sql,$params);
    }

    /**
     * Obtenemos listado de secretaría PAGINADO
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */
    public static function listaSec($nombre=null,$fecha_creado=null,$activo=1)
    {
        list($sql,$params) = self::listSec($nombre,$fecha_creado,$activo);

        return Paginador::paginar($sql,$params);           
    
    }


    public static function saveSecretaria($seg_secretaria,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo donde contendremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';
      
        //Obtenemos el id del usuario creador
        $id_usuario = $_SESSION['usr_id'];

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

            //Creamos variable CURP        $curp = "";

        foreach($seg_secretaria as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;

        //Campos obligatorios
        if($nombre)
        {                       
                         
                //Quitamos vueltas de carro
                $desc_ubicacion = trim(str_replace("\\r\\n"," ",$desc_ubicacion));

                $insertData = array(
                'nombre' => mb_strtoupper(trim($nombre),"UTF-8"),
                'descripcion' => mb_strtoupper(trim($descripcion),"UTF-8"),
                'fecha_creado' => date('Y-m-d'),
                'id_usuario_creador' => $id_usuario,
                'id_usuario_ultima_mod' => $id_usuario,
                'activo' => $activo
                 );                            

                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

                    //Si recibimos id para editar
                    if(intval($id)>0){

                        //Indicamos que haremos un update
                        $db_accion = 'update';
                        
                        //Al editarse no se guardará el usuario creador                    
                        unset($insertData['id_usuario_creador']);
                    
                        //Agregamos condición para indicar qué id se actualiza
                        self::getInstance()->where('id',$id);                                        

                    }

                    //print_r($insertData);
                    //exit;

                    //Iniciamos transacción
                    self::getInstance()->startTransaction();                  

                    if(! self::getInstance()->{$db_accion}('seg_secretaria', $insertData))
                    {
                        
                        /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                        NO se está guardando el registro por tener campos incompletos o incorrectos*/
                        $msg_no = ($db_accion == 'update')?  14 : 3;                    
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();                        

                    }else{
                            //Campos guardados correctamente
    						$msg_no = 1;                                            
                     
                            if($msg_no == 1){
                                self::getInstance()->commit(); 
                                }else{
                                    self::getInstance()->rollback();     
                                }
                         }     
                }

            else{

            //'Campos Incompletos'
            $msg_no = 2;             

            }
                           
        //Regresamos el mensaje, CURP y el id generado/modificado
        return $msg_no;

    }     
}
?>