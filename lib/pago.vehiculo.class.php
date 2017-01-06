<?	

	/*  
	 *	PAGO DE VEHICULO
	 */

	class pago {
		
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
		
		
		
		
	
	


		private function AlmacenaDetalle($conn, $anio_a_pagar, $tipo_vehiculo, $declaracion, $contribuyente, $num_pago, $fecha_pago){

			$this->anio=$anio_a_pagar; 
			
			$q="SELECT monto FROM vehiculo.esp_costo_veh ";
			$q.="WHERE (fecha_desde <= '$anio_a_pagar-01-01'AND fecha_hasta >= '$anio_a_pagar-01-01' ";
			$q.="AND cod_veh='$tipo_vehiculo' )";
			
			$r = $conn->Execute($q);
			$this->impuesto = $r->fields['monto'];
			
			
			if ($anio_a_pagar==date("Y")){
				$hoy = date("Y-m-d");	
			} else {
				$hoy = "$anio_a_pagar-12-31";
			}
			
			$q="SELECT recargo,desc_tipo,art_ref,vig_desde,vig_hasta,id FROM vehiculo.base_calculo_veh ";
			$q.="WHERE anio='$anio_a_pagar'";

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
					 *	IMPUESTO Y RECARGO (det_pago)
					 */

					if ($this->impuesto>0){
 
						$id_transaccion=1;				

						$q= "INSERT INTO vehiculo.det_pago (num_pago,
										monto_pago, 
										nro_declaracion,
										anio,
										id_base_calculo,
										id_tipo_transaccion,
										usr_crea,
										fec_crea) ";

						$q.="VALUES ($num_pago,
							     $this->impuesto,
							    '$declaracion',
							     $anio_a_pagar,
							     $this->id_base_calculo,
							     $id_transaccion,
							    '$_SESSION[login]',
					     		    '$fecha_pago')";

						$conn->Execute($q);
					}

					if ($this->recargo>0){
 
						$id_transaccion=3;

					
						$q="INSERT INTO vehiculo.det_pago (num_pago,
										  monto_pago, 
										  nro_declaracion,
										  anio,
										  id_base_calculo,
										  id_tipo_transaccion,
										  usr_crea,
										  fec_crea)";

						$q.="VALUES ($num_pago,
							     $this->recargo,
							    '$declaracion',
							     $anio_a_pagar,
							     $this->id_base_calculo,
							     $id_transaccion,
							    '$_SESSION[login]',
					     		    '$fecha_pago')";


						$conn->Execute($q);

					}
				}
	
				$r->movenext();
			}

						
		}

		



		public function add($conn, $ultimo_pago, $tipo_vehiculo, $primera_vez,$contribuyente,$declaracion,
					   $los_tipos_pagos,$los_bancos,$los_documentos,$los_montos,$total,$calcomania){

			/*
			 *	VARIABLES
			 */
			
			
			$r = $conn->Execute("select * from nextval('vehiculo.num_pago_seq')");
			$num_pago = $r->fields['nextval'];

			$fecha_pago=date("Y-m-d");

			$year=date('Y');

			
			
			/*
			 *	LIMPIANDO REGISTROS DE LA TABLA IMP_LIQ
			 */

			$q="DELETE FROM vehiculo.imp_liq WHERE nro_declaracion='$declaracion'";
			$conn->Execute($q);



			/*
			 *	NUEVO: AGREGANDO REGISTRO DE PAGO EN IMP_PAGO   (un solo registro)
			 */

				
				$id_ramo=4;
		
				$q="SELECT ramo 
				    FROM vehiculo.ramo_imp 
				    WHERE (id=$id_ramo)";
						
				$r=$conn->Execute($q);
						
				$ramo = $r->fields['ramo'];	



				$q="INSERT INTO vehiculo.imp_pago (num_pago,
								  mto_tot_pago,
								  id_contribuyente,
								  calcomania,
								  fecha_pago,
								  ramo,
								  fec_crea,
								  usr_crea) ";

				$q.="VALUES ($num_pago,
					     $total,
					     $contribuyente,
					     '$calcomania',
					     '$fecha_pago',
					      $ramo,
					     '$fecha_pago',
					      '$_SESSION[login]')";

				$conn->Execute($q);



			$q= "SELECT monto FROM vehiculo.costo_calcomania WHERE anio='".date("Y")."'";
			$r = $conn->Execute($q);
				
			$this->calcomania = $r->fields['monto'];
	
			$ultimo_pago.='-12-31';

			if(date("Y",strtotime($ultimo_pago)) == date("Y")) { $this->calcomania=0;}
						
			if (empty($ultimo_pago) or $ultimo_pago == 0){ $primera_vez=true;}

			if ($primera_vez == false ) {
				
				if (	( date("Y")-date("Y",strtotime($ultimo_pago)) ) > 4){
					$ultimo_pago = date("Y")-4;
					$ultimo_pago.= '-12-31';
				}

				$this->tasa = 0;
			}

			if ($primera_vez == true) {

				if (	( date("Y")-date("Y",strtotime($ultimo_pago)) ) > 6) {
					$ultimo_pago = date("Y")-6;
					$ultimo_pago.= '-12-31';

				} 

				$q= "SELECT monto FROM vehiculo.tasa_inscripcion "; 
				$q .= "WHERE (fecha_desde <='".date("Y-m-d")."' AND fecha_hasta >='".date("Y-m-d")."')";
				$r = $conn->Execute($q);
				$this->tasa = $r->fields['monto'];
			}

			
		//	$collection=array();


