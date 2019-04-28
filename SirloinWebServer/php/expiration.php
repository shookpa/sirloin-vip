<?php
error_reporting(0);
require_once 'lib/create_excel_cli_vip_todos.php';
require_once 'lib/sendMailrelay.php';
$arrayDatosInsertar = json_decode ( str_replace("\\", "", $_REQUEST ["store_data"]), true );
// var_dump($arrayDatosInsertar);
$i=0;
//COMO HASTA AHORITA SIGUE SIENDO UN OBJETO, LO CONVERTIMOS A UN ARREGLO PARA MIS FUNCIONES
foreach ( $arrayDatosInsertar [0] as $key => $value ) {
	//	     $arrayDatosInsertar2[$key] = utf8_encode(trim($value));
	if ($key=="tarjetas") {
		// 		$correos =  json_decode($value);
		// 		var_dump($value[0]["selected"]);
		// 		$correos =  $value;
		foreach ( $value[0]["selected"] as $keyC => $valueC ) {
			// 			echo "--------$keyC => $valueC----------";
			$tarjetas [$i] = $valueC ;
			$i++;
		}
		
	}

}
$contTarjetas=0;
$contCorreos=0;
$rcpt[0]["name"]="Webmaster";
$rcpt[0]["email"]="mr_shookp@hotmail.com";
$tarjetas = array_unique($tarjetas);

foreach ( $tarjetas as $key => $value ) {
	//REINICIANDO LOS SALDOS A CERO
	$sqlUpdate="UPDATE clientes_vip SET pto_vip=0, datetime_vip=NOW() WHERE pto_vip <> 0 AND num_vip = '$value' ";
	traedatosmysql($sqlUpdate);
	
	
	$sqlDatos="SELECT ema_vip, nom_vip,id_restaurante, num_vip  FROM clientes_vip WHERE num_vip = '$value'";
	$rsdDatos=traedatosmysql($sqlDatos);
	while (!$rsdDatos->EOF)
	{
		//Aparte de actualizar el saldo, debe generar un movimiento de actualizacion de saldo:
		$arDatMovs["id_restaurante"]=$rsdDatos->fields["id_restaurante"];
		$arDatMovs["num_vip"]=$rsdDatos->fields["num_vip"];
		$arDatMovs["fec_vip"]=date("Y-m-d");
		$arDatMovs["det_vip"]="Vencimiento de puntos";
		$arDatMovs["mon_vip"]=0;
		InsertTable("mov_vip", $arDatMovs,true);
		$rsdDatos->MoveNext();
	}

	
	
	
	$sqlCorreos="SELECT ema_vip, nom_vip FROM clientes_vip WHERE ema_vip<>'' AND num_vip = '$value'";
	$rsdCorreos=traedatosmysql($sqlCorreos);
	
	while (!$rsdCorreos->EOF)
	{
		$para[0] = $rsdCorreos->fields["ema_vip"];
		$para[1] = $rsdCorreos->fields["nom_vip"];
		array_push($rcpt, array('name' => $para[1],'email' => $para[0]));
		$contCorreos++;
		$rsdCorreos->MoveNext();
	}	
	$contTarjetas++;
}



$body= '<html><head><title>Saldo Vencido</title></head><body><h1>
			<p>Buen d&iacute;a estimado cliente</p><p>Para informarle que de acuerdo a nuestras pol&iacute;ticas de vencimiento de puntos, el saldo de su tarjeta quedo en ceros.</p>
			<p>Sin embargo, la podr&aacute; seguir utilizando en sus consumos y as&iacute; seguir acumulando puntos para pagar en cualquiera de nuestras sucursales.</p><p><br></p>
			</h1></body></html>';
$subject="Puntos de su tarjeta Sirloin Expirados";
$result=sendEmail($rcpt, $body, $subject);
if ($result->status == 0) {
	echo "{'success':true, 'mensaje':'Hubo un problema en el envio,\" $numBoletines \", pero los saldos ya se reniciaron a ceros de las $contTarjetas seleccionadas'}";
}
else
{
	echo "{'success':true, 'mensaje':'Se enviaron ".$contCorreos." correos  de las $contTarjetas tarjetas que expiraron sus puntos '}";
}

?>