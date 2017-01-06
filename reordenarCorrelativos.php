<?
//include('adodb/adodb-exceptions.inc.php'); 
set_time_limit(0);
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonEnv = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	//CONSULTO SI EL DOCUMENTO DE ORIGEN AUN ESTA DISPONIBLE
	$sql = "SELECT nrodoc FROM historico.orden_pago WHERE nrodoc = '$JsonRec->origen'";
	$row = $conn->Execute($sql);
	if($row->RecordCount()>0){
		//die(var_dump($JsonRec));
		// Inserto los registros en el correlativo reasignado
		$q = "SELECT * FROM historico.orden_pago WHERE nrodoc = '".$JsonRec->destino."'";
		//die($q);
		$r = $conn->Execute($q);
		if($r->RecordCount()>0){
			//Si hay una orden de pago en el correlativo destino se reasigna correlativo a esta
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
			$q2.= "('".$JsonRec->reasignada."', '".$nroref."', '".$fecha."', ".$status.", ".$id_condicion_pago.", ".$fuente_financiamiento.", ".$id_tipo_solicitud_si.", ";
			$q2.= $monto_si.", ".$fecha_aprobacion.", ".$montodoc.", ".$montoret.", ".$montopagado.", '".$motivo."', ".$id_proveedor.", '".$id_unidad_ejecutora."', ";
			$q2.= "'".$descripcion."', ".$ano.", ".$cuenta_contable_anticipo.", ".$monto_anticipo.", ".$fecha_anulacion.")";
			//echo 'PRIMERA<br>';
			//echo $q2."<br>";
			$r2 = $conn->Execute($q2);
			//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
			//Relacion Orden de pago
			$q3 = "UPDATE historico.relacion_orden_pago SET id_orden_pago = '$JsonRec->reasignada' WHERE id_orden_pago = '$JsonRec->destino'";
			//echo $q3."<br>";
			$r3 = $conn->Execute($q3);
			//Contabilidad
			$q4 = "UPDATE contabilidad.com_enc SET num_doc = '$JsonRec->reasignada' WHERE num_doc = '$JsonRec->destino'";
			//echo $q4."<br>";
			$r4 = $conn->Execute($q4);
			//Cheque
			$q5 = "UPDATE historico.relacion_cheque SET nroref = '$JsonRec->reasignada' WHERE nroref = '$JsonRec->destino'";
			//echo $q5."<br>";
			$r5 = $conn->Execute($q5);
			//Otros Pagos
			$q6 = "UPDATE historico.relacion_otros_pagos SET nroref = '$JsonRec->reasignada' WHERE nroref = '$JsonRec->destino'";
			//echo $q6."<br>";
			$r6 = $conn->Execute($q6);
			//Guarda el nro de documento que fue reasignado
			$q7 = "INSERT INTO historico.reasignadas (id_old, id_new) VALUES ('$JsonRec->destino', '$JsonRec->reasignada')";
			//echo $q7."<br>";
			$r7 = $conn->Execute($q7);
			//Eliminamos la orden de pago de destino para que no se duplique duplico
			$q8 = "DELETE FROM historico.orden_pago WHERE nrodoc = '$JsonRec->destino'";
			//echo $q8."<br>";
			$r8 = $conn->Execute($q8);
			
			//Se actualiza el correlativo destino con los datos del documento de origen
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
			$fecha_anulacion = !empty($r->fields['fecha_anulacion']) ? "'".$r->fields['fecha_anulacion']."'" : 'null';*/
			//Inserto el registro con el numero de documento reasignado
			/*$q2 = "UPDATE historico.orden_pago SET (nrodoc = '$JsonRec->origen', nroref = '$nroref', fecha = '$fecha', status = $status, id_condicion_pago = $id_condicion_pago, ";
			$q2.= "fuente_financiamiento = $fuente_financiamiento, id_tipo_solicitud_si = $id_tipo_solicitus_si, monto_si = $monto_si, fecha_aprobacion = $fecha_aprobacion, ";
			$q2.= "montodoc = $montodoc, montoret = $montoret, montopagado = $montopagado, motivo = '$motivo', id_proveedor = $id_proveedor, id_unidad_ejecutora = '$id_unidad_ejecutora', ";
			$q2.= "descripcion = '$descripcion', ano = $ano, cuenta_contable_anticipo = $cuenta_contable_anticipo, monto_anticipo = $monto_anticipo, fecha_anulacion = $fecha_anulacion) ";
			$q2.= "WHERE"*/
			
			//SE ASIGNA EL CORRELATIVO DESTINO EN EL DOCUMENTO DE ORIGEN
			$q2 = "UPDATE historico.orden_pago SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
			//echo $q2."<br>";
			$r2 = $conn->Execute($q2);
			//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
			//Relacion Orden de pago
			$q3 = "UPDATE historico.relacion_orden_pago SET id_orden_pago = '$JsonRec->destino' WHERE id_orden_pago = '$JsonRec->origen'";
			//echo $q3."<br>";
			$r3 = $conn->Execute($q3);
			//Contabilidad
			$q4 = "UPDATE contabilidad.com_enc SET num_doc = '$JsonRec->destino' WHERE num_doc = '$JsonRec->origen'";
			//echo $q4."<br>";
			$r4 = $conn->Execute($q4);
			//Cheque
			$q5 = "UPDATE historico.relacion_cheque SET nroref = '$JsonRec->destino' WHERE nroref = '$JsonRec->origen'";
			//echo $q5."<br>";
			$r5 = $conn->Execute($q5);
			//Otros Pagos
			$q6 = "UPDATE historico.relacion_otros_pagos SET nroref = '$JsonRec->destino' WHERE nroref = '$JsonRec->origen'";
			//echo $q6."<br>";
			$r6 = $conn->Execute($q6);
			//SE ELIMINA EL CORRELATIVO ORIGEN
			$q8 = "DELETE FROM historico.orden_pago WHERE nrodoc = '$JsonRec->origen'";
			//echo $q8."<br>";
			$r8 = $conn->Execute($q8);
			
			
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
			$q2 = "UPDATE historico.orden_pago SET nrodoc = '$JsonRec->destino' WHERE nrodoc = '$JsonRec->origen'";
			
			//echo $q2."<br>";
			$r2 = $conn->Execute($q2);
			//Actualizo el nro de docuemnto en todas las tablas donde este relacionado
			//Relacion Orden de pago
			$q3 = "UPDATE historico.relacion_orden_pago SET id_orden_pago = '$JsonRec->destino' WHERE id_orden_pago = '$JsonRec->origen'";
			//echo $q3."<br>";
			$r3 = $conn->Execute($q3);
			//Contabilidad
			$q4 = "UPDATE contabilidad.com_enc SET num_doc = '$JsonRec->destino' WHERE num_doc = '$JsonRec->origen'";
			//echo $q4."<br>";
			$r4 = $conn->Execute($q4);
			//Cheque
			$q5 = "UPDATE historico.relacion_cheque SET nroref = '$JsonRec->destino' WHERE nroref = '$JsonRec->origen'";
			//echo $q5."<br>";
			$r5 = $conn->Execute($q5);
			//Otros Pagos
			$q6 = "UPDATE historico.relacion_otros_pagos SET nroref = '$JsonRec->destino' WHERE nroref = '$JsonRec->origen'";
			//echo $q6."<br>";
			$r6 = $conn->Execute($q6);
			
		}
		echo 'Proceso de reasinacion realizado con exito';
	} else {
		echo 'El documento de orien fue reasignado';
	}	
} 
?>
