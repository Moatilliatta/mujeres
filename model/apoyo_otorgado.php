<?php
/**
 * Clase que nos permite administrar lo relacionado a los Apoyos_Otorgados
 * **/ 
//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

//Incluimos librería de beneficiario_pys
include_once($_SESSION['model_path'].'beneficiario_pys.php');

class Apoyo_otorgado extends Db{
    
    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}
            
    /**
     * Cambiamos el estatus del módulo 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_modulo Módulo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */     
     public static function activaApoyo_otorgado($id_apoyo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla Modulo
        $sql = 'SELECT activo from `trab_apoyo_otorgado` where id = ?'; 

        //parámetros para la consulta
        $params = array($id_apoyo);                

        //Verificamos el estatus del Modulo        
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará

        if($registro['activo'] == 0){

            $estatus = 1;

        }else if($registro['activo'] == 1){

        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;

        }

        //Preparamos update
        self::getInstance()->where('id',$id_apoyo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
      
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('trab_apoyo_otorgado',$updateData)){

            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3;

            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback();  

        }else{

            //Campos guardados correctamente
            $msg_no = 1;

            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();                       

        } 

        return $msg_no;

    }
     
        /**
     * Obtenemos listado de los centros de atención 
     * @param string $nombre Nombre de Comunidad a buscar
     * @param string $localidad Localidad a buscar
     * @param $activo $clave_comunidad filtramos por la comunidad
     * @return array Resultado de la consulta
     * */

    public static function listaApoyos_otorgados($id_trab_expediente=null){
   
        $sql = 
       'SELECT
        ta.id,
        ta.id_trab_expediente,
        ta.fecha_entrega,
        te.id as numero_expediente,
        tp.nombre as apoyo_solicitado,
        ps.nombre as producto_servicio,
        ta.activo
        FROM `trab_apoyo_otorgado` ta 
        LEFT JOIN trab_expediente te on te.id = ta.id_trab_expediente
        LEFT JOIN trab_tipo_apoyo_solicitado tp on tp.id = ta.id_tipo_apoyo 
        LEFT JOIN beneficiario_pys p on p.id = ta.id_beneficiario_pys
        LEFT JOIN producto_servicio ps on p.id_producto_servicio = ps.id
        where ? 
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda
        
        //Buscamos nombre del centro           

        if($id_trab_expediente !=null){
           
          $sql .= 'and ta.id_trab_expediente = ?';

          $params[] = $id_trab_expediente;    

            
       }
       

        
        return Paginador::paginar($sql,$params);           
    }
    
       
  /**
     * Guardamos registro en la tabla centros_atencion
     * @param array $centros Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */

     public static function saveApoyo_otorgado($apoyo,$id = null){
       
       //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //id de la tabla beneficiario_pys
        $id_beneficiario_pys = 0;

      //Indicamos predeterminadamente que insertaremos un registro

        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($apoyo as $key => $value):

        ${$key} = self::getInstance()->real_escape_string($value);
    
        endforeach;

            //Obtenemos el id del usuario creador
            $id_usuario_creador = $_SESSION['usr_id'];
          
            /*Si no esta creada la variable activo 
            predeterminadamente la guardamos = 1*/        
            if(!isset($activo) ){
                $activo = 1 ;            
            }  

            //Si vamos a editar el registro, editaremos también la tabla beneficiario_pys
            if($id != NULL){

                //Agregamos condición para indicar qué id se actualiza
                $trab_apoyo_otorgado = self::getInstance()->where('id',$id)
                                                          ->getOne('trab_apoyo_otorgado');

                $id_beneficiario_pys = $trab_apoyo_otorgado['id_beneficiario_pys'];                
            }            

            //Guardamos el producto/servicio en la tabla beneficiario_pys
            list($msg_no,$id_generados)= Beneficiario_pys::saveBeneficiario_pys($apoyo,
                                                                                $id_beneficiario_pys);
            $id_beneficiario_pys = $id_generados[0];          
            

            //Campos obligatorios            
            if ( $id_trab_expediente && $fecha_autorizacion  
            && $cantidad && $vale && $proveedor && $costo_total && $aportacion_dif
            && $fecha_entrega && $fecha_verificacion && $contra_recibo && $numero_factura && $partida_presupuestal 
            && $numero_transferencia && $fecha_pago && $id_beneficiario_pys)
            {                                                
                
                $insertData = array(
                'fecha_autorizacion' => $fecha_autorizacion,  
                'cantidad' => $cantidad,  
                'id_tipo_apoyo' => $id_tipo_apoyo,
                'id_beneficiario_pys' => $id_beneficiario_pys,
                'vale'=> $vale,
                'proveedor'=> $proveedor,                
                'num_ext'=> $num_ext,
                'costo_total'=> $costo_total,
                'aportacion_dif' => $aportacion_dif,
                'dif_municipal' => $dif_municipal,
                'familia' => $familia,
                'otros' => $otros,
                'fecha_entrega'=> $fecha_entrega,
                'fecha_verificacion'=> $fecha_verificacion,                
                'contra_recibo' => $contra_recibo,
                'numero_factura' =>$numero_factura,              
                'partida_presupuestal' => $partida_presupuestal,
                'numero_transferencia' => $numero_transferencia,
                'id_trab_expediente' => $id_trab_expediente,
                'fecha_pago' => $fecha_pago,
                'activo' => $activo,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
                );                
               
                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
            
                //Si recibimos id para editar
                if(intval($id)>0){                    

                    //Indicamos que haremos un update
                    $accion = 'update';
                  
                    //Agregamos condición para indicar qué id se actualiza
                    self::getInstance()->where('id',$id);                                        

                }
                

                //Iniciamos transacción
                self::getInstance()->startTransaction();
                

                if(! self::getInstance()->{$accion}('trab_apoyo_otorgado', $insertData) &&
                    $msg_no != 1){

                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();
                    
                    }else{
                    
                        //Campos guardados correctamente
                        $msg_no = 1;

                        //Guardamos campos afectados en la tabla
                        self::getInstance()->commit();

                    }

                }else{
                //'Campos Incompletos'
                $msg_no = 2;             

            }        
        
                
        
        return $msg_no;
        
    }   

    /**
    *Guardamos finalmente el producto/servicio del beneficiario mediante
    * el registro de beneficiario_pys
    * @param int $id_trab_apoyo_otorgado id de la tabla trab_apoyo_otorgado
    * @param int $id_beneficiario_pys id de la tabla beneficiario_pys
    *
    * @return int $msg_no Mensaje generado
    **/
    private function guardaPysBen($id_trab_apoyo_otorgado,$id_beneficiario_pys){

        $insertData = array('id_beneficiario_pys' => $id_beneficiario_pys);

        self::getInstance()->where('id',$id_trab_apoyo_otorgado);
                        
        if(! self::getInstance()->update('trab_apoyo_otorgado', $insertData)){

            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 
            
        }else{

            //Campos guardados correctamente
            $msg_no = 1;
        }

        return $msg_no;
    }

    /**
    * Obtenemos cantidad y monto totales por mes y/o servicios especifico. 
    * Solamente obtenemos los meses con datos
    * @param int $id_servicio_especifico ID de la tabla servicio_especifico
    * @param int $mes Mes del año que queremos consultar
    *
    * @return array Resultado de la consulta
    **/
    public function repTotalPorMes($id_servicio_especifico = NULL,$mes = NULL){

        // id_producto_servicio, debe ser cambiado

        $sql = 
        'SELECT 
        MONTH(t.fecha_entrega) as mes,
        s.nombre,
        bp.id_producto_servicio,
        t.id_beneficiario_pys,
        t.costo_total,
        t.aportacion_dif,
        count(t.id) as total_mes,
        IFNULL(SUM(t.costo_total),0) as total_monto,
        IFNULL(SUM(t.aportacion_dif),0) as total_aportacion,
        IFNULL(SUM(t.cantidad),0) as total_cantidad
        FROM `trab_apoyo_otorgado` t
        LEFT JOIN beneficiario_pys bp on t.id_beneficiario_pys = bp.id
        LEFT JOIN producto_servicio s on bp.id_producto_servicio = s.id
        where 1 
        and s.id = ?         
        and YEAR(t.fecha_entrega) = year(CURRENT_DATE()) -- Año actual ';

        $params = array($id_servicio_especifico);

         //Si queremos un mes específico
            if($mes != NULL){
                $sql .= ' and MONTH(t.fecha_entrega) = ? ';
                $params[] = $mes;
            }

        $sql .= ' GROUP BY MONTH(t.fecha_entrega) ';

        return self::executar($sql,$params);
    }

    /**
    * Obtenemos cantidad y monto totales por mes por servicios especifico. 
    * De no especificar el mes, tendremos un listado por mes con 0's en los meses 
    * donde no haya datos
    * @param int $id_servicio_especifico ID de la tabla servicio_especifico
    * @param int $mes Mes del año que queremos consultar
    *
    * @return array Resultado de la consulta
    **/
    public function repTotalesPorMeses($id_servicio_especifico = NULL,$mes = NULL){            

        //Sentencia principal
        $sql=
           'SELECT 
            m.id as mes,
            -- e.nombre as servicio_especifico,
            -- bp.id_servicio_especifico,
            -- bp.id_producto_servicio,
            -- t.id_beneficiario_pys,
            -- t.costo_total as monto,
            -- t.aportacion_dif,
            count(t.id) as total_mes,
            IFNULL(SUM(t.costo_total),0) as total_monto,
            IFNULL(SUM(t.aportacion_dif),0) as total_aportacion,
            IFNULL(SUM(t.cantidad),0) as total_cantidad
            FROM meses m
            LEFT JOIN `trab_apoyo_otorgado` t on m.id = MONTH(t.fecha_entrega) 
                                              and YEAR(t.fecha_entrega) = year(CURRENT_DATE()) ';

            //Especificamos el servicio
            if($id_servicio_especifico != NULL){
                $sql .= ' and t.id IN (
                            select id 
                            from trab_apoyo_otorgado t 
                            where id_beneficiario_pys IN ( 
                                                          select id 
                                                          from beneficiario_pys 
                                                          where id_servicio_especifico = ? 
                                                          )
                            ) ';
                $params[] = $id_servicio_especifico;
            }

            $sql .= ' left JOIN beneficiario_pys bp on t.id_beneficiario_pys = bp.id ';

            //Especificamos el servicio
            if($id_servicio_especifico != NULL){
                $sql .= ' and bp.id_servicio_especifico = ?';
                $params[] = $id_servicio_especifico;
            }

            $sql .= ' left JOIN producto_servicio s on bp.id_producto_servicio = s.id ';

            //Especificamos el servicio
            if($id_servicio_especifico != NULL){
                $sql .= ' and bp.id_servicio_especifico = ? ';
                $params[] = $id_servicio_especifico;
            }
                
            $sql .= ' LEFT JOIN servicios_especificos e on bp.id_servicio_especifico = e.id
                                                        and e.orden is not null 
                                                        and e.tipo = ?
                      where 1 ';
            
            $params[] = 'apoyo';

            //Si queremos un mes específico
            if($mes != NULL){
                $sql .= ' and MONTH(t.fecha_entrega) = ? and bp.id_servicio_especifico IS NOT NULL ';
                $params[] = $mes;
            }

            $sql .= ' GROUP BY m.id ';

            //echo $sql;

            return self::executar($sql,$params);

    }


    /**
    * Obtenemos cantidad y monto totales por mes por servicios especifico. 
    * De no especificar el año, se tomará el año en curso
    * @param int $id_servicio_especifico ID de la tabla servicio_especifico
    * @param int $axo Año que queremos consultar
    *
    * @return array Resultado de la consulta
    **/
    public function TotalesMesAys($axo = NULL)
    {

        //Si no se escoge año, obtenemos predeterminadamente el año actual
        $axo = ($axo == NULL)?  date('Y') : $axo;

        $sql = 
        "SELECT 
        id,
        nombre,
        padre,
        concat(orden,?,nombre) as nombre_serv,
        IFNULL(mensual_ays(id,1,?,?),0) as cantidad_tot_ene,
        IFNULL(mensual_ays(id,1,?,?),0) as monto_tot_ene,
        IFNULL(mensual_ays(id,2,?,?),0) as cantidad_tot_feb,
        IFNULL(mensual_ays(id,2,?,?),0) as monto_tot_feb,
        IFNULL(mensual_ays(id,3,?,?),0) as cantidad_tot_mar,
        IFNULL(mensual_ays(id,3,?,?),0) as monto_tot_mar,
        IFNULL(mensual_ays(id,4,?,?),0) as cantidad_tot_abr,
        IFNULL(mensual_ays(id,4,?,?),0) as monto_tot_abr,
        IFNULL(mensual_ays(id,5,?,?),0) as cantidad_tot_may,
        IFNULL(mensual_ays(id,5,?,?),0) as monto_tot_may,
        IFNULL(mensual_ays(id,6,?,?),0) as cantidad_tot_jun,
        IFNULL(mensual_ays(id,6,?,?),0) as monto_tot_jun,
        IFNULL(mensual_ays(id,7,?,?),0) as cantidad_tot_jul,
        IFNULL(mensual_ays(id,7,?,?),0) as monto_tot_jul,
        IFNULL(mensual_ays(id,8,?,?),0) as cantidad_tot_ago,
        IFNULL(mensual_ays(id,8,?,?),0) as monto_tot_ago,
        IFNULL(mensual_ays(id,9,?,?),0) as cantidad_tot_sep,
        IFNULL(mensual_ays(id,9,?,?),0) as monto_tot_sep,
        IFNULL(mensual_ays(id,10,?,?),0) as cantidad_tot_oct,
        IFNULL(mensual_ays(id,10,?,?),0) as monto_tot_oct,
        IFNULL(mensual_ays(id,11,?,?),0) as cantidad_tot_nov,
        IFNULL(mensual_ays(id,11,?,?),0) as monto_tot_nov,
        IFNULL(mensual_ays(id,12,?,?),0) as cantidad_tot_dic,
        IFNULL(mensual_ays(id,12,?,?),0) as monto_tot_dic
        from servicios_especificos 
        where 1 
        and orden is not null 
        AND tipo = ?
        ORDER BY orden  ";

        $params = array(' ',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        $axo,'c',$axo,'m',
                        'apoyo');
        
        return self::executar($sql,$params);

    }
}

?>