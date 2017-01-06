<?
	include("comun/ini.php");
	include("Constantes.php");

$id_cp = $_GET['id_cp']; 
$escenario = $_REQUEST['id_escenario']; 	
	
		$oppcp = new relacion_pp_cp;
		$rppcp = $oppcp->reporte_pp_cp($conn, $escenario, $id_cp);
	
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
			$this->SetFont($this->fontStyle,'',$this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);

			$this->Image ("images/logoa.jpg",16,5,26);//logo a la izquierda 
			$this->SetXY(42, 22); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->SetXY(170, 22); 
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell(0,2, "CREDITOS PRESUPUESTARIOS DEL PROGRAMA\n\nA NIVEL DE PARTIDAS Y SUB-PARTIDAS", 0, 'C');
			
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: Año ' . date ('Y');
			//$this->Text(16, 48, 'Presupuesto: Año ' . $this->escEnEje);
			//---- Parte donde va el código y el nombre del progarma.
			$this->Rect($this->leftMargin, 51, $this->rightMargin-$this->leftMargin, 8);
			$this->SetY(51);
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell(($this->cellColWidth*6)+1, 4, 'Codigo', RB, '', 'L');
			$this->Cell($this->cellDescWidth+$this->cellTotalWidth, 4, 'Denominacion', B, '', 'C');
			$this->Ln();
			$this->Cell(($this->cellColWidth*5)+1, 4, 'Programa', R, '', 'L');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Cell($this->cellColWidth, 4, $this->codPrograma, R, '', 'C');
			$this->Cell($this->cellDescWidth+$this->cellTotalWidth, 4, $this->descPrograma, 0, '', 'C');
			$this->Ln(6);
			//---- fin Parte donde va el código y el nombre del progarma.
			
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
			$this->Ln();
			//---- fin Cabeceras de partidas y subpartidas, denominación y monto
	}

	function Footer()
	{	
		$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->codPrograma = substr($rppcp[0]->id_cp, 0, 2);
$pdf->descPrograma = $rppcp[0]->desc_cp;
$pdf->escEnEje = $escEnEje;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);
//session_unset();
//session_destroy();

$total_presupuesto = 0;
$control = 0;
if (is_array($rppcp))
{
  $cod_part_ant = '';
  $cod_part_act = '';
	foreach ($rppcp as $report) 
	{
    $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
		$cod_part_act = substr($report->id_pp,0,3);

		if ($cod_part_act != $cod_part_ant)
		{
		  $query = "SELECT ";
		  $query .= "puser.partidas_presupuestarias.id AS id_pp, ";
		  $query .= "puser.partidas_presupuestarias.descripcion AS partida_presupuestaria ";
		  $query .= "FROM ";
		  $query .= "puser.partidas_presupuestarias ";
		  $query .= "WHERE ";
		  $query .= "puser.partidas_presupuestarias.id = '" . $cod_part_act . "0000000000' ";
		  $query .= "AND ";
		  $query .= "puser.partidas_presupuestarias.id_escenario = '" . $escenario . "'";
		  
		  $madre = $conn->Execute($query);

		  $query = "SELECT ";
		  $query .= "Sum(presupuesto_original) as tPresupuesto ";
		  $query .= "FROM ";
		  $query .= "puser.relacion_pp_cp ";
		  $query .= "WHERE ";
		  $query .= "puser.relacion_pp_cp.id_partida_presupuestaria LIKE '" . $cod_part_act ."%' ";
		  $query .= "AND ";
		  $query .= "puser.relacion_pp_cp.id_categoria_programatica = '" . $rppcp[0]->id_cp . "' ";
      $query .= "AND ";
      $query .= "puser.relacion_pp_cp.id_escenario = '" . $escenario . "'"; 
      
      $totalMadre = $conn->Execute($query);
      
  		$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
  		$pdf->Cell($pdf->cellColWidth+1,4, substr($madre->fields['id_pp'], 0, 3), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($madre->fields['id_pp'], 3, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($madre->fields['id_pp'], 5, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($madre->fields['id_pp'], 7, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($madre->fields['id_pp'], 9, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($madre->fields['id_pp'], 11, 2), L, '','C' );
  		$pdf->Cell($pdf->cellDescWidth,4, utf8_decode($madre->fields['partida_presupuestaria']), L, '','L' );
  		$pdf->Cell($pdf->cellTotalWidth,4, muestrafloat($totalMadre->fields['tpresupuesto']), LR, '','R' );
  		$pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
  		$pdf->Ln(4);
    }

    $maxPal = intval($pdf->cellDescWidth/$pdf->GetStringWidth('0'));
    if (strlen($report->desc_pp) >= $maxPal)
    {
      $strArray = array();
      do
      {
        if (strlen($report->desc_pp) >= $maxPal)
          $posF = strrpos( substr( $report->desc_pp, 0, $maxPal ), ' ' );
        else
          $posF = -1;
        
        if ($posF===false || $posF==-1)
        {
          $strArray[] = substr( $report->desc_pp, 0 );
          $report->desc_pp = substr( $report->desc_pp, 0 );
          $posF = -1;
        }
        else
        {
          $strArray[] = substr( $report->desc_pp, 0, $posF );
          $report->desc_pp = substr( $report->desc_pp, $posF );
        }
      }while ($posF != -1);
      
      $pdf->Cell($pdf->cellColWidth+1, 4, substr($report->id_pp, 0, 3), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 3, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 5, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 7, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 9, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth, 4, substr($report->id_pp, 11, 2), L, '','C' );
  		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[0]), L, '','L' );
  	  $pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($report->monto), LR, '','R' );
  	  
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
    	  $pdf->Cell($pdf->cellTotalWidth, 4, '', LR, '','R' );
      }
    }
    else
    {
      $pdf->Cell($pdf->cellColWidth+1,4, substr($report->id_pp, 0, 3), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 3, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 5, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 7, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 9, 2), L, '','C' );
  		$pdf->Cell($pdf->cellColWidth,4, substr($report->id_pp, 11, 2), L, '','C' );
  		$pdf->Cell($pdf->cellDescWidth,4, utf8_decode($report->desc_pp), L, '','L' );
  	  $pdf->Cell($pdf->cellTotalWidth,4, muestrafloat($report->monto), LR, '','R' );
		}
    $total_presupuesto += $report->monto;
		$pdf->Ln(4);
		
		$cod_part_ant = $cod_part_act;
	}
	
$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES', 1, '', 'C' );
$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($total_presupuesto), 1, '', 'R' );

if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES Bs.F.:', 1, '', 'C' );
	$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($total_presupuesto/1000), 1, '', 'R' );
}
}

$pdf->Output();
?>
