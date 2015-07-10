
<h3 class="centro"> Seguimiento De Capacitaciones En Punto Rosa </h3>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>fecha.js"></script>
    <script type="text/javascript">
    $(function() {
    $("table").tablesorter({widgets: ['zebra']});
    });
    </script>
<form id='formcapacitacion' method="post" action='save_punto_rosa_capacitacion.php'>    
<table class="tablesorter">
    <thead>
        <th>N&uacute;mero</th>
        <th>Capacitaci&oacute;n</th>
        <th>Fecha Capacitaci&oacute;n</th>
        <th>Asistio</th>
    </thead>

    <tbody>
        <?php
        //$lista es seg_punto_ros_capacitacion
        //$mujer_capacitacion seg_capacitacion_mujer  
        foreach($lista as $key => $l): ?>
        <tr>
            <td><?php echo $l['id'];?></td>
            <td><?php echo $l['capacitacion']; ?></td>
            <td>
            <input type="hidden" id="id_edicion_" name="id_edicion" value="<?php echo $id_edicion; ?>" />
            <input type="hidden" name="id_mujer_avanzando" value="<?php echo $id_mujeres_avanzando; ?>" />  
            <input type = 'text' id = 'fecha_capacitacion<?php echo $l['id']; ?>' class="fecha date" name = 'fecha_capacitacion[]'value="<?php echo $l['fecha_capacitacion']?>" />
            <input type="button"  value="Hoy" id="<?php echo $l['id']; ?>" class="today"/>
            </td>
            <td>
              <div>                    
                   
                <input id="id_capacitacion" 
                <?php if($mujer_capacitacion != NULL) echo (in_array($l['id'],$mujer_capacitacion) === true )? 'checked': ''; ?> class='componente' value="<?php echo $l['id_capacitacion']?>" name="id_seg_capacitacion[]" type="checkbox"/>                 
                  
                </div>
            </td>
        </tr>

        <?php endforeach; ?> 
        
             
       
    </tbody>
    
    </table>
    <div>
			<td>&nbsp;</td>
			<td><input type = 'submit'  id = 'enviar'value = 'Enviar' /></td>
</div>		
</form>    