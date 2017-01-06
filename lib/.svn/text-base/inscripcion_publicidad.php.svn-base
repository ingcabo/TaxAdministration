<?
class inscripcion_publicidad{

	// Propiedades
	var $id;
	var $fecha;
	var $anio;
	var $id_contribuyente;
	var $patente;
	var $cod_ins;
	var $fec_desde;
	var $fec_hasta;
	var $sancion;
	var $fecha_sancion;
	
	var $id_publicidad;
	var $cod_tipo_publicidad;
	var	$cant;
	var $unid;
	var $aforo;
	var $sub_total;
	var $total;
	var $monto_sancion;
	var $interes_sancion;
	

	var $nombre_contribuyente;
	var $razon_contribuyente;
	var $telefono_contribuyente;
	var $ciudad_contribuyente;
	var $domiciliotrib_contribuyente;

	/*****************************
			Objeto Relacion Publicidades
	*****************************/
	var $relacionPublicidad; // almacena un array de objetos de relaciones de publicidades	

	/********************************
			Objeto Relacion entradas
	********************************/
	var $relacionEntradas; // almacena un array de objetos de relaciones de entradas
	
	// Propiedades utilizadas por el objeto con la publicidad
	var $id_solicitud;
	var $propaganda;
	var $cant1;
	var $unid1;
	var $cant2;
	var $unid2;
	var $cant3;
	var $unid3;
	var $aTot_med;
	var $precioTotalPu;
		
	var $id_espectaculo;	
	var $fec_ini;
	var $hor_ini;
	var $fec_fin;
	var $hor_fin;

	var $id_entrada;
	var $cant4;
	var $aforo1;
	var $aforo2;
	var $paforo;	
	var $precioTotalEsp;
	var $precioSubTotalEsp;
	var $precioTotalEspImp;	
	var $multa;
	
	var $desdemes;
	var $desdeanio;
	var $hastames;	
	var $hastaanio;
	
	var $TB;
	var $UT;
	var $monto_aforo;
	
	function get($conn, $id){
		$q = "SELECT * FROM publicidad.publicidad ";
		$q.= "WHERE id='$id'";
 		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];  
			$this->fecha = $r->fields['fecha']; 
			$this->patente = $r->fields['patente']; //---
			$this->id_contribuyente = $r->fields['id_contribuyente'];
			$this->cod_ins = $r->fields['cod_inspector'];
			

			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM publicidad.publicidad ";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new inscripcion_publicidad;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, 						
						$fecha,
						$patente,
						$id_contribuyente,
						$id_solicitud,
						$cod_ins,
						$fec_desde,
						$fec_hasta,
						$sancion,
						$fecha_sancion)	{

		$q = "INSERT INTO publicidad.publicidad (";
		$q.= "		    fecha,
						patente,
						id_contribuyente,						
						id_solicitud,
						cod_inspector,
						fec_desde,
						fec_hasta,
						cod_sancion,
						fecha_sancion)";
		$q.= "VALUES ";
		$q.= " 		   ('$fecha',
						'$patente',
						'$id_contribuyente',						
						'$id_solicitud',
						'$cod_ins',
						'$fec_desde',
						'$fec_hasta',
						'$sancion',
						$fecha_sancion)";
		//die($q);
		if ($conn->Execute($q))
		{
			return true;
		}
		else
		{
			return false;
		} 
}

