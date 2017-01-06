<?php
	include("comun/ini.php");
	$id=$_REQUEST['id'];//die($nro_recibo);
	$oOrdenPago = new orden_pago();
	$oOrdenPago->get($conn, $id, $escEnEje);
	//var_dump($oOrdenPago);
class PDF extends FPDF
{
	function Header()
	{
		$this->SetLeftMargin(5);
		$this->SetFont('Courier','',10);
		$this->Ln(1);
		$this->Image ("images/logoa.jpg",5,4,50,20);//logo a la izquierda 
		$this->Ln(12);
		$this->SetXY(55, 7); 
		$textoCabecera = "SERVICIO AUTONOMO MUNICIPAL DE ADMINISTRACION TRIBUTARIA\n\n";
		$textoCabecera.= "G-20002908-0\n\n";
		$textoCabecera.= "A.V. 3F CON CALLE 81 EDIF. C.P.U.\n\n";
		$this->MultiCell(150,2, $textoCabecera, 0, 'L');
		$this->SetXY(310, 7);
		$this->Cell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
		$this->Ln(20);
		$this->SetFont('Courier','',14);
		$this->Cell(0, 0, "COMPROBANTE DE RETENCION DE IMPUESTO MUNICIPAL",0,0,'C');
		$this->Ln(5);
	}

	function Footer()
	{	
		$this->SetFont('Courier','',12);
		$this->Cell(70,5,"Tesorero",1,0,'C');
		$this->SetX(278);
		$this->Cell(70,5,"Contribuyente",1,0,'C');
		$this->Ln(5);				
		$this->Cell(70,28,"",1,0,'C');
		$this->SetX(278);
		$this->Cell(70,28,"",1,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF('l','mm','legal');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(18);

//$oespectaculo = new espectaculo;
//$oespectaculo_id_contribuyente = $oespectaculo->id_contribuyente;die($oespectaculo_id_contribuyente);
//$oespectaculo->get($conn, $oespectaculo->id_contribuyente);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $oespectaculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
//$pdf->Ln();//$pdf->Ln();
//$pdf->Ln(5);
$sql = "SELECT nrocorret FROM finanzas.relacion_retenciones_orden A ";
$sql.= "INNER JOIN finanzas.retenciones_adiciones B ON (A.codret = B.id) ";
$sql.= "WHERE A.nrodoc = '$id' AND B.es_iva = '3'";
//die($sql);
$row = $conn->Execute($sql);
$pdf->SetFont('Courier','B',14);
$pdf->Cell(0,0,'Nro.'.$row->fields['nrocorret'],0,0,'C');
$pdf->Ln(10);
$pdf->SetFont('Courier','',12);
$pdf->Cell(250,5,'Nombre o Razon Social',0,0,'L');
$pdf->Cell(80,5,'RIF',0,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Courier','',12);
$pdf->Cell(250,5,	$oOrdenPago->proveedor,0,0,'L');
$pdf->Cell(80,5,$oOrdenPago->rif_proveedor,0,0,'L');
$pdf->Ln(5);
$pdf->Cell(330,5,'Direccion',0,0,'L');
$pdf->Ln(5);
$pdf->Cell(330,5,$oOrdenPago->dir_proveedor,0,0,'L');
$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
//$pdf->SetFillColor  (250,250,250);
$pdf->SetDrawColor (255,255,255);
$pdf->SetWidths(array(35,35,35,35,35,35,35,35,50));			
$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
$pdf->Row(array("Factura","Control","Fecha Fact.","Facturado","Exento","Base Imponible","Impuestos","% de Retencion","Impuesto Retenido"),11);

$pdf->Ln();
$q = "SELECT f.*, fa.nrocontrol, fa.fecha, fa.monto_excento, fa.monto AS mfactura FROM finanzas.relacion_retenciones_orden f ";
$q.= "INNER JOIN finanzas.retenciones_adiciones ra ON (f.codret = ra.id) ";
$q.= "INNER JOIN finanzas.facturas fa ON (f.nrofactura = fa.nrofactura AND f.nrodoc = fa.nrodoc) ";
$q.= "WHERE f.nrodoc = '$id' AND ra.es_iva = '3' ";
//die($q);
$r = $conn->Execute($q); //or die($q);

while(!$r->EOF)
{
	$pdf->SetFont('Courier','',10);
	$pdf->Row(array($r->fields['nrofactura'],$r->fields['nrocontrol'],muestraFecha($r->fields['fecha']),muestraFloat($r->fields['mfactura']),muestraFloat($r->fields['monto_excento']),muestraFloat($r->fields['mntbas']),muestraFloat($r->fields['monto_iva']),muestraFloat($r->fields['porcen']),muestraFloat($r->fields['mntret'])),11);
	$r->movenext();
}

$pdf->SetDrawColor (0,0,0);
$pdf->SetXY(18,67);
$pdf->Cell(330,5,"",1,0,'C');
$pdf->Ln(5);
$pdf->Cell(330,90,"",1,0,'C');


$pdf->Ln(95);
$pdf->Output();
?>