<?php
/**
 * Clase que guarda lo relacionado a la tabla trab_expediente
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos modelo trab_exp_beneficiario
include_once($_SESSION['model_path'].'trab_exp_beneficiario.php');  
//Incluimos modelo trab_pys_exp
include_once($_SESSION['model_path'].'trab_pys_exp.php');  

class Trab_expediente extends Db{
   
   /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
   public function __construct(){}
   
    /**
    * Obtenemos los datos de un beneficiario con expediente por su id
    *@param int $id_beneficiario id de la tabla beneficiario
    *
    *@return Array datos de producto/servicio
    **/
    public static function get_by_id($id_trab_expediente = NULL){

        $datos = NULL;

        if($id_trab_expediente){
            $datos = self::getInstance()->where('id', $id_trab_expediente)
                                        ->getOne('trab_expediente');
        }        

        return $datos;
    }

    /**
    * Obtenemos los datos de un beneficiario con expediente por su id
    *@param int $id_beneficiario id de la tabla beneficiario
    *
    *@return Array datos de producto/servicio
    **/
    public static function get_by_id_ben($id_beneficiario = NULL){

        $datos = NULL;

        if($id_beneficiario){
            $datos = self::getInstance()->where('id_beneficiario', $id_beneficiario)
                                        ->getOne('trab_expediente');
        }        

        return $datos;
    }


    /**
    * Obtenemos los datos de un beneficiario con expediente por su id
    *@param int $id_beneficiario id de la tabla beneficiario
    *
    *@return Array datos de producto/servicio
    **/
    public static function get_by_id_asp($id_aspirante = NULL){

        $datos = NULL;

        if($id_aspirante){
            $datos = self::getInstance()->where('id_aspirante', $id_aspirante)
                                        ->getOne('trab_expediente');
        }        

        return $datos;
    }

    /**
     * Cambiamos el estatus del módulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_modulo Módulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
   public static function activaTrab_expediente($id_trabajo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Modulo
        $sql = 'SELECT activo from `trab_expediente` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_trabajo);                

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
        self::getInstance()->where('id',$id_trabajo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
      
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('trab_expediente',$updateData)){

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
     * @param string $localidad Localidad a buscar
     * @param $activo $clave_comunidad filtramos por la comunidad
     * @return array Resultado de la consulta
     * */
     public static function listaExpBeneficiario($id_beneficiario = NULL,$id_trab_expediente = NULL)
     {
        
        $sql = 
        'SELECT 
        t.id,
        b.id as id_beneficiario,
        b.nombres,
        b.paterno,
        b.materno,
        ce.NOM_ENT as estado,
        cm.NOM_MUN as municipio,
        cl.NOM_LOC as localidad
        FROM `trab_expediente` t
        LEFT JOIN beneficiario b on b.id = t.id_beneficiario
        LEFT JOIN cat_localidad cl on cl.CVE_EST_MUN_LOC = b.CVE_EST_MUN_LOC
        LEFT JOIN cat_estado ce on ce.CVE_ENT = b.CVE_EDO_RES
        LEFT JOIN cat_municipio cm on cm.CVE_MUN = b.id_cat_municipio and cm.CVE_ENT = ce.CVE_ENT 
        WHERE 1 ';

        //Parámetros de la sentencia
        $params = array();

        //Filtro de búsqueda
        if($id_beneficiario !== NULL){
            $sql .= ' AND b.id = ? ';
            $params[] = $id_beneficiario;
        }

        //Filtro de búsqueda
        if($id_trab_expediente !== NULL){
            $sql .= ' AND t.id = ? ';
            $params[] = $id_trab_expediente;
        }                

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND t.activo = ?';
            $params[] = $activo;
        }

        return Paginador::paginar($sql,$params);           
    }   

    /**
     * Obtenemos listado de los aspirantes que tienen un expediente 
     * @param string $nombre Nombre de Comunidad a buscar
     * @param string $localidad Localidad a buscar
     * @param $activo $clave_comunidad filtramos por la comunidad
     * @return array Resultado de la consulta
     * */
     public static function listaTrab_expediente($nombre=NULL,$paterno=NULL,
        $materno=NULL,$tipo=NULL,$numero_expediente=NULL, $id_trab_expediente = NULL) 
     {
        //Sentencia
        $sql = 
        'SELECT
        te.activo,
        te.id,
        te.id_aspirante,
        te.id_beneficiario,
        te.numero_expediente,
        te.fecha_recibido,
        te.fecha_registro,
        te.mes,
        te.axo,
        b.id as beneficiario,
        p.nombre as producto_servicio,
        CONCAT(a.nombres,?,ifnull(a.paterno,?),?,ifnull(a.materno,?)) as nombres_asp,
        CONCAT(b.nombres,?,b.paterno,?,ifnull(b.materno,?)) as nombres_ben
        FROM `trab_expediente` te
        LEFT JOIN producto_servicio p on te.id_producto_servicio = p.id
        LEFT JOIN aspirantes a on te.id_aspirante = a.id
        LEFT JOIN beneficiario b on te.id_beneficiario = b.id  
        where te.activo = 1';

        //Parámetros de la sentencia
        $params = array(' ','',' ','',' ',' ','');

        //Filtro de búsqueda        
        if($nombre != NULL && $paterno != NULL ){
           
           $aspirante = ' AND CONCAT(ifnull(a.nombres,?),
                          ?,ifnull(a.paterno,?),
                          ?,ifnull(a.materno,?)) LIKE ? ';
                          
                          
            $beneficiario = ' AND CONCAT(ifnull(b.nombres,?),
                          ?,ifnull(b.paterno,?),
                          ?,ifnull(b.materno,?)) LIKE ? ';              
           
           
             $todo = ' AND CONCAT(ifnull(a.nombres,?),
                          ?,ifnull(a.paterno,?),
                          ?,ifnull(a.materno,?)) LIKE ? 
                          or CONCAT(ifnull(b.nombres,?),
                          ?,ifnull(b.paterno,?),
                          ?,ifnull(b.materno,?)) LIKE ? '; 
            //echo $tipo;
            //exit;
            
            switch($tipo){
              case 'aspirante':
              
               $sql.=$aspirante;           
               $params = array_merge($params, array(
                                     '',
                                     ' ',
                                     '',
                                     ' ',
                                     ' ',
                                     '%'.$nombre.'%'.$paterno.'%'.$materno.'%') );
                          
                          break;
              
              case 'beneficiario':
              
              
                $sql.=$beneficiario;
                $params = array_merge($params, array(
                                     '',
                                     ' ',
                                     '',
                                     ' ',
                                     ' ',
                                     '%'.$nombre.'%'.$paterno.'%'.$materno.'%') );      
                   
                          break;            
              
              case 'todo':
                $sql.=$todo;
                  
                $params = array_merge($params, array(
                               '',
                               ' ',
                                '',
                                ' ',
                                ' ',
                                '%'.$nombre.'%'.$paterno.'%'.$materno.'%',
                                '',
                               ' ',
                                '',
                                ' ',
                                ' ',
                                '%'.$nombre.'%'.$paterno.'%'.$materno.'%')); 
                                
                                
              
                             
                   
            }

          
        }
		
        //Buscamos por número de expediente
        if($numero_expediente != NULL){
            $sql .= ' AND te.numero_expediente = ?';
            $params[] = $numero_expediente;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($id_trab_expediente !== NULL){
            $sql .= ' AND te.id = ?';
            $params[] = $id_trab_expediente;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND te.activo = ?';
            $params[] = $activo;
        }

        
      // echo $sql;
       //exit;
        //print_r($params);
        
        
        return Paginador::paginar($sql,$params);           
    }     
    
     /**
     * Guardamos registro en la tabla trab_expediente
     * @param array $centros Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */     
    public static function saveTrab_expediente($trabajo,$id = null){
         
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        $id_trabajo_social = 0;

        //Indicamos predeterminadamente que insertaremos un registro

        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas 
        vía POST y las asignamos a su respectiva variable. 
        Por ejemplo $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($trabajo as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);    
        endforeach;
        
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `trab_expediente` where numero_expediente = ?';
        $params = array($numero_expediente);
        
        //Sólo si se edita el mismo registro puede 'repetir el numero de expediente'
        if($id !=null){

            $sql.=' and id not in (?)';
            $params[] = $id;            
        }
                                
        //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){

            //Nombre duplicado
            $msg_no = 17;            

        }else{
                        
            //Obtenemos el id del usuario creador
            $id_usuario_creador = $_SESSION['usr_id'];
            
            /*Si no esta creada la variable activo 
            predeterminadamente la guardamos = 1*/        
            if(!isset($activo) ){
                $activo = 1 ;            
            }   
            
             /*Si no esta creada la variable condicion 
            predeterminadamente la guardamos = 1*/        
            if(!isset($condicion) ){
                $condicion = 'ABIERTA' ;            
            }               

           //definir si es valido el apoyo
            if($valido == 'SI'){
                $validacion='SI';
            }else{
                $validacion='NO';
            }

            //Formateamos el mes con 0 a la izquierda            
            $mes_bien = str_pad($mes, 2, "0", STR_PAD_LEFT);
               
            if ($fecha_recibido && $fecha_registro && $mes_bien 
                && $axo_padron && $id_tipo_apoyo_solicitado)
            {
                
                $insertData = array(
                'fecha_recibido' => $fecha_recibido,
                'fecha_registro' => $fecha_registro,
                'id_producto_servicio' => $id_producto_servicio,
                'mes' => $mes_bien,
                'axo' => $axo_padron,
                'validacion' => $validacion,
                'activo' => $activo,
                'id_aspirante' => $id_aspirante,
                'id_beneficiario' => $id_beneficiario,
                'id_usuario_creador' => $id_usuario_creador,
                'id_instancia' => $id_instancia,
				'numero_documento' => $numero_documento,
				'condicion' => $condicion,
				'id_problematica' => $id_problematica,
				'id_tipo_discapacidad' => $id_tipo_discapacidad,
				'id_enfermedad' => $id_enfermedad,				
				'id_tipo_apoyo' => $id_tipo_apoyo,
				'id_motivo_cierre' => $id_motivo_cierre,
                'observacion_cierre' => $observacion_cierre,
				'id_atencion_medica' => $id_atencion_medica,
                'id_tipo_apoyo_solicitado' => $id_tipo_apoyo_solicitado,
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
              
                if(!self::getInstance()->{$db_accion}('trab_expediente', $insertData) && $db_accion == 'insert'){

                   // print_r($insertData);

                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                   
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();                

                    }else{
                        
                        //Campos guardados correctamente                    
                        $msg_no = 1;                                                                                                     

                        //Obtenemos el id de trabajo_social
                        $id_trab_expediente = ($db_accion == 'insert')?self::getInstance()->getInsertId():$id;
                        
                        //UPDATE PARA GUARDAR NUMERO DE EXPEDIENTE 
                         $num_exp = array(
                        'numero_expediente' => substr($axo_padron,-2).'/'.$mes_bien.'/'.$id_trab_expediente,
                        );

                        self::getInstance()->where('id', $id_trab_expediente)
                                           ->update('trab_expediente',$num_exp);                       

                        //Guardamos beneficiarios en caso de haber
                        if(count($_SESSION['arrayCarro'])){
                            //$msg_no = Trab_exp_beneficiario::saveListaBen();  
                            //echo 'Msg 1'.$msg_no;
                        }
                                                
                        //Si es beneficiario y escoge el producto/servicio
                        if($id_beneficiario && $id_producto_servicio){
                            
                            //Datos relacionados al producto/servicio
                            $trab_pys_exp = array(
                            'id_producto_servicio' => $id_producto_servicio,
                            'id_trab_expediente' => $id_trab_expediente,
                            'justificacion' => $justificacion,
                            );
                            
                            //Guardamos datos de producto_servicio
                            $msg_no = Trab_pys_exp::saveTrab_pys_exp($trab_pys_exp);
                        
                        }                        
                        
                        
                        if($msg_no == 1){
                            //Guardamos campos afectados en la tabla
                            self::getInstance()->commit();
                            //unset($_SESSION['arrayCarro']);
                            
                             
                        }else{
                            //Cancelamos los posibles campos afectados
                            self::getInstance()->rollback();              
                        }                        

                    } 
                    
                }else{
                //'Campos Incompletos'
                
                $msg_no = 2;             
            }        

        }                
       
        return array($msg_no,$id_trabajo_social);
    }
     
}    
?>