<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario_Secretaria
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');

class UsuarioSecretaria extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
     * Cambiamos el estatus de la caravana asignada
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario_secretaria a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaUsuarioSecretaria($id_usuario_secretaria){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `usuario_secretaria` where id = ?'; 
        
        //Parámetro(s) para la edición
        $params = array($id_usuario_secretaria);
        
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
        self::getInstance()->where('id',$id_usuario_secretaria);                                                
        //datos a actualizar
        $updateData = array('activo' => $estatus);

        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('usuario_secretaria',$updateData)){
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


    public function listadoSecretariaUsr($nombre_usuario= NULL,$id_usuario = NULL,$activo = 1){
        
        //Obtenemos lista de caravanas
        $lista = self::listaSecretariaUsr($nombre_usuario,$id_usuario,$activo);

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
    public static function listaSecretariaUsr($nombre_usuario= NULL,$id_usuario = NULL,$activo = 1){

        $sql = 
           'SELECT
            s.id,
            s.nombre,
            e.nombre as estatus 
            FROM usuario_secretaria us 
            left join estatus e on e.valor = us.activo 
            LEFT JOIN usuario u on us.id_usuario = u.id 
            LEFT JOIN seg_secretaria s on us.id_secretaria = s.id
            WHERE 1 
            ';

        //Parámetros de la sentencia
        $params = array();

        //Filtro de búsqueda        
        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND us.activo = ?';
            $params[] = $activo;
        }

        //Filtramos por nombre de Usuario
        if($nombre_usuario != NULL){
            $sql .= ' AND u.usuario = ? ';
            $params[] = $nombre_usuario;
        }

        //Filtramos por ID de usuario
        if($id_usuario !== NULL){
            $sql .= ' AND us.id_usuario = ?';
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
    * Obtenemos las secretarías del usuario
    * @param int $id_usuario ID del usuario
    * 
    * @return array secretarías del usuario
    **/
    public static function secUsuario($id_usuario){
    	
    	$secretarias = array();

    	$sql = 'SELECT us.id_secretaria,s.nombre,s.descripcion
				FROM `usuario_secretaria` us
                LEFT JOIN seg_secretaria s on s.id = us.id_secretaria
				where us.id_usuario = ? and us.activo = 1';
		$params = array($id_usuario);

		$C = self::executar($sql,$params);


		if($C != NULL){

			foreach($C as $key => $value):
    			$secretarias[] = $value['id_secretaria'];
			endforeach;			
		}	

		return $secretarias;
    }

    /**
    *Limpiamos los registros de caravana de algún usuario
    *@param int $id_usuario ID de la tabla usuario
    *
    *@return int $msg_no Mensaje resultante
    **/
    public static function limpiarSecretaria($id_usuario = NULL){

    		$msg_no = 0;

	    	$updateData = Array (
				    'activo' => 0
				);

			self::getInstance()->where('id_usuario', $id_usuario);

			if(!self::getInstance()->update('usuario_secretaria',$updateData)){
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
    *Guardamos las secretarías que tiene ligadas un usuario
    *@param Array $secretaria secretarias que se guardarán
    *@param int $id_usuario usuario que tendrá las secretarias
    *
    *@return int $msg_no Mensaje a regresar
    **/
	public static function saveUsuarioSecretaria($secretaria,$id_usuario = NULL ){
                
        //Mensaje 
        $msg_no = 1;

        if($id_usuario != NULL){
        	//Limpiamos los registros de secretaria
        	self::limpiarSecretaria($id_usuario);
        }        

        if(count($secretaria) > 0){        	
             	
        	//Iniciamos transacción
            self::getInstance()->startTransaction();

	        //Obtenemos el id del usuario creador
	        $id_usuario_creador = $_SESSION['usr_id'];

	        foreach ($secretaria as $value):

	        //En este punto se debe verificar si ya hay en la tabla
	        //un id con esa secretaria y el mismo usuario, en ese caso
	        //se omitirá el registro del campo
	        self::getInstance()->where('id_secretaria', $value);
			self::getInstance()->where('id_usuario', $id_usuario);
			$results = self::getInstance()->get('usuario_secretaria');

			/*
			print_r($results);
			exit;
			*/

			if($results == NULL){

				$insertData = array(
					'id_secretaria' => $value,
					'id_usuario' => $id_usuario,
					'fecha_creado' => date('Y-m-d H:i:s'),
					'id_usuario_creador' => $id_usuario_creador
					);

				//Quitamos del arreglo los valores vacíos
				//$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

				//print_r($insertData);

				if(!self::getInstance()->insert('usuario_secretaria', $insertData)){
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
				$id_usuario_secretaria = $results['id'];
				$msg_no = self::activaUsuarioSecretaria($id_usuario_secretaria);
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