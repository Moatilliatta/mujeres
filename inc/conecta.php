<?php

    // Usar este en producción, no envia errores
	//error_reporting(0); 
	
	//Uso de reporte de errores anteriores
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR);
	
	//Uso de reporte de errores más estricto
	//error_reporting(E_ALL ^ E_STRICT);
	
	date_default_timezone_set('America/Mexico_City');

	//comentar la siguiente linea en producción
	ini_set('display_errors', 'On'); 
	
	//incluimos la clase MyqliDb por que es externo a la clase 
    include('libs/Db.php');
    
    //hacemos una instancia ala clase MysqliDb en la variable $db
    $db = new Db('localhost', 'root', '', 'mujeres_avanzando');
	//$db = new Db('localhost', 'root', 'difJalisc0', 'mujeres_avanzando');
	//$db = new Db('localhost', 'root', 'Muj3rd1f.jal', 'mujeres_avanzando');
    
    $ruta = "C:\\xampp\\htdocs\\padron\\";

    //Habilitamos log de errores
    $db->setErrTrack(true,$ruta);

    /* Comprueba la conexión */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
	//str_ireplace remplaza
	// Scripts para evitar inyección de código
	foreach( $_REQUEST as $variable => $valor )
	{ 
		$_REQUEST [ $variable ] = str_ireplace ( "'" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( '"' , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "SELECT" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "INSERT" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "UPDATE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "DELETE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "CREATE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "TRUNCATE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "DROP" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "FROM" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "SHOW" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "TABLES" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "TABLE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "WHERE" , "" , $_REQUEST [ $variable ]); 
		$_REQUEST [ $variable ] = str_ireplace ( "LIKE" , "" , $_REQUEST [ $variable ]); 
	} 
	
	// Fin de scripts para evitar inyección de código
?>