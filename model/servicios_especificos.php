<?php
/**
 * Clase que nos permite administrar lo relacionado a los Apoyos_Otorgados
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['model_path'].'beneficiario_pys.php');

class Servicios_especificos extends Db{
    
    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
      
    /**
    * Obtenemos listado de Padres
    **/
    public static function listaPadres(){

        $cols = Array ("id, nombre");        
        self::getInstance()->where('padre', 0);
        $padres = self::getInstance()->get('servicios_especificos',null,$cols);

        return $padres;
    }

    /**
    * Obtenemos listado de servicios
    *
    * @return Array Listado de servicios
    **/
    public static function listaServicios($padre = NULL){
        
        $sql = 'SELECT 
                id,
                nombre
                from servicios_especificos 
                where ? ';

        $params = array(1);

        if($padre != NULL){
            $sql .= ' AND padre = ? ';
            $params[] = $padre;
        }else{
            $sql .= ' AND (padre is null or padre > 0) ';
        }
        
        return self::executar($sql,$params);

    }

}
