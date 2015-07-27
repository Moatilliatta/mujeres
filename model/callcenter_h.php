<?php
/**
 * Clase que nos permite administrar lo relacionado al modulo CallCenter
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class CallCenterH extends Db{
    
    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    public static function listaCallCenterH($id_callcenter = 0,
    	$id_mujeres_avanzando = 0)
    {
    	$sql = "SELECT 
				s.estatus,
				u.usuario,
				h.fecha_ultima_mod
				FROM `callcenter_h` h
				LEFT JOIN status_llamada s on s.id = h.id_status_llamada
				LEFT JOIN usuario u on u.id = h.id_usuario_ultima_mod
				where ? ";
		
		$params = array(1);

		if($id_callcenter > 0){
			$sql.= " AND h.id_callcenter = ?";
			$params[] = $id_callcenter;
		}

		if($id_mujeres_avanzando > 0){
			$sql.= " AND h.id_mujeres_avanzando = ?";
			$params[] = $id_mujeres_avanzando;
		}
		
		$sql .= " ORDER BY h.fecha_ultima_mod DESC ";

		return Paginador::paginar($sql,$params);
    }

    public static function saveCallCenterH($insertData){

	   	//Iniciamos transacción
		self::getInstance()->startTransaction();               

		//Obtenemos ID del registro creado
		$nuevo_id = self::getInstance()->insert('callcenter_h', $insertData);

		//Variable para mensaje de error
		$err = "";

		if(!$nuevo_id ){

			//NO se está guardando el registro por tener campos 
			//incompletos o incorrectos
			$msg_no = 3; 

			//Cancelamos los posibles campos afectados
			self::getInstance()->rollback();

			//Obtenemos el error
			$err = self::getInstance()->getLastError();

		}else{

			//Campos guardados correctamente
			$msg_no = 1;

			self::getInstance()->commit();
		}     

		return array($msg_no,$err);
    }

}?>