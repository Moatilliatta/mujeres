<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla de capacitaciones
 * **/ 

//Incluimos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos librería de fecha
include_once($_SESSION['inc_path'].'libs/Fechas.php');

class Seg_punto_rosa_capacitacion extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
        
    /**
    * Obtenemos los datos de un punto rosa ligado a su capacitación por su id
    *@param int $id_seg_punto_rosa id de la tabla seg_punto_rosa_capacitacion.
    *
    *@return Array datos de la tabla sericip
    **/
    public static function get_by_id($id_seg_punto_rosa_capacitacion){

        $datos = self::getInstance()->where('id', $id_seg_punto_rosa_capacitacion)
                                    ->getOne('seg_punto_rosa_capacitacion');


        return $datos;
    }

    /**
    * Obtenemos los datos de un  registro mediante el ID del punto rosa y el
    * id de su capacitación
    *@param int $id_seg_punto_rosa id de la tabla seg_punto_rosa_capacitacion.
    *@param int $id_seg_capacitacion id de la tabla seg_capacitacion.
    *
    *@return Array datos de la tabla seg_punto_rosa_capacitacion
    **/
    public static function get_by_punto_cap($id_seg_punto_rosa,$id_seg_capacitacion){
        //echo $id_seg_punto_rosa;
        //exit;
        
        $datos = self::getInstance()->where('id_seg_punto_rosa', $id_seg_punto_rosa)
                                    ->where('id_seg_capacitacion', $id_seg_capacitacion)
                                    ->getOne('seg_punto_rosa_capacitacion');
    
       
        return $datos;
    }

    /**
     * Función que nos generará un listado de capacitaciones(platicas)
     * 
     */
     public static function listaSegPuntoRosaCapMujer($id_mujeres_avanzando,$id_seg_punto_rosa){

         $sql =  ' SELECT
                   spr.id,
                   spr.id_seg_punto_rosa,
                   spr.id_seg_capacitacion,
                   scm.fecha_capacitacion,
                   scm.id_mujer_avanzando,
                   scm.activo as activo_t2,
                   c.nombre as capacitacion
                   from seg_punto_rosa_capacitacion spr       
                   LEFT JOIN seg_capacitacion c on c.id = spr.id_seg_capacitacion
                   LEFT JOIN seg_capacitacion_mujer scm on scm.id_seg_punto_rosa_capacitacion = spr.id 
                                                        and scm.id_mujer_avanzando = ?
                   where spr.id_seg_punto_rosa = ? ';
        
        //Parámetros de la sentencia
        $params = array($id_mujer,$id_seg_punto_rosa);

        return array($sql,$params);          
     }
     
    /**
    * Obtenemos listado de los puntos rosas ligadas ala mujer
   * @param int $id_mujeres_avanzando id del la mujer a buscar
    *
    * @return Array $capacitacion_mujer Arreglo con listado de mujeres
    **/
    public static function puntosRosaCapMujer($id_mujeres_avanzando = NULL){

         //Buscamos si ya está este registro pero como inactivo
        $sql = 'SELECT
                scm.id_seg_punto_rosa_capacitacion 
                from seg_capacitacion_mujer scm
                where scm.id_mujeres_avanzando = ? and scm.activo = 1';

        $params = array($id_mujeres_avanzando);         

        /*
        echo $sql;
        print_r($params);
        */
        
        //Obtenemos todos los componentes        
        $resultado = self::executar($sql,$params);
        
        $l = array(); 
        
      //Recorremos arreglo $resultado para obtener los puros id_seg_punto_rosa_capacitacion
      //y comparar con in_array();  
        foreach ($resultado as $key => $value): 
        $l[] = $value['id_seg_punto_rosa_capacitacion']; 
        endforeach;

        //print_r($resultado);
        
        return $l;
    }    
    
   /**
    * Función genérica para listar capacitaciones(platicas) de los beneficiarios 
    * @param  [type] $id_seg_punto_rosa   [description]
    * @param  [type] $id_seg_capacitacion [description]
    * @return [type]                      [description]
    */
    private static function listSegPuntoRosaCap($id_seg_punto_rosa = NULL,$id_seg_capacitacion = NULL){

      //Buscamos si ya está este registro pero como inactivo
      $sql = 'SELECT 
              sprc.*,
              r.descripcion as punto_rosa,
              c.nombre as capacitacion
              from seg_punto_rosa_capacitacion sprc 
              LEFT JOIN seg_punto_rosa r on sprc.id_seg_punto_rosa = r.id
              LEFT JOIN seg_capacitacion c on sprc.id_seg_capacitacion = c.id
              where c.tipo = ? ';
      $params = array('seguimiento');   
      //Buscamos por id_seg_punto_rosa           
      if($id_seg_punto_rosa !=null){
                    
          $sql .= ' AND sprc.id_seg_punto_rosa = ? ';
          $params[] = $id_seg_punto_rosa;

      }

      //Buscamos por id_seg_capacitacion           
      if($id_seg_capacitacion !=null){
                    
          $sql .= ' AND sprc.id_seg_capacitacion = ? ';
          $params[] = $id_seg_capacitacion;

      }

      return array($sql,$params);

    }
    
    /**
     * Función genérica para listar capacitaciones(huertos) de los beneficiarios
     * @param  [type] $id_seg_punto_rosa   [description]
     * @param  [type] $id_seg_capacitacion [description]
     * @return [type]                      [description]
     */
    private static function listHuertoCap($id_seg_punto_rosa = NULL,$id_seg_capacitacion = NULL){
        
      $sql = 'SELECT 
              sprc.*,
              r.descripcion as punto_rosa,
              c.nombre as capacitacion
              from seg_punto_rosa_capacitacion sprc 
              LEFT JOIN seg_punto_rosa r on sprc.id_seg_punto_rosa = r.id
              LEFT JOIN seg_capacitacion c on sprc.id_seg_capacitacion = c.id
              where c.tipo = ?';
              
     $params = array('huerto');          

      //Buscamos por id_seg_punto_rosa           
      if($id_seg_punto_rosa !=null){
                    
          $sql .= ' AND sprc.id_seg_punto_rosa = ? ';
          $params[] = $id_seg_punto_rosa;

      }

      //Buscamos por id_seg_capacitacion           
      if($id_seg_capacitacion !=null){
                    
          $sql .= ' AND sprc.id_seg_capacitacion = ? ';
          $params[] = $id_seg_capacitacion;

      }

      return array($sql,$params);  
        
        
    } 
    
   
  /**
   * Listamos capacitaciones huertas 
   * @param  [type] $id_seg_punto_rosa   [description]
   * @param  [type] $id_seg_capacitacion [description]
   * @return [type]                      [description]
   */
    public static function listaSegPuntoRosaHuertos($id_seg_punto_rosa = NULL,$id_seg_capacitacion = NULL){
      list($sql,$params) = self::listHuertoCap($id_seg_punto_rosa,$id_seg_capacitacion); 
                           
      $resultado = self::executar($sql,$params);

      return $resultado;
    }

    /**
     * Listado de las capacitaciones por puntos rosas
     * @param  [type] $id_seg_punto_rosa   [description]
     * @param  [type] $id_seg_capacitacion [description]
     * @return [type]                      [description]
     */
    public static function listaSegPuntoRosaCap($id_seg_punto_rosa = NULL,$id_seg_capacitacion = NULL){
      list($sql,$params) = self::listSegPuntoRosaCap($id_seg_punto_rosa,$id_seg_capacitacion); 
                           
      $resultado = self::executar($sql,$params);

      return $resultado;
    }

    /**
     * Obtenemos un arreglo con el ID de las capacitaciones
     * @param  [type] $id_seg_punto_rosa   [description]
     * @param  [type] $id_seg_capacitacion [description]
     * @return [type]                      [description]
     */
    public static function capacitaciones($id_seg_punto_rosa = NULL,$id_seg_capacitacion = NULL){
        
        list($sql,$params) = self::listSegPuntoRosaCap($id_seg_punto_rosa,$id_seg_capacitacion); 
                           
        $resultado = self::executar($sql,$params);
        
        $l = array();

        foreach ($resultado as $key => $value): 
        $l[] = $value['id']; 
        endforeach;        
       
        return $l;
    }
 
    /**
     * Obtenemos información relacionada al punto rosa y las capacitaciones
     * @param  string $nom_punto_rosa   
     * @param  string $id_cat_municipio ID del municipio
     * @return [type]                   [description]
     */
    public static function infoPuntoRosa($nom_punto_rosa=NULL,$id_cat_municipio=NULL){

      $sql = 
       'SELECT 
        c.id as id_seg_punto_rosa_capacitacion,
        c.id_seg_punto_rosa,
        c.id_seg_capacitacion,
        c.fecha_creado,
        p.descripcion,
        p.id_cat_municipio
        FROM `seg_punto_rosa_capacitacion` c 
        LEFT JOIN seg_punto_rosa p on p.id = id_seg_punto_rosa
        where 1 ';

        //Buscamos por nom_punto_rosa           
      if($nom_punto_rosa !=null){
                    
          $sql .= ' AND p.descripcion = ? ';
          $params[] = $nom_punto_rosa;

      }

      //Buscamos por id_cat_municipio           
      if($id_cat_municipio !=null){
                    
          $sql .= ' AND and p.id_cat_municipio = ? ';
          $params[] = $id_cat_municipio;

      }

      return self::executar($sql,$params);

    }

}
?>