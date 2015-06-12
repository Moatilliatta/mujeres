<?php
class Carrito{    
    // Variables
    var $articulo_id;
    var $cantidad;
    
    //Agrega un involucrado al "carrito".
    function agregar($articulo_id,$id_beneficiario = NULL,$cantidad = 1)
    {
        $LIMITE = 50;
        $mensaje = "";
        $pos = NULL;
        
        //Predeterminadamente se agrega 1 elemento
        if($cantidad <= 0){ $cantidad = 1; }

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
                        
                //Obtenemos arreglo con los servicios ya asignados al beneficiario
                $beneficiario_serv = Beneficiario_pys::listaArrPysBeneficiario($id_beneficiario);    

                //de haber servicios ligados al beneficiario, comprobamos que no se duplique
                    if($beneficiario_serv != NULL){  
                            
                            /* echo "<br>Arreglo en tabla del beneficiario $id_beneficiario ";
                            print_r($beneficiario_serv);
                            echo "<br>";*/
                    
                            //Buscamos en la tabla si ya tiene dicho artículo                
                            $pos = array_search($articulo_id,$beneficiario_serv);                  
                    
                            //echo "Pos:".$pos;
                    }                                             
                }
                                            
                //Hay duplicado, mostramos mensaje
                if(is_int($pos)){
                
                    $mensaje = "Artículo ya agregado previamente";                        
                
                }else{
                        //echo "Se Agrega";
                        //Verificamos límite
                        if ( count($this->articulo_id) <= $LIMITE ){						             

                        $this->articulo_id[] = $articulo_id;
                        $this->cantidad[] = $cantidad;                        

                       } else {
                       
                        $mensaje = "Solo se pueden solicitar " . $LIMITE . " artículos";
                       
                       }
                }     
                         
        return $mensaje;
        
    }

    //Elimina un involucrado
    function dilete($linea)
    {
        unset($this->articulo_id[$linea]);
        unset($this->cantidad[$linea]);
    }
    
} 