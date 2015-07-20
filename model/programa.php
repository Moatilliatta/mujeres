<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Programas
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Programa extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
    * Obtenemos los datos de un programa por su id
    *@param int $ID_C_PROGRAMA id de la tabla c_programa
    *
    *@return Array datos de la tabla programa
    **/
    public static function get_by_id($id_producto_servicio){

        $datos = self::listaPys(null,null,null,null,$id_producto_servicio);
        $datos = $datos[0];

        return $datos;
    }

    /**
     * Obtenemos listado de los productos y servicios. Predeterminadamente mostramos
     * los componentes de estatus activo = 1
     * @param int $id_mujeres_avanzando id de la tabla beneficiario
     * @param int $id_componente id de la tabla componente (programa)
     * @param int $id_direccion id de la tabla direccion
     * @param int $id_departamento id de la tabla departamento
     * @param int $id_producto_servicio id de la tabla producto_servicio
     * @param array $lista_comp Listado de componentes
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaPrograma($id_mujeres_avanzando = NULL,
        $ID_C_DEPENDENCIA = NULL, $lista_comp = NULL, $activo = 1)
    {

        $sql = 
        'SELECT 
        p.ID_C_PROGRAMA,
        p.NOMBRE as programa,        
        d.NOMBRE as dependencia
        FROM c_programa p
        INNER JOIN  c_dependencia d on p.ID_C_DEPENDENCIA = d.ID_C_DEPENDENCIA
        WHERE 1 ';

        //Parámetros de la sentencia
        $params = array();

        /*Verificamos si filtraremos por beneficiario y 
        no mostrarle los productos y servicios que ya tiene*/
        
         if($id_mujeres_avanzando !== NULL){
            $sql .= " and p.ID_C_PROGRAMA not in (
                        SELECT IFNULL(s.ID_C_PROGRAMA,0) 
                        FROM c_mujeres_avanzando_detalle c
                        LEFT JOIN c_servicio s on c.ID_C_SERVICIO = s.ID_C_SERVICIO
                        LEFT JOIN c_programa p on p.ID_C_PROGRAMA = s.ID_C_PROGRAMA
                        WHERE c.ID_MUJERES_AVANZANDO = ?) ";
            $params[] = $id_mujeres_avanzando;
        }

        //Verificamos si filtraremos por algún componentes (programa)
        if($ID_C_DEPENDENCIA !== NULL){
            $sql .= ' AND d.ID_C_DEPENDENCIA = ? ';
            $params[] = $ID_C_DEPENDENCIA;
        }        
    
        //Verificamos si filtraremos por algún listado de componentes (programas)
        if($lista_comp !== NULL){
            $codigo = implode(',',$lista_comp);
            $sql .= " AND c.codigo IN (?) ";
            $params[] = $codigo;
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND p.CVE_ESTATUS = ? ';
            $params[] = $activo;
        }

        /*
        echo $sql;
        print_r($params);
        */ 

        //Regresamos resultado
         return self::executar($sql,$params);           
    }

    /**
    *Obtenemos los códigos del componente (programa) y producto/servicio
    *@param int $id_producto_servicio Id de la tabla producto_servicio
    *
    *@return Array Resultado de la consulta
    **/
    public static function getCodCompPys($id_producto_servicio = NULL){
        
        $sql = 
        'SELECT 
        p.codigo as codigo_producto,
        c.codigo as codigo_componente,
        c.id as id_componente
        FROM `producto_servicio` p 
        LEFT JOIN departamento d on p.id_departamento = d.id
        LEFT JOIN direcciones di on d.id_direccion = di.id
        LEFT JOIN componente c on di.id_componente = c.id
        where p.id = ? ';

        //Parámetros de la sentencia
        $params = array($id_producto_servicio);

        //Regresamos resultado
        $resultado = self::executar($sql,$params);
        $resultado = $resultado[0];

        return $resultado;
    }
}
?>