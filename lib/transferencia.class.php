<?
class transferencia{

	#PROPIEDADES#
	
	var $id;
	var $nrodoc;
	var $origen;
	var $status;
	var $id_escenario;
	var $descripcion;
	var $id_cuenta_cedente;
	var $id_cuenta_receptora;
	var $fecha;
	
	function get($conn, $id){
	
		$q = "SELECT * FROM finanzas.transferencias WHERE id ='$id' ";
		$r = $conn->execute($q);
		
		if (!$r->EOF){
			$this->id =	$r->fields['id'];
			$this->nrodoc	= $r->fields['nrodoc'];
			$this->origen	= $r->fields['origen'];		
			$this->status	= $r->fields['status'];
			$this->status_nombre = $this->status==0 ?'Registrado':'Anulado'; 
			$this->descripcion =	$r->fields['descripcion'];
			$this->id_cuenta_cedente =	$r->fields['id_cuenta_cedente'];
			$q = "SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb 
				INNER JOIN public.banco as b ON cb.id_banco=b.id  WHERE cb.id =".$r->fields['id_cuenta_cedente']."order by descripcion";
			$rCedente = $conn->Execute($q);
			$this->cedente = $rCedente->fields['descripcion'];
			$this->id_cuenta_receptora = $r->fields['id_cuenta_receptora'];
			$q = "SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb 
				INNER JOIN public.banco as b ON cb.id_banco=b.id  WHERE cb.id =".$r->fields['id_cuenta_receptora']."order by descripcion";

			$rReceptora = $conn->Execute($q);
			$this->receptora = $rReceptora->fields['descripcion'];
			$this->fecha = muestrafecha($r->fields['fecha']);
			$this->monto = muestrafloat($r->fields['monto']);			
			return true;	
					
		}else{
			return false;
		}
	}
	
	function get_all($conn,$orden="id"){
		
		$q = "SELECT * FROM finanzas.transferencias ";
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
	
	function add($conn, $nrodoc, $origen, $status, $eseEnEje, $descripcion, $id_cuenta_cedente, $id_cuenta_receptora, $fecha,$monto){
			//die($nrodoc);
			$q = "INSERT INTO finanzas.transferencias (nrodoc,origen,status,id_escenario,descripcion,id_cuenta_cedente,id_cuenta_receptora, fecha, monto) ";
			$q.= "VALUES ('$nrodoc','$origen',$status, '$eseEnEje', '$descripcion',$id_cuenta_cedente, $id_cuenta_receptora, '$fecha', $monto ) ";
			//die($q);
			$r = $conn->execute($q);
			$this->msg = $r ?  REG_ADD_OK : ERROR_ADD;
			if($r){
				$q="SELECT public.asiento_cheque ('$nrodoc'::varchar, 2::int2, $eseEnEje::int8)";
				$conn->Execute($q);
				$this->msg = $r ?  REG_ADD_OK : ERROR_ADD;
			}
	}
	
	function anular($conn, $id, $nrodoc, $origen, $status, $eseEnEje, $descripcion, $id_cuenta_cedente, $id_cuenta_receptora, $fecha){
		if($status==1) {
			#CAMBIO EL ESTATUS DEL TRANSFERENCIA POR ANULADO#
			$q = "UPDATE finanzas.transferencias SET  ";
			$q.= "status = 1, descripcion='$descripcion', fecha='$fecha'";
			$q.= "WHERE id='$id' ";	
			//die($q);
			$r =$conn->Execute($q);
			if($r){
				$q="SELECT asiento_cheque ('$nrodoc'::varchar, 2::int2, $eseEnEje::int8)";
				$conn->Execute($q);
			}

		}else{
			#ACTUALIZO TRANSFERENCIA#
			$q = "UPDATE finanzas.transferencias SET  ";
			$q.= "descripcion='$descripcion', fecha='$fecha' ";
			$q.= "WHERE id='$id' ";	
			//die($q);
			$r =$conn->Execute($q);
		}
		if($r){
			$this->msg = OK;
		}else{
			$this->msg = ERROR;
		}				 
	}


	function buscar($conn, $id_cedente, $id_receptora, $fecha_desde, $fecha_hasta, $nrodoc,  $tdoc, $orden="id", $from, $max){
		
			if(empty($id_cedente) && empty($id_receptora) && empty($fecha_desde) && empty($fecha_hasta)
				&& empty($nrodoc) && empty($tdoc))
			
				return false;
			
			$q = 	"SELECT * from finanzas.transferencias AS T ";
				
			$q.= 	"WHERE  1=1 ";
			$q.= 	!empty($nrodoc) ? "AND T.nrodoc='$nrodoc' ": "";
			$q.= 	!empty($tdoc) ? "AND T.origen='$tdoc' ": "";
			$q.= 	!empty($fecha_desde) ? "AND T.fecha >='".guardafecha($fecha_desde)."' ": "";
			$q.= 	!empty($fecha_hasta) ? "AND T.fecha <='".guardafecha($fecha_hasta)."' ": "";
			$q.= 	!empty($id_cedente) ? "AND T.id_cuenta_cedente = '$id_cedente'  ":"";
			$q.= 	!empty($id_receptora) ? "AND T.id_cuenta_receptora = '$id_receptora'  ":"";
			$q.= 	"ORDER BY T.$orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
			if(!r || $r->EOF)
				return false;
			$collection=array();
		while(!$r->EOF){
			$ue = new transferencia;
			$ue->get($conn, $r->fields['id']);			
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function totalRegsBusqueda($conn, $id_cedente, $id_receptora, $fecha_desde, $fecha_hasta, $nrodoc,$tdoc)
	{
			if(empty($id_cedente) && empty($id_receptora) && empty($fecha_desde) && empty($fecha_hasta)
				&& empty($nrodoc) && empty($tdoc))
					return 0;
			
		$q = 	"SELECT * from finanzas.transferencias AS T ";
		$q.= 	"WHERE  1=1 ";
		$q.= 	!empty($nrodoc) ? "AND T.nrodoc='$nrodoc' ": "";
		$q.= 	!empty($nrocontrol) ? "AND T.origen='$tdoc' ": "";
		$q.= 	!empty($fecha_desde) ? "AND T.fecha >='".guardafecha($fecha_desde)."' ": "";
		$q.= 	!empty($fecha_hasta) ? "AND T.fecha <='".guardafecha($fecha_hasta)."' ": "";
		$q.= 	!empty($id_cedente) ? "AND T.id_cuenta_cedente = '$id_cedente'  ":"";
		$q.= 	!empty($id_receptora) ? "AND T.id_cuenta_receptora = '$id_receptora'  ":"";
		//die($q);
		$r = $conn->Execute($q);
		
		return $r->RecordCount();
	}
	
	function getNroDoc($conn){
		$q = "SELECT max(nrodoc) AS nrodoc FROM finanzas.transferencias  ";
		$r = $conn->execute($q);
		//die($r->fields['nrodoc']);
		return "017-".str_pad(substr($r->fields['nrodoc'], 4, 4) + 1, 4, 0, STR_PAD_LEFT);
	}
	
}

?>