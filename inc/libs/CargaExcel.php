<?php
//Librería para leer/crear exceles
include_once('PHPExcel.php');
//Obtenemos el modelo de mujeres avanzando
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'familiares_mujer.php');
include_once($_SESSION['model_path'].'registro_excel_enhina.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');

class CargaExcel extends Db{
    
    // Variables
    var $Hojas;    

    public function __construct(){}
    public function __destruct(){}

    public static function hoja1($hoja_1){

    //Columnas: B,D,R,U,Z,AH,AJ,AK,AL,AM,AN,FU,FV,FW,FX,FY,FZ

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
	$D = $hoja_1->rangeToArray('D2:D'.$maxFila,null,null,false,true);// Folio
	$R = $hoja_1->rangeToArray('R2:R'.$maxFila,null,null,false,true);// CP
	$U = $hoja_1->rangeToArray('U2:U'.$maxFila,null,null,false,true);// cve_mun
	$Z = $hoja_1->rangeToArray('Z2:Z'.$maxFila,null,null,false,true);// completa 'true'
	$AH = $hoja_1->rangeToArray('AH2:AH'.$maxFila,null,null,false,true); //encuestador 'mac'

    //CALLE, NÚMERO EXTERIOR, NÚMERO INTERIOR, COLONIA, REFERENCIA
	$AJ_AN = $hoja_1->rangeToArray('AJ2:AN'.$maxFila,null,null,false,true);

    //IndiceGlobal, Nivel Socioeconomico, Calidad Dieta, Diversidad, Variedad, ELCSA
	$FU_FZ = $hoja_1->rangeToArray('FU2:FZ'.$maxFila,null,null,false,true);	

	//Recorremos cada registro y solo guardamos los que tengan datos
	for ($i=0; $i <= $maxFila ; $i++) { 

		if( isset($B[$i]['B'])&& $B[$i]['B'] != NULL && 
            isset($D[$i]['D']) && $D[$i]['D'] != NULL){

			$H[] =$B[$i]+$D[$i]+$R[$i]+$U[$i]+$Z[$i]+$AH[$i]+$AJ_AN[$i]+$FU_FZ[$i];
			
			//Validamos total de datos y columnas
			if(count($H[$j]) == 17){

				//Obtenemos los entrevistados
		        if(trim($H[$j]['Z']) == 'True'){
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
     
     //Columnas "C","E","F","G","H","J","K","N","X"

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
     $N = $hoja_2->rangeToArray('N2:N'.$maxFila,null,null,false,true);//Ocupación
     $X = $hoja_2->rangeToArray('X2:X'.$maxFila,null,null,false,true);//Persona Entrevistada

     //Paterno, Materno, Nombre, FechaNacimiento
     $E_H = $hoja_2->rangeToArray('E2:H'.$maxFila,null,null,false,true);

     //Genero, Escolaridad
     $J_K = $hoja_2->rangeToArray('J2:K'.$maxFila,null,null,false,true);     

     //Madre Soltera
     $AD = $hoja_2->rangeToArray('AD2:AD'.$maxFila,null,null,false,true);

     //Recorremos cada registro y solo guardamos los que tengan datos
     for ($i=0; $i <= $maxFila ; $i++) { 
		
        if(isset($C[$i]['C']) && $C[$i]['C'] != NULL){

			$H2[] =$C[$i]+$E_H[$i]+$J_K[$i]+$N[$i]+$X[$i]+$AD[$i];	

			//Validamos total de datos y columnas
			if(count($H2[$j]) == 10){

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
    public static function carga($nombre,$ruta = NULL,$id_caravana=0,$visita = 1,$ext = 'xls'){
      

    //Inicializamos variables
    $msg_no = 0;
    $total_encuestados = 0;
    $total_enc_completo = 0;
    $total_familiares = 0;
    $total_prog_mac = 0;
    $total_prog_map = 0;
    $total_prog_mas = 0;
    $total_registrados = 0;
    $total_duplicados = 0;
    $total_no_coinciden = 0;
    $total_enc_inc = 0;
    $id_entrevista_dup = NULL;
    $id_entrevista_noc = NULL;
	$total_enc_inc = NULL;    
	$total_severa = 0;
	$total_moderada = 0;
	$total_leve = 0;
	$total_segura = 0;
	$total_otra = 0;

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

        $benef = (isset($beneficiarias[$k]))? $beneficiarias[$k] : null;

        //Guardaremos registro
		$msg_no = self::guarda($value,$benef,$id_caravana,$visita);
				
		//Dependiendo la respuesta hacemos incrementos
		switch ($msg_no) {
			case 1:$total_registrados++;
					break;
			case 21://Registro de Beneficiario duplicado
		            $total_duplicados++;
		            //Guardamos el ID de entrevista para no duplicar los familiares
		            $id_entrevista_dup[]=$benef['C'];
		            break;
		    case 22://No coinciden ID de entrevista
		    		$total_no_coinciden++;
		    		$id_entrevista_noc[$value['D']]=$value['B'];
		    		$id_entrevista_dup[]=$benef['C'];
		    		break;
		    default:$id_entrevista_dup[]=$benef['C'];
		    		break;
		}

		//Obtenemos programa
		$prog = substr($value['AH'], 0,3);
        
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

		$id_entrevista_dup = ($id_entrevista_dup == NULL)? array(0) : $id_entrevista_dup ;
		$id_entrevista_noc = ($id_entrevista_noc == NULL)? array() : $id_entrevista_noc ;
		$datos_h1_false = ($datos_h1_false == NULL)? array(0) : $datos_h1_false ;

		//Recorremos los registro de los familiares
		if(count($familiares) > 0 ){
			foreach ($familiares as $key => $valor):
				
				if(!in_array($valor['C'], $id_entrevista_dup) && 
					!in_array($valor['C'], $datos_h1_false)){
					$msg_no = FamiliaresMujer::guardaFamiliares($valor);
					$total_familiares++;
				}				

			endforeach;
		}		

		$totales = array('total_encuestados' => $total_encuestados,
						'total_enc_completo' => $total_enc_completo,
						'total_enc_inc' => $total_enc_inc,
						'total_familiares' => $total_familiares,
						'total_prog_mac' => $total_prog_mac,
						'total_prog_map' => $total_prog_map,
                        'total_prog_mas' => $total_prog_mas,
						'total_registrados' => $total_registrados,
						'total_duplicados' => $total_duplicados,
						'total_no_coinciden' => $total_no_coinciden,
						'id_entrevista_noc' => $id_entrevista_noc,
						'total_severa' => $total_severa,
						'total_moderada' => $total_moderada,
						'total_leve' => $total_leve,
						'total_segura' => $total_segura,
						'total_otra' => $total_otra,
						'nombre' => $nombre);

		Registro_excel::saveRegistroexcel($totales); 

	}                            		
			
	return array($msg_no,$totales);

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
	       if(trim($value['Z']) == 'True'){

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

     private static function convertir_fecha($numero = 0){
    	
    	$UNIX_DATE = ($numero - 25569) * 86400;
		return gmdate("Y-m-d", $UNIX_DATE);
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
    $gr = $datos_h1['FU'];
    $grados = 0;
    $n = $datos_h1['FV'];
    $niveles = 0;
    $cd = $datos_h1['FW'];
    $dietas = 0;
    $d = $datos_h1['FX'];
    $diversidadades = 0;
    $v = $datos_h1['FY'];
    $variedades = 0;
    $elc = $datos_h1['FZ'];
    $elcs = 0;
    
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
    	case 'NO VARIADA':
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
	//$tel_prueba = '379-605-14 3339696809 ,referencia santa lucia';
   
    //echo 'resultado'.$r;
    //exit;
    
	//Programas válidos
	$programas = array('MAP','MAC','MAS');

	//Los ID de ambas hojas deben coincidir
	if($id_entrevista_h1 == $id_entrevista_h2){

		//Vemos si hay un registro con el mismo id de entrevista
		$obj = mujeresAvanzando::get_by_id_entr($id_entrevista_h1);

		//Si no encontramos una entrevista previa, procedemos a guardar
		if($obj == NULL){

		//Obtenemos programa
        $prog = substr($datos_h1['AH'], 0,3);
        $fecha = substr($datos_h2['H'],0,10);
        
        //echo $prog;
        //exit;  
        //Sólo los programas válidos
        if(in_array($prog,$programas)){
	                         					        	
			//Obtenemos información			            
            $paterno = $datos_h2['E'];
			$materno = $datos_h2['F'];
            $nombres = $datos_h2['G'];			

            if(intval($fecha) > 0){
        	$fecha_nacimiento = substr(self::convertir_fecha($datos_h2['H']),0,10);
        	}else{
        		$fecha_nacimiento = Fechas::fechadmyAymd($fecha);
        	}


			$genero = $datos_h2['J'];
			$escolaridad = $datos_h2['K'];			
            $ocupacion = $datos_h2['N'];   
            $es_madre_soltera = $datos_h2['AD'];         

            $folio = $datos_h1['D'];
            $CODIGO = $datos_h1['R'];
            //$id_ocupacion = $datos_h1['N'];
            //$id_escolaridad = $datos_h1['K'];
			$id_cat_municipio = str_pad($datos_h1['U'],3,"0",STR_PAD_LEFT);
			//$id_cat_localidad = str_pad($datos_h1['W'],4,"0",STR_PAD_LEFT);			
			$id_grado = $grados;					
			$desc_ubicacion = 'CALLE: '.$datos_h1['AJ'].' COLONIA: '.$datos_h1['AM'];
            $calle = $datos_h1['AJ'];
            $num_ext = $datos_h1['AK'];
			$num_int = $datos_h1['AL'];
            $colonia =$datos_h1['AM'];
	        $referencia = Permiso::procesa_tel($datos_h1['AN']);

            //$referencia=$datos_h1['AN'];
	        $programa = strtoupper($prog);
            $nivel = $niveles;
            $calidad_dieta = $dietas;
            $diversidad = $diversidadades;
            $variedad = $variedades;
            $elcsa = $elcs;                        

			/*Campos obligatorios de nuestro modelo, de momento pondremos los
			siguientes valores predeterminados*/
			//$CVE_VIA = 475946;
			$CVE_EDO_RES = 14;
			$id_estado_civil = null;
            
            //echo $programa;
            //exit;

			//Nuevo registro, procedemos a registrarlo en mujeres avanzando
			$mujeres_avanzando = array(
			    'nombres' => $nombres,
			    'paterno' => $paterno,
			    'materno' => $materno,
			    'fecha_nacimiento' => $fecha_nacimiento,
			    'genero' => $genero,
			    'id_cat_estado' => 14,
			    'id_cat_municipio' => $id_cat_municipio,
			    //'id_cat_localidad' => $id_cat_localidad,
			    'num_ext' => $num_ext,
			    'num_int' => $num_int,
			    'desc_ubicacion' => $desc_ubicacion,
                'calle' => $calle,
                'colonia' => $colonia,
                'id_grado' => $id_grado,
			    'id_pais' => 90,
			    'CODIGO' => $CODIGO,
			    //'CVE_VIA' => $CVE_VIA,
			    'CVE_EDO_RES' => $CVE_EDO_RES,
				//'id_escolaridad' => $id_escolaridad,
				//'id_ocupacion' => $id_ocupacion,
				'es_madre_soltera' => $es_madre_soltera,
                'escolaridad' => $escolaridad,
				'ocupacion' => $ocupacion,
				'id_estado_civil' => $id_estado_civil,
				'id_entrevista' => $id_entrevista_h1,
				'folio' => $folio,
	            'telefono' => $referencia,
	            'programa' => $programa,
                'id_caravana' => $id_caravana,
                'visita' => $visita,
                'nivel' => $nivel,
                'calidad_dieta' => $calidad_dieta,
                'diversidad' => $diversidad,
                'variedad' => $variedad,
                'elcsa'=> $elcsa,
                'masiva' => 1            
			    );    
				
                //print_R($mujeres_avanzando);
                //exit;		
                        
                        	
				list($msg_no,$curp,$id_generado) = mujeresAvanzando::
													saveMujer($mujeres_avanzando);
                                                    
                                                    

	        }

		}else{
			//Registro de Beneficiaria duplicado
			$msg_no = 21;
		}

	}else{
		//No coinciden los ID de entrevista
		$msg_no = 22;
	}

	return $msg_no;    	
   }
   
}