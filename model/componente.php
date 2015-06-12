<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Componentes
 * **/ 
//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Componente extends MysqliDb{

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
     * Obtenemos listado de los componentes (programas). Predeterminadamente mostramos
     * los componentes de estatus activo = 1
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaComponentes($id_programa_institucional = NULL,$lista_comp=NULL,$activo = 1)
    {

        $sql = 
        'SELECT 
         *
        FROM `componente` c
        WHERE 1 ';

        //Parámetros de la sentencia
        $params = array();

        //Verificamos si filtraremos por programa institucional
        if($id_programa_institucional !== NULL){
            $sql .= " AND c.id_programa_institucional = ? ";
            $params[] = $id_programa_institucional;
        }

        //Verificamos si filtraremos por algún listado de componentes
        if($lista_comp !== NULL){
            $codigo = implode(',',$lista_comp);
            $sql .= " AND c.codigo IN (?) ";
            $params[] = $codigo;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND c.activo = ? ';
            $params[] = $activo;
        }

        //Regresamos resultado
         return self::executar($sql,$params);           
    }
    /////////////////////////////////////////////////////////////////
     public static function get_Componente($id_producto_servicio)
    {

        $sql = 
       'SELECT 
        co.id as id_componente,
        co.nombre
        from producto_servicio ps
        LEFT JOIN departamento dp on dp.id = ps.id_departamento
        LEFT JOIN direcciones ds on ds.id = dp.id_direccion
        LEFT JOIN componente co on co.id = ds.id_componente
        WHERE ps.id = ?';

        //Parámetros de la sentencia
        $params = array($id_producto_servicio);

       
       //Regresamos resultado
         return self::executar($sql,$params);        
    }
           
       

}
?>