<?php
include_once($_SESSION['model_path'].'c_mujeres_avanzando_detalle.php');
include_once($_SESSION['model_path'].'servicio.php');
include_once 'Carrito.php';

class Articulos extends Carrito{
    
    // Variables
    var $nombre;
    var $pts_otorgados;

    //Usaremos nuestra función de agregar (Sobreescribiremos de la clase Carrito)
    function agregar($articulo_id,$id_mujeres_avanzando = NULL,$cantidad = 1)
    {
        $LIMITE = 50;
        $mensaje = "";
        $pos = NULL;
        
        //Predeterminadamente se agrega 1 elemento
        if($cantidad <= 0){
            
            $cantidad = 1;
            
        }
                /* 
                echo "Articulo_id : ".$articulo_id;                
                echo "<br>Arreglo Carrito ";
                print_r($this->articulo_id);
                echo "<br>";
                */
                
                //Si existe el carrito, buscamos duplicados en él
                if($this->articulo_id){
                    
                    /*Posición que ocupa (en caso de ser duplicado) en el carrito.
                    la función array_search devuelve la posición en el arreglo en
                    caso de encontrarlo; caso contrario, devuelve 'false'
                    */
                    $pos = array_search($articulo_id,$this->articulo_id);                                                                                   
                    //echo "Pos carrito: ".$pos;   
                }
                
                //No se encontraron duplicados en el carrito, buscamos en tabla
                if($pos === false){
                        
                        //echo "Beneficiario: ".$id_mujeres_avanzando;
                        
                        //Obtenemos arreglo con los servicios ya asignados al beneficiario
                        $mujeres_det = mujeresAvanzandoDetalle::listaArrServMujer($id_mujeres_avanzando);                        
                        
                        //de haber servicios ligados al beneficiario, comprobamos que no se duplique
                        if($mujeres_det != NULL){                                                        
                            
                            /* echo "<br>Arreglo en tabla del beneficiario $id_mujeres_avanzando ";
                            print_r($mujeres_det);
                            echo "<br>";*/
                    
                            //Buscamos en la tabla si ya tiene dicho artículo                
                            $pos = array_search($articulo_id,$mujeres_det);                  
                    
                            //echo "Pos:".$pos;
                        }                                             
                }
                
                            
                //Hay duplicado, mostramos mensaje
                if(is_int($pos)){
                    $mensaje = "Servicio ya agregado o asignado al beneficiario previamente";                        
                
                }else{
                        //echo "Se Agrega";
                        //Verificamos límite
                        //if ( count($this->articulo_id) <= $LIMITE ){						                                                         
                        
                      	//$articulo = servicio::get_by_id($articulo_id);
                        
                        $articulo = Servicio::get_by_id($articulo_id);
                        
                        if($articulo != NULL){

                        $this->articulo_id[] = $articulo_id;
                        $this->cantidad[] = $cantidad;
                        $this->nombre[] = $articulo['ID_C_SERVICIO'].' - '.$articulo['servicio'];
                        $this->pts_otorgados[] = $articulo['PUNTOS_OTORGA'];

                        }else{
                            $mensaje = "Servicio Inexistente";
                        }                        
                    	
                       /*} else {
                       
                        $mensaje = "Solo se pueden solicitar " . $LIMITE . " artículos";
                       
                       }*/                
                }     
                         
        return $mensaje;
        
    }
    
} 