//********************************************************************************************************************************

			for ($i = date("Y") ; $i > date("Y",strtotime($ultimo_pago)) ; $i--){

				/*
				 *	NUEVO: SE LLAMA AL METODO DE ARRIBA Y SE ESCRIBEN LOS REGISTROS EN DET_PAGO
				 */

				$opago = new pago;	

				$opago->AlmacenaDetalle($conn, $i, $tipo_vehiculo, $declaracion,$contribuyente,$num_pago,$fecha_pago);

				//$colleccion[] = $opago;
				//$this->total += $opago->impuesto_mas_recargo; 			
			}

//**********************************************************************************************************************************/				
			

			//HAY QUE CALCULAR THIS TOTAL						
		//	$this->total += $this->calcomania + $this->tasa;

		

			/*
			 *	MONTO TASA DE INSCRIPCION (det_pago)		
			 */

			if ($this->tasa>0){
				
				$id_transaccion=5;

				$q="INSERT INTO vehiculo.det_pago (num_pago,
								  monto_pago,
								  nro_declaracion,
								  anio,
								  id_tipo_transaccion,
								  usr_crea,
								  fec_crea) ";

				$q.="VALUES ($num_pago,
					     $this->tasa,
					    '$declaracion',
					     '$year',
					     $id_transaccion,
					     $_SESSION[login]
					     '$fecha_pago')";

				$r=$conn->Execute($q);
			}



			/*
			 *	MONTO CALCOMANIA (det_pago)		
			 */	

			if ($this->calcomania>0){

				$id_transaccion=6;	

				$q="INSERT INTO vehiculo.det_pago (num_pago,
								   monto_pago,
								   nro_declaracion,
								   anio,
								   id_tipo_transaccion,
								   usr_crea,
								   fec_crea) ";
	
				$q.=" VALUES ($num_pago,
					      $this->calcomania,
					     '$declaracion',
					      $year,
					      $id_transaccion,
					     '$_SESSION[login]',
					     '$fecha_pago')";


				$r=$conn->Execute($q);
			}

		
			/*
			 *	FORMAS DE PAGO (det_forma_pago)
			 */
			
 			for ($i=0; $i<count($los_montos);  $i++){

				$monto_flotante = guardafloat($los_montos[$i]);
			

				$q="INSERT INTO vehiculo.det_forma_pago (num_pago,
									tipo_pago,
									mto_pago,
									nro_doc,
									cod_banco) ";

				$q.="VALUES ($num_pago,
					     '$los_tipos_pagos[$i]',
					     $monto_flotante,
					     '$los_documentos[$i]',
					     $los_bancos[$i])";

				$r=$conn->Execute($q);
       
			}


			/*
			 *	ACTUALIZA LA FECHA DE PAGO DEL VEHICULO
			 */

			$anio_pago = date("Y",strtotime($fecha_pago));			

			$q="UPDATE vehiculo.vehiculo SET anio_pago='$anio_pago' WHERE id='$declaracion'";
			$r=$conn->Execute($q);

		}


	}
?>
