<?
	include("comun/ini.php");
	include("Constantes.php");

$id_cp = $_GET['id_cp']; 
$escenario = $_REQUEST['id_escenario']; 	
	
		$oppcp = new relacion_pp_cp;
		$rppcp = $oppcp->reporte_pp_cp($conn, $escenario, $id_cp, 1);
		//var_dump($rppcp);
$partOrdenadas = array();
$montOrdenados = array();	
//var_dump($rppcp);

class PDF extends FPDF
{
  var $leftMargin = 15;
  var $rightMargin = 195;
  var $fontStyle = 'Courier';
  var $fontBodySize = 8;
  var $fontHeaderSize = 6;
  var $fontTableBodySize = 7;
  var $fontHeaderTitleSize = 12;
  var $cellColWidth = 4;
  var $cellDescWidth = 68;
  var $cellOrdi = 19;
  var $cellCoordi = 18;
  var $cellLaee = 15;
  var $cellFides = 15;
  var $cellTotalWidth = 20;
  var $codPrograma;
  var $descPrograma;
  var $escEnEje;
  var $anoCurso;
  
	function Header()
	{
		//var_dump($this);
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle,'',$this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);

			$this->Image ("images/logoa.jpg",16,5,26);//logo a la izquierda 
			$this->SetXY(42, 22); 
			$textoCabecera.= UBICACION."\n";
			//$textoCabecera.= "MUNICIPIO: SANTOS MICHELENA";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->SetXY(170, 22); 
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0,2, "CREDITOS PRESUPUESTARIOS DEL PROGRAMA\n\nA NIVEL DE PARTIDAS", 0, 'C');
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: A駉 ' . $this->anoCurso);
			
			//---- Parte donde va el c贸digo y el nombre del progarma.
			$this->Rect($this->leftMargin, 51, $this->rightMargin-$this->leftMargin, 8);
			$this->SetY(51);
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell(($this->cellColWidth*6)+1, 4, 'Codigo', RB, '', 'L');
			$this->Cell($this->cellDescWidth+$this->cellTotalWidth+$this->cellOrdi+$this->cellCoordi+$this->cellLaee+$this->cellFides, 4, 'Denominacion', B, '', 'C');
			$this->Ln();
			$this->Cell(($this->cellColWidth*5)+1, 4, 'Programa', R, '', 'L');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Cell($this->cellColWidth, 4, $this->codPrograma, R, '', 'C');
			$this->Cell($this->cellDescWidth+$this->cellTotalWidth, 4, utf8_decode($this->descPrograma), 0, '', 'C');
			$this->Ln(6);
			//---- fin Parte donde va el c贸digo y el nombre del progarma.
			
			//---- Cabeceras de partidas y subpartidas, denominaci贸n y monto
			$this->Cell($this->cellColWidth+1, 4, '',  TLR);
			$this->Cell(20, 4, 'Subpartidas', TRB);
			$this->Cell($this->cellDescWidth, 4, '', TR);
			$this->Cell($this->cellOrdi, 4, '', T);
			$this->Cell($this->cellCoordi, 4, '', T, '', 'C');
			$this->Cell($this->cellLaee, 4, '', T, '', 'C');
			$this->Cell($this->cellFides, 4, '', T, '', 'C');
			$this->Cell($this->cellTotalWidth, 4, '', TR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', 0, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', 0, '', 'C');
			$this->Cell($this->cellLaee, 3, '', 0, '', 'C');
			$this->Cell($this->cellFides, 3, '', 0, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', R, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'A', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', 0, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', 0, '', 'C');
			$this->Cell($this->cellLaee, 3, '', 0, '', 'C');
			$this->Cell($this->cellFides, 3, '', 0, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', R, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'R', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'N', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', 0, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', LRT, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', LRT, '', 'C');
			$this->Cell($this->cellLaee, 3, '', LRT, '', 'C');
			$this->Cell($this->cellFides, 3, '', LRT, '', 'C');
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellTotalWidth, 3, 'Total', LRT, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Ln();

			$this->Cell($this->cellColWidth+1, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellDescWidth, 4, 'Denominacion', LR, '', 'C');
			$this->Cell($this->cellOrdi, 4, 'Ordinario', LR, '', 'C');
			$this->Cell($this->cellCoordi, 4, 'Coordinado', LR, '', 'C');
			$this->Cell($this->cellLaee, 4, 'LAEE', LR, '', 'C');
			$this->Cell($this->cellFides, 4, 'FIDES', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 4, 'Programa', LR, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', LR, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', LR, '', 'C');
			$this->Cell($this->cellLaee, 3, '', LR, '', 'C');
			$this->Cell($this->cellFides, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', LR, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', LR, '', 'C');
			$this->Cell($this->cellLaee, 3, '', LR, '', 'C');
			$this->Cell($this->cellFides, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellOrdi, 3, '', LRB, '', 'C');
			$this->Cell($this->cellCoordi, 3, '', LRB, '', 'C');
			$this->Cell($this->cellLaee, 3, '', LRB, '', 'C');
			$this->Cell($this->cellFides, 3, '', LRB, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LRB, '', 'C');
			$this->Ln();
			//---- fin Cabeceras de partidas y subpartidas, denominaci贸n y monto
	}

	function Footer()
	{	
		$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}

