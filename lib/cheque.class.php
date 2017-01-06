<?
class cheque{

	#PROPIEDADES#
	
	var $id;
	var $id_banco;
	var $banco;
	var $id_nro_cuenta;
	var $nro_cuenta;
	var $nro_cheque;
	var $id_proveedor;
	var $proveedor;
	var $rif_proveedor;
	var $fecha;
	var $status;
	var $json;
	var $observacion;
	var $nrodoc;
	var $nomBenef;
	var $concepto;
	var $cerrado;

	function get($conn, $id){
	
		$q = "SELECT * FROM finanzas.cheques WHERE id ='$id' ";
		$r = $conn->execute($q);
		
		if (!$r->EOF){
		
			$this->id =	$r->fields['id'];
			$this->id_banco	= $r->fields['id_banco'];			
			$ban = new banco;
			$ban->get($conn, $r->fields['id_banco']);
			$this->banco = $ban->descripcion;
			$cue = new cuentas_bancarias;
			$this->id_nro_cuenta=	$r->fields['nro_cuenta'];
			$cue->get($conn, $r->fields['nro_cuenta']);
			$this->nro_cuenta	=	$cue->nro_cuenta;
			$this->fecha = $r->fields['fecha'];
			$cue = new proveedores;
			$this->id_proveedor=	$r->fields['id_proveedor'];
			$cue->get($conn, $r->fields['id_proveedor']);
			$this->proveedor = $cue->nombre;
			$this->rif_proveedor = $cue->rif;
			$this->nro_cheque =	$r->fields['nro_cheque'];	
			$this->status =	$r->fields['status'];
			$this->nrodoc =	$r->fields['nrodoc'];
			$this->observacion =	$r->fields['observacion'];
			$this->json = $this->getRelaciones($conn, $r->fields['nrodoc']);
			$this->nomBenef = $r->fields['nombre_beneficiario'];
			$this->concepto = $r->fields['concepto'];
			$this->cerrado = $r->fields['cerrado'];
			return true;	
					
		}else{
		
			return false;
		
		}
	
	}
	
	function get_all($conn,$orden="id"){
		
		$q = "SELECT * FROM finanzas.cheque ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new cheque;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn,$id_banco,$nro_cuenta,$nro_cheque,$id_proveedor,$fecha,$status,$aOrdenes, $id_usuario, $eseEnEje,$anoCurso,$observacion,$total_cheque,$nomBenef,$concepto ){
		//VALIDO QUE LA CUENTA TENGA DISPONIBILIDAD
		$oCuentasBancarias =  new cuentas_bancarias;
		$oCuentasBancarias->get($conn,$nro_cuenta);
		$saldo = $oCuentasBancarias->saldo_inicial + $oCuentasBancarias->creditos - $oCuentasBancarias->debitos;
		//die('ch: '.$total_cheque.' saldo: '.$saldo);
		$saldo = 9999999999;
		if($saldo>=guardafloat($total_cheque)){
			$nrodoc = cheque::getNroDoc($conn);
			//die($nrodoc);
			$q = "INSERT INTO finanzas.cheques (id_banco,nro_cuenta,nro_cheque,id_proveedor,fecha,status,nrodoc, id_escenario,observacion,nombre_beneficiario,concepto ) ";
			$q.= "VALUES ($id_banco,'$nro_cuenta','$nro_cheque',$id_proveedor,'$fecha','$status','$nrodoc', '$eseEnEje','$observacion','$nomBenef','$concepto' ) ";
			//die($q);
			$r = $conn->execute($q);
			$r=true;
			if($r){
				$this->addrelacion($conn, $nrodoc,$aOrdenes,$id_usuario,$anoCurso,$observacion,$fecha);
				$q="SELECT public.asiento_cheque ('$nrodoc'::varchar, 0::int2, $eseEnEje::int8)";
				$conn->Execute($q);
				$q = "UPDATE finanzas.control_chequera SET ultimo_cheque=$nro_cheque WHERE nro_cuenta=$nro_cuenta";
				$r = $conn->execute($q);
				
			}
			$this->msg = $r ?  REG_ADD_OK : ERROR_ADD;
			
		}else{
			$this->msg = 'No hay disponibilidad en la cuenta bancaria';
		}
	}
	
