<?php
require_once 'lib/UtilsVIP.php';
require_once 'lib/conexionMysql.php';		
require_once 'lib/sendMailrelay.php';

$arrayDatosInsertar = json_decode ( str_replace("\\", "", $_REQUEST ["store_data"]), true );
// var_dump($arrayDatosInsertar);
$i=0;
//COMO HASTA AHORITA SIGUE SIENDO UN OBJETO, LO CONVERTIMOS A UN ARREGLO PARA MIS FUNCIONES
foreach ( $arrayDatosInsertar [0] as $key => $value ) {
	//	     $arrayDatosInsertar2[$key] = utf8_encode(trim($value)); 
		$arrayDatosInsertar2 [$key] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $value ) ) );
}

$arrayDatosInsertar2 ["datetime_vip"]=date("Y-m-d H:i:s");
// var_dump($arrayDatosInsertar2 );
$sql="SELECT pto_vip FROM clientes_vip WHERE num_vip ='".$arrayDatosInsertar2["num_vip"]."'";
$rsd=traedatosmysql($sql);
$detMov="";
if($arrayDatosInsertar2["tipoMov"]==1)
{
	$arrayDatosInsertar2 ["pto_vip"]=$rsd->fields["pto_vip"]+$arrayDatosInsertar2 ["montoMov"];
	$detMov="Acumulación de Puntos";
	
}
if($arrayDatosInsertar2["tipoMov"]==2)
{
	$arrayDatosInsertar2 ["pto_vip"]=$rsd->fields["pto_vip"]-$arrayDatosInsertar2 ["montoMov"];
	$detMov="Descuento de Puntos";
}
// var_dump($arrayDatosInsertar2);


$resultado = UpdateTable("clientes_vip", $arrayDatosInsertar2,"num_vip");

//Aparte de actualizar el saldo, debe generar un movimiento de actualizacion de saldo:
$arDatMovs["id_restaurante"]=$arrayDatosInsertar2 ["id_restaurante"];
$arDatMovs["num_vip"]=$arrayDatosInsertar2 ["num_vip"];
$arDatMovs["fec_vip"]=date("Y-m-d");
$arDatMovs["det_vip"]=$detMov." desde Sistema Web";
$arDatMovs["mon_vip"]=$arrayDatosInsertar2 ["montoMov"];
InsertTable("mov_vip", $arDatMovs,true);
echo "{'success':true, 'mensaje':'Se actualizo correctamente el saldo del cliente'}";

?>