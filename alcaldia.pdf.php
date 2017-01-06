<?
	include("comun/ini.php");
	include("Constantes.php");
	
	$oescenarios = new escenarios;
	$rescenarios = $oescenarios->get($conn, $escEnEje);
	
	$oalcaldia = new alcaldia;
	$ralcaldia = $oalcaldia->get($conn, '02');

	function dividirStr($str, $max)
  {
    $strArray = array();
    do
    {
      if (strlen($str) > $max)
        $posF = strrpos( substr($str, 0, $max), ' ' );
      else
        $posF = -1;
      
      if ($posF===false || $posF==-1)
      {
        $strArray[] = substr($str, 0);
        $str = substr($str, 0);
        $posF = -1;
      }
      else
      {
        $strArray[] = substr($str, 0, $posF);
        $str = substr($str, $posF+1 );
      }
    }while ($posF != -1);
    
    return ($strArray);
  }

	
	
	
	class PDF extends FPDF
	{
		function Header()
		{
				$this->SetLineWidth(0.3);
				$this->Line(15, 6, 191, 6);
				$this->Line(14.5, 6, 14.5, 72);
				$this->Line(191.5, 6, 191.5, 72);
				$this->SetLeftMargin(2);
				$this->SetFont('Courier','',8);
				$this->Ln(2);
				//$this->SetXY(20, 20); 
				$textoCabecera1 = UBICACION."\n";
				$this->Text(20, 15, $textoCabecera1, 0, 'L');
				$this->Ln(10);
				//$textoCabecera2= "MUNICIPIO:  SANTOS MICHELENA\n";
				//$this->Text(20, 20, $textoCabecera2, 0, 'L');
				$this->Ln(10);
				$this->SetFont('Courier','b',12);
				//$this->SetXY(40, 40);
				$tipoOrden1 = ENTE."\n";
				$this->Text(55, 45, $tipoOrden1, 0, 'C');
				$this->Ln(10);
				$this->SetFont('Courier','',8);
				//$this->SetXY(20, 50);
				//$anio = utf8_decode('AÑO');
				
				$tipoOrden2 = "PRESUPUESTO: AÑO ".date('Y');
				$this->Text(20, 70,$tipoOrden2, 0 , 'L');
				$this->SetLineWidth(0.3);
				$this->Line(15, 72, 191, 72);
				$this->Line(15, 74, 191, 74);
				$this->Line(14.5, 210, 14.5, 74);
				$this->Line(191.5, 210, 191.5, 74);
				$this->Ln(10);
		}
		
		function Footer()
		{	
	
			//$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial', 'I', 8);
			//Número de página
			//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
		}

	}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier', '', 8);
$pdf->SetLeftMargin(20);


$pdf->Ln(25);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 4, 'DOMICILIO LEGAL:', 0, '','L');
$pdf->SetLineWidth(0.3);
$pdf->Line(15, 82, 191, 82);
$pdf->Ln(8);
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(120,4, strtoupper($oalcaldia->domicilio), 0, '', 'L');
$pdf->SetLineWidth(0.3);
$pdf->Line(15, 90, 191, 90);
$pdf->Ln(8);
$pdf->SetFont('Courier', 'B', 8);
$fecha_creacion = "FECHA DE CREACIÓN:";
$pdf->Line(15, 97, 191, 97);
$pdf->Cell(20, 4, $oalcaldia->fecha_creacion, 0, '', 'L');
$pdf->Line(15, 112, 191, 112);
$pdf->Ln(12);
$pdf->SetFont('Courier', '', 8);
//$pdf->Cell(120, 4, utf8_decode('Gaceta Oficial del Estado Carabobo, Numero Extraordinario 616 Resolucion Nº 003, de Fecha ').strtoupper($oalcaldia->fecha_creacion), 0, '', 'L');
$pdf->Ln(16);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(30, 4, 'Ciudad', 0, '', 'L');
$pdf->Line(45, 140, 45, 112);
$pdf->Cell(30, 4, 'Estado', 0, '', 'L');
$pdf->Line(70, 140, 70, 112);
$pdf->Cell(30, 4, 'Telefonos', 0, '', 'L');
$pdf->Line(108, 140, 108, 112);
$pdf->Cell(24, 4, 'Fax', 0, '', 'R');
$pdf->Line(150, 140, 150, 112);
$pdf->Cell(50, 4, 'Codigo Postal', 0, '', 'R');
$pdf->Line(15, 125, 191, 125);
$pdf->Ln(14);
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(30, 4, $oalcaldia->ciudad, 0, '', 'L');
$pdf->Cell(30, 4, $oalcaldia->estado, 0, '', 'L');
$pdf->Cell(30, 4, $oalcaldia->telefono, 0, '', 'L');
$pdf->Cell(44, 4, $oalcaldia->fax, 0, '', 'L');
$pdf->Cell(20, 4, $oalcaldia->cpostal, 0, '', 'R');
$pdf->Line(15, 140, 191, 140);
$pdf->Ln(12);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(30, 4, 'ALCALDE', 0, '', 'L');
$pdf->Line(15, 151, 191, 151);
$pdf->Ln(12);
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(150,4, $oalcaldia->alcalde, 0, '', 'C');
$pdf->Line(15, 165, 191, 165);
$pdf->Ln(12);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(30,4, 'PERSONAL DIRECTIVO', 0, '', 'L');
$pdf->Line(15, 175, 191, 175);
$pdf->Ln(12);
$pdf->SetFont('Courier', '', 8);
$desc_personal = dividirStr(utf8_decode($oalcaldia->personal),intval(170/$pdf->GetStringWidth('M')));
	$pdf->Cell(180,4, $desc_personal[0],0, '','L');
		$hay_per = next($desc_personal);
  		for ($i=1; $hay_per!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(180,4, $desc_personal[$i],0, '','L');
    			$hay_per = next($desc_personal);
  		}

$pdf->Line(15, 210, 191, 210);




$pdf->Output();
?>