	function anular($conn, $nrodoc, $escEnEje,$status,$observacion,$id_usuario,$anoCurso,$nomBenef,$concepto,$fecha){
		if($status==1) {
			#CAMBIO EL ESTATUS DEL CHEQUE POR ANULADO#
			$q = "UPDATE finanzas.cheques SET  ";
			$q.= "status = 1, observacion='$observacion' ";
			$q.= "WHERE nrodoc='$nrodoc' ";	
			//die($q);
			$r =$conn->Execute($q);
			if($r){
				$q="SELECT public.asiento_cheque ('$nrodoc'::varchar, 0::int2, $escEnEje::int8)";
				//echo $q;
				$conn->Execute($q);
			}
			
	
			$q = "SELECT nroref,monto FROM finanzas.relacion_cheque WHERE nrodoc='$nrodoc'";
			$r = $conn->Execute($q);
			while(!$r->EOF){
				$nroOrden=$r->fields['nroref'];
				$monto=$r->fields['monto'];
				$q = "UPDATE finanzas.orden_pago SET montopagado=montopagado-$monto WHERE nrodoc='$nroOrden'";
				$conn->execute($q);
				
				$q = "SELECT montodoc,montoret,id_unidad_ejecutora,id_proveedor,nroref FROM finanzas.orden_pago WHERE nrodoc='$nroOrden' ";
				$rM = $conn->Execute($q);
				if($rM->fields['nroref']){
					$idUniEje=$rM->fields['id_unidad_ejecutora'];
					$idProveedor=$rM->fields['id_proveedor'];
					$porc=$monto*100/($rM->fields['montodoc']-$rM->fields['montoret']);
					$fecha=date("Y-m-d");
	
					$q = "INSERT INTO movimientos_presupuestarios ";
					$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref,nroref, status_movimiento,  ";//nroref, 
					$q.= "fechadoc, fecharef, status, id_proveedor) ";
					$q.= "VALUES ";
					$q.= "($id_usuario, '$idUniEje', '$anoCurso', '$observaciones', '$nrodoc', '005', '','$nrodoc-ANULADO',2, ";//'$nroref', 
					$q.= " '$fecha', '$fecha', '3', $idProveedor) ";
					//die($q);
					$conn->Execute($q);
	
					$q = "SELECT * FROM finanzas.relacion_orden_pago WHERE id_orden_pago='$nroOrden' ";
					$rP = $conn->Execute($q);
					while(!$rP->EOF){
						$MontoImputar=($rP->fields['monto']*$porc/100);
						movimientos_presupuestarios::add_relacion_nomina($conn,
							$rP->fields['id_parcat'],
							$rP->fields['id_categoria_programatica'],
							$rP->fields['id_partida_presupuestaria'],	
							$nrodoc, 
							(-1*$MontoImputar));  
						relacion_pp_cp::set_desde_pagado_cheque_anulado($conn, $rP->fields['id_parcat'], $MontoImputar);
						$rP->movenext();
					}
				}
				$r->movenext();
			}
		}else{
			#CAMBIO EL ESTATUS DEL CHEQUE POR ANULADO#
			$q = "UPDATE finanzas.cheques SET  ";
			$q.= "observacion='$observacion', nombre_beneficiario = '$nomBenef', concepto = '$concepto', fecha = '$fecha'  ";
			$q.= "WHERE nrodoc='$nrodoc' ";	
			//die($q);
			$r =$conn->Execute($q);
		}
		if($r){
			$this->msg = OK;
		}else{
			$this->msg = ERROR;
		}				 
	}
	function addrelacion($conn, $nrodoc,$Ordenes,$id_usuario,$anoCurso,$observacion,$fecha){
		$JsonRec = new Services_JSON();
		$JsonRec = $JsonRec->decode(str_replace("\\","",$Ordenes));
		if(is_array($JsonRec->ordenes)){
			foreach($JsonRec->ordenes as $orden){
				//INSERTO LA RELACION DETALLE DEL CHEQUE
				$q = "INSERT INTO finanzas.relacion_cheque (nrodoc, nroref, monto) VALUES ('$nrodoc','$orden[0]', $orden[1])";
				//echo $q."<br>";
				$r= $conn->execute($q);
				//ACTAULIZO EL MONTO PAGADO DE LA ORDEN DE PAGO
				$q = "UPDATE finanzas.orden_pago SET montopagado=montopagado+$orden[1] WHERE nrodoc='$orden[0]'";
				//echo $q."<br>";
				$r = $conn->execute($q);
				//REALIZO LA IMPUTACION PRESUPESTARIA
				$q = "SELECT montodoc,montoret,id_unidad_ejecutora,id_proveedor,nroref FROM finanzas.orden_pago WHERE nrodoc='$orden[0]' ";
				//echo $q."<br>";
				//die($q);
				$rM = $conn->Execute($q);
				if($rM->fields['nroref']){
					$idUniEje=$rM->fields['id_unidad_ejecutora'];
					$idProveedor=$rM->fields['id_proveedor'];
					$porc=$orden[1]*100/($rM->fields['montodoc']-$rM->fields['montoret']);
	
					//$fecha=date("Y-m-d");
	
					$q = "INSERT INTO movimientos_presupuestarios ";
					$q.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, tipref,nroref, ";//nroref, 
					$q.= "fechadoc, fecharef, status, id_proveedor) ";
					$q.= "VALUES ";
					$q.= "($id_usuario, '$idUniEje', '$anoCurso', '$observacion', '$nrodoc', '005', '','$orden[0]', ";//'$nroref', 
					$q.= " '$fecha', '$fecha', '3', $idProveedor) ";
					$r = $conn->Execute($q);
					//echo $q."<br>";
					$q = "SELECT * FROM finanzas.relacion_orden_pago WHERE id_orden_pago='$orden[0]' ";
					$r = $conn->Execute($q);
					while(!$r->EOF){
						$MontoImputar=$r->fields['monto']*$porc/100;
						movimientos_presupuestarios::add_relacion_nomina($conn,
							$r->fields['id_parcat'],
							$r->fields['id_categoria_programatica'],
							$r->fields['id_partida_presupuestaria'],	
							$nrodoc, 
							$MontoImputar);  
						relacion_pp_cp::set_desde_pagado_cheque($conn, $r->fields['id_parcat'], $MontoImputar);
						$r->movenext();
					}
				}
			} 
		}
		if($r)
			return true;
		else
			return false;
	}
	/*
	function actualizarMovimientos($conn, $nroref, $nrodoc, $id_usuario){
	
		$q="SELECT relacion_solicitud_pago.id_solicitud_pago,
			finanzas.relacion_orden_pago.nrodoc,
			relacion_solicitud_pago.id_categoria_programatica,
			relacion_solicitud_pago.id_partida_presupuestaria,
			relacion_solicitud_pago.monto,
			relacion_solicitud_pago.id_parcat
			FROM
			finanzas.relacion_orden_pago
			Inner Join puser.relacion_solicitud_pago ON 

			finanzas.relacion_orden_pago.nroref = relacion_solicitud_pago.id_solicitud_pago
			WHERE finanzas.relacion_orden_pago.nrodoc =  '$nroref'";
		//die($q);
		
		##ISMAEL DEPABLOS 28/12/2006
		##ESPERAR A REVISAR EL PROCESO DEL PAGO DE CHEQUES PARA SEGUIR MODIFICANDO ESTE MODULO
		
		$r = $conn->execute($q);	

			$ob = new movimientos_presupuestarios;
			$ob->getNroRef($conn,$r->fields['id_solicitud_pago'], 2);
			
			#REGISTRO EL CHEQUE EN MOVIMIENTOS PRESUPUESTARIOS#	
			$q2 = "INSERT INTO movimientos_presupuestarios ";
			$q2.= "(id_usuario, id_unidad_ejecutora, ano, descripcion, nrodoc, tipdoc, nroref,"; 
			$q2.= "fechadoc, fecharef, status, id_proveedor) ";
			$q2.= "VALUES ";
			$q2.= "('$id_usuario', '".$ob->id_unidad_ejecutora."', '".date('Y')."', '".$ob->descripcion."', '".$nrodoc."', '005', '$nroref', "; 
			$q2.= " '".date('Y-m-d')."', '".date('Y-m-d')."', '3', '".$ob->id_proveedor."') ";
			//echo $q2."<br>";
			//die($q2);
			$r2 = $conn->Execute($q2) or die($q2);
	}
	function addRelacionesMov($conn, $nrodoc, $monto_total, $monto_pagado, $nroref, $escEnEje){
		
		$q="SELECT relacion_solicitud_pago.id_solicitud_pago,
			finanzas.relacion_orden_pago.nrodoc,
			relacion_solicitud_pago.id_categoria_programatica,
			relacion_solicitud_pago.id_partida_presupuestaria,
			relacion_solicitud_pago.monto,
			relacion_solicitud_pago.id_parcat
			FROM
			finanzas.relacion_orden_pago
			Inner Join puser.relacion_solicitud_pago ON 
			finanzas.relacion_orden_pago.nroref = relacion_solicitud_pago.id_solicitud_pago
			WHERE finanzas.relacion_orden_pago.nrodoc =  '$nrodoc'";
		//die($q);
		$r = $conn->execute($q);	
		$ob = new movimientos_presupuestarios;
		if($aRelaciones = $ob->get_relaciones($conn,$r->fields['id_solicitud_pago'], $escEnEje)){
			foreach($aRelaciones as $relacion){
			
				$total_partidas = ($monto_pagado / $monto_total) * $relacion->monto;
				//die(muestraFloat($total_partidas));
				$aIdParCat[] 				= $relacion->idParCat;
				$aCategoriaProgramatica[] 	= $relacion->id_categoria_programatica;
				$aPartidaPresupuestaria[] 	= $relacion->id_partida_presupuestaria;
				$aMonto[] 					= muestraFloat($total_partidas);
			
			}
			
			movimientos_presupuestarios::add_relacion($conn,
						$aIdParCat,
						$aCategoriaProgramatica,
						$aPartidaPresupuestaria,
						$nroref,
						$aMonto);
		return true;
		}else{
			return false;
		}
	} 	*/
	function montoTotalCheque($conn, $nrodoc){
	
		$q = "SELECT Sum(monto) as total FROM finanzas.relacion_cheque WHERE nrodoc =  '$nrodoc'";
		$r = $conn->execute($q);
		
		if($r)
			return $r->fields['total'];
		else
			return false;
	}
	function buscar($conn, $id_proveedor, $id_banco, $fecha_desde, $fecha_hasta, $nrodoc, $nrocheque, $cuenta, $orden="id", $from, $max)
	{
		if(empty($id_proveedor) && empty($id_banco) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($nrocheque) && empty($cuenta))
			return false;
			
		$q = 	"SELECT * from finanzas.cheques  ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND cheques.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($nrocheque) ? "AND cheques.nro_cheque='$nrocheque' ": "";
		$q.= 	!empty($fecha_desde) ? "AND cheques.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND cheques.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_proveedor) ? "AND cheques.id_proveedor = '$id_proveedor'  ":"";
		$q.= 	!empty($id_banco) ? "AND cheques.id_banco = '$id_banco'  ":"";
		$q.= 	!empty($cuenta) ? "AND cheques.nro_cuenta = '$cuenta'  ":"";
		$q.= 	"ORDER BY cheques.$orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		if(!r || $r->EOF)
			return false;
			
		$collection=array();
		while(!$r->EOF){
			$ue = new cheque;
			$ue->get($conn, $r->fields['id']);			
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegsBusqueda($conn, $id_proveedor, $id_banco, $fecha_desde, $fecha_hasta, $nrodoc,$cuenta)
	{
		if(empty($id_proveedor) && empty($id_banco) && empty($fecha_desde) && empty($fecha_hasta) && empty($nrodoc) && empty($cuenta))
			return 0;
			
		$q = 	"SELECT * from finanzas.cheques  ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND cheques.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($nrocheque) ? "AND cheques.nro_cheque='$nrocheque' ": "";
		$q.= 	!empty($fecha_desde) ? "AND cheques.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND cheques.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_proveedor) ? "AND cheques.id_proveedor = '$id_proveedor'  ":"";
		$q.= 	!empty($id_banco) ? "AND cheques.id_banco = '$id_banco'  ":"";
		$q.= 	!empty($cuenta) ? "AND cheques.nro_cuenta = '$cuenta'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getRelaciones($conn, $nrodoc){
	
		$q="SELECT A.nroref,A.monto,(B.montodoc-B.montoret-B.montopagado) AS montopagar,B.montopagado AS montopagado FROM finanzas.relacion_cheque AS A INNER JOIN finanzas.orden_pago AS B ON A.nroref=B.nrodoc WHERE A.nrodoc='$nrodoc'";
		$r= $conn->execute($q);
		
		while(!$r->EOF){
		
			$ch = new cheque;
			$ch->nroref = $r->fields['nroref'];
			$ch->montopagar = $r->fields['montopagar'];
			$ch->montopagado = $r->fields['montopagado'];
			$ch->monto 	= $r->fields['monto'];
			$coleccion[] = $ch;
			$r->movenext();
		
		}
		
		$json = new Services_JSON();
		return $json->encode($coleccion);
	
	}
	function getNroDoc($conn){
		$q = "SELECT max(nrodoc) AS nrodoc FROM finanzas.cheques  ";
		$r = $conn->execute($q);
		$q2 = "SELECT MAX(nrodoc) AS nrodoc FROM finanzas.cheques_anteriores ";
		$r2 = $conn->Execute($q2);
		if($r->fields['nrodoc']>=$r2->fields['nrodoc']){
			$resp = $r->fields['nrodoc'];
		} else {
			$resp = $r->fields['nrodoc'];
		}
		return "005-".str_pad(substr($r->fields['nrodoc'], 4, 4) + 1, 4, 0, STR_PAD_LEFT);
	}
	
	function addLote($conn, $id_banco, $nro_cuenta,$nro_cheque_desde, $nro_cheque_hasta, $id_proveedor,$fecha, $eseEnEje,$observacion){
		for($i=$nro_cheque_desde;$i<=$nro_cheque_hasta;$i++){
			$nrodoc = cheque::getNroDoc($conn);			
			$q = "INSERT INTO finanzas.cheques (id_banco,nro_cuenta,nro_cheque,id_proveedor,fecha,status,nrodoc, id_escenario,observacion,nombre_beneficiario,concepto ) ";
			$q.= "VALUES ($id_banco,'$nro_cuenta','$i','761','$fecha','1','$nrodoc', '1111','$observacion','','') ";
			//die($q);
			$r = $conn->execute($q);	
		}
		$this->msg = $r ?  REG_ADD_OK : ERROR_ADD;
	}
	
	
	function revisaLote($conn, $id_banco, $nro_cuenta,$nro_cheque_desde, $nro_cheque_hasta){
		$coleccion = array();
		for($i=$nro_cheque_desde;$i<=$nro_cheque_hasta;$i++){
		$q = "SELECT nro_cheque  FROM finanzas.cheques WHERE id_banco=$id_banco AND nro_cuenta=$nro_cuenta AND nro_cheque = $i ";
		$r = $conn->execute($q);
		if (!$r->EOF){
			$coleccion[]=$r->fields['nro_cheque'];
			}
		}
		//die($q);
		return $coleccion;
	}
}
?>