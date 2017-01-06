<?
include("comun/ini.php");
$oOtrosPagos = new otros_pagos;
$oOtrosPagos->get($conn, $_REQUEST['id']);
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
	}

	function Footer()
	{
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(5);
$pdf->SetFont('Times','',14);
$pdf->Ln(1);
$pdf->Image ("images/logoa.jpg",5,4,26);//logo a la izquierda 
$pdf->SetXY(42, 7); 
$pdf->SetXY(150, 7); 
$pdf->Cell(130,5,"Nro Pago: ".$oOtrosPagos->nro_otros_pagos,0,0,'L');
$pdf->Ln(40);
$pdf->Cell(260, 5, utf8_decode("Señores"),0,0,'L');
$pdf->Ln(7);
$pdf->SetFont('Times','B',12);
$pdf->Cell(260,5, utf8_decode($oOtrosPagos->banco),0,0,'L');
$pdf->SetFont('Times','',12);
$pdf->Ln(7);
$pdf->SetFont('Times','',12);
$pdf->Cell(130,5,"Oficina Maracaibo:",0,0,'L');
$aux = explode('-',$oOtrosPagos->fecha);
$pdf->Cell(130,5, "Maracaibo.- ".$aux[2]." de ".obtieneMes($aux[1])." del ".$aux[0], 0, 'R');
$pdf->Ln(13);
$pdf->SetLeftMargin(8);
$pdf->MultiCell(180, 4,utf8_decode("						Yo, Juan España, C.I: 12.522.463, en condicion de firma tipo B autorizo al ".utf8_decode($oOtrosPagos->banco).", para realizar una transferencia bancaria de la cuenta Nº ".$oOtrosPagos->nro_cuenta." a nombre del ENTE EJECUTOR a la cuenta ".$oOtrosPagos->cuentaDestino." a favor de ".utf8_decode($oOtrosPagos->beneficiario)." por un monto de ".utf8_decode(num2letras($oOtrosPagos->montoTotal($conn, $oOtrosPagos->nrodoc),true))." (".muestrafloat($oOtrosPagos->montoTotal($conn, $oOtrosPagos->nrodoc)).") correspondiente a ".$oOtrosPagos->descripcion."   "),0,'J');
$pdf->Ln(6);			
$pdf->Ln(15);
$pdf->Cell(175,5, 'Atentamente','', '','C');
$pdf->Ln(15);
$pdf->Cell(50,5, '','', '','C');
$pdf->Cell(100,5, utf8_decode('Lic. Juan España'),'T', '','C');
$pdf->Cell(50,5, '','', '','C');
$pdf->Ln(5);
$pdf->Cell(50,5, '','', '','C');
$pdf->Cell(100,5, 'Director (A) de Admnistracion',0, '','C');
$pdf->Cell(50,5, '','', '','C');


$pdf->Output();
?>
