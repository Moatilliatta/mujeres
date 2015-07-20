<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla dependencia
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Dependencia extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
     * Obtenemos listado de los dependencias. Predeterminadamente mostramos
     * los componentes de estatus activo = 1
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaDependencias($id_programa_institucional = NULL,
        $id_mujeres_avanzando=NULL,$resto = NULL,$activo = 1)
    {

        $sql = 
        'SELECT 
         *
        FROM `c_dependencia` d
        WHERE 1 ';

        //Parámetros de la sentencia
        $params = array();

        //Verificamos si filtraremos por programa institucional
        if($id_programa_institucional !== NULL){
            $sql .= " AND d.id_programa_institucional = ? ";
            $params[] = $id_programa_institucional;
        }

        //Verificamos si filtraremos por algún listado de componentes
        if($id_mujeres_avanzando !== NULL){  

            $r = ($resto === true)? ' NOT ' : '';

            $sql .= " AND d.ID_C_DEPENDENCIA ".$r." IN (

                SELECT 
                    DISTINCT(i_d.ID_C_DEPENDENCIA)
                    FROM mujeres_avanzando i_m
                    LEFT JOIN c_servicio i_s on i_m.id_grado = i_s.ID_GRADO
                    LEFT JOIN c_programa i_p on i_p.ID_C_PROGRAMA = i_s.ID_C_PROGRAMA
                    LEFT JOIN c_dependencia i_d on i_d.ID_C_DEPENDENCIA = i_p.ID_C_DEPENDENCIA
                    where 1
                    AND i_m.id = ?

                ) ";

            $params[] = $id_mujeres_avanzando;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND d.CVE_ESTATUS = ? ';
            $params[] = $activo;
        }

        //Regresamos resultado
         return self::executar($sql,$params);           
    }

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