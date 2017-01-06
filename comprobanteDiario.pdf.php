<?
include("comun/ini.php");
include("Constantes.php");

$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];

$oComprobante = new comprobante($conn);
$cComprobantes = $oComprobante->get_all('', '', '', $fecha_desde, $fecha_hasta);

// Crea un array donde cada posicion es un string de tamaño 'max' caracteres,
// teniendo en cuenta de no cortar una palabra, busca el espacio en blanco  
// mas cerca del tamaño 'max' y ahi corta el string

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
	var $fechaDesde;
	var $fechaHasta;
	
	// Definen el ancho de varias celdas que se utilizan en el documento
	var $fechaEmiW = 25;
	var $descW = 70;
	var $numComW = 27;
	var $tipoDocW = 28;
	var $numDocW = 25;
	var $codctaW = 31;
	var $descDetW = 84;
	var $debeW = 30;
	var $haberW = 30;
	var $descTotW = 115;
	
	
	//Cabecera de página
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(10);
			$this->SetFont('Courier','b',12);
			$titulo = "Comprobante de Diario\n\n";
			$titulo.= (empty($this->fechaHasta) ? "Desde ":"").(empty($this->fechaDesde) ? "Hasta ":$this->fechaDesde);
			$titulo.= (empty($this->fechaHasta) ? "":(empty($this->fechaDesde) ? "":" Al ").$this->fechaHasta);

			$this->MultiCell(0, 2, utf8_decode($titulo), 0, 'C');
			$this->Ln(8);
			
			$this->SetFont('Courier','B',8);
			$this->SetLineWidth(0.3);
			$this->Cell($this->fechaEmiW, 4, utf8_decode('Fecha Emisión'), T);
			$this->Cell($this->numComW, 4, utf8_decode('Nº Comprobante'), T);
			$this->Cell($this->descW, 4, utf8_decode("Descripción del Asiento"), T);
			$this->Cell($this->tipoDocW, 4, 'Tipo Documento', T);
			$this->Cell($this->numDocW, 4, utf8_decode('Nº Documento'), T);
			$this->Ln();
			$this->Cell($this->codctaW, 4, "Cuenta Contable", B);
			$this->Cell($this->descDetW, 4, utf8_decode("Descripción"), B);
			$this->Cell($this->debeW, 4, "Debe", B, '', 'C');
			$this->Cell($this->haberW, 4, "Haber", B, '', 'C');
			$this->SetLineWidth(0.2);
			$this->Ln();
			$this->Ln();
	}

	//Pie de página
	function Footer()
	{
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->fechaDesde = $fecha_desde;
$pdf->fechaHasta = $fecha_hasta;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);
$pdf->SetFillColor(240);

foreach($cComprobantes as $comprobante)
{
	$desc_com = dividirStr(utf8_decode($comprobante->descrip), intval($pdf->descW/$pdf->GetStringWidth('M')));

	//die($comprobante->fecha);
	$pdf->Cell($pdf->fechaEmiW, 4, $comprobante->fecha, 0, '', 'C');
	$pdf->Cell($pdf->numComW, 4, $comprobante->numcom, 0, '', 'C');
	$pdf->Cell($pdf->descW, 4, $desc_com[0], 0);
	$pdf->Cell($pdf->tipoDocW, 4, utf8_decode($comprobante->aux), 0, '', 'C');
	$pdf->Cell($pdf->numDocW, 4, $comprobante->num_doc, 0, '', 'C');
	
	$hay_com = next($desc_com);
	for ($i=1; $hay_com!==false; $i++)
	{
		$pdf->Ln();
		$pdf->Cell($pdf->fechaEmiW, 4, '', 0);
		$pdf->Cell($pdf->numComW, 4, '', 0);
		$pdf->Cell($pdf->descW, 4, $desc_com[$i], 0);
		$pdf->Cell($pdf->tipoDocW, 4, '', 0);
		$pdf->Cell($pdf->numDocW, 4, '', 0);
		$hay_com = next($desc_com);
	}

	$totalDebe = 0;
	$totalHaber = 0;
	$pdf->SetFont('Courier','',8);
	foreach($comprobante->det as $det)
	{
		$desc_det = dividirStr(utf8_decode($det->cuenta->descripcion), intval($pdf->descDetW/$pdf->GetStringWidth('M')));
		$pdf->Ln();
		$pdf->Cell($pdf->codctaW, 4, $det->cod_cta, 0);
		$pdf->Cell($pdf->descDetW, 4, $desc_det[0], 0);
		$pdf->Cell($pdf->debeW, 4, muestrafloat($det->debe), 0, '', 'R');
		$pdf->Cell($pdf->haberW, 4, muestrafloat($det->haber), 0, '', 'R');
		$totalDebe += $det->debe;
		$totalHaber += $det->haber;
			$hay_det = next($desc_det);
			for ($i=1; $hay_det!==false; $i++)
			{
				$pdf->Ln();
				$pdf->Cell($pdf->codctaW, 4, '', 0);
				$pdf->Cell($pdf->descDetW, 4, $desc_det[$i], 0);
				$pdf->Cell($pdf->debeW, 4, '', 0);
				$pdf->Cell($pdf->haberW, 4, '', 0);
				$hay_det = next($desc_det);
			}
	}
	
	$pdf->SetFont('Courier','B',8);
	$pdf->Ln();
	$pdf->Cell($pdf->descTotW, 4, "Total Comprobante", T, '', 'R', 1);
	$pdf->Cell($pdf->debeW, 4, muestrafloat($totalDebe), T, '', 'R', 1);
	$pdf->Cell($pdf->haberW, 4, muestrafloat($totalHaber), T, '', 'R', 1);
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell($pdf->debeW+$pdf->descTotW, 4, 'Bs.F.: '.muestrafloat($totalDebe/1000), T, '', 'R', 1);
		$pdf->Cell($pdf->haberW, 4, muestrafloat($totalHaber/1000), T, '', 'R', 1);
	}
	
	$pdf->Ln();
	/*$pdf->Ln();
	$pdf->SetLineWidth(0.3);
	$pdf->Line(15, $pdf->GetY(), 190, $pdf->GetY());
	$pdf->SetLineWidth(0.2);*/
	$pdf->Ln();
	$pdf->Ln();
}

$pdf->Output();
?>
