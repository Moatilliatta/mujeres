<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Cp_sepomex
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Cp_sepomex extends Db{
    
    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
        
    /**
    *Listado de códigos postales de Jalisco
    *@return array Lista de Códigos
    **/
    public static function codigosJalisco(){
      
      $sql = 
      'SELECT 
      d_codigo 
      from cp_sepomex 
      where c_estado = 14 
      GROUP BY d_codigo';
      
      return self::getInstance()->query($sql);

    }


    /**
    *Listado de asentamientos por código postal
    *@param int $d_codigo Código Postal
    *
    *@return array Listado de vialidades
    **/
    public static function listaVialidades($d_codigo = NULL){

      $sql = 
          'SELECT
            id,
            d_codigo,
            d_asenta,
            d_tipo_asenta 
            FROM `cp_sepomex` 
            where 1 ';

      $params = array();

      if($d_codigo != NULL){
        $sql .= ' AND d_codigo = ? ';
        $params[] = $d_codigo;
      }
          
      return self::executar($sql,$params);

    }
}