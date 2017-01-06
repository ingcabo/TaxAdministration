<?
class otros_pagos_anteriores{

	#PROPIEDADES#
	
	var $id;
	var $id_banco;
	var $banco;
	var $id_nro_cuenta;
	var $nro_cuenta;
	var $nro_otros_pagos;
	var $id_proveedor;
	var $proveedor;
	var $rif_proveedor;
	var $fecha;
	var $status;
	var $json;
	var $observacion;
	var $nrodoc;
	var $descripcion;
	var $nro_control;
	var $cerrado;
	
	function get($conn, $id){
	
		$q = "SELECT * FROM finanzas.otros_pagos_anteriores WHERE id ='$id' ";
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
			$this->nro_otros_pagos =	$r->fields['nro_otros_pagos'];	
			$this->status =	$r->fields['status'];
			$this->nrodoc =	$r->fields['nrodoc'];
			$this->observacion =	$r->fields['observacion'];
			$this->descripcion =	$r->fields['descripcion'];
			$this->nro_control =	$r->fields['nro_control'];
			
			$this->json = $this->getRelaciones($conn, $r->fields['nrodoc']);
			$this->cerrado = $r->fields['cerrado'];
			
			return true;	
					
		}else{
		
			return false;
		
		}
	
	}
	
	function get_all($conn,$orden="id"){
		
		$q = "SELECT * FROM finanzas.otros_pagos_anteriores ";
		$q.= "ORDER BY $orden ";
		$r = $conn->Execute($q);
		while(!$r->EOF){
			$ue = new otros_pagos;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn,$id_banco,$nro_cuenta,$nro_otros_pagos,$id_proveedor,$fecha,$status,$aOrdenes, $id_usuario, $eseEnEje,$anoCurso,$observacion,$total_cheque,$descripcion,$nro_control ){
		//VALIDO QUE LA CUENTA TENGA DISPONIBILIDAD
		//$q = "SELECT (saldo_inicial+creditos-debitos) AS disponible FROM finanzas.cuentas_bancarias WHERE id=$nro_cuenta AND id_banco=$id_banco";
		$oCuentasBancarias =  new cuentas_bancarias;
		$oCuentasBancarias->get($conn,$nro_cuenta);
		$saldo = $oCuentasBancarias->saldo_inicial + $oCuentasBancarias->creditos - $oCuentasBancarias->debitos;
		//$r = $conn->execute($q);
		if(saldo>=guardafloat($total_cheque)){
			$nrodoc = otros_pagos_anteriores::getNroDoc($conn);
			//die($nrodoc);
			$q = "INSERT INTO finanzas.otros_pagos_anteriores (id_banco,nro_cuenta,nro_otros_pagos,id_proveedor,fecha,status,nrodoc, id_escenario,observacion,descripcion,nro_control ) ";
			$q.= "VALUES ($id_banco,'$nro_cuenta','$nro_otros_pagos',$id_proveedor,'$fecha','$status','$nrodoc', '$eseEnEje','$observacion','$descripcion','$nro_control' ) ";
			//die($q);
			$r = $conn->execute($q);
			if($r){
				$this->addrelacion($conn, $nrodoc,$aOrdenes,$id_usuario,$anoCurso,$observacion,$fecha);
				$q="SELECT asiento_cheque ('$nrodoc'::varchar, 1::int2, 1::int2)";
				$conn->Execute($q);
				$this->msg = $r ?  REG_ADD_OK : ERROR_ADD;
			}
			
			
		}else{
			$this->msg = 'No hay disponibilidad en la cuenta bancaria';
		}
	}
	
	function anular($conn, $nrodoc, $escEnEje,$status,$observacion,$id_usuario,$anoCurso,$descripcion,$nro_control){
		if($status==1) {
			#CAMBIO EL ESTATUS DEL CHEQUE POR ANULADO#
			$q = "UPDATE finanzas.otros_pagos_anteriores SET  ";
			$q.= "status = 1, observacion='$observacion', descripcion='$descripcion', nro_control='$nro_control' ";
			$q.= "WHERE nrodoc='$nrodoc' ";	
			//die($q);
			$r =$conn->Execute($q);
			if($r){
				$q="SELECT asiento_cheque ('$nrodoc'::varchar, 1::int2, 1::int2)";
				$conn->Execute($q);
			}
			
	
			$q = "SELECT nroref,monto FROM finanzas.relacion_otros_pagos_anteriores WHERE nrodoc='$nrodoc'";
			$r = $conn->Execute($q);
			while(!$r->EOF){
				$nroOrden=$r->fields['nroref'];
				$monto=$r->fields['monto'];
				$q = "UPDATE historico.orden_pago SET montopagado=montopagado-$monto WHERE nrodoc='$nroOrden'";
				$conn->execute($q);
			}
		}else{
			#CAMBIO EL ESTATUS DEL CHEQUE POR ANULADO#
			$q = "UPDATE finanzas.otros_pagos SET  ";
			$q.= "observacion='$observacion',descripcion='$descripcion', nro_control='$nro_control' ";
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
				$q = "INSERT INTO finanzas.relacion_otros_pagos_anteriores (nrodoc, nroref, monto) VALUES ('$nrodoc','$orden[0]', $orden[1])";
				$r= $conn->execute($q);
				//ACTAULIZO EL MONTO PAGADO DE LA ORDEN DE PAGO
				$q = "UPDATE historico.orden_pago SET montopagado=montopagado+$orden[1] WHERE nrodoc='$orden[0]'";
				$r = $conn->execute($q);
			} 
		}
		if($r)
			return true;
		else
			return false;
	}
	function montoTotal($conn, $nrodoc){
	
		$q = "SELECT Sum(monto) as total FROM finanzas.relacion_otros_pagos_anteriores WHERE nrodoc =  '$nrodoc'";
		$r = $conn->execute($q);
		
		if($r)
			return $r->fields['total'];
		else
			return false;
	}
	function buscar($conn, $id_proveedor, $id_banco, $fecha_desde, $fecha_hasta, $nrodoc,$cuenta, $orden="id", $from, $max){
		
			if(empty($id_proveedor) && empty($id_banco) && empty($fecha_desde) && empty($fecha_hasta)
				&& empty($nrodoc) && empty($cuenta))
			
				return false;
			
			$q = 	"SELECT * from finanzas.otros_pagos_anteriores  ";
				
			$q.= 	"WHERE  1=1 ";
			$q.= 	!empty($nrodoc) ? "AND otros_pagos.nrodoc='$nrodoc' ": "";
			$q.= 	!empty($fecha_desde) ? "AND otros_pagos.fecha >='".guardafecha($fecha_desde)."' ": "";
			$q.= 	!empty($fecha_hasta) ? "AND otros_pagos.fecha <='".guardafecha($fecha_hasta)."' ": "";
			$q.= 	!empty($id_proveedor) ? "AND otros_pagos.id_proveedor = '$id_proveedor'  ":"";
			$q.= 	!empty($id_banco) ? "AND otros_pagos.id_banco = '$id_banco'  ":"";
			$q.= 	!empty($cuenta) ? "AND otros_pagos.nro_cuenta = '$cuenta'  ":"";
			$q.= 	"ORDER BY otros_pagos.$orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
			if(!r || $r->EOF)
				return false;
			$collection=array();
		while(!$r->EOF){
			$ue = new otros_pagos;
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
			
		$q = 	"SELECT * from finanzas.otros_pagos_anteriores  ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND otros_pagos.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($fecha_desde) ? "AND otros_pagos.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND otros_pagos.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_proveedor) ? "AND otros_pagos.id_proveedor = '$id_proveedor'  ":"";
		$q.= 	!empty($id_banco) ? "AND otros_pagos.id_banco = '$id_banco'  ":"";
		$q.= 	!empty($cuenta) ? "AND otros_pagos.nro_cuenta = '$cuenta'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getRelaciones($conn, $nrodoc){
	
		$q="SELECT A.nroref,A.monto,(B.montodoc-B.montoret-B.montopagado) AS montopagar,B.montopagado AS montopagado FROM finanzas.relacion_otros_pagos_anteriores AS A INNER JOIN finanzas.orden_pago AS B ON A.nroref=B.nrodoc WHERE A.nrodoc='$nrodoc'";
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
		$q = "SELECT max(nrodoc) AS nrodoc FROM finanzas.otros_pagos  ";
		$r = $conn->execute($q);
		$q2 = "SELECT MAX(nrodoc) AS nrodoc FROM finanzas.otros_pagos_anteriores ";
		$r2 = $conn->Execute($q2);
		if($r->fields['nrodoc']>=$r2->fields['nrodoc']){
			$resp = $r->fields['nrodoc'];
		} else {
			$resp = $r2->fields['nrodoc'];
		}
		//die($r->fields['nrodoc']);
		return "017-".str_pad(substr($resp, 4, 4) + 1, 4, 0, STR_PAD_LEFT);
	}
	
}

?>