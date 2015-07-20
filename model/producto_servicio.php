<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Producto_Servicio
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Producto_servicio extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
    * Obtenemos los datos de un producto/servicio por su id
    *@param int $id_producto_servicio id de la tabla producto/servicio
    *
    *@return Array datos de producto/servicio
    **/
    public static function get_by_id($id_producto_servicio){

        $datos = self::listaPys(null,null,null,null,$id_producto_servicio);
        $datos = $datos[0];

        return $datos;
    }

    /**
     * Obtenemos listado de los productos y servicios. Predeterminadamente mostramos
     * los componentes de estatus activo = 1
     * @param int $id_beneficiario id de la tabla beneficiario
     * @param int $id_componente id de la tabla componente (programa)
     * @param int $id_direccion id de la tabla direccion
     * @param int $id_departamento id de la tabla departamento
     * @param int $id_producto_servicio id de la tabla producto_servicio
     * @param array $lista_comp Listado de componentes
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaPys($id_beneficiario = NULL,$id_componente = NULL,
        $id_direccion = NULL, $id_departamento = NULL, $id_producto_servicio = NULL,
        $lista_comp = NULL, $activo = 1)
    {

        $sql = 
        'SELECT 
        p.id,
        p.nombre,        
        p.codigo as codigo_producto,
        c.codigo as codigo_componente,
        p.tipo
        FROM producto_servicio p
        INNER JOIN departamento dp on dp.id = p.id_departamento
        INNER JOIN direcciones d on d.id = dp.id_direccion
        INNER JOIN componente c on c.id = d.id_componente
        WHERE 1 ';

        //Parámetros de la sentencia
        $params = array();

        /*Verificamos si filtraremos por beneficiario y 
        no mostrarle los productos y servicios que ya tiene*/
        if($id_beneficiario !== NULL){
            $sql .= " and p.id not in (
                        SELECT IFNULL(id_producto_servicio,0) 
                        FROM beneficiario_pys
                        WHERE id_beneficiario = ?) ";
            $params[] = $id_beneficiario;
        }

        //Verificamos si filtraremos por algún componentes (programa)
        if($id_componente !== NULL){
            $sql .= ' AND c.id = ? ';
            $params[] = $id_componente;
        }

        //Verificamos si filtraremos por algún componentes (programa)
        if($id_direccion !== NULL){
            $sql .= ' AND d.id = ? ';
            $params[] = $id_direccion;
        }

        //Verificamos si filtraremos por algún componentes (programa)
        if($id_departamento !== NULL){
            $sql .= ' AND dp.id = ? ';
            $params[] = $id_departamento;
        }

        //Verificamos si filtraremos por id de algún producto/servicio
        if($id_producto_servicio !== NULL){
            $sql .= ' AND p.id = ? ';
            $params[] = $id_producto_servicio;
        }

        //Verificamos si filtraremos por algún listado de componentes (programas)
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