<?php

    //Si tenemos listado
    if($lista){?>

    <p>   
    <?php // Listado de páginas del paginador
        echo $p->display();?>
    </p>

    <script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
    $("table").tablesorter({widgets: ['zebra']});
    });
    </script>

    <table class="tablesorter">
    <thead>
        <th>CURP</th>
        <th>Nombre</th>
        <th>Fecha de Nacimiento</th>
        <th>Municipio</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['curp']; ?></td>
            <td><?php echo $l['nombre_completo'];?></td>
            <td><?php echo $l['fecha_nacimiento']; ?></td>
            <td><?php echo $l['municipio']; ?></td>            
            <td>
                <div title="Agregar a Callcenter" class="ui-state-default ui-corner-all lista">
                    <a class="ui-icon ui-icon-circle-triangle-e" href="agregar_mujer_callcenter.php?id_mujeres_avanzando=<?php echo $l['id']; ?>"></a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    <?php }else{
        echo 'Beneficiaria NO encontrada';
        } ?>