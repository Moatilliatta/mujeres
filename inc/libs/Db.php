<?php 
/**
 * Clase que utlizaremos para el sistema, heredaremos todo
 * lo de MysqliDb (y usaremos el código tal cual está actualizado
 * desde su repositorio original). Usaremos funciones específicas
 * para este sistema
 */
include_once('MysqliDb.php');

class Db extends MysqliDb {
	
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
        return $this->_mysqli->real_escape_string($string);
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
    protected static function ultimoQuery(){
      //Ejecutamos
      $resultado = "Última Sentencia: ".self::getInstance()->getLastQuery();
      
      //Regresamos resultado
      return $resultado;
    }

    /**
     * Ejecutamos sentencia sql con parámetros
     * @param string $sql Sentencia SQL
     * @param array $params Cada uno de los parámetros de la sentencia
     * 
     * @return int Resultado
     * */
    public static function executar($sql,$params){
        //Ejecutamos
        $resultado = self::getInstance()->rawQuery($sql, $params);

        //Regresamos resultado
        return $resultado;        
    }
}

?>