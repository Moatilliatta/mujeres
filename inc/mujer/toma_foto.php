<?php session_start(); 

$folio = "'".$_POST['folio']."'";

?>
<link rel="stylesheet" href="<?php echo $_SESSION['css_path'].'foto.css' ?>" type="text/css"/>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/photobooth_min.js"></script>
<script type="text/javascript">

$('#example').photobooth().on("image",function( event, dataUrl ){ 
		$( "#gallery" ).html('<img id="img_'+<?php echo $folio;?>+'" src="' + dataUrl + '" >'+
							  '<div id="tomar" style="display:none;">'+
							  '<button class="guarda_foto" id="btn_'+<?php echo $folio;?>+'">'+
							  'Guardar Fotograf&iacute;a</button></div>'); 
});	

$('#example').data( "photobooth" ).resize( 320, 200 );
</script>

<div style="width: 100%;">
	<div id="example" style="width: 700px; height: 200px;">
		<div id="gallery" style="float:right; margin-right:20px;width:330px;"></div>
	</div>		
</div>