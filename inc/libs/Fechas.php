<?php
class Fechas{

	public static function meses(){		
		return array(
			1 => "Enero",
			2 => "Febrero",
			3 => "Marzo",
			4 => "Abril",
			5 => "Mayo",
			6 => "Junio",
			7 => "Julio",
			8 => "Agosto",
			9 => "Septiembre",
			10 => "Octubre",
			11 => "Noviembre",
			12 => "Diciembre");
	}

	public static function fecha(){

		$d = date("d",time());	
		$m = date("m",time());
		$y = date("Y",time());

		$n = date("N",time());	

		switch($n){
			case 1: $fecha = "Lunes"; break;
			case 2: $fecha = "Martes"; break;
			case 3: $fecha = "Miercoles"; break;
			case 4: $fecha = "Jueves"; break;
			case 5: $fecha = "Viernes"; break;
			case 6: $fecha = "Sábado"; break;
			case 7: $fecha = "Domingo"; break;
		}

		$fecha .= ", ".$d." de ";		

		switch($m){

			case 1: $fecha .= "Enero"; break;
			case 2: $fecha .= "Febrero"; break;
			case 3: $fecha .= "Marzo"; break;
			case 4: $fecha .= "Abril"; break;
			case 5: $fecha .= "Mayo"; break;
			case 6: $fecha .= "Junio"; break;
			case 7: $fecha .= "Julio"; break;
			case 8: $fecha .= "Agosto"; break;
			case 9: $fecha .= "Septiembre"; break;
			case 10: $fecha .= "Octubre"; break;
			case 11: $fecha .= "Noviembre"; break;
			case 12: $fecha .= "Diciembre"; break;
		}

		$fecha .= " de ".$y;

		return $fecha;

	}

	public static function fechacorta($separador='/',$f){

	    if (isset($f)){

            if(is_null($f) or ($f == ""))
                return "";

            $components = explode("/", $f, 3);
            $count = count($components);
            if($count == 3) // Tiene 3 Diagonales
            {
		      $d = $components[0];	
		      $m = intval($components[1]);
		      $y = $components[2];		
            }else{
              return "";
          	}          	

	    }else {

		$d = date("d",time());	
		$m = date("m",time());
		$y = date("Y",time());

		$n = date("N",time());

	    }

		$fecha = $d.$separador;

		switch($m){
			case 1: $fecha .= "Ene"; break;
			case 2: $fecha .= "Feb"; break;
			case 3: $fecha .= "Mar"; break;
			case 4: $fecha .= "Abr"; break;
			case 5: $fecha .= "May"; break;
			case 6: $fecha .= "Jun"; break;
			case 7: $fecha .= "Jul"; break;
			case 8: $fecha .= "Ago"; break;
			case 9: $fecha .= "Sep"; break;
			case 10: $fecha .= "Oct"; break;

			case 11: $fecha .= "Nov"; break;

			case 12: $fecha .= "Dic"; break;

		}

		

		$fecha .= $separador.$y;

		

		return $fecha;

	}

    /**

     * Para fechas de Tablas del sistema

     * formato 2011-01-01 00:00:00

     **/

	public static function fechalarga($f){

	   //return substr($f,0,10);

       

	    if (isset($f)){



            if(is_null($f) or ($f == ""))

                return "";

            $components = explode("-", substr($f,0,10), 3);

            $count = count($components);

            if($count == 3) // Tiene 3 datos

            {

		      $d = $components[2];	

		      $m = intval($components[1]);

		      $y = $components[0];

              $fec = mktime(0,0,0, $m, $d,$y);

		      $n = date("N",$fec);

            }else{

              return "";}

	       

	       

	    }else {

		$d = date("d",time());	

		$m = date("m",time());

		$y = date("Y",time());

		

		$n = date("N",time());

	    }

		        

        $fecha = $d." de ";

                		

		switch($m){

			case 1: $fecha .= "Enero"; break;

			case 2: $fecha .= "Febrero"; break;

			case 3: $fecha .= "Marzo"; break;

			case 4: $fecha .= "Abril"; break;

			case 5: $fecha .= "Mayo"; break;

			case 6: $fecha .= "Junio"; break;

			case 7: $fecha .= "Julio"; break;

			case 8: $fecha .= "Agosto"; break;

			case 9: $fecha .= "Septiembre"; break;

			case 10: $fecha .= "Octubre"; break;

			case 11: $fecha .= "Noviembre"; break;

			case 12: $fecha .= "Diciembre"; break;

		}

		

		//$fecha .= $separador.$y;

		$fecha .= " de ".$y;

		return $fecha;

	}	

