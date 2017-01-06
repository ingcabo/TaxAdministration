<?
include("comun/ini.php");
include("Constantes.php");

$sql = "SELECT a.id, a.codcta, a.descripcion, b.id_partida_presupuestaria AS id_partida, c.descripcion AS partida FROM contabilidad.plan_cuenta a
LEFT JOIN contabilidad.relacion_cc_pp b ON (a.id = b.id_cuenta_contable)
LEFT JOIN puser.partidas_presupuestarias c ON (b.id_partida_presupuestaria = c.id AND c.id_escenario = '$escEnEje') ORDER BY codcta::text";

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
	var $descCtaW = 65;
	var $codParW = 30;
	var $descParW = 65;
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
			$this->MultiCell(0, 2, $titulo, 0, 'C');
			$this->Ln(6);
			
			$this->SetFont('Courier', 'B', 8);
			$this->Cell($this->codCtaW, 4, "Código", TB);
			$this->Cell($this->descCtaW, 4, "Descripción", TB);
			$this->Cell($this->codParW, 4, "Partida", TB);
			$this->Cell($this->descParW, 4, "Descripción", TB);
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
	$descPar = dividirStr(utf8_decode($r->fields['partida']), intval($pdf->codParW/$pdf->GetStringWidth('M')));

	
	$pdf->Cell($pdf->codCtaW, 4, $r->fields['codcta'], 0, '', 'L', ($i%2));
	$pdf->Cell($pdf->descCtaW, 4,$descCta[0], 0, '', 'L', ($i%2));
	$pdf->Cell($pdf->codParW, 4, $r->fields['id_partida'], 0, '', 'L', ($i%2));
	$pdf->Cell($pdf->descParW, 4,$descPar[0] , 0, '', 'L', ($i%2));
	$pdf->Ln();

	$count = count($descPar) >= count($descCta) ? count($descPar) : count($descCta);
	for($j=1;$j<$count;$j++){
		$pdf->Cell($pdf->codCtaW, 4, '', 0, '', 'L', ($i%2));
		$pdf->Cell($pdf->descCtaW, 4, $descCta[$j] ? $descCta[$j] : '', 0, '', 'L', ($i%2));
		$pdf->Cell($pdf->codParW, 4, '', 0, '', 'L', ($i%2));
		$pdf->Cell($pdf->descParW, 4, $descPar[$j] ? $descPar[$j] : '' , 0, '', 'L', ($i%2));
		$pdf->Ln();
	}
	
	$i++;
	$r->movenext();
}

$pdf->Output();
?>
