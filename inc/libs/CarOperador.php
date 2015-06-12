<?php
include_once($_SESSION['model_path'].'usuario.php');
include_once($_SESSION['model_path'].'callcenter_grupo_operador.php');

include_once 'Carrito.php';

class CarOperador extends Carrito{
    
    // Variables
    var $nombre;    
    var $id_callcenter_grupo;

    //Usaremos nuestra función de agregar (Sobreescribiremos de la clase Carrito)
    function agregar($articulo_id,$id_callcenter_grupo,$cantidad = 1)
    {
        $LIMITE = 50;
        $mensaje = "";
        $pos = '';
        
        //Guardamos id_callcenter_grupo
        $this->id_callcenter_grupo = $id_callcenter_grupo;

        //Predeterminadamente se agrega 1 elemento
        $cantidad = ($cantidad <= 0)? 1 : $cantidad;

        //Verificamos que no esté duplicado en la tabla
        if($articulo_id){

        //Obtenemos arreglo para verificar si el usuario tiene grupos
        //de la tabla intermediaria
        $grupos_operador = CallcenterGrupoOperador::buscaOperador($articulo_id,
                                                                  $this->id_callcenter_grupo,1);

        //De haber registros ligados al beneficiario, comprobamos que no se duplique
        if(is_array($grupos_operador)){

            //Buscamos en el arreglo si ya tiene dicho artículo
            $pos = array_search($articulo_id,$grupos_operador);

           }    

        }

        //Si existe el carrito, buscamos duplicados en él
        if($this->articulo_id && strlen($pos) == 0){

        /*Posición que ocupa (en caso de ser duplicado) en el carrito.
        la función array_search devuelve la posición en el arreglo en
        caso de encontrarlo; caso contrario, devuelve 'false'*/
        $pos = array_search($articulo_id,$this->articulo_id);

        //echo "Pos carrito: ".$pos;
        }                        

        //echo 'Posición: '.strlen($pos);

        //En caso de no ser duplicado, guardamos en arreglo
        if(strlen($pos) == 0){

            //Verificamos límite

          //if ( count($this->articulo_id) <= $LIMITE ){

                //Verificamos que no esté previamente el registro
                //guardado en su respectiva tabla
                
                $registro = CallcenterGrupoOperador::buscaOperador($articulo_id,
                                                                   $this->id_callcenter_grupo,
                                                                   1);

                if($registro == NULL){

                    $usuario = Usuario::get_by_id($articulo_id);

                    if($usuario != NULL){

                        $this->articulo_id[] = $articulo_id;
                        $this->nombre[] = $usuario['nombres'].' '.
                                      $usuario['paterno'].' '.
                                      $usuario['materno'];

                    }else{
                        $mensaje = "Usuario NO existente o NO activo!";    
                    }                    

                }else{
                    $mensaje = "Usuario previamente agregado al grupo";
                }
                    	
            /*} else {
                       
                $mensaje = "Solo se pueden solicitar " . $LIMITE . " artículos";
                       
            }*/                

        }else{
            //Hay duplicado, mostramos mensaje
            $mensaje = "Usuario ya agregado o asignado al grupo previamente ";
        }     
                         
        return $mensaje;
        
    }
    
} 