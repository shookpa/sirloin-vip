<?php
/**
 * @class User
 */
class Movimiento extends Model {
	public $id, $attributes;
	static function create($params) {
		$obj = new self ( get_object_vars ( $params ) );
		$obj->save ();
		return $obj;
	}
	static function find($id) {
		global $dbh;
		$found = null;
		foreach ( $dbh->rs ( "tarifaBaseIntegra", "id_tarifa" ) as $rec ) {
			if ($rec ['id'] == $id) {
				$found = new self ( $rec );
				break;
			}
		}
		return $found;
	}
	static function update($id, $params) {
		global $dbh;
		$rec = self::find ( $id );
		
		if ($rec == null) {
			return $rec;
		}
		$rs = $dbh->rs ( "tarifaBaseIntegra", "id_tarifa" );
		
		foreach ( $rs as $idx => $row ) {
			if ($row ['id'] == $id) {
				$rec->attributes = array_merge ( $rec->attributes, get_object_vars ( $params ) );
				$rec->attributes ["id_tarifa"] = $id;
				// var_dump($rec->attributes);
				$dbh->update ( "tarifaBaseIntegra", $rec->attributes, "id_tarifa" );
				break;
			}
		}
		return $rec;
	}
	static function destroy($id) {
		global $dbh;
		$rec = null;
		$rs = $dbh->rs ( "tarifaBaseIntegra", "id_tarifa" );
		foreach ( $rs as $idx => $row ) {
			if ($row ['id'] == $id) {
				$rec = new self ( $dbh->destroy ( "tarifaBaseIntegra", "id_tarifa", $id ) );
				break;
			}
		}
		return $rec;
	}
	static function all() {
		global $dbh;
		
		return $dbh->rs ( "mov_vip
				LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
				LEFT JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip LIMIT 0", "id_mov", "id_mov,mov_vip.num_vip, clientes_vip.nom_vip, clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip,
				cat_restaurantes.nombre_restaurante, mov_vip.doc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip, mov_vip.mon_vip, mov_vip.caj_vip" );
	}
	static function searchMovimientos() {
		global $dbh;
		
		$muestraMovs=false;
		
		if (isset($_REQUEST["tarjeta"]))
		{
			$tarjeta= $_REQUEST["tarjeta"];
			$condTarj = "AND clientes_vip.num_vip= '$tarjeta'";
		}
		if (isset($_REQUEST["restaurante"]))
		{
			$rest = $_REQUEST["restaurante"];
			if ($rest>0)
				$condRest = "AND clientes_vip.id_restaurante= '$rest'";
		}
		if (isset($_REQUEST["nombreCliente"]))
		{
			$nombre= $_REQUEST["nombreCliente"];
			$condNombre = "AND clientes_vip.nom_vip LIKE '%$nombre%'";
		}
		if (isset($_REQUEST["operSaldo1"]))
		{
			$op1= $_REQUEST["operSaldo1"];
			$op2= $_REQUEST["operSaldo2"];
			$saldoMin= $_REQUEST["saldoMin"];
			$saldoMax= $_REQUEST["saldoMax"];
			$condSaldo = "AND clientes_vip.pto_vip $op1 $saldoMin AND clientes_vip.pto_vip $op2 $saldoMax";
		}
		if (isset($_REQUEST["fechaApertura"]))
		{
			$fechaApertura= $_REQUEST["fechaApertura"];
			$condFecApertura = "AND clientes_vip.fec_vip LIKE '$fechaApertura'";
		}
		if (isset($_REQUEST["telefono"]))
		{
			$tel= $_REQUEST["telefono"];
			$condTel = "AND clientes_vip.tel_vip LIKE '$tel'";
		}
		if (isset($_REQUEST["email"]))
		{
			$email= $_REQUEST["email"];
			$condEmail = "AND clientes_vip.ema_vip LIKE '$email'";
		}
		if (isset($_REQUEST["cp"]))
		{
			$cp= $_REQUEST["cp"];
			$condCP = "AND usuarios.cp LIKE '$cp'";
		}
		if (isset($_REQUEST["fechaNacIni"]))
		{
			
			$fechaNacIni= $_REQUEST["fechaNacIni"];
			$fechaNacFin= $_REQUEST["fechaNacFin"];
			
			$condFecNac = " AND usuarios.dia_nac BETWEEN DAY('$fechaNacIni') AND DAY('$fechaNacFin') 
							AND usuarios.mes_nac BETWEEN MONTH('$fechaNacIni') AND MONTH('$fechaNacFin') 
							"; //AND usuarios.year_nac BETWEEN YEAR('$fechaNacIni') AND YEAR('$fechaNacFin')   NO DEBEMOS CONSIDERAR EL AÑO
		}
		if (isset($_REQUEST["tipo_mov"]))
		{
			$muestraMovs=true;
			$tipoMov= $_REQUEST["tipo_mov"];
			switch ($tipoMov) {
				
				case 0:
					$condTipoMov= "AND mov_vip.det_vip LIKE '%'";
					break;
				case 1:
					$condTipoMov= "AND mov_vip.det_vip= 'Descarga Puntos en Tarjeta VIP'";
					break;
				case 2:
					$condTipoMov= "AND mov_vip.det_vip= 'Acumula Puntos en Tarjeta VIP'";
					break;
				case 3:
					$condTipoMov= "AND mov_vip.det_vip= 'Activacion de tarjeta en sistema web'";
					break;
				case 4:
					$condTipoMov= "AND mov_vip.det_vip= 'Registro de tarjeta en sistema web'";
					break;
				default:
					;
					break;
			}
			
			
		}
		if (isset($_REQUEST["fechaMovIni"]))
		{
			$muestraMovs=true;
			$fechaMovIni= $_REQUEST["fechaMovIni"];
			$fechaMovFin= $_REQUEST["fechaMovFin"];			
			
			$condFecMov = " AND mov_vip.fec_vip BETWEEN '$fechaMovIni' AND '$fechaMovFin'";
		}
		if (isset($_REQUEST["operMov1"]))
		{
			$muestraMovs=true;
			$operMov1= $_REQUEST["operMov1"];
			$montoMin= $_REQUEST["montoMin"];
			$operMov2= $_REQUEST["operMov2"];
			$montoMax= $_REQUEST["montoMax"];
			$condMontoMov = "HAVING sum(mov_vip.mon_vip)  $operMov1 $montoMin AND sum(mov_vip.mon_vip) $operMov2 $montoMax";
		}
		if ($_SESSION["s_tipoUser"]=="3")
			$condRest.=" AND cat_restaurantes.id_restaurante IN (".$_SESSION["s_rest"].")";
			
		if (isset($_REQUEST["operUso"]))
		{
			$operUso= $_REQUEST["operUso"];
			$numeroVeces= $_REQUEST["numeroVeces"];
			$fechaUsoIni= $_REQUEST["fechaUsoIni"];
			$fechaUsoFin= $_REQUEST["fechaUsoFin"];
			$periodo= $_REQUEST["periodo"];
			$condGroupAd="";
			switch ($periodo) {
				case 0:
					$condGroupAd="";
				break;
				case 1:
					$condGroupAd=", DAY(fec_vip)";
					break;
				case 2:
					$condGroupAd=", MONTH(fec_vip)";
					break;
				default:
					$condGroupAd="";
				break;
			}
			return $dbh->rs ( "mov_vip
					INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
					LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
					LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
					WHERE 
						mov_vip.fec_vip
						BETWEEN  CAST('".$fechaUsoIni."' AS DATE)
						AND  CAST('".$fechaUsoFin."' AS DATE)
						AND clientes_vip.num_vip
						IN (
							SELECT DISTINCT num_vip
							FROM mov_vip
							WHERE fec_vip
							BETWEEN  CAST('".$fechaUsoIni."' AS DATE)
							AND  CAST('".$fechaUsoFin."' AS DATE)
							GROUP BY num_vip $condGroupAd
							HAVING COUNT( id_mov ) $operUso  $numeroVeces
						)
					ORDER BY clientes_vip.num_vip, mov_vip.id_mov
					", "id_mov", "id_mov,mov_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante,CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  sum(mov_vip.mon_vip) AS mon_vip
	 		" );
		}
		
		if ($muestraMovs)	
			return $dbh->rs ( "mov_vip
				INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
				LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
				LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
				WHERE 1
					$condFec $condRest  $condTarj $condNombre $condSaldo  $condFecApertura $condTel $condEmail $condCP $condFecNac $condTipoMov $condFecMov 
				GROUP BY mov_vip.num_vip, mov_vip.det_vip, mov_vip.fec_vip 	
				$condMontoMov
				", "id_mov", "id_mov,mov_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante, CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac,usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  sum(mov_vip.mon_vip) AS mon_vip 
	 		" );
		else 
			return $dbh->rs ( "
					clientes_vip 
					LEFT JOIN cat_restaurantes ON clientes_vip.id_restaurante = cat_restaurantes.id_restaurante
					LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
					WHERE 1
					$condFec $condRest  $condTarj $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condFecNac $condTipoMov $condFecMov
					GROUP BY clientes_vip.num_vip					
					", "id_mov", "clientes_vip.id AS id_mov,clientes_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante, CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac,usuarios.cp, clientes_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, 
				clientes_vip.pto_vip 
	 		" );
	}
	static function searchMovimientosPaginados() {
		global $dbh;

	
		$muestraMovs=false;
		if (isset($_REQUEST["sort"]))
		{
			$paramsOrd=json_decode(str_replace('\\"', '"',$_REQUEST["sort"]),true);
			
			if ($paramsOrd[0]["property"]=="mon_vip" || $paramsOrd[0]["property"]=="det_vip" || $paramsOrd[0]["property"]=="fecha_mov" )
				if (isset($_REQUEST["tipo_mov"]) || isset($_REQUEST["fechaMovIni"]) || isset($_REQUEST["operMov1"]))
					$ordenacion=" ORDER BY ".$paramsOrd[0]["property"]. " ". $paramsOrd[0]["direction"];
				else 
					$ordenacion="";
			else 
				$ordenacion=" ORDER BY ".$paramsOrd[0]["property"]. " ". $paramsOrd[0]["direction"];
		}
		if (isset($_REQUEST["tarjeta"]))
		{	
			$tarjeta= $_REQUEST["tarjeta"];
			$condTarj = "AND clientes_vip.num_vip= '$tarjeta'";
		}
		if (isset($_REQUEST["restaurante"]))
		{	
			$rest = $_REQUEST["restaurante"];
			if ($rest>0)
				$condRest = "AND clientes_vip.id_restaurante= '$rest'";
		}
		if (isset($_REQUEST["nombreCliente"]))
		{	
			$nombre= $_REQUEST["nombreCliente"];
			$condNombre = "AND clientes_vip.nom_vip LIKE '%$nombre%'";
		}	
		
		if (isset($_REQUEST["operSaldo1"]))
		{
			$op1= $_REQUEST["operSaldo1"];
			$op2= $_REQUEST["operSaldo2"];
			$saldoMin= $_REQUEST["saldoMin"];
			$saldoMax= $_REQUEST["saldoMax"];
			$condSaldo = "AND clientes_vip.pto_vip $op1 $saldoMin AND clientes_vip.pto_vip $op2 $saldoMax";
		}
		if (isset($_REQUEST["fechaApertura"]))
		{
			$fechaApertura= $_REQUEST["fechaApertura"];
			$condFecApertura = "AND clientes_vip.fec_vip LIKE '$fechaApertura'";
		}	
		if (isset($_REQUEST["telefono"]))
		{
			$tel= $_REQUEST["telefono"];
			$condTel = "AND clientes_vip.tel_vip LIKE '$tel'";
		}	
		if (isset($_REQUEST["email"]))
		{
			$email= $_REQUEST["email"];
			$condEmail = "AND clientes_vip.ema_vip LIKE '$email'";
		}	
		if (isset($_REQUEST["cp"]))
		{
			$cp= $_REQUEST["cp"];
			$condCP = "AND usuarios.cp LIKE '$cp'";
		}	
		if (isset($_REQUEST["fechaNacIni"]))
		{
			
			$fechaNacIni= $_REQUEST["fechaNacIni"];
			$fechaNacFin= $_REQUEST["fechaNacFin"];
			
			$condFecNac = " AND usuarios.dia_nac BETWEEN DAY('$fechaNacIni') AND DAY('$fechaNacFin')
			AND usuarios.mes_nac BETWEEN MONTH('$fechaNacIni') AND MONTH('$fechaNacFin')
			"; //AND usuarios.year_nac BETWEEN YEAR('$fechaNacIni') AND YEAR('$fechaNacFin')   NO DEBEMOS CONSIDERAR EL AÑO
		}
		
		if (isset($_REQUEST["tipo_mov"]))
		{
			$muestraMovs=true;
			$tipoMov= $_REQUEST["tipo_mov"];
			switch ($tipoMov) {
				
				case 0:
					$condTipoMov= "AND mov_vip.det_vip LIKE '%'";
					break;
				case 1:
					$condTipoMov= "AND mov_vip.det_vip= 'Descarga Puntos en Tarjeta VIP'";
				break;
				case 2:
					$condTipoMov= "AND mov_vip.det_vip= 'Acumula Puntos en Tarjeta VIP'";
					break;
				case 3:
					$condTipoMov= "AND mov_vip.det_vip= 'Activacion de tarjeta en sistema web'";
					break;
				case 4:
					$condTipoMov= "AND mov_vip.det_vip= 'Registro de tarjeta en sistema web'";
					break;
				default:
					;
				break;
			}
			
			 
		}
		if (isset($_REQUEST["fechaMovIni"]))
		{
			$muestraMovs=true;
			$fechaMovIni= $_REQUEST["fechaMovIni"];
			$fechaMovFin= $_REQUEST["fechaMovFin"];			
			$condFecMov = " AND mov_vip.fec_vip BETWEEN '$fechaMovIni' AND '$fechaMovFin'";
		}
		
		if (isset($_REQUEST["operMov1"]))
		{
			$muestraMovs=true;
			$operMov1= $_REQUEST["operMov1"];
			$montoMin= $_REQUEST["montoMin"];
			$operMov2= $_REQUEST["operMov2"];
			$montoMax= $_REQUEST["montoMax"];
			$condMontoMov = "HAVING sum(mov_vip.mon_vip)  $operMov1 $montoMin AND sum(mov_vip.mon_vip) $operMov2 $montoMax";			
		}
	
			
		
		
		if ($_SESSION["s_tipoUser"]=="3")
			$condRest.=" AND cat_restaurantes.id_restaurante IN (".$_SESSION["s_rest"].")";
		
			
			if (isset($_REQUEST["operUso"]))
			{
				$operUso= $_REQUEST["operUso"];
				$numeroVeces= $_REQUEST["numeroVeces"];
				$fechaUsoIni= $_REQUEST["fechaUsoIni"];
				$fechaUsoFin= $_REQUEST["fechaUsoFin"];
				$periodo= $_REQUEST["periodo"];
				$condGroupAd="";
				switch ($periodo) {
					case 0:
						$condGroupAd="";
						break;
					case 1:
						$condGroupAd=", DAY(fec_vip)";
						break;
					case 2:
						$condGroupAd=", MONTH(fec_vip)";
						break;
					default:
						$condGroupAd="";
						break;
				}
				
				return $dbh->rs ( "mov_vip
					INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
					LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
					LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
					WHERE
						mov_vip.fec_vip
						BETWEEN  CAST('".$fechaUsoIni."' AS DATE)
						AND  CAST('".$fechaUsoFin."' AS DATE)
						AND clientes_vip.num_vip
						IN (
							SELECT DISTINCT num_vip
							FROM mov_vip
							WHERE fec_vip
							BETWEEN  CAST('".$fechaUsoIni."' AS DATE)
							AND  CAST('".$fechaUsoFin."' AS DATE)
						GROUP BY num_vip $condGroupAd
						HAVING COUNT( id_mov ) $operUso  $numeroVeces
						)
						$ordenacion
						", "id_mov", "id_mov,mov_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante,
				CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac,  
		usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  mov_vip.mon_vip
	 		" );//LA ORDENACION ANTERIOR: ORDER BY clientes_vip.num_vip, mov_vip.id_mov
			}
			
		
// 		echo "SELECT id_mov,mov_vip.num_vip, clientes_vip.nom_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
// 				cat_restaurantes.nombre_restaurante, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip, clientes_vip.pto_vip,
// 				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  sum(mov_vip.mon_vip) AS mon_vip FROM mov_vip
// 														INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
// 														LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
// 														LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
// 														WHERE 1 
// 														$condFec $condRest  $condTarj $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condTipoMov $condFecMov
// 														GROUP BY mov_vip.num_vip, mov_vip.det_vip, mov_vip.fec_vip 
// 														$condMontoMov  LIMIT " . $_REQUEST ["limit"] . " OFFSET " . $_REQUEST["start"]. $ordenacion;	
	
		
			
			
		if ($muestraMovs)
			return $dbh->rs ( "mov_vip
														INNER JOIN clientes_vip ON clientes_vip.num_vip=mov_vip.num_vip
														LEFT JOIN cat_restaurantes ON mov_vip.id_restaurante = cat_restaurantes.id_restaurante
														LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
														WHERE 1 
														$condFec $condRest  $condTarj $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condFecNac $condTipoMov $condFecMov
														GROUP BY mov_vip.num_vip, mov_vip.det_vip, mov_vip.fec_vip 
														$condMontoMov
														$ordenacion
														LIMIT " . $_REQUEST ["limit"] . " OFFSET " . $_REQUEST["start"] . "
														", "id_mov", "id_mov,mov_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante,CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac, usuarios.cp,mov_vip.doc_vip,mov_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, clientes_vip.pto_vip,
				mov_vip.det_vip, mov_vip.fec_vip AS fecha_mov,  sum(mov_vip.mon_vip) AS mon_vip
	 		" );
		else
			return $dbh->rs ( "
																	clientes_vip 
																	LEFT JOIN cat_restaurantes ON clientes_vip.id_restaurante = cat_restaurantes.id_restaurante
																	LEFT JOIN usuarios ON usuarios.num_vip = clientes_vip.num_vip
																	WHERE 1
																	$condFec $condRest  $condTarj $condNombre $condSaldo $condFecApertura $condTel $condEmail $condCP $condFecNac $condTipoMov $condFecMov
																	GROUP BY clientes_vip.num_vip
																	$condMount
																	$ordenacion
																	LIMIT " . $_REQUEST["limit"] . " OFFSET " . $_REQUEST["start"] . "
																	", "id_mov", "clientes_vip.id AS id_mov,clientes_vip.num_vip, clientes_vip.nom_vip,clientes_vip.muc_vip, clientes_vip.fup_vip, clientes_vip.tct_vip, clientes_vip.tel_vip,clientes_vip.ema_vip, clientes_vip.pto_vip,
				cat_restaurantes.nombre_restaurante,CONCAT( year_nac,  '-', IF (mes_nac <10, CONCAT(  '0', mes_nac ) , mes_nac ) ,  '-', IF (dia_nac <10, CONCAT(  '0', dia_nac ) , dia_nac)) AS fecha_nac,usuarios.cp, clientes_vip.id_restaurante,clientes_vip.fec_vip,clientes_vip.fuc_vip, 
				clientes_vip.pto_vip
	 		" );
	}
	public function __construct($params) {
		$this->id = isset ( $params ['id'] ) ? $params ['id'] : null;
		$this->attributes = $params;
	}
	public function save() {
		global $dbh;
		$dbh->insert ( $this->attributes, "tarifaBaseIntegra" );
	}
	public function to_hash() {
		return $this->attributes;
	}
}