    /**

     * Para fechas larga nobre de dia y fecha

     * formato 2011-01-01 00:00:00

     **/

	public static function fechalarga_dia($f){

	   //return substr($f,0,10);

       

	    if (isset($f)){



            if(is_null($f) or ($f == ""))

                return "";

            $components = explode("-", substr($f,0,10), 3);

            $count = count($components);

            if($count == 3) // Tiene 3 datos

            {

		      $d = $components[2];	

		      $m = intval($components[1]);

		      $y = $components[0];

              $fec = mktime(0,0,0, $m, $d,$y);

		      $n = date("N",$fec);

            }else{

              return "";}

	       

	       

	    }else {

		$d = date("d",time());	

		$m = date("m",time());

		$y = date("Y",time());

		

		$n = date("N",time());

	    }

		

        

		switch($n){

			case 1: $fecha = "Lunes"; break;

			case 2: $fecha = "Martes"; break;

			case 3: $fecha = "Miercoles"; break;

			case 4: $fecha = "Jueves"; break;

			case 5: $fecha = "Viernes"; break;

			case 6: $fecha = "Sábado"; break;

			case 7: $fecha = "Domingo"; break;

		}

	   

		$fecha .= ", ".$d." de ";

        

        

        //$fecha = $d." de ";

                		

		switch($m){

			case 1: $fecha .= "Enero"; break;

			case 2: $fecha .= "Febrero"; break;

			case 3: $fecha .= "Marzo"; break;

			case 4: $fecha .= "Abril"; break;

			case 5: $fecha .= "Mayo"; break;

			case 6: $fecha .= "Junio"; break;

			case 7: $fecha .= "Julio"; break;

			case 8: $fecha .= "Agosto"; break;

			case 9: $fecha .= "Septiembre"; break;

			case 10: $fecha .= "Octubre"; break;

			case 11: $fecha .= "Noviembre"; break;

			case 12: $fecha .= "Diciembre"; break;

		}

		

		//$fecha .= $separador.$y;

		$fecha .= " de ".$y;

		return $fecha;

	}

    /**

     * Cambia una fecha dd/mm/yyyy a yyyy-mm-dd

     * 

     * */

     

	public static function fechadmyAymd($f){

	    if (isset($f)){



            if(is_null($f) or ($f == ""))

                return "";

            $components = explode("/", $f, 3);

            $count = count($components);

            if($count == 3) // Tiene 3 datos

            {

		      $d = $components[0];	

		      $m = $components[1];

		      $y = $components[2];

              return $y."-".$m."-".$d;

            }else{

              return "";}

	       

	       

	    }else {

             return "";

	    }

        

    }



    /**

     * Cambia una fecha yyyy-mm-dd a dd/mm/yyyy

     * 

     * */

     

	public static function fechaymdAdmy($f){

	    if (isset($f)){



            if(is_null($f) or ($f == ""))

                return "";

            $components = explode("-", $f, 3);

            $count = count($components);

            if($count == 3) // Tiene 3 datos

            {

		      $d = $components[2];	

		      $m = $components[1];

		      $y = $components[0];

              return $d."/".$m."/".$y;

            }else{

              return "";}

	       

	       

	    }else {

             return "";

	    }

        

    }

    /**

     * Obtiene el numero de dia de una fecha fecha dd/mm/yyyy 

     * 

     * ej 28/02/2011 = 1 (lunes)

     * 

     * */

     

	public static function numdia($f){

	    if (isset($f)){



            if(is_null($f) or ($f == ""))

                return "";

            $components = explode("/", $f, 3);

            $count = count($components);

            if($count == 3) // Tiene 3 datos

            {

		      $d = $components[0];	

		      $m = $components[1];

		      $y = $components[2];

              $fec = mktime(0,0,0, $m, $d,$y);

		      $n = date("N",$fec);

              return $n;

            }else{

              return 0;}

	       

	       

	    }else {

             return 0;

	    }

        

    }    		

	public static function hora(){

		$hora = date("(H:i)",time());	

		return $hora;

	}

    /**

     * Extrae una fecha yyyy-mm-dd hh:nn:ss

     * Resultado: yyy-mm-dd

     * 

     * */

     

