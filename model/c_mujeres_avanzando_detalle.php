<?php
/**
* Clase que nos permite administrar lo relacionado a la tabla c_mujeres_avanzando
* (programas ligados al usuario) 
 * **/ 

//Inclumos librería Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');
//Incluimos librería Artículos
include_once($_SESSION['inc_path'].'libs/Articulos.php');               
//Incluimos modelo de servicio_caravana
include_once($_SESSION['model_path'].'servicio_caravana.php');

class mujeresAvanzandoDetalle extends MysqliDb{

    public function __construct(){}

    /**
    * Ejecutamos sentencia sql con parámetros
    * @param string $sql Sentencia SQL
    * @param array $params Cada uno de los parámetros de la sentencia
    * 
    * @return int Resultado
    * */   
    private static function executar($sql,$params){

    //Ejecutamos
    $resultado = self::getInstance()->rawQuery($sql, $params);

    //Regresamos resultado
    return $resultado;        
    
    }

    /**
    * Obtenemos los datos del aspirante por su id
    *@param int $id_mujeres_avanzando id de la tabla aspirante
    *
    *@return Array datos de aspirante
    **/
    public static function get_servicio($id_mujeres_avanzando){

        $datos = self::getInstance()->join("c_servicio s", 
                                    "s.ID_C_SERVICIO = c.ID_C_SERVICIO ", "LEFT")
                                    ->where('c.ID_MUJERES_AVANZANDO', $id_mujeres_avanzando)
                                    ->getOne('c_mujeres_avanzando_detalle c',
                                            'c.*, s.ES_CONTABLE');
        return $datos;
    }

    /**
     * Guardamos el servicio asignado
     * @param array $submodulo Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveMujerServ($mujer_data,$id = null){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable de caravana
        $id_caravana = 0;        

        //Si servicio es contable
        $ES_CONTABLE = '';

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($mujer_data as $key => $value):
        ${$key} = self::getInstance()->real_escape_string($value);
        endforeach;

        //Obtenemos servicio (en caso de tener previamente uno)
        $c_mujeres_avanzando_detalle = mujeresAvanzandoDetalle::get_servicio($ID_MUJERES_AVANZANDO);

        if($c_mujeres_avanzando_detalle != NULL){
            
            //Datos para actualizar el servicio en caravana
            $id_caravana = $c_mujeres_avanzando_detalle['id_caravana'];   
            $prev_ID_C_SERVICIO = $c_mujeres_avanzando_detalle['ID_C_SERVICIO'];
            $ES_CONTABLE = $c_mujeres_avanzando_detalle['ES_CONTABLE'];   
            
            //Como se cambiará de servicio, aumentamos en 1 en stock 
            //de este servicio que será dejado         
            if($ES_CONTABLE == 'SI'){
                ServicioCaravana::incServ($prev_ID_C_SERVICIO,$id_caravana);
            }     
            
        }

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];

        //Obtenemos el id de la caravana
        $id_caravana = $_SESSION['id_caravana'];

            //Campos obligatorios
            if ($ID_C_SERVICIO != NULL && $ID_MUJERES_AVANZANDO != NULL) 
            {                                        
                
                $insertData = array(
                'ID_C_SERVICIO' => $ID_C_SERVICIO,
                'ID_MUJERES_AVANZANDO' => $ID_MUJERES_AVANZANDO,
                'USUARIO_ALTA' => $id_usuario_creador,
                'id_caravana' => $id_caravana
                );
                
                //Quitamos del arreglo los valores vacíos
                //$insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
                
                //Si recibimos id para editar
                if(intval($id)>0){
                    //Indicamos que haremos un update
                    $db_accion = 'update';
    
                    //Agregamos condición para indicar qué id se actualiza
                    self::getInstance()->where('ID_MUJERES_AVANZANDO_DETALLE',$id);                                        
                }
                
                //Iniciamos transacción
                self::getInstance()->startTransaction();
                
                if(! self::getInstance()->{$db_accion}('c_mujeres_avanzando_detalle', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3; 
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback();
                    
                    }else{
                    
                    //Campos guardados correctamente
                    $msg_no = 1;     
                    
                    //Verificamos si la caravana tiene un stock del servicio
                    //en caso de no tenerlo, se creará un registro con el stock
                    //predeterminado de la tabla de servicios
                    $servicio_caravana = ServicioCaravana::buscaServCaravana($ID_C_SERVICIO,
                                                                            $id_caravana);
                    /*
                    print_r($servicio_caravana);
                        echo $ID_C_SERVICIO.' - '.$id_caravana;
                        exit;
                    */

