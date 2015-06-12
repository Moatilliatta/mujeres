<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Modulo
 * **/ 
//Inclumos librería MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class HistoricoGIA extends MysqliDb{

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
     * Guardamos registro en la tabla Modulo
     * @param array $Modulo Arreglo con los campos a guardar
     * @param int $id del Modulo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */

    public static function saveHistoricoGIA($historico_gia,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;        

        /*Obtenemos cada una de las variables enviadas vía POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($historico_gia as $key => $value):

        ${$key} = self::getInstance()->real_escape_string($value);

        endforeach;

        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];
            
        //Si no esta creada la variable activo predeterminadamente la guardamos = 1
        if(!isset($activo) ){
            $activo = 1 ;
        }        
            
        //Campos obligatorios
        if ($id_mujeres_avanzando && $folio && $id_grado && $nivel && $calidad_dieta &&
            $diversidad && $variedad && $elcsa){

            $insertData = array(
                'id_mujeres_avanzando' => $id_mujeres_avanzando,
                'folio' => $folio,
                'visita' => $visita,
                'id_caravana' => $id_caravana,
                'id_grado' => $id_grado,
                'nivel' => $nivel,
                'calidad_dieta' => $calidad_dieta,
                'diversidad' => $diversidad,
                'variedad' => $variedad,
                'elcsa'=> $elcsa,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
                );

            //Iniciamos transacción
            self::getInstance()->startTransaction();
                
            if(! self::getInstance()->insert('historico_gia', $insertData)){
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
     * Obtenemos el total de visitas que se le han realizado
     * a cierta caravana
     * @param  [type] $id_caravana [description]
     * @return [type]              [description]
     */
    public static function totalVisitas($id_caravana = NULL){
        $sql = 'SELECT 
                visita 
                FROM `historico_gia` 
                WHERE id_caravana = ?
                GROUP BY 1';

        $params = array($id_caravana);

        //Inicializamos cadena
        $visitas = array();

        //Ejecutamos sentencia
        $lista = self::executar($sql,$params);

        //Llenamos listado
        foreach ($lista as $key => $value):
            $visitas[] = $value['visita'];
        endforeach;

        return $visitas;
    }

    /**
     * Obtenemos un listado separado por comas (para usar en consulta SQL)
     * de las mujeres que tuvieron algún cambio en sus grados agrupadas por
     * su caravana
     * @param  [type] $id_caravana [description]
     * @return [type]              [description]
     */
    public static function beneficiariasCambio($id_caravana = NULL){
        $sql = 'SELECT 
                DISTINCT(h.id_mujeres_avanzando) as id_mujeres_avanzando
                from historico_gia h
                where h.id_caravana = ?
                ORDER BY h.id_mujeres_avanzando ';

        $params = array($id_caravana);

        //Inicializamos cadena
        $listado = '';

        //Ejecutamos sentencia
        $lista = self::executar($sql,$params);

        //Llenamos listado
        foreach ($lista as $key => $value):
            $listado .= $value['id_mujeres_avanzando'].',';
        endforeach;

        //Quitamos última coma
        $listado = substr($listado, 0,-1);

        return $listado;
    }

    /**
     * Obtenemos listado de beneficiarias que tuvieron algún cambio en alguno
     * de sus grados GIA acorde a la tabla historico_gia
     * @param  int $id_caravana ID de la tabla caravana
     * @param  int $visita      No. de visita
     * @return [type]              [description]
     */
    public static function listaBenGIA($id_caravana = NULL,$visita = NULL){
                
        $sql = 'SELECT 
                m.id as id_mujeres_avanzando,
                h.fecha_creado,
                h.folio,
                g.grado as grado,
                n.nivel as nivel,
                cd.calidad_dieta as calidad_dieta,
                d.diversidad as diversidad,
                v.variedad as variedad,
                e.elcsa as elcsa 
                FROM `historico_gia` h
                LEFT JOIN mujeres_avanzando m on h.id_mujeres_avanzando = m.id
                LEFT JOIN caravana c on c.id = h.id_caravana
                LEFT JOIN grado g on h.id_grado = g.id
                LEFT JOIN nivel_socioeconomico n on h.nivel = n.id
                LEFT JOIN calidad_dieta cd on h.calidad_dieta = cd.id
                LEFT JOIN diversidad d on h.diversidad = d.id
                LEFT JOIN variedad v on h.variedad = v.id
                LEFT JOIN elcsa e on h.elcsa = e.id
                WHERE m.id_caravana = ?
                and h.visita = ?
                GROUP BY h.id
                ORDER BY m.id ';

                $params = array($id_caravana,$visita);


                 $l = self::executar($sql,$params);

                  foreach ($l as $key => $value) {
                   $listado[$value['id_mujeres_avanzando']]=$value;
                  }

                //return Paginador::paginar($sql,$params);
                return $listado;
    }
}
?>