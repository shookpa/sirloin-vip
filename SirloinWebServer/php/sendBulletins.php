<?php
error_reporting(0);
require_once 'lib/UtilsVIP.php';
require_once 'lib/conexionMysql.php';		
require_once 'lib/sendMailrelay.php';

$arrayDatosInsertar = json_decode ( str_replace("\\", "", $_REQUEST ["store_data"]), true );
// var_dump($arrayDatosInsertar);
$i=0;
//COMO HASTA AHORITA SIGUE SIENDO UN OBJETO, LO CONVERTIMOS A UN ARREGLO PARA MIS FUNCIONES
foreach ( $arrayDatosInsertar [0] as $key => $value ) {
	//	     $arrayDatosInsertar2[$key] = utf8_encode(trim($value));
	if ($key=="correos") {
// 		$correos =  json_decode($value);
// 		var_dump($value[0]["selected"]);
// 		$correos =  $value;
		foreach ( $value[0]["selected"] as $keyC => $valueC ) {
// 			echo "--------$keyC => $valueC----------";
			$correos [$i] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $valueC ) ) );
			$i++;
		}
		
	}
	else 
		$arrayDatosInsertar2 [$key] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $value ) ) );
}

//VALIDAMOS QUE NO EXISTA ESE BOLETIN EN LA TABLA
$sqlBol="SELECT * FROM boletines WHERE id='".$arrayDatosInsertar2["id_boletin"]."'";
$rsd=traedatosmysql($sqlBol);

$arrayDatosInsertar2["cuerpo"]="<html><body><img src='http://www.programadepuntos-vip.com/images/boletines/".$rsd->fields["foto_boletin"]."' /></body></html>";
$arrayDatosInsertar2["asunto"]=$rsd->fields["asunto"];
$from[0]= "administrador@sirloin.com.mx";
$from[1]= "Administrador Sirloin";
$contEnvios=0;
$contCorreos=0;
$contErroneos=0;

$rcpt[0]["name"]="Webmaster Sirloin";
$rcpt[0]["email"]="jcenteno.ibm@gmail.com";


foreach ( $correos as $key => $value ) {
// 	echo "--------$key => $value----------";
	$para[0] = $value;
	$para[1] = $value;
	array_push($rcpt, array('name' => $para[1],'email' => $para[0]));
	$contCorreos++;
}

$result=sendEmail($rcpt, $arrayDatosInsertar2["cuerpo"], $arrayDatosInsertar2["asunto"]);
//VERIFICAMOS EL STATUS DEL ENVIO DEL BOLETIN:
// var_dump($result);
if ($result->status == 0) {
	echo "{'success':true, 'mensaje':'Hubo un problema en el envio,\" $numBoletines \" favor de intentar nuevamente'}";
}
else
{
	echo "{'success':true, 'mensaje':'Se enviaron ".$contCorreos." correos  $mensaje2 '}";
}

?>