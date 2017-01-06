<?
	include("comun/ini.php");
	include("Constantes.php");
	
	//die($escEnEje);
	
	$oparpre = new partidas_presupuestarias;
	
	$rparpre = $oparpre->get_reporte_ingresos($conn, $escEnEje);

	//if(empty($oOrden->nrodoc))
	//header ("location: orden_servicio_trabajo.php");
	//$_SESSION['pdf'] = serialize($ovehiculo);
	//$_SESSION['pdf'] = serialize($oliquidacion);

	function get($conn, $id, $EscEnEje, $a=0){
		$num = strlen($id);
		$completa = 13 - $num;
		$idnew = $id;
		//echo "id: ".$id."<br>";
		//echo "completa ".$completa."<br>";
		for($i=0;$i<$completa;$i++){
			$idnew.='0';
		}
		$q = "SELECT id,descripcion, ingreso, (SELECT SUM(ingreso) AS total ";	
		$q.= "FROM puser.partidas_presupuestarias ";
		$q.= "WHERE substring(id,1,".$num.") LIKE '$id%' and id_escenario = 1111) AS total ";
		$q.= "FROM puser.partidas_presupuestarias ";
		$q.= "WHERE id = '$idnew' AND id_escenario = $EscEnEje";
		//if($a==1) echo $q."<br>";
		$r = $conn->Execute($q);
		$coleccion = array();
		if($r){
			
			$coleccion['id'] = $r->fields['id'];
			$coleccion['descripcion'] = $r->fields['descripcion'];
			$coleccion['ingreso'] = $r->fields['total'];
			$coleccion['madre'] = $r->fields['ingreso']; // Si retorna este valor en 0 significa que la partida es madre
		}
		$a = 0;
		return $coleccion;
	}	
	
	class PDF extends FPDF
	{
	var $leftMargin = 15;
    var $rightMargin = 195;
    var $fontStyle = 'Courier';
    var $fontBodySize = 8;
    var $fontHeaderSize = 6;
    var $fontHeaderTitleSize = 12;
    var $cellColWidth = 5;
    var $cellDescWidth = 105;
    var $cellTotalWidth = 45;
		function Header()
		{
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Rect($this->leftMargin, 10, $this->rightMargin-$this->leftMargin, 30);
			
			$this->Ln(1);
			$this->SetXY(16, 12); 
			$this->MultiCell(0, 2, UBICACION."\n", 0, 'L');
			
			$this->SetXY(150, 12); 
			$this->MultiCell(45, 2, DEPARTAMENTO."\n", 0, 'R');
			$this->Ln();
			
			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0, 28, "PRESUPUESTO DE INGRESOS", 0, 'C');
			$this->Ln();
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(17, 38, 'PRESUPUESTO: AÑO ' . date('Y'));
			$this->Line(15, 44, 191, 44);
			
			$this->SetY(44);
			$this->SetLeftMargin($this->leftMargin);
				$this->SetFont($this->fontStyle, 'B', $this->fontHeaderSize);
			//---- Cabeceras de partidas y subpartidas, denominación y monto
			$this->Cell($this->cellColWidth+1, 4, '',  TLR);
			$this->Cell(25, 4, 'Subpartidas', TRB);
			$this->Cell($this->cellDescWidth, 4, '', TR);
			$this->Cell($this->cellTotalWidth, 4, '', TR);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'A', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'R', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'N', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();

			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellDescWidth, 3, 'Denominacion', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, 'Total Programa', LR, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LRB, '', 'C');
			$this->Ln();
			//---- fin Cabeceras de partidas y subpartidas, denominación y monto
		}
	
		function Footer()
		{	
			$this->SetFont('Arial', 'I', 8);
		}
	}
	
	//Creación del objeto de la clase heredada
	$pdf=new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Courier', '', 8);
	//$pdf->SetLeftMargin(15);
	
	$q1 = "SELECT substring(id,1,3):: varchar as id1 ";
	$q1.= "FROM puser.partidas_presupuestarias ";
	$q1.= "WHERE id LIKE '30%' AND id_escenario = $escEnEje ";
	$q1.= "GROUP BY id1";
	//die($q1);
	$r1 = $conn->Execute($q1);
	while(!$r1->EOF){
		
		$pdf->SetFont('Courier', 'B', 8);
		$dato = get($conn, $r1->fields['id1'], $escEnEje,1);
		//die(print_r($dato));
		if (is_array($dato)){
			if($dato['ingreso']!=''){
			//echo "ingreso: ".$dato['ingreso']."<br>";
			$pdf->Ln(3);
			$pdf->Cell($pdf->cellColWidth+1, 4, substr($dato['id'],0,3), 'LRBT', '','C');
			$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],3,2), 'LRBT', '','C');
			$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],5,2), 'LRBT', '','C');
			$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],7,2), 'LRBT', '','C');
			$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],9,2), 'LRBT', '','C');
			$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],11,2), 'LRBT', '','C');
			$pdf->Cell($pdf->cellDescWidth, 4, $dato['descripcion'], 'LRBT', '', 'L');
			$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($dato['ingreso']).' Bs.', 'LRBT', '', 'R');
			}
			$pdf->Ln(3);
			//die ("=".$dato['id']);
		}
		//$pdf->Ln();
		$q2 = "SELECT substring(id,1,5):: varchar as id2 ";
		$q2.= "FROM puser.partidas_presupuestarias ";
		$q2.= "WHERE id LIKE '".$r1->fields['id1']."%' AND id_escenario = $escEnEje ";
		$q2.= "GROUP BY id2 ";
		$q2.= "ORDER BY id2 ";
		$r2 = $conn->Execute($q2);
		
		while(!$r2->EOF){
			if(substr($r2->fields['id2'],3,2)!='00'){
				//$pdf->Ln(1);
				
				$dato = get($conn, $r2->fields['id2'], $escEnEje,1);
				if (is_array($dato)){
					$pdf->Ln(1);
					if($dato['ingreso']>0){
						if ($dato['madre']==0){
							$pdf->SetFont('Courier', 'B', 8);
						} else {
							 $pdf->SetFont('Courier', '', 8);
							}
					$pdf->Cell($pdf->cellColWidth+1, 4, substr($dato['id'],0,3), 'LRBT', '','C');
					$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],3,2), 'LRBT', '','C');
					$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],5,2), 'LRBT', '','C');
					$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],7,2), 'LRBT', '','C');
					$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],9,2), 'LRBT', '','C');
					$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],11,2), 'LRBT', '','C');
					$pdf->Cell($pdf->cellDescWidth,4, $dato['descripcion'], 'LRBT', '', 'L');
					$pdf->Cell($pdf->cellTotalWidth,4, muestrafloat($dato['ingreso']).' Bs.', 'LRBT', '', 'R');
					$pdf->Ln(3);
					}
				}
			
				//$pdf->Ln();
				$q3 = "SELECT substring(id,1,7):: varchar as id3 ";
				$q3.= "FROM puser.partidas_presupuestarias ";
				$q3.= "WHERE id LIKE '".$r2->fields['id2']."%' AND id_escenario = $escEnEje ";
				$q3.= "GROUP BY id3 ";
				$q3.= "ORDER BY id3 ";
				//die($q3);
				$r3 = $conn->Execute($q3);
				while(!$r3->EOF){
					/*echo $r3->fields['id3']."<br>";
					echo(":".substr($r3->fields['id3'],5,2)."<br>");*/
					if(substr($r3->fields['id3'],5,2)!='00'){
					
					$dato = get($conn, $r3->fields['id3'], $escEnEje);
					if (is_array($dato)){
						$pdf->Ln(1);
						if($dato['ingreso']>0){
							if ($dato['madre']==0){
								$pdf->SetFont('Courier', 'B', 8);
							} else {
								 $pdf->SetFont('Courier', '', 8);
								}
							$pdf->Cell($pdf->cellColWidth+1, 4, substr($dato['id'],0,3), 'LR', '','C');
							$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],3,2), 'LR', '','C');
							$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],5,2), 'LR', '','C');
							$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],7,2), 'LR', '','C');
							$pdf->Cell($pdf->cellColWidth,4, substr($dato['id'],9,2), 'LR', '','C');
							$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],11,2), 'LR', '','C');
							$pdf->Cell($pdf->cellDescWidth, 4, $dato['descripcion'], 'LR', '', 'L');
							$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($dato['ingreso']).' Bs.', 'LR', '', 'R');
							$pdf->Ln(3);
						}
					}
					$q4 = "SELECT substring(id,1,9):: varchar as id4 ";
					$q4.= "FROM puser.partidas_presupuestarias ";
					$q4.= "WHERE id LIKE '".$r3->fields['id3']."%' AND id_escenario = $escEnEje ";
					$q4.= "GROUP BY id4 ";
					$q4.= "ORDER BY id4 ";
					//die($q3);
					$r4 = $conn->Execute($q4);
					//$control2= $r3->fields['id3'];
					while(!$r4->EOF){
					/*echo $r4->fields['id4']."<br>";
					echo(":".substr($r4->fields['id4'],7,2)."<br>");*/
						if(substr($r4->fields['id4'],7,2)!='00'){
						
							
							$dato = get($conn, $r4->fields['id4'], $escEnEje);
							if (is_array($dato)){
								$pdf->Ln(1);
								if($dato['ingreso']>0){
									if ($dato['madre']==0){
										$pdf->SetFont('Courier', 'B', 8);
									} else {
										 $pdf->SetFont('Courier', '', 8);
										}
									$pdf->Cell($pdf->cellColWidth+1, 4, substr($dato['id'],0,3), 'LR', '','C');
									$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],3,2), 'LR', '','C');
									$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],5,2), 'LR', '','C');
									$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],7,2), 'LR', '','C');
									$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],9,2), 'LR', '','C');
									$pdf->Cell($pdf->cellColWidth, 4, substr($dato['id'],11,2), 'LR', '','C');
									$pdf->Cell($pdf->cellDescWidth, 4, $dato['descripcion'], 'LR', '', 'L');
									$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($dato['ingreso']).' Bs.', 'LR', '', 'R');
									$pdf->Ln(3);
								}
							}
							
						}
						$r4->movenext();
					}
					}
					$r3->movenext();
				}
					
			}
			$ante = $r2->fields['id2'];
			$r2->movenext();	
		}
		$r1->movenext();
	}
		
		
		
	
		$q = "select 
				SUM(ingreso) AS monto 
			  from 
			  	puser.partidas_presupuestarias 
			  WHERE 
			  	(puser.partidas_presupuestarias.id LIKE  '30%' and id_escenario = '$escEnEje') 
			  ";
		//die($q);
		$rq = $conn->Execute($q);
		$pdf->Ln(4);
		$pdf->SetFont('Courier', 'B', 8);
		$pdf->Cell(136, 4, 'Total', 'LTRB', '', 'R');		
		$pdf->Cell(45, 4, muestrafloat($rq->fields['monto']).' Bs.', 'LTRB', '', 'R');	
	//}
	$pdf->Output();
?>