<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de capacitaciones
 * **/ 

//Incluimos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos librería de fecha
include_once($_SESSION['inc_path'].'libs/Fechas.php');

class SegCapacitacion extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
              
    /**
     * Función que nos generará un listado de capacitaciones(platicas)
     * 
     */
     public static function listaSegCapacitacion($id_mujeres_avanzando,$activo = 1){

         $sql =  ' SELECT c.*,
                   c.nombre as nombre_capacitacion,
                   sprc.id as id_seg_punto_rosa_capacitacion
                   from seg_capacitacion c
                   left join seg_punto_rosa_capacitacion sprc on sprc.id_seg_capacitacion = c.id
                   where 1 ';
        
        //Apellido materno
        if($activo !=null){

          $sql .= ' AND c.activo = ? ';
          $params[] = $activo;

        }        

        //Parámetros de la sentencia
        $params = array($id_mujer,$id_punto_rosa);

        return array($sql,$params);          
     }
     
 
}
?>