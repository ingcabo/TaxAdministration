<?
	class pago_publicidad{

		// Propiedades
		
		var $id;
		var $fecha_actual;
		var $patente;
		var $id_contribuyente;
		var $id_solicitud;
		var $cod_ins;
		var $fec_desde;
		var $fec_hasta;
		var $cod_tipo_publicidad_1;
		var $cod_tipo_publicidad_2;
		var $cod_tipo_publicidad_3;
		var $cod_tipo_publicidad_4;
		var $cantidad_1;
		var $cantidad_2;
		var $cantidad_3;
		var $cantidad_4;
		var $aforo_1;
		var $aforo_2;
		var $aforo_3;
		var $aforo_4;
		var $interes_1;
		var $interes_2;
		var $interes_3;
		var $interes_4;
		var $total_impuesto;
		var $fecha_hoy;
		var $total_impuesto_1;
		var $total_impuesto_2;
		var $total_impuesto_3;
		var $total_impuesto_4;
		var $tipo_pago;
		var $banco;
		var $nro_documento;
		var $impuesto_anual_1;
		var $impuesto_anual_2;
		var $impuesto_anual_3;
		var $impuesto_anual_4;
		var $tb;
		
		
			function get_publicidad_fija($conn, $patente)
			{
				$q = "SELECT 
						publicidad.publicidad.*,  
						publicidad.calculo_publicidad_fija.*
					  FROM
						publicidad.publicidad
					  INNER JOIN
						publicidad.calculo_publicidad_fija
					  ON
						(publicidad.publicidad.patente=publicidad.calculo_publicidad_fija.patente)	
					  WHERE 
						publicidad.publicidad.patente = '$patente'";
				//die($q);
				$r = $conn->Execute($q);
				if(!$r->EOF){
					$this->id = $r->fields['id'];  
					$this->fecha_actual = $r->fields['fecha_actual']; 
					$this->patente = $r->fields['patente']; //---
					$this->id_contribuyente = $r->fields['id_contribuyente'];
					$this->cod_ins = $r->fields['cod_inspector'];
					$this->nombre_contribuyente = $r->fields['primer_nombre']." ".$r->fields['segundo_nombre']." ".$r->fields['primer_apellido']." ".$r->fields['segundo_apellido'];
					$this->razon_contribuyente = $r->fields['razon_social'];
					$this->ciudad_contribuyente = $r->fields['ciudad_domicilio'];
					$this->telefono_contribuyente = $r->fields['telefono'];
					$this->domiciliotrib_contribuyente = $r->fields['domicilio_fiscal'];
					$this->id_solicitud = $r->fields['id_solicitud'];
					$this->fec_desde = $r->fields['fec_desde'];
					$this->fec_hasta = $r->fields['fec_hasta'];
					$this->cod_tipo_publicidad_1 = $r->fields['cod_tipo_publicidad_1'];
					$this->cod_tipo_publicidad_2 = $r->fields['cod_tipo_publicidad_2'];
					$this->cod_tipo_publicidad_3 = $r->fields['cod_tipo_publicidad_3'];
					$this->cod_tipo_publicidad_4 = $r->fields['cod_tipo_publicidad_4'];    
					$this->cantidad_1 = $r->fields['cantidad_1'];
					$this->cantidad_2 = $r->fields['cantidad_2'];
					$this->cantidad_3 = $r->fields['cantidad_3'];
					$this->cantidad_4 = $r->fields['cantidad_4'];      
					$this->aforo_1 = $r->fields['impuesto_1'];
					$this->aforo_2 = $r->fields['impuesto_2'];
					$this->aforo_3 = $r->fields['impuesto_3'];
					$this->aforo_4 = $r->fields['impuesto_4'];
					$this->interes_1 = $r->fields['interes_1'];
					$this->interes_2 = $r->fields['interes_2'];
					$this->interes_3 = $r->fields['interes_3'];
					$this->interes_4 = $r->fields['interes_4'];
					$this->total_impuesto = $r->fields['total_impuesto'];
						  
					
					return true;
				}else
					return false;
			}
									
						 	
		
			function Cuantifica_publicidad_fija($conn, $patente)
			{
					$q = "SELECT 
							publicidad.publicidad.*,  
							publicidad.calculo_publicidad_fija.*
						  FROM
							publicidad.publicidad
						  INNER JOIN
							publicidad.calculo_publicidad_fija
						  ON
							(publicidad.publicidad.patente=publicidad.calculo_publicidad_fija.patente)	
					  	  WHERE 
							publicidad.publicidad.patente = '$patente'";//die($q);
					
					$r = $conn->Execute($q);
					
					$fecha_hoy = $r->fields['fecha_actual'];
					$cantidad_1 = $r->fields['cantidad_1'];
					$cantidad_2 = $r->fields['cantidad_2'];
					$cantidad_3 = $r->fields['cantidad_3'];
					$cantidad_4 = $r->fields['cantidad_4'];
					$aforo_1 = $r->fields['impuesto_1'];
					$aforo_2 = $r->fields['impuesto_2'];
					$aforo_3 = $r->fields['impuesto_3'];
					$aforo_4 = $r->fields['impuesto_4'];//die($aforo_2);
					
					$sql_ut = $conn->Execute("SELECT * FROM publicidad.unidad_tributaria WHERE status = '1'");
					$ut_monto = $sql_ut->fields['monto'];
					
					$sql_tb = $conn->Execute("SELECT * FROM publicidad.tasa_bancaria_publicidad WHERE status = '1'");
					$sql_tb.=" AND ('$fecha_hoy' between fecha_desde AND fecha_hasta) ";
					
					$res = $conn->Execute($sql_tb);
					$tb = $res->fields['monto'];
					
					$tb_monto = $sql_ut->fields['monto'];
					
					
					
							$impuesto_anual_1 = $aforo_1 * $ut_monto; 
							$impuesto_anual_2 = $aforo_2 * $ut_monto; 
							$impuesto_anual_3 = $aforo_3 * $ut_monto; 
							$impuesto_anual_4 = $aforo_4 * $ut_monto; 
							
							$interes_1 = ($tb * 30/3600) * 1.2 * $impuesto_anual_1;
							$interes_2 = ($tb * 30/3600) * 1.2 * $impuesto_anual_2;
							$interes_3 = ($tb * 30/3600) * 1.2 * $impuesto_anual_3;
							$interes_4 = ($tb * 30/3600) * 1.2 * $impuesto_anual_4;
							
							$total_impuesto_1 = $impuesto_anual_1 + $interes_1;
							$total_impuesto_2 = $impuesto_anual_2 + $interes_2;
							$total_impuesto_3 = $impuesto_anual_3 + $interes_3;
							$total_impuesto_4 = $impuesto_anual_4 + $interes_4;
										
							$total_impuesto = $total_impuesto_1 + $total_impuesto_2 + $total_impuesto_3 + $total_impuesto_4;
							//die($total_impuesto);
							return $total_impuesto; 
			}
			
			function set_pago_publicidad_fija(	$conn,	$tipo_pago, $banco, $nro_documento, $patente){
											
									$q = "SELECT 
											publicidad.publicidad.*,  
											publicidad.calculo_publicidad_fija.*
								  		  FROM
											publicidad.publicidad
								  		  INNER JOIN
											publicidad.calculo_publicidad_fija
								  		  ON
											(publicidad.publicidad.patente=publicidad.calculo_publicidad_fija.patente)	
								  		  WHERE 
											publicidad.publicidad.patente = '$patente'";//die($q);
							
										$r = $conn->Execute($q);
										
										$fecha_hoy = $r->fields['fecha_actual'];
										$cantidad_1 = $r->fields['cantidad_1'];
										$cantidad_2 = $r->fields['cantidad_2'];
										$cantidad_3 = $r->fields['cantidad_3'];
										$cantidad_4 = $r->fields['cantidad_4'];
										$aforo_1 = $r->fields['impuesto_1'];
										$aforo_2 = $r->fields['impuesto_2'];
										$aforo_3 = $r->fields['impuesto_3'];
										$aforo_4 = $r->fields['impuesto_4'];//die($aforo_2);
										
										$sql_ut = $conn->Execute("SELECT * FROM publicidad.unidad_tributaria WHERE status = '1'");
										$ut_monto = $sql_ut->fields['monto'];
										
										$sql_tb = $conn->Execute("SELECT * FROM publicidad.tasa_bancaria_publicidad WHERE ( (status = '1') AND ('$fecha_hoy' between fecha_desde AND fecha_hasta) ) ");
										$tb = $sql_tb->fields['monto'];
										
										
							
										$impuesto_anual_1 = $aforo_1 * $ut_monto; 
										$impuesto_anual_2 = $aforo_2 * $ut_monto; 
										$impuesto_anual_3 = $aforo_3 * $ut_monto; 
										$impuesto_anual_4 = $aforo_4 * $ut_monto; 
										
										$interes_1 = ($tb * 30/3600) * 1.2 * $impuesto_anual_1;
										$interes_2 = ($tb * 30/3600) * 1.2 * $impuesto_anual_2;
										$interes_3 = ($tb * 30/3600) * 1.2 * $impuesto_anual_3;
										$interes_4 = ($tb * 30/3600) * 1.2 * $impuesto_anual_4;//die($tb);
										
										//echo "aforo_1 = ".$aforo_1." -  ut_monto = ".$ut_monto." - impuesto_anual_1 = ".$impuesto_anual_1." - "; 
										//echo "interes_1 = ".$interes_1." - "; 
										
										$total_impuesto_1 = $impuesto_anual_1 + $interes_1;
										$total_impuesto_2 = $impuesto_anual_2 + $interes_2;
										$total_impuesto_3 = $impuesto_anual_3 + $interes_3;
										$total_impuesto_4 = $impuesto_anual_4 + $interes_4;//die($ut_monto);
													
										$total_impuesto = $total_impuesto_1 + $total_impuesto_2 + $total_impuesto_3 + $total_impuesto_4;
											
							$w = "UPDATE publicidad.calculo_publicidad_fija SET";
							$w.= " interes_1 = '$interes_1',";
							$w.= " interes_2 = '$interes_2',";
							$w.= " interes_3 = '$interes_3',";
							$w.= " interes_4 = '$interes_4',";
							$w.= " total_impuesto = '$total_impuesto',";
							$w.= " tipo_pago = '$tipo_pago',";
							$w.= " banco = '$banco',";
							$w.= " nro_documento = '$nro_documento'";
							$w.= " WHERE patente = '$patente'";//die($w);
							
				if($conn->Execute($w))
					return true;
				else
					return false;		
			}
}
?>