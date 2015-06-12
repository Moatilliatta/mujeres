<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Vialidad
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
class Vialidad extends MysqliDb{
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

    public static function obtenerVia($CVE_VIA = NULL){
        
        $sql = 
        'SELECT
        CVE_TIPO_VIAL
        from  `vialidad`
        WHERE CVE_VIA = ? ';

        $params = array($CVE_VIA);
        $vialidad = self::executar($sql,$params);
        $vialidad=$vialidad[0];

        return $vialidad;
    }

    /**
    *Obtenemos un listado de vialidades filtrado por la localidad y el tipo de vialidad
    *@param int $CVE_EST_MUN_LOC Llave compuesta por el estado, municipio y localidad
    *@param int $CVE_TIPO_VIAL Llave con el tipo de vialidad
    *
    *@return array Listado de vialidades
    **/
    public static function listaVialidades($CVE_EST_MUN_LOC,$CVE_TIPO_VIAL){

      $sql = 
          'SELECT 
          v.CVE_VIA,
          v.NOM_VIA 
          FROM `vialidad` v 
          WHERE CVE_EST_MUN_LOC = ? 
          AND CVE_TIPO_VIAL = ?
          GROUP BY v.NOM_VIA
          ';

          $params = array($CVE_EST_MUN_LOC,$CVE_TIPO_VIAL);
          
          return self::executar($sql,$params);

    }

    /**
    *Obtenemos un listado de los tipos de vialidades filtrado por la localidad
    *@param int $CVE_EST_MUN_LOC Llave compuesta por el estado, municipio y localidad
    *
    *@return array Listado de tipos de vialidades
    **/    
    public static function listaTipoVialidades($CVE_EST_MUN_LOC){

        $sql = 
          'SELECT
          c.CVE_TIPO_VIAL,
          c.DESCRIPCION
          FROM `vialidad` v
          LEFT JOIN cat_vialidad c on v.CVE_TIPO_VIAL = c.CVE_TIPO_VIAL
          where v.CVE_EST_MUN_LOC = ? GROUP BY v.CVE_TIPO_VIAL';

          $params = array($CVE_EST_MUN_LOC);
          return self::executar($sql,$params);          
    }

     /**
     * Cambiamos el estatus del beneficiario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_Beneficiario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
         public static function saveVialidad($vialidad,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        
        //Arregl donde contedremos si hay un registro duplicado
       // $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';
        
        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($vialidad as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;
        //Evitamos duplicidad de nombres en los registros        
        $sql=
        'SELECT CVE_VIA FROM `vialidad` 
        where CVE_EST_MUN_LOC = ? 
        and NOM_VIA like ? AND CVE_TIPO_VIAL = ?';
        $params = array($CVE_EDO_RES.$id_cat_municipio.$id_cat_localidad,'%'.$NOM_VIA.'%',$CVE_TIPO_VIAL);
        
       
         //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){
            $msg_no = 6;
            //Nombre duplicado
        }else{
                                        
            //Obtenemos el id del usuario creador
            $id_usuario = $_SESSION['usr_id'];
                                     
            //Campos obligatorios
            if($CVE_TIPO_VIAL && $CVE_EDO_RES && $NOM_VIA && $id_cat_municipio && $id_cat_localidad ) 
            {                        
                $insertData = array(
                'CVE_ENT' => $CVE_EDO_RES,
                'CVE_MUN' => $id_cat_municipio,
                'CVE_LOC' => $id_cat_localidad,
                'CVE_TIPO_VIAL' => $CVE_TIPO_VIAL,
                'CVE_EST_MUN_LOC' => $CVE_EDO_RES.$id_cat_municipio.$id_cat_localidad,
                'AGREGADA' => 2,
                'NOM_VIA' => mb_strtoupper($NOM_VIA,"UTF-8")
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
                
                if(! self::getInstance()->{$db_accion}('vialidad', $insertData)){
                    //print_r($insertData);
                    /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                    NO se está guardando el registro por tener campos incompletos o incorrectos*/
                    $msg_no = ($db_accion == 'update')?  14 : 3;                    
                    
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
                
              
        }
        return $msg_no;        
    } 
     
  } 
?>

