<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla comunidad
 * **/ 
//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Comunidad extends MysqliDb{

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
     * Obtenemos listado de los Submódulos. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */

    public static function datos_comunidad($CVE_ENT_MUN_LOC){

        $sql = '
        SELECT 
        cm.CVE_MUN,
        cm.NOM_MUN,
        co.nombre_comunidad,
        co.cp,
        case co.TIPO
            WHEN co.TIPO = 0 THEN  ?
            WHEN co.TIPO = 1 THEN  ?
            WHEN co.TIPO = 2 THEN  ?
            END as nombre_tipo

        FROM comunidad co
        LEFT JOIN cat_municipio cm on cm.CVE_ENT_MUN=CONCAT(co.CVE_ENT,co.CVE_MUN)
        WHERE CVE_ENT_MUN_LOC = ?
        ';

        //Parámetros de la sentencia
          $params = array('URBANA',
                      'RURAL',
                      'INDIGENA',
                      $CVE_ENT_MUN_LOC);
                      
         $datos_comunidad = self::executar($sql,$params);
         $datos_comunidad = $datos_comunidad [0];  
        //Regresamos resultado
      return $datos_comunidad;             
    }


}
?>