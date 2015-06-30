<?php
/**
 * Clase que nos permite administrar variables globales, restricciones y demás
 * **/

//Inclumos librería de paginación

include('Pagination.php');

class Paginador extends MysqliDb
{

    public function __construct(){}
    public function __destruct(){}

     /**
      * Número de páginas desplegadas en la barra de navegación.    
      * 
      * @var int  
      * */
     public static $pagesPerSection = 15;

     /**
      * Desplegado de opciones
      * array(5, 10, 25, 50, "All");
      * @var Array
      * */    
     private static $options = array(15,30);

     /**
      * Nombre del ID para el paginador
      * 
      * @var int
      * */
     public static $paginationID = "comments";	

     /**
      * Lo siguiente son nombres de las clases de estilo
      * 
      * @var string
      * */      
      public static $stylePageOff    = "pageOff";
      public static $stylePageOn	 = "pageOn";
      public static $styleErrors	 = "paginationErrors";
      public static $styleSelect	 = "paginationSelect";      

    public function setPagesPerSection($number = 15){
      self::$pagesPerSection = $number;
    }

    /**
    *Regresamos resultados desde una función NO estática
    *@param string $sql_pag consulta a paginar
    *@param array $params parámetros de la consulta
    *
    *@return array Arreglo con resultados y paginador
    */
    public function objPaginar($sql_pag,$params){
       return self::paginar($sql_pag,$params);
    }

    public static function paginar($sql_pag,$params,$start = NULL,$end = NULL){

        //En caso de tener limites los asignamos
        $limit = (is_int($start) && is_int($end))? " LIMIT $start, $end" : ""; 

        //Obtenemos el total de registro del sql a paginar
        $sql='SELECT COUNT(*) FROM ('.$sql_pag.$limit.') pag';

        //Hacemos consulta
        $results = self::getInstance()->rawQuery($sql,$params);        
        
        //Obtenemos el total de registros
        $totalEntries = array_shift($results[0]);

        $Pagination = new Pagination(
        $totalEntries, 
        self::$pagesPerSection, 
        self::$options, 
        self::$paginationID, 
        self::$stylePageOff, 
        self::$stylePageOn, 
        self::$styleErrors, 
        self::$styleSelect);

        //Obtenemos el rango de registros  mostrar
        $start = $Pagination->getEntryStart();
        $end = $Pagination->getEntryEnd();

          
        //preparamos parámetros
        $params[] = $start;
        $params[] = $end;

        //Armamos la consulta final
        $sql = $sql_pag." LIMIT ?, ?";

        $results = self::getInstance()->rawQuery($sql,$params);

        return array($results,$Pagination);
     }
}