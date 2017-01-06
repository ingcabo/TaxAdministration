<?php

include("comun/ini.php");
set_time_limit(0);

$_SESSION['conex'] = $conn;

class PDF extends FPDF
{
	//Cabecera de página
	function Header()
	{
		$this->SetLeftMargin(5);
		$this->SetFont('Courier','',10);
		$this->Ln(1);
		$this->Image ("images/logoa.jpg",5,4,66,20);//logo a la izquierda 
		
		$separaFecha= explode('-',date('Y-m-d'));
		$dia = $separaFecha[2];
		$mes = $separaFecha[1];
		$ano = $separaFecha[0];
		$this->Ln(20);	
		$this->Cell(190,5, 'MARACAIBO, '.$dia.' DE '.strtoupper(obtieneMes($mes)).' DE '.$ano,0,0, 'R','L');
		$this->Ln(10);
	}
	//Pie de página
	function Footer()
	{
		$conn = $_SESSION['conex'];
		$this->SetFont('Arial','',8);
		$this->Ln(10);
		$this->Cell(50,5,"Elaborado por",1,0,'C');
		$this->SetX(75);
		$this->Cell(50,5,"Firma 1",1,0,'C');
		$this->SetX(145);
		$this->Cell(50,5,"Firma 2",1,0,'C');
		$this->Ln(5);
		$this->Cell(50,30,"",1,0,'C');
		$this->SetX(75);
		$this->Cell(50,30,"",1,0,'C');
		$this->SetX(145);
		$this->Cell(50,30,"",1,0,'C');
		$this->Ln(25);
		$this->Cell(50,5,"EDWIN LEAL",0,0,'C');
	}
}

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','',10);

$conn = $_SESSION['conex'];
$q = "	SELECT
			C.descripcion,
			B.nro_cuenta,
			A.monto
		FROM
			finanzas.transferencias AS A
			INNER JOIN finanzas.cuentas_bancarias AS B ON B.id = A.id_cuenta_cedente
			INNER JOIN public.banco AS C ON B.id_banco = C.id
		WHERE
			A.nrodoc = '".$_GET['id']."'";
$rN = $conn->Execute($q);
$q_r = "SELECT
			A.fecha,
			A.descripcion as concepto,
			C.descripcion,
			B.nro_cuenta
		FROM
			finanzas.transferencias AS A
			INNER JOIN finanzas.cuentas_bancarias AS B ON B.id = A.id_cuenta_receptora
			INNER JOIN public.banco AS C ON B.id_banco = C.id
		WHERE
			A.nrodoc = '".$_GET['id']."'";
//var_dump($q_r);
$rR = $conn->Execute($q_r);

$montoLetras = num2letras(muestraFloat($rN->fields['monto']),false,true);
$pdf->Cell(190,5, 'Sres.: '.utf8_decode($rN->fields['descripcion']) ,0, 0, 'L','L');
$pdf->ln(15);
$pdf->SetX(10);
$pdf->MultiCell(180,5,'Sirva la presente para autorizar nos debiten de la cuenta '.$rN->fields['nro_cuenta'].' la cantidad de '.$montoLetras.' (Bs.'. muestraFloat($rN->fields['monto']).')', 0, 'L');
$pdf->ln(5);
$pdf->SetX(10);
$pdf->MultiCell(180,5,'Por el concepto que se define a continuación', 0, 'L');

$pdf->ln(10);
$pdf->Cell(190,5, 'CONCEPTO',1, 0, 'C','C');
$pdf->ln(5);
$pdf->SetDrawColor (0,0,0);
$pdf->Cell(190,100, $rR->fields['concepto'],1, 0, 'L');
$pdf->ln(55);
$pdf->SetX(10);
$pdf->Cell(180,100,'Para acreditar en la cuenta '.$rR->fields['nro_cuenta'].' del '.$rR->fields['descripcion'].'.',0, 0, 'L');
$pdf->Ln(70);
$separaFecha= explode('-',$rR->fields['fecha']);
$dia = $separaFecha[2];
$mes = $separaFecha[1];
$ano = $separaFecha[0];
$pdf->Cell(190,5,"NUESTRO CONTROL		".$ano.'-'.$mes.'-'.$_GET['id'],0,0,'R');
$pdf->Output();

?>
