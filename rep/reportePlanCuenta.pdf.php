<?
include("comun/ini.php");
include("Constantes.php");

$sql = "SELECT id, codcta, descripcion FROM contabilidad.plan_cuenta ORDER BY codcta::text";
$r = $conn->Execute($sql);

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
	// Definen el ancho de las celdas
	var $codCtaW = 30;
	var $descCtaW = 150;

	//Cabecera de página
	function Header()
	{
			$this->SetFillColor(240);
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(163, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(12);
			$this->SetFont('Courier', 'B', 12);
			$titulo = "Plan Único de Cuenta";
			$this->MultiCell(0, 2, utf8_decode($titulo), 0, 'C');
			$this->Ln(6);
			
			$this->SetFont('Courier', 'B', 8);
			$this->Cell($this->codCtaW, 4, utf8_decode("Código"), TB);
			$this->Cell($this->descCtaW, 4, utf8_decode("Descripción"), TB);
			$this->Ln(5);
	}

	//Pie de página
	function Footer()
	{
		//Arial italic 8
		$this->SetFont('Arial','I', 7);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','', 8);
$pdf->SetLeftMargin(15);
$pdf->SetFillColor(240);

$i = 0;
while(!$r->EOF)
{
	$descCta = dividirStr(utf8_decode($r->fields['descripcion']), intval($pdf->descCtaW/$pdf->GetStringWidth('M')));

	$pdf->Cell($pdf->codCtaW, 4, $r->fields['codcta'], 0, '', 'L', ($i%2));
	$pdf->Cell($pdf->descCtaW, 4, $descCta[0], 0, '', 'L', ($i%2));
	$pdf->Ln();
	
	while(next($descCta))
	{
		$pdf->Cell($pdf->codCtaW, 4, '', 0, '', 'L', ($i%2));
		$pdf->Cell($pdf->descCtaW, 4, current($descCta), 0, '', 'L', ($i%2));
		$pdf->Ln();
	}
	
	$i++;
	$r->movenext();
}

$pdf->Output();
?>
