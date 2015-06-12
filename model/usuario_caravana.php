<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Caravana
 * **/ 

//Inclumos librería de Paginador

include_once('../../inc/libs/Paginador.php');

class UsuarioCaravana extends MysqliDb{

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
     * Cambiamos el estatus de la caravana asignada
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario_caravana a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaUsuarioCaravana($id_usuario_caravana){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `usuario_caravana` where id = ?'; 
        
        //Parámetro(s) para la edición
        $params = array($id_usuario_caravana);
        
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
        self::getInstance()->where('id',$id_usuario_caravana);                                                
        //datos a actualizar
        $updateData = array('activo' => $estatus);

        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('usuario_caravana',$updateData)){
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


    public function listadoCaravanaUsr($nombre_usuario= NULL,$id_usuario = NULL,$activo = 1){
        
        //Obtenemos lista de caravanas
        $lista = self::listaCaravanaUsr($nombre_usuario,$id_usuario,$activo);

        //Inicializamos arreglo
        $listado = array();

        foreach ($lista as $key => $value):
            $listado[] = $value['id'];
        endforeach;

        return $listado;
    }

    /**
     * Obtenemos listado de las caravanas
     * @param varchar $nombre_usuario Nombre del usuario
     * @param int $id_usuario ID de la tabla Usuario
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * predeterminadamente mostramos los activos
     *
     * @return array Resultado de la consulta
     * */
    public static function listaCaravanaUsr($nombre_usuario= NULL,$id_usuario = NULL,$activo = 1){

        $sql = 
           'SELECT
            c.id,
            c.descripcion,
            e.nombre as estatus 
            FROM usuario_caravana uc 
            left join estatus e on e.valor = uc.activo 
            LEFT JOIN usuario u on uc.id_usuario = u.id 
            LEFT JOIN caravana c on uc.id_caravana = c.id
            WHERE 1 
            ';

        //Parámetros de la sentencia
        $params = array();

        //Filtro de búsqueda        
        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND uc.activo = ?';
            $params[] = $activo;
        }

        //Filtramos por nombre de Usuario
        if($nombre_usuario != NULL){
            $sql .= ' AND u.usuario = ? ';
            $params[] = $nombre_usuario;
        }

        //Filtramos por ID de usuario
        if($id_usuario !== NULL){
            $sql .= ' AND uc.id_usuario = ?';
            $params[] = $id_usuario;
        }

        /*
        echo $sql;
        print_r($params);
        */

        //Regresamos resultado
        return  self::executar($sql,$params);

    }


    /**
    * Obtenemos las caravanas del usuario
    * @param int $id_usuario ID del usuario
    * 
    * @return array Caravanas del usuario
    **/
    public static function caravanasUsuario($id_usuario){
    	
    	$caravanas = array();

    	$sql = 'SELECT id_caravana 
				FROM `usuario_caravana` 
				where id_usuario = ? and activo = 1';
		$params = array($id_usuario);

		$C = self::executar($sql,$params);


		if($C != NULL){

			foreach($C as $key => $value):
    			$caravanas[] = $value['id_caravana'];
			endforeach;			
		}	

		return $caravanas;
    }

    /**
    *Limpiamos los registros de caravana de algún usuario
    *@param int $id_usuario ID de la tabla usuario
    *
    *@return int $msg_no Mensaje resultante
    **/
    public static function limpiarCaravana($id_usuario = NULL){

    		$msg_no = 0;

	    	$updateData = Array (
				    'activo' => 0
				);

			self::getInstance()->where('id_usuario', $id_usuario);

			if(!self::getInstance()->update('usuario_caravana',$updateData)){
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
    *Guardamos las caravans que tiene ligadas un usuario
    *@param Array $caravana Caravanas que se guardarán
    *@param int $id_usuario usuario que tendrá las caravanas
    *
    *@return int $msg_no Mensaje a regresar
    **/
	public static function saveUsuarioCaravana($caravana,$id_usuario = NULL ){
                
        //Mensaje 
        $msg_no = 1;

        if($id_usuario != NULL){
        	//Limpiamos los registros de caravana
        	self::limpiarCaravana($id_usuario);
        }        

        if(count($caravana) > 0){        	
             	
        	//Iniciamos transacción
            self::getInstance()->startTransaction();

	        //Obtenemos el id del usuario creador
	        $id_usuario_creador = $_SESSION['usr_id'];

	        foreach ($caravana as $value):

	        //En este punto se debe verificar si ya hay en la tabla
	        //un id con esa caravana y el mismo usuario, en ese caso
	        //se omitirá el registro del campo
	        self::getInstance()->where('id_caravana', $value);
			self::getInstance()->where('id_usuario', $id_usuario);
			$results = self::getInstance()->get('usuario_caravana');

			/*
			print_r($results);
			exit;
			*/

			if($results == NULL){

				$insertData = array(
					'id_caravana' => $value,
					'id_usuario' => $id_usuario,
					'fecha_creado' => date('Y-m-d H:i:s'),
					'id_usuario_creador' => $id_usuario_creador
					);

				//Quitamos del arreglo los valores vacíos
				//$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

				//print_r($insertData);

				if(!self::getInstance()->insert('usuario_caravana', $insertData)){
					//'Error al guardar, NO se guardo el registro'
					$msg_no = 3;
				}else{
					//Campos guardados correctamente
					$msg_no = 1;
				}                     

			}else{
				$results = $results[0];
				/*
				print_r($results);
				exit;
				*/
				$id_usuario_caravana = $results['id'];
				$msg_no = self::activaUsuarioCaravana($id_usuario_caravana);
			}
	                
	        endforeach;
	            
	        if($msg_no == 3){
	        	//Cancelamos los posibles campos afectados
	        	self::getInstance()->rollback();
	        }else if($msg_no == 1){
	        	//Guardamos los campos afectados en la tabla
	        	self::getInstance()->commit();
	        }	                                 
                    
        }/*else{
            //'Campos Incompletos'
            $msg_no = 2;
        }*/

        //Regresamos mensaje
        return $msg_no;
    }

}?>