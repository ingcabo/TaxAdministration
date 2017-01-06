<?
class proveedores{

	// Propiedades

	var $id;
	var $descripcion;
	var $nombre;
	var $fecha;
	var $rif_letra;
	var $rif_numero;
	var $rif_control;
	var $rif;
	var $nit;
	var $nro_trabajadores;
	var $direccion;
	var $provee_contrib_munic;
	var $provee_contrat;
	var $status;
	var $datos_reg;
	var $registro_const;
	var $ci_representante;
	var $nombre_representante;
	var $contacto;
	var $accionistas;
	var $junta_directiva;
	var $telefono;
	var $fax;
	var $email;
	var $ci_comisario;
	var $nombre_comisario;
	var $ci_responsable;
	var $nombre_responsable;
	var $cap_suscrito;
	var $cap_pagado;
	//var $id_grupo_prove;
	var $id_estado;
	var $id_municipio;
	var $id_parroquia;
	var $obj_empresa;
	var $idParCat;
	
	var $estado;
	var $municipio;
	var $parroquia;
	
	var $cta_contable;
	var $desc_cuenta;

	var $total;
	
	var $msj;

	function get($conn, $id){
		$q = "SELECT proveedores.* , PC.descripcion AS desc_cuenta, ";
		$q.= "puser.estado.descripcion AS estado, ";
		$q.= "puser.municipios.descripcion AS municipio, ";
		$q.= "puser.parroquias.descripcion AS parroquia ";
		$q.= "FROM puser.proveedores ";
		$q.= "Inner Join puser.estado ON puser.proveedores.id_estado = puser.estado.id ";
		$q.= "Inner Join puser.municipios ON puser.proveedores.id_municipio = puser.municipios.id ";
		$q.= "Inner Join puser.parroquias ON puser.proveedores.id_parroquia = puser.parroquias.id ";
		$q.= "LEFT Join contabilidad.plan_cuenta PC ON puser.proveedores.cta_contable = PC.id ";
		$q.= "WHERE puser.proveedores.id='$id'";
		//die($q);
		$r = $conn->Execute($q);
		//die(var_dump($r));
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->descripcion = $r->fields['descripcion'];
			$this->nombre = $r->fields['nombre'];
			$this->rif_letra = $r->fields['rif_letra'];	
			$this->rif_numero = $r->fields['rif_numero'];
			$this->rif_control = $r->fields['rif_control'];
			$this->rif = $r->fields['rif'];
			$this->nit = $r->fields['nit'];	
			$this->nro_trabajadores = $r->fields['nro_trabajadores'];
			$this->fecha = $r->fields['fecha'];
			$this->direccion = $r->fields['direccion'];
			$this->provee_contrib_munic = $r->fields['provee_contrib_munic'];
			$this->provee_contrat = $r->fields['provee_contrat'];
			$this->status = $r->fields['status'];
			$this->datos_reg = $r->fields['datos_reg'];
			$this->registro_const = $r->fields['registro_const'];
			$this->ci_representante = $r->fields['ci_representante'];
			$this->nombre_representante = $r->fields['nombre_representante'];
			$this->contacto = $r->fields['contacto'];
			$this->accionistas = $r->fields['accionistas'];
			$this->junta_directiva = $r->fields['junta_directiva'];
			$this->telefono = $r->fields['telefono'];
			$this->fax = $r->fields['fax'];
			$this->email = $r->fields['email'];
			$this->ci_comisario = $r->fields['ci_comisario'];
			$this->nombre_comisario = $r->fields['nombre_comisario'];
			$this->ci_responsable = $r->fields['ci_responsable'];
			$this->nombre_responsable = $r->fields['nombre_responsable'];
			$this->cap_suscrito = $r->fields['cap_suscrito'];
			$this->cap_pagado = $r->fields['cap_pagado'];
			$this->id = $r->fields['id'];		
			$this->id_estado = $r->fields['id_estado'];
			$this->id_municipio = $r->fields['id_municipio'];
			$this->id_parroquia = $r->fields['id_parroquia'];
			$this->obj_empresa = $r->fields['obj_empresa'];
			$this->getRelacionGrupos($conn, $id);
			$this->estado = $r->fields['estado'];
			$this->municipio = $r->fields['municipio'];
			$this->parroquia = $r->fields['parroquia'];
			$this->cta_contable = $r->fields['cta_contable'];
			$this->desc_cuenta = $r->fields['desc_cuenta'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM proveedores ";
		//$q.= "WHERE id <> 90 ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new proveedores;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 
					$nombre, 
					$fecha, 
					$rif_letra, 
					$rif_numero, 
					$rif_cntrl, 
					$nit, 
					$n_trabajadores, 
					$direccion, 
					$id_estado, 
					$id_municipio, 
					$id_parroquia, 
					$provee_contrib_munic, 
					$provee_contrat, 
					$status, 
					$datos_reg, 
					$registro_const, 
					$ci_representante, 
					$nombre_representante, 
					$contacto,
					$accionistas, 
					$junta_directiva, 
					$telefono, 
					$fax, 
					$email, 
					$ci_comisario, 
					$nombre_comisario, 
					$ci_responsable, 
					$nombre_responsable, 
					$cap_suscrito, 
					$cap_pagado, 
					$rif, 
					$obj_empresa='', 
					$relacion_gp,
					$cta_contable = 'null'){			// Cuando este hecho el modulo decontabilidad hay que quitar = 'null'
		$q = "INSERT INTO proveedores ";
		$q.= "(nombre, fecha, rif_letra, rif_numero, rif_control, nit, nro_trabajadores, direccion, id_estado, id_municipio, id_parroquia, ";
		$q.= " provee_contrib_munic, provee_contrat, status, datos_reg, registro_const,ci_representante, nombre_representante, contacto, accionistas, ";
		$q.= " junta_directiva, telefono, fax, email, ci_comisario, nombre_comisario, ci_responsable, nombre_responsable, cap_suscrito, cap_pagado, rif, obj_empresa, cta_contable)";
		$q.= "VALUES ";
		$q.= "('$nombre', '$fecha', '$rif_letra', '$rif_numero', '$rif_cntrl', '$nit', '$n_trabajadores', '$direccion', $id_estado, $id_municipio, ";
		$q.= " $id_parroquia, '$provee_contrib_munic', '$provee_contrat', '$status', '$datos_reg', '$registro_const','$ci_representante', '$nombre_representante', ";
		$q.= " '$contacto', '$accionistas', '$junta_directiva', '$telefono', '$fax', '$email', '$ci_comisario', '$nombre_comisario', '$ci_responsable', ";
		$q.= " '$nombre_responsable', '$cap_suscrito', '$cap_pagado', '$rif', '$obj_empresa', $cta_contable) ";
		
		//die($q);
		
		
		if($conn->Execute($q)){
			$nrodoc = getLastId($conn, 'id', 'proveedores'); 
			$this->addRelacionGrupos($conn, 
												$nrodoc,
												$relacion_gp);
			$sql = "SELECT puser.set_tipo_contrib($nrodoc,'$$provee_contrib_munic')";
			$row = $conn->Execute($sql);
		
			$this->msj = REG_ADD_OK;
			return true;
		}
		else{
			$this->msj = ERROR;
			return false;
		}	
		
	}

	function set($conn, 
					$id, 
					$nombre, 
					$rif_letra, 
					$rif_numero, 
					$rif_cntrl, 
					$nit, 
					$n_trabajadores, 
					$direccion, 
					$id_estado, 
					$id_municipio, 
					$id_parroquia, 
					$provee_contrib_munic, 
					$provee_contrat, 
					$status, 
					$datos_reg, 
					$registro_const, 
					$ci_representante, 
					$nombre_representante, 
					$contacto,
					$accionistas, 
					$junta_directiva, 
					$telefono, 
					$fax, 
					$email, 
					$ci_comisario, 
					$nombre_comisario, 
					$ci_responsable, 
					$nombre_responsable, 
					$cap_suscrito, 
					$cap_pagado, 
					$rif, 
					$obj_empresa='',
					$relacion_gp,
					$cta_contable = 'null'){		// Cuando este hecho el modulo decontabilidad hay que quitar = 'null'
		$q = "UPDATE proveedores SET nombre = '$nombre', rif_letra='$rif_letra', rif_numero='$rif_numero', ";
		$q.= " rif_control='$rif_cntrl', nit='$nit', nro_trabajadores='$n_trabajadores', direccion='$direccion', id_estado=$id_estado, ";
		$q.= " id_municipio=$id_municipio, id_parroquia=$id_parroquia, provee_contrib_munic='$provee_contrib_munic', provee_contrat='$provee_contrat', ";
		$q.= " status='$status', datos_reg='$datos_reg', registro_const='$registro_const', ci_representante='$ci_representante', ";
		$q.= " nombre_representante='$nombre_representante', contacto='$contacto', accionistas='$accionistas', junta_directiva='$junta_directiva', ";
		$q.= " telefono='$telefono', fax='$fax', email='$email', ci_comisario='$ci_comisario', nombre_comisario='$nombre_comisario', ";
		$q.= " ci_responsable='$ci_responsable', nombre_responsable='$nombre_responsable', cap_suscrito='$cap_suscrito', cap_pagado='$cap_pagado', rif='$rif', obj_empresa='$obj_empresa', cta_contable = $cta_contable ";
		$q.= "WHERE id=$id";	
		//die($q);
		if($conn->Execute($q)){
			$this->delRelacion($conn, $id);
			$this->addRelacionGrupos($conn, 
												$id,
												$relacion_gp);
			$sql = "SELECT puser.set_tipo_contrib($id,'$provee_contrib_munic')";
			//die($sql);
			$row = $conn->Execute($sql);
			$this->msj = REG_SET_OK; 
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function del($conn, $id){
		try{
		$q = "DELETE FROM proveedores WHERE id='$id'";
		$r = $conn->Execute($q);
		$this->msj = REG_DEL_OK;
		return true;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1){
				
				$this->msj = ERROR_PROVEEDOR_VFK;
				return false;
			}elseif($e->getCode()==-5){
				$this->msj = ERROR_CATCH_VUK;
				return false;
			}else{
				$this->msj = ERROR_CATCH_GENERICO;
				return false;
			}
		}
		
	}
	
	function busca_req ($conn, $id){
	$q= "SELECT * FROM puser.relacion_req_prov WHERE id_proveedores = '$id' ";
	$r = $conn->Execute($q) or die($q);
		while(!$r->EOF){
			$ue = new proveedores;
			$ue->id_requisitos = $r->fields['id_requisitos'];
			$ue->fecha_emi = $r->fields['fecha_emi'];
			$ue->fecha_vcto	= $r->fields['fecha_vcto'];
			$ue->prorroga = $r->fields['prorroga'];
			$coleccion[] = $ue;
			$r->movenext();
			
		}
		
		return($coleccion);
	}
	
	function busca_req_grupo($conn, $id, $id_grupo){
	$q="SELECT DISTINCT ON(puser.relacion_req_gp.id_requisito)puser.requisitos.nombre AS requisitos, ";
	$q.="puser.grupos_proveedores.nombre AS grupo, ";
	$q.="puser.proveedores.nombre, ";
	$q.="puser.grupos_proveedores.id, ";
	$q.="puser.relacion_req_gp.id_requisito, ";
	$q.="puser.requisitos.nombre, ";
	$q.="puser.relacion_req_prov.fecha_vcto, ";
	$q.="puser.relacion_req_prov.fecha_emi, ";
	$q.="puser.relacion_req_prov.prorroga ";
	$q.="FROM puser.proveedores ";
	$q.="Inner Join puser.relacion_provee_grupo_provee ON puser.proveedores.id = puser.relacion_provee_grupo_provee.id_provee ";
	$q.="Inner Join puser.grupos_proveedores ON puser.relacion_provee_grupo_provee.id_grupo_provee = puser.grupos_proveedores.id ";
	$q.="Inner Join puser.relacion_req_gp ON puser.grupos_proveedores.id = puser.relacion_req_gp.id_grupo_proveedor ";
	$q.="Inner Join puser.requisitos ON puser.relacion_req_gp.id_requisito = puser.requisitos.id ";
	$q.="LEFT Join puser.relacion_req_prov ON puser.proveedores.id = puser.relacion_req_prov.id_proveedores ";
	$q.="AND puser.relacion_req_prov.id_requisitos = puser.requisitos.id ";
	$q.="WHERE puser.proveedores.id  =  '$id'";
		//die($q);
			$req = $conn->Execute($q) or die($q);
				while(!$req->EOF){
					$ue= new proveedores;
						$ue->requisito = $req->fields['nombre'];
						$ue->fecha_emi = $req->fields['fecha_emi'];	
						//if ($ue->fecha_emi=='') $ue->fecha_emi='---'; 
						$ue->fecha_vcto = $req->fields['fecha_vcto'];
						//if ($ue->fecha_vcto=='') $ue->fecha_vcto='---';
						$ue->prorroga = $req->fields['prorroga']; 
						$coleccion[] = $ue;
						$req->movenext();
				}
		
		/*print_r($coleccion);
		die("aqui");*/
		return($coleccion);
	}
	
	function proveedores_rif($conn, $rif){
	
		$q = "SELECT * FROM proveedores WHERE rif ILIKE '$rif'";
		//die($q);
		$r = $conn->Execute($q);
		
		if ($r->fields['id']){
		
			$this->get($conn,$r->fields['id']);
				
		}
	
	}
	
	function buscarProveedoresContrato($conn, $tipo='', $status='', $rif='', $nombre='', $from=0, $max=0, $orden='nombre')
	{
		$tipo = str_replace('\\','',$tipo);
		$q = "SELECT p.*, ri.tipo_contribuyente AS tipo_contribuyente, ri.ingreso_periodo_fiscal AS ingreso_periodo_fiscal FROM puser.proveedores AS p ";
		$q.= "LEFT JOIN puser.retencion_iva AS ri ON (ri.id_proveedor = p.id) "; 
		$q.= "WHERE 1=1 ";// CAMBIAR EL ID EN LA ALCALDIA DE LIBERTADOR A 90 YA QUE ESE ES EL ID DEL PROVEEDOR ALCALDIA
		
		/*if($aux==0){
			$q.= "AND p.id <> 90 ";
		}*/
		if ($tipo!='')
		{
			$q .= "AND provee_contrat = '$tipo' ";
		}
		
		if ($status!=''){
			$q .= "AND status='$status' ";		
		}
		
		if ($rif != '')
			$q .= "AND rif ILIKE '$rif%' ";
			
		if ($nombre != '')
			$q .= "AND nombre ILIKE '%$nombre%' ";
			
		if(trim($orden)!='')
			$q .= " ORDER BY $orden ";
		//die($q);
		//die($from."  max: ".$max);
		//$e = $conn->Execute($q);
		//die(var_dump($conn->ErrorMsg()));
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		//die(var_dump($r));
		while(!$r->EOF){
		
			$ob = new proveedores;
			$ob->nombre	=	$r->fields['nombre'];
			$ob->rif	=	$r->fields['rif'];
			$ob->id		=	$r->fields['id'];	
			$ob->tipo_contribuyente		=	$r->fields['tipo_contribuyente'];
			$ob->ingreso_periodo_fiscal	=	$r->fields['ingreso_periodo_fiscal'];	
			$coleccion[] = $ob;
			$r->movenext();
		}
		
		return $coleccion;
	}
	
	function totalRegistroContrato($conn, $tipo, $status='', $rif='', $nombre='')
	{
		$tipo = str_replace('\\','',$tipo);
		$q = "SELECT * FROM proveedores WHERE id <> 54 ";
		
		if ($tipo!='')
		{
			$q .= "AND provee_contrat IN $tipo ";
		}
		
		if ($status!=''){
			$q .= "AND status='$status' ";		
		}
		
		if ($rif != '')
			$q .= "AND rif ILIKE '$rif%' ";
			
		if ($nombre != '')
			$q .= "AND nombre ILIKE '%$nombre%' ";
			
		//die($q);
		$r = $conn->Execute($q);
		return $r->RecordCount();
	}
	
	function getRelacionGrupos($conn, $id_proveedor){
		$q= "SELECT * FROM puser.relacion_provee_grupo_provee WHERE id_provee = '$id_proveedor'";
		//die($q);
		//echo $q."<br>";
		$r= $conn->Execute($q);
		//die(var_dump($r));
		while(!$r->EOF){
			$rgp = new proveedores;
			$rgp->id_grupo = $r->fields['id_grupo_provee'];
			$coleccion[] = $rgp;
			$r->movenext();
		}
		$this->relacionPARCAT = new Services_JSON();
		$this->relacionPARCAT = is_array($coleccion) ? $this->relacionPARCAT->encode($coleccion) : false;
	}
	
	function addRelacionGrupos($conn, 												
										 $nrodoc,
										 $oRelacion){
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$oRelacion));
		if(is_array($JsonRec->relacion_gp)){
			foreach ($JsonRec->relacion_gp as $oR_Aux){
			
				$q = "INSERT INTO puser.relacion_provee_grupo_provee ";
				$q.= "( id_provee, id_grupo_provee) ";
				$q.= "VALUES ";
				$q.= "('$nrodoc', '$oR_Aux[0]') ";
				//echo($q."<br>");
				//die($q);
				$r = $conn->Execute($q);
			} 
		if($r){
			return true;
		} else {
			return false;
		
				}
		}
	}
	
