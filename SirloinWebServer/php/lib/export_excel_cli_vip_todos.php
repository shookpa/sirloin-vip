<?php
ini_set("memory_limit","4096M");
date_default_timezone_set ( 'America/Mexico_City' );
include ('../lib/sesion_usuario.php');
include ('../lib/valida_sesion.php');
include ("../lib/conexionMysql.php");
include_once "PHPExcel/PHPExcel.php";
// Se crea el objeto PHPExcel
$objPHPExcel = new PHPExcel ();
$nombreArchivo = "Listado_de_Clientes_VIP_al_" . date ( "Y-m-d_H.i.s" );
// Se asignan las propiedades del libro
$objPHPExcel->getProperties ()->setCreator ( "Jorge Centeno" )-> // Autor
setLastModifiedBy ( "Jorge Centeno" )-> // Ultimo usuario que lo modificó
setTitle ( "Reporte Excel con PHP y MySQL" )->setSubject ( "Reporte Excel con PHP y MySQL" )->setDescription ( $nombreArchivo )->setKeywords ( $nombreArchivo )->setCategory ( $nombreArchivo );
$arrayDatosInsertar =  json_decode ( str_replace("\\", "", $_REQUEST ["data"]), true );
$seleccion="";
$seleccionados= array();
foreach ( $arrayDatosInsertar as $key => $value ) {

	if ($key=="seleccionados") {
		$seleccionados=  $value;
		foreach ( $value  as $keyC => $valueC ) {
			$seleccionados [$i] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $valueC ) ) );
			$seleccion.=$seleccionados [$i].",";
			$i++;
		}
		
	}
	else
		$arrayDatosInsertar2 [$key] = html_entity_decode ( preg_replace ( "/\\\\u([0-9abcdef]{4})/", "&#x$1;", trim ( $value ) ) );
}
$muestraMovs = false;
if ($seleccion!="" &&  sizeof($seleccionados)<501) {
	$seleccion=substr($seleccion, 0,strlen($seleccion)-1);	
 	$condSeleccionados = "AND clientes_vip.num_vip IN ( $seleccion)";
}
if (isset ( $arrayDatosInsertar ["tarjeta"] )) {
	$tarjeta = $arrayDatosInsertar ["tarjeta"];
	$condTarj = "AND clientes_vip.num_vip= '$tarjeta'";
}

if (isset ( $arrayDatosInsertar ["restaurante"] )) {
	$rest = $arrayDatosInsertar ["restaurante"];
	if ($rest > 0)
		$condRest = "AND clientes_vip.id_restaurante= '$rest'";
}
if (isset ( $arrayDatosInsertar ["nombreCliente"] )) {
	$nombre = $arrayDatosInsertar ["nombreCliente"];
	$condNombre = "AND clientes_vip.nom_vip LIKE '%$nombre%'";
}

if (isset ( $arrayDatosInsertar ["operSaldo1"] )) {
	$op1 = $arrayDatosInsertar ["operSaldo1"];
	$op2 = $arrayDatosInsertar ["operSaldo2"];
	$saldoMin = $arrayDatosInsertar ["saldoMin"];
	$saldoMax = $arrayDatosInsertar ["saldoMax"];
	$condSaldo = "AND clientes_vip.pto_vip $op1 $saldoMin AND clientes_vip.pto_vip $op2 $saldoMax";
}
if (isset ( $arrayDatosInsertar ["fechaApertura"] )) {
	$fechaApertura = $arrayDatosInsertar ["fechaApertura"];
	$condFecApertura = "AND clientes_vip.fec_vip LIKE '$fechaApertura'";
}
if (isset ( $arrayDatosInsertar ["telefono"] )) {
	$tel = $arrayDatosInsertar ["telefono"];
	$condTel = "AND clientes_vip.tel_vip LIKE '$tel'";
}
if (isset ( $arrayDatosInsertar ["email"] )) {
	$email = $arrayDatosInsertar ["email"];
	$condEmail = "AND clientes_vip.ema_vip LIKE '$email'";
}
if (isset ( $arrayDatosInsertar ["cp"] )) {
	$cp = $arrayDatosInsertar ["cp"];
	$condCP = "AND usuarios.cp LIKE '$cp'";
}

