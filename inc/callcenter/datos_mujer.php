<?php 

    if($id_mujeres_avanzando > 0){
        //Obtenemos información de la beneficiaria
        $mujer = mujeresAvanzando::get_by_id($id_mujeres_avanzando);
    }
    
?>
<table style="font-size:14pt;" class="tablesorter">             
    <thead> 
        <tr>
            <th>Beneficiaria</th>    
        </tr> 
    </thead>
    <tbody>
        <tr class="zebra"> 
            <td>
                <?php $calle = $mujer['calle'].' No. '.$mujer['num_ext']; ?>
                <?php echo '<B>NOMBRE:</B> '.$mujer['nombre_completo'];?>
                <?php echo '<br><B>FECHA DE NACIMIENTO:</B> '.Fechas::fechacorta('/',Fechas::fechaymdAdmy($mujer['fecha_nacimiento'])).' ('. $mujer['edad'].' a&ntilde;os)';?>
                <?php //echo '<B>DOMICILIO:</B> '.$mujer['calle'].' '.$mujer['num_ext'].' '.'INTERIOR '.$mujer['num_int'].'<br>';?>
                <?php echo '<br><B>DOMICILIO:</B> '.$calle .= (isset($mujer['num_int']) && (strtoupper($mujer['num_int']) != 'S/N') )? ' INTERIOR '.$mujer['num_int'] : '';?>
                <?php echo '<br><B>COLONIA:</B> '.$mujer['colonia'];?>
                <?php echo '<br><B>MUNICIPIO:</B> '.$mujer['NOM_MUN'];?>
                <?php echo '<br><B>C&Oacute;DIGO POSTAL:</B> '.$mujer['CODIGO'];?>
                <?php echo '<br><B>TELEFONO:</B> '.$mujer['telefono'].'<BR>';?>                
            </td>     
        </tr>
    </tbody>
</table>