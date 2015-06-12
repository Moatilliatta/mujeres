<?php
include_once($_SESSION['model_path'].'beneficiario.php');
include_once($_SESSION['model_path'].'trab_exp_beneficiario.php');
include_once 'Carrito.php';

class CarBeneficiario extends Carrito{
    
    // Variables
    var $nombre;    
    var $trab_expediente;

    //Usaremos nuestra función de agregar (Sobreescribiremos de la clase Carrito)
    function agregar($articulo_id,$id_beneficiario_pivote = NULL,
        $id_trab_expediente = NULL,$cantidad = 1)
    {
        $LIMITE = 50;
        $mensaje = "";
        $pos = '';
        
        //Predeterminadamente se agrega 1 elemento
        $cantidad = ($cantidad <= 0)? 1 : $cantidad;

        //Verificamos que no esté duplicado en la tabla
        if($articulo_id){

        //Obtenemos arreglo para verificar si el beneficiario ya está en el expediente
           $trab_exp_beneficiario = Trab_exp_beneficiario::expedientesLigados($articulo_id,
                                                                              $id_trab_expediente,
                                                                              $id_beneficiario_pivote);
        //de haber expedientes ligados al beneficiario, comprobamos que no se duplique
           if($trab_exp_beneficiario != NULL){

            //Buscamos en la tabla si ya tiene dicho artículo
            $pos = array_search($articulo_id,$trab_exp_beneficiario);

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

                $beneficiario = Beneficiario::get_by_id($articulo_id);

                if($beneficiario != NULL){

                        $this->articulo_id[] = $articulo_id;
                        $this->cantidad[] = $cantidad;
                        $this->nombre[] = $beneficiario['nombres'].' '.
                                          $beneficiario['paterno'].' '.
                                          $beneficiario['materno'];
                        $this->trab_expediente[] = $id_trab_expediente;

                }else{
                    $mensaje = "Beneficiario NO existente!";
                }                        
                    	
                       /*} else {
                       
                        $mensaje = "Solo se pueden solicitar " . $LIMITE . " artículos";
                       
                       }*/                
        }else{
            //Hay duplicado, mostramos mensaje
            $mensaje = "Beneficiario ya agregado o asignado al expediente previamente ";
        }     
                         
        return $mensaje;
        
    }
    
} 