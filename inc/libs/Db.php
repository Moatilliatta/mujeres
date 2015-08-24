<?php 
/**
 * Clase que utlizaremos para el sistema, heredaremos todo
 * lo de MysqliDb (y usaremos el código tal cual está actualizado
 * desde su repositorio original). Usaremos funciones específicas
 * para este sistema
 */
include_once('MysqliDb.php');

class Db extends MysqliDb {    
    
    /**
     * Variable for error tracking
     */
    protected $errTrack = false;
    protected $routeTrack;
    protected $errMsg = '';

    public function __construct($host = NULL, $username = NULL, 
        $password = NULL, $db = NULL, $port = NULL, $charset = 'utf8')
    {
        parent::__construct($host,$username,$password,$db,$port,$charset);
    }

    /**
     * Use of real_escape_string function plus some reserverd words substitution
     * @param string $string String to validate
     * 
     * @return string 
     * */
    public function real_escape_string($string){
                
        $reserverd_words = array(" SELECT "," INSERT " ," UPDATE " ," DELETE "," CREATE " ,
        " TRUNCATE " ," DROP " ," FROM " ," SHOW " ," TABLES "," TABLE " ," WHERE " ,
        " LIKE ","'",'"','%','*');
        
        //Replace reserved words on string;
        $string = str_ireplace($reserverd_words, '' , $string);
        
        //if magic quotes are on, we add stripslashes
        if(get_magic_quotes_gpc() != 0) {
            $string = stripslashes($string);
        }
        
        //return string with real_escape_string function
        return $this->escape($string);
    }
    
    /**
    *Use function SOUNDEX 
    *@param string $string String to convert
    *
    *@return string String converted
    **/
    protected function soundex($string){
        $sql = 'SELECT SOUNDEX(?) as soundex_word';
        $params = array($string);

        $result = $this->rawQuery($sql,$params);
        $result = $result[0];
        
        return $result['soundex_word'];
    }

    /**
     * Obtenemos la última sentencia ejecutada
     * @return string $sql con parámetros
     */
    public static function ultimoQuery(){
      //Ejecutamos
      $resultado = "Última Sentencia: ".self::getInstance()->getLastQuery();
      
      //Regresamos resultado
      return $resultado;
    }

    /**
     * Función donde iniciamos el "rastro" de algún query
     * @return [type] [description]
     */
    public static function iniciaRastro(){
        self::getInstance()->setTrace(true);
    }

    /**
     * Obtenemos arreglo del rastro que se ha hecho de la
     * consulta (debe ejecutarse primero iniciaRastro)
     * @return [type] [description]
     */
    public static function arregloRastro(){
        return self::getInstance()->trace;
    }

    /**
     * Ejecutamos sentencia sql con parámetros
     * @param string $sql Sentencia SQL
     * @param array $params Cada uno de los parámetros de la sentencia
     * 
     * @return int Resultado
     * */
    public static function executar($sql,$params = null){        

        //Ejecutamos
        $resultado = self::getInstance()->rawQuery($sql, $params);     

        //Regresamos resultado
        return $resultado;        
    }

     /**
     * Save errors into a file
     * @param bool $enabled Allow create file to save errors
     * @param string $route Set a route to create this file
     */
    public function setErrTrack($enabled,$route=""){
        $this->errTrack = $enabled;

        // "/var/www/html/inc/libs/";
        $this->routeTrack = $route;
    }

     /**
      * Internal function to create a .txt file to save all the log
      * collected from an specific query
      * @param  [type] $data Data that is going to be written in the file
      */
    protected function createLog($data){ 
        
        //Guardamos directorio actual
        $actual = getcwd();
        
        $file = $this->routeTrack."mysqli_errors.txt";
        
        $fh = fopen($file, 'a') or die("Can't open/create file");
        fwrite($fh,$data);
        fclose($fh);
    
    }   

    /**
     * Reset states after an execution (OVERWRITTEN METHOD)
     *
     * @return object Returns the current instance.
     */
    protected function reset()
    {
        if ($this->traceEnabled)
            $this->trace[] = array ($this->_lastQuery, (microtime(true) - $this->traceStartQ) , $this->_traceGetCaller());

        if($this->errTrack && $this->_mysqli->error != NULL){

            //Put the MySQLi errno in order to complement the error info
            $this->errMsg = date("Y-m-d h:i:s ").'MySQLi errno: '.
                        $this->_mysqli->errno.' - '.
                        $this->_mysqli->error." \nQuery: ".
                        $this->_lastQuery."\n\n";

            $this->createLog($this->errMsg);

        }

        $this->_where = array();
        $this->_join = array();
        $this->_orderBy = array();
        $this->_groupBy = array();
        $this->_bindParams = array(''); // Create the empty 0 index
        $this->_query = null;
        $this->_queryOptions = array();
        $this->returnType = 'Array';
        $this->_nestJoin = false;
        $this->_tableName = '';
        $this->_lastInsertId = null;
        $this->_updateColumns = null;
    }
   
}

?>