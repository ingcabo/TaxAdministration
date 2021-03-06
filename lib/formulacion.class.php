<?
class formulacion
{
	function get($conn, $id_formulacion)
	{
		$q = "SELECT * FROM puser.formulacion WHERE id_formulacion = '$id_formulacion'";
		//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF)
		{
			$this->id_form = $r->fields['id_form'];
			$this->id_formulacion = $r->fields['id_formulacion'];
			$this->id_escenario = $r->fields['id_escenario'];
			$this->anio = $r->fields['anio'];
			$this->organismo = $r->fields['organismo'];
			$this->desc_ue = $r->fields['desc_ue'];
			$this->objetivo_gral = $r->fields['objetivo_gral'];
			$this->status = $r->fields['status'];
			$this->id_ue = $r->fields['id_ue'];
			$this->nro_meta = $r->fields['nro_meta'];
			if (!empty($r->fields['status']))
			{
				$this->disabled = "disabled";
			}
			
			$this->metas = $this->get_metas($conn, $r->fields['id_formulacion']);
			$this->gastos_personal = $this->get_gastos_personal($conn, $r->fields['id_formulacion']);
			$this->mat_suminis = $this->get_mat_suminis($conn, $r->fields['id_formulacion']);
			$this->serv_no_personal = $this->get_serv_no_personal($conn, $r->fields['id_formulacion']);
			$this->act_reales = $this->get_act_reales($conn, $r->fields['id_formulacion']);
			$this->otros = $this->get_otros($conn, $r->fields['id_formulacion']);
			$this->observacion = $this->get_observacion($conn, $r->fields['id_formulacion']);
			
			return true;
		}
		else
			return false;
	}
	
