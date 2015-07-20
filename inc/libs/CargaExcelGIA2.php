<?php
/**
 * ATENCIÓN : La información corresponde a la clase CargaExcel3.php
 */

//Librería para leer/crear exceles
include_once('PHPExcel.php');
//Obtenemos el modelo de mujeres avanzando
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'historico_gia.php');
include_once($_SESSION['model_path'].'registro_excel_enhina.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

class CargaExcelGIA extends Db{
    
    // Variables
    var $Hojas;    

    public function __construct(){}
    public function __destruct(){}

    public static function hoja1($hoja_1){

    //Columnas: B,C,Y,AE,FZ,GA,GB,GC,GD,GE

    //Variable donde guardaremos la Hoja 2
    $H = NULL;
	$datos_h1 = NULL;
	$datos_h1_false = NULL;
	$msg_no = 0;
	$j = 0;

    //Variable donde guardaremos la Hoja 1
    $maxFila = $hoja_1->getHighestRow();
	
    //Obtenemos columnas que necesitaremos	
	$B = $hoja_1->rangeToArray('B2:B'.$maxFila,null,null,false,true);//Entrevista id
	$C = $hoja_1->rangeToArray('C2:C'.$maxFila,null,null,false,true);// Folio
	$Y = $hoja_1->rangeToArray('Y2:Y'.$maxFila,null,null,false,true);// completa 'true'
	$AE = $hoja_1->rangeToArray('AE2:AE'.$maxFila,null,null,false,true); //encuestador 'mac'

    //IndiceGlobal, Nivel Socioeconomico, Calidad Dieta, Diversidad, Variedad, ELCSA
	$FZ_GE = $hoja_1->rangeToArray('FZ2:GE'.$maxFila,null,null,false,true);	

	//Recorremos cada registro y solo guardamos los que tengan datos
	for ($i=0; $i <= $maxFila ; $i++) { 

		if($B[$i]['B'] != NULL && $C[$i]['C'] != NULL){

			$H[] =$B[$i]+$C[$i]+$Y[$i]+$AE[$i]+$FZ_GE[$i];
			
			//Validamos total de datos y columnas
			if(count($H[$j]) == 10){

				//Obtenemos los entrevistados
		         $entrevistado = trim(strtoupper($H[$j]['Y']));

                //echo $entrevistado.' - '.$H[$j]['Y'];
                //exit;

		        if($entrevistado == 'TRUE' || $entrevistado == 'VERDADERO' || $entrevistado == 1){

		            //Guardamos arreglo
		            $datos_h1[] = $H[$j];

		        }else{
		        	$datos_h1_false[] = $H[$j]['B'];
		        }			        

			}else{
			
				//Número incorrecto de columnas y/o datos incorrectos
				$msg_no = 20;
				break;

			}			

	        $j++;
		}
	}		

	/*
	echo'Hoja1: ';
	print_r($H);
	exit;
	*/

	return array($H,$datos_h1,$datos_h1_false,$msg_no);

    }

    public static function hoja2($hoja_2){
     
     //Columnas "C","E","F","G","H","J","X"

     //Variable donde guardaremos la Hoja 2
     $H2 = NULL;
     $beneficiarias = NULL;
     $familiares = NULL;
	 $j = 0;
	 $msg_no = 0;

     //Obtenemos la fila más grande
     $maxFila = $hoja_2->getHighestRow();
     
      //Obtenemos columnas que necesitaremos
     $C = $hoja_2->rangeToArray('C2:C'.$maxFila,null,null,false,true);//Entrevista id
     $X = $hoja_2->rangeToArray('X2:X'.$maxFila,null,null,false,true);//Persona Entrevistada
     $J = $hoja_2->rangeToArray('J2:J'.$maxFila,null,null,false,true);//Genero

     //Paterno, Materno, Nombre, FechaNacimiento
     $E_H = $hoja_2->rangeToArray('E2:H'.$maxFila,null,null,false,true);     

     //Recorremos cada registro y solo guardamos los que tengan datos
     for ($i=0; $i <= $maxFila ; $i++) { 
		if($C[$i]['C'] != NULL){

			$H2[] =$C[$i]+$X[$i]+$J[$i]+$E_H[$i];	

			//Validamos total de datos y columnas
			if(count($H2[$j]) == 7){

				//Obtenemos los entrevistados
	       		if(trim($H2[$j]['X']) == 'SI'){

	            //Guardamos arreglo
	            $beneficiarias[] = $H2[$j];

		        }else{
		            //Es un familiar
		            $familiares[] = $H2[$j];
		        }

			}else{
			
			//Número incorrecto de columnas y/o datos incorrectos
			$msg_no = 20;
			break;
			}				

	        $j++;
		}
	  }

		/*
		echo'Hoja2: ';
		print_r($H2);
		exit;
		*/	

	  return array($H2,$beneficiarias,$familiares,$msg_no);

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
    public static function carga($nombre,$ruta = NULL,$id_caravana=0,$visita = 0,$ext = 'xls'){
      

    //Inicializamos variables
    $msg_no = 0;
    $total_encuestados = 0;
    $total_enc_completo = 0;
    $total_prog_mac = 0;
    $total_prog_map = 0;
    $total_prog_mas = 0;
    $total_registrados = 0;
    $total_no_coinciden = 0;
    $total_no_encontrado = 0;
    $id_entrevista_noc = NULL;
    $total_enc_inc = 0;
	$total_severa = 0;
	$total_moderada = 0;
	$total_leve = 0;
	$total_segura = 0;
	$total_otra = 0;

	//Arreglo de personas no encontradas en sistema
	$no_encontradas = array();

     //Obtenemos ruta
     $ruta = ($ruta == NULL)? $_SESSION['files_path'] : $ruta ;
	 
	 //ID recién generado
     $id_generado = NULL;

     //CURP Generado
     $curp = NULL;
     
	 //Armamos la ruta completa del archivo
	 $inputFileName =  $ruta.$nombre.".".$ext;     

	 //Hojas a leer
	 $sheetnames = array('ENTREVISTAS','INTEGRANTES',
	 					 'ENTREVISTA','INTEGRANTE',
	 					 'Entrevistas','Integrantes',
	 					 'Entrevista','Integrante',
	 					 'entrevista','integrante');

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

	//Obtenemos las 2 hojas	
	list($Hoja1,$datos_h1,$datos_h1_false,$msg_no) = self::hoja1($objPHPExcel->setActiveSheetIndex(0));
	list($Hoja2,$beneficiarias,$familiares,$msg_no) = self::hoja2($objPHPExcel->setActiveSheetIndex(1));
	
	//Si no obtenemos mensaje de error al procesar excel
	if($msg_no == 0){

		//Obtenemos total de columnas
		$total_cols_h1 = count($Hoja1[1]);
		$total_cols_h2 = count($Hoja2[1]);
		$total_encuestados = count($Hoja1);
		$total_enc_completo = count($datos_h1);
		$total_enc_inc = count($datos_h1_false);		

		//Recorremos total de filas en el excel
		foreach ($datos_h1 as $key => $value):				

		//Obtenemos ID del registro donde coincide
		$k = self::searchForId($value['B'], $beneficiarias);   				

        //Guardaremos registro
		$msg_no = self::guarda($value,$beneficiarias[$k],$id_caravana,$visita);
				
		//Dependiendo la respuesta hacemos incrementos
		switch ($msg_no) {
			case 1:$total_registrados++;
					break;
		    case 22://No coinciden ID de entrevista
		    		$total_no_coinciden++;
		    		$id_entrevista_noc[$value['C']]=$value['B'];
		    		break;

			case 30:$total_no_encontrado++;
					
					$fecha = substr($beneficiarias[$k]['H'],0,10);
        			$fecha_nacimiento = Fechas::fechadmyAymd($fecha);

					$no_enc['id_entrevista']=$beneficiarias[$k]['C'];
					$no_enc['folio']=$value['C'];
					$no_enc['paterno']=$beneficiarias[$k]['E'];
					$no_enc['materno']=$beneficiarias[$k]['F'];
					$no_enc['nombre']=$beneficiarias[$k]['G'];
					$no_enc['fecha_nacimiento']=$fecha_nacimiento;
					$no_encontradas[] = $no_enc;
		            break;
		    default:
		    		break;
		}

		//Obtenemos programa
		$prog = substr($value['AE'], 0,3);
        
        //echo $prog;
        //exit;

		//Dependiendo el programa evaluamos
		switch ($prog) {
			case 'MAC': $total_prog_mac++;
			            break;
			case 'MAP': $total_prog_map++;
						break;
            case 'MAS': $total_prog_mas++;
						break;            
			default: break;
		}

		//Obtenemos grado de inseguridad
		$grado_ins = $value['FU'];

		//Dependiendo el programa evaluamos
		switch (trim(strtoupper($grado_ins))){
			case 'SEVERA':$total_severa++;
		    			  	break;
		    case 'MODERADA': $total_moderada++;
					    	break;
			case 'LEVE': $total_leve++;
							break;
			case 'SEGURA': $total_segura++;
					    	break;
			default: $total_otra++;
				            break;
		}

		endforeach;

		$datos_h1_false = ($datos_h1_false == NULL)? array(0) : $datos_h1_false ;
		$id_entrevista_noc = ($id_entrevista_noc == NULL)? array() : $id_entrevista_noc ;
		

		$totales = array('total_encuestados' => $total_encuestados,
						'total_enc_completo' => $total_enc_completo,
						'total_enc_inc' => $total_enc_inc,
						'total_prog_mac' => $total_prog_mac,
						'total_prog_map' => $total_prog_map,
                        'total_prog_mas' => $total_prog_mas,
						'total_registrados' => $total_registrados,
						'total_no_coinciden' => $total_no_coinciden,
						'total_no_encontrado' => $total_no_encontrado,
						'total_severa' => $total_severa,
						'total_moderada' => $total_moderada,
						'total_leve' => $total_leve,
						'total_segura' => $total_segura,
						'total_otra' => $total_otra,
						'id_entrevista_noc' => $id_entrevista_noc,
						'nombre' => $nombre);

		Registro_excel::saveRegistroexcel($totales); 

	}    
	return array($msg_no,$totales,$no_encontradas);

    }

    /**
    *Buscamos un valor en un arreglo de arreglos
    * @param int $id ID a buscar
    * @param array $array Arreglo de arreglos donde buscaremos
    *
    * @return int $key Llave de la coincidencia
    **/
    private static function searchForId($id, $array) {
   		
 		//Agregamos & al valor para que no see cree una copia
 		//del arreglo y por tanto sea más rápida su búsqueda
 		$valor = NULL;

	   	foreach ($array as $key => & $val):
	       if ($val['C'] === $id) {
	           $valor = $key;
	           break;
	       }
	   endforeach;

	   return $valor;
	}

    /**
    *Procesamos los registros de beneficiarios y sus familiares de la hoja 1
    *@param array $hoja1 Hoja que procesaremos
    *
    *@return array que contiene
    * array $datos_h1 Beneficiarias únicos que SÍ completaron la encuesta
    * array $datos_h1_false Beneficiarios únicos que NO completaron la encuesta
    **/
    private function procesaH1($hoja1){

    //Inicializamos variables
    $datos_h1 = NULL;
    $datos_h1_false = NULL;

	    //Recorremos la hoja
	    foreach ($hoja1 as $key => $value):	        

	    	//Obtenemos los entrevistados
	       if(trim($value['Y']) == 'True'){

	            //Guardamos arreglo
	            $datos_h1[] = $value;

	        }else{
	        	$datos_h1_false[] = $value['B'];
	        }

	    endforeach;

    return array($datos_h1,$datos_h1_false);

    }


    /**
    *Procesamos los registros de beneficiarios y sus familiares de la hoja 2
    *@param array $hoja2 Hoja que procesaremos
    *
    *@return array que contiene
    * array $beneficarias Beneficiarias únicos de la hoja
    * array $familiares Familiares de los beneficiarios
    **/
    private function procesaH2($hoja2){

    //Inicializamos variables
    $beneficiarias = NULL;
    $familiares = NULL;

    //Recorremos la hoja
    foreach ($hoja2 as $key => $value):

    	//Obtenemos los entrevistados
       if(trim($value['X']) == 'SI'){

            //Guardamos arreglo
            $beneficiarias[] = $value;

        }else{
            //Es un familiar
            $familiares[] = $value;
        }

    endforeach;

    return array($beneficiarias,$familiares);

    }

    /**
     * Buscamos beneficiaria por nombre
     * @param  [type] $paterno [description]
     * @param  [type] $materno [description]
     * @param  [type] $nombre  [description]
     * @return [type]          [description]
     */
    private static function buscaNombre($paterno,$materno,$nombre,$fecha_nacimiento = NULL) {
   	
 	 //Obtenemos soundex del nombre y apellido paterno
   	 $soundex = mujeresAvanzando::getInstance()->soundex($nombre.' '.$paterno);

 	 $mujeres_avanzando = mujeresAvanzando::get_by_soundex_fecha($soundex,$fecha_nacimiento);

 	 return $mujeres_avanzando;

	}

    /**
     * Armamos arreglo para guardar registro en mujeres_avanzando
     * @param  [type] $datos_h1    Arreglo con la información de la hoja 1
     * @param  [type] $datos_h2    Arreglo con la información de la hoja 2
     * @param  [type] $id_caravana ID de la caravana
     * @param  [type] $visita      Número de visita en caravana
     * @return [type]              [description]
     */
    public static function guarda($datos_h1,$datos_h2,$id_caravana,$visita){

	//Obtenemos id de la entrevista
	$id_entrevista_h1 = $datos_h1['B'];
	$id_entrevista_h2 = $datos_h2['C'];    
    
	//Obtenemos id de la entrevista
	$id_entrevista_h1 = $datos_h1['B'];
	$id_entrevista_h2 = $datos_h2['C'];
    
    //Índice Global
    $gr = $datos_h1['GE'];
    $grados = 0;

    //Nivel Socioeconómico
    $n = $datos_h1['FZ'];
    $niveles = 0;

    //Calidad de dieta
    $cd = $datos_h1['GC'];
    $dietas = 0;

    //Diversidad de dieta
    $d = $datos_h1['GA'];    
    $diversidadades = 0;

    //Variedad Dieta
    $v = $datos_h1['GB'];
    $variedades = 0;

    //ELCSA
    $elc = $datos_h1['GD'];
    $elcs = 0;

	//Programas válidos
	$programas = array('MAP','MAC','MAS','SOL');

	//Los ID de ambas hojas deben coincidir
	if($id_entrevista_h1 == $id_entrevista_h2){

		//Obtenemos fecha de nacimiento
        $fecha = substr($datos_h2['H'],0,10);
        $fecha_nacimiento = Fechas::fechadmyAymd($fecha);
        
		//Buscaremos registro en tabla de mujeres_avanzando
		//mediante la búsqueda por nombre y folio
		$mujeres_avanzando = self::buscaNombre($datos_h2['E'], 
												$datos_h2['F'], 
												$datos_h2['G'],
												$fecha_nacimiento);		

		if(count($mujeres_avanzando) > 0){
		
	    switch (strtoupper(trim($elc))) {
	    	case 'LEVE':
	    		$elcs = 1;
	    		break;
	    	case 'MODERADA':
	    		$elcs = 2;
	    		break;
	    	case 'SEGURA':
	    		$elcs = 3;
	    		break;
	        case 'SEVERA':
	    		$elcs = 4;
	    		break;
	        
	    
	    	default:
	    		# code...
	    		break;
	    }
	    
	    switch (strtoupper(trim($v))) {
	    	case 'NO VARIADA': case 'MONOTONA': case 'MONÓTONA':
	    		$variedades = 1;
	    		break;
	    	case 'POCO VARIADA':
	    		$variedades = 2;
	    		break;
	    	case 'VARIADA':
	    		$variedades = 3;
	    		break;
	    
	    	default:
	    		# code...
	    		break;
	    }
	    
	    
	     switch (strtoupper(trim($d))) {
	    	case 'COMPLETA':
	    		$diversidadades = 1;
	    		break;
	    	case 'MODERADA':
	    		$diversidadades = 2;
	    		break;
	    	default:
	    		# code...
	    		break;
	    }
	    
	    switch (strtoupper(trim($cd))) {
	    	case 'NO SALUDABLE':
	    		$dietas = 1;
	    		break;
	    	case 'POCO SALUDABLE':
	    		$dietas = 2;
	    		break;
	    	case 'SALUDABLE':
	    		$dietas = 3;
	    		break;
	    
	    	default:
	    		# code...
	    		break;
	    }
	    

	    switch (strtoupper(trim($n))) {
	    	case 'ALTO':
	    		$niveles = 1;
	    		break;
	    	case 'BAJO':
	    		$niveles = 2;
	    		break;
	    	case 'MEDIO':
	    		$niveles = 3;
	    		break;
	    
	    	default:
	    		# code...
	    		break;
	    }
		
	    
	    switch (strtoupper(trim($gr))) {
	    	case 'SEVERA':
	    		$grados = 1;
	    		break;
	    	case 'MODERADA':
	    		$grados = 2;
	    		break;
	    	case 'LEVE':
	    		$grados = 3;
	    		break;
	    	case 'SEGURA':
	    		$grados = 4;
	    		break;
	    	default:
	    		# code...
	    		break;
	    }

		//Obtenemos programa
        $prog = substr($datos_h1['AE'], 0,3);

        //print_r($programas);
        //echo $prog;
        
        //Sólo los programas válidos
        if(in_array($prog,$programas)){
	                         					        	
			//Obtenemos información del excel
            //$excel_ma['folio'] = $datos_h1['D'];
            $excel_ma['id_grado'] = $grados;		  
            $excel_ma['nivel'] = $niveles;
            $excel_ma['calidad_dieta'] = $dietas;
            $excel_ma['diversidad'] = $diversidadades;
            $excel_ma['variedad'] = $variedades;
            $excel_ma['elcsa'] = $elcs;            

            //print_r($excel_ma);

            //Actualizamos datos del registro en la tabla de mujeres_avanzando
            $msg_no = mujeresAvanzando::actualizaGIA($excel_ma,$mujeres_avanzando['id']);
            
	            if($msg_no == 1){

	            	//Nuevo registro en historico, procedemos a registrar 
	            	//lo que previamente estaba en la tabla mujeres_avanzando
					$historico_gia = array(
						'id_mujeres_avanzando' => $mujeres_avanzando['id'],
						'folio' => $datos_h1['C'],
						'visita' => $visita,
						'id_caravana' => $id_caravana,
						'id_grado' => $mujeres_avanzando['id_grado'],
						'nivel' => $mujeres_avanzando['nivel'],
		                'calidad_dieta' => $mujeres_avanzando['calidad_dieta'],
		                'diversidad' => $mujeres_avanzando['diversidad'],
		                'variedad' => $mujeres_avanzando['variedad'],
		                'elcsa'=> $mujeres_avanzando['elcsa']   
					    );    

					//print_r($historico_gia);

					$msg_no = HistoricoGIA::saveHistoricoGIA($historico_gia);
	            }			

	        }

		}else{
			//No existe en la tabla de mujeres_avanzando
			$msg_no = 30;
		}

	}else{
		//Las entrevistas no coinciden
		$msg_no = 22;
	}

	return $msg_no;    	
   }
   
}