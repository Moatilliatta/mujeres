<?php
abstract class Carrito{    
    
    // Variables
    var $articulo_id;
    var $cantidad;
    
    /**
     * Definimos método de agregar artículo
     * @param  [type] $articulo_id     ID del artículo
     * @param  [type] $id_beneficiario ID del beneficiario o campo pivote
     * @param  [type] $cantidad        cantidad de artículos
     * @return [type]                  [description]
     */
    abstract protected function agregar($articulo_id,$id_campo_pivote,$cantidad);

    /**
     * Elimina un artículo del carrito
     * @param  [type] $linea [description]
     * @return [type]        [description]
     */
    public function dilete($linea)
    {
        unset($this->articulo_id[$linea]);
        unset($this->cantidad[$linea]);
    }
    
} 