	function get_cp($conn, $id, $id_escenario)
	{
		$q = "SELECT DISTINCT
				puser.relacion_ue_cp.id_categoria_programatica
			FROM
				puser.relacion_ue_cp
			INNER JOIN 
				puser.categorias_programaticas 
			ON 
				puser.relacion_ue_cp.id_categoria_programatica = puser.categorias_programaticas.id
			WHERE
				puser.relacion_ue_cp.id_unidad_ejecutora =  '$id'
			AND
				puser.relacion_ue_cp.id_escenario =  '$id_escenario'";

		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new formulacion;
			$ue->id = $r->fields['id_categoria_programatica'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function get_pp($conn, $id, $id_escenario, $pp, $cp)
	{
		$q = "SELECT "; 
		$q.= "	PP.descripcion AS partida_presupuestaria,
				PP.id AS id_partida_presupuestaria,
				CP.estimacion ";
		$q.="FROM ";
		$q.="	puser.partidas_presupuestarias AS PP ";




		if($pp ='401')
		{
			$q.="INNER JOIN ";

			$q.="	rrhh.conc_part AS CP ";
			$q.="ON "; 



			$q.="	(PP.id = CP.par_cod  AND  PP.id_escenario = CP.escenario) ";
			$q.="WHERE ";
			$q.="	substr(PP.id, 1, 3) = '$pp' ";
			$q.="AND "; 

			$q.="	CP.cat_cod = '$cp' ";
			$q.="AND ";
			$q.="	CP.escenario = '$id_escenario' ";
			$q.="ORDER BY ";
			$q.="	PP.descripcion";
		}

		else
		{
			$q.="WHERE ";
			$q.="	substr(puser.relacion_pp_cp.id_partida_presupuestaria, 1, 3) != '401' ";
			$q.="AND ";
			$q.="	substr(puser.relacion_pp_cp.id_partida_presupuestaria, 1, 3) != '402' ";
			$q.="AND ";
			$q.="	substr(puser.relacion_pp_cp.id_partida_presupuestaria, 1, 3) != '403' ";
			$q.="AND ";
			$q.="	substr(puser.relacion_pp_cp.id_partida_presupuestaria, 1, 3) != '404' ";
			$q.="AND ";
			$q.="	puser.categorias_programaticas.id_escenario = '$id_escenario' ";
			$q.="AND ";
			$q.="	puser.partidas_presupuestarias.id_escenario = '$id_escenario' ";
			$q.="ORDER BY ";
			$q.="	puser.categorias_programaticas.descripcion";
		}
		//die($q);
		$r = $conn->Execute($q);
		$collection=array();
		while(!$r->EOF)
		{
			$ue = new formulacion;
			$ue->id = $r->fields['id_partida_presupuestaria'];
			$ue->descripcion = $r->fields['partida_presupuestaria'];
			$ue->estimacion = $r->fields['estimacion'];
			//$ue->valor = $r->fields['id_partida_presupuestaria'];
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function get_metas($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_metas'];
				$FamiliarAux[$i][1] = $r->fields['id_cp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['cant_programada'];
				$FamiliarAux[$i][4] = $r->fields['unid_medida'];//die($FamiliarAux[$i][4]);
						
				$i++;
				$r->movenext();
			}
			$this->metas = new Services_JSON();
			$this->metas = is_array($FamiliarAux) ? $this->metas->encode($FamiliarAux) : false;	
		return $this->metas;
	}
	
	function get_gastos_personal($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.gastos_personales WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_gast_pers'];
				$FamiliarAux[$i][1] = $r->fields['id_pp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['monto'];
				$FamiliarAux[$i][4] = $r->fields['id_cp'];
						
				$i++;
				$r->movenext();
			}
			$this->gastos_personal = new Services_JSON();
			$this->gastos_personal = is_array($FamiliarAux) ? $this->gastos_personal->encode($FamiliarAux) : false;	
		return $this->gastos_personal;
	}
	
	function get_mat_suminis($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.materiales_suministros WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_mat_sum'];
				$FamiliarAux[$i][1] = $r->fields['id_pp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['monto'];
				$FamiliarAux[$i][4] = $r->fields['id_cp'];
						
				$i++;
				$r->movenext();
			}
			$this->mat_suminis = new Services_JSON();
			$this->mat_suminis = is_array($FamiliarAux) ? $this->mat_suminis->encode($FamiliarAux) : false;	
		return $this->mat_suminis;
	}
	
	function get_serv_no_personal($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.serv_no_personales WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_serv_no_pers'];
				$FamiliarAux[$i][1] = $r->fields['id_pp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['monto'];
				$FamiliarAux[$i][4] = $r->fields['id_cp'];
						
				$i++;
				$r->movenext();
			}
			$this->serv_no_personal = new Services_JSON();
			$this->serv_no_personal = is_array($FamiliarAux) ? $this->serv_no_personal->encode($FamiliarAux) : false;	
		return $this->serv_no_personal;
	}
	
	function get_act_reales($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.activos_reales WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_act_reales'];
				$FamiliarAux[$i][1] = $r->fields['id_pp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['monto'];
				$FamiliarAux[$i][4] = $r->fields['id_cp'];
						
				$i++;
				$r->movenext();
			}
			$this->act_reales = new Services_JSON();
			$this->act_reales = is_array($FamiliarAux) ? $this->act_reales->encode($FamiliarAux) : false;	
		return $this->act_reales;
	}
	
	function get_otros($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.otros WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_otros'];//die($FamiliarAux[$i][0]);
				$FamiliarAux[$i][1] = $r->fields['id_pp'];
				$FamiliarAux[$i][2] = $r->fields['descripcion'];
				$FamiliarAux[$i][3] = $r->fields['monto'];
				$FamiliarAux[$i][4] = $r->fields['id_cp'];
						
				$i++;
				$r->movenext();
			}
			$this->otros = new Services_JSON();
			$this->otros = is_array($FamiliarAux) ? $this->otros->encode($FamiliarAux) : false;	
		return $this->otros;
	}
	
	function get_observacion($conn, $id_formulacion) 
	{
		$w = "SELECT * FROM puser.observacion WHERE id_formulacion = '$id_formulacion'"; //die($w);
			$r = $conn->Execute($w);
			$i=0;
			while(!$r->EOF)
			{
				$FamiliarAux[$i][0] = $r->fields['id_observacion'];//die($FamiliarAux[$i][0]);
				$FamiliarAux[$i][1] = $r->fields['observacion'];
						
				$i++;
				$r->movenext();
			}
			$this->observacion = new Services_JSON();
			$this->observacion = is_array($FamiliarAux) ? $this->observacion->encode($FamiliarAux) : false;	
		return $this->observacion;
	}
		
	
	function get_all($conn, $from=0, $max=0,$orden="id_formulacion")
	{
		$q = "SELECT * FROM puser.formulacion ";
		//$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new formulacion;
			$ue->get($conn, $r->fields['id_formulacion']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function add($conn, 
				$id_ue, 
				$status, 
				$escenario,
				$anio,
				$organismo, 
				$desc_ue,
				$objetivo,
				$metas,
				$gastos_personal,
				$mat_suminis,
				$serv_no_personal,
				$act_reales,
				$otros,
				$observacion,
				$cant_metas)
	{
		$id_formulacion = $id_ue."-".$cant_metas;
		$q = "INSERT INTO puser.formulacion ";
		$q.= "(id_formulacion, desc_ue, status, id_escenario, anio, organismo, objetivo_gral, id_ue, nro_meta) ";
		$q.= "VALUES ";
		$q.= "('$id_formulacion', '$desc_ue', $status, $escenario, $anio, '$organismo', '$objetivo', '$id_ue', $cant_metas) ";
		//die($q);
		$rq = $conn->Execute($q);

		$this->GuardarMetas($conn, $id_formulacion, $metas);
		$this->GuardarGastosPersonales($conn, $id_formulacion, $gastos_personal);
		$this->GuardarMaterialesSuministros($conn, $id_formulacion, $mat_suminis);
		$this->GuardarServiciosNoPersonales($conn, $id_formulacion, $serv_no_personal);
		$this->GuardarActivosReales($conn, $id_formulacion, $act_reales);
		$this->GuardarOtros($conn, $id_formulacion, $otros);
		$this->GuardarObservacion($conn, $id_formulacion, $observacion);
		
		if($rq)
			return true;
		else
	 		return false;
	}

	function set($conn, 
				$id_formulacion,
				$objetivo_gral, 
				$status,
				$metas,
				$gastos_personal,
				$mat_suminis,
				$serv_no_personal,
				$act_reales,
				$otros,
				$observacion
				)
	{
		$q = "UPDATE puser.formulacion SET ";
		$q.= "objetivo_gral = '$objetivo_gral', status = $status ";
		$q.= "WHERE id_formulacion = '$id_formulacion'";	
		//die($q);
		$rq = $conn->Execute($q);
		
		$this->GuardarMetas($conn, $id_formulacion, $metas);
		/*$this->GuardarGastosPersonales($conn, $id_formulacion, $gastos_personal);
		$this->GuardarMaterialesSuministros($conn, $id_formulacion, $mat_suminis);
		$this->GuardarServiciosNoPersonales($conn, $id_formulacion, $serv_no_personal);
		$this->GuardarActivosReales($conn, $id_formulacion, $act_reales);
		$this->GuardarOtros($conn, $id_formulacion, $otros);
		$this->GuardarObservacion($conn, $id_formulacion, $observacion);*/
	
		if($rq)
			return true;
		else
	 		return false;
	}
		
	function GuardarMetas($conn, $id_formulacion, $metas)
	{
		//die($patente);
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$metas));
		if(is_array($JsonRec->metas))
		{
			$q = "DELETE FROM puser.metas WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			foreach($JsonRec->metas as $familiarAux)
			{
				$q = "INSERT INTO puser.metas ";
				$q.= "(id_metas, id_cp, descripcion, cant_programada, unid_medida, id_formulacion) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '$familiarAux[3]', '$familiarAux[4]', '$id_formulacion') ";
				//die($q);
				echo $q."<br>";
				$conn->Execute($q);
				$i++;
			}
		}
	}
	
	function GuardarGastosPersonales($conn, $id_formulacion, $gastos_personal)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$gastos_personal));
		if(is_array($JsonRec->gastos_personal))
		{
			$q = "DELETE FROM puser.gastos_personales WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			$z = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rz = $conn->Execute($z);
			$j = 0;
			while(!$rz->EOF)
			{
				$id_cp[$j] = $rz->fields['id_cp'];
				$j++;
				$rz->movenext();
			}
			$k = 0;
			$i = 0;
			foreach($JsonRec->gastos_personal as $familiarAux)
			{
				if($i > $k)
				{
					$k = $i - 2*$k;
				}
				$q = "INSERT INTO puser.gastos_personales ";
				$q.= "(id_gast_pers, id_pp, descripcion, monto, id_formulacion, id_cp) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '".guardaFloat($familiarAux[3])."', '$id_formulacion', '$id_cp[$k]') ";
				//echo $q."<br>";//die($q);
				$conn->Execute($q);
				$i++;
				if($k < $j - 1)
				{	
					$k++;
				}
			}
		}
	}
	