if (isset ( $arrayDatosInsertar["fechaNacIni"] )) {
	
	$fechaNacIni = $arrayDatosInsertar["fechaNacIni"];
	$fechaNacFin = $arrayDatosInsertar["fechaNacFin"];
	
	$condFecNac = " AND usuarios.dia_nac BETWEEN DAY('$fechaNacIni') AND DAY('$fechaNacFin')
	AND usuarios.mes_nac BETWEEN MONTH('$fechaNacIni') AND MONTH('$fechaNacFin')
	"; // AND usuarios.year_nac BETWEEN YEAR('$fechaNacIni') AND YEAR('$fechaNacFin') NO DEBEMOS CONSIDERAR EL AÑO
}
if (isset ( $arrayDatosInsertar["mesNac"] )) {
	$mesNac = $arrayDatosInsertar["mesNac"];
	$condMesNac = " AND usuarios.mes_nac = '$mesNac'
	";
}


if (isset ( $arrayDatosInsertar ["tipo_mov"] )) {
	$muestraMovs = true;
	$tipoMov = $arrayDatosInsertar ["tipo_mov"];
	switch ($tipoMov) {
		
		case 0 :
			$condTipoMov = "AND mov_vip.det_vip LIKE '%'";
			break;
		case 1 :
			$condTipoMov = "AND mov_vip.det_vip= 'Descarga Puntos en Tarjeta VIP'";
			break;
		case 2 :
			$condTipoMov = "AND mov_vip.det_vip= 'Acumula Puntos en Tarjeta VIP'";
			break;
		case 3 :
			$condTipoMov = "AND mov_vip.det_vip= 'Activacion de tarjeta en sistema web'";
			break;
		case 4 :
			$condTipoMov = "AND mov_vip.det_vip= 'Registro de tarjeta en sistema web'";
			break;
		case 5 :
			$condTipoMov = "AND mov_vip.det_vip LIKE '%desde Sistema Web'";
			break;
		case 6 :
			$condTipoMov = "AND mov_vip.det_vip= 'Vencimiento de puntos'";
			break;
		default :
			;
			break;
	}
}
if (isset ( $arrayDatosInsertar ["fechaMovIni"] )) {
	$muestraMovs = true;
	$fechaMovIni = $arrayDatosInsertar ["fechaMovIni"];
	$fechaMovFin = $arrayDatosInsertar ["fechaMovFin"];
	$condFecMov = " AND mov_vip.fec_vip BETWEEN '$fechaMovIni' AND '$fechaMovFin'";
}

if (isset ( $arrayDatosInsertar ["operMov1"] )) {
	$muestraMovs = true;
	$operMov1 = $arrayDatosInsertar ["operMov1"];
	$montoMin = $arrayDatosInsertar ["montoMin"];
	$operMov2 = $arrayDatosInsertar ["operMov2"];
	$montoMax = $arrayDatosInsertar ["montoMax"];
	$condMontoMov = "HAVING sum(mov_vip.mon_vip)  $operMov1 $montoMin AND sum(mov_vip.mon_vip) $operMov2 $montoMax";
}

if ($_SESSION ["s_tipoUser"] == "3")
	if ($_SESSION ["s_rest"]!="false" && $_SESSION ["s_rest"]!=false)
		$condRest .= " AND cat_restaurantes.id_restaurante IN (" . $_SESSION ["s_rest"] . ")";
	else
		$condRest .= " AND cat_restaurantes.id_restaurante IN (0)";

if (isset ( $arrayDatosInsertar ["operUso"] )) {
	$operUso = $arrayDatosInsertar ["operUso"];
	$numeroVeces = $arrayDatosInsertar ["numeroVeces"];
	$fechaUsoIni = $arrayDatosInsertar ["fechaUsoIni"];
	$fechaUsoFin = $arrayDatosInsertar ["fechaUsoFin"];
	$periodo = $arrayDatosInsertar ["periodo"];
	$condGroupAd = "";
	switch ($periodo) {
		case 0 :
			$condGroupAd = "";
			break;
		case 1 :
			$condGroupAd = ", DAY(fec_vip)";
			break;
		case 2 :
			$condGroupAd = ", MONTH(fec_vip)";
			break;
		default :
			$condGroupAd = "";
			break;
	}
	$sql = "SELECT
	clientes_vip.id,mov_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  mov_vip.mon_vip
	FROM
	mov_vip
					INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
					LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
					LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
					WHERE
						mov_vip.fec_vip
						BETWEEN  CAST('" . $fechaUsoIni . "' AS DATE)
						AND  CAST('" . $fechaUsoFin . "' AS DATE)
						AND clientes_vip.num_vip
						IN (
							SELECT DISTINCT num_vip
							FROM mov_vip
							WHERE fec_vip
							BETWEEN  CAST('" . $fechaUsoIni . "' AS DATE)
							AND  CAST('" . $fechaUsoFin . "' AS DATE)
				GROUP BY num_vip $condGroupAd
				HAVING COUNT( id_mov ) $operUso  $numeroVeces
				)";
	// LA ORDENACION ANTERIOR: ORDER BY clientes_vip.num_vip, mov_vip.id_mov
}

