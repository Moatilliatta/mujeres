
<?php 
    //Listado con jquery ui
    //Obtenemos el nombre del módulo actual y el id del usuario logueado
    $nombre_modulo = $_SESSION['module_name'];
    $id_usuario = $_SESSION['usr_id'];

    //Obtenemos listado de módulos que tiene el usuario
    $modulos = Permiso::getModulos($id_usuario);

?>
<link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'menu2.css' ?>" type="text/css"/>
<script type="text/javascript" src="<?php echo $_SESSION['js_path']?>/jquery.ui.core.js" ></script>
<script type="text/javascript" src="<?php echo $_SESSION['js_path']?>/jquery.ui.widget.js" ></script>
<script type="text/javascript" src="<?php echo $_SESSION['js_path']?>/jquery.ui.position.js" ></script>
<script type="text/javascript" src="<?php echo $_SESSION['js_path']?>/jquery.ui.menu.js" ></script>   

<div class="bloque_menu">

<ul id="menu">
<?php 
    //Recorremos módulos
     foreach ($modulos as $m):?>
    <li><a href="<?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].'/ini/index.php'?>
                    "><?php echo $m['descripcion_modulo'];?></a>
    <?php 
    //Obtenemos listado de submódulos que tiene el usuario
    $submodulos = Permiso::getSubmodulos($m['nombre_modulo'],$id_usuario);
    $tot_submodulos = count($submodulos);
    $tiene_acciones = null;

    //Verificamos si tiene submodulos
    if($submodulos !=  NULL){

        //Verificamos si tiene acciones
        $tiene_acciones = Permiso::listaMenuAcciones('HEADER',
                                                    null,
                                                    $submodulos[0]['id_submodulo']);

    }
    
    if($submodulos != NULL && $tiene_acciones != NULL){

    ?>
    <ul id="primer_nivel">
    <?php //Mostramos el listado de submódulos que tiene acceso
        foreach($submodulos as $s): 

            //Obtenemos listado de acciones que tiene el usuario
            //$acciones = Permiso::getAcciones($s['id_submodulo'],$id_usuario,false);
            $acciones = Permiso::listaMenuAcciones('HEADER',null,$s['id_submodulo']);

            //Armamos el enlace del submódulo
            $enlace = $_SESSION['app_path_p'].$m['nombre_modulo'].
                                    '/'.$s['nombre_submodulo'].
                                    '/';

            //Ponemos el listado como la acción predeterminada, en caso de no existir
            //listados la primer acción del submódulo como enlace inmediato
            if($acciones){  
                
                $accion = NULL;
                $needle='lista';

                foreach ($acciones as $key => $value):                
                    $haystack = $value['nombre_accion'];
                    $r = strpos($haystack,$needle);

                    if($r !==false){
                       $accion = $value['nombre_accion'].'.php';
                        break;                    
                    }

                endforeach;                        

                $enlace .= ($accion != NULL)? $accion : $acciones[0]['nombre_accion'].'.php';
            } 

            ?>
            
            <?php 
            //Imprimimos los módulos, si son más de 1
            if($tot_submodulos > 1){ ?>
                <li>
                    <a href="<?php echo $enlace ?>"><?php echo $s['descripcion_submodulo']; ?></a>
                    <ul>

                    <?php } ?>    

                    <?php                                    
                    
                    if($acciones){ 
                                //print_r($acciones);
                                //Mostramos listado de acciones
                                foreach($acciones as $a):?>
                                
                                <li>
                                    <a href="
                                    <?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].
                                    '/'.$s['nombre_submodulo'].
                                    '/'.$a['nombre_accion'].'.php' ?>" > 

                                        <?php echo $a['descripcion_accion'] ?>
                                    </a>               
                                </li>                        
                                <?php endforeach; ?>
                    <?php } ?>

                <?php 
                //Imprimimos los módulos, si son más de 1
                if($tot_submodulos > 1){ ?>

                    </ul>
                </li>

                <?php } ?>

    <?php endforeach; ?>

        </ul> 

        <?php } ?>

    </li>
<?php endforeach;?>
</ul>

</div>

 <script type="text/javascript">
 $(function() {
    var menu = "#menu";
    var position = {my: "left top", at: "left bottom"};
    
    $(menu).menu({
        
        position: position,
        blur: function() {
            $(this).menu("option", "position", position);
            },
        focus: function(e, ui) {
            
            if ($(menu).get(0) !== $(ui).get(0).item.parent().get(0)) {
                $(this).menu("option", "position", {my: "left top", at: "right top"});
                }
        }
    });
 });
 </script>
