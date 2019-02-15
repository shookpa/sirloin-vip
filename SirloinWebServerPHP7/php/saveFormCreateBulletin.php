<?php
error_reporting ( 0 );
require_once 'lib/UtilsVIP.php';
require_once 'lib/conexionMysql.php';
// $arrayDatosInsertar = json_decode ( str_replace ( "\\", "", str_replace ( "\\\\\"", "'", $_REQUEST ["store_data"] ) ), true );
//COMO NOS LLEGA COMO UN OBJETO JSON, CONVIRTAMOSLO A UN ARREGLO PHP QUE ENTIENDEN MIS FUNCIONES DE MI FRAMEWORKSITO:
$arrayDatosInsertar =  json_decode ( str_replace("\\", "", $_REQUEST ["store_data"]), true );

$i = 0;
// COMO HASTA AHORITA SIGUE SIENDO UN OBJETO, LO CONVERTIMOS A UN ARREGLO PARA MIS FUNCIONES
foreach ( $arrayDatosInsertar [0] as $key => $value ) {
	// $arrayDatosInsertar2 [$key] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $value ) ) );
	$arrayDatosInsertar2 [$key] =html_entity_decode (preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', trim ( $value )));

	// $arrayDatosInsertar2 [$key] =html_entity_decode(trim ( $value ), ENT_QUOTES | ENT_HTML401, "UTF-8");

}
// var_dump($arrayDatosInsertar2);
// VALIDAMOS QUE NO EXISTA ESE BOLETIN EN LA TABLA
// echo "SELECT * FROM boletines WHERE asunto ='".$arrayDatosInsertar2["asunto"]."'";
$numBoletines = num_regmysql ( "SELECT * FROM boletines WHERE asunto ='" . $arrayDatosInsertar2 ["asunto"] . "'" );
if ($numBoletines <= 0) {
	$file_name = $_FILES ["foto"] ["name"];
	$file_tmp_name = $_FILES ["foto"] ["tmp_name"];
	$file_type = $_FILES ["foto"] ["type"];
	$file_size = round ( $_FILES ["foto"] ["size"] / 1024, 2 ) . "  Kilo Bytes";
	if ($file_name != '')
		if (move_uploaded_file ( $file_tmp_name, "../images/boletines/" . $file_name ))
			$arrayDatosInsertar2["foto_boletin"] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $file_name ) ) );
		else {
			$arrayDatosInsertar2["foto_boletin"] = "";
			echo "<P>MOVE UPLOADED FILE FAILED!</P>";
			print_r ( error_get_last () );
		}
		$arrayDatosInsertar2["fecha_registro"]=date("Y-m-d");
// 		var_dump($arrayDatosInsertar);
		$resultado = InsertTable ( "boletines", $arrayDatosInsertar2, true );
	echo "{'success':true, 'mensaje':'Se dio de alta correctamente el boletin: " . $arrayDatosInsertar2 ["asunto"] . "'}";
} else
	echo "{'success':false, 'mensaje':'Ya existe un boletin con el asunto: " . $arrayDatosInsertar2 ["asunto"] . ", favor de verificar'}";

?>
