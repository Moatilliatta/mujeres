<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Punto Rosa
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');

class PuntoRosa extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}    

    /**
     * Obtenemos listado de las caravanas
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * predeterminadamente mostramos los activos
     * @return array Resultado de la consulta
     * */
    public static function listaPuntos($activo = 1){

        $sql = 
        'SELECT
        p.*,
        e.nombre as estatus
        FROM `punto_rosa` p
        left join estatus e on e.valor = p.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda        
        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND p.activo = ?';
            $params[] = $activo;
        }

        //echo $sql;

        //Regresamos resultado
        return  self::executar($sql,$params);

    }
}
?>