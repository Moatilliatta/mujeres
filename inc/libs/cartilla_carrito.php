<?php
session_start();
include_once($_SESSION['model_path'].'mujeres_avanzando.php');
class Cartilla{    
    // Variables
    var $articulo_id;
    /*
    var $nombres;
    var $edad;
    var $colonia;
    var $domicilio;
    var $codigo;
    var $municipio;
    var $calle;
    var $num_ext;
    var $num_int;
    var $telefono;
   */
    
    //Agrega un involucrado al "carrito".
    function agregar($articulo_id,$id_beneficiario = NULL)
    {
        $LIMITE = 4;
        $mensaje = "";
        $pos = NULL;
        
        

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
                
                
               
                                            
                //Hay duplicado, mostramos mensaje
                if(is_int($pos)){
                    
                    //Beneficiaria ya agregada previamente
                    $mensaje = 29;                        
                
                }else{
                        //echo "Se Agrega";
                        //Verificamos límite
                         
                         
                         
                        if ( count($this->articulo_id) < $LIMITE ){						             

                        
                        
                        $mujer = mujeresAvanzando::get_by_id(null,$articulo_id);
                          
                         if($mujer != NULL){
  
                          $this->articulo_id[] = $articulo_id; 
                          /*
                          $this->nombres[] = $mujer['nombres'];
                          $this->edad[] = $mujer['edad'];
                          $this->colonia[]=$mujer['colonia'];
                          $this->calle[]=$mujer['calle'];
                          $this->num_ext[]='# '.$mujer['num_ext'];
                          $this->num_int[]='# '.$mujer['num_int'];
                          $this->codigo[]=$mujer['CODIGO'];
                          $this->municipio[]=$mujer['NOM_MUN'];
                          $this->telefono[]=$mujer['telefono'];
                          */

                }
                                              

                       } else {
                       
                        $mensaje = "Sólo se puede seleccionar hasta ". $LIMITE ." beneficiarias por impresi&oacute;n";
                       }
                }     
                         
        return $mensaje;
        
    }

    //Elimina un involucrado
    function dilete($linea)
    {
        unset($this->articulo_id[$linea]);
        unset($this->nombres[$linea]);
    }
    
} 

