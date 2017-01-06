<?
	include("comun/ini.php");
	include("Constantes.php");

$escenario = $_REQUEST['id_escenario']; 	
	
		$oCp = new categorias_programaticas;
		$rppcp = $oCp->getPrograma($conn, $escenario);
		//var_dump($rppcp);

class PDF extends FPDF
{
  var $leftMargin = 15;
  var $rightMargin = 195;
  var $fontStyle = 'Courier';
  var $fontBodySize = 8;
  var $fontHeaderSize = 6;
  var $fontHeaderTitleSize = 12;
  var $cellColWidth = 5;
  var $cellDescWidth = 114;
  var $cellTotalWidth = 35;
  var $codPrograma;
  var $descPrograma;
  var $escEnEje;
  
  
	function Header()
	{
			$this->SetLeftMargin($this->leftMargin);
			//$this->SetRightMargin($this->rightMargin);
			$this->SetFont($this->fontStyle,'',$this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);

			$this->Image ("images/logoa.jpg",16,5,26);//logo a la izquierda 
			$this->SetXY(42, 22); 
			$textoCabeceraIzq = UBICACION."\n";
			//$textoCabeceraIzq.= "MUNICIPIO: SANTOS MICHELENA\n";
			$this->MultiCell(50,2, $textoCabeceraIzq, 0, 'L');
			
			$textoDerecha = utf8_decode("COORDINACIÓN DE PLANIFICACIÓN\n");
			$textoDerecha.= utf8_decode("PLANIFICACIÓN DE ADMINISTRACIÓN\n");
			$this->SetXY(150, 22); 
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0,2, "DESCRIPCION SUB-PROGRAMA", 0, 'C');
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: Año ' . date ('Y'));
			//$this->Text(16, 48, 'Presupuesto: Año ' . $this->escEnEje);
			$this->SetLineWidth(0.1);
			$this->Line(15, 210, 195, 210);
			$this->Line(15, 74, 195, 74);
			$this->Line(14.5, 210, 14.5, 74);
			$this->Line(195, 210, 195, 74);
			
			$this->Ln(6);
			//---- fin Parte donde va el código y el nombre del progarma.
			
			//---- Cabeceras de partidas y subpartidas, denominación y monto
			/*$this->Cell($this->cellColWidth+1, 4, '',  TLR);
			$this->Cell(25, 4, 'Subpartidas', TRB);
			$this->Cell($this->cellDescWidth, 4, '', TR);
			$this->Cell($this->cellTotalWidth, 4, '', TR);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'A', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'R', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'N', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();

			$this->Cell($this->cellColWidth+1, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellDescWidth, 4, 'Denominacion', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 4, 'Total Programa', LR, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LRB, '', 'C');
			$this->Ln();*/
			//---- fin Cabeceras de partidas y subpartidas, denominación y monto
	}

	function Footer()
	{	
		//$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->SetLeftMargin($pdf->leftMargin);
if (is_array($rppcp)){
	foreach($rppcp as $cp){
	
		$pdf->AddPage();
		$pdf->codPrograma = substr($cp->id, 0, 2);
		//echo $cp->descripcion."<br>";
		$pdf->descPrograma = $cp->descripcion;
		$pdf->escEnEje = $escEnEje;
		//---- Parte donde va el código y el nombre del progarma.
		$pdf->Rect($pdf->leftMargin, 51, $pdf->rightMargin-$pdf->leftMargin, 8);
		$pdf->SetY(51);
		$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
		$pdf->Cell(($pdf->cellColWidth*6)+1, 4, 'Codigo', RB, '', 'L');
		$pdf->Cell($pdf->cellDescWidth+$pdf->cellTotalWidth, 4, 'Denominacion', B, '', 'C');
		$pdf->Ln();
		$pdf->Cell(($pdf->cellColWidth*5)+1, 4, 'Sector', R, '', 'L');
		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellColWidth, 4, $pdf->codPrograma, R, '', 'C');
		$pdf->Cell($pdf->cellDescWidth+$pdf->cellTotalWidth, 4, utf8_decode($cp->sector), 0, '', 'C');
		$pdf->Ln();
		$pdf->Cell(($pdf->cellColWidth*5)+1, 4, 'Sector', R, '', 'L');
		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellColWidth, 4, substr($cp->id,2,2), R, '', 'C');
		$pdf->Cell($pdf->cellDescWidth+$pdf->cellTotalWidth, 4, utf8_decode($cp->programa), 0, '', 'C');
		$pdf->Ln();
		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$pdf->Cell(($pdf->cellColWidth*5)+1, 4, 'Programa', LR, '', 'L');
		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellColWidth, 4, substr($cp->id,4,2), R, '', 'C');
		$pdf->Cell($pdf->cellDescWidth+$pdf->cellTotalWidth, 4, utf8_decode($cp->descripcion), 0, '', 'C');
		$pdf->Ln();
		//$pdf->Cell(($pdf->cellColWidth*3)+$pdf->cellDescWidth+$pdf->cellTotalWidth,6,'DESCRIPCION',1,'','C');
		$pdf->Cell(180,6,'DESCRIPCION',1,'','C');
		$pdf->Ln(16);
		// este replace se hace porque los asteriscos al convertirlos en utf8 se vuelven signos de interrogacion (?)
		$aux = str_replace('?',' - ',utf8_decode($cp->dp));
		$pdf->MultiCell(177,4,$aux,0,'J',0);
		
		
		
		//$pdf->Ln();
		
		
		
		
	}
}
$pdf->Output();

?>
