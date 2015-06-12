<?php
/**
 * Clase que nos permite administrar lo relacionado al modulo CallCenter
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['model_path'].'callcenter_h.php');

class CallCenterGrupo extends MysqliDb{
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
     * Cambiamos el estatus del beneficiario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_Beneficiario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaCallCenterGrupo($id_grupocallcenter){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `callcenter_grupo` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_grupocallcenter);

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
        self::getInstance()->where('id',$id_grupocallcenter);

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();        

        if(!self::getInstance()->update('callcenter_grupo',$updateData)){

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
    
    public function listaCallCenterGrupo($descripcion=null,$fecha_instalacion=null,$activo=1){

       $sql = 
       'SELECT
        cg.id,
        cg.activo,
        cg.nombre as nombre_grupo,
        c.descripcion as caravana,
        cf.estatus as filtro 
        FROM callcenter_grupo cg
        LEFT JOIN caravana c on c.id = cg.id_caravana
        LEFT JOIN callcenter_filtro cf on cf.id = cg.id_callcenter_filtro
        where ? ';

        //Parámetros de la sentencia
        $params = array(1);
        
       /*
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
      */
        //Regresamos consulta y parámetros
        return Paginador::paginar($sql,$params); 

    }

  
    /** Guardamos datos  **/
    
     public static function saveCallCenterGrupo($grupocallcenter,$id_edicion = null){
       // print_r($grupocallcenter);
       // exit;

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo donde contendremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id_edicion = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($grupocallcenter as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach; 
        
        //Obtenemos el id del usuario creador
        $id_usuario = $_SESSION['usr_id'];
        
          if(!isset($activo) ){
            $activo = 1 ;            
        }        
        
        //Campos obligatorios
        if($nombre && $id_caravana)
        {                       
            
                $insertData = array(
                'nombre' => $nombre,
                'id_caravana' => $id_caravana,
                'activo' => $activo,
                'id_callcenter_filtro' => $id_filtro,
                'id_tipo_lugar' => $id_tipo_lugar,
                'id_usuario_creador' => $id_usuario,
                'id_usuario_ultima_mod' => $id_usuario,
                'fecha_creado' => date('Y-m-d')
                 );                            

                //Quitamos del arreglo los valores vacíos
                $insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

                    
                    //Si recibimos id para editar
                    if(intval($id_edicion)>0){

                        //Indicamos que haremos un update
                        $db_accion = 'update';
                        
                        //Al editarse no se guardará el usuario creador, fecha_creado                    
                        unset($insertData['id_usuario_creador']);
                        unset($insertData['fecha_creado']); 

                        //Agregamos condición para indicar qué id se actualiza
                        self::getInstance()->where('id',$id_edicion);                                        

                    }                                                           
                   
                    //Iniciamos transacción
                    self::getInstance()->startTransaction();               

                    $nuevo_id = self::getInstance()->{$db_accion}('callcenter_grupo', $insertData);

                    if(! $nuevo_id ){
                        
                        /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                        NO se está guardando el registro por tener campos incompletos o incorrectos*/
                        $msg_no = ($db_accion == 'update')?  14 : 3;                    
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();                        

                        }else{

                        //Campos guardados correctamente
						$msg_no = 1;
                        
                        //Realizamos transacción
                        self::getInstance()->commit(); 
                        
                        include_once($_SESSION['model_path'].'callcenter.php');
                        
                        $id_caravana  = $_POST['id_caravana'];
                        $id_filtro = $_POST['id_filtro'];
                        $lista_CallCenter = CallCenter::listaCallCenter($id_caravana,$id_filtro);
                        //print_R($lista_CallCenter);
                        //exit;
                        foreach($lista_CallCenter as $key => $value):
                    
                           $datos = array('id_mujeres_avanzando'=>$value['id'],'id_grupo'=>$nuevo_id 
                                          ); 
                           CallCenter::saveCallCenter($datos);               
                    
                        endforeach;        
    

                        }     

        }else{

            //'Campos Incompletos'
            $msg_no = 2;             

        }
                          
        //Regresamos el mensaje
        return $msg_no;

    } 

  }
?>