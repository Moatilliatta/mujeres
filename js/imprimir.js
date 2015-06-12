jQuery(document).ready(function ($) {     

//Objeto
obj = window.matchMedia;

//Detectar impresión en Chrome
    if (obj) {
        var mediaQueryList = obj('print');
        var contador = 0;
        mediaQueryList.addListener(function(mql) {

            //Por alguna razón, chrome simula la impresión 4 veces, usaremos contador
            contador++;

            if (mql.matches) {
                beforePrint();
            } else if( (contador%4) == 0) {
                afterPrint();
            
            }            
        });           

        //Detectar impresión en Firefox,IE
        window.onbeforeprint = beforePrint;
        window.onafterprint = afterPrint;
    }else{
        alert('No existe');
    }

    function beforePrint(){

    }

    //Guardamos en el log el intento de impresión
    function afterPrint(){
        
        var ruta = '../../inc/mujer/';
        var accion = 'guarda_impr';

        //Por cada folio, ingresamos registro en log
        for (f in folios) {
            $.post( ruta+accion+".php", { folio: folios[f]} );
        }                                
        
    }
});