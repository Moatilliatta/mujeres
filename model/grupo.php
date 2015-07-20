<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Grupo
 * **/ 

//Inclumos librería de Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class Grupo extends Db{

    /**
    * Tenemos que crear un constructor vacío por que 
    * se tomarían los valores del constructor de la clase Db 
    */
    public function __construct(){}

    /**
     * Cambiamos el estatus del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     **/
    public static function activaGrupo($id_grupo){
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla grupo
        $sql = 'SELECT activo from `grupo` where id = ?'; 

        $params = array($id_grupo);

        //Verificamos el estatus del usuario        
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
        self::getInstance()->where('id',$id_grupo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacción
        self::getInstance()->startTransaction();
        
        if(!self::getInstance()->update('grupo',$updateData)){
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
     * Obtenemos listado de los grupos. 
     * @param string $busqueda La cadena a buscar
     * @param string $tipo_filtro Tipo de filtro  
     * @param $activo Determinamos si queremos los activos, inactivos o ambos (predeterminado)      
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaGrupos($busqueda=NULL,$tipo_filtro=NULL,$activo = NULL){

        $sql = '
        SELECT
        g.*,
        u.usuario,
        e.nombre as estatus
        FROM `grupo` g
        left join usuario u on u.id = g.id_usuario_creador
        left join estatus e on e.valor = g.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(1);

         //Filtro de búsqueda
        if ($busqueda !==NULL && $tipo_filtro !==NULL){

            //switch($tipo_filtro){
                //case :
                 $sql .= ' AND g.'.$tipo_filtro.' LIKE ? ';
                 $params[] = '%'.$busqueda.'%';       
                    //break;
            //}
        }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND g.activo = ? ';
            $params[] = $activo;
        }

        //Regresamos resultado
        //return self::executar($sql,$params);
        return Paginador::paginar($sql,$params);             
    }
    
    /**
     * Guardamos registro en la tabla Usuario
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveGrupo($grupo,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Variables con arreglos de checkbox
        $grupo_mun_area = NULL;
        $ciclo_escolar = NULL;
        $axo_padron = NULL;

        //Quitamos arreglo de municipios/areas para no incluirlo en la creación de variables
        if(isset($grupo['id_municipio_area'])){
            $grupo_mun_area = $grupo['id_municipio_area'];
            unset($grupo['id_municipio_area']);
        }

        //Quitamos arreglo de ciclo escolar para no incluirlo en la creación de variables
        if(isset($grupo['ciclo_escolar'])){
            $ciclo_escolar = $grupo['ciclo_escolar'];
            unset($grupo['ciclo_escolar']);
        }        

        //Quitamos arreglo de año padrón para no incluirlo en la creación de variables
        if(isset($grupo['axo_padron'])){
            $axo_padron = $grupo['axo_padron'];
            unset($grupo['axo_padron']);
        }        


        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($grupo as $key => $g):
        ${$key} = self::getInstance()->real_escape_string($g);
        endforeach;
        
       //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `grupo` where nombre = ? ';
        $params = array($nombre);
        
        //Sólo si se edita el mismo registro puede 'repetir el nombre'
        if($id !=null){
            $sql.=' and id not in (?)';
            $params[] = $id;
            
        }
                                
        //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){
            $msg_no = 6;
            //Nombre duplicado
        }else{            
                //Obtenemos el id del usuario creador
                $id_usuario_creador = $_SESSION['usr_id'];
                
                /*Si no esta creada la variable activo 
                predeterminadamente la guardamos = 1*/        
                if(!isset($activo) ){
                    $activo = 1 ;            
                }        
                
                //Campos obligatorios
                if ($nombre) 
                {
                    include_once($_SESSION['inc_path'].'libs/Permiso.php');
                    
                    $insertData = array(
                        'nombre' => mb_strtoupper($nombre, "UTF-8"),
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
                        
                        if(! self::getInstance()->{$accion}('grupo', $insertData)){
                            //'Error al guardar, NO se guardo el registro'
                            $msg_no = 3;
                            
                            //Cancelamos los posibles campos afectados
                            self::getInstance()->rollback(); 
        
                        }else{
                            //Campos guardados correctamente
                            $msg_no = 1;     
                            
                            //Guardamos campos afectados en la tabla
                            self::getInstance()->commit();  

                            
                            //En caso de no estar editando, obtenemos el id recién creado
                            if($id == NULL){
                                $id = self::getInstance()->getInsertId();
                            }  


                            //Verificamos si se le asignará algún municipio o área
                            if(is_array($grupo_mun_area)){

                                $msg_no = self::mun_area_grupo($grupo_mun_area,$id);

                            }

                            //Verificamos si se le asignará algún ciclo escolar
                            if(is_array($ciclo_escolar)){

                                $msg_no = self::ciclo_escolar_grupo($ciclo_escolar,$id);

                            }

                            //Verificamos si se le asignará algún año de padrón
                            if(is_array($axo_padron)){

                                $msg_no = self::axo_grupo($axo_padron,$id);

                            }

                        } 
        
                }else{
                    //'Campos Incompletos'
                    $msg_no = 2;             
                }   
        }                                    

        return $msg_no;
    }

    /**
    *Listado de Municipios
    **/
    public static function lista_municipios(){
        //Obtenemos listado de municipios para asignar
        $sql = 'SELECT id,nombre FROM `municipio_area` where id BETWEEN 1 and 125;';
        $municipios = self::getInstance()->query($sql);
        
        return $municipios;    
    }

    /**
    *Listado de Áreas
    **/
    public static function lista_area(){
        //Obtenemos listado de áreas para asignar
        $sql = 'SELECT id,nombre FROM `municipio_area` where tipo NOT IN (?);';
        $params = array('Municipio');
        $area = self::getInstance()->rawQuery($sql,$params);

        return $area;
    }

    /**
    * Guardamos los municipios/areas ligados a este grupo
    * @param array $mun_area
    * @param int $id_grupo
    *
    * @return $msg_no;
    */
    private static function mun_area_grupo($mun_area,$id_grupo){

        //Validamos que se nos envíe un arreglo y un grupo a asignar
        if(is_array($mun_area) && $id_grupo != NULL){
            
            //Pondremos con estatus de inactivo
            $updateData = array(
            'activo' => 0
            );
            self::getInstance()->where('id_grupo', $id_grupo);
            $results = self::getInstance()->update('grupo_mun_area', $updateData);

            //Mensaje de Respuesta
            $msg_no = 1;
    
            //Iniciamos transacción
            self::getInstance()->startTransaction();

            foreach($mun_area as $key => $value):

                //Complementamos arreglo para guardar el servicio
                $insertData['id_grupo'] = $id_grupo;
                $insertData['id_municipio_area'] = $value;
                $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
                $insertData['fecha_creado'] = date('Y-m-d H:i:s');
                
                //Buscamos si ya está este registro pero como inactivo
                $sql = 'SELECT 
                        id, activo 
                        from `grupo_mun_area` 
                        where 1 
                        AND id_municipio_area = ? 
                        AND id_grupo = ? AND activo = 0';

                $params = array($value,$id_grupo);        
                $mun_area = self::executar($sql,$params);
                $mun_area = $mun_area[0];

                    /*Previamente había un registro inactivo de este servicio, 
                    sólo será activado de nuevo, caso contrario, agregamos un nuevo
                    registro en la tabla*/
                    if($mun_area != NULL){
                        $msg_no = self::activaMunArea($value,$id_grupo);
                    }else{                            

                            //Guardamos cada registro, en caso de haber error, cancelamos los registros
                            if(!self::getInstance()->insert('grupo_mun_area', $insertData)){
                                //No se pudo guardar uno de los servicios
                                $msg_no = 3;                                              
                            }
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
            }
        }

        return $msg_no;        

    }

/**
    *Activamos o desactivamos un municipio/área del grupo
    *@param int $id_municipio_area id del municipio
    *@param int $id_beneficiario id del grupo
    *
    *@return int $msg_no Mensaje
    **/
     public static function activaMunArea($id_municipio_area,$id_grupo,$id = NULL){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;


        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla grupo_mun_area
        $sql = 'SELECT id, activo from `grupo_mun_area` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

            $sql .= ' AND id = ?';

            //parámetros para la consulta
            $params = array($id);

        }else{

            $sql .= ' AND id_grupo = ? AND id_municipio_area = ?'; 

            //parámetros para la consulta
            $params = array($id_grupo,$id_municipio_area);

        }        

        //Obtenemos el registro
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;

        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;

        }

        //Obtenemos id del registro
        $id_grupo_mun_area = $registro['id'];

        //Preparamos update
        self::getInstance()->where('id',$id_grupo_mun_area);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('grupo_mun_area',$updateData)){

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
    * Obtenemos listado de municipios/áreas ligados al grupo 
    * En caso de NO poner el id_grupo, obtenemos los municipios 
    * del usuario logueado.
    * @param int $id_grupo id del grupo a buscar
    *
    * @return Array $grupo_mun_area Arreglo con listado de municipios
    **/
    public static function munAreaGrupo($id_grupo = NULL,$solo_mun = NULL){

        //Obtenemos listado de grupos a los que pertenece un usuario
        if($id_grupo == NULL){            

             //Cargamos modelo de usuario_grupo
            include_once($_SESSION['model_path'].'usuario_grupo.php');            

            //Obtenemos listado de grupos
            $grupos = UsuarioGrupo::listUsuarioGrupo($_SESSION['usr_id'],1);
            $grupo_usuario = array();

            foreach ($grupos as $val): 
                
                //Quitamos índice con el nombre de del municipio
                unset($val['nombre']);        
                foreach($val as $val2):                    
                    $grupo_usuario[] = str_pad($val2,3,'0', STR_PAD_LEFT);
                 endforeach;
            endforeach;

            //creamos una cadena separada por comas
            $id_grupo = implode(",", $grupo_usuario);
            
        }

         //Buscamos si ya está este registro pero como inactivo
        $sql = 'SELECT 
                g.id_municipio_area,
                m.tipo
                from `grupo_mun_area` g 
                LEFT JOIN municipio_area m on m.id = g.id_municipio_area
                where g.id_grupo IN (?) 
                AND g.activo = 1 ';

        $params = array($id_grupo);     

        if($solo_mun != NULL){

            //Filtramos municipios o áreas
            $sql .= ($solo_mun === true)? ' and m.tipo = ? ' : '';        
            $sql .= ($solo_mun === false)? ' and m.tipo != ? ' : '';       

            $params[] = 'Municipio';
        }        

        /*
        echo $sql;
        print_r($params);
        */
        
        //Obtenemos todos los municipios y áreas        
        $resultado = self::executar($sql,$params);

        $grupo_mun_area = array();

        if($resultado != NULL){            

            foreach ($resultado as $val):         
                foreach($val as $val2):
                 $grupo_mun_area[] = str_pad($val2,3,'0', STR_PAD_LEFT);
                 endforeach;
            endforeach;
        }
        
        return $grupo_mun_area;
    }

    /**
    * Guardamos los ciclos escolares ligados a este grupo
    * @param array $ciclo_escolar
    * @param int $id_grupo
    *
    * @return $msg_no;
    */
    private static function ciclo_escolar_grupo($ciclo_escolar,$id_grupo){

        //Validamos que se nos envíe un arreglo y un grupo a asignar
        if(is_array($ciclo_escolar) && $id_grupo != NULL){
            
            //Pondremos con estatus de inactivo
            $updateData = array(
            'activo' => 0
            );
            self::getInstance()->where('id_grupo', $id_grupo);
            $results = self::getInstance()->update('grupo_ciclo_escolar', $updateData);

            //Mensaje de Respuesta
            $msg_no = 1;
    
            //Iniciamos transacción
            self::getInstance()->startTransaction();

            foreach($ciclo_escolar as $key => $value):

                //Complementamos arreglo para guardar el servicio
                $insertData['id_grupo'] = $id_grupo;
                $insertData['ciclo_escolar'] = $value;
                $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
                $insertData['fecha_creado'] = date('Y-m-d H:i:s');
                
                //Buscamos si ya está este registro pero como inactivo
                $sql = 'SELECT 
                        id, activo 
                        from `grupo_ciclo_escolar` 
                        where 1 
                        AND ciclo_escolar = ? 
                        AND id_grupo = ? AND activo = 0';

                $params = array($value,$id_grupo);        
                $ciclo_escolar = self::executar($sql,$params);
                $ciclo_escolar = $ciclo_escolar[0];

                    /*Previamente había un registro inactivo de este servicio, 
                    sólo será activado de nuevo, caso contrario, agregamos un nuevo
                    registro en la tabla*/
                    if($ciclo_escolar != NULL){
                        $msg_no = self::activaCicloEscolar($value,$id_grupo);
                    }else{                            

                            //Guardamos cada registro, en caso de haber error, cancelamos los registros
                            if(!self::getInstance()->insert('grupo_ciclo_escolar', $insertData)){
                                //No se pudo guardar uno de los servicios
                                $msg_no = 3;                                              
                            }
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
            }
        }

        return $msg_no;        

    }

    /**
    *Activamos o desactivamos un ciclo escolar del grupo
    *@param int $ciclo_escolar id del municipio
    *@param int $id_beneficiario id del grupo
    *
    *@return int $msg_no Mensaje
    **/
     public static function activaCicloEscolar($ciclo_escolar,$id_grupo,$id = NULL){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;


        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla grupo_ciclo_escolar
        $sql = 'SELECT id, activo from `grupo_ciclo_escolar` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

            $sql .= ' AND id = ?';

            //parámetros para la consulta
            $params = array($id);

        }else{

            $sql .= ' AND id_grupo = ? AND ciclo_escolar = ?'; 

            //parámetros para la consulta
            $params = array($id_grupo,$ciclo_escolar);

        }        

        //Obtenemos el registro
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;

        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;

        }

        //Obtenemos id del registro
        $id_grupo_ciclo_escolar = $registro['id'];

        //Preparamos update
        self::getInstance()->where('id',$id_grupo_ciclo_escolar);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('grupo_ciclo_escolar',$updateData)){

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
    * Obtenemos listado de municipios/áreas ligados al grupo 
    * En caso de NO poner el id_grupo, obtenemos los municipios 
    * del usuario logueado.
    * @param int $id_grupo id del grupo a buscar
    *
    * @return Array $grupo_mun_area Arreglo con listado de municipios
    **/
    public static function cicloEscolarGrupo($id_grupo = NULL){

        //Obtenemos listado de grupos a los que pertenece un usuario
        if($id_grupo == NULL){            

             //Cargamos modelo de usuario_grupo
            include_once($_SESSION['model_path'].'usuario_grupo.php');            

            //Obtenemos listado de grupos
            $grupos = UsuarioGrupo::listUsuarioGrupo($_SESSION['usr_id'],1);
            $grupo_usuario = array();

            foreach ($grupos as $val): 

                //Quitamos índice con el nombre de del municipio
                unset($val['nombre']);  

                foreach($val as $val2):                    
                    $grupo_usuario[] = $val2;
                 endforeach;
            endforeach;

            //creamos una cadena separada por comas
            $id_grupo = implode(",", $grupo_usuario);
            
        }

         //Buscamos si ya está este registro pero como inactivo
        $sql = 'SELECT
                ciclo_escolar
                from `grupo_ciclo_escolar` 
                where id_grupo IN (?) 
                AND activo = 1';

        $params = array($id_grupo);                 

        /*
        echo $sql;
        print_r($id_grupo);
        */
        
        //Obtenemos todos los municipios y áreas        
        $resultado = self::executar($sql,$params);

        $grupo_ciclo_escolar = array();

        if($resultado != NULL){            

            foreach ($resultado as $val):         
                foreach($val as $val2):
                 $grupo_ciclo_escolar[$val2] = $val2;
                 endforeach;
            endforeach;
        }
        
        return $grupo_ciclo_escolar;
    }

/**
    * Guardamos los años de padrón ligados a este grupo
    * @param array $axo
    * @param int $id_grupo
    *
    * @return $msg_no;
    */
    private static function axo_grupo($axo,$id_grupo){

        //Validamos que se nos envíe un arreglo y un grupo a asignar
        if(is_array($axo) && $id_grupo != NULL){
            
            //Pondremos con estatus de inactivo
            $updateData = array(
            'activo' => 0
            );
            self::getInstance()->where('id_grupo', $id_grupo);
            $results = self::getInstance()->update('grupo_axo', $updateData);

            //Mensaje de Respuesta
            $msg_no = 1;
    
            //Iniciamos transacción
            self::getInstance()->startTransaction();

            foreach($axo as $key => $value):

                //Complementamos arreglo para guardar el servicio
                $insertData['id_grupo'] = $id_grupo;
                $insertData['axo'] = $value;
                $insertData['id_usuario_creador'] = $_SESSION['usr_id'];
                $insertData['fecha_creado'] = date('Y-m-d H:i:s');
                
                //Buscamos si ya está este registro pero como inactivo
                $sql = 'SELECT 
                        id, activo 
                        from `grupo_axo` 
                        where 1 
                        AND axo = ? 
                        AND id_grupo = ? AND activo = 0';

                $params = array($value,$id_grupo);        
                $axo = self::executar($sql,$params);
                $axo = $axo[0];

                    /*Previamente había un registro inactivo de este servicio, 
                    sólo será activado de nuevo, caso contrario, agregamos un nuevo
                    registro en la tabla*/
                    if($axo != NULL){
                        $msg_no = self::activaAxo($value,$id_grupo);
                    }else{                            

                            //Guardamos cada registro, en caso de haber error, cancelamos los registros
                            if(!self::getInstance()->insert('grupo_axo', $insertData)){
                                //No se pudo guardar uno de los servicios
                                $msg_no = 3;                                              
                            }
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
            }
        }

        return $msg_no;        

    }

    /**
    *Activamos o desactivamos un año del grupo
    *@param int $axo id del municipio
    *@param int $id_beneficiario id del grupo
    *
    *@return int $msg_no Mensaje
    **/
     public static function activaAxo($axo,$id_grupo,$id = NULL){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;


        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla grupo_axo
        $sql = 'SELECT id, activo from `grupo_axo` where 1 ';

        //Dependiendo las variables recibidas, armamos la sentencia
        if($id != NULL){

            $sql .= ' AND id = ?';

            //parámetros para la consulta
            $params = array($id);

        }else{

            $sql .= ' AND id_grupo = ? AND axo = ?'; 

            //parámetros para la consulta
            $params = array($id_grupo,$axo);

        }        

        //Obtenemos el registro
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activará
        if($registro['activo'] == 0){
            $estatus = 1;

        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminará
            $estatus = 0;

        }

        //Obtenemos id del registro
        $id_grupo_axo = $registro['id'];

        //Preparamos update
        self::getInstance()->where('id',$id_grupo_axo);                                                

        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacción
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('grupo_axo',$updateData)){

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
    * Obtenemos listado de años ligados al grupo 
    * En caso de NO poner el id_grupo, obtenemos los municipios 
    * del usuario logueado.
    * @param int $id_grupo id del grupo a buscar
    *
    * @return Array $grupo_mun_area Arreglo con listado de municipios
    **/
    public static function axoGrupo($id_grupo = NULL){

        //Obtenemos listado de grupos a los que pertenece un usuario
        if($id_grupo == NULL){            

             //Cargamos modelo de usuario_grupo
            include_once($_SESSION['model_path'].'usuario_grupo.php');            

            //Obtenemos listado de grupos
            $grupos = UsuarioGrupo::listUsuarioGrupo($_SESSION['usr_id'],1);
            $grupo_usuario = array();

            foreach ($grupos as $val):

            //Quitamos índice con el nombre de del municipio
            unset($val['nombre']);     

                foreach($val as $val2):                    
                    $grupo_usuario[] = str_pad($val2,3,'0', STR_PAD_LEFT);
                 endforeach;
            endforeach;

            //creamos una cadena separada por comas
            $id_grupo = implode(",", $grupo_usuario);
            
        }

         //Buscamos si ya está este registro pero como inactivo
        $sql = 'SELECT
                axo
                from `grupo_axo` 
                where id_grupo IN (?) 
                AND activo = 1 ORDER BY axo DESC';

        $params = array($id_grupo);                 

        /*
        echo $sql;
        print_r($params);
        */
        
        //Obtenemos todos los municipios y áreas        
        $resultado = self::executar($sql,$params);

        $grupo_axo = array();

        if($resultado != NULL){            

            foreach ($resultado as $val):         
                foreach($val as $val2):
                 $grupo_axo[$val2] = $val2;
                 endforeach;
            endforeach;
        }
        
        return $grupo_axo;
    }

    /**
    * Obtenemos arreglo de municipios/areas listo para ser recorrido
    * en un select
    */
    public static function munAreaArreglo($id_grupo = NULL,$solo_mun = NULL){

        //Obtenemos municipios y areas
        $params  = self::munAreaGrupo($id_grupo,$solo_mun);

        //Inicializamos variable
        $mun_areas_disponibles = NULL;

        if($params != NULL){

            //Obtenemos los signos de interrogación necesarios para la consulta
            $lista = implode(',',array_fill(0, count($params), '?'));

            //Obtenemos municipios (predeterminado los de jalisco)
            $sql='SELECT CVE_ENT_MUN,CVE_MUN, NOM_MUN 
                    FROM cat_municipio 
                    WHERE CVE_ENT = 14 AND CVE_MUN IN ('.$lista.')';

            //Obtenemos arreglo con municipios y areas
            $mun_areas_disponibles = self::getInstance()->rawQuery($sql,$params);

        }        

        return $mun_areas_disponibles;
    }

    /**
     * Obtenemos el ID de determinado grupo por nombre, en caso de haber más de
     * un grupo con el mismo nombre, regresamos la primer coincidencia encontrada
     * @param  string $nombre [description]
     * @return [type]         [description]
     */
    public static function getId($nombre = ''){

        //Obtenemos el registro del caravana
        self::getInstance()->where('nombre',$nombre);
        $obj = self::getInstance()->getOne('grupo');
        $id = 0;
        if($obj != NULL){
            $id = $obj['id'];
        }

        return $id;
    }

}