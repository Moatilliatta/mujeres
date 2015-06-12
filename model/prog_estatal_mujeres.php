<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Caravana
 * **/ 

//Inclumos librería de Paginador

include_once('../../inc/libs/Paginador.php');
include_once('../../inc/libs/Permiso.php');
class ProgEstatalMujeres extends MysqliDb{

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
     * Listado de mujeres con al menos un programa
     * @param  integer $id_caravana [description]
     * @return [type]               [description]
     */
    public static function listaProgMujeres($id_caravana = 0){
      $sql = "SELECT 
              p.id,
              p.id_mujeres_avanzando,
              m.nombres,
              m.paterno,
              m.materno,
              c.NOMBRE as programa,
              p.fecha_creacion,
              p.fecha_ultima_modificacion,
              u.usuario as usuario_creador,
              u2.usuario as usuario_modif
              FROM `prog_estatal_mujeres` p
              LEFT JOIN mujeres_avanzando m on p.id_mujeres_avanzando = m.id
              LEFT JOIN c_programa c on p.id_c_programas = c.ID_C_PROGRAMA
              LEFT JOIN usuario u on p.id_usuario_creador = u.id
              LEFT JOIN usuario u2 on p.id_usuario_ultima_modificacion = u2.id
              WHERE ? ";

        $params = array(1);

       if($id_caravana != 0){
         $sql .= " AND m.id_caravana = ? ";
         $params[] = $id_caravana;
       }

       //Regresamos consulta y parámetros
      return Paginador::paginar($sql,$params);
    }

    public static function listaProgEstatales($id_mujeres_avanzando=null){
        
        $sql = 
        'SELECT
        pe.NOMBRE
        from prog_estatal_mujeres bp
        LEFT JOIN prog_estatales pe on pe.ID_C_PROGRAMAS = bp.id_c_programas
        where bp.id_mujeres_avanzando = ?
        ';
        
        $params = array($id_mujeres_avanzando);          
        
        
        //Regresamos consulta y parámetros
        return Paginador::paginar($sql,$params);
      
      }
      
      
      public static function searchprogEstatal($id_mujeres_avanzando=null){
       
             $sql = 'SELECT 
                     id, 
                     nombres, 
                     paterno, 
                     materno from mujeres_avanzando
                     where id = ?';
                     
        $params = array($id_mujeres_avanzando);          
        $beneficiario = self::executar($sql,$params);
        //$beneficiario = $beneficiario [0];
        
        //print_r($beneficiario);
        //exit;
        
        foreach($beneficiario as $key => $value): 
        
            $prog_estatal = Permiso::buscaNombreWS($value['nombres'],$value['paterno'],$value['materno']);
            //print_R($prog_estatal);
            //exit;
            if (is_array($prog_estatal)){
            $prog_estatal = $prog_estatal[0];
            }
            
            //print_r($prog_estatal);
            //exit;
            
            $dependencia = $prog_estatal->programas->Programa->CdDependencia;
            $programa = $prog_estatal->programas->Programa->CdPrograma;
            
            $programas = array('dependencia'=>$dependencia,
                               'programa'=>$programa);
                               
            
            //print_R($programas);
            //exit;
                               
          
            $msg_no = self::saveProgEstatal($programas,$value['id']);
            
             
           endforeach; 
        
        
          return array($msg_no);
      }
      
      public static function saveProgEstatal($prog_estatal,$id_mujeres_avanzando){
     // print_R($prog_estatal);
      //echo $id_mujeres_avanzando;
      //exit;

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        
        $duplicado = array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        //Obtenemos el id del usuario que modifica
        $id_usuario = $_SESSION['usr_id'];

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/         
        foreach($prog_estatal as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;

        
            //Evitamos duplicidad de programas        
          $sql =
               'SELECT
                bp.id_mujeres_avanzando,
                bp.id_c_programas 
                from prog_estatal_mujeres bp
                where bp.id_mujeres_avanzando = ? and bp.id_c_programas = ?';

            $params = array($id_mujeres_avanzando,$programa);
            $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        

            //Campos obligatorios
            if($id_mujeres_avanzando && $programa)
            {                       
            

                $insertData = array(
                'id_mujeres_avanzando' => $id_mujeres_avanzando,
                'id_c_programas' => $programa,
                'id_usuario_creador' => $id_usuario
                 );                            

               
             //Si tenemos registro duplicado solo actualizamos(sobrescribir)
                    if($duplicado !=null){

                        //Indicamos que haremos un update
                        $db_accion = 'update';

                        //Al editarse no se guardará el usuario creador                    
                        unset($insertData['id_usuario_creador']);

                        $insertData['id_usuario_ultima_modificacion'] = $id_usuario ;
                        $insertData['fecha_ultima_modificacion'] = date('Y-m-d H:i:s');
                        
                        //Agregamos condición para indicar qué id se actualiza
                        self::getInstance()->where('id_mujeres_avanzando',$id_mujeres_avanzando); 
                        self::getInstance()->where('id_c_programas',$programa);                                       

                    }
                    

                    //print_r($insertData);
                    //exit;

                    //Iniciamos transacción
                    self::getInstance()->startTransaction();                  

                    if(! self::getInstance()->{$db_accion}('prog_estatal_mujeres', $insertData)){
                        
                        /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                        NO se está guardando el registro por tener campos incompletos o incorrectos*/
                        $msg_no = ($db_accion == 'update')?  14 : 3;                    
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();                        

                        }else{

                        //Campos guardados correctamente
						$msg_no = 1;
                        
                       
                     
                         if($msg_no == 1){
                        self::getInstance()->commit(); 
                        }else{
                            self::getInstance()->rollback();     
                        }              

                        }     

                }

            else{

            //'Campos Incompletos'
            $msg_no = 2;             

            }
              
             

        //Regresamos el mensaje, CURP y el id generado/modificado
        return $msg_no;

    } 

}
?>