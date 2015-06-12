<?php
/**
 * Clase que nos permite administrar lo relacionado al modulo CallCenter
 * **/ 
//Inclumos librer�a Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['model_path'].'callcenter_h.php');

class CallCenter extends MysqliDb{
    public function __construct(){}
        
    /**
    * Ejecutamos sentencia sql con par�metros
    * @param string $sql Sentencia SQL
    * @param array $params Cada uno de los par�metros de la sentencia
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
     * Listado para obtener a las personas que ser�n agregadas
     * a alg�n callcenter
     * @param  integer $id_caravana          [description]
     * @param  integer $id_callcenter_filtro [description]
     * @param  integer $id_seg_capacitacion  [description]
     * @return [type]                        [description]
     */
    public static function listadoCallcenter($id_caravana = 0,$id_callcenter_filtro = 0,
        $id_seg_capacitacion = 0){
        
        $sql = 
        'SELECT 
        DISTINCT(m.id) as id_mujeres_avanzando
        from mujeres_avanzando m
        LEFT JOIN seg_capacitacion_mujer s on m.id = s.id_mujeres_avanzando
        where 1 ';

        $params = array();

        if($id_caravana > 0){
            
            //Filtramos por caravana
            $sql .= " AND m.id_caravana = ? ";
            $params[] = $id_caravana;
        }

        switch ($id_callcenter_filtro) {
            case 1:
                //Personas que NO han recogido su cartilla
                $sql .= " AND m.fecha_foto IS NULL ";
                break;
            case 2:
                //Personas que se les entreg� su cartilla
                $sql .= " AND m.fecha_foto IS NOT NULL ";
                break;
            case 3:
                
                if($id_seg_capacitacion != 0){
                    //Personas que asistieron a pl�ticas de punto rosa
                    $sql .= " AND s.id_seg_capacitacion = ? ";
                    $params[] = $id_seg_capacitacion;
                }
                break;
            default:
                # code...
                break;
        }
        
        //echo $sql;
        //print_r($params);

        return self::executar($sql,$params);
    }
     
    /**
     * Listado de personas para ser llamadas desde el 
     * callcenter
     * @param  [type]  $nombre  [description]
     * @param  [type]  $paterno [description]
     * @param  [type]  $materno [description]
     * @param  integer $activo  [description]
     * @return [type]           [description]
     */
    public static function listaCallCenter($busqueda=NULL,$tipo_filtro=NULL,
      $id_status_llamada = null,$activo = NULL)
    {
        
        $sql = 
       'SELECT 
        concat(ifnull(m.nombres,?),?,ifnull(m.paterno,?),?,ifnull(m.materno,?)) as nombre_completo,
        m.telefono,
        c.id_status_llamada,
        s.estatus,
        c.id as id_callcenter,
        c.id_usuario_ocupa,
        c.estatus as estatus_reg,
        u.nombres as nombres_operador,
        IFNULL(cg.nombre,"(sin grupo)")  as nombre_grupo,
        (SELECT 
                    count(id_status_llamada)
                    FROM `callcenter_h` where 
                    id_mujeres_avanzando = 1) as total_llamadas
        FROM `callcenter` c
        LEFT JOIN callcenter_grupo cg on cg.id = c.id_callcenter_grupo
                                            and cg.id IN (
                                                SELECT 
                                                id_callcenter_grupo 
                                                FROM callcenter_grupo_operador 
                                                where id_usuario = 1 and activo = 1
                                            )
        LEFT JOIN callcenter_filtro cf on cf.id = cg.id_callcenter_filtro
        LEFT JOIN mujeres_avanzando m on c.id_mujeres_avanzando = m.id
        LEFT JOIN status_llamada s on s.id = c.id_status_llamada
        LEFT JOIN usuario u on u.id = c.id_usuario_ocupa
        WHERE 1 ';

        //Par�metros de la sentencia
        $params = array('',' ','',' ','');

        //Filtro de b�squeda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){
            
            if($tipo_filtro == 'nombre' || $tipo_filtro='telefono') {
                $alias='m';
            }

            switch($tipo_filtro){

                case 'nombre':
                 $alias=' concat(ifnull(m.nombres,?),?,ifnull(m.paterno,?),?,ifnull(m.materno,?)) ';
                 $params = array_merge($params,array('',' ','',' ',''));
                 $sql .=  ' AND '.$alias.' LIKE ? ';
                 $params[] = '%'.$busqueda.'%';
                 break;

                 case 'telefono':
                 $sql .= ' AND m.telefono LIKE ? ';
                 $params[] = '%'.$busqueda.'%';        
                 break;
            }

        }