//Creaci贸n del objeto de la clase heredada
$pdf=new PDF();
$pdf->codPrograma = substr($rppcp[0]->id_cp, 0, 2);
$pdf->descPrograma = $rppcp[0]->desc_cp;
$pdf->escEnEje = $escEnEje;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);

$total_presupuesto = 0;
$total_ordinario = 0;
$total_coordinado = 0;
$total_fides = 0;
$total_laee = 0;
$control = 0;
if (is_array($rppcp))
{
  $cod_part_ant = '';
  $cod_part_act = '';
	foreach ($rppcp as $report) 
	{
   	 	$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$codPart = str_split(substr($report->id_pp,3,10),2 );
		$indice = array_search('00',$codPart);
		$cad = substr($report->id_pp,0,3);
		$cadMadre = str_pad($cad,13,'0');
		if(isset($partOrdenadas[$cadMadre])){
				$partOrdenadas[$cadMadre]['ordinario'] += $report->ordinario; 
				$partOrdenadas[$cadMadre]['coordinado'] += $report->coordinado; 
				$partOrdenadas[$cadMadre]['fides'] += $report->fides; 
				$partOrdenadas[$cadMadre]['laee'] += $report->laee; 
				$partOrdenadas[$cadMadre]['monto'] += $report->monto; 			
				
		}else{
			$q = "SELECT descripcion FROM puser.partidas_presupuestarias WHERE id = '$cadMadre' AND id_escenario = '$escenario'";
			//die($q);
			$row = $conn->Execute($q);
			$partOrdenadas[$cadMadre]= array();
			$partOrdenadas[$cadMadre]['descripcion'] = $row->fields['descripcion'];
			$partOrdenadas[$cadMadre]['monto'] = $report->monto; 
			$partOrdenadas[$cadMadre]['ordinario'] = $report->ordinario; 
			$partOrdenadas[$cadMadre]['coordinado'] = $report->coordinado; 
			$partOrdenadas[$cadMadre]['fides'] = $report->fides; 
			$partOrdenadas[$cadMadre]['laee'] = $report->laee; 
			$partOrdenadas[$cadMadre]['madre'] = 1;  
		}
		/*for($i=0; $i<$indice; $i++){
			$cad = $cad.$codPart[$i];
			$part = str_pad($cad,13,'0');
			if(isset($partOrdenadas[$part])){
				$partOrdenadas[$part]['monto'] += $report->monto; 			
			}else{
				$partOrdenadas[$part] = array();
				$q = "SELECT descripcion, madre FROM puser.partidas_presupuestarias WHERE id = '$part' AND id_escenario = '$escenario'";
				$row = $conn->Execute($q);
				$partOrdenadas[$part]['descripcion'] = $row->fields['descripcion'];
				$partOrdenadas[$part]['ordinario'] = $report->ordinario; 
				$partOrdenadas[$part]['coordinado'] = $report->coordinado; 
				$partOrdenadas[$part]['fides'] = $report->fides; 
				$partOrdenadas[$part]['laee'] = $report->laee; 
				$partOrdenadas[$part]['monto'] = $report->monto; 
				$partOrdenadas[$part]['madre'] = $row->fields['madre'];  
			}
		}*/
	}
	//die(var_dump($partOrdenadas));
	foreach($partOrdenadas as $cod=>$reporte){
	
	if($reporte['madre']==1){
		if(substr($cod,3,10) == '0000000000'){
			$total_presupuesto += $reporte['monto'];
			$total_ordinario += $reporte['ordinario'];
			$total_cooordinado += $reporte['coordinado'];
			$total_fides += $reporte['fides'];
			$total_laee += $reporte['laee'];
		}
		$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontTableBodySize);
	}else{
		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontTableBodySize);
		//$this->SetFont($this->fontStyle, 'B', $this->fontTableBodySize);
	}	
		$maxPal = intval($pdf->cellDescWidth/$pdf->GetStringWidth('0'));
		if (strlen($reporte['descripcion']) >= $maxPal)
		{
		  $strArray = array();
		  do
		  {
			if (strlen($reporte['descripcion']) >= $maxPal)
			  $posF = strrpos( substr( $reporte['descripcion'], 0, $maxPal ), ' ' );
			else
			  $posF = -1;
			
			if ($posF===false || $posF==-1)
			{
			  $strArray[] = substr( $reporte['descripcion'], 0 );
			  $reporte['descripcion'] = substr( $reporte['descripcion'], 0 );
			  $posF = -1;
			}
			else
			{
			  $strArray[] = substr( $reporte['descripcion'], 0, $posF );
			  $reporte['descripcion'] = substr( $reporte['descripcion'], $posF );
			}
		  }while ($posF != -1);
		  
		  $pdf->Cell($pdf->cellColWidth+1, 4, substr($cod, 0, 3), L, '','C' );
			$pdf->Cell($pdf->cellColWidth, 4, substr($cod, 3, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth, 4, substr($cod, 5, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth, 4, substr($cod, 7, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth, 4, substr($cod, 9, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth, 4, substr($cod, 11, 2), L, '','C' );
			$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[0]), L, '','L' );
		  	$pdf->Cell($pdf->cellOrdi, 4, muestrafloat($reporte['ordinario']), LR, '','R' );
			$pdf->Cell($pdf->cellCoordi, 4, muestrafloat($reporte['coordinado']), LR, '','R' );
			$pdf->Cell($pdf->cellFides, 4, muestrafloat($reporte['fides']), LR, '','R' );
			$pdf->Cell($pdf->cellLaee, 4, muestrafloat($reporte['laee']), LR, '','R' );
			$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($reporte['monto']), LR, '','R' );
		  
		  for ($i=1,$lon=count($strArray); next($strArray); $i++)
		  {
			  $pdf->Ln(4);
			$pdf->Cell($pdf->cellColWidth+1, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
				$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[$i]), L, '','L' );
			  	$pdf->Cell($pdf->cellOrdi, 4, '', LR, '','R' );
				$pdf->Cell($pdf->cellCoordi, 4, '', LR, '','R' );
				$pdf->Cell($pdf->cellFides, 4, '', LR, '','R' );
				$pdf->Cell($pdf->cellLaee, 4, '', LR, '','R' );
				$pdf->Cell($pdf->cellTotalWidth, 4, '', LR, '','R' );
		  }
		}
		else
		{
		  $pdf->Cell($pdf->cellColWidth+1,4, substr($cod, 0, 3), L, '','C' );
			$pdf->Cell($pdf->cellColWidth,4, substr($cod, 3, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth,4, substr($cod, 5, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth,4, substr($cod, 7, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth,4, substr($cod, 9, 2), L, '','C' );
			$pdf->Cell($pdf->cellColWidth,4, substr($cod, 11, 2), L, '','C' );
			$pdf->Cell($pdf->cellDescWidth,4, utf8_decode($reporte['descripcion']), L, '','L' );
		  	$pdf->Cell($pdf->cellOrdi, 4, muestrafloat($reporte['ordinario']), LR, '','R' );
			$pdf->Cell($pdf->cellCoordi, 4, muestrafloat($reporte['coordinado']), LR, '','R' );
			$pdf->Cell($pdf->cellFides, 4, muestrafloat($reporte['fides']), LR, '','R' );
			$pdf->Cell($pdf->cellLaee, 4, muestrafloat($reporte['laee']), LR, '','R' );
			$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($reporte['monto']), LR, '','R' );
			}
			
		//$total_presupuesto += $report->monto;
			$pdf->Ln(4);
			
			//$cod_part_ant = $cod_part_act;
		
	}	
	$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontTableBodySize);
	$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES', 1, '', 'C' );
	$pdf->Cell($pdf->cellOrdi, 4, muestrafloat($total_ordinario), BR, '', 'R' );
	$pdf->Cell($pdf->cellCoordi, 4, muestrafloat($total_coordinado), BR, '', 'R' );
	$pdf->Cell($pdf->cellFides, 4, muestrafloat($total_fides), BR, '', 'R' );
	$pdf->Cell($pdf->cellLaee, 4, muestrafloat($total_laee), BR, '', 'R' );
	$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($total_presupuesto), BR, '', 'R' );


}

$pdf->Output();
?>
