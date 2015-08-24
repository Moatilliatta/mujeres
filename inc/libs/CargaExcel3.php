<?php
//Librería para leer/crear exceles
include_once('PHPExcel.php');
//Obtenemos el modelo de mujeres avanzando
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
include_once($_SESSION['model_path'].'familiares_mujer.php');
include_once($_SESSION['model_path'].'registro_excel_enhina.php');
include_once($_SESSION['inc_path'].'libs/Permiso.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');

class CargaExcel3 extends Db{
    
    // Variables
    var $Hojas;    

    public function __construct(){}
    public function __destruct(){}

    public static function hoja1($hoja_1){

    //Columnas: B,C,Q,T,Y,AE,AG,AH,AI,AJ,AK,FZ,GA,GB,GC,GD,GE

    //Variable donde guardaremos la Hoja 1
    $H = NULL;
	$datos_h1 = NULL;
	$datos_h1_false = NULL;
	$msg_no = 0;
	$j = 0;

    //Variable donde guardaremos la Hoja 1
    $maxFila = $hoja_1->getHighestRow();
	
	//Obtenemos columnas que necesitaremos	
	$A = $hoja_1->rangeToArray('A2:A'.$maxFila,null,null,false,true);//Entrevista id
	$B = $hoja_1->rangeToArray('B2:B'.$maxFila,null,null,false,true);// Folio
	$P = $hoja_1->rangeToArray('P2:P'.$maxFila,null,null,false,true);// CP
	$S = $hoja_1->rangeToArray('S2:S'.$maxFila,null,null,false,true);// cve_mun
	$X = $hoja_1->rangeToArray('X2:X'.$maxFila,null,null,false,true);// completa 'true'
	$AD = $hoja_1->rangeToArray('AD2:AD'.$maxFila,null,null,false,true); //encuestador 'mac'

    //CALLE, NÚMERO EXTERIOR, NÚMERO INTERIOR, COLONIA, REFERENCIA
	$AF_AJ = $hoja_1->rangeToArray('AF2:AJ'.$maxFila,null,null,false,true);

    //IndiceGlobal, Nivel Socioeconomico, Calidad Dieta, Diversidad, Variedad, ELCSA
	$FY_GD = $hoja_1->rangeToArray('FY2:GD'.$maxFila,null,null,false,true);	

	//Recorremos cada registro y solo guardamos los que tengan datos
	for ($i=0; $i <= $maxFila ; $i++) { 

		if(isset($A[$i]['A']) && $A[$i]['A'] != NULL && 
            isset($B[$i]['B']) && $B[$i]['B'] != NULL){

			$H[] =$A[$i]+$B[$i]+$P[$i]+$S[$i]+$X[$i]+$AD[$i]+$AF_AJ[$i]+$FY_GD[$i];
			
			//Validamos total de datos y columnas
			if(count($H[$j]) == 17){

				//Obtenemos los entrevistados
                $entrevistado = trim(strtoupper($H[$j]['X']));

                //echo $entrevistado.' - '.$H[$j]['Y'];
                //exit;

		        if($entrevistado == 'TRUE' || $entrevistado == 'VERDADERO' || $entrevistado == 1){
		            //Guardamos arreglo
		            $datos_h1[] = $H[$j];

		        }else{
		        	$datos_h1_false[] = $H[$j]['A'];
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
    */

	return array($H,$datos_h1,$datos_h1_false,$msg_no);

    }

    public static function hoja2($hoja_2){
     
     //Columnas "C","E","J","N","X","AD"

     //Variable donde guardaremos la Hoja 2
     $H2 = NULL;
     $beneficiarias = NULL;
     $familiares = NULL;
	 $j = 0;
	 $msg_no = 0;

     //Obtenemos la fila más grande
     $maxFila = $hoja_2->getHighestRow();
     
     //Obtenemos columnas que necesitaremos
     $B = $hoja_2->rangeToArray('B2:B'.$maxFila,null,null,false,true);//Entrevista id
     $M = $hoja_2->rangeToArray('M2:M'.$maxFila,null,null,false,true);//Ocupación
     $W = $hoja_2->rangeToArray('W2:W'.$maxFila,null,null,false,true);//Persona Entrevistada

     //Paterno, Materno, Nombre, FechaNacimiento
     $D_G = $hoja_2->rangeToArray('D2:G'.$maxFila,null,null,false,true);

     //Genero, Escolaridad
     $I_J = $hoja_2->rangeToArray('I2:J'.$maxFila,null,null,false,true);     

     //Madre Soltera
     $AC = $hoja_2->rangeToArray('AC2:AC'.$maxFila,null,null,false,true);

     //Recorremos cada registro y solo guardamos los que tengan datos
     for ($i=0; $i <= $maxFila ; $i++) { 
		if(isset($B[$i]['B']) && $B[$i]['B'] != NULL){

			$H2[] =$B[$i]+$D_G[$i]+$I_J[$i]+$M[$i]+$W[$i]+$AC[$i];	

			//Validamos total de datos y columnas
			if(count($H2[$j]) == 10){

				//Obtenemos los entrevistados
	       		if(trim($H2[$j]['W']) == 'SI'){

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
		echo'Beneficiarias: ';
		print_r($beneficiarias);
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
    $total_prog_sol = 0;
    $total_prog_pio = 0;
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
                         'entrevistas','integrantes',
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

        if(count($datos_h1) > 0){
            //Recorremos total de filas en el excel
            foreach ($datos_h1 as $key => $value):              

            //Obtenemos ID del registro donde coincide
            $k = self::searchForId($value['A'], $beneficiarias);                
            
            //Guardaremos registro
            $msg_no = self::guarda($value,$beneficiarias[$k],$id_caravana,$visita);
            echo "Mensaje : ".$msg_no;

            //Dependiendo la respuesta hacemos incrementos
            switch ($msg_no) {
                case 1:$total_registrados++;
                        break;
                case 21://Registro de Beneficiario duplicado
                        $total_duplicados++;
                        //Guardamos el ID de entrevista para no duplicar los familiares
                        $id_entrevista_dup[]=$beneficiarias[$k]['B'];
                        break;
                case 22://No coinciden ID de entrevista
                        $total_no_coinciden++;
                        $id_entrevista_noc[$value['B']]=$value['A'];
                        $id_entrevista_dup[]=$beneficiarias[$k]['B'];
                        break;
                default:$id_entrevista_dup[]=$beneficiarias[$k]['B'];
                        break;
            }

            //Obtenemos programa
            $prog = substr($value['AD'], 0,3);
            
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
                case 'SOL': $total_prog_sol++;
                            break;
                case 'PIO': $total_prog_pio++;
                            break;
                default: break;
            }

            //Obtenemos grado de inseguridad
            $grado_ins = $value['GD'];

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
        }		

		$id_entrevista_dup = ($id_entrevista_dup == NULL)? array(0) : $id_entrevista_dup ;
		$id_entrevista_noc = ($id_entrevista_noc == NULL)? array() : $id_entrevista_noc ;
		$datos_h1_false = ($datos_h1_false == NULL)? array(0) : $datos_h1_false ;

		//Recorremos los registro de los familiares
		if(count($familiares) > 0 ){
			foreach ($familiares as $key => $valor):
				
				if(!in_array($valor['B'], $id_entrevista_dup) && 
					!in_array($valor['B'], $datos_h1_false)){
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
                        'total_prog_sol' => $total_prog_sol,
                        'total_prog_pio' => $total_prog_pio,
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
	       if ($val['B'] === $id) {
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

            $entrevistado = trim(strtoupper($value['W']));
	       
           //Obtenemos los entrevistados
	       if($entrevistado == 'TRUE' || $entrevistado == 'VERDADERO' || $entrevistado == 1){

	            //Guardamos arreglo
	            $datos_h1[] = $value;

	        }else{
	        	$datos_h1_false[] = $value['A'];
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
       if(trim($value['W']) == 'SI'){

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
     * Armamos arreglo para guardar registro en mujeres_avanzando
     * @param  [type] $datos_h1    Arreglo con la información de la hoja 1
     * @param  [type] $datos_h2    Arreglo con la información de la hoja 2
     * @param  [type] $id_caravana ID de la caravana
     * @param  [type] $visita      Número de visita en caravana
     * @return [type]              [description]
     */
    public static function guarda($datos_h1,$datos_h2,$id_caravana,$visita){

	//Obtenemos id de la entrevista
	$id_entrevista_h1 = $datos_h1['A'];
	$id_entrevista_h2 = $datos_h2['B'];
    
    //Índice Global
    $gr = $datos_h1['GD'];
    $grados = 0;

    //Nivel Socioeconómico
    $n = $datos_h1['FY'];
    $niveles = 0;

    //Calidad de dieta
    $cd = $datos_h1['GB'];
    $dietas = 0;

    //Diversidad de dieta
    $d = $datos_h1['FZ'];    
    $diversidadades = 0;

    //Variedad Dieta
    $v = $datos_h1['GA'];
    $variedades = 0;

    //ELCSA
    $elc = $datos_h1['GC'];
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
	//$tel_prueba = '379-605-14 3339696809 ,referencia santa lucia';
   
    //echo 'resultado'.$r;
    //exit;
    
	//Programas válidos
	$programas = array('MAP','MAC','MAS','SOL','PIO');

	//Los ID de ambas hojas deben coincidir
	if($id_entrevista_h1 == $id_entrevista_h2){

		//Vemos si hay un registro con el mismo id de entrevista
		$obj = mujeresAvanzando::get_by_id_entr($id_entrevista_h1);

		//Si no encontramos una entrevista previa, procedemos a guardar
		if($obj == NULL){

		//Obtenemos programa
        $prog = substr($datos_h1['AD'], 0,3);//Encuestador
        $fecha = substr($datos_h2['G'],0,10);
        
        //echo $prog;
        //exit;  
        
        //Sólo los programas válidos
        //if(in_array($prog,$programas)){
	                         					        	
			//Obtenemos información			            
            $paterno = $datos_h2['D'];
			$materno = $datos_h2['E'];
            $nombres = $datos_h2['F'];			

            if(intval($fecha) > 0){
        	$fecha_nacimiento = substr(Fechas::convertir_fecha_excel($datos_h2['G']),0,10);
        	}else{
        		$fecha_nacimiento = Fechas::fechadmyAymd($fecha);
        	}


			$genero = $datos_h2['I'];
			$escolaridad = $datos_h2['J'];
            $ocupacion = $datos_h2['M']; 
            $es_madre_soltera = $datos_h2['AC'];

            $folio = $datos_h1['B'];
            $CODIGO = $datos_h1['P'];
            //$id_ocupacion = $datos_h1['N'];
            //$id_escolaridad = $datos_h1['K'];
			$id_cat_municipio = str_pad($datos_h1['S'],3,"0",STR_PAD_LEFT);
			//$id_cat_localidad = str_pad($datos_h1['W'],4,"0",STR_PAD_LEFT);			
			$id_grado = $grados;					
			$desc_ubicacion = 'CALLE: '.$datos_h1['AF'].' COLONIA: '.$datos_h1['AI'];
            $calle = $datos_h1['AF'];
            $num_ext = $datos_h1['AG'];
			$num_int = $datos_h1['AH'];
            $colonia =$datos_h1['AI'];
	        $referencia = Permiso::procesa_tel($datos_h1['AJ']);

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

	       /* }else{
                //No es grupo de carga
                $msg_no = 31;
            }*/

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