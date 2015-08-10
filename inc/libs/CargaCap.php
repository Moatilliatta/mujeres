<?php
//Librería para leer/crear exceles ommmaaaarr
include_once('PHPExcel.php');
//Obtenemos el modelo de mujeres avanzando
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'seg_capacitacion_mujer.php');
include_once($_SESSION['model_path'].'seg_punto_rosa.php');
include_once($_SESSION['model_path'].'seg_no_enc.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

class CargaCap extends Db{
    
    // Variables
    var $Hojas;    
//Muy importante especificar q el constructor esta vacio si no tomaria el constructor d la clase de la que heredas
    public function __construct(){}
    public function __destruct(){}

    public static function hoja1($hoja_1){

    //Columnas: B,C,D,E,G,H,I,J,K,N,Q,T,W,Z,AD,AG,AJ,AM,AR
    
    //Variable donde guardaremos la Hoja 1
    //print_r($hoja_1);
    //exit;
    $titular = Null;
    $Hoja = NULL;
	$datos_h1 = NULL;
	$msg_no = 0;
	$j = 0;

    //Variable donde guardaremos la Hoja 1
    $maxFila = $hoja_1->getHighestRow();
    
    //echo $maxFila;
    //exit;
	$folios = array();

	//Obtenemos columnas que necesitaremos	
	//Folio
	$E = $hoja_1->rangeToArray('E4:E'.$maxFila,null,null,false,true);
    //No de tarjeta de bienebono
    $F = $hoja_1->rangeToArray('F4:F'.$maxFila,null,null,false,true);
    //Apellido Paterno
	$G = $hoja_1->rangeToArray('G4:G'.$maxFila,null,null,false,true);
	//Apellido Materno
	$H = $hoja_1->rangeToArray('H4:H'.$maxFila,null,null,false,true);
	//Nombre
	$I = $hoja_1->rangeToArray('I4:I'.$maxFila,null,null,false,true);
	//Colonia
	$K = $hoja_1->rangeToArray('K4:K'.$maxFila,null,null,false,true);
    //Teléfono
	$L = $hoja_1->rangeToArray('L4:L'.$maxFila,null,null,false,true);
    //Numero de hijos
	$N = $hoja_1->rangeToArray('N4:N'.$maxFila,null,null,false,true);
    //Madres menores de edad
	$O = $hoja_1->rangeToArray('O4:O'.$maxFila,null,null,false,true);
    //Madres embarazadas o lactando menores de edad
	$P = $hoja_1->rangeToArray('P4:P'.$maxFila,null,null,false,true);
	//GIA Inicial
	$Q = $hoja_1->rangeToArray('Q4:Q'.$maxFila,null,null,false,true);
	//GIA Final
	$R = $hoja_1->rangeToArray('R4:R'.$maxFila,null,null,false,true);
	//Estatus Llamada
	$Y = $hoja_1->rangeToArray('Y4:Y'.$maxFila,null,null,false,true);
	//Observacion de llamada
	$Z = $hoja_1->rangeToArray('Z4:Z'.$maxFila,null,null,false,true);
    //Asistencia Salud
	$AC = $hoja_1->rangeToArray('AC4:AC'.$maxFila,null,null,false,true);
	//Asistencia Alimentaria
    $AO = $hoja_1->rangeToArray('AO4:AO'.$maxFila,null,null,false,true);
	//Asistencia Ocupación
	$AI = $hoja_1->rangeToArray('AI4:AI'.$maxFila,null,null,false,true);
	//Asistencia Madres y Padres
	$AL = $hoja_1->rangeToArray('AL4:AL'.$maxFila,null,null,false,true);
	//Asistencia Producción
	$AF = $hoja_1->rangeToArray('AF4:AF'.$maxFila,null,null,false,true);
	
	//Asistencia Taller 1
	$AS = $hoja_1->rangeToArray('AS4:AS'.$maxFila,null,null,false,true);

	//Asistencia Taller 2
	$AV = $hoja_1->rangeToArray('AV4:AV'.$maxFila,null,null,false,true);

	//Asistencia Taller 3
	$AY = $hoja_1->rangeToArray('AY4:AY'.$maxFila,null,null,false,true);

	//Producción
	$BB = $hoja_1->rangeToArray('BB4:BB'.$maxFila,null,null,false,true);
    
    //----------------------------------------------------------------------
    
    //*Emprendurismo
    $BE = $hoja_1->rangeToArray('BE4:BE'.$maxFila,null,null,false,true);
    //*No Gas
    $BL = $hoja_1->rangeToArray('BL4:BL'.$maxFila,null,null,false,true);
    //*Chimenea
    $BM = $hoja_1->rangeToArray('BM4:BM'.$maxFila,null,null,false,true);
   	
	//Recorremos cada registro y solo guardamos los que tengan datos
	for ($i=0; $i <= $maxFila ; $i++) { 

		if($G[$i]['G'] != NULL && $I[$i]['I'] != NULL){

			$Hoja[] =$E[$i]+$F[$i]+$G[$i]+ $H[$i]+ $I[$i]+ $K[$i]+ $L[$i]+$N[$i]+$O[$i]+$P[$i]+
				  $Q[$i]+$R[$i]+ $Y[$i]+ $Z[$i]+ $AC[$i]+ $AF[$i]+
				  $AI[$i]+$AL[$i]+$AO[$i]+$AS[$i]+$AV[$i]+$AY[$i]+
				  $BB[$i]+$BE[$i]+$BL[$i]+$BM[$i];
			
			//Validamos total de datos y columnas
			if(count($Hoja[$j]) == 26){

		            //Guardamos arreglos
		            $datos_h1[] = $Hoja[$j];
		            $folios[] = intval($E[$i]['E']);

			}else{
			
			//Número incorrecto de columnas y/o datos incorrectos
			$msg_no = 20;
			break;

			}			

	        $j++;
		}
	}	
    //print_r($datos_h1);
    //exit;	
    
	return array($Hoja,$datos_h1,$folios,$msg_no);

    }

    /**
    * Carga de excel
    *@param varchar $nombre Nombre del archivo SIN extensión
    *@param varchar $ruta Ruta del archivo (de ser diferente a la establecida)
    *
    *@return Array que contiene
	*    	 int $msg_no Mensaje generado
	*		 int $id_generado ID generado
    **/
    public static function carga($nombre,$ruta = NULL,$id_caravana=0,$ext = 'xls')
    {

    //Inicializamos variables
    $msg_no = 0;
    $total_encuestados = 0;
    $total_registrados = 0;
    $total_duplicados = 0;   
    $total_sin_coinc = 0;
    $total_cap = 0;
    $total_sin_cap = 0;
    $total_segura = 0;
    $no_encontrado = array();

     //Obtenemos ruta normal pero se puede guardar en otro destino
     $ruta = ($ruta == NULL)? $_SESSION['files_path'] : $ruta ;
	 
	 //ID recién generado
     $id_generado = NULL;

     //CURP Generado
     $curp = NULL;
     
	 //Armamos la ruta completa del archivo
	 $inputFileName =  $ruta.$nombre.".".$ext; 
     
     //cho $inputFileName;
     //exit;    

	 //Hojas a leer segun el archivo exel a leer
	 $sheetnames = array('zalatitan','san pedrito','oblatos',
	 					 'tlajomulco centro','santa lucia',
	 					 'Zalatitan','San Pedrito','Oblatos',
	 					 'Tlajomulco Centro','Santa Lucia','Hoja1'); 

	 //Cargamos archivo
	try {
	    /**  Identify the type of $inputFileName  **/
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        
        /** Usamos caché **/
       // $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
       // $cacheSettings = array( ' memoryCacheSize ' => '8MB');
        //PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        
		/**  Create a new Reader of the type that has been identified  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		/**  Advise the Reader of which WorkSheets we want to load  **/ 
		$objReader->setLoadSheetsOnly($sheetnames); 
		/**  Load $inputFileName to a PHPExcel Object  **/ 
		$objPHPExcel = $objReader->load($inputFileName);		
	} catch(PHPExcel_Reader_Exception $e) {
	    die('Error al cargar archivo "'.
	    pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	}

	//Obtenemos la hoja	
	list($Hoja1,$datos_h1,$folios,$msg_no) = self::hoja1($objPHPExcel->setActiveSheetIndex(0));
    //print_r($datos_h1);
    //exit;
	//Si no obtenemos mensaje de error al procesar excel
	if($msg_no == 0){

		//Obtenemos total de columnas
		$total_cols_h1 = count($Hoja1[1]);
		$total_encuestados = count($Hoja1);		
		$id_mujer = array();

		list($duplicados,$unicos) = SegCapacitacionMujer::foliosDuplicados($folios);
		
		//Recorremos total de filas en el excel
		foreach ($datos_h1 as $key => $value):
        
        //Obtenemos ID del registro donde coincide
		//Búsqueda por nombre y folio
        $mujeres_avanzando = self::buscaFolio($value['E'], //folio 
       	                          $value['G'], //paterno
					               $value['H'], //materno
								   $value['I']); // nombre				
	
		if(count($mujeres_avanzando) > 0){

			//Obtenemos punto rosa y fecha de capacitación
			$id_caravana = $mujeres_avanzando['id_caravana'];
            $id_mujeres_avanzando = $mujeres_avanzando['id'];
			
            //echo $id_caravana;
            //exit;
           	//Lo ideal es obtener la información directa del excel
			//$id_seg_punto_rosa = $value['X'];
			//$fecha_capacitacion = $value['Y'];			
			$seg_punto_rosa = SegPuntoRosa::get_by_id_caravana($id_caravana);
             //print_r($seg_punto_rosa);
             //exit;
			$id_seg_punto_rosa = (isset($seg_punto_rosa['id']))? $seg_punto_rosa['id'] : NULL;

			//Obtenemos arreglo de las capacitaciones
			list($capacitaciones,$H,$HD) = self::getCap($value);
        //print_r($capacitaciones).'<br>';
	
			//Si tiene al menos una capacitación, guardaremos el registro			
			if(count($capacitaciones) > 0){

				//La búsqueda la realizamos con nombre + paterno (soundex)
				//evitaremos duplicados de familiares que son padres e hijos
				if(!in_array($id_mujeres_avanzando, $id_mujer)){
					
					//Guardaremos registro
					$msg_no = self::guarda($id_mujeres_avanzando,
									$id_seg_punto_rosa,
									$capacitaciones,
                                    $H,
                                    $HD);
                                   			

					$id_mujer[] = $id_mujeres_avanzando;
					$total_cap += count($capacitaciones);
				}else{
					//Duplicado o tiene el mismo nombre propio y apellido paterno
					$total_duplicados++;
				}					

			}else{
				$total_sin_cap++;
			}
            
           
            //$selected = ($g['id'] == $mujeres_avanzando['id_grado'])? 'selected': '';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////           
            $menores_edad = ($value['O'] == 'X')?'SI':'NO';
            $embarazadas_lactando = ($value['P'] == 'X')?'SI':'NO';
            $no_gas = ($value['BL'] == 'X')?'SI':'NO';
            $si_chimenea = ($value['BM'] == 'X')?'SI':'NO';
             self::getInstance()->startTransaction();
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////              
            //Actualizamos campos nuevos en tabla mujeres_avanzando
                    $datos = Array (
                    'numero_tarjeta' => $value['F'],
                    'menores_edad' => $menores_edad,
                    'embarazadas_lactando' => $embarazadas_lactando,
                    'num_hijos' => $value['N'],
                    'no_gas' => $no_gas,
                    'si_chimenea' => $si_chimenea
                    
                    
               );
                
                //print_r($datos);
                //exit;
                self::getInstance()->where ('id', $id_mujeres_avanzando);
                
                $obj = self::getInstance()->update ('mujeres_avanzando', $datos);
                
                self::getInstance()->commit();
        }else{
			//No se encontró al beneficiario
            
			$msg_no = 30;

		    $seg_no_enc = array('folio' => $value['E'],
								'nombres' => $value['I'],
								'paterno' => $value['G'],
								'materno' => $value['H'],
								'colonia' => $value['K'],
								'telefono' => $value['L']);			

			
         list($resp,$id) = SegNoEnc::saveSegNoEnc($seg_no_enc);
         
         if ($resp == 1) {
         
         $no_encontrado[] = $id;
         
         }
        

		}

		//Dependiendo la respuesta hacemos incrementos
		switch ($msg_no) {
			case 1:
					break;
			case 21://Registro de Beneficiario duplicado
		            $total_duplicados++;
		            break;
		    case 30://No se encontró coincidencia de beneficiario
		    		$total_sin_coinc++;
		    		break;
		    default:
		    		break;
		}
		
	
		endforeach;
        
        //print_R($no_encontrado);
        //exit;
		
		//Obtenemos total de registrados
		$total_registrados = count($id_mujer);

		//Si tenemos algún registro, ponemos como exitosa la carga del archivo
		$msg_no = ($total_registrados > 0)? 1 :$msg_no;

		$totales = array('total_encuestados' => $total_encuestados,
						'total_registrados' => $total_registrados,
						'total_cap' => $total_cap,
						'total_sin_coinc' => $total_sin_coinc,
						'total_sin_cap' => $total_sin_cap,
						'total_duplicados' => $total_duplicados,
						'nombre' => $nombre);

	}                            		
			
	return array($msg_no,$totales,$no_encontrado);

    }

    /**
     * Llenamos el arreglo de las capacitaciones
     * @param  [type] $fila Fila de donde obtendremos cada una de las capacitaciones
     * @return [type]           [description]
     */
    private static function getCap($fila){
        //print_r($fila);
        //exit;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  	
    	//Arreglo donde cada letra es una columna y corresponde
    	//con el ID que se tiene en la tabla seg_capacitacion    	
    	$columnas = array(1 => 'AC',2 => 'AF',3 => 'AI', 4 => 'AL', 5 => 'AO',
    					  6 => 'AS',7 => 'AV',8 => 'AY',9 => 'BB',10 => 'BE');

    	//Arreglo donde tendremos las capacitaciones
    	$capacitaciones = array();
        $H = array();
        $HD = array();

    	//Procedemos a llenar las columnas
    	foreach ($columnas as $key => $value):
         
         
         switch(trim($fila[$value])){
                
                case 'A': case 'X':
                 array_push($capacitaciones, $key);      
                    break;
                case 'H':
                 array_push($capacitaciones, $key);
                 array_push($H, $key);
                    
                    break;    
                case 'HD':
                 array_push($capacitaciones, $key);
                 array_push($HD, $key);
                  
                    break;    
                    
            }
         
      
    	endforeach;
        
        //print_r($H).'<BR>';
        //print_r($HD).'<BR>';
        //EXIT;

    	return array ($capacitaciones,$H,$HD);

    }

    /**
     * Buscamos beneficiaria por nombre
     * @param  [type] $paterno [description]
     * @param  [type] $materno [description]
     * @param  [type] $nombre  [description]
     * @return [type]          [description]
     */
    private static function buscaNombre($folio = NULL,$paterno,$materno,$nombre) {
   	
 	 //Obtenemos soundex del nombre y apellido paterno
   	 $soundex = mujeresAvanzando::getInstance()->soundex($nombre.' '.$paterno);
     //echo $soundex;
     //exit;

 	 $mujeres_avanzando = mujeresAvanzando::get_by_soundex_folio($soundex,$folio);

 	 return $mujeres_avanzando;

	}
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    //prueba
     private static function buscaName($folio = NULL,$paterno,$materno,$nombre) {
   	
 	 //Obtenemos soundex del nombre y apellido paterno
   	 $soundex = mujeresAvanzando::getInstance()->soundex($nombre);
     //echo $soundex;
     //exit;

 	 $mujeres_avanzando = mujeresAvanzando::get_by_soundex_folio($soundex,$folio);

 	 return $mujeres_avanzando;

	}
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
      public static function buscaFolio($folio=null,$paterno=null,$materno=null,$nombre=null){    
       $mujeres_avanzando = array();
       //$datos = self::buscaNombre($folio,$paterno,$materno,$nombre);
       //datos que vienen del excel
         $datos = self::buscaName($folio,$paterno,$materno,$nombre);
        //print_R($datos);
        //exit; 
                                   
        if ($datos > 0){
           
           $mujeres_avanzando['id_caravana'] = $datos['id_caravana'];
           
           
           $num_folio = $datos['num_folio'];
          
          
           if (intval($num_folio) == 0){
             //titular
            $mujeres_avanzando['id'] = $datos['id'];
           
            //familiar
           }elseif(intval($num_folio) > 0){
            
            $mujeres_avanzando['id'] = $datos['id'];
            
           }
           
        }
        //print_R($mujeres_avanzando);
        //exit;
       
      
        return ($mujeres_avanzando);
    }
    
    public static function buscaFamiliar($folio=null,$paterno=null,$materno=null,$nombre=null){
        
        $sql='
                SELECT
                m.id,
                m.nombres,
                m.materno,
                m.paterno
                FROM `mujeres_avanzando` m
                where m.nombres = ? and m.paterno = ? and m.materno = ? and m.folio = ?
        ';
        
        
        $params = array($nombre,$paterno,$materno,$folio);
        
        $integrantes = self::executar($sql,$params);
        $integrantes = $integrantes [0];
               
        return $integrantes;
    }

    /**
     * Guardamos el registro en la tabla de seg_capacitacion_mujer
     * @param  [type] $datos_h1             [description]
     * @param  [type] $id_mujeres_avanzando [description]
     * @return [type]                       [description]
     */
    public static function guarda($id_mujeres_avanzando,$id_seg_punto_rosa,
    	$capacitaciones,$H,$HD,$fecha_capacitacion = NULL)
    {
        //echo $id_seg_punto_rosa;
        //exit;
	                        
	//Actualizaremos el registro en mujeres avanzando
	$data = array(
			    'id_seg_punto_rosa' => $id_seg_punto_rosa,
			    'fecha_capacitacion' => $fecha_capacitacion,
			    'id_seg_capacitacion' => $capacitaciones
                );    

	$msg_no = SegCapacitacionMujer::saveSegCapMujer($data,$id_mujeres_avanzando,$H,$HD);


	return $msg_no;    	
	
   }
   
}