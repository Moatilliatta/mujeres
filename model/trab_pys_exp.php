<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario
 * **/ 
 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Trab_pys_exp extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}

    /**
     * Cambiamos el estatus del producto/servicio seleccionado
     * 1 = Activo, 0 = Inactivo
     * @param int $id_trab_pys_exp Producto/servicio a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */

    public static function activaAccion($id_trab_pys_exp){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla trab_pys_exp
        $sql = 'SELECT activo from `trab_pys_exp` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_trab_pys_exp);                

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
        self::getInstance()->where('id',$id_trab_pys_exp);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('trab_pys_exp',$updateData)){
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
     * Obtenemos listado de los aspirantes que tienen un expediente 
     * @param string $nombre Nombre de Comunidad a buscar
     * @return array Resultado de la consulta
     * */
    public static function listadoHistorial($id_trab_expediente){
        
        $sql = 
        'SELECT 
        p.nombre as programa,
        e.justificacion,
        e.fecha_ultima_mod,
        u.nombres as usuario,
        case when (e.activo = 1) THEN ?
             when (e.activo = 0) THEN ?
        end as estatus
        FROM `trab_pys_exp` e
        LEFT JOIN producto_servicio p on p.id = e.id_producto_servicio
        LEFT JOIN usuario u on u.id = e.id_usuario_ultima_mod
        where 1 ';

        $params = array('ACTIVO','INACTIVO');

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND e.id_trab_expediente = ? ';
            $params[] = $id_trab_expediente;
        }
        
       return Paginador::paginar($sql,$params);  

    }

    /**
     * Guardamos registro en la tabla trab_pys_exp
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveTrab_pys_exp($trab_pys_exp,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($trab_pys_exp as $key => $b):
         ${$key} =  self::getInstance()->real_escape_string($b);
         //${$key} = ($b);
         endforeach;
          
        //Obtenemos el id del usuario con sesión activa
        $id_usuario = $_SESSION['usr_id'];

        //Campos obligatorios
        if ($id_producto_servicio && $id_trab_expediente)
        {

            //Quitamos vueltas de carro
            $justificacion = trim(str_replace("\\r\\n"," ",$justificacion));

            $insertData = array(                
             'id_producto_servicio' => $id_producto_servicio,
             'id_trab_expediente' => $id_trab_expediente,
             'justificacion' => $justificacion,
             'fecha_creado' => date('Y-m-d H:i:s'),
             'id_usuario_ultima_mod' => $id_usuario,
             'activo' => 1
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
                
                //Obtenemos el registro (de existir) del programa activo más reciente recién creación
                self::getInstance()->where('id_trab_expediente',$id_trab_expediente)
                                       ->where('activo', 1);
                self::getInstance()->orderBy("id","DESC");
                $registro = self::getInstance()->getOne('trab_pys_exp');

                //Iniciamos transacción
                self::getInstance()->startTransaction();
                
                if(! self::getInstance()->{$db_accion}('trab_pys_exp', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3;
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();                    
                    
                }else{

                    //Campos guardados correctamente
                    $msg_no = 1;
                    
                    //Si tenemos mensaje exitoso procedemos a guardar
                    if($msg_no == 1){
                        //Guardamos campos afectados en la tabla
                        self::getInstance()->commit();    
                    }                    

                    //Si un expediente tenía un programa registrado previamente
                    if($registro != NULL){

                        //Procedemos a quitarle el estatus de activo
                        $insertData = array('activo'=>0);

                        self::getInstance()->where('id',$registro['id'])
                                           ->where('activo', 1);

                        //En caso de no guardar, procedemos a cancelar la transacción
                        if(!self::getInstance()->update('trab_pys_exp', $insertData)){

                        //'Error al guardar, NO se guardo el registro'
                        $msg_no = 3;

                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();
                        
                        }else{
                            
                            //Guardamos campos afectados en la tabla
                            self::getInstance()->commit();           
                        }

                    }                                        
                    
                } 

        }else{
            //'Campos incompletos'
            $msg_no = 2;             
        }
        return $msg_no;
    }


    /**
    * Quitamos los otros programas activos
    *@param int $id_trab_pys_exp Id de la tabla trab_pys_exp
    *
    *@return int $mensaje Mensaje de respuesta
    */
    private static function quitaResto($id_trab_pys_exp = 0){

        //Mensaje de respuesta
        $msg_no = 0;

        if($id_trab_expediente > 0){

            //Solo dejaremos activo el de recién creación, los demás los quitamos
            $insertData = array('activo'=>0);

            //Tiene expediente, así que actualizaremos la tabla
            self::getInstance()->where('id_trab_expediente',$id_trab_expediente)
                               ->where('activo', 1);

            //Iniciamos transacción
            self::getInstance()->startTransaction();

            if(!self::getInstance()->update('trab_pys_exp', $insertData)){

                //'Error al guardar, NO se guardo el registro'
                $msg_no = 3;

                //Cancelamos los posibles campos afectados
                self::getInstance()->rollback();
                            
                            /*
                            echo $id_nuevo.' - '.$id_trab_expediente.' AQUI ES '.$db_accion.' - '.count($tiene_exp);
                            print_r($tiene_exp);
                            echo $tiene_exp;
                            exit;      
                            */
               }else{

                //Campos guardados correctamente
                $msg_no = 1;

                //Guardamos campos afectados en la tabla
                self::getInstance()->commit();
            }

        }else{
            $msg_no = 2;
        } 

        return $msg_no;
    }
}