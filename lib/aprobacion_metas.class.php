<?
	class aprobacion_metas
	{
		function get($conn, $id_aprobacion_meta)
		{
			$q = "SELECT * FROM puser.aprobacion_metas WHERE id_aprobacion_meta = $id_aprobacion_meta";
			$rq = $conn->Execute($q);
			
			if(!$rq->EOF)
			{
				$this->id_aprobacion_meta = $rq->fields['id_aprobacion_meta'];
				$this->id_formulacion = $rq->fields['id_formulacion'];
				$this->id_escenario = $rq->fields['id_escenario'];
				$this->escenario = $rq->fields['escenario'];
				$this->status = $rq->fields['status'];
				$this->id_unidad_ejecutora = $rq->fields['id_unidad_ejecutora'];
				$this->unidad_ejecutora = $rq->fields['unidad_ejecutora'];
				$this->organismo = $rq->fields['organismo'];
				$this->id_meta = $rq->fields['id_meta'];
				$this->desc_meta = $rq->fields['desc_meta'];
				$this->objetivo = $rq->fields['objetivo'];
				$this->procesado = $rq->fields['procesado'];
			}
		}
		
		function get_all($conn, $from=0, $max=0,$orden="id_aprobacion_meta")
		{
			$q = "SELECT * FROM puser.aprobacion_metas ";
			//$q.= "ORDER BY $orden ";
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$colleccion=array();
			while(!$r->EOF){
				$ue = new aprobacion_metas;
				$ue->get($conn, $r->fields['id_aprobacion_meta']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
			return $coleccion;
		}
		
		function get_metas($conn, $id_formulacion)
		{
			$q = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rq = $conn->Execute($q);
			$coleccion = array();
			while(!$rq->EOF)
			{
				$id_metas = new aprobacion_metas;
				$id_metas->id = $rq->fields['id_metas'];
				$coleccion[] = $id_metas;
				$rq->movenext();
			}
			return $coleccion;
		}
		
		function get_formulacion($conn, $id_aprobacion_meta)
		{
			$q = "SELECT * FROM puser.aprobacion_metas WHERE id_aprobacion_meta = '$id_aprobacion_meta'";
			$rq = $conn->Execute($q);
			$coleccion = array();
			while(!$rq->EOF)
			{
				$id_formulacion = new aprobacion_metas;
				$id_formulacion->id = $rq->fields['id_formulacion'];
				$coleccion[] = $id_formulacion;
				$rq->movenext();
			}
			return $coleccion;
		}
		
		function set_formulacion($conn, 
								$id_formulacion,
								$status
								)
		{
			$q = "UPDATE puser.formulacion SET ";
			$q.= "status = $status ";
			$q.= "WHERE id_formulacion = '$id_formulacion'";//die($q);
			$rq = $conn->Execute($q);
			
			if($rq)
				return true;
			else
	 			return false;
		}
		
		function add($conn,
					$id_formulacion,
					$id_escenario,
					$escenario,
					$status,
					$id_unidad_ejecutora,
					$unidad_ejecutora,
					$organismo,
					$id_meta,
					$desc_meta,
					$objetivo,
					$procesado
					)
		{
			$q = "INSERT INTO puser.aprobacion_metas ";
			$q.= "(id_formulacion, id_escenario, escenario, status, id_unidad_ejecutora, unidad_ejecutora, organismo, id_meta, desc_meta, ";
			$q.= "objetivo, procesado) ";
			$q.= "VALUES ";
			$q.= "('$id_formulacion', $id_escenario,  '$escenario', '$status', '$id_unidad_ejecutora', '$unidad_ejecutora', '$organismo', ";
			$q.= "'$id_meta', '$desc_meta', '$objetivo', $procesado)";//die($q);
			$rq = $conn->Execute($q);
			
			$this->set_formulacion($conn, $id_formulacion, $procesado);
			
			if($rq)
				return true;
			else
	 			return false;
		}	
		
		function get_montos_gastos_personales($conn, $id_aprobacion_meta, $id_formulacion, $id_cp)
		{
			$q = "SELECT DISTINCT
					puser.gastos_personales.monto AS monto
				  FROM
					puser.aprobacion_metas
				  INNER JOIN 
					puser.metas 
				  ON 
					puser.aprobacion_metas.id_formulacion = puser.metas.id_formulacion
				  INNER JOIN 
					puser.gastos_personales 
				  ON 
					puser.metas.id_formulacion = puser.gastos_personales.id_formulacion 
				  AND 
					puser.metas.id_cp = puser.gastos_personales.id_cp
				  WHERE
					puser.aprobacion_metas.id_aprobacion_meta =  $id_aprobacion_meta 
				  AND
					puser.aprobacion_metas.id_formulacion =  '$id_formulacion' 
				  AND
					puser.metas.id_cp =  '$id_cp'";
			$monto_gastos_personales = $conn->Execute($q);
			return $monto_gastos_personales;
		}
		
		function get_montos_materiales_suministros($conn, $id_aprobacion_meta, $id_formulacion, $id_cp)			
		{
			$q = "SELECT DISTINCT
						puser.materiales_suministros.monto AS monto
					FROM
						puser.aprobacion_metas
					INNER JOIN 
						puser.metas 
					ON 
						puser.aprobacion_metas.id_formulacion = puser.metas.id_formulacion
					INNER JOIN 
						puser.materiales_suministros
					ON 
						puser.metas.id_formulacion = puser.materiales_suministros.id_formulacion
					AND
						puser.metas.id_cp = puser.materiales_suministros.id_cp
					WHERE
						puser.aprobacion_metas.id_aprobacion_meta =  $id_aprobacion_meta 
					AND
						puser.aprobacion_metas.id_formulacion =  '$id_formulacion' 
					AND
						puser.metas.id_cp =  '$id_cp'";
			$monto_materiales_suministros = $conn->Execute($q);
			return $monto_materiales_suministros;
		}
		
		function get_montos_serv_no_personales($conn, $id_aprobacion_meta, $id_formulacion, $id_cp)
		{
			$q = "SELECT DISTINCT
						puser.serv_no_personales.monto AS monto
					FROM
						puser.aprobacion_metas
					INNER JOIN 
						puser.metas 
					ON 
						puser.aprobacion_metas.id_formulacion = puser.metas.id_formulacion
					INNER JOIN 
						puser.serv_no_personales 
					ON 
						puser.metas.id_formulacion = puser.serv_no_personales.id_formulacion 
					AND 
						puser.metas.id_cp = puser.serv_no_personales.id_cp
					WHERE
						puser.aprobacion_metas.id_aprobacion_meta =  $id_aprobacion_meta 
					AND
						puser.aprobacion_metas.id_formulacion =  '$id_formulacion' 
					AND
						puser.metas.id_cp =  '$id_cp'";
			$monto_serv_no_personales = $conn->Execute($q);
			return $monto_serv_no_personales;
		}
		
		function get_montos_activos_reales($conn, $id_aprobacion_meta, $id_formulacion, $id_cp)
		{
			$q = "SELECT DISTINCT
						puser.activos_reales.monto AS monto
					FROM
						puser.aprobacion_metas
					INNER JOIN 
						puser.metas 
					ON 
						puser.aprobacion_metas.id_formulacion = puser.metas.id_formulacion
					INNER JOIN 
						puser.activos_reales 
					ON 
						puser.metas.id_formulacion = puser.activos_reales.id_formulacion 
					AND 
						puser.metas.id_cp = puser.activos_reales.id_cp
					WHERE
						puser.aprobacion_metas.id_aprobacion_meta =  $id_aprobacion_meta 
					AND
						puser.aprobacion_metas.id_formulacion =  '$id_formulacion' 
					AND
						puser.metas.id_cp =  '$id_cp'";
			$monto_activos_reales = $conn->Execute($q);
			return $monto_activos_reales;
		}
		
		function get_montos_otros($conn, $id_aprobacion_meta, $id_formulacion, $id_cp)
		{
			$q = "SELECT DISTINCT
						puser.otros.monto AS monto
					FROM
						puser.aprobacion_metas
					INNER JOIN 
						puser.metas 
					ON 
						puser.aprobacion_metas.id_formulacion = puser.metas.id_formulacion
					INNER JOIN 
						puser.otros 
					ON 
						puser.metas.id_formulacion = puser.otros.id_formulacion 
					AND 
						puser.metas.id_cp = puser.otros.id_cp
					WHERE
						puser.aprobacion_metas.id_aprobacion_meta =  $id_aprobacion_meta 
					AND
						puser.aprobacion_metas.id_formulacion =  '$id_formulacion' 
					AND
						puser.metas.id_cp =  '$id_cp'";
			$monto_otros = $conn->Execute($q);
			return $monto_otros;
		}
	}//fin clase
?>