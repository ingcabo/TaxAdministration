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
		$this->SetFont('Arial','',8);
		$this->Cell(42.5,5,"Elaborado por",1,0,'C');
		$this->SetX(57.5);
		$this->Cell(42.5,5,"FIRMA 1",1,0,'C');
		$this->SetX(110);
		$this->Cell(42.5,5,"FIRMA 2",1,0,'C');
		$this->SetX(162.5);
		$this->Cell(42.5,5,"Recibe conforme",1,0,'C');
		$this->SetY(194);
		$this->Cell(42.5,28,"",1,0,'C');
		$this->SetXY(57.5,194);
		$this->Cell(42.5,28,"",1,0,'C');
		$this->SetXY(110,194);
		$this->Cell(42.5,28,"",1,0,'C');
		$this->SetFont('Arial','',6);
				
		$this->SetXY(162.5,196);
		$this->Cell(42.5,7,"Nombre _________________________",0,0,'L');
		$this->Ln(7);
		$this->SetX(162.5);
		$this->Cell(42.5,7,"Fecha __________________________",0,0,'L');
		$this->Ln(7);
		$this->SetX(162.5);
		$this->Cell(42.5,7,"C.I. ó R.I.F. ______________________",0,0,'L');
		$this->Ln(7);
		$this->SetX(162.5);
		$this->Cell(42.5,7,"Fecha __________________________",0,0,'L');
		$this->Ln(5);
		$this->SetXY(162.5,194);
		$this->Cell(42.5,28,"",1,0,'C');
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

$pdf->Cell(75,5, utf8_decode($oCheque->nro_cuenta) ,0, 0, 'L','L');
$pdf->Cell(50,5, utf8_decode($oCheque->nro_cheque),0, 0, 'C','C');
$pdf->Cell(65,5, '***********'.utf8_decode(muestraFloat($monto)),0, 0, 'R','R');
$pdf->ln(5);
$pdf->Cell(190,5, utf8_decode($oCheque->banco),0, 0, 'L','C');
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
$pdf->ln(15);
$pdf->Cell(190,5, 'OP - '.$docs,0, 0, 'C','L');


$pdf->SetFont('Arial','',8);
$pdf->ln(10);
$pdf->Cell(200,5, 'CONCEPTO',1, 0, 'C','C');
$pdf->Ln(5);
$pdf->SetDrawColor (255,255,255);
$pdf->SetWidths(array(25,20,20,75,20,20,20));			
$pdf->SetAligns(array('C','C','C','L','C','C','C'));
$pdf->Row(array("Documento", "Soporte", "Fecha", "Descripción", "Monto", "Retenciones", "Monto"),11);
$pdf->Ln(5);
$pdf->SetAligns(array('C','C','C','L','R','R','R'));
$orden_c="	SELECT 
				B.nrodoc, 
				B.fecha,
				B.descripcion,
				A.monto,
				A.nrofactura,
				A.iva_retenido
			FROM
				finanzas.orden_pago AS B
				Inner Join finanzas.facturas AS A ON B.nrodoc = A.nrodoc
			WHERE
				B.nrodoc = '".rtrim($docs)."'";
//var_dump($orden_c);
$r_orden_c=$conn->Execute($orden_c);
$Total=0;
while(!$r_orden_c->EOF)
{
	$pdf->Row(array($r_orden_c->fields['nrofactura'], $oOrdenPago->nrorefcomp, muestrafecha($r_orden_c->fields['fecha']), $r_orden_c->fields['descripcion'],  muestraFloat($r_orden_c->fields['monto']), "", ""),11);
	$pdf->Row(array("","","", "I.V.A.", "", muestraFloat($r_orden_c->fields['iva_retenido']), ""),11);
	$Total+=$r_orden_c->fields['monto']-$r_orden_c->fields['iva_retenido'];
	$q_retencion="	SELECT
						B.mntret,
						A.descri,
						B.codret 
					FROM
							finanzas.retenciones_adiciones AS A
						Inner Join finanzas.relacion_retenciones_orden AS B ON B.codret = A.id
					WHERE
						B.nrofactura =  '".$r_orden_c->fields['nrofactura']."' AND
						B.nrodoc = TRIM('".$docs."')";
	//var_dump($q_retencion);	
	$r_retencion=$conn->Execute($q_retencion);
	//die(var_dump($r_retencion));
	while(!$r_retencion->EOF)
	{
		$pdf->Row(array("","", "", $r_retencion->fields['codret']==1?'IVA':$r_retencion->fields['codret']==2?'ISLR':'Retencion Municipal', "", muestraFloat($r_retencion->fields['mntret']), ""),11);
		$Total-=$r_retencion->fields['mntret'];
		$r_retencion->movenext();
		//die('entro');
	}
	$r_orden_c->movenext();
}
//$pdf->Line();
$pdf->SetFont('Arial','B',8);
$pdf->SetAligns(array('C','C','C','R','R','R','R'));
$pdf->Row(array("","", "", "Total a Pagar","" , "",  muestraFloat($Total)),T,11);

$pdf->SetDrawColor (0,0,0);
$pdf->SetXY(5, 74);
$pdf->Cell(200,5,"",1,0,'C');
$pdf->Ln(5);
$pdf->Cell(200,100,"",1,0,'C');

$pdf->Ln(110);
$pdf->Output();

?>
