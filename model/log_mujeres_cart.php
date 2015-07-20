<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla log_mujeres_avanzando
 * **/ 
class logMujeresCart extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
     * Guardamos registro en la tabla log_mujeres_avanzando
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id de la tabla log_mujeres_avanzando a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
     public static function saveLogMujeresCart($log_mujeres_cart,$id = null)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];  

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
         a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($log_mujeres_cart as $key => $value):
           ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;            

        //Campos obligatorios
        if ($id_mujeres_avanzando) 
        {
                $insertData = array(
                "motivo" => $motivo,
                "id_mujeres_avanzando" => $id_mujeres_avanzando,
                "id_usuario_creador" => $id_usuario_creador
                );
                
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
                
                if(! self::getInstance()->{$accion}('log_mujeres_cart', $insertData)){
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

        }else{
            //'Campos Incompletos'
            $msg_no = 2;             
        }

        return $msg_no;
    }

    /**
     * Obtenemos listado de folios con el total de sus 
     * reposiciones
     * @return array Resultado de la consulta
     */
    public static function listaLogMujeresCart(){

        $sql = 
        'SELECT 
        lmc.id_mujeres_avanzando,
        CASE
         WHEN( LENGTH(m.num_folio) > 1) THEN
              CONCAT(m.folio,?,m.num_folio)
         ELSE m.folio
         END as folio,
        CONCAT(m.nombres,?,m.paterno,?,m.materno) as nombre_completo,
        c.descripcion,
        count(lmc.id_mujeres_avanzando) as num_rep
        from log_mujeres_cart lmc
        LEFT JOIN mujeres_avanzando m on m.id = lmc.id_mujeres_avanzando
        LEFT JOIN caravana c on c.id = m.id_caravana
        GROUP BY lmc.id_mujeres_avanzando
        ORDER BY lmc.id_mujeres_avanzando
        ';

        $params = array("-"," "," ");
       
        //Regresamos resultado
        return self::executar($sql,$params);          
    }

     /**
     * Obtenemos total de reposiciones
     * @return [type] [description]
     */
    public function total_rep(){

        $sql = 'SELECT
                count(lmc.id_mujeres_avanzando) as total_rep
                from log_mujeres_cart lmc
                LEFT JOIN mujeres_avanzando m on m.id = lmc.id_mujeres_avanzando
                LEFT JOIN caravana c on c.id = m.id_caravana ';
                
        $obj = self::executar($sql,null);
        
        if($obj != NULL){            
            $result = $obj[0]['total_rep'];
        }        

        return $result;
    }   
    
}
?> 