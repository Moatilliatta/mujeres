<?php
session_start();//Habilitamos uso de variables de sesión

//Incluimos cabecera
include_once('../../inc/header.php');    

//Incluimos modelo 'Usuario'
include_once($_SESSION['model_path'].'bloqueo.php');

//Valores de la búsqueda
$tipo_filtro = $_GET['tipo_filtro'];
$busqueda = $_GET['busqueda'];
$respuesta=$_GET['r'];

//Obtenemos listado paginado de bloqueo
list($lista,$p) = Bloqueo::listaBloqueos($busqueda,$tipo_filtro);
if($respuesta !=null){
    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);
}
if($lista === NULL){
    //No existen registros
    list($mensaje,$clase) = Permiso::mensajeRespuesta(8);
}?>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
$("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
    <div id="contenido">
       
        <div class="centro">
        <div  align="center">
        <form id='formbusqueda' method="get" action='lista_bloqueo.php'>        
        <table>
        <tr>
         <td>
            <label for="tipo_filtro">Buscar Por:</label>
         </td>
            <td>
                <select id="tipo_filtro" name="tipo_filtro">
                    <option value="nombre">Nombre</option> 
                    <option value="descripcion">Descripci&oacute;n</option>                                        
                </select>
            </td>
            <td>
                <label for="busqueda"> Palabra Clave</label>
            </td>
            <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
            <td><input type="submit" id="boton"  value="Buscar" /></td></td>
        </tr>
        </table>
        </form>
        </div>    	
    </div>

    <h2 class="centro">Listado de bloqueos</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
            
     <?php } ?>
    
    <div id="page_list" align="center">        
        <p>
          <a href="alta_bloqueo.php" class="btn">Agregar</a>
        </p> 
        <p>
        <?php    
        //Si tenemos listado    
        if($lista != NULL){                    
            // Listado de páginas del paginador    
            echo $p->display();    
        ?>
        </p>

        <table class="tablesorter">
            <thead>
                <th>Usuario</th>
                <th>Grupo</th>
                <th>M&oacute;dulo</th>
                <th>Subm&oacute;dulo</th>
                <th>Acci&oacute;n</th>
                <th>Aplicado por</th>
                <th>Fecha Creado</th>
                <th>Fecha Modif.</th>
                <th>Acci&oacute;n</th>                 
            </thead>
            <tbody>

             <?php foreach($lista as $l){ ?>
            <tr>
                <td><?php echo $l['nombre_usuario'];?></td>
                <td><?php echo $l['nombre_grupo'];?></td>
                <td><?php echo $l['nombre_modulo'];?></td>
                <td><?php echo $l['nombre_submodulo'];?></td>
                <td><?php echo $l['nombre_accion'];?></td>
                <td><?php echo $l['usuario'];?></td>
                <td><?php echo $l['fecha_creado'];?></td>
                <td><?php echo $l['fecha_mod'];?></td>                
                <td>
                    <div title="Editar" class="ui-state-default ui-corner-all lista">
                        <a class="ui-icon ui-icon-note" href="edita_bloqueo.php?id_edicion=<?php echo $l['id']; ?>">Editar</a>
                    </div>                    
                </td>
            </tr>
            <?php } ?>      
            </tbody>
    </table>        
    <?php } ?>
    </div>
   </div> 
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>