<?php if ($lista_familiar !=null){ ?>
            <h3 class="centro"> Coincidencias en Integrantes de Hogar: <?php echo count($lista_familiar);?></h3> 
            <?php }?>
<?php

    //Si tenemos listado
   // print_r($lista_familiar);
   // exit; 
    
    if(count($lista_familiar) > 0){?>

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

    <table class="tablesorter color_alt">
    <thead>
      <tr>
        <th colspan="7">
          INTEGRANTES
        </th>
      </tr>
       <tr> 
        <th>Folio</th>
        <th>Nombres</th>
        <th>Estado</th>
        <th>Municipio</th>
       <th>Fecha de Nacimiento</th>
        <th>Caravana</th>
        <th>Acci&oacute;n</th>
       </tr>
    </thead>

    <tbody>
        <?php foreach($lista_familiar as $l): ?>
        <tr>
            <td><?php echo $l['folio']; ?></td>
            <td><?php echo $l['nombre_completo']; ?></td>
            <td><?php echo $l['estado_residencia']; ?></td>
            <td><?php echo $l['nombre_municipio'];?></td>
            <td><?php echo $l['fecha_nacimiento'];?></td>
            <td><?php echo $l['nom_caravana'];?></td>
            <td>
               <?php if(array_key_exists('alta_fam_cred',$central)){ ?> 
               <?php if($l['cartilla'] == null){ ?>                  
                <div title="Agregar Familiar a Cartilla" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-circle-triangle-e confirmation" 
                  title="&iquest;Est&aacute;s seguro de dar de alta a &eacute;ste integrante?"
                  href="alta_fam_cred.php?id_familiar=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php } ?>  
                 <?php } ?>    
            </td>
        </tr>

        <?php endforeach; ?>      
       
    </tbody>
    
    </table>        
    <?php } ?>