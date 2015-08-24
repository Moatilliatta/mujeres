<?php
/**
 * Clase que nos permite administrar lo relacionado a los familiares de la beneficiaria
 * **/ 
//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
include_once($_SESSION['inc_path'].'libs/Fechas.php');

class FamiliaresMujer extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
    
    /**
     * Lista Genérica de Familiares
     * @param  [type] $busqueda    [description]
     * @param  [type] $tipo_filtro [description]
     * @param  [type] $activo      [description]
     * @param  [type] $nombre      [description]
     * @param  [type] $paterno     [description]
     * @param  [type] $materno     [description]
     * @param  [type] $curp        [description]
     * @param  [type] $id_mujer    [description]
     * @param  [type] $id_caravana [description]
     * @param  [type] $id_familiar [description]
     * @return [type]              [description]
     */
    public static function listaFamiliarGenerica($busqueda=NULL,$tipo_filtro=NULL,
      $activo = NULL,$nombre=null,$paterno=null,$materno=null,$curp=null,
      $id_mujer=NULL,$id_caravana=NULL,$id_familiar=NULL){
                
        $sql = 
        'SELECT
        f.id,
        f.fecha_nacimiento,
        m.folio,
        concat(ifnull(f.nombres,?),?,ifnull(f.paterno,?),?,ifnull(f.materno,?)) as nombre_completo,
        ests.NOM_ENT as estado_residencia,        
        mpos.NOM_MUN as nombre_municipio,        
        c.descripcion as nom_caravana 
        FROM `familiares_mujer` f
        LEFT JOIN mujeres_avanzando m on m.id_entrevista = f.id_entrevista
                         -- and CONCAT(m.folio,"-",m.num_folio) = f.cartilla
        LEFT JOIN cat_estado ests on m.CVE_EDO_RES = ests.CVE_ENT
        LEFT JOIN cat_municipio mpos on mpos.CVE_ENT_MUN = CONCAT(m.CVE_EDO_RES,m.id_cat_municipio)
        LEFT JOIN caravana c on c.id = m.id_caravana 
        WHERE ? ';   

        //Parámetros de la sentencia
        $params = array('',' ','',' ','',1);
                        
        //Filtro de búsqueda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){
            
             switch($tipo_filtro){
                
                case 'nombre':
                 
                 $alias=' concat(ifnull(f.nombres,?),?,ifnull(f.paterno,?),?,ifnull(f.materno,?)) ';
                 $params = array_merge($params,array('',' ','',' ',''));
                 $sql .=  ' AND '.$alias.' LIKE ? ';
                 $params[] = '%'.$busqueda.'%';
                    break; 
                case 'folio':
                 $sql .= ' AND m.folio LIKE ? ';
                 $params[] = '%'.$busqueda.'%';       
                    break;
                case 'calle':
                 $sql .= ' AND m.calle LIKE ? ';
                 $params[] = '%'.$busqueda.'%';       
                    break;    
                case 'caravana':
                 $sql .= ' AND c.descripcion LIKE ? ';
                 $params[] = '%'.$busqueda.'%';
                    break;    
                    
            }

        }
        
         //Buscamos nombre propio           
        if($nombre !=null){
                    
          $sql .= ' AND f.nombres like ? ';
          $params[] = '%'.$nombre.'%';    

        }

        //Apellido paterno
        if($paterno !=null){
          //echo $paterno;
          //exit;
            
          $sql .= ' AND f.paterno like ? ';
          $params[] = '%'.$paterno.'%';    

        }

        //Apellido materno
        if($materno !=null){

          $sql .= ' AND f.materno like ? ';   
          $params[] = '%'.$materno.'%';    

        }
        
        

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND f.activo = ?';
            $params[] = $activo;
        }        
        
        if($id_familiar !=NULL){
           $sql.=' AND f.id = ?';
           $params[] = $id_familiar;
        }

        if($id_caravana !=NULL){
           $sql.=' AND c.id = ?';
           $params[] = $id_caravana;
        }

        //Los agrupamos por su id
        $sql .= ' GROUP BY f.id ';
        

        //Regresamos resultado
        // self::executar($sql,$params);

        /*
        print_r($params);
        echo $sql;
        exit;
        */
       
     return array($sql,$params);      
        
        
    }
     /**
      * Listar datos de los integrantes de la familia del titular de la cartilla
      * @param  [type] $busqueda    [description]
      * @param  [type] $tipo_filtro [description]
      * @param  [type] $activo      [description]
      * @param  [type] $nombre      [description]
      * @param  [type] $paterno     [description]
      * @param  [type] $materno     [description]
      * @param  [type] $curp        [description]
      * @param  [type] $id_mujer    [description]
      * @param  [type] $id_caravana [description]
      * @return [type]              [description]
      */
     public static function listaFamiliaresMujer($busqueda=NULL,$tipo_filtro=NULL,
      $activo = NULL,$nombre=null,$paterno=null,$materno=null,$curp=null,
      $id_mujer=NULL,$id_caravana=NULL)
    {
        
       list($sql,$params) = self::listaFamiliarGenerica($busqueda,$tipo_filtro,
       $activo,$nombre,$paterno,$materno,$curp,$id_mujer,$id_caravana);
       
       $obj = Paginador::paginar($sql,$params);

       //echo self::getInstance()->getLastQuery();

       //Regresamos resultado        
       return $obj;
        
      
     }
     
     /** Funcion para precargar datos de cartilla **/
    public static function datos_cartilla($id_familiar){            
       
       $sql = 
       'SELECT
        f.id_entrevista,
        f.id,
        f.cartilla,
        f.nombres,
        f.paterno,
        f.materno,
        concat(ifnull(f.nombres,?),?,ifnull(f.paterno,?),?,ifnull(f.materno,?)) as nombre_completo,
        f.fecha_nacimiento,
        ests.NOM_ENT as estado_residencia,
        mpos.NOM_MUN as nombre_municipio,
        m.id_cat_municipio municipio_residencia,
        m.calle,
        m.desc_ubicacion,
        m.programa,
        m.visita,
        m.colonia,
        m.num_ext,
        m.num_int,
        m.CODIGO,
        e.nombre as estado_civil,
        m.telefono,
        p.nombre as pais,
        estn.NOM_ENT as estado_de_nacimiento,
        m.elcsa,
        m.nivel,
        m.calidad_dieta,
        m.diversidad,
        m.variedad,
        m.folio,
        m.id_grado,
        IF(m.activo = 1,?,?) as es_activo,        
        case WHEN (LENGTH(m.num_folio) > 1) THEN 
                      CONCAT(m.folio,?,m.num_folio)ELSE
                      m.folio
        end as folio_compuesto,
        m.id_caravana,
        c.descripcion as nom_caravana,
        IFNULL(g.grado,?) as grado,
        IFNULL(ec.elcsa,?) as desc_elcsa,
        IFNULL(nv.nivel,?) as nivel_desc,
        IFNULL(cd.calidad_dieta,?) as calidad_desc,
        IFNULL(dv.diversidad,?) as diversidad_desc,
        IFNULL(va.variedad,?) as variedad_desc
        FROM `familiares_mujer` f
        LEFT JOIN mujeres_avanzando m on m.id_entrevista = f.id_entrevista
                         -- and CONCAT(m.folio,?,m.num_folio) = f.cartilla
        LEFT JOIN cat_estado ests on m.CVE_EDO_RES = ests.CVE_ENT
        left join cat_municipio mpos on m.id_cat_municipio = mpos.CVE_MUN 
                                and mpos.CVE_ENT = m.CVE_EDO_RES
        LEFT JOIN caravana c on c.id = m.id_caravana
        LEFT JOIN estado_civil e on e.id = m.id_estado_civil
        LEFT JOIN pais p on p.id = m.id_pais
        LEFT JOIN cat_estado estn on m.id_cat_estado = estn.CVE_ENT
        LEFT JOIN elcsa el on el.id = m.elcsa
        LEFT JOIN nivel_socioeconomico nv on nv.id = m.nivel
        LEFT JOIN calidad_dieta cd on cd.id = m.calidad_dieta 
        LEFT JOIN diversidad d on d.id = m.diversidad
        LEFT JOIN variedad v on v.id = m.variedad
        LEFT JOIN grado g on g.id = m.id_grado
        LEFT JOIN elcsa ec on ec.id = m.elcsa
        LEFT JOIN diversidad dv on dv.id = m.diversidad
        LEFT JOIN variedad va on va.id = m.variedad
        where ?
        
     ';   
     
      //Parámetros de la sentencia
      $params = array('',' ','',' ','','SI','NO','-','(SIN ESPECIFICAR)','(SIN ESPECIFICAR)','(SIN ESPECIFICAR)','(SIN ESPECIFICAR)','(SIN ESPECIFICAR)','(SIN ESPECIFICAR)',1);
        
      if($id_familiar !=NULL){
           $sql.=' AND f.id = ?';
           $params[] = $id_familiar;
      }

       $datos = self::executar($sql,$params);   
       $datos = $datos[0];
       
       $mujeres_avanzando['nombres']=$datos['nombres'];
       $mujeres_avanzando['paterno']=$datos['paterno'];
       $mujeres_avanzando['materno']=$datos['materno'];
       $mujeres_avanzando['calle']=$datos['calle'];
       $mujeres_avanzando['desc_ubicacion']=$datos['desc_ubicacion'];
       $mujeres_avanzando['programa']=$datos['programa'];
       $mujeres_avanzando['visita']=$datos['visita'];
       $mujeres_avanzando['colonia']=$datos['colonia'];
       $mujeres_avanzando['num_ext']=$datos['num_ext'];
       $mujeres_avanzando['num_int']=$datos['num_int'];
       $mujeres_avanzando['CVE_EDO_RES']=$datos['estado_residencia'];
       $mujeres_avanzando['id_cat_municipio']=$datos['municipio_residencia'];
       $mujeres_avanzando['CODIGO']=$datos['CODIGO'];
       $mujeres_avanzando['id_estado_civil']=$datos['estado_civil'];
       $mujeres_avanzando['telefono']=$datos['telefono'];
       $mujeres_avanzando['fecha_nacimiento']=$datos['fecha_nacimiento'];
       $mujeres_avanzando['CODIGO']=$datos['CODIGO'];
       $mujeres_avanzando['id_cat_estado']=$datos['estado_de_nacimieto'];
       $mujeres_avanzando['elcsa']=$datos['elcsa'];
       $mujeres_avanzando['nivel']=$datos['nivel'];
       $mujeres_avanzando['calidad_dieta']=$datos['calidad_dieta'];
       $mujeres_avanzando['diversidad']=$datos['diversidad']; 
       $mujeres_avanzando['variedad']=$datos['variedad'];
       $mujeres_avanzando['id_grado']=$datos['id_grado'];
       $mujeres_avanzando['grado']=$datos['grado'];
       $mujeres_avanzando['desc_elcsa']=$datos['desc_elcsa'];
       $mujeres_avanzando['nivel_desc']=$datos['nivel_desc'];
       $mujeres_avanzando['calidad_desc']=$datos['calidad_desc'];
       $mujeres_avanzando['diversidad_desc']=$datos['diversidad_desc'];
       $mujeres_avanzando['variedad_desc']=$datos['variedad_desc'];                            
       $mujeres_avanzando['folio']=$datos['folio'];
       $mujeres_avanzando['folio_compuesto']=$datos['folio_compuesto'];
       $mujeres_avanzando['id_caravana']=$datos['id_caravana'];
       $mujeres_avanzando['id_entrevista']=$datos['id_entrevista'];
       return $mujeres_avanzando;
       
    }
     

    /**
     * Guardamos los familiares directamente del excel, armamos el arreglo para
     * guardarlo con la función principal
     * @param  Array $valor Datos de los familiares
     * @return int $msg_no Mensaje de respuesta
     */
    public static function guardaFamiliares($valor){
    
        $id_entrevista = (isset($valor['B']))? $valor['B'] : null;
        $nombres = (isset($valor['F']))? $valor['F']: null;
        $paterno = (isset($valor['D']))? $valor['D']: null;   
        $materno = (isset($valor['E']))? $valor['E']: null;
        $fecha = (isset($valor['G']))? $valor['G'] : null;

        if(intval($fecha) > 0){
          $f = substr(Fechas::convertir_fecha_excel($fecha,"d/m/Y"),0,10);
          $fecha_nacimiento = Fechas::fechadmyAymd($f);
        }else{
          $fecha_nacimiento = Fechas::fechadmyAymd($fecha);
        }
        
         $datos = array('id_entrevista' => $id_entrevista,
                        'nombres'=>$nombres,
                        'paterno'=>$paterno,
                        'materno'=>$materno,
                        'fecha_nacimiento'=>$fecha_nacimiento); 

         $msg_no = self::saveFamiliares($datos);
         
         return $msg_no;
                   
        }
                
        public static function saveFamiliares($datos){
        //Variable que nos indica el mensaje generado al guardar el registro               
        $msg_no = 0;
        
        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        foreach($datos as $key => $p):
            ${$key} = self::getInstance()->real_escape_string($p);
        endforeach;

        $insertData = array(
            'id_entrevista' => $id_entrevista,
            'nombres' => $nombres,
            'paterno' => $paterno,
            'materno' => $materno,
            'fecha_nacimiento' => $fecha_nacimiento
            );

        //Quitamos del arreglo los valores vacíos
        //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

        //Si recibimos id para editar
        /*if(intval($id)>0){
            //Indicamos que haremos un update
            $accion = 'update';
            //Agregamos condición para indicar qué id se actualiza
            self::getInstance()->where('id',$id);
        }*/

        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(! self::getInstance()->{$accion}('familiares_mujer', $insertData)){
        //'Error al guardar, NO se guardo el registro'
        $msg_no = 3;

        //Cancelamos los posibles campos afectados
        self::getInstance()->rollback();

        }else{
                //Campos guardados correctamente
                $msg_no = 1;
                //Guardamos los campos afectados en la tabla
                self::getInstance()->commit();
        } 
                           
        return $msg_no;
    }

    /**
     * Obtenemos un listado de posibles familiares ligados a los id
     * de entrevista que NO coincidieron al procesarse el excel
     * @param  array $id_entrevista_noc Arreglo de ID's de entrevistas
     * @return array Listado final
     */
    public function listaPosiblesFam($id_entrevista_noc){
        
        $lista = NULL;

        if(is_array($id_entrevista_noc) && count($id_entrevista_noc) > 0){

            $lista = self::getInstance()->where('id_entrevista', Array( 'IN' => $id_entrevista_noc))
                                        ->groupby('CONCAT(nombres,paterno,materno)')
                                        ->get('familiares_mujer');            
        }
        
        return $lista;
    }

    /**
     * Obtenemos los familiares de una caravana determinada
     * @param  integer $id_caravana ID de la tabla caravana
     * @return Array               Listado de Familiares
     */
    public function datosCaravanaFam($id_caravana = 0){

      $sql = 'SELECT 
              * 
              from familiares_mujer f 
              where f.id_entrevista in(
              SELECT 
              id_entrevista
              from mujeres_avanzando
              where id_caravana = ?
              )';
      
      $params = array($id_caravana);

      return self::executar($sql,$params);

    }
}
?>