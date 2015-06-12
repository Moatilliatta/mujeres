<?php
error_reporting(E_ALL ^ E_STRICT);

function buscaNombre($nombres,$paterno,$materno){

    $direccion = "https://apps.padronunico.jalisco.gob.mx:8080/WsTisa/WSConsultaPU.svc?wsdl";
    $Beneficarios = array();

    try {

        $client = new SoapClient($direccion);
              
       /* $fcs = $client->__getFunctions();
        print_r($fcs);
        exit;
       */
       
        $obj = array('user' => 'DIFweb',
                     'pw' => '53hn5uch7',
                     'apaterno'  => $paterno ,
                     'nombre'  => $nombres ,
                     'amaterno'  =>  $materno);

        $fcs = $client->BuscaBeneficiario($obj);
        $resultado = $fcs->BuscaBeneficiarioResult;
        
        //echo '<br><br><br>';
        //echo $resultado->Mensaje." ". $resultado->Resultado;
        
        $datos = $resultado->beneficiarios;
        //print_r($datos);      
        //echo '<br><br><br>';
        
        //print_r($datos->Beneficiario2);
        //echo 'CURP:'.$datos->Beneficiario2->Curp;
        
        $beneficario = $datos->Beneficiario2;
        

    } catch (Exception $e) {
        trigger_error($e->getMessage(), E_USER_WARNING);
    }

    return $beneficario;
	print_R($beneficiario);
	exit;
}

/*
//Obtenemos beneficiarios
foreach ($mujeres_avanzando as $key => $value):
    $obj = buscaNombre($value['nombres'],$value['paterno'],$value['materno']);     
    if($obj != NULL){
        $Beneficarios[] = $obj;
    }        
endforeach;
*/

$obj = buscaNombre('evelin margarita','polanco','mata');     
    if($obj != NULL){
        $Beneficarios[] = $obj;
    }        
?>

<table>
    <tr>
        <th>ID(FPU)</th>
        <th>CURP</th>
        <th>NOMBRE</th>
        <th>APELLIDO PATERNO</th>
        <th>APELLIDO MATERNO</th>
        <th>SEXO</th>
        <th>CALLE</th>
        <th>NUM EXT</th>
        <th>MUNICIPIO</th>
        <th>C.P.</th>
        <th>Programas</th>
    </tr>
    <?php foreach($Beneficarios as $k => $v):?>
    <tr>
        <td><?php echo $v->Fpu; ?></td>
        <td><?php echo $v->Curp; ?></td>
        <td><?php echo utf8_decode($v->Nombre); ?></td>
        <td><?php echo utf8_decode($v->Apaterno); ?></td>
        <td><?php echo utf8_decode($v->Amaterno); ?></td>
        <td><?php echo $v->Sexo; ?></td>
        <td><?php echo utf8_decode($v->Calle); ?></td>
        <td><?php echo $v->NumExt; ?></td>
        <td><?php echo $v->NomLocalidad; ?></td>
        <td><?php echo $v->Cp; ?></td>
        <td><?php print_r($v->programas); ?></td>
    </tr>
    <?php endforeach;?>
