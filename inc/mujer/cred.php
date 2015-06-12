<?php
session_start();
 
?>

<style type="text/css">
.foto_<?php echo ($key + 1) ?> {
background: url(<?php echo $_SESSION['img_path'];?>/mujeres/<?php echo $m['folio_compuesto']?>.png) top center no-repeat;
background-size: auto 100%;
}
</style>

		<div class="recorte">
			<div class="cartilla">
				<div class="foto foto_<?php echo ($key + 1) ?>"></div>
				<div class="car car_1 car_folio">
                    <?php echo $m['folio_compuesto']?>
					&nbsp;
				</div>
				<div class="car car_1 car_tel">
					<?php echo $m['telefono']?>
				</div>
				<div class="car car_2 car_nombre">
					<?php echo $m['nombres']?>
				</div>    	
				<div class="car car_2 car_apellido">
					<?php echo $m['paterno'].' '.$m['materno']  ?>
				</div>    	
				<div class="car car_3 car_edad">
					<?php echo $m['edad'] ?>
				</div>    	
				<div class="car car_4 car_grado">
					<?php echo $m['grado'] ?>
				</div>    	
				<div class="car car_2 car_nombre car_dom">
					<?php 
					$calle = $m['calle'].' No. '.$m['num_ext'];
					$calle .= (isset($m['num_int'])  && (strtoupper($m['num_int']) != 'S/N') )? ' INTERIOR '.$m['num_int'] : '';
					echo $calle;
					?>
				</div>    	
				<div class="car car_2 car_nombre car_col">
					<?php echo $m['colonia'] ?>
				</div>    	
				<div class="car car_2 car_nombre car_mun">
					<?php echo $m['municipio'] ?>
				</div>    	
				<div class="car car_3 car_edad car_cp">
					<?php echo $m['CODIGO'] ?>
				</div>    		      	
			</div>
		</div>
