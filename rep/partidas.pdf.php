<?
	include("comun/ini.php");
	include("Constantes.php");
	
		
		$oparpre = new partidas_presupuestarias;
		$rparpre = $oparpre->get_reporte($conn, $_REQUEST['id_escenario']);
		//die(var_dump($rparpre));
	
class PDF extends FPDF
{
  var $leftMargin = 15;
  var $rightMargin = 280;
  var $fontStyle = 'Courier';
  var $fontBodySize = 8;
  var $fontHeaderSize = 6;
  var $fontHeaderTitleSize = 12;
  var $cellColWidth = 5;
  var $cellDescWidth = 199;
  var $cellTotalWidth = 35;
  var $codSector;
  var $descSector;
  var $escEnEje;

	function Header()
	{
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);
			$this->Image ("images/logoa.jpg",$this->leftMargin+1,5,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(225, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(55,2, "DIRECCION DE PLANIFICACION Y PROYECTOS\nPLANIFICACION DE ADMINISTRACION", 0, 'R');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell($this->rightMargin-$this->leftMargin,2, "PRESUPUESTO DE GASTOS DEL\n\nMUNICIPIO A NIVEL DE PARTIDAS Y SUB-PARTIDAS", 0, 'C');

			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: Año ' . date ('Y'));
      //$this->Text(16, 48, 'Presupuesto: Año ' . $this->escEnEje);
			//---- Parte donde va el código y el nombre del progarma.
			//$this->Rect($this->leftMargin, 51, $this->rightMargin-$this->leftMargin, 4);
			$this->SetY(51);
			//$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			//$this->Cell(($this->cellColWidth*5)+1, 4, 'Codigo', RB, '', 'L');
			//$this->Cell($this->cellColWidth, 4, $this->codSector, R, '', 'C');
			//$this->Cell($this->cellDescWidth+$this->cellTotalWidth, 4, 'Denominacion del Sector: ' . $this->descSector, B, '', 'C');
			//$this->Ln(4);
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
		$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF('L');
$pdf->codSector = '00';
$pdf->descSector = 'Falta';
$pdf->escEnEje = $escEnEje;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);

$tPresupuesto = 0;
$control = 0;
$y=48;

if (is_array($rparpre))
{
	
  $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
	foreach ($rparpre as $report)
  {
		$cod_ini = substr($report->id, 0, 3);

    $maxPal = intval($pdf->cellDescWidth/$pdf->GetStringWidth('0'));
    //die(strlen($report->descripcion));
    if (strlen($report->descripcion) >= $maxPal)
    {
      $multiLine = true;
      $strArray = array();
      do
      {
        if (strlen($report->descripcion) >= $maxPal)
          $posF = strrpos( substr( $report->descripcion, 0, $maxPal ), ' ' );
        else
          $posF = -1;
        
        if ($posF===false || $posF==-1)
        {
          $strArray[] = substr( $report->descripcion, 0 );
          $report->descripcion = substr( $report->descripcion, 0 );
          $posF = -1;
        }
        else
        {
          $strArray[] = substr( $report->descripcion, 0, $posF );
          $report->descripcion = substr( $report->descripcion, $posF );
        }
      }while ($posF != -1);
    }
    //die('entro '.$report->descripcion);
    if ($report->madre == '1')
      $pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);

    $pdf->Cell($pdf->cellColWidth+1, 4, substr($report->id, 0, 3), L, '', 'C');
    $pdf->Cell($pdf->cellColWidth, 4, substr($report->id, 3, 2), L, '', 'C');
    $pdf->Cell($pdf->cellColWidth, 4, substr($report->id, 5, 2), L, '', 'C');
    $pdf->Cell($pdf->cellColWidth, 4, substr($report->id, 7, 2), L, '', 'C');
    $pdf->Cell($pdf->cellColWidth, 4, substr($report->id, 9, 2), L, '', 'C');
    $pdf->Cell($pdf->cellColWidth, 4, substr($report->id, 11, 2), L, '', 'C');
    
    if ($multiLine)
  		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[0]), L, '','L' );
    else
      $pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($report->descripcion), L, '', 'L');
    
    if ($report->madre == '1')
    {
			$query = "SELECT ";
      $query .= "Sum(presupuesto_original) AS tpresupuesto ";
      $query .= "FROM "; 
      $query .= "puser.relacion_pp_cp ";
      $query .= "WHERE ";
      $query .= "puser.relacion_pp_cp.id_partida_presupuestaria LIKE  '".$cod_ini."%' ";
      $query .= "AND ";
      $query .= "id_escenario = '$escEnEje'";
	  //die($query);
			$tot_part_madre = $conn->Execute($query);
			
			$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($tot_part_madre->fields['tpresupuesto']), LR, '','R');
      
      if ($multiLine)
      {
    	  for ($i=1; next($strArray); $i++)
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

      $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);
			$tPresupuesto+= $tot_part_madre->fields['tpresupuesto'];
		}
    else
    {
  		
		$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($report->presupuesto), LR, '','R');
		//die('echo'.$report->presupuesto.' aqui');		
      if ($multiLine)
      {
    	  for ($i=1; next($strArray); $i++)
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
  	}
	//die('entro: '.$report->presupuesto);	
		$pdf->Ln(4);
		$multiLine = false;
	}
}

$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES', 1, '', 'C' );
$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($tPresupuesto), 1, '', 'R' );
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES Bs.F.: ', 1, '', 'C' );
	$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat($tPresupuesto/1000), 1, '', 'R' );
}
$pdf->Output();
?>
