<?php 
    //Si tenemos listado
    if($lista != NULL){
    // Listado de páginas del paginador
    //echo $p->display();
        ?>

    <script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
        $("table").tablesorter({widgets: ['zebra']});
    });
    </script>    
    
    <div id="page_list" align="center">        
    <p>
        <label>Beneficiario en expediente</label> 
    </p>
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Estado</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <!-- <th>Seleccionar</th> -->
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombres'];?></td>
            <td><?php echo $l['paterno'];?></td>
            <td><?php echo $l['materno'];?></td>
            <td><?php echo $l['estado'];?></td>
            <td><?php echo $l['municipio'];?></td>
            <td><?php echo $l['localidad'];?></td>
            <!--
            <td>
             <?php// if($l['activo']==1){ ?>
                <?php //if(array_key_exists('edita_trabajo_social',$central)){ ?>
            
            <!--
                <div title="Editar Trabajo Social" class="ui-state-default ui-corner-all lista">                
                    <a class="ui-icon ui-icon-note" href="edita_beneficiario.php?id_edicion=<?php //echo $l['id'];?>"></a>
                </div>
                -->                            
                <?php //} ?>
             <?php // }?> 
            <!-- </td>
            -->
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    </div>
    <?php } ?>