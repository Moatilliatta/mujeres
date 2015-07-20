<?php
/**
 * Clase que nos permite administrar lo relacionado a los centros atencion
 * **/ 

//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Centros_atencion extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
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
     * Cambiamos el estatus del módulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_modulo Módulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */

    public static function activaCentros_atencion($id_centro){

    //Variable que nos indica el mensaje generado al guardar el registro
    $msg_no = 0;

    //Variable donde guardamos el estatus
    $estatus = 0;

    //Sentencia para obtener el campo activo de la tabla Modulo
    $sql = 'SELECT activo from `centros_atencion` where id = ?';

    //parámetros para la consulta
    $params = array($id_centro);                

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
    self::getInstance()->where('id',$id_centro);

    //datos a actualizar
    $updateData = array('activo' => $estatus);

    //Iniciamos transacción
    self::getInstance()->startTransaction();

    if(!self::getInstance()->update('centros_atencion',$updateData)){

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
     * Obtenemos listado de los centros de atención 
     * @param string $nombre Nombre de Comunidad a buscar
     * @param string $localidad Localidad a buscar
     * @param $activo $clave_comunidad filtramos por la comunidad
     * @return array Resultado de la consulta
     * */

    public static function listaCentros_atencion($nombre=null,$localidad=null,$clave_comunidad=null){
        
        $sql = 
        'SELECT
        ct.activo,
        ct.id,
        ct.nombre as nombre_centro,
        ct.CVE_EST_MUN_LOC, 
        loc.NOM_LOC as localidad,
        e.nombre as estatus,
        ct.fecha_creado, 
        tp.tipo as tipo_centro
        FROM `centros_atencion` ct
        left join tipo_centro_atencion tp on ct.tipo_centro = tp.id
        left join cat_localidad loc on ct.CVE_EST_MUN_LOC = loc.CVE_EST_MUN_LOC
        left join estatus e on e.valor = ct.activo
        where ? 
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda
        
        //Buscamos nombre del centro           

        if($nombre !=null){
          $sql .= ' AND ct.nombre like ? ';
          $params[] = '%'.$nombre.'%';              
        }

        // buscamos localidad
        if($localidad !=null){

          $sql .= ' AND loc.NOM_LOC like ? ';
          $params[] = '%'.$localidad.'%';    

        }
        
        // buscamos clave_comunidad
        if($clave_comunidad !=null){

          $sql .= ' AND ct.CVE_EST_MUN_LOC = ? ';
          $params[] = $clave_comunidad;    
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND b.activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
        // self::executar($sql,$params);
        
        //print_r($params).'<br>';
        //echo 'consulta'.$sql;
        //exit;
        
        return Paginador::paginar($sql,$params);           
    }
    
    /**
     * Guardamos registro en la tabla centros_atencion
     * @param array $centros Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */

     public static function saveCentros_atencion($centros,$id = null){

       //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

      //Indicamos predeterminadamente que insertaremos un registro

        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($centros as $key => $value):

        ${$key} = self::getInstance()->real_escape_string($value);
    
        endforeach;



       //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `centros_atencion` where nombre = ?';

        $params = array($nombre);
      
        //Sólo si se edita el mismo registro puede 'repetir el nombre'
        if($id !=null){
            $sql.=' and id not in (?)';
            $params[] = $id;          
        }
                                
        //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
      
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){

            $msg_no = 6;
            //Nombre duplicado

        }else{
                        
            //Obtenemos el id del usuario creador
            $id_usuario_creador = $_SESSION['usr_id'];
          
            /*Si no esta creada la variable activo 
            predeterminadamente la guardamos = 1*/        
            if(!isset($activo) ){
                $activo = 1 ;            
            }           
          
          /*
            echo 'cve_ent_mun'.$CVE_ENT_MUN.
            ' CVE_EST_MUN_LOC: '.$CVE_EST_MUN_LOC .
            ' nombre: '. $nombre .
            ' CVE_VIA: '.  $CVE_VIA .
            ' CVE_TIPO_VIAL: '.  $CVE_TIPO_VIAL .
            ' num_ext: '. $num_ext .
            ' id_cp_sepomex: '.  $id_cp_sepomex .
            ' CVE_ASEN: '.  $CVE_ASEN .
            ' id_tipo_centro: '.  $id_tipo_centro;
            exit;*/

            //Campos obligatorios
            if ( $CVE_ENT_MUN && $CVE_EST_MUN_LOC && $nombre && $CVE_VIA && 
              $CVE_TIPO_VIAL && $num_ext && $id_cp_sepomex && $id_tipo_centro)
            {
                //include_once($_SESSION['inc_path'].'libs/Permiso.php');
                
                $insertData = array(
                'CVE_EST_MUN_LOC' => $CVE_EST_MUN_LOC,  
                'CVE_ENT_MUN' => $CVE_ENT_MUN,  
                'nombre' => mb_strtoupper(trim($nombre, "UTF-8")),
                'CVE_TIPO_VIAL'=> $CVE_TIPO_VIAL,
                'CVE_VIA'=> $CVE_VIA,                
                'num_ext'=> $num_ext,
                'num_int'=> $num_int,
                'observacion' => mb_strtoupper(trim($observacion, "UTF8")),
                'id_cp_sepomex'=> $id_cp_sepomex,
                'CODIGO'=> $CODIGO,                
                'telefono' => $telefono,
                'tipo_centro' =>$id_tipo_centro,              
                'activo' => $activo,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
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
                
                // print_r($insertData);
                //exit;

                //Iniciamos transacción

                self::getInstance()->startTransaction();
                

                if(! self::getInstance()->{$accion}('centros_atencion', $insertData)){

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
        
        }        
        
        return $msg_no;
        
    }

    /**
    *Obtenemos los datos generales de un centro
    *@param $id_centro_atencion int id de la tabla centro de atención
    *
    *@return Array 
    **/

    public static function datosCentro($id_centro_atencion){

        $sql = 'SELECT
            ca.id as id_centro_atencion, 
            ca.CVE_EST_MUN_LOC,
            m.NOM_MUN as municipio_nom,
            m.CVE_MUN as id_municipio,
            ca.nombre as nombre_centro,
            c.TIPO,
            case c.TIPO
            WHEN c.TIPO = 0 then ?
            WHEN c.TIPO = 1 THEN ?
            WHEN c.TIPO = 2 THEN ?
            END as nombre_tipo,
            ca.tipo_centro,
            case ca.tipo_centro
            when ca.tipo_centro = 0 then ?
            when ca.tipo_centro = 1 then ?
            when ca.tipo_centro = 2 then ?
            when ca.tipo_centro = 3 then ?
            end as nombre_tipo_centro,
            ca.direccion,
            ca.CVE_ENT_MUN,
            ca.telefono,
            c.nombre_comunidad,
            c.cp
            FROM centros_atencion ca
            LEFT JOIN comunidad c on c.CVE_ENT_MUN_LOC = ca.CVE_EST_MUN_LOC
            LEFT JOIN cat_municipio m on m.CVE_ENT_MUN = ca.CVE_ENT_MUN
            WHERE 1 AND ca.id = ? ';

      $params = array('URBANA',
                      'RURAL',
                      'INDIGENA',
                      'DIF MUNICIPAL',
                      'CDC',
                      'CAIC',
                      'OTROS',
                      $id_centro_atencion);

      /*
      print_r($params);
      echo $sql_centros;
      */

      //Datos referente a los centros
      $datos_centro = self::executar($sql,$params);
      $datos_centro = $datos_centro[0];

      return $datos_centro;
    }
    
    /**
    *Obtenemos listado de localidades del centro de atención
    *@param $CVE_ENT_MUN id del estado y municipio
    *
    *@return array Lista de localidades    
    **/
    public static function localidadesCentro($CVE_ENT_MUN){
        $sql = 
        'SELECT
        ct.CVE_EST_MUN_LOC,
        CONCAT(SUBSTR(ct.CVE_EST_MUN_LOC,6,4),?,loc.NOM_LOC) as localidades  
        FROM `centros_atencion` ct
        left join cat_localidad loc on ct.CVE_EST_MUN_LOC = loc.CVE_EST_MUN_LOC
        where ct.CVE_ENT_MUN = ? '; 
        $params = array(' - ',$CVE_ENT_MUN);
       
        return self::executar($sql,$params);
    }
}
?>