<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Caravana
 * **/ 

//Inclumos librería de Paginador

include_once('../../inc/libs/Paginador.php');
include_once($_SESSION['model_path'].'servicio_caravana.php');
class Caravana extends MysqliDb{

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
    public static function activaCaravana($id_caravana){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `caravana` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_caravana);

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
        self::getInstance()->where('id',$id_caravana);

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();        

        if(!self::getInstance()->update('caravana',$updateData)){

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

    private function listCaravana($descripcion=null,$fecha_instalacion=null,$activo=1){

        $sql = 
        'SELECT
        b.id,
        b.descripcion,
        b.fecha_instalacion,
        b.direccion,
        b.observaciones,
        e.nombre as estatus,
        b.longitud,
        b.latitud,
        b.activo,
        t.tipo_importacion
        FROM `caravana` b
        inner join estatus e on b.activo = e.valor
        INNER JOIN tipo_lugar t on t.id = b.id_tipo_lugar
        where ? ';

        //Parámetros de la sentencia
        $params = array(1);
        
        /*
        //Filtro de búsqueda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){

             switch($tipo_filtro){
                
               case 'descripcion':
                 $sql .= ' AND b.descripcion = ? ';
                 $params[] = '%'.$busqueda.'%';
                    break;
                   
                    break; 
                case 'fecha_instalacion':
                 $sql .= ' AND b.fecha_instalacion = ? ';
                 $params[] = $busqueda;       
                    break;
                case 'status':
                 $sql .= ' AND b.status = ? ';
                 $params[] = $busqueda;
                    break;
                
                    
            }

        }*/
        
         //Buscamos nombre propio           
        if($descripcion !=null){
                    
          $sql .= ' AND b.descripcion like ? ';
          $params[] = '%'.$descripcion.'%';

        }

        //Apellido paterno
        if($fecha_instalacion !=null){
          //echo $paterno;
          //exit;
            
          $sql .= ' AND b.fecha_instalacion = ? ';
          $params[] = $fecha_instalacion;

        }

        //Apellido materno
        if($activo !=null){

          $sql .= ' AND b.activo = ? ';
          $params[] = $activo;

        }        

        //Regresamos consulta y parámetros
        return array($sql,$params);

    }

    public static function listadoCaravana($descripcion=null,$fecha_instalacion=null,$activo=1){

        list($sql,$params) = self::listCaravana($descripcion,$fecha_instalacion,$activo);

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
    public static function listaCaravana($descripcion=null,$fecha_instalacion=null,$activo=1)
    {
        list($sql,$params) = self::listCaravana($descripcion,$fecha_instalacion,$activo);

        return Paginador::paginar($sql,$params);           
    
    }


    public static function saveCaravana($caravana,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo donde contendremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        //Variable para verificar si es homónimo un mujeres_avanzando
        $esHomonimo = NULL;

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

            //Creamos variable CURP        $curp = "";

        foreach($caravana as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;

        //echo $indigena;
        //exit;

         //Comprobamos duplicidad y validez de CURP
                                

            //Campos obligatorios
            if($descripcion && $fecha_instalacion && $direccion &&  /*$genero*/  $activo )
            {                       
            
             /*
             if($fecha_instalacion !=null) {
                              
                $dias = Fechas::anios_meses_dias(
                                    Fechas::invierte_fecha($fecha_instalacion),
                                    Fechas::invierte_fecha(date('Y/m/d')));

                
                
             } */ 

                //Quitamos vueltas de carro
                $desc_ubicacion = trim(str_replace("\\r\\n"," ",$desc_ubicacion));

                $insertData = array(
                'descripcion' => mb_strtoupper(trim($descripcion),"UTF-8"),
                'fecha_instalacion' => $fecha_instalacion,
                'direccion' => mb_strtoupper(trim($direccion),"UTF-8"),
                'activo' => $activo,
                'id_tipo_lugar' => $id_tipo_lugar,
                'observaciones' => mb_strtoupper(trim($observaciones),"UTF-8"),
                'longitud' => $longitud,
                'latitud' => $latitud,
                'fecha_alta' => date('Y-m-d')
                 );                            

                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

                //Si no es indígena, los campos comunidad_indigena y dialecto serán nulos
               

                //Verificamos homónimos
                /*$homonimo = self::verificaHomonimo(
                                $nombres,
                                $paterno,
                                $materno,
                                $fecha_nacimiento,
                                $id_municipio_nacimiento,
                                $id_cat_estado,
                                $id);*/

                //echo $homonimo.' - '.$esHomonimo.' - '.$id;
                //exit;

                //Si encontró homónimo y no se ha confirmado que lo es
                /*if($homonimo === true && $esHomonimo != 'SI' && $id == NULL){

                    //mostramos mensaje de homónimo
                    $msg_no = 15; 
                  
                }else{*/

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

                    if(! self::getInstance()->{$db_accion}('caravana', $insertData)){
                        
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
    
    public static function saveServicioCaravana($servicio_caravana,$id = null){
    
    
    //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        
        foreach($servicio_caravana as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;
        
        if($id != NULL && $id_servicio !=null && $stock !=null){
            
            $datos_serv_caravana =  array('id_caravana'=>$id,'id_servicio'=>$id_servicio,'stock'=>$stock);
            
            $msg_no = ServicioCaravana::UpdateServicios($datos_serv_caravana);
                
        }
        
        return $msg_no;               
   }
}
?>