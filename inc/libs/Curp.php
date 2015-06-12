<?php
class Curp extends MysqliDb{
    
public static function generar_CURP($appat,$apmat,$nombre,$fecha_nac,
  $sexo,$estado,$omitirDigitos = false)
{
  
  //Obtenemos el apellido paterno
  $appat=self::apellido_bien($appat);
  
  //Obtenemos primeras 2 letras del apellido paterno
  $_12=self::primeros_dos($appat);
  
  //Predeterminadamente asignamos 'X' a la consonante del apellido materno
  // y su 3er caracter. Caso contrario asignamos su correspondiente letra
  if ($apmat=="") {
    $consonante_apmat="X";
    $_3="X";
  } else {
    $apmat=self::apellido_bien($apmat);
    $_3=substr($apmat,0,1);
    $consonante_apmat=self::primer_consonante($apmat);
  }
  
  //Obtenemos nombre
  $nombre=self::nombre_bien($nombre);
  
  //Obtenemos primer caracter del nombre
  $_4=substr($nombre,0,1);
  
  //Obtenemos fecha
  $desbaratada=explode("/",$fecha_nac);
  $dia=$desbaratada[0];
  $mes=$desbaratada[1];
  $anio=substr($desbaratada[2],-2);  
  
  //Obtenemos sexo
  if ($sexo=='HOMBRE') $sexo='H';
  if ($sexo=='MUJER') $sexo='M';

  //Obtenemos estado y consonantes
  $estado=self::generar_estado_sin_CURP($estado);
  $consonante_appat=self::primer_consonante($appat);
  $consonante_nombre=self::primer_consonante($nombre);
  $primeras_cuatro=self::valida_altisonantes($_12.$_3.$_4);

  //Armado de la curp
  $CURP=$primeras_cuatro.$anio.$mes.$dia.$sexo.$estado.$consonante_appat.
        $consonante_apmat.$consonante_nombre;

  //Si queremos omitir los 2 dígitos finales del curp
  if($omitirDigitos === false){
    $_2digitos=self::generar_digitos($con);  
    $CURP .= $_2digitos;
  }  

  //Función strtoupper quita acentos
  $CURP=strtoupper($CURP);

  return $CURP;
}  // Fin generar_CURP.        


private static function apellido_bien($apellido) {
  $apellido_sin_prep="";
  // Aquí se le quitan las preposiciones:
  $palabras=explode(" ",$apellido);
  $j=count($palabras);
  for ($i=0;$i<$j;$i++) {
    if (!self::es_preposicion($palabras[$i]))
      $apellido_sin_prep.=$palabras[$i].' ';
  }  // Fin for.
  
  // Para quitarle el último espacio:
  $lon=strlen($apellido_sin_prep);
  $apellido_sin_prep=substr($apellido_sin_prep,0,$lon-1);
  $apellido_sin_prep=self::quita_caracteres_raros($apellido_sin_prep);
  return $apellido_sin_prep;
}  // Fin funci

private static function valida_altisonantes($palabra) {
  $sql='SELECT palabra_correcta from altisonantes where palabra_mala LIKE ?';
  $params = array($palabra); 
  $result= self::getInstance()->rawQuery($sql,$params);
  
  if ($result != null) {
    return $result["palabra_correcta"];
  }
  else {
    
    return $palabra;
  }
}  // Fin función valida_altisonantes.

private static function es_preposicion($prep) {
  
  $preposicion = array("DA","DAS","DEL","DER","DI","DIE","DD","EL","LA","LOS","LAS",
    "LE","LES","MAC","VAN","VON","I","DE","MC","MA","MA.");

  return (in_array($prep, $preposicion))? true : false;
  
}  

private static function es_letra($l) {
  $letras = array('A','Á','B','C','D','E','É','F','G','H','I','Í','J','K','L','M','N','Ñ',
    'O','Ó','P','Q','R','S','T','U','Ú','V','W','X','Y','Z');
  
  return (in_array($l, $letras))? true : false;
} 

private static function es_consonante($l) {
  $letras = array('B','C','D','F','G','H','J','K','L','M','N','P',
    'Q','R','S','T','V','W','X','Y','Z','Ñ');

  return (in_array($l, $letras))? true : false;
}

private static function es_vocal($l){
  $letras = array('A','E','I','O','U');
  return (in_array($l, $letras))? true : false;
}

private static function nombre_bien($nombre) {
  $nombre_sin_prep="";  
  $nombre_definitivo="";

  if($nombre=="MARÍA" || $nombre=="MARIA" || $nombre=="JOSÉ" || $nombre=="JOSE"){
    $nombre_definitivo=strtoupper($nombre);
  }else{
    
      //echo 'entre';
      // Aquí se le quitan las preposiciones:
      $palabras=explode(" ",$nombre);
      $j=count($palabras);


      for ($i=0;$i<$j;$i++) {
        if (!self::es_preposicion($palabras[$i])){
          $nombre_sin_prep.=$palabras[$i].' ';
        }
      }

      // Aquí se limpian los María y José.
      $palabras=explode(" ",$nombre_sin_prep);

      $j=count($palabras);

      if (($palabras[0]=="MARÍA" || $palabras[0]=="MARIA" || $palabras[0]=="JOSÉ" || 
          $palabras[0]=="JOSE") && ($palabras[1]!="")){
          
          for ($i=1;$i<$j;$i++){
            $nombre_definitivo.=$palabras[$i].' ';
          }
      
      }else{
        $nombre_definitivo=$nombre_sin_prep;
      }  

      // Para quitarle el último espacio:
      $lon=strlen($nombre_definitivo);
      $nombre_definitivo=substr($nombre_definitivo,0,$lon-1);
      //echo $nombre_definitivo;
      $nombre_definitivo=self::quita_caracteres_raros($nombre_definitivo);
        
  } 

  return $nombre_definitivo;
}  

// Reemplaza caracteres raros y los reemplaza por espacio.
private static function quita_caracteres_raros($cadena) {
  
  $cadena_limpia="";

  for ($i=0;$i<strlen($cadena);$i++) {
    
    //Verificamos si es una letra válida
    $letra = (self::es_letra($cadena[$i]))? $cadena[$i] : ' ';
    
    //Concatenamos letra
    $cadena_limpia .= $letra;
    
  }

  return $cadena_limpia;
}  