        if($id_status_llamada != NULL){
            $sql .= " AND c.id_status_llamada = ? ";
            $params[] = $id_status_llamada;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND m.activo = ?';
            $params[] = $activo;
        }

        $sql.= ' ORDER BY c.fecha_ultima_mod DESC ';
        
        return Paginador::paginar($sql,$params);
          
    }
    /**
     * Guardamos registro de callcenter
     * @param  [type] $callcenter [description]
     * @param  [type] $id_edicion [description]
     * @return [type]             [description]
     */
    public static function saveCallCenter($callcenter,$id_edicion = null){
       // print_r($callcenter);
        //exit;

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo donde contendremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas v�a POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id_edicion = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($callcenter as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;        
        
        //Obtenemos el id del usuario creador
        $id_usuario = $_SESSION['usr_id'];
          
        //Campos obligatorios
        if($id_mujeres_avanzando > 0 /*&& ($id_status_llamada || $id_callcenter_grupo)*/)
        {       

            $insertData = array(
                'id_mujeres_avanzando' => $id_mujeres_avanzando,
                'id_status_llamada' => $id_status_llamada,
                'observacion' => $observacion,
                'id_callcenter_grupo' => $id_callcenter_grupo,                
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'id_usuario_creador' => $id_usuario,
                'id_usuario_ultima_mod' => $id_usuario
            );                            

            //Quitamos del arreglo los valores vac�os
            $insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

            //Si recibimos id para editar
            if(intval($id_edicion)>0){

                //Indicamos que haremos un update
                $db_accion = 'update';
                        
                //Al editarse no se guardar� el usuario creador, fecha_creado                    
                unset($insertData['id_usuario_creador']);
                unset($insertData['fecha_creacion']); 

                //Agregamos condici�n para indicar qu� id se actualiza
                self::getInstance()->where('id',$id_edicion);

            }                   
                   
            //Iniciamos transacci�n
            self::getInstance()->startTransaction();               

            $nuevo_id = self::getInstance()->{$db_accion}('callcenter', $insertData);

            if(! $nuevo_id ){
                        
                /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                NO se est� guardando el registro por tener campos incompletos o incorrectos*/
                
                $msg_no = ($db_accion == 'update')?  14 : 3;                    
                        
                //Cancelamos los posibles campos afectados
                self::getInstance()->rollback();

            }else{

                //Campos guardados correctamente
				$msg_no = 1;
                        
                //Realizamos transacci�n
                self::getInstance()->commit(); 

                //Guardamos nuevo ID
                $insertData['id_callcenter'] = $nuevo_id;

                list($msg_no,$err) = CallCenterH::saveCallCenterH($insertData);

            }     

        }else{

            //'Campos Incompletos'
            $msg_no = 2;             

        }
                          
        //Regresamos el mensaje
        return $msg_no;

    } 

    /**
     * Actualizamos el estatus de alg�n registro en callcenter
     * para indicar que est� ocupado y no debe ser modificado
     * mientras que el usuario est� en la opci�n de edici�n
     * @param  integer $id_usuario [description]
     * @return [type]              [description]
     */
    public static function actualizaEstatus($id_usuario_ocupa = 0,$id_callcenter = 0){

        //Si recibimos como par�metro el id_callcenter, pondremos
        //el estatus de ocupado (1), caso contrario, ser� el 
        //estatus de desocupado (0)
        $estatus = ($id_callcenter > 0)? 1 : 0 ;
        
        //Preparamos update
        
        //Ponemos como condici�n el ID del usuario que ocup� alg�n registro;
        //solamente si el id_callcenter no es enviado, pues con esto
        //se entiende que el usuario est� en el listado principal y NO
        //est� ocupando un registro
        if($id_usuario_ocupa > 0 && $id_callcenter == 0 ){
            self::getInstance()->where('id_usuario_ocupa',$id_usuario_ocupa);
            
            //Como el usuario ya no ocupar� el registro, pondremos como nulo
            //el campo de id_usuario_ocupa
            $id_usuario_ocupa = NULL;
        }

        //Ponemos condici�n del ID del callcenter del registro que ser�
        //ocupado por alg�n usuario, con esto se entiende que el usuario
        //ha entrado a la edici�n de un registro y est� OCUPANDO un registro
        if($id_callcenter > 0){
            self::getInstance()->where('id',$id_callcenter);            
        }
        
        //datos a actualizar
        $updateData = array('estatus' => $estatus,
                            'id_usuario_ocupa' => $id_usuario_ocupa);

        //Iniciamos transacci�n
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('callcenter',$updateData)){
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

  }
?>