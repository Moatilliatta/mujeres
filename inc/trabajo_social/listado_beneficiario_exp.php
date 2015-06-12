<?php 
    //Si tenemos listado
    if($lista != NULL){
    // Listado de páginas del paginador
    //echo $p->display();
        ?>
    <div id="page_list" align="center">        
    <p>
        <label>Beneficiario(s) ligados en expediente</label> 
    </p>
    
    <script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
        $("table").tablesorter({widgets: ['zebra']});
    });
    </script>
    
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Fecha Creado</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombres'];?></td>
            <td><?php echo $l['paterno'];?></td>
            <td><?php echo $l['materno'];?></td>
            <td><?php echo $l['fecha_creado'];?></td>
            <td>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> beneficiario?" 
                       href="activa_beneficiario_exp.php?id_activo=<?php echo $l['id']; ?>&id_trab_expediente=<?php echo $l['id_trab_expediente']?>"></a>
                </div> 
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    </div>
    <?php }else{ ?>
       <div class="mensaje">
           No hay beneficiarios ligados a este expediente.
       </div>
    <?php } ?>