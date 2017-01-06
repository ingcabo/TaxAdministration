<?	
	/*  
	 *	LIQUIDACION TRIBUTARIA DE VEHICULO
	 */

	class liquidacion {
		
		var $anio;
		var $impuesto;
		var $recargo;
		var $porcentaje;
		var $impuesto_mas_recargo;
		var $art_ref;
		var $desc_tipo;
		var $vig_desde;
		var $vig_hasta;
		var $total;
		var $tasa;
		var $calcomania;
		var $id_base_calculo;
	



		private function Liquida_ver($conn, $anio_a_liquidar, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento)
		{

			$this->anio=$anio_a_liquidar;
			
			$q="SELECT monto FROM vehiculo.esp_costo_veh ";
			$q.="WHERE (fecha_desde <= '$anio_a_liquidar-01-01'AND fecha_hasta >= '$anio_a_liquidar-01-01' ";
			$q.="AND cod_veh='$tipo_vehiculo')";
			
			
			$r = $conn->Execute($q);
			$this->impuesto = $r->fields['monto'];
			
			if ($anio_a_liquidar==date("Y")){
				$hoy = date("Y-m-d");	
			} else {
				$hoy = "$anio_a_liquidar-12-31";
			}
			
			$q="SELECT recargo,desc_tipo,art_ref,vig_desde,vig_hasta,id FROM vehiculo.base_calculo_veh ";
			$q.="WHERE anio='$anio_a_liquidar' ";
			//$q.="ORDER BY anio ASC";
			

			$r = $conn->Execute($q);
	
			while(!$r->EOF)
			{

				if (($r->fields['vig_desde'] <= $hoy and $hoy <= $r->fields['vig_hasta'])
				    or (($r->fields['vig_desde'] == $r->fields['vig_hasta']) 
					 and ($hoy >= $r->fields['vig_hasta'])))
				{

					$this->porcentaje = $r->fields['recargo'];

					$this->recargo = $this->impuesto*$this->porcentaje/100;
					$this->impuesto_mas_recargo = $this->impuesto + $this->recargo;		

					$this->art_ref = $r->fields['art_ref'];
					$this->desc_tipo = $r->fields['desc_tipo'];
					$this->vig_desde = $r->fields['vig_desde'];
					$this->vig_hasta = $r->fields['vig_hasta'];

					$this->id_base_calculo = $r->fields['id'];
				

					/*
					 *	ALMACENADO EN IMP_LIQ: IMPUESTO Y RECARGO
					 */

					if ($this->impuesto>0)
					{

						/*
						 *	TRANSACCION: CUANTIFICAR IMPUESTO		
     			 	 		 */
 
						$id_transaccion=1;

						$q="SELECT ramo 
						    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
						    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='1')";
						
						$r=$conn->Execute($q);
						
						$ramo = $r->fields['ramo'];		
						$conn->Execute($q);
					}

					if ($this->recargo>0)
					{

						/*
						 *	TRANSACCION: CUANTIFICAR RECARGO		
     			 	 		 */
 
						$id_transaccion=3;

						$q="SELECT ramo 
						    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
						    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='3')";
						
						$r=$conn->Execute($q);
						
						$ramo = $r->fields['ramo'];	
						$conn->Execute($q);
					}
				}
	
				$r->movenext();
			}

						
		}
		
		
		
		private function Liquida($conn, $anio_a_liquidar, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento){

			$this->anio=$anio_a_liquidar;
			
			$q="SELECT monto FROM vehiculo.esp_costo_veh ";
			$q.="WHERE (fecha_desde <= '$anio_a_liquidar-01-01'AND fecha_hasta >= '$anio_a_liquidar-01-01' ";
			$q.="AND cod_veh='$tipo_vehiculo')";
			
			
			$r = $conn->Execute($q);
			$this->impuesto = $r->fields['monto'];
			
			if ($anio_a_liquidar==date("Y")){
				$hoy = date("Y-m-d");	
			} else {
				$hoy = "$anio_a_liquidar-12-31";
			}
			
			$q="SELECT recargo,desc_tipo,art_ref,vig_desde,vig_hasta,id FROM vehiculo.base_calculo_veh ";
			$q.="WHERE anio='$anio_a_liquidar' ";
			//$q.="ORDER BY anio ASC";
			

			$r = $conn->Execute($q);
	
			while(!$r->EOF){

				if (($r->fields['vig_desde'] <= $hoy and $hoy <= $r->fields['vig_hasta'])
				    or (($r->fields['vig_desde'] == $r->fields['vig_hasta']) 
					 and ($hoy >= $r->fields['vig_hasta'])))
				{

					$this->porcentaje = $r->fields['recargo'];

					$this->recargo = $this->impuesto*$this->porcentaje/100;
					$this->impuesto_mas_recargo = $this->impuesto + $this->recargo;		

					$this->art_ref = $r->fields['art_ref'];
					$this->desc_tipo = $r->fields['desc_tipo'];
					$this->vig_desde = $r->fields['vig_desde'];
					$this->vig_hasta = $r->fields['vig_hasta'];

					$this->id_base_calculo = $r->fields['id'];
				

					/*
					 *	ALMACENADO EN IMP_LIQ: IMPUESTO Y RECARGO
					 */

					if ($this->impuesto>0){

						/*
						 *	TRANSACCION: CUANTIFICAR IMPUESTO		
     			 	 		 */
 
						$id_transaccion=1;

						$q="SELECT ramo 
						    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
						    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='1')";
						
						$r=$conn->Execute($q);
						
						$ramo = $r->fields['ramo'];					


						$q= "INSERT INTO vehiculo.imp_liq (id_mov,
										monto_bs, 
										nro_declaracion,
										anio,
										id_contribuyente,
										id_base_calculo_veh,
										id_tipo_transaccion,
										ramo) ";

						$q.="VALUES ($movimiento,
							     $this->impuesto,
							    '$declaracion',
							     $anio_a_liquidar,
							     $contribuyente,
							     $this->id_base_calculo,
							     $id_transaccion,
							     $ramo)";

						$conn->Execute($q);
					}

					if ($this->recargo>0){

						/*
						 *	TRANSACCION: CUANTIFICAR RECARGO		
     			 	 		 */
 
						$id_transaccion=3;

						$q="SELECT ramo 
						    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
						    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='3')";
						
						$r=$conn->Execute($q);
						
						$ramo = $r->fields['ramo'];	

						$q="INSERT INTO vehiculo.imp_liq (id_mov,
										  monto_bs, 
										  nro_declaracion,
										  anio,
										  id_contribuyente,
										  id_base_calculo_veh,
										  id_tipo_transaccion,
										  ramo)";

						$q.="VALUES ($movimiento,
							     $this->recargo,
							    '$declaracion',
							     $anio_a_liquidar,
							     $contribuyente,
							     $this->id_base_calculo,
							     $id_transaccion,
							     $ramo)";

						$conn->Execute($q);

					}
				}
	
				$r->movenext();
			}

						
		}

		



		public function Cuantifica($conn, $ultimo_pago, $tipo_vehiculo, $primera_vez, $contribuyente, $declaracion){

			/*
			 *	LIMPIANDO REGISTROS DE LA TABLE IMP_LIQ
			 */
			//echo $primera_vez;
			//echo $ultimo_pago;
			 
			$q="DELETE FROM vehiculo.imp_liq WHERE nro_declaracion='$declaracion'";
			$conn->Execute($q);

	
			/*
			 *	CUANTIFICANDO
			 */
	
			$r = $conn->Execute("select * from nextval('vehiculo.id_movimiento_seq')");
			$movimiento = $r->fields['nextval'];
			

			// array utilizado para mostrar el valor del impuesto del año actual
			$mivcollection=array();
			
			//echo $tipo_vehiculo;
			/*$ap=date("Y", strtotime($ultimo_pago));
			die($ap);*/
			if((date("Y",strtotime($ultimo_pago))==date("Y")) and ($primera_vez==0))
			{
				$this->miv = 0;
			}
			else
			{
				for ($i = date("Y") ; $i <= date("Y") ; $i++)
				{
					$mivliquidacion = new liquidacion;
					$mivliquidacion->Liquida_ver($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento);		
					$mivcolleccion[] = $mivliquidacion;
					$this->miv = $mivliquidacion->impuesto;
				}
			}
			// array utilizado para mostrar el valor de la deuda morosa
			
			
			$dmcollection=array(); 
			
			if (date("Y",strtotime($ultimo_pago))<2000)
				{
					$cond = 1999;
				}
			else
				{
					$cond = date("Y",strtotime($ultimo_pago));
				}

			for ($i = date("Y")-1 ; $i > $cond ; $i--){
				//echo "anio ".$i."<br>";
				$dmliquidacion = new liquidacion;
				$dmliquidacion->Liquida_ver($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento);		
				$dmcolleccion[] = $dmliquidacion;
				$this->dm += $dmliquidacion->impuesto;
				//echo $this->dm;
			}
			
			// array utilizado para mostrar el valor del monto de recargos por morosidad
			$rmcollection=array();
			//echo $ver = date("Y",strtotime($ultimo_pago));
			if (date("Y",strtotime($ultimo_pago))<=1999)
				{
					$cond = 1999;
				}
			else
				{
					$cond = date("Y",strtotime($ultimo_pago));
				}

			for ($i = date("Y") ; $i > $cond ; $i--){
				$rmliquidacion = new liquidacion;
				$rmliquidacion->Liquida_ver($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento);		
				$rmcolleccion[] = $rmliquidacion;
				$this->rm += $rmliquidacion->recargo;
				//echo $this->rm;
			}
			
			$q= "SELECT monto FROM vehiculo.costo_calcomania WHERE anio='".date("Y")."'";
			$r = $conn->Execute($q);
				
			$this->calcomania = $r->fields['monto']; //echo $ver = date("Y",strtotime($ultimo_pago));
			
		
			if(date("Y",strtotime($ultimo_pago)) == date("Y")) { $this->calcomania=0; }
			
			//if (empty($ultimo_pago) or $ultimo_pago == 0){ $primera_vez=true;}
			
			if ($primera_vez == 0 ) {
			
				if (	( date("Y")-date("Y",strtotime($ultimo_pago)) ) > 7){
				    $ultimo_pago = date("Y")-7;
					$ultimo_pago.= '-12-31';
				}

				$this->tasa = 0;
			}

			if ($primera_vez == 1) 
			{ 
			
				$ult=date("Y")-date("Y",strtotime($ultimo_pago));
				
									
				if ($ult == 7) 
				{
					$ultimo_pago = date("Y")-7;
					//$ultimo_pago= '1999-12-31';
				} 
				elseif (	($ult >= 0) and ($ult < 7) )
				{
					$ultimo_pago = date("Y",strtotime($ultimo_pago));
					$ultimo_pago.='-12-31';
				}
				elseif ($ult > 7)
				{
				    $ultimo_pago = date("Y")-7;
					$ultimo_pago.= '-12-31';
				}

				$q= "SELECT monto FROM vehiculo.tasa_inscripcion "; 
				$q .= "WHERE (fecha_desde <='".date("Y-m-d")."' AND fecha_hasta >='".date("Y-m-d")."') ";
				$r = $conn->Execute($q);
				$ver = $this->tasa = $r->fields['monto'];
			}

			//echo $ultimo_pago;
			$collection=array();
			
			//echo $i = date("Y",strtotime($ultimo_pago));
			if(date("Y",strtotime($ultimo_pago))==2006)
			{
				for ($i = date("Y"); $i >= (date("Y",strtotime($ultimo_pago))); $i--)
				{	
					$oliquidacion = new liquidacion;	
					$oliquidacion->Liquida($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento);
					$colleccion[] = $oliquidacion;
				
					$this->total += $oliquidacion->impuesto_mas_recargo; 			
				}
			}
			else
			{
				for ($i = date("Y"); $i > (date("Y",strtotime($ultimo_pago))); $i--)
				{	
					$oliquidacion = new liquidacion;	
					$oliquidacion->Liquida($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$movimiento);
					$colleccion[] = $oliquidacion;
				
					$this->total += $oliquidacion->impuesto_mas_recargo; 			
				}
			}	
										
			$this->total += $this->calcomania + $this->tasa;
				
			/*
			 *	ALMACENANDO EN IMP_LIQ: TASA DE INSCRIPCION Y COSTO CALCOMANIA			
			 */

			$year=date('Y');

			if ($this->tasa>0){
				
				/*
				 *	TRANSACCION: CUANTIFICAR TASA DE INSCRIPCION		
				 */

				$id_transaccion=5;
									
				$q="SELECT ramo 
				    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
				    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='5')";
						
				$r=$conn->Execute($q);
						
				$ramo = $r->fields['ramo'];	

				$q="INSERT INTO vehiculo.imp_liq (id_mov,
								  monto_bs,
								  nro_declaracion,
								  anio,
								  id_contribuyente,
								  id_tipo_transaccion,
								  ramo) ";

				$q.="VALUES ($movimiento,
					     $this->tasa,
					    '$declaracion',
					     $year,
					     $contribuyente,
					     $id_transaccion,
					     $ramo)";

				$conn->Execute($q);
			}

			if ($this->calcomania>0){

				/*
				 *	TRANSACCION: CUANTIFICAR COSTO DE CALCOMANIA		
				 */

				$id_transaccion=6;

				$q="SELECT ramo 
				    FROM vehiculo.ramo_imp a, vehiculo.ramo_transaccion b 
				    WHERE (a.id=b.id_ramo_imp AND b.id_tipo_transaccion='6')";
						
				$r=$conn->Execute($q);
						
				$ramo = $r->fields['ramo'];	
		

				$q="INSERT INTO vehiculo.imp_liq (id_mov,
								  monto_bs, 
								  nro_declaracion, 
								  anio,
								  id_contribuyente,
								  id_tipo_transaccion,
								  ramo) ";

				$q.="VALUES ($movimiento,
					     $this->calcomania,
					    '$declaracion',
					     $year,
					     $contribuyente,
					     $id_transaccion,
					     $ramo)";

				$conn->Execute($q);
			}


			return $colleccion;
		
		}

		
	}
?>
