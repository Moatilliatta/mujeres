<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario
 * **/ 

//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['model_path'].'usuario_caravana.php');
include_once($_SESSION['model_path'].'usuario_secretaria.php');

class Usuario extends Db{
    
    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}

    /**
     * Buscamos un operador (y su grupo relacionado)
     * @param  [type] $id_usuario [description]
     * @param  [type] $id_grupo   [description]
     * @return [type]             [description]
     */
    public function get_by_id($usuario_id,$activo = 1){
        
        $usuario = self::getInstance()->where('id', $usuario_id)
                                     ->where('activo', $activo )
                                     ->getOne('usuario');
        return $usuario;
    }

    /**
     * Cambiamos el estatus del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaUsuario($id_usuario){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `usuario` where id = ?'; 
        
        //Parámetro(s) para la edición
        $params = array($id_usuario);

        //Verificamos el estatus del usuario        
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
        self::getInstance()->where('id',$id_usuario);                                                
        //datos a actualizar
        $updateData = array('activo' => $estatus);

        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('usuario',$updateData)){
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

        return $msg_no;
    } 
    
     /**
     * Obtenemos listado de los usuarios. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param string $busqueda Campo a buscar
     * @param string $tipo_filtro Tipo de filtro para realizar la búsqueda
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    private static function listUser($busqueda=NULL,$tipo_filtro=NULL,$activo = NULL){
    
    //Preparamos sentencia
        $sql = 
        'SELECT 
        concat(nombres,?,paterno,?,materno) as nombre_completo,
        p.nombre as nombre_perfil,
        e.nombre as estatus,
        u.*
        FROM `usuario` u
        left join perfil p on u.id_perfil = p.id
        left join estatus e on e.valor = u.activo
        where 1
        ';
        
        //Parámetros de la sentencia
        $params = array(' ',' ');
        
        //filtro de busqueda
        if ($busqueda && $tipo_filtro){
            
            switch($tipo_filtro){
                
                case 'nombres':
                     $sql .= ' AND concat(nombres,?,paterno,?,materno) LIKE ? ';
                    $params[] =' ';
                    $params[] =' ';
                    $params[] = '%'.$busqueda.'%'; 
                    break; 
                case 'usuario':
                 $sql .= ' AND usuario LIKE ? ';
                 $params[] = '%'.$busqueda.'%';       
                    break;
            }
            
            
            
           
        }

        //Verificamos si se quieren filtrar los activos/inactivos ???????
        if($activo !== NULL){
            $sql .= ' and u.activo = ?';
            $params[] = $activo;
        }
        
        return array($sql,$params);
    }
    
     /**
     * Obtenemos listado de los usuarios SIN PAGINAR. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param string $busqueda Campo a buscar
     * @param string $tipo_filtro Tipo de filtro para realizar la búsqueda
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    
    public static function listadoUsuarios($busqueda=NULL,$tipo_filtro=NULL,$activo = NULL){
        
        //Obtenemos parámetros
        list($sql,$params) = self::listUser($busqueda,$tipo_filtro,$activo);
        
        //Regresamos listado de usuarios
        return self::executar($sql,$params);
                        
    }
    
    /**
     * Obtenemos listado de los usuarios PAGINADO. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param string $busqueda Campo a buscar
     * @param string $tipo_filtro Tipo de filtro para realizar la búsqueda
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
     ///////////////////////////////////////////////////////////////////////////
    public static function listaUsuarios($busqueda=NULL,$tipo_filtro=NULL,$activo = NULL){
    
        //Obtenemos parámetros
        list($sql,$params) = self::listUser($busqueda,$tipo_filtro,$activo);
        
        //Regresamos resultado paginado     
        return Paginador::paginar($sql,$params);      
    }

    /**
     * Guardamos registro en la tabla Usuario
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveUsuario($usuario_data,$id = null)
    {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';        

        
        /*Obtenemos cada una de las variables enviadas vía POST 
        y las asignamos a su respectiva variable. Por ejemplo:
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($usuario_data as $key => $u):

        ${$key} = (is_array($u))? $u : self::getInstance()->real_escape_string($u);

        endforeach;
        //si no esta creada la variable activo predeterminadamente la guardamos como uno
        if(!isset($activo) ){
            $activo = 1 ;
            
        }        

        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `usuario` where usuario = ? ';
        $params = array($usuario);
        
        if($id !=null){
            $sql.=' and id not in (?)';
            $params[] = $id;
            
        }              

        //Verificamos que no se dupliquen
        $duplicado = self::getInstance()->rawQuery($sql,$params);                
        
        if(count($duplicado)>0){
            $msg_no = 6;
            //Nombre de usuario duplicado
        }else{
            
            //Falta validar SQL injection 
            //Obtenemos el id del usuario creador
             $id_usuario_creador = $_SESSION['usr_id'];

            //Valida Correo, si no cumple con el filtro invalidamos correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                //Correo inválido
                return 5;
            }                        
          
            //Campos obligatorios
            if ( ($usuario || $id != null ) && ($clave || $id != null ) && $nombres 
                && $paterno && $correo) 
            {
            //Verificamos que la contraseña sea igual a la confirmación
            if($clave == $clave_conf){

                $insertData = array(
                'usuario' => $usuario,                
                'nombres' => mb_strtoupper(trim($nombres),"UTF-8"),
                'paterno' => mb_strtoupper(trim($paterno),"UTF-8"),
                'materno' => mb_strtoupper(trim($materno),"UTF-8"),
                'activo' => $activo,
                /*'id_perfil' => $id_perfil,*/
                'id_punto_rosa' => $id_punto_rosa,
                'id_usuario_creador' => $id_usuario_creador,
                'correo' => $correo,
                'fecha_creado' => date('Y-m-d H:i:s')
                );
                
                if($clave != NULL){
                    $insertData['clave'] = Permiso::encripta($clave);                    
                }

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
                
                        if(! self::getInstance()->{$accion}('usuario', $insertData)){
                            //'Error al guardar, NO se guardo el registro'
                            $msg_no = 3;
                            
                            //Cancelamos los posibles campos afectados
                            self::getInstance()->rollback();  
                             
                        }else{

                            //Campos guardados correctamente
                            $msg_no = 1;                    

                            //Guardamos los campos afectados en la tabla
                            self::getInstance()->commit();

                            //Guardamos las caravanas
                            //if($caravana != NULL){
                                $msg_no = UsuarioCaravana::saveUsuarioCaravana($caravana,$id_usuario);    
                            //}                                                        
                            
                            //Dependiendo el mensaje
                            if($msg_no == 1){
                                
                            }else{
                                //Cancelamos los posibles campos afectados
                                self::getInstance()->rollback();
                            }                         

                            //Guardamos las secretarías
                            $msg_no = UsuarioSecretaria::saveUsuarioSecretaria($secretaria,$id_usuario);                                  
                            
                            //Dependiendo el mensaje
                            if($msg_no == 1){
                                
                            }else{
                                //Cancelamos los posibles campos afectados
                                self::getInstance()->rollback();
                            }      
                        } 
                    }else{                
                        //'No coinciden las claves'
                        $msg_no = 4;                 
                    }
                    
                }else{
                    //'Campos Incompletos'
                    $msg_no = 2;             
                }
            }
        //Regresamos mensaje
        return $msg_no;
    }
    
    /**
     * Actualizamos la clave de un usuario en la tabla Usuario
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveClaveUsuario($usuario,$id)
    {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        
        /*Obtenemos cada una de las variables enviadas vía POST 
        y las asignamos a su respectiva variable. Por ejemplo:
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
       //////////////////////////////////////////////////////////
        foreach($usuario as $key => $u):
        ${$key} = $u;
        endforeach;
        
        //Si el usuario cambia la clave de otro, verificamos que tenga permiso
        if(!isset($clave_actual) || $clave_actual == '1'){
            $clave_actual=Permiso::accesoAccion("cambia_clave","usuario","usuario");        
        }
        
        //verificar clave actual con la clave de la tabla usuarios
        $id_usuario = $_SESSION['usr_id'];
        
        //Incluimos archivo de permiso
        include_once($_SESSION['inc_path'].'libs/Permiso.php');
        
        //Encriptamos clave para comparar
        $clave_encriptada=Permiso::encripta($clave_actual); 
        
        //Realizamos la comparación de la clave enviada con la de la tabla
        if(isset($_POST['clave_actual']) && !is_array($clave_actual)){                    

            $sql = 'SELECT  clave from usuario where id = ?';
            $params = array($id_usuario);    

            //Obtenemos registro
            $usr = self::getInstance()->rawQuery($sql,$params);
            $usr = $usr[0];
            
            //echo 'clave_actual'.$clave_encriptada.'<br> clave_bd'.$usr["clave"].'<br> clave_nueva'.$clave;
            //exit;

            if($clave_encriptada !=$usr["clave"]){              
              //clave incorecta no se permite hacer cambio de contraseña  
              return 10;                 
            }
        }
        
            //Campos obligatorios
            //se puede dar el caso que un admin cambie la clave de otros usuarios
            if ($clave && $clave_conf && $clave_actual && $id){
                
                //Verificamos que la contraseña sea igual a la confirmación
                if($clave == $clave_conf ){
    
                    $insertData = array(
                    'clave' => (Permiso::encripta($clave)),
                    );
                    
                    //Agregamos condición para indicar qué id se actualiza
                    self::getInstance()->where('id',$id);                                        
                    
                    //Iniciamos transacción
                    self::getInstance()->startTransaction();
                    
                        if(! self::getInstance()->update('usuario', $insertData)){
                                //'Error de clave al buscar'
                                $msg_no = 10; 
                                
                                //Cancelamos los posibles campos afectados
                                self::getInstance()->rollback();     
                                
                            }else{
                                //Cambio de clave correcto
                                $msg_no = 11;            
                                
                                //Guardamos los campos afectados en la tabla
                                self::getInstance()->commit();         
                            } 
                            
                        }else{                
                            //'No coinciden las claves'
                            $msg_no = 4;                 
                        }
                    
            }else{
                    //'Campos Incompletos'
                    $msg_no = 2;             
                }
            
        //Regresamos mensaje
        return $msg_no;
    }
}
?>