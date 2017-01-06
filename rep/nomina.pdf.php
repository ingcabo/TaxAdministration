<?
include("comun/ini.php");
$oOrden = new nomina();
$oOrden->get($conn, $_GET['id'], $escEnEje);
if(empty($oOrden->nrodoc))
	header ("location: nomina.php");
$_SESSION['pdf'] = serialize($oOrden);
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = "REPUBLICA BOLIVARIANA DE VENEZUELA\n";
			$textoCabecera.= "TOCUYITO, ESTADO CARABOBO\n";
			$textoCabecera.= "ALCALDIA DEL MUNICIPIO LIBERTADOR\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oOrden = unserialize($_SESSION['pdf']);
			$tipo = $oOrden->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$this->Text(90, 40, 'NOMINA');
			$this->Text(153, 40, "Nro.:".$oOrden->nrodoc."\n");
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);
			
	}

	//Pie de página
	function Footer()
	{
		
		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'IMPUTACION PRESUPUESTARIA',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(100,4, 'Descripcion',0, '','L');
$pdf->Cell(35,4, 'Monto',0, '','C');

$cPartidas = $oOrden->getRelacionPartidas($conn, $oOrden->id, $escEnEje);

foreach($cPartidas as $partida){
	$pdf->Ln();
	$pdf->Cell(40,4, $partida->id_categoria_programatica."-".$partida->id_partida_presupuestaria,0, '','C');
	$pdf->Cell(100,4, $partida->partida_presupuestaria,0, '','L');
	$pdf->Cell(35,4, muestrafloat($partida->monto),0, '','C');
	$montoTotal += $partida->monto;
}

$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(35,4, muestrafloat($montoTotal),0, '','C');
$pdf->Ln();

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(30,25, '',0, '','R');
$pdf->Cell(40,25, '___________________',0, '','C');
$pdf->Cell(40,25, '___________________',0, '','C');
$pdf->Cell(40,25, '___________________',0, '','C');
$pdf->Ln();

$pdf->Output();
?>
