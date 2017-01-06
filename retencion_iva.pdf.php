<?
	include("comun/ini.php");
	include("Constantes.php");
	$id=$_REQUEST['id'];//die($nro_recibo);
	$oOrdenPago = new orden_pago();
	$oOrdenPago->get($conn, $id, $escEnEje);

class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(18);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$textoCabecera.= $id_espectaculo;
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(300, 20); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "COMPROBANTE DE RETENCION DE IVA";
			
			$this->Text(130, 40, $tipoOrden);
			$this->Line(18, 41, 340, 41);
			$this->Ln(20);
			//$this->Text(160, 40, '#');
			//$this->Text(175, 40, $id);
	}

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


$pdf->SetFont('Courier','',8);
$pdf->Cell(150,6,'',0,'','C');
$pdf->Cell(60,6,'Nº DE COMPROBANTE','LRT','','C');
$pdf->Cell(55,6,'',0,'','C');
$pdf->Cell(60,6,'FECHA','LRT','','C');
$pdf->Ln();
$pdf->Cell(150,6,'',0,'','C');
$sql = "SELECT nrocorret FROM finanzas.facturas A ";
$sql.= "INNER JOIN finanzas.retenciones_adiciones B ON (A.id_retencion = B.id) ";
$sql.= "WHERE A.nrodoc = '$id' AND B.es_iva = '1'";
//die($sql);
$row = $conn->Execute($sql);
$pdf->Cell(60,6,$row->fields['nrocorret'],'LRBT','','C');
$pdf->Cell(55,6,'',0,'','C');
$pdf->Cell(60,6,muestrafecha($oOrdenPago->fecha),'LRBT','','C');
$pdf->Ln();

$pdf->Cell(40,6,'',0,'','C');
$pdf->Cell(90,6,'COMPROBANTE DE RETENCION DE I.V.A.','LRBT','','C');
$pdf->Ln();

$pdf->Cell(100,6, 'NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCION:','LRTB', '','C' );
$pdf->Cell(30,6,'',0,'','C');
$pdf->Cell(90,6,'R.I.F. DEL CONTRIBUYENTE','LRBT','','C');
$pdf->Cell(45,6,'',0,'','C');
$pdf->Cell(60,6,'PERIODO FISCAL','LRBT','','C');
$pdf->Ln();

$auxfec = explode('-',$oOrdenPago->fecha);
$ano = $auxfec[0];
$mes = $auxfec[1];
$pdf->Cell(100,6, 'ALCALDIA DEL MUNICIPIO LIBERTADOR','LRTB', '','C' );
$pdf->Cell(30,6,'',0,'','C');
$pdf->Cell(90,6,'G-20000437-1','LRBT','','C');
$pdf->Cell(45,6,'',0,'','C');
$pdf->Cell(60,6,'AÑO '.$ano.' / MES '.$mes,'LRBT','','C');
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(120,6, 'DIRECCION FISCAL DEL AGENTE DE RETENCION','LRTB', '','C' );
$pdf->Ln();
$pdf->Cell(120,6, 'CALLE SUCRE FRENTE A LA PLAZA LA VICTORIA','LRTB', '','C' );
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(100,6,'NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO','LRBT','','C');
$pdf->Cell(30,6,'',0,'','C');
$pdf->Cell(90,6,'R.I.F. DEL SUJETO RETENIDO','LRBT','','C');
$pdf->Ln();

$pdf->Cell(100,6, $oOrdenPago->proveedor,'LRB', '','C');
$pdf->Cell(30,6,'',0,'','C');
$pdf->Cell(90,6, $oOrdenPago->rif_proveedor,'LRB', '','C');

$pdf->Ln();
$pdf->Cell(220,6,'',0,'','C');
$pdf->Cell(80,6,'COMPRAS INTERNAS O IMPORTACIONES','LRT','','C');

$pdf->Ln(6);
$pdf->Cell(320,0.1, '',1, '','C');