  private static function generar_estado_sin_CURP($estado) {
    $sql='SELECT Curp from cat_estado where CVE_ENT = ?';
    $params=array($estado);
    $result= self::getInstance()->rawQuery($sql,$params);
    
    if ($result!=null) {
      
      $result=$result[0];
      
      return $result["Curp"];
    }
    
    else return 0;
  }  

  private static function primer_consonante($ap_nom) {
    
    // Se toma la primer palabra del ap_nom (apellido o nombre)
    $arreglo=explode(" ",$ap_nom);
    $ap_nom=$arreglo[0];

    /*Determinamos qué posición se regresará; si la primer letra
    es consonante, se regresará la 2da consonante y 
    si la primera es vocal, se regresará la primer consonante
    */
    $num_cons = (self::es_consonante($ap_nom[0]) === true)? 2 : 1;

    //Contador de consonante
    $cont_cons = 0;

    //Predeterminadamente la consonante es vacía, es decir ""
    $cons = 'X';

    //Recorremos toda la palabra hasta encontrar alguna coincidencia
    for($i = 0; $i <= strlen($ap_nom); $i++){ 

      //Primer letra la convertimos a mayúscula
      $letra=strtoupper($ap_nom[$i]);

      //Validamos si es consonante
      $consonante = self::es_consonante($letra);
      
      //Si es consonante, aumenta contador
      if($consonante === true){
          $cont_cons++;
      }
          
      //Comprobamos que coincida con la posición indicada
      if ($cont_cons === $num_cons){      

        /*Si la letra en cuestión es Ñ, regresamos X, 
        caso contrario regresamos la letra*/
        $cons = ($letra =="Ñ")? 'X' : $letra;

        return $cons;
        
        }
          
    } 

    //Verificamos que la letra no esté vacía ni nula
    $letra = ($letra == "" || $letra == NULL)? 'X' : $letra;

    // Retorna la letra si no se entró en ningún
    return $letra;  
  }

  private static function generar_digitos() {
    //inicializamos variable
    $consecutivo='00';
    
    //Obtenemos el número consecutivo
    $result= self::getInstance()->getOne('tconsecutivos');
    
    if ($result!=null) {
      
      // En caso de no llegar al número límite 99, se lee el último consecutivo y se le suma 1
      // caso contrario se le asigna 0
      $consecutivo = ($result["CURP"]=='99')? '0' : $result["CURP"]+1;

      //Formateamos el consecutivo con 0 a la izquierda            
      $consecutivo = str_pad($consecutivo, 2, "0", STR_PAD_LEFT);

    }
    
    //Actualizamos el número consecutivo
    $insertData =array('CURP'=>$consecutivo);
    self::getInstance()->where('id',1) 
                       ->update('tconsecutivos',$insertData);
    
    return $consecutivo;
  }  

  private static function primeros_dos($appat) {
    
    // Se toma la primer palabra del apellido:
    $arreglo=explode(" ",strtoupper($appat));
    $appat = $arreglo[0];
    
    //Se toma la primera letra del apellido paterno.
    $c1 = substr($appat,0,1);    

    $c1 = ($c1=="Ñ")? 'X': $c1 ;

    $c2="X";
    
    // Empieza el for desde 1 y no desde cero para no tomar en cuenta la primer vocal.
    for ($i = 1;$i < strlen($appat);$i++) {  
      
      $aux=strtoupper($appat[$i]);

      //Tomamos la primer vocal que encontremos, hasta terminar el ciclo
      if(self::es_vocal($aux)){
        $c2 = strtoupper($aux);
        break;
      }

    } 

    return $c1.$c2;
  }  

}
?>
