<?
//include('adodb/adodb-exceptions.inc.php'); 
set_time_limit(0);
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	//CONSULTO SI EL DOCUMENTO DE ORIGEN AUN ESTA DISPONIBLE
	$sql = "SELECT nrodoc FROM historico.movimientos_presupuestarios WHERE nrodoc = '$JsonRec->origen'";
	$row = $conn->Execute($sql);
	if($row->RecordCount()>0){
		//die(var_dump($JsonRec));
		// Inserto los registros en el correlativo reasignado
		$q = "SELECT * FROM historico.movimientos_presupuestarios WHERE nrodoc = '".$JsonRec->destino."'";
		
		$r = $conn->Execute($q);
		//echo 'es: '.$r->RecordCount();
		if($r->RecordCount()>0){
			//Si hay una orden de pago en el correlativo destino se reasigna correlativo a esta
			/*$nroref = $r->fields['nroref'];
			$fecha = $r->fields['fecha'];
			$status = $r->fields['status'];
			$id_condicion_pago = $r->fields['id_condicion_pago'];
			$fuente_financiamiento = $r->fields['fuente_financiamiento'];
			$id_tipo_solicitud_si = $r->fields['id_tipo_solicitud_si'];
			$monto_si = $r->fields['monto_si'];
			$fecha_aprobacion = !empty($r->fields['fecha_aprobacion']) ? "'".$r->fields['fecha_aprobacion']."'" : 'null';
			$montodoc = $r->fields['montodoc'];
			$montoret = $r->fields['montoret'];
			$montopagado = $r->fields['montopagado'];
			$motivo = $r->fields['motivo'];
			$id_proveedor = $r->fields['id_proveedor'];
			$id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$descripcion = $r->fields['descripcion'];
			$ano = $r->fields['ano'];
			$cuenta_contable_anticipo = !empty($r->fields['cuenta_contable_anticipo']) ? $r->fields['cuenta_contable_anticipo'] : 'null';
			$monto_anticipo = !empty($r->fields['monto_anticipo']) ? $r->fields['monto_anticipo'] : 'null';
			$fecha_anulacion = !empty($r->fields['fecha_anulacion']) ? "'".$r->fields['fecha_anulacion']."'" : 'null';
			//Inserto el registro con el numero de documento reasignado
			$q2 = "INSERT INTO historico.orden_pago (nrodoc, nroref, fecha, status, id_condicion_pago, fuente_financiamiento, id_tipo_solicitud_si, monto_si, fecha_aprobacion, ";
			$q2.= "montodoc, montoret, montopagado, motivo, id_proveedor, id_unidad_ejecutora, descripcion, ano, cuenta_contable_anticipo, monto_anticipo, fecha_anulacion) VALUES ";
			$q2.= "('".$JsonRec->reasignada."', '".$nroref."', '".$fecha."', ".$status.", ".$id_condicion_pago.", ".$fuente_financiamiento.", ".$id_tipo_solicitud_si.", ";
			$q2.= $monto_si.", ".$fecha_aprobacion.", ".$montodoc.", ".$montoret.", ".$montopagado.", '".$motivo."', ".$id_proveedor.", '".$id_unidad_ejecutora."', ";
			$q2.= "'".$descripcion."', ".$ano.", ".$cuenta_contable_anticipo.", ".$monto_anticipo.", ".$fecha_anulacion.")";*/
			//echo 'PRIMERA<br>';
			//echo $q2."<br>";
			$q2 = "UPDATE historico.movimientos_presupuestarios SET nrodoc = '$JsonRec->reasignada' WHERE nrodoc = '$JsonRec->destino'";
			//die($q2);
			$r2 = $conn->Execute($q2);
			//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
			//Relacion Orden de pago
			//$sql = "SELECT DISTINCT nrodoc FROM historico.relacion_movimientos WHERE nrodoc LIKE '$JsonRec->destino%'";
			//echo $sql."<br>";
			//$row = $conn->Execute($sql);
			//die('aqui '.substr($row->fields['nrodoc'],14));
			/*while(!$row->EOF){
				if(substr($row->fields['nrodoc'],14) == 'ANULADO'){
					$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '".$JsonRec->reasignada."-ANULADA' WHERE nrodoc = '".$row->fields['nrodoc']."'";
					echo $q3."<br>";
					//$r3 = $conn->Execute($q3);
				}else{
					$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->reasignada' WHERE nrodoc = '".$row->fields['nrodoc']."'";
					echo $q3."<br>";
					//$r3 = $conn->Execute($q3);
				}
				$row->movenext();
			
			}*/
			
			$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->reasignada' WHERE nrodoc = '$JsonRec->destino'";
			//echo $q3."<br>";
			$r3 = $conn->Execute($q3);
			
			$q7 = "INSERT INTO historico.reasignadas (id_old, id_new) VALUES ('$JsonRec->destino', '$JsonRec->reasignada')";
			//echo $q7."<br>";
			$r7 = $conn->Execute($q7);
						
			
			$sql2 = "SELECT nrodoc FROM historico.movimientos_presupuestarios WHERE nrodoc = '$JsonRec->origen'";
			$row2 = $conn->Execute($sql2);
			if($row2->RecordCount() > 0){
			//SE ASIGNA EL CORRELATIVO DESTINO EN EL DOCUMENTO DE ORIGEN
				$q4 = "UPDATE historico.movimientos_presupuestarios SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
				$r4 = $conn->Execute($q4);
				//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
				//Relacion Orden de pago
				
				/*$sql = "SELECT DISTINCT nrodoc FROM historico.relacion_movimientos WHERE nrodoc LIKE '$JsonRec->origen%'";
				$row = $conn->Execute($sql);
				while(!$row->EOF){
					if(substr($row->fields['nrodoc'],9) == 'ANULADO'){
						$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '".$JsonRec->destino."-ANULADA' WHERE nrodoc = '".$row->fields['nrodoc']."'";
						$r3 = $conn->Execute($q3);
					}else{
						$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '".$row->fields['nrodoc']."'";
						$r3 = $conn->Execute($q3);
					}
					$row->movenext();
				}*/
				
				$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
				$r3 = $conn->Execute($q3);
			}		
		}else{
			/*$q = "SELECT * FROM historico.orden_pago WHERE nrodoc = '".$JsonRec->origen."'";
			$r = $conn->Execute($q);
			$nroref = $r->fields['nroref'];
			$fecha = $r->fields['fecha'];
			$status = $r->fields['status'];
			$id_condicion_pago = $r->fields['id_condicion_pago'];
			$fuente_financiamiento = $r->fields['fuente_financiamiento'];
			$id_tipo_solicitud_si = $r->fields['id_tipo_solicitud_si'];
			$monto_si = $r->fields['monto_si'];
			$fecha_aprobacion = !empty($r->fields['fecha_aprobacion']) ? "'".$r->fields['fecha_aprobacion']."'" : 'null';
			$montodoc = $r->fields['montodoc'];
			$montoret = $r->fields['montoret'];
			$montopagado = $r->fields['montopagado'];
			$motivo = $r->fields['motivo'];
			$id_proveedor = $r->fields['id_proveedor'];
			$id_unidad_ejecutora = $r->fields['id_unidad_ejecutora'];
			$descripcion = $r->fields['descripcion'];
			$ano = $r->fields['ano'];
			$cuenta_contable_anticipo = !empty($r->fields['cuenta_contable_anticipo']) ? $r->fields['cuenta_contable_anticipo'] : 'null';
			$monto_anticipo = !empty($r->fields['monto_anticipo']) ? $r->fields['monto_anticipo'] : 'null';
			$fecha_anulacion = !empty($r->fields['fecha_anulacion']) ? "'".$r->fields['fecha_anulacion']."'" : 'null';
			//Inserto el registro con el numero de documento reasignado
			$q2 = "INSERT INTO historico.orden_pago (nrodoc, nroref, fecha, status, id_condicion_pago, fuente_financiamiento, id_tipo_solicitud_si, monto_si, fecha_aprobacion, ";
			$q2.= "montodoc, montoret, montopagado, motivo, id_proveedor, id_unidad_ejecutora, descripcion, ano, cuenta_contable_anticipo, monto_anticipo, fecha_anulacion) VALUES ";
			$q2.= "('".$JsonRec->destino."', '".$nroref."', '".$fecha."', ".$status.", ".$id_condicion_pago.", ".$fuente_financiamiento.", ".$id_tipo_solicitud_si.", ";
			$q2.= $monto_si.", ".$fecha_aprobacion.", ".$montodoc.", ".$montoret.", ".$montopagado.", '".$motivo."', ".$id_proveedor.", '".$id_unidad_ejecutora."' ";
			$q2.= "'".$descripcion."', ".$ano.", ".$cuenta_contable_anticipo.", ".$monto_anticipo.", ".$fecha_anulacion.")";*/
			//echo 'SEGUNDO<br>';
			
			$sql2 = "SELECT nrodoc FROM historico.movimientos_presupuestarios WHERE nrodoc = '$JsonRec->origen'";
			$row2 = $conn->Execute($sql2);
			if($row2->RecordCount() > 0){
			
				$q4 = "UPDATE historico.movimientos_presupuestarios SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
				$r4 = $conn->Execute($q4);
				//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
				//Relacion Orden de pago
				/*$sql = "SELECT DISTINCT nrodoc FROM historico.relacion_movimientos WHERE nrodoc LIKE '$JsonRec->destino%'";
				$row = $conn->Execute($sql);*/
				/*while(!$row->EOF){
					if(substr($row->fields['nrodoc'],9) == 'ANULADO'){
						$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '".$JsonRec->reasignada."-ANULADA' WHERE nrodoc = '".$row->fields['nrodoc']."'";
						$r3 = $conn->Execute($q3);
					}else{
						$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->reasignada' WHERE nrodoc = '".$row->fields['nrodoc']."'";
						$r3 = $conn->Execute($q3);
					}
					$row->movenext();
				}*/
				$q3 = "UPDATE historico.relacion_movimientos SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
				$r3 = $conn->Execute($q3);
			
			}	
			
		}
		echo 'Proceso de reasinacion realizado con exito';
	} else {
		echo 'El documento de orien fue reasignado';
	}	
} 
?>