$pdf->Ln();

			$q = "SELECT f.* FROM finanzas.facturas f ";
			$q.= "INNER JOIN finanzas.retenciones_adiciones ra ON (f.id_retencion = ra.id) ";
			$q.= "WHERE f.nrodoc = '$id' AND ra.es_iva = '1' ";
			//die($q);
			$r = $conn->Execute($q); //or die($q);
	$pdf->SetFont('Courier','',7);
	$cfact = 1;
	$pdf->Cell(10,5,'','LRT','','C');
	$pdf->Cell(18,5,'FECHA DE','LRT','','C');
	$pdf->Cell(18,5,'NUMERO DE','LRT','','C');
	$pdf->Cell(20,5,'NUMERO DE','LRT','','C');
	$pdf->Cell(20,5,'NUMERO NOTA','LRT','','C');
	$pdf->Cell(20,5,'NUMERO NOTA','LRT','','C');
	$pdf->Cell(19,5,'TIPO DE','LRT','','C');
	$pdf->Cell(21,5,utf8_decode('Nº FACTURA'),'LRT','','C');
	$pdf->Cell(35,5,'TOTAL COMPRAS','LRT','','C');
	$pdf->Cell(39,5,'COMPRAS SIN','LRT','','C');
	$pdf->Cell(33,5,'BASE','LRT','','C');
	$pdf->Cell(20,5,'%','LRT','','C');
	$pdf->Cell(27,5,'IMPUESTO','LRT','','C');
	$pdf->Cell(25,5,'IVA','LRT','','C');
	$pdf->Ln();
	$pdf->Cell(10,5,'','LRB','','C');
	$pdf->Cell(18,5,'FACTURA','LRB','','C');
	$pdf->Cell(18,5,'FACTURA','LRB','','C');
	$pdf->Cell(20,5,'CONTROL','LRB','','C');
	$pdf->Cell(20,5,'DE DEBITO','LRB','','C');
	$pdf->Cell(20,5,'DE CREDITO','LRB','','C');
	$pdf->Cell(19,5,'TRANSACCION','LRB','','C');
	$pdf->Cell(21,5,utf8_decode('AFECTADA'),'LRB','','C');
	$pdf->Cell(35,5,'INCLUYENO EL IVA','LRB','','C');
	$pdf->Cell(39,5,'DERECHO A CREDITO IVA','LRB','','C');
	$pdf->Cell(33,5,'IMPONIBLE','LRB','','C');
	$pdf->Cell(20,5,'ALICUOTA','LRB','','C');
	$pdf->Cell(27,5,'IVA','LRB','','C');
	$pdf->Cell(25,5,'RETENIDO','LRB','','C');
	$pdf->Ln();
	
	while(!$r->EOF){
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(10,6,$cfact,1,'LRB','C');
		$pdf->Cell(18,6,muestraFecha($r->fields['fecha']),1,'LRB','C');
		$pdf->Cell(18,6,$r->fields['nrofactura'],1,'LRB','C');
		$pdf->Cell(20,6,$r->fields['nrocontrol'],1,'LRB','C');
		$pdf->Cell(20,6,'',1,'LRB','C');
		$pdf->Cell(20,6,'',1,'LRB','C');
		$pdf->Cell(19,6,'01-REG',1,'LRB','C');
		$pdf->Cell(21,6,'','LRB','','C');
		$pdf->Cell(35,6,muestraFloat($r->fields['monto']),1,'LRB','C');
		$pdf->Cell(39,6,muestraFloat($r->fields['monto_excento']),1,'LRB','C');
		$pdf->Cell(33,6,muestraFloat($r->fields['base_imponible']),1,'LRB','C');
		$pdf->Cell(20,6,muestraFloat($r->fields['iva']),1,'LRB','C');
		$pdf->Cell(27,6,muestraFloat($r->fields['monto_iva']),1,'LRB','C');
		$pdf->Cell(25,6,muestraFloat($r->fields['iva_retenido']),1,'LRB','C');
		$total_fact+= $r->fields['monto'];
		$total_bi+= $r->fields['base_imponible'];
		$total_iva+= $r->fields['monto_iva'];
		$total_ret+= $r->fields['iva_retenido'];
		$cfact++;
		$pdf->Ln();
		$r->movenext();
	}
	
	$pdf->Ln();
	$pdf->Cell(150,4, '',0,'','C');
	$pdf->Cell(35,4, muestraFloat($total_fact),0,'','R');
	$pdf->Cell(35,4, '',0,'','C');
	$pdf->Cell(30,4, muestraFloat($total_bi),0,'','R');
	$pdf->Cell(25,4, '',0,'','C');
	$pdf->Cell(25,4, muestraFloat($total_iva),0,'','R');
	$pdf->Cell(25,4, muestraFloat($total_ret),0,'','R');

	


$pdf->Output();
?>