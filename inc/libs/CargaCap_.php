<?php
//Librería para leer/crear exceles
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

    public function __construct(){}
    public function __destruct(){}

    public static function hoja1($hoja_1){

    //Columnas: B,C,D,E,G,H,I,J,K,N,Q,T,W,Z,AD,AG,AJ,AM,AR
    
    //Variable donde guardaremos la Hoja 1
    $Hoja = NULL;
	$datos_h1 = NULL;
	$msg_no = 0;
	$j = 0;

    //Variable donde guardaremos la Hoja 1
    $maxFila = $hoja_1->getHighestRow();
	
	//Obtenemos columnas que necesitaremos	
	//Folio
	$B = $hoja_1->rangeToArray('B4:B'.$maxFila,null,null,false,true);
	//Apellido Paterno
	$C = $hoja_1->rangeToArray('C4:C'.$maxFila,null,null,false,true);
	//Apellido Materno
	$D = $hoja_1->rangeToArray('D4:D'.$maxFila,null,null,false,true);
	//Nombre
	$E = $hoja_1->rangeToArray('E4:E'.$maxFila,null,null,false,true);
	//Colonia
	$G = $hoja_1->rangeToArray('G4:G'.$maxFila,null,null,false,true);

	//GIA Inicial
	$H = $hoja_1->rangeToArray('H4:H'.$maxFila,null,null,false,true);

	//GIA Final
	$I = $hoja_1->rangeToArray('I4:I'.$maxFila,null,null,false,true);

	//Estatus Llamada
	$J = $hoja_1->rangeToArray('J4:J'.$maxFila,null,null,false,true);

	//Observacion de llamada
	$K = $hoja_1->rangeToArray('K4:K'.$maxFila,null,null,false,true);

	//Asistencia Salud
	$N = $hoja_1->rangeToArray('N4:N'.$maxFila,null,null,false,true);

	//Asistencia Alimentaria
	$Q = $hoja_1->rangeToArray('Q4:Q'.$maxFila,null,null,false,true);

	//Asistencia Ocupación
	$T = $hoja_1->rangeToArray('T4:T'.$maxFila,null,null,false,true);

	//Asistencia Madres y Padres
	$W = $hoja_1->rangeToArray('W4:W'.$maxFila,null,null,false,true);

	//Asistencia Producción
	$Z = $hoja_1->rangeToArray('Z4:Z'.$maxFila,null,null,false,true);
	
	//Asistencia Taller 1
	$AD = $hoja_1->rangeToArray('AD4:AD'.$maxFila,null,null,false,true);

	//Asistencia Taller 2
	$AG = $hoja_1->rangeToArray('AG4:AG'.$maxFila,null,null,false,true);

	//Asistencia Taller 3
	$AJ = $hoja_1->rangeToArray('AJ4:AJ'.$maxFila,null,null,false,true);

	//Producción
	$AM = $hoja_1->rangeToArray('AM4:AM'.$maxFila,null,null,false,true);

	//Teléfono
	$AR = $hoja_1->rangeToArray('AR4:AR'.$maxFila,null,null,false,true);

	//Recorremos cada registro y solo guardamos los que tengan datos
	for ($i=0; $i <= $maxFila ; $i++) { 

		if($C[$i]['C'] != NULL && $E[$i]['E'] != NULL){

			$Hoja[] =$B[$i]+$C[$i]+ $D[$i]+ $E[$i]+ $G[$i]+ $H[$i]+
				  $I[$i]+$J[$i]+ $K[$i]+ $N[$i]+ $Q[$i]+ $T[$i]+
				  $W[$i]+$Z[$i]+$AD[$i]+$AG[$i]+$AJ[$i]+$AM[$i]+
				  $AR[$i];
			
			//Validamos total de datos y columnas
			if(count($Hoja[$j]) == 19){

		            //Guardamos arreglo
		            $datos_h1[] = $Hoja[$j];

			}else{
			
			//Número incorrecto de columnas y/o datos incorrectos
			$msg_no = 20;
			break;

			}			

	        $j++;
		}
	}		

	print_r($B);

	/*
	echo 'datos_h1';
	print_r($datos_h1);
	exit;
	*/

	return array($Hoja,$datos_h1,$msg_no);

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

     //Obtenemos ruta
     $ruta = ($ruta == NULL)? $_SESSION['files_path'] : $ruta ;
	 
	 //ID recién generado
     $id_generado = NULL;

     //CURP Generado
     $curp = NULL;
     
	 //Armamos la ruta completa del archivo
	 $inputFileName =  $ruta.$nombre.".".$ext;     

	 //Hojas a leer
	 $sheetnames = array('zalatitan','san pedrito','oblatos',
	 					 'tlajomulco centro','santa lucia',
	 					 'Zalatitan','San Pedrito','Oblatos',
	 					 'Tlajomulco Centro','Santa Lucia','Hoja1'); 

	 //Cargamos archivo
	try {
	    /**  Identify the type of $inputFileName  **/
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
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
	list($Hoja1,$datos_h1,$msg_no) = self::hoja1($objPHPExcel->setActiveSheetIndex(0));

	//Si no obtenemos mensaje de error al procesar excel
	if($msg_no == 0){

		//Obtenemos total de columnas
		$total_cols_h1 = count($Hoja1[1]);
		$total_encuestados = count($Hoja1);		
		$id_mujer = array();

		//Recorremos total de filas en el excel
		foreach ($datos_h1 as $key => $value):				

		//Obtenemos ID del registro donde coincide
		//Búsqueda por nombre
		$mujeres_avanzando = self::buscaNombre($value['C'], $value['D'], $value['E']);

		//Deberíamos colocar aquí la búsqueda por folio e identificar
		//los que son familiares para que no sean tomados como
		//duplicados

		if(count($mujeres_avanzando) > 0){

			//Obtenemos punto rosa y fecha de capacitación
			$id_mujeres_avanzando = $mujeres_avanzando['id'];
			$id_caravana = $mujeres_avanzando['id_caravana'];

			//Lo ideal es obtener la información directa del excel
			//$id_seg_punto_rosa = $value['X'];
			//$fecha_capacitacion = $value['Y'];
			
			$id_seg_punto_rosa = SegPuntoRosa::get_by_id_caravana($id_caravana);
			
			/*
			print_r($mujeres_avanzando);
			echo $id_seg_punto_rosa.' - '.$id_caravana;
			exit;
			 */

			//Obtenemos arreglo de las capacitaciones
			$capacitaciones = self::getCap($value);

			
			//print_r($capacitaciones);
			//echo 'id_mujeres_avanzando: '.$id_mujeres_avanzando.
			//	 ' id_seg_punto_rosa: '.$id_seg_punto_rosa.
			//	 ' fecha_capacitacion: '.$fecha_capacitacion.'</br>';
				//exit;
			
			//Si tiene al menos una capacitación, guardaremos el registro			
			if(count($capacitaciones) > 0){

				//La búsqueda la realizamos con nombre + paterno (soundex)
				//evitaremos duplicados de familiares que son padres e hijos
				if(!in_array($id_mujeres_avanzando, $id_mujer)){
					
					//Guardaremos registro
					$msg_no = self::guarda($id_mujeres_avanzando,
									$id_seg_punto_rosa,
									$capacitaciones);			

					$id_mujer[] = $id_mujeres_avanzando;
					$total_cap += count($capacitaciones);
				}else{
					//Duplicado o tiene el mismo nombre propio y apellido paterno
					$total_duplicados++;
				}					

			}else{
				$total_sin_cap++;
			}	        

		}else{
			//No se encontró al beneficiario
			$msg_no = 30;

			$seg_no_enc = array('nombres' => $value['E'],
								'paterno' => $value['C'],
								'materno' => $value['D'],
								'colonia' => $value['G'],
								'telefono' => $value['AR']);			

			SegNoEnc::saveSegNoEnc($seg_no_enc);

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
			
	return array($msg_no,$totales);

    }

    /**
     * Llenamos el arreglo de las capacitaciones
     * @param  [type] $fila Fila de donde obtendremos cada una de las capacitaciones
     * @return [type]           [description]
     */
    private static function getCap($fila){
    	
    	//Arreglo donde cada letra es una columna y corresponde
    	//con el ID que se tiene en la tabla seg_capacitacion    	
    	$columnas = array(1 => 'N',2 => 'Q',3 => 'T', 4 => 'W', 5 => 'Z',
    					  6 => 'AD',7 => 'AG',8 => 'AJ',9 => 'AM');

    	//Arreglo donde tendremos las capacitaciones
    	$capacitaciones = array();

    	//Procedemos a llenar las columnas
    	foreach ($columnas as $key => $value):
    		if($fila[$value] == 'X'){
    			array_push($capacitaciones, $key);
    		}
    	endforeach;

    	return $capacitaciones;

    }

    /**
     * Buscamos beneficiaria por nombre
     * @param  [type] $paterno [description]
     * @param  [type] $materno [description]
     * @param  [type] $nombre  [description]
     * @return [type]          [description]
     */
    private static function buscaNombre($paterno,$materno,$nombre) {
   		
 	//$M = mujeresAvanzando::listadoMujer(NULL,NULL,NULL,$nombre,$paterno,$materno);

    
 	 //Obtenemos soundex del nombre y apellido paterno
   	 $soundex = mujeresAvanzando::getInstance()->soundex($nombre.' '.$paterno);

 	 $M = mujeresAvanzando::listadoMujer(NULL,NULL,NULL,
 	 									 NULL,NULL,NULL,
 	 									 NULL,NULL,NULL,$soundex);

 	 $mujeres_avanzando = (count($M)>0)? $M[0] : NULL;

 	 //$id_mujeres_avanzando = $mujeres_avanzando['id'];
 	 
 	 //echo 'Nombre: '.$paterno.' '.$materno.' '.$nombre.' '.$mujeres_avanzando['id'].'<br>';

 	 /*
 	 echo 'Nombre: '.$mujeres_avanzando['paterno'].' '.
 	 				$mujeres_avanzando['materno'].' '.
 	 				$mujeres_avanzando['nombres'].' ID:'.$id_mujeres_avanzando.'<br>';
 	 */
 	
	 //return $id_mujeres_avanzando;
	 return $mujeres_avanzando;

	}

    /**
     * Guardamos el registro en la tabla de seg_capacitacion_mujer
     * @param  [type] $datos_h1             [description]
     * @param  [type] $id_mujeres_avanzando [description]
     * @return [type]                       [description]
     */
    public static function guarda($id_mujeres_avanzando,$id_seg_punto_rosa,
    	$capacitaciones,$fecha_capacitacion = NULL)
    {
	                        
	//Actualizaremos el registro en mujeres avanzando
	$data = array(
			    'id_seg_punto_rosa' => $id_seg_punto_rosa,
			    'fecha_capacitacion' => $fecha_capacitacion,
			    'id_seg_capacitacion' => $capacitaciones
			    );    

	$msg_no = SegCapacitacionMujer::saveSegCapMujer($data,$id_mujeres_avanzando);


	return $msg_no;    	
   }
   
}