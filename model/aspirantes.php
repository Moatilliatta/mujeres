<?php
/**
 * Clase que nos permite administrar lo relacionado con Aspirantes
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Aspirantes extends Db{

  /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
  public function __construct(){}

  /**
  * Guardamos registro en la tabla aspirantes
  * @param array $centros Arreglo con los campos a guardar
  * @param int $id del Modulo a editar (opcional)
  * 
  * @return int No. de mensaje
  * */    
  public static function saveAspirantes($aspirantes,$id = null){
         
  //Variable que nos indica el mensaje generado al guardar el registro
  $msg_no = 0;
  $id_aspirante = 0;
  
  //Indicamos predeterminadamente que insertaremos un registro
  $db_accion = 'insert';

  /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
    a su respectiva variable. Por ejemplo 
    $id = $_POST['id'], $nombre = $_POST['nombre']*/
  foreach($aspirantes as $key => $value):
    ${$key} = self::getInstance()->real_escape_string($value);
  endforeach;        
                            
  //Ejecutamos sentencia
  $duplicado = self::verificaDuplicado($nombres,$paterno,$materno,$id);
      
  //Verificamos que no haya nombre duplicado
  if(count($duplicado)>0){
    $msg_no = 18;
    //Nombre duplicado
  }else{

    //Obtenemos el id del usuario creador
    $id_usuario_creador = $_SESSION['usr_id'];

    /*Si no esta creada la variable activo
    predeterminadamente la guardamos = 1*/
    if(!isset($activo) ){
        $activo = 1 ;
    }

    //Campos obligatorios
    if ( ($nombres ) || ($id_beneficiario) )
    {

      $insertData = array(
                'nombres' => $nombres,
                'paterno' => $paterno,
                'materno' => $materno,
                'id_cat_municipio' =>$id_cat_municipio,
                'id_cat_localidad' => $id_cat_localidad,
                'id_beneficiario' => $id_beneficiario,
                'num_ext' => $num_ext,
                'num_int' => $num_int,
                'genero' => $genero,
                'CVE_EDO_RES' => $CVE_EDO_RES,
                'CVE_EST_MUN_LOC' => $CVE_EDO_RES.$id_cat_municipio.$id_cat_localidad,
                'CVE_VIA' => $CVE_VIA,
                'CVE_TIPO_VIAL' => $CVE_TIPO_VIAL,
                'CODIGO' => $CODIGO,  
                'id_cp_sepomex' => $id_cp_sepomex,
                'domicilio' => md5($CVE_TIPO_VIAL.$CVE_VIA.$num_ext),
                'id_usuario_creador' => $id_usuario_creador,
                'soundex_nombre' => self::getInstance()->soundex($nombres.' '.$paterno),
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
                
      //print_r($insertData);
      //exit;
      
      //Iniciamos transacción
      self::getInstance()->startTransaction();

      if(!self::getInstance()->{$db_accion}('aspirantes', $insertData) && $db_accion == 'insert'){        

        //'Error al guardar, (sólo si se está creando registro)'
        $msg_no = 3;

        //Cancelamos los posibles campos afectados
        self::getInstance()->rollback();

      }else{

            //Campos guardados correctamente
            $msg_no = 1;

            //Obtenemos el id del aspirante
            $id_aspirante = ($db_accion == 'insert')?self::getInstance()->getInsertId():$id;

            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();
            } 

      }else{

        //'Campos Incompletos'
        $msg_no = 2;            
      } 

    }            

    return array($msg_no,$id_aspirante);

    }          

    /**
    *Verificamos posible duplicidad de aspirantes
    *@param varchar $nombres Nombres del aspirante
    *@param varchar $paterno Apellido Paterno del aspirante
    *@param varchar $materno Apellido Materno del aspirante
    *@param int $id ID edición de aspirante
    **/
    public static function verificaDuplicado($nombres,$paterno,$materno = NULL,$id = NULL){

       //Obtenemos nombre completo con función soundex
       $nombre_completo = self::getInstance()->soundex($nombres.' '.$paterno);

       //Evitamos duplicidad de nombres en los registros        
       $sql=
       'SELECT
        a.id as id_aspirante,
        a.nombres,
        a.paterno,
        a.materno,
        CONCAT(a.nombres,?,a.paterno,?,ifnull(a.materno,?)) as nombre_completo,
        ce.NOM_ENT as estado,
        cm.NOM_MUN as municipio,
        cl.NOM_LOC as localidad,
        te.id as id_expediente
        FROM `aspirantes` a
        LEFT JOIN cat_localidad cl on cl.CVE_EST_MUN_LOC = a.CVE_EST_MUN_LOC
        LEFT JOIN cat_estado ce on ce.CVE_ENT =  a.CVE_EDO_RES
        LEFT JOIN cat_municipio cm on cm.CVE_MUN = a.id_cat_municipio and cm.CVE_ENT = ce.CVE_ENT
        left join trab_expediente te on te.id_aspirante = a.id
        -- where a.nombres = ? and a.paterno = ? 
        WHERE 1 AND a.soundex_nombre = (?)
        ';

        $params = array(' ',' ','',$nombre_completo);

       //Sólo si se edita el mismo registro puede 'repetir el aspirante'
        if($materno !=null){
            $sql.=' and a.materno = ? ';
            //$sql.=' and SOUNDEX(a.materno) = SOUNDEX(?) ';
            $params[] = $materno;
        }                          

        //Sólo si se edita el mismo registro puede 'repetir el aspirante'
        if($id !=null){
            $sql.=' and a.id not in (?)';
            $params[] = $id;
        }                          

        /*
        echo $sql;
        print_r($params);
        */
        
        //Ejecutamos sentencia
        return self::getInstance()->rawQuery($sql,$params);

    }
    public static function verificaDomicilio($CVE_TIPO_VIAL,$CVE_VIA,$num_ext){

       //Obtenemos nombre completo con función soundex
       $domicilio = md5($CVE_TIPO_VIAL.$CVE_VIA.$num_ext);
       
       //Evitamos duplicidad de nombres en los registros        
       $sql=
       'SELECT
        a.id as id_aspirante,
        te.id as id_expediente,
        cv.DESCRIPCION as tipo_vialidad,
        v.NOM_VIA as vialidad,
        a.num_ext,
        CONCAT(a.nombres,?,a.paterno,?,ifnull(a.materno,?)) as nombre_completo
        FROM `aspirantes` a
        LEFT JOIN cat_vialidad cv on cv.CVE_TIPO_VIAL = a.CVE_TIPO_VIAL
        LEFT JOIN vialidad v on v.CVE_VIA = a.CVE_VIA
        left join trab_expediente te on te.id_aspirante = a.id
        WHERE domicilio = ?'
        ;

        $params = array(' ',' ','',$domicilio);

         

        /*
        echo $sql;
        print_r($params);
        */
        
        //Ejecutamos sentencia
       return self::getInstance()->rawQuery($sql,$params); 

    }
    public static function verificaDomicilioBeneficiario($CVE_TIPO_VIAL,$CVE_VIA,$num_ext){

       //Obtenemos nombre completo con función soundex
       $domicilio = md5($CVE_TIPO_VIAL.$CVE_VIA.$num_ext);
       
       //Evitamos duplicidad de nombres en los registros        
       $sql=
       'SELECT
        b.id,
        cv.DESCRIPCION as tipo_vialidad,
        v.NOM_VIA as vialidad,
        b.num_ext,
        CONCAT(b.nombres,?,b.paterno,?,ifnull(b.materno,?)) as nombre_completo
        FROM `beneficiario` b
        LEFT JOIN cat_vialidad cv on cv.CVE_TIPO_VIAL = b.CVE_TIPO_VIAL
        LEFT JOIN vialidad v on v.CVE_VIA = b.CVE_VIA
        WHERE domicilio = ?'
        ;

        $params = array(' ',' ','',$domicilio);

         

        /*
        echo $sql;
        print_r($params);
        */
        
        //Ejecutamos sentencia
        return Paginador::paginar($sql,$params);   

    }
    /**
    *Verificamos posible duplicidad de aspirantes
    *@param varchar $nombres Nombres del aspirante
    *@param varchar $paterno Apellido Paterno del aspirante
    *@param varchar $materno Apellido Materno del aspirante
    *@param int $id ID edición de aspirante
    **/
    public static function verificaDuplicado_beneficiario($nombres = NULL, $paterno = NULL,
      $materno = NULL,$id = NULL)
    {
       //Obtenemos nombre completo con función soundex
       $nombre_completo = self::getInstance()->soundex($nombres.' '.$paterno);

       //Evitamos duplicidad de nombres en los registros        
       $sql=
       'SELECT
        b.id,
        b.nombres,
        b.paterno,
        b.materno,
        CONCAT(b.nombres,?,b.paterno,?,ifnull(b.materno,?)) as nombre_completo,
        ce.NOM_ENT as estado,
        cm.NOM_MUN as municipio,
        cl.NOM_LOC as localidad,
        b.fecha_nacimiento,
        b.fecha_aproxim
        FROM `beneficiario` b
        LEFT JOIN cat_localidad cl on cl.CVE_EST_MUN_LOC = b.CVE_EST_MUN_LOC
        LEFT JOIN cat_estado ce on ce.CVE_ENT = b.CVE_EDO_RES  
        LEFT JOIN cat_municipio cm on cm.CVE_MUN = b.id_cat_municipio and cm.CVE_ENT = ce.CVE_ENT 
        where 1 ';

        $params = array(' ',' ','');

        //Nombre
        if($nombres !=null && $paterno !=null){
            //$sql.=' and b.nombres = ? ';
            $sql.=' and b.soundex_nombre = ? ';
            $params[] = $nombre_completo;
        }                          

       //Sólo si se edita el mismo registro puede 'repetir el aspirante'
        if($materno !=null){
            $sql.=' and b.materno = ? ';
            $params[] = $materno;
        }                          

        //Sólo si se edita el mismo registro puede 'repetir el aspirante'
        if($id !=null){
            $sql.=' and b.id not in (?)';
            $params[] = $id;
        }                          

        /*
        echo $sql;
        print_r($params);
        */
        
        //Ejecutamos sentencia
        return self::getInstance()->rawQuery($sql,$params);

    }

    /**
    * Obtenemos los datos del aspirante por su id
    *@param int $id_aspirante id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function get_by_id($id_aspirante){

        $datos = self::getInstance()->where('id', $id_aspirante)
                                    ->getOne('aspirantes');

        return $datos;
    }

    /**
    * Obtenemos los datos PRECARGADOS del aspirante por su id
    *@param int $id_aspirante id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function infoPrecargada($id_aspirante = NULL){

        $datos = self::get_by_id($id_aspirante);
      
        $beneficiario = array();

      if($datos){
        $beneficiario['nombres'] = $datos['nombres'];
        $beneficiario['paterno'] = $datos['paterno'];
        $beneficiario['materno'] = $datos['materno'];
        $beneficiario['CVE_EDO_RES'] = $datos['CVE_EDO_RES'];
        $beneficiario['id_cat_municipio'] = $datos['id_cat_municipio'];
        $beneficiario['id_cat_localidad'] = $datos['id_cat_localidad'];
        $beneficiario['CVE_EST_MUN_LOC'] = $datos['CVE_EDO_RES'].$datos['id_cat_municipio'].$datos['id_cat_localidad'];
        $beneficiario['genero'] = $datos['genero'];
        $beneficiario['num_ext'] = $datos['num_ext'];
        $beneficiario['num_int'] = $datos['num_int'];       
        $beneficiario['CVE_VIA'] = $datos['CVE_VIA'];
        $beneficiario['CVE_TIPO_VIAL'] = $datos['CVE_TIPO_VIAL'];
        $beneficiario['id_cp_sepomex'] = $datos['id_cp_sepomex'];
        $beneficiario['CODIGO'] = $datos['CODIGO'];
      }        
        return $beneficiario;
    }
}           
?>