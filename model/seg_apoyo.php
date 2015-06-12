<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla seg_apoyo
 * **/ 

//Inclumos librería de Paginador

include_once('../../inc/libs/Paginador.php');
class SegApoyo extends MysqliDb{

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
    * Obtenemos los datos del aspirante por su id
    *@param int $id_beneficiario id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function get_by_id($id_caravana){

        $datos = self::getInstance()->where('id', $id_caravana)
                                    ->getOne('caravana');

        return $datos;
    }
  

    /**
     * Obtenemos listado genérico de secretarías
     * @param  varchar  $nombre            [description]
     * @param  datetime $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return Array                     [description]
     */
    private function listApoyo($nombre=null,$fecha_creado=null,$activo=1){

        $sql = 'SELECT s.* FROM `seg_apoyo` s where ? ';

        //Parámetros de la sentencia
        $params = array(1);
        
         //Buscamos por nombre           
        if($nombre !=null){
                    
          $sql .= ' AND s.nombre like ? ';
          $params[] = '%'.$nombre.'%';

        }

        //Fecha creado
        if($fecha_creado !=null){
            
          $sql .= ' AND s.fecha_creado = ? ';
          $params[] = $fecha_creado;

        }

        //Activo o Inactivo
        if($activo !=null){

          $sql .= ' AND s.activo = ? ';
          $params[] = $activo;

        }        
       
        /*
        echo $sql;
        print_r($params);
        */
       
        //Regresamos consulta y parámetros
        return array($sql,$params);

    }

    /**
     * Obtenemos un arreglo con el listado de las secretarías
     * @param  [type]  $nombre       [description]
     * @param  [type]  $fecha_creado [description]
     * @param  integer $activo            [description]
     * @return [type]                     [description]
     */
    public static function listadoApoyo($nombre=null,$fecha_creado=null,$activo=1){

        list($sql,$params) = self::listApoyo($nombre,$fecha_creado,$activo);

        //Regresamos resultado
        return  self::executar($sql,$params);
    }

    /**
     * Obtenemos listado de secretaría PAGINADO
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * @return array Resultado de la consulta
     * */
    public static function listaApoyo($nombre=null,$fecha_creado=null,$activo=1)
    {
        list($sql,$params) = self::listApoyo($nombre,$fecha_creado,$activo);

        return Paginador::paginar($sql,$params);           
    
    }

}
?>