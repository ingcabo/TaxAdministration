<?php

include("comun/ini.php");
$tipoReporte = $_REQUEST['tipo'];

if($tipoReporte=='1')
{
	$oCheque = new cheque;
}
elseif($tipoReporte=='2')
{
	$oCheque = new cheque_anteriores;
}
$oCheque->get($conn, $_REQUEST['id']);

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
    }
	while ($posF != -1);
    return ($strArray);
}

class PDF extends FPDF
{
	//Cabecera de página
	function Header()
	{
		$this->SetLeftMargin(5);
	}
	//Pie de página
	function Footer()
	{
		
	}
}

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$json = new Services_JSON;
$cadena = str_replace("\\","",$oCheque->json);//-->OJO, MUY IMPORTANTE !!!
$vector = $json->decode($cadena);
$x = count($vector);
$docs = '';

for ($i=0;$i<$x;$i++)
{
	$docs.= $vector[$i]->nroref.'  ';
}

// CHEQUE
$monto = $oCheque->montoTotalCheque($conn, $oCheque->nrodoc);

$pdf->Cell(75,5,'',0, 0, 'L','L');
$pdf->Cell(50,5,'',0, 0, 'C','C');
$pdf->Cell(65,5, '***********'.utf8_decode(muestraFloat($monto)),0, 0, 'R','R');
$pdf->ln(5);
$pdf->Cell(190,5,'',0, 0, 'L','C');
$pdf->ln(15);
$pdf->SetX(20);
$pdf->Cell(180,5, '***'. utf8_decode($oCheque->nomBenef).'***',0, 0, 'L','C');
$pdf->ln(5);
$montoLetras = num2letras($monto,false,true);
$desc_monto = dividirStr($montoLetras, intval(240/$pdf->GetStringWidth('M')));
$pdf->Cell(190,5, strtoupper(utf8_decode($desc_monto[0])),0, 0, 'L','L');
$pdf->ln(5);
$separaFecha= explode('-',$oCheque->fecha);
$dia = $separaFecha[2];
$mes = $separaFecha[1];
$ano = $separaFecha[0];
$pdf->Cell(190,5, 'MARACAIBO, '.$dia.' DE '.strtoupper(obtieneMes($mes)).' DE '.$ano,0,0, '','L');
$pdf->ln(5);
$pdf->Cell(190,5,'NO ENDOSABLE, CADUCA A LOS 90 DIAS',0,0, '','L');
$pdf->Output();

?>