                    //Si no encontramos el servicio, lo crearemos
                    if($servicio_caravana == NULL){                        

                        $servicio = Servicio::get_by_id($ID_C_SERVICIO);
                        $stock = ($servicio['ES_CONTABLE'] == 'SI')? $servicio['STOCK'] : NULL;

                        //Armamos los datos a guardar
                        $stock_data = array('ID_C_SERVICIO' => $ID_C_SERVICIO,
                                       'id_caravana' => $id_caravana,
                                       'stock' => $stock);
                        
                        /*
                        print_r($stock_data);
                        exit;
                        */

                        $ES_CONTABLE = $servicio['ES_CONTABLE'];

                        $msg_no = ServicioCaravana::saveServicioCaravana($stock_data);
                    }else{
                        $ES_CONTABLE = $servicio_caravana['ES_CONTABLE'];
                    }
                    
                        if($ES_CONTABLE == 'SI'){
                            //Decrementamos stock en el servicio de esta caravana
                            ServicioCaravana::decServ($ID_C_SERVICIO,$id_caravana);    
                        }
                    
                        //Verificamos si se pudieron realizar los cambios
                        if($msg_no == 1){
                            //Guardamos campos afectados en la tabla
                            self::getInstance()->commit();               
                        }else{
                            //Cancelamos los posibles campos afectados
                            self::getInstance()->rollback();    
                        }
                    
                    } 
    
            }else{
            //'Campos Incompletos'
            $msg_no = 2;             
            }
                
            return $msg_no;  
        }        

    /**
     * Obtenemos la consulta y parámetros para paginar o listar
     * los servicios ligados a la beneficiaria
     * @param int $id_mujeres_avanzando ID de la mujer buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro       
     *       
     * @return array Regresamos consulta y parámetros resultado de la consulta
     * */
    private static function listadoMujerServ($id_mujeres_avanzando = NULL,$busqueda=NULL,
        $tipo_filtro=NULL)
    {
            
        //Consulta utilizada cuando se usa solamente beneficiario_pys 

        $sql = 
        'SELECT 
        c.ID_MUJERES_AVANZANDO as id,
        p.NOMBRE as nombre_programa,
        s.NOMBRE as nombre_servicio,
        c.FECHA_ALTA as fecha_alta,
        c.ID_C_ESTATUS_SERVICIO as estado_actual
        FROM `c_mujeres_avanzando_detalle` c 
        INNER JOIN mujeres_avanzando m on m.id = c.ID_MUJERES_AVANZANDO
        LEFT JOIN c_servicio s on c.ID_C_SERVICIO = s.ID_C_SERVICIO
        LEFT JOIN c_programa p on s.ID_C_PROGRAMA = p.ID_C_PROGRAMA
        where ? ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtramos un beneficiario
        if($id_mujeres_avanzando){
          $sql .= " and c.ID_MUJERES_AVANZANDO = ? ";
          $params[] = $id_mujeres_avanzando; 
        }                

        $sql .= " GROUP BY 1 ";

        //echo $sql;

        return array($sql,$params);
    }

    /**
     * Obtenemos listado PAGINADO de los servicios ligados al beneficiario 
     * @param int $id_mujeres_avanzando ID del beneficiario a buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro   
     *       
     * @return array Resultado de la consulta
     * */
    public static function listaPagMujerServ($id_mujeres_avanzando = NULL,$busqueda=NULL,
        $tipo_filtro=NULL)
    {

        list($sql,$params) = self::listadoMujerServ($id_mujeres_avanzando,$busqueda,$tipo_filtro);
        return Paginador::paginar($sql,$params);           
    }

    /**
     * Obtenemos LISTA de los servicios ligados al beneficiario 
     * @param int $id_mujeres_avanzando ID del beneficiario a buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro   
     *       
     * @return array Resultado de la consulta
     * */
    public static function listaBeneficiarioServ($id_mujeres_avanzando = NULL,$busqueda=NULL,
        $tipo_filtro=NULL)
    {

        list($sql,$params) = self::listadoBeneficiarioServ($id_mujeres_avanzando,$busqueda,$tipo_filtro);
        return self::executar($sql,$params);
    }

    /**
     * Guardamos registro en la tabla c_mujeres_avanzando_detalle
     * @param array $c_mujeres_avanzando_detalle Arreglo con los campos a guardar
     * @param int $id del registro a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveMujerAvanzandoDet($c_mujeres_avanzando_detalle,$id = null){        

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo con los id's generados
        $id_generados = array();

        //Arreglo donde contedremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/
        foreach($c_mujeres_avanzando_detalle as $key => $value):

        ${$key} = self::getInstance()->real_escape_string($value);

        endforeach;        

        //Obtenemos el id del usuario creador
        $id_usuario = $_SESSION['usr_id'];

        /*Si no esta creada la variable activo 
         predeterminadamente la guardamos = 1*/        
        if(!isset($activo) ){
            $activo = 1 ;            
        }

        //Campos obligatorios
        if($id_mujeres_avanzando && $ID_C_SERVICIO){                       

            //Quitamos vueltas de carro
            $desc_ubicacion = trim(str_replace("\\r\\n"," ",$desc_ubicacion));

            $insertData = array(
                'ID_MUJERES_AVANZANDO' => $id_mujeres_avanzando,
                'ID_C_ESTATUS_SERVICIO' => $activo,
                'USUARIO_ALTA' => $id_usuario
                //'FECHA_ALTA' => date('Y-m-d H:i:s')
                );                

            //Quitamos del arreglo los valores vacíos
            $insertData = array_filter($insertData, 
                                       create_function('$a','return preg_match("#\S#", $a);'));

            //Si tenemos un arreglo de artículos (carrito)
            if(isset($_SESSION['arrayArt'])){

                //Arreglo de fechas
                $arreglo_fechas = json_decode($c_mujeres_avanzando_detalle['arreglo_fechas']);

                //Enviamos datos para guardar cada servicio
                list($msg_no,$id_generados) = self::saveServicios($insertData,
                                                                  $id_mujeres_avanzando,
                                                                  $arreglo_fechas);

            }else{
                //Guardaremos solamente 1 producto/servicio

                //Obtenemos los códigos
                $datos = Producto_servicio::getCodCompPys($ID_C_SERVICIO);

                //Complementamos arreglo para guardar el servicio
                $insertData['ID_C_SERVICIO'] = $ID_C_SERVICIO;                
                $insertData['ID_C_ACTIVIDAD_TALLER'] = (isset($ID_C_ACTIVIDAD_TALLER))? $ID_C_ACTIVIDAD_TALLER : null;
                $insertData['PUNTOS_OTORGADOS'] = (isset($PUNTOS_OTORGADOS))? $PUNTOS_OTORGADOS : null;

                //Iniciamos transacción
                self::getInstance()->startTransaction(); 

                //Enviamos datos 
                list($msg_no,$id_gen) = self::guardaServ($insertData,$id);                

                //Guardamos el id generado
                $id_generados[] = $id_gen;

                //Si no hubo error al insertar algún servicio
                if($msg_no == 1){

                    //Guardamos campos afectados en la tabla
                    self::getInstance()->commit();

                }else{
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback(); 
                }             

            }

        }else{
            //'Campos Incompletos'
            $msg_no = 2;             
        }

        return array($msg_no,$id_generados);

    } 
    
    /**
    *Función para guardar los productos y/o servicios recolectados en carrito
    *@param Array $insertData Datos recolectados del formulario
    *@param Array $arreglo_fechas Cada una de las fechas del producto/servicio
    *
    *@return Array Arreglo con: int $msg_no Mensaje de respuesta
    *                           array $id_generados ID's generados de la tabla beneficiario_pys      
    **/
    private static function saveServicios($insertData = NULL,$id_mujeres_avanzando,
        $arreglo_fechas = NULL)
    {

        //Obtenemos objeto con los artículos 
        $articulos = unserialize($_SESSION['arrayArt']);

        //Arreglo de id's generados
        $id_generados = array();

        $msg_no = 1;

        //Iniciamos transacción
        self::getInstance()->startTransaction();        

        //Recorremos arreglo
        foreach($articulos->articulo_id as $key => $value):

            //Complementamos arreglo para guardar el servicio
            $insertData['ID_C_SERVICIO'] = $value;
            $insertData['ID_C_ACTIVIDAD_TALLER'] = (isset($articulos->ID_C_ACTIVIDAD_TALLER))? $articulos->ID_C_ACTIVIDAD_TALLER[$key] : null;
            $insertData['PUNTOS_OTORGADOS'] = (isset($articulos->PUNTOS_OTORGADOS))? $articulos->PUNTOS_OTORGADOS[$key] : null;
            //$insertData['FECHA_ALTA'] = $arreglo_fechas[$key];

            //Buscamos si ya está este registro pero como inactivo
            $sql = 'SELECT                    
                    cmd.ID_MUJERES_AVANZANDO_DETALLE,
                    cmd.ID_C_ESTATUS_SERVICIO 
                    from  c_mujeres_avanzando_detalle cmd               
                    where 1
                    AND cmd.ID_C_SERVICIO = ?
                    AND cmd.ID_MUJERES_AVANZANDO = ?
                    AND cmd.ID_C_ESTATUS_SERVICIO = 0 ';

            $params = array($value,$id_mujeres_avanzando);     

            //Verificamos el estatus del Modulo        
            $servicio = self::executar($sql,$params);
            $servicio = $servicio[0];

            /*Previamente había un registro inactivo de este servicio, 
            sólo será activado de nuevo, caso contrario, agregamos un nueco
            registro en la tabla*/
            if($servicio != NULL){

              $msg_no = self::activaServicioMujer($value,$id_mujeres_avanzando);

            }else{

              //Guardamos cada registro, en caso de haber error, cancelamos los registros
              list($msg_no,$id) = self::guardaServ($insertData);

              //Guardamos lista de id's generados
              $id_generados[] = $id;
            }

            //Si tenemos mensaje de error
            if($msg_no == 3){

            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback();               

            }                    

        endforeach;

        //Si no hubo error al insertar algún servicio
        if($msg_no == 1){

            //Guardamos campos afectados en la tabla
            self::getInstance()->commit();

            //Eliminamos variable de sesión de carrito
            unset($_SESSION['arrayArt']);

        }                

        return array($msg_no,$id_generados);

    }
    

    /**
    *Función con la que guardamos el producto/servicio
    *@param Array $insertData Arreglo con información a guardar
    *
    *@return Array que contiene int $msg_no Mensaje al ejecutar función
    *                           int $id ID generado de la tabla beneficiario_pys
    */
    private static function guardaServ($insertData = NULL,$id = NULL){

        //Variable con el mensaje final
        $msg_no = 0;
        
        //Predeterminadamente insertamos registro
        $db_accion = 'insert';

        //Si recibimos id para editar
        if(intval($id)>0){                    

            //Indicamos que haremos un update
            $db_accion = 'update';
                  
            //Agregamos condición para indicar qué id se actualiza
            self::getInstance()->where('ID_MUJERES_AVANZANDO_DETALLE',$id);

        }

        if(!self::getInstance()->{$db_accion}('c_mujeres_avanzando_detalle', $insertData)){
            
            //No se pudo guardar uno de los servicios
            $msg_no = 3;

        }else{
            
            //Datos guardados correctamente
            $msg_no = 1;

            //Obtenemos el id del registro ya sea que lo actualizamos o generamos
            $id = ($id != NULL)? $id : self::getInstance()->getInsertId();

            //Aquí deberíamos reducir el stock de servicios_caravana
            /*
            UPDATE table 
              SET field = field - 1
              WHERE id = $number"
              and field > 0
            */
        }

        return array($msg_no,$id);
    }

   
    /**
    *Activamos o desactivamos un producto/servicio del beneficiario
    *@param int $id_beneficiario_pys id de la tabla beneficiario_pys
    *
    *@return int $msg_no Mensaje
    **/
     public static function activaServicioMujer($ID_MUJERES_AVANZANDO_DETALLE = NULL)
     {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla beneficiario_pys
        $sql = 'SELECT 
                ID_MUJERES_AVANZANDO_DETALLE, 
                ID_C_ESTATUS_SERVICIO 
                from `c_mujeres_avanzando_detalle` 
                where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($ID_MUJERES_AVANZANDO_DETALLE != NULL){

                $sql .= ' AND ID_MUJERES_AVANZANDO_DETALLE = ?';
                //parámetros para la consulta
                $params = array($ID_MUJERES_AVANZANDO_DETALLE);

                //Verificamos el estatus del Modulo        
                $registro = self::executar($sql,$params);
                $registro = $registro[0];

                //Si el registro tiene estatus de 'Eliminado', se activará
                if($registro['ID_C_ESTATUS_SERVICIO'] == 0){
                    $estatus = 1;
                }else if($registro['ID_C_ESTATUS_SERVICIO'] == 1){

                //Si el registro tiene estatus de 'Activo', se eliminará
                $estatus = 0;

                }

                //Obtenemos id del registro
                $ID_MUJERES_AVANZANDO = $registro['ID_MUJERES_AVANZANDO_DETALLE'];

                //Preparamos update
                self::getInstance()->where('ID_MUJERES_AVANZANDO_DETALLE',$ID_MUJERES_AVANZANDO);                                                

                //datos a actualizar
                $updateData = array('ID_C_ESTATUS_SERVICIO' => $estatus);

                //Iniciamos transacción
                self::getInstance()->startTransaction();

                if(!self::getInstance()->update('c_mujeres_avanzando_detalle',$updateData)){

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

        //ERROR: Campos incompletos
        $msg_no = 2;                       

        }        
        
        return $msg_no;

    }


    /**
     * Obtenemos la consulta y parámetros para paginar o listar
     * los servicios ligados al beneficiario 
     * @param int $id_mujeres_avanzando ID del beneficiario a buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro       
     *       
     * @return array Regresamos consulta y parámetros resultado de la consulta
     * */
    private static function listadoServMujer($id_mujeres_avanzando = NULL,$busqueda=NULL,
        $tipo_filtro=NULL)
    {
        $sql = 
        'SELECT 
         bp.ID_MUJERES_AVANZANDO_DETALLE as id,
         bp.ID_MUJERES_AVANZANDO,
         concat(b.nombres,?,b.paterno,?,b.materno) as nombre_completo,
         bp.ID_C_SERVICIO,
         s.NOMBRE as nombre_servicio,
         p.NOMBRE as nombre_programa,
         g.grado,
         -- pys.tipo,
         -- bp.fecha_asignado,
         bp.FECHA_ALTA as fecha_alta,
         bp.ID_C_ESTATUS_SERVICIO as activo
         from c_mujeres_avanzando_detalle bp
         LEFT JOIN mujeres_avanzando b on bp.ID_MUJERES_AVANZANDO = b.id
         LEFT JOIN c_servicio s on bp.ID_C_SERVICIO = s.ID_C_SERVICIO
         LEFT JOIN c_programa p on s.ID_C_PROGRAMA = p.ID_C_PROGRAMA
         LEFT JOIN grado g on g.id = s.ID_GRADO
         where 1 ';

         $params = array(' ',' ');

         //Filtramos un beneficiario
        if($id_mujeres_avanzando != NULL){

          $sql .= " and bp.ID_MUJERES_AVANZANDO = ? ";
          $params[] = $id_mujeres_avanzando; 

        }                

        return array($sql,$params);

    }


    /**
     * Obtenemos listado PAGINADO de los servicios ligados al beneficiario 
     * @param int $id_beneficiario ID del beneficiario a buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro   
     *       
     * @return array Resultado de la consulta
     * */

    public static function listaPagServMujer($id_beneficiario = NULL,$busqueda=NULL,$tipo_filtro=NULL){

        list($sql,$params) = self::listadoServMujer($id_beneficiario,$busqueda,$tipo_filtro);
        return Paginador::paginar($sql,$params);           
    }

    
    //listaPysBeneficiario
    /**
     * Obtenemos LISTA de los servicios ligados al beneficiario 
     * @param int $id_mujeres_avanzando ID del beneficiario a buscar
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro   
     *       
     * @return array Resultado de la consulta
     * */
    public static function listaServMujer($id_mujeres_avanzando = NULL,$busqueda=NULL,$tipo_filtro=NULL){

        list($sql,$params) = self::listadoServMujer($id_mujeres_avanzando,$busqueda,$tipo_filtro);
        return self::executar($sql,$params);
    }

     /**
     * Obtenemos listado de los productos y servicios ACTIVOS que ya dispone un beneficiario
     * @param int $id_mujeres_avanzando
     * 
     * @return array Id's de los productos y servicios
     * **/
    public static function listaArrServMujer($id_mujeres_avanzando){

        $resultado = self::listaServMujer($id_mujeres_avanzando);         
        $serv = array();

        foreach($resultado as $l):

        $serv[] = $l['id_producto_servicio'];

        endforeach;

        return $serv;
    }

}

?>