// echo "SELECT id_mov,mov_vip.num_vip, clientes_vip.nom_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
// cat_restaurantes.nombre_restaurante, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip, clientes_vip.pto_vip,
// mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov, sum(mov_vip.mon_vip) AS mon_vip FROM mov_vip
// INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
// LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
// LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
// WHERE 1
// $condFec $condRest $condTarj $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condTipoMov $condFecMov
// GROUP BY mov_vip.num_vip, mov_vip.det_vip, mov_vip.fec_vip
// $condMontoMov LIMIT " . $arrayDatosInsertar ["limit"] . " OFFSET " . $arrayDatosInsertar["start"]. ;

if ($muestraMovs)
	$sql = "SELECT clientes_vip.id,mov_vip.num_vip, clientes_vip.nom_vip, clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip,clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  sum(mov_vip.mon_vip) AS mon_vip 
			  FROM mov_vip
				INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
				LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
				LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
				WHERE 1
				$condFec $condRest  $condTarj $condSeleccionados $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condTipoMov $condFecMov $condFecNac $condMesNac 
				GROUP BY  mov_vip.id_mov
				$condMontoMov";

else
	$sql = "SELECT clientes_vip.id ,clientes_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante,usuarios.cp, clientes_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip,
				clientes_vip.pto_vip FROM clientes_vip
							LEFT JOIN cat_restaurantes ON clientes_vip.id_restaurante = cat_restaurantes.id_restaurante
							LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
							WHERE 1
							$condFec $condRest  $condTarj $condSeleccionados $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condTipoMov $condFecMov $condFecNac $condMesNac 
							GROUP BY clientes_vip.num_vip
							$condMount ";
$titulosColumnas = array (
		'id',
		'nombre_restaurante',
		'num_vip',
		'nom_vip',
		'tel_vip',
		'ema_vip',
		'cp',
		'fec_vip',
		'fuc_vip',
		'pto_vip',
		'det_vip',
		'fecha_mov',
		'mon_vip',
		'muc_vip',
		'fup_vip',
		'tct_vip' 
);
$objPHPExcel->setActiveSheetIndex ( 0 )->mergeCells ( 'A1:Q1' );
// Se agregan los titulos del reporte
$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', $nombreArchivo)->setCellValue ( 'A3', $titulosColumnas [0] )->setCellValue ( 'B3', $titulosColumnas [1] )->setCellValue ( 'C3', $titulosColumnas [2] )->setCellValue ( 'D3', $titulosColumnas [3] )->setCellValue ( 'E3', $titulosColumnas [4] )->setCellValue ( 'F3', $titulosColumnas [5] )->setCellValue ( 'G3', $titulosColumnas [6] )->setCellValue ( 'H3', $titulosColumnas [7] )->setCellValue ( 'I3', $titulosColumnas [8] )->setCellValue ( 'J3', $titulosColumnas [9] )->setCellValue ( 'K3', $titulosColumnas [10] )->setCellValue ( 'L3', $titulosColumnas [11] )->setCellValue ( 'M3', $titulosColumnas [12] )->setCellValue ( 'N3', $titulosColumnas [13] )->setCellValue ( 'O3', $titulosColumnas [14] )->setCellValue ( 'P3', $titulosColumnas [15] );
// Se agregan los datos de los alumnos
$i = 4;
$rsdDatos = traedatosmysql ( $sql );
while ( ! $rsdDatos->EOF ) {	
	$objPHPExcel->setActiveSheetIndex ( 0 )
	->setCellValue ( 'A' . $i, $rsdDatos->fields ["id"] )
	->setCellValue ( 'B' . $i, $rsdDatos->fields ["nombre_restaurante"] )
	->setCellValue ( 'C' . $i, $rsdDatos->fields ["num_vip"] )
	->setCellValue ( 'D' . $i, utf8_encode ( $rsdDatos->fields ["nom_vip"] ) )	
	->setCellValue ( 'e' . $i, ($rsdDatos->fields ["tel_vip"]) )
	->setCellValue ( 'F' . $i, ($rsdDatos->fields ["ema_vip"]) )
	->setCellValue ( 'G' . $i, ($rsdDatos->fields ["cp"]) )
	->setCellValue ( 'H' . $i, ($rsdDatos->fields ["fec_vip"]) )
	->setCellValue ( 'I' . $i, ($rsdDatos->fields ["fuc_vip"]) )
	->setCellValue ( 'J' . $i, ($rsdDatos->fields ["pto_vip"]) )
	->setCellValue ( 'K' . $i, ($rsdDatos->fields ["det_vip"]) )
	->setCellValue ( 'L' . $i, ($rsdDatos->fields ["fecha_mov"]) )
	->setCellValue ( 'M' . $i, ($rsdDatos->fields ["mon_vip"]) )
	->setCellValue ( 'N' . $i, ($rsdDatos->fields ["muc_vip"]) )
	->setCellValue ( 'O' . $i, ($rsdDatos->fields ["fup_vip"]) )
	->setCellValue ( 'P' . $i, ($rsdDatos->fields ["tct_vip"]) )
	;
	$i ++;
	$rsdDatos->MoveNext ();
}

