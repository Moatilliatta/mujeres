<?php
/**
 * Clase que nos permite administrar lo relacionado al modulo CallCenter
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['model_path'].'callcenter_h.php');
include_once($_SESSION['model_path'].'callcenter.php');

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
    public static function activaCallCenterGrupo($id_grupocall_center){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Submódulo
        $sql = 'SELECT activo from `callcenter_grupo` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_grupocall_center);

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
        self::getInstance()->where('id',$id_grupocall_center);

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
    
    public function listaCallCenterGrupo($nombre=null,$fecha_instalacion=null,
        $id_caravana = NULL,$activo=1){

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
        
       
         //Buscamos nombre            
        if($nombre !=null){
                    
          $sql .= ' AND cg.nombre LIKE ? ';
          $params[] = '%'.$nombre.'%';

        }

        //Fecha Instalación
        if($fecha_instalacion !=null){
            
          $sql .= ' AND cg.fecha_creado = ? ';
          $params[] = $fecha_instalacion;

        }

        if($id_caravana !=null){
            
          $sql .= ' AND cg.id_caravana = ? ';
          $params[] = $id_caravana;

        }

        //Apellido materno
        if($activo !=null){

          $sql .= ' AND cg.activo = ? ';
          $params[] = $activo;

        }        
      
        //Regresamos consulta y parámetros
        return Paginador::paginar($sql,$params); 

    }

    /**
     * Guardamos registro
     * @param  [type] $grupocall_center [description]
     * @param  [type] $id_edicion       [description]
     * @return [type]                   [description]
     */
    public static function saveCallCenterGrupo($grupocall_center,$id_edicion = null){
       // print_r($grupocall_center);
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
        foreach($grupocall_center as $key => $value):

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
                'id_callcenter_filtro' => $id_callcenter_filtro,
                'id_seg_capacitacion' => $id_seg_capacitacion,
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

            if(!$nuevo_id ){

                /*Si se hace un update, no se guardaron campos nuevos, 
                caso contrario NO se está guardando el registro por tener 
                campos incompletos o incorrectos*/
                
                $msg_no = ($db_accion == 'update')?  14 : 3;
                        
                //Cancelamos los posibles campos afectados
                self::getInstance()->rollback();

            }else{

                //Campos guardados correctamente
				$msg_no = 1;
                        
                //Realizamos transacción
                self::getInstance()->commit();
                
                //Si recibimos id para editar
                if($id_edicion == NULL){

                    //Obtenemos listado de personas delimitandolo por
                    //su caravana y su filtro        
                    $lista_callcenter = CallCenter::listadoCallcenter($id_caravana,
                                                                    $id_callcenter_filtro,
                                                                    $id_seg_capacitacion);                
                    
                    //Recorremos cada registro y guardamos cada registro
                    //en la tabla de callcenter
                    foreach($lista_callcenter as $l):

                        $datos = array('id_mujeres_avanzando'=>$l['id_mujeres_avanzando'],
                                      'id_callcenter_grupo'=>$nuevo_id); 
                    
                        CallCenter::saveCallCenter($datos);               
                    endforeach;

                }
                
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