	function GuardarMaterialesSuministros($conn, $id_formulacion, $mat_suminis)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$mat_suminis));
		if(is_array($JsonRec->mat_suminis))
		{
			$q = "DELETE FROM puser.materiales_suministros WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			$z = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rz = $conn->Execute($z);
			$j = 0;
			while(!$rz->EOF)
			{
				$id_cp[$j] = $rz->fields['id_cp'];
				$j++;
				$rz->movenext();
			}
			$k = 0;
			$i = 0;
			foreach($JsonRec->mat_suminis as $familiarAux)
			{
				if($i > $k)
				{
					$k = $i - 2*$k;
				}
				$q = "INSERT INTO puser.materiales_suministros ";
				$q.= "(id_mat_sum, id_pp, descripcion, monto, id_formulacion, id_cp) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '".guardaFloat($familiarAux[3])."', '$id_formulacion', '$id_cp[$k]') ";
				//echo $q."<br>";//die($q);
				$conn->Execute($q);
				$i++;
				if($k < $j - 1)
				{	
					$k++;
				}
			}
		}
	}
	
	function GuardarServiciosNoPersonales($conn, $id_formulacion, $serv_no_personal)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$serv_no_personal));
		if(is_array($JsonRec->serv_no_personal))
		{
			$q = "DELETE FROM puser.serv_no_personales WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			$z = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rz = $conn->Execute($z);
			$j = 0;
			while(!$rz->EOF)
			{
				$id_cp[$j] = $rz->fields['id_cp'];
				$j++;
				$rz->movenext();
			}
			$k = 0;
			$i = 0;
			foreach($JsonRec->serv_no_personal as $familiarAux)
			{
				if($i > $k)
				{
					$k = $i - 2*$k;
				}
				$q = "INSERT INTO puser.serv_no_personales ";
				$q.= "(id_serv_no_pers, id_pp, descripcion, monto, id_formulacion, id_cp) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '".guardaFloat($familiarAux[3])."', '$id_formulacion', '$id_cp[$k]') ";
				//die($q);//echo $q."<br>";
				$conn->Execute($q);
				$i++;
				if($k < $j - 1)
				{	
					$k++;
				}
			}
		}
	}
	
	function GuardarActivosReales($conn, $id_formulacion, $act_reales)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$act_reales));
		if(is_array($JsonRec->act_reales))
		{
			$q = "DELETE FROM puser.activos_reales WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			$z = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rz = $conn->Execute($z);
			$j = 0;
			while(!$rz->EOF)
			{
				$id_cp[$j] = $rz->fields['id_cp'];
				$j++;
				$rz->movenext();
			}
			$k = 0;
			$i = 0;
			foreach($JsonRec->act_reales as $familiarAux)
			{
				if($i > $k)
				{
					$k = $i - 2*$k;
				}
				$q = "INSERT INTO puser.activos_reales ";
				$q.= "(id_act_reales, id_pp, descripcion, monto, id_formulacion, id_cp) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '".guardaFloat($familiarAux[3])."', '$id_formulacion', '$id_cp[$k]') ";
				//die($q);//echo $q."<br>";
				$conn->Execute($q);
				$i++;
				if($k < $j - 1)
				{	
					$k++;
				}
			}
		}
	}
	
	function GuardarOtros($conn, $id_formulacion, $otros)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$otros));
		if(is_array($JsonRec->otros))
		{
			$q = "DELETE FROM puser.otros WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			$z = "SELECT * FROM puser.metas WHERE id_formulacion = '$id_formulacion'";
			$rz = $conn->Execute($z);
			$j = 0;
			while(!$rz->EOF)
			{
				$id_cp[$j] = $rz->fields['id_cp'];
				$j++;
				$rz->movenext();
			}
			$k = 0;
			$i = 0;
			foreach($JsonRec->otros as $familiarAux)
			{
				if($i > $k)
				{
					$k = $i - 2*$k;
				}
				$q = "INSERT INTO puser.otros ";
				$q.= "(id_otros, id_pp, descripcion, monto, id_formulacion, id_cp) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]', '".guardaFloat($familiarAux[3])."', '$id_formulacion', '$id_cp[$k]') ";
				//die($q);//echo $q."<br>";
				$conn->Execute($q);
				$i++;
				if($k < $j - 1)
				{	
					$k++;
				}
			}
		}
	}
	
	function GuardarObservacion($conn, $id_formulacion, $observacion)
	{
		$JsonRec = new Services_JSON();
		$JsonRec=$JsonRec->decode(str_replace("\\","",$observacion));
		if(is_array($JsonRec->observacion))
		{
			$q = "DELETE FROM puser.observacion WHERE id_formulacion = $id_formulacion";
			$r = $conn->Execute($q);
			foreach($JsonRec->observacion as $familiarAux)
			{
				$q = "INSERT INTO puser.observacion ";
				$q.= "(id_observacion, observacion, id_formulacion) ";
				$q.= "VALUES ";
				$q.= "('$familiarAux[0]', '$familiarAux[1]', '$id_formulacion') ";
				//die($q);//echo $q."<br>";
				$conn->Execute($q);
				$i++;
			}
		}
	}
	
	function del($conn, $id_form)
	{
		$q = "DELETE FROM puser.formulacion WHERE id_form='$id_form'";
		if($conn->Execute($q))
			return true;
		else
			return false;
	}
}//fin clase
?>