function add_Publicidad_Eventual($conn, 						
									$id_publicidad,
									$cod_tipo_publicidad,
									$cant,
									$unid,
									$aforo,
									$sub_total,
									$total,
									$monto_sancion,
									$interes_sancion)	{

		$q = "INSERT INTO publicidad.publicidad_eventual (";
		$q.= "		    cod_publicidad,
						cod_tipo_publicidad ,
						cantidad_publicidad,						
						unidad,
						aforo,
						sub_total_publicidad,
						total_publicidad,
						monto_sancion,
						interes_sancion)";
		$q.= "VALUES ";
		$q.= " 		   ('$id_publicidad',
						'$cod_tipo_publicidad',
						'$cant',						
						'$unid',
						'$aforo',
						'$sub_total',
						'$total',
						'$monto_sancion',
						'$interes_sancion')";
		die($q);
		if ($conn->Execute($q))
		{
			return true;
		}
		else
		{
			return false;
		} 
}
	function set($conn, 
							$id, 
							$fecha,
						$desdemes,
						$desdeanio,
						$hastames,	
						$hastaanio,	
						$patente,
						$id_contribuyente,						
						$id_solicitud,						
						$aPropaganda,
						$aCant1,
						$aUnid1,
						$aCant2,
						$aUnid2,
						$aCant3,
						$aUnid3,
						$aTot_med,
						$aAforo,
						$aPrecioTotalPu,
						$id_espectaculo,
						$fec_ini,
						$hor_ini,
						$fec_fin,
						$hor_fin,
						$aEntrada,
						$aCant4,
						$aAforo_esp,
						$aforo2,
						$paforo,
						$aPrecioTotalEsp){
		$q = "UPDATE publicidad SET  ";
		$q.= "id_tipo_documento = '$id_tipo_documento', ";
		$q.= "id_unidad_ejecutora = '$id_unidad_ejecutora', ";
		$q.= "fecha = '$fecha', ";
		$q.= "fecha_entrega = '$fecha_entrega', ";
		$q.= "lugar_entrega = '$lugar_entrega', ";
		$q.= "condicion_pago = '$condicion_pago', ";
		$q.= "condicion_operacion = '$condicion_operacion', ";
		$q.= "rif = '$rif', ";
		$q.= "observaciones = '$observaciones', ";
		$q.= "nro_requisicion = '$nro_requisicion', ";
		$q.= "nro_cotizacion = '$nro_cotizacion', ";
		$q.= "nro_factura = '$nro_factura', ";
		$q.= "fecha_factura = '$fecha_factura', ";
		$q.= "cod_contraloria = '$cod_contraloria' ";
		$q.= "id_ciudadano = '$id_ciudadano' ";
		$q.= "WHERE id='$id' ";	
		//die($q);
		if($conn->Execute($q)){
			if(!empty($id_ciudadano))
				ciudadadanos::add($conn, $id_ciudadano, $ciudadano, $dir_ciudadano, $tlf_ciudadano);
			if($this->delRelacionPartidas($conn, $nrodoc) && $this->delRelacionProductos($conn, $nrodoc)){
				if($this->addRelacionEntradas($conn, 
																	$desdemes,
																	$desdeanio,
																	$hastames,	
																	$hastaanio,
																	$id_publicidad,
																	$id_espectaculo,
																	$fec_ini,
																	$hor_ini,
																	$fec_fin,
																	$hor_fin,
																	$aCant4,
																	$aEntrada,
																	$aAforo_esp,
																	$aforo2,
																	$paforo,
																	$aPrecioTotalEsp) &&
					$this->addRelacionPublicidad($conn, 
																	$desdemes,
																	$desdeanio,
																	$hastames,	
																	$hastaanio,
																	$id_publicidad,
																	$aPropaganda,																	
																	$aCant1,
																	$aUnid1,
																	$aCant2,
																	$aUnid2,
																	$aCant3,
																	$aUnid3,
																	$aTot_med,
																	$aAforo,
																	$aPrecioTotalPu) ){
					return true;
				}else
					return false;
			}else
				return false;
		}else
			return false;
	}

	function del($conn, $id){
		$q = "DELETE FROM publicidad.publicidad WHERE id='$id'";		
		//die($q);
		$r = $conn->execute($q);
		if($r){
		  $id_publicidad = getLastId($conn, 'id', 'publicidad.publicidad'); //die('id= '.$id_publicidad);
				$this->delRelacionPublicidad($conn, $id);				
		//die($this->delRelacionPublicidad($conn, $id));
	if( 	$this->delRelacionEntradas($conn, $id)){
					return true;
				
		}else{
			return false;} 
	
}}
function addRelacionEntradas($conn, 
																	$desdemes,
																	$desdeanio,
																	$hastames,	
																	$hastaanio,
																	$id_publicidad,
																	$id_espectaculo,
																	$fec_ini,
																	$hor_ini,
																	$fec_fin,
																	$hor_fin,
																	$aCant4,
																	$aEntrada,
																	$aAforo_esp,
																	$aforo2,
																	$paforo,
																	$aPrecioTotalEsp){
		for($i = 0; $i<count($aEntrada); $i++){
			$q = "INSERT INTO publicidad.relacion_publicidad_espectaculo ";
			$q.= "(desdemes, desdeanio, hastames, hastaanio,id_publicidad, id_espectaculo, fec_ini, hor_ini, fec_fin, hor_fin, cant4, id_entrada, aforo, aforo1, paforo, total_esp) ";																
																	
			$q.= "VALUES ";
			$q.= "('$desdemes','$desdeanio','$hastames','$hastaanio','$id_publicidad', '$id_espectaculo', '$fec_ini', '$hor_ini', '$fec_fin', '$hor_fin', '$aCant4[$i]', '$aEntrada[$i]', ".guardafloat($aAforo_esp[$i]).", ".guardafloat($aforo2).", '$paforo', ".guardafloat($aPrecioTotalEsp[$i]).")";
			//echo($q."<br>");
			//die($q);
			$r = $conn->Execute($q);
		} 
		if($r)
			return true;
		else
			return false;
	}
	function addRelacionPublicidad($conn, 
																	$desdemes,
																	$desdeanio,
																	$hastames,	
																	$hastaanio,
																	$id_publicidad,
																	$aPropaganda,																	
																	$aCant1,
																	$aUnid1,
																	$aCant2,
																	$aUnid2,
																	$aCant3,
																	$aUnid3,
																	$aTot_med,
																	$aAforo,
																	$aPrecioTotalPu){ //die('cant= '.$aCant3);
		for($i = 0;$i<count($aPropaganda);$i++){
			$q = "INSERT INTO publicidad.relacion_publicidad_publicidades ";
			$q.= "(desdemes, desdeanio, hastames, hastaanio,id_publicidad, id_propaganda, cant1, unid1, cant2, unid2, cant3, unid3, total_medida, aforo, total_publicidad) ";
			$q.= "VALUES ";
			$q.= "('$desdemes','$desdeanio','$hastames','$hastaanio','$id_publicidad', '$aPropaganda[$i]', '$aCant1[$i]', '$aUnid1[$i]', '$aCant2[$i]', '$aUnid2[$i]', '$aCant3[$i]', '$aUnid3[$i]', '$aTot_med[$i]', ".guardafloat($aAforo[$i]).", ".guardafloat($aPrecioTotalPu[$i]).") ";
			//echo($q."<br>");
			//die($q);
			$r = $conn->Execute($q);
		} 
		if($r)
			return true;
		else
			return false;
	}

	function delRelacionEntradas($conn, $id){
		$g = "DELETE FROM relacion_publicidad_espectaculo WHERE id_publicidad='$id'";
		//die($g);
		$t = $conn->Execute($g);
		if($t)
			return true;
		else
			return false;
	}
	
	function delRelacionPublicidad($conn, $id){
		$f = "DELETE FROM relacion_publicidad_publicidades WHERE id_publicidad='$id'";
		//die($f);
		$w = $conn->Execute($f);
											  }	
	function getRelacionEntradas($conn, $id){
		$q = "SELECT publicidad.relacion_publicidad_espectaculo.*, publicidad.espectaculo.descripcion AS espectaculo		
		      FROM publicidad.relacion_publicidad_espectaculo  
			  INNER JOIN publicidad.espectaculo ON (publicidad.relacion_publicidad_espectaculo.id_espectaculo = publicidad.espectaculo.id) ";
		$q.= "WHERE publicidad.relacion_publicidad_espectaculo.id_publicidad='$id' ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		while(!$r->EOF){
			$ue = new inscripcion_publicidad;

			$ue->desdemes = $r->fields['desdemes'];
			$ue->desdeanio = $r->fields['desdeanio'];
			$ue->hastames = $r->fields['hastames'];
			$ue->hastaanio = $r->fields['hastaanio'];
			$ue->id_publicidad = $r->fields['id_publicidad'];
			$ue->id_espectaculo = $r->fields['id_espectaculo'];
			$ue->id_entrada = $r->fields['id_entrada'];
			$ue->espectaculo = $r->fields['espectaculo'];
			$ue->fec_ini = $r->fields['fec_ini'];
			$ue->hor_ini = $r->fields['hor_ini'];
			$ue->fec_fin = $r->fields['fec_fin'];
			$ue->cant4 = $r->fields['cant4'];
			$ue->aforo1 = $r->fields['aforo'];
			$ue->aforo3 = $r->fields['aforo1']; 
			$ue->paforo3 = $r->fields['paforo'];
			$ue->total_esp = $r->fields['total_esp'];//die($this->total_esp);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
function getRelacionPublicidad($conn, $id){
	 $q = "SELECT publicidad.relacion_publicidad_publicidades.*, 
	 			  publicidad.propaganda.id AS propaganda
	       FROM 
		   		  publicidad.relacion_publicidad_publicidades
		   INNER JOIN 
		   		  publicidad.propaganda 
		   ON 
		   		  (publicidad.relacion_publicidad_publicidades.id_propaganda = publicidad.propaganda.id) ";
	$q.= "WHERE publicidad.relacion_publicidad_publicidades.id_publicidad='$id' ";
		//die($q);
		if(!$r = $conn->Execute($q))
			return false;
		while(!$r->EOF){
			$ue = new inscripcion_publicidad;			
																	
			$ue->desdemes	= $r->fields['desdemes'];
			$ue->desdeanio	= $r->fields['desdeanio'];
			$ue->hastames	= $r->fields['hastames'];
			$ue->hastaanio	= $r->fields['hastaanio'];
			$ue->propaganda = $r->fields['propaganda'];
			$ue->id_propaganda = $r->fields['id_propaganda'];  
			$ue->cant1	= $r->fields['cant1'];
			$ue->unid1 = $r->fields['unid1'];
			$ue->cant2	= $r->fields['cant2'];
			$ue->unid2 = $r->fields['unid2'];
			$ue->cant3 = $r->fields['cant3'];
			$ue->unid3 = $r->fields['unid3'];
			$ue->tot_med = $r->fields['total_medida'];
			$ue->aforo_pub = $r->fields['aforo'];
			$ue->precioTotalPub = $r->fields['total_publicidad'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}	
}
?>