	function delRelacion($conn, $id){
		$q= "DELETE FROM puser.relacion_provee_grupo_provee WHERE id_provee = '$id'";
		$r = $conn->Execute($q);
		if($r){
			return true;
		} else {
			return false;
			}
		}
		
		function buscar($conn, $max=10, $from=1, $orden="id", $nombre=""){
		try{
			$q = "SELECT * FROM puser.proveedores ";
			$q.= "WHERE id <> 90 ";
			if (!empty($nombre))
				$q.= "AND nombre ILIKE '%$nombre%' ";
			$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new proveedores;
				$ue->get($conn, $r->fields['id']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
	}
	
	function total_registro_busqueda($conn, $orden="id", $nombre=""){
		
		$q = "SELECT * FROM puser.proveedores ";
		$q.= "WHERE id <> 90 ";
			if (!empty($nombre))
				$q.= "AND nombre ILIKE '%$nombre%' ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();

		return $total;
	}
	
	function buscarProveedoresContratoAnterior($conn, $ano, $tipo='', $status='', $rif='', $nombre='', $from=0, $max=0, $orden='nombre')
	{
		$tipo = str_replace('\\','',$tipo);
		$q = "SELECT DISTINCT p.*, ri.tipo_contribuyente AS tipo_contribuyente, ri.ingreso_periodo_fiscal AS ingreso_periodo_fiscal FROM puser.proveedores AS p ";
		$q.= "LEFT JOIN puser.retencion_iva AS ri ON (ri.id_proveedor = p.id) ";
		$q.= "INNER JOIN historico.orden_pago op ON (p.id = op.id_proveedor AND op.ano = '$ano' AND  (op.montodoc - (op.montoret + op.montopagado) > 0) AND op.status = '2') ";
		$q.= "WHERE 1=1 ";// CAMBIAR EL ID EN LA ALCALDIA DE LIBERTADOR A 90 YA QUE ESE ES EL ID DEL PROVEEDOR ALCALDIA
		
		/*if($aux==0){
			$q.= "AND p.id <> 90 ";
		}*/
		if ($tipo!='')
		{
			$q .= "AND provee_contrat = '$tipo' ";
		}
		
		if ($status!=''){
			$q .= "AND status='$status' ";		
		}
		
		if ($rif != '')
			$q .= "AND rif ILIKE '$rif%' ";
			
		if ($nombre != '')
			$q .= "AND nombre ILIKE '%$nombre%' ";
			
		if(trim($orden)!='')
			$q .= " ORDER BY $orden ";
		//die($q);
		//die($from."  max: ".$max);
		//$e = $conn->Execute($q);
		//die(var_dump($conn->ErrorMsg()));
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from):$conn->Execute($q);
		//die(var_dump($r));
		while(!$r->EOF){
		
			$ob = new proveedores;
			$ob->nombre	=	$r->fields['nombre'];
			$ob->rif	=	$r->fields['rif'];
			$ob->id		=	$r->fields['id'];	
			$ob->tipo_contribuyente		=	$r->fields['tipo_contribuyente'];
			$ob->ingreso_periodo_fiscal	=	$r->fields['ingreso_periodo_fiscal'];	
			$coleccion[] = $ob;
			$r->movenext();
		}
		
		return $coleccion;
	}
			
}

?>
