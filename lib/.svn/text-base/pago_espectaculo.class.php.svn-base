<?
	class pago_espectaculo{

		// Propiedades
		
		var $entradas_1;
		var $cant_1;
		var $aforo_1;
		var $ut;
		var $entradas_2;
		var $cant_2;
		var $aforo_2;
		var $entradas_3;
		var $cant_3;
		var $aforo_3;
		var $patente;
		var $total_entradas_1;
		var $total_entradas_2;
		var $total_entradas_3;
		var $total_impuesto;
		var $tipo_entrada_1;
		var $tipo_entrada_2;
		var $tipo_entrada_3;
		var $cant_entrada_1;
		var $cant_entrada_2;
		var $cant_entrada_3;
		var $monto_entradas_1;
		var $monto_entradas_2;
		var $monto_entradas_3;
		var $monto_ut;
		var $forma_pago;
		var $banco;
		var $nro_documento;
		var $ut_monto;
		
		
			function get($conn, $patente)
			{
				$q = "SELECT 
						publicidad.espectaculo.*,  
						publicidad.calculo_espectaculo.*
					  FROM
						publicidad.espectaculo
					  INNER JOIN
						publicidad.calculo_espectaculo
					  ON
						(publicidad.espectaculo.patente=publicidad.calculo_espectaculo.patente)	
					  WHERE 
						publicidad.espectaculo.patente = '$patente'";//die($q);
				$r = $conn->Execute($q);
				if(!$r->EOF)
				{
					$this->tipo_entrada_1 = $r->fields['id_entradas_1'];
					$this->tipo_entrada_2 = $r->fields['id_entradas_2'];
					$this->tipo_entrada_3 = $r->fields['id_entradas_3'];  
					$this->cant_entrada_1 = $r->fields['cant_entradas_1'];
					$this->cant_entrada_2 = $r->fields['cant_entradas_2'];
					$this->cant_entrada_3 = $r->fields['cant_entradas_3'];  
					$this->monto_entradas_1 = $r->fields['monto_entradas_1'];
					$this->monto_entradas_2 = $r->fields['monto_entradas_2'];
					$this->monto_entradas_3 = $r->fields['monto_entradas_3'];
					$this->monto_ut = $r->fields['ut_espectaculo'];
					$this->total_entradas_1 = $r->fields['total_entradas_1'];
					$this->total_entradas_2 = $r->fields['total_entradas_2'];
					$this->total_entradas_3 = $r->fields['total_entradas_3']; 
					$this->total_impuesto = $r->fields['total_impuesto'];
					$this->forma_pago = $r->fields['id_forma_pago'];
					$this->banco = $r->fields['id_banco'];
					$this->nro_documento = $r->fields['nro_documento'];
					
					return true;
				}
				else
					return false;
			}
									
						 	
		
			function cuantifica($conn, 
								$entradas_1, 
								$cant_1, 
								$aforo_1, 
								$ut, 
								$entradas_2, 
								$cant_2, 
								$aforo_2, 
								$entradas_3, 
								$cant_3, 
								$aforo_3,
								$ut_monto,
								$patente){
							
							$total_entradas_1 = $cant_1 * $aforo_1 * ($ut * $ut_monto);
							$total_entradas_2 = $cant_2 * $aforo_2 * ($ut * $ut_monto);
							$total_entradas_3 = $cant_3 * $aforo_3 * ($ut * $ut_monto);
							
							$total_impuesto = $total_entradas_1 + $total_entradas_2 + total_entradas_3;
							
							/*$opago_espectaculo = new pago_espectaculo;	

							$opago_espectaculo->set_pago_espectaculo($conn, 
													$total_entradas_1,
													$total_entradas_2,
													$total_entradas_3,
													$total_impuesto,
													$patente);*/
										
							return $total_impuesto; 
			}
			
			function set_pago_espectaculo(	$conn, 
											$entradas_1, 
											$cant_1, 
											$aforo_1, 
											$ut, 
											$entradas_2, 
											$cant_2, 
											$aforo_2, 
											$entradas_3, 
											$cant_3, 
											$aforo_3,
											$forma_pago,
											$banco,
											$nro_documento,
											$ut_monto,
											$patente){
											
							$total_entradas_1 = $cant_1 * $aforo_1 * ($ut * $ut_monto);
							$total_entradas_2 = $cant_2 * $aforo_2 * ($ut * $ut_monto);
							$total_entradas_3 = $cant_3 * $aforo_3 * ($ut * $ut_monto);
							
							$total_impuesto = $total_entradas_1 + $total_entradas_2 + total_entradas_3;
											
							$q = "UPDATE publicidad.calculo_espectaculo SET";
							$q.= " total_entradas_1 = $total_entradas_1,";
							$q.= " total_entradas_2 = $total_entradas_2,";
							$q.= " total_entradas_3 = $total_entradas_3,";
							$q.= " total_impuesto = $total_impuesto,";
							$q.= " id_forma_pago = $forma_pago,";
							$q.= " id_banco = $banco,";
							$q.= " nro_documento = $nro_documento";
							$q.= " WHERE patente = '$patente'";//die($q);
							
				if($conn->Execute($q))
					return true;
				else
					return false;		
			}
}
?>