	public static function extraeymd($f){

	    if (isset($f)){       

	         return substr($f,0,10);

	    }else {

             return "";

	    }

        

    }

    public static function invierte_fecha($fechita){
	  
	  if($fechita!=""){
	      if(strpos($fechita,'-')>0){
	        $fecha=explode('-',$fechita);
	      }else{
	        $fecha=explode('/',$fechita);
	      }
	      $dia=$fecha[2];
	      $mes=$fecha[1];
	      $anio=$fecha[0];
	      $fechita="$dia/$mes/$anio";
	    }
	  
	  return $fechita;

	}
    ///mayor e un año y menor a 4 años 2 meses
    public static function rango_edad($edad,$mes){
        $valido=false;
        if($edad>=1 && $edad<=4){
            
            if($edad==4 && $mes<=2){
                
                $valido = true;
                
            }elseif($edad < 4){
                $valido = true;
            }
        }
        return $valido;
        
    }

	public static function anios_meses_dias ($fecha1,$fecha2){

	  $dias=self::dias_entre_fechas($fecha1,$fecha2);
	  $anios=floor($dias/365);
	  $meses=fmod($dias,365);
	  $meses2=floor($meses/30);
	//  $dias=fmod($dias,39);
	  $dias=fmod($dias,30.416);

	  $total=$anios.".".$meses2.".".$dias;

	  return $total;
	}

	public static function dias_entre_fechas($fecha1,$fecha2){
	
	//defino fecha 1 fecha inferior
	//echo 'F1 '.$fecha1.' F2 '.$fecha2;
    //exit; 
	$fecha_res=explode("/",$fecha1);
	$dia1 =$fecha_res[0];
	$mes1 =$fecha_res[1];
	$ano1 =$fecha_res[2];

	//defino fecha 2  fecha superior
	$fecha_res2=explode("/",$fecha2);
	// echo alerta(' d/f '.$fecha_res2);
	$dia2 =$fecha_res2[0];
	$mes2 =$fecha_res2[1];
	$ano2 =$fecha_res2[2];

	date_default_timezone_set('America/Mexico_City');
	//calculo timestam de las dos fechas
	$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
	$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

	// ECHO $timestamp1,$timestamp2;
	 
	//resto a una fecha la otra
	$segundos_diferencia = $timestamp1 - $timestamp2;
	//echo $segundos_diferencia;

	//convierto segundos en días
	$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

	//obtengo el valor absoulto de los días (quito el posible signo negativo)
	$dias_diferencia = abs($dias_diferencia);

	//quito los decimales a los días de diferencia
	$dias_diferencia = floor($dias_diferencia);

	return $dias_diferencia;

	}

	public static function dias_entre_fechas_utils($fecha1,$fecha2){

		//echo 'F1 '.$fecha1.' F2 '.$fecha2.'<br>';
        //exit;
        
        if($fecha2==null){
            return null;
        }else{
        
		//defino fecha 1 fecha inferior
		$fecha_res=explode("/",$fecha1);
		$dia1 =$fecha_res[0];
		$mes1 =$fecha_res[1];
		$ano1 =$fecha_res[2];
        
        //echo $dia1.'<br>';
        //echo $mes1.'<br>';
        //echo $ano1.'<br>';
        
        //exit;


		//defino fecha 2  fecha superior
		$fecha_res2=explode("/",$fecha2);
		// echo alerta(' d/f '.$fecha_res2);
		$dia2 =$fecha_res2[0];
		$mes2 =$fecha_res2[1];
		$ano2 =$fecha_res2[2];

		//calculo timestam de las dos fechas
		date_default_timezone_set('America/Mexico_City');
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
		$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

		// ECHO $timestamp1,$timestamp2;

		//resto a una fecha la otra
		$segundos_diferencia = $timestamp1 - $timestamp2;
		//echo $segundos_diferencia;

		//convierto segundos en días
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		//obtengo el valor absoulto de los días (quito el posible signo negativo)
		$dias_diferencia = abs($dias_diferencia);

		//quito los decimales a los días de diferencia
		$dias_diferencia = floor($dias_diferencia);

		return $dias_diferencia;
	}


	}

	public static function convertir_fecha_excel($numero = 0){
    	
    	$UNIX_DATE = ($numero - 25569) * 86400;
		return gmdate("Y-m-d", $UNIX_DATE);
    }
}