$estiloTituloReporte = array (
		'font' => array (
				'name' => 'Verdana',
				'bold' => true,
				'italic' => false,
				'strike' => false,
				'size' => 16,
				'color' => array (
						'rgb' => 'FFFFFF' 
				) 
		),
		'fill' => array (
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array (
						'argb' => 'FF220835' 
				) 
		),
		'borders' => array (
				'allborders' => array (
						'style' => PHPExcel_Style_Border::BORDER_NONE 
				) 
		),
		'alignment' => array (
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'rotation' => 0,
				'wrap' => TRUE 
		) 
);

$estiloTituloColumnas = array (
		'font' => array (
				'name' => 'Arial',
				'bold' => true,
				'color' => array (
						'rgb' => 'FFFFFF' 
				) 
		),
		'fill' => array (
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array (
						'rgb' => 'c47cf2' 
				),
				'endcolor' => array (
						'argb' => 'FF431a5d' 
				) 
		),
		'borders' => array (
				'top' => array (
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
						'color' => array (
								'rgb' => '143860' 
						) 
				),
				'bottom' => array (
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
						'color' => array (
								'rgb' => '143860' 
						) 
				) 
		),
		'alignment' => array (
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => TRUE 
		) 
);

$estiloInformacion = new PHPExcel_Style ();
$estiloInformacion->applyFromArray ( array (
		'font' => array (
				'name' => 'Arial',
				'color' => array (
						'rgb' => '000000' 
				) 
		),
		'fill' => array (
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array (
						'argb' => 'FFd9b7f4' 
				) 
		),
		'borders' => array (
				'left' => array (
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array (
								'rgb' => '3a2a47' 
						) 
				) 
		) 
) );

$objPHPExcel->getActiveSheet ()->getStyle ( 'A1:Q1' )->applyFromArray ( $estiloTituloReporte );
$objPHPExcel->getActiveSheet ()->getStyle ( 'A3:Q3' )->applyFromArray ( $estiloTituloColumnas );
$objPHPExcel->getActiveSheet ()->setSharedStyle ( $estiloInformacion, "A4:D" . ($i - 1) );

for($i = 'A'; $i <= 'Q'; $i ++) {
	$objPHPExcel->setActiveSheetIndex ( 0 )->getColumnDimension ( $i )->setAutoSize ( true );
}

// Se asigna el nombre a la hoja
$objPHPExcel->getActiveSheet ()->setTitle ( 'Tarjetas_Clientes_VIP' );

// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
$objPHPExcel->setActiveSheetIndex ( 0 );
// Inmovilizar paneles
// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
$objPHPExcel->getActiveSheet ( 0 )->freezePaneByColumnAndRow ( 0, 4 );

// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
// header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
// header ( 'Content-Disposition: attachment;filename="' . $nombreArchivo . '.xlsx"' );
// header ( 'Cache-Control: max-age=0' );

$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
// $objWriter->save('php://output');
// $filePath = "/usr/local/4admin/apache/vhosts/programadepuntos-vip.com/httpdocs/resources/" . rand ( 0, getrandmax () ) . rand ( 0, getrandmax () ) . ".tmp";
$filePath = "/usr/local/4admin/apache/vhosts/programadepuntos-vip.com/httpdocs/resources/" . $nombreArchivo . '.xlsx';
$objWriter->save ( $filePath );
// readfile ( $filePath );
echo "resources/". $nombreArchivo . '.xlsx';
// unlink ( $filePath );
exit ();
?>
