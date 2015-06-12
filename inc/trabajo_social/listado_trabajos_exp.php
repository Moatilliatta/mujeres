<?php 
    //Si tenemos listado
    if($lista != NULL){
    // Listado de páginas del paginador
    //echo $p->display();
        ?>
    <div id="page_list" align="center">        
    <p>
        <label>Historial de Trabajos</label> 
    </p>

    <script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
        $("table").tablesorter({widgets: ['zebra']});
    });
    </script>

    <table class="tablesorter">
    <thead>
        <th>Programa</th>
        <th>Justificaci&oacute;n</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Estatus</th>        
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['programa'];?></td>
            <td><?php echo $l['justificacion'];?></td>
            <td><?php echo $l['usuario'];?></td>
            <td><?php echo $l['fecha_ultima_mod'];?></td>
            <td><?php echo $l['estatus'];?></td>            
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    </div>
    <?php } ?>