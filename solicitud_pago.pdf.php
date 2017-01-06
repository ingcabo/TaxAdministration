<?
include("comun/ini.php");
include("Constantes.php");
$oSolicitud = new solicitud_pago();
$oSolicitud->get($conn, $_GET['id']);
if(empty($oSolicitud->nrodoc))
	header ("location: solicitud_pago.php");
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
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oOrden = unserialize($_SESSION['pdf']);
			//$tipo = $oSolicitud->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oSolicitud->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oSolicitud->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
				$tipoOrden = "Solicitud de Pago";
			//elseif($tipo == '009')
				//$tipoOrden = "Orden de Trabajo";
			$this->Text(80, 40, $tipoOrden);
			$this->Text(145, 40, "Nro.:".$_GET['id']."\n");
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

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);

$oProveedor = new proveedores;
$oProveedor->get($conn, $oSolicitud->id_proveedor);

#BENEFICIARIO DE LA SOLICITUD#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(28,4, 'BENEFICIARIO:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(67,4, utf8_decode($oProveedor->nombre),0, '','L' );

#RIF O CEDULA DEL BENEFICIARIO DE LA SOLICITUD#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(28,4, 'R.I.F o CEDULA:',0, '','L' );
$pdf->SetFont('Courier','',8);
if($oProveedor->provee_contrib_munic == 'N')
	$pdf->Cell(67,4, utf8_decode($oProveedor->rif_letra."-".$oProveedor->rif_numero),0, '','L' );
else
	$pdf->Cell(67,4, utf8_decode($oProveedor->rif),0, '','L' );

#DESCRIPCION DE LA SOLICITUD DE PAGO#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(28,4, 'MOTIVO:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4,utf8_decode($oSolicitud->motivo),0, '','L');

/*#UNIDAD EJECUTORA DE LA SOLICITUD#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Unidad Ejecutora:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4,utf8_decode($oSolicitud->unidad_ejecutora),0, '','L');*/


/*#TIPO DE CONTRIBUYENTE DE LA SOLICITUD#
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Tipo de Contribuyente:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oSolicitud->tipo_contribuyente,0, '','L' );*/

/*#INGRESO PERIODO FISCAL#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(42,4, 'Ingreso Periodo Fiscal:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(50,4, muestrafloat($oSolicitud->ingreso_periodo_fiscal),0, '','L' );*/

/*#STATUS DE LA SOLICITUD#
if ($oSolicitud->status == 1){
	$status = "Registrada";
}elseif ($oSolicitud->status == 2){
	$status = "Aprobada";
}else{
	$status = "Anulada";
}

$pdf->SetFont('Courier','B',8);
$pdf->Cell(15,4, 'Status:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $status,0, '','L' );*/

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

#TIPO DE DOCUMENTO#
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(33,4, 'Tipo de Documento:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(59,4, $oSolicitud->tipdoc->descripcion,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'NUMERO:',0, '','r' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(59,4, $oSolicitud->nroref,0, '','L' );
$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');


/*#CONDICION DE PAGO#
$cp = new condicion_pago;
$cp->get($conn,$oSolicitud->id_condicion_pago);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(33,4, 'Condición de Pago:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $cp->descripcion,0, '','L' );*/

/*#TIPO DE SOLICITUD#
$ts = new tipos_solicitudes;
$ts->get($conn,$oSolicitud->id_tipo_solicitud);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(33,4, 'Tipo de Solicitud:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4,utf8_decode($ts->descripcion),0, '','L' );

$pdf->Ln();

/*#TIPO DE SOLICITUD SIN IMPUTACION#
$tssi = new tipos_solicitud_sin_imp;
$tssi->get($conn,$oSolicitud->id_tipo_solicitud_si);
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Tipo de Solicitud S/I:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $tssi->descripcion,0, '','L' );
$pdf->Ln();
$pdf->Ln();*/

#RETENCIONES#
/*$pdf->Ln(3);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(33,4, 'Retenciones:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$cRelaciones = $oSolicitud->getRelacionPartidas($conn,$_REQUEST['id'], $escEnEje);

//die(print_r($cRelaciones));

/*$pdf->Ln(2);

$pdf->SetAligns(array('C','C','C','C'));
$pdf->SetWidths(array(45,45,45,45));
$pdf->SetFont('Courier','B',7);
$pdf->RowNL(array('Descripción','(%) Porcentaje','Monto Base','Monto Ret/Adic'));*/

/*$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);*/

/*$pdf->SetWidths(array(45,45,45,45));
$pdf->SetAligns(array('C','C','C','C'));*/
/*$cretenciones = $oSolicitud->getretenciones($conn, $_REQUEST['id']);
$JsonRet = new Services_JSON();
$JsonRet = $JsonRet->decode(str_replace("\\","",$oSolicitud->relacionRetenciones));
$pdf->Ln();
//die(var_dump($JsonRet));

if($JsonRet[0]->montobase > 0){
	$pdf->SetWidths(array(80,45,15,25));
	$pdf->SetAligns(array('R','C','R','R'));
	
	$pdf->SetFont('Courier','B',7);
	$pdf->Cell(120,4, 'Base Imponible:',0, '','R');
	$pdf->Cell(45,4, muestrafloat($JsonRet[0]->montobase ),0, '','R');
	$pdf->Ln();
	$pdf->Ln();
	$totalret = 0;
	foreach($JsonRet as $retenciones){
		$pdf->Ln();
		$ra = new retenciones_adiciones;
		$ra->get($conn,$retenciones->codigoretencion);
		$totalret += $retenciones->montoretencion;
		$pdf->RowNL(array('RETENCION',$ra->descripcion,
												"-".$retenciones->porcentaje."%",
												muestrafloat($retenciones->montoretencion)));
	}
	$totalad = 0;
	foreach($cRelaciones as $relPartidas){
		if($relPartidas->idParCat == $idpc_iva){
			$totalad+= $relPartidas->monto;
			$pdf->SetWidths(array(80,45,40));
			$pdf->SetAligns(array('R','C','R'));
			$pdf->RowNL(array('ADICIONES','IVA',
												muestrafloat($relPartidas->monto),
												));
			$pdf->RowNL(array('','','-------------'));
			
		}
	}
	$pdf->Ln(2);
	$a_pagar= $JsonRet[0]->montobase - $totalret + $totalad;
	$pdf->Cell(125,4, 'TOTAL: ',0, '','R');
	$pdf->Cell(40,4, muestrafloat($a_pagar),0, '','R');
	$pdf->Ln(2);
	
	
}
$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');

/*$pdf->Ln();
$pdf->Cell(151,4, 'TOTAL RET / ADIC: ',0, '','R');
$pdf->Cell(25,4, muestrafloat($totalret),0, '','C');
$pdf->Ln();*/

$pdf->Ln(2);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, utf8_decode('IMPUTACION PRESUPUESTARIA:'),0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);

$pdf->SetAligns('C');
$pdf->SetWidths(array(70,70,60));
$pdf->SetFont('Courier','B',8);
$pdf->RowNL(array('Categoria','Partida Presupuestaria','Monto'));

$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetWidths(array(70,70,60));
$pdf->SetAligns('C');
$cRelaciones = $oSolicitud->getRelacionPartidas($conn,$_REQUEST['id'], $escEnEje);
//die(print_r($cRelaciones));
$pdf->SetFont('Courier','B',7);
 $montoReferencia = 0;
$total_pagar = 0;
foreach($cRelaciones as $relaciones){
	 	$comprometido = $relaciones->monto + $comprometido;
		 $montoReferencia += movimientos_presupuestarios::get_monto($conn,	
		 															$relaciones->nroref, 
																	$relaciones->id_categoria_programatica,
																	$relaciones->id_partida_presupuestaria);
			
		$causado = $montoReferencia;
		$total_pagar+= $relaciones->monto;
		$pdf->RowNL(array($relaciones->id_categoria_programatica." - ".$relaciones->id_partida_presupuestaria,
											$relaciones->partida_presupuestaria,
											muestraFloat($relaciones->monto)
											));
}

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln();
$pdf->Cell(60,4, '',0, '','C');
$pdf->Cell(40,4, '',0, '','C');
$pdf->Cell(30,4, 'Total: ',0, '','R');
$pdf->Cell(30,4, muestraFloat($total_pagar),0, '','R');
$pdf->Ln();

if($anoCurso == 2007){
	$pdf->Cell(60,4, '',0, '','C');
	$pdf->Cell(40,4, '',0, '','C');
	$pdf->Cell(30,4, 'Total Bs.F.: ',0, '','R');
	$pdf->Cell(30,4, muestraFloat($total_pagar/1000),0, '','R');
	$pdf->Ln();
}

/*
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(120,4, 'Monto de la solicitud sólo si ésta no tiene Imputación Presupuestaria:',0, '','R');

$pdf->SetFont('Courier','',8);
$pdf->Cell(200,4, ($oSolicitud->monto_si =="")? muestraFloat(0): muestraFloat($oSolicitud->monto_si) ,0, '','L' );
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4, 'Compromiso:',0, '','R');

$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, muestraFloat($comprometido) ,0, '','L' );

$pdf->SetFont('Courier','B',8);
$pdf->Cell(10,4, 'Causado:',0, '','R');

$pdf->SetFont('Courier','',8);
$pdf->Cell(45,4, muestraFloat($causado) ,0, '','L' );

$disponible = $comprometido - $causado;

$pdf->SetFont('Courier','B',8);
$pdf->Cell(10,4, 'Disponible:',0, '','R');

$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, muestraFloat($disponible) ,0, '','L' );
$pdf->Ln();
$pdf->Ln();

#FACTURAS#

$pdf->SetFont('Courier','B',12);
$pdf->Cell(25,4, 'Facturas:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);

$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
$pdf->SetWidths(array(17,20,18,8,22,22,19,25,25));
$pdf->SetFont('Courier','B',7);
$pdf->RowNL(array('Nº Fact.','Nº Control.','Fecha','Iva.','Monto Doc.','Base Imp.','Monto Exc.','Monto Iva', 'Iva Retenido'));

$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetWidths(array(17,20,18,8,22,22,19,25,25));
$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
$JsonFac = new Services_JSON();
$JsonFac = $JsonFac->decode(str_replace("\\","",$oSolicitud->relacionFacturas));



$pdf->SetFont('Courier','B',7);
foreach($JsonFac as $facturas){
	$total += $facturas->montofac;
	$pdf->RowNL(array($facturas->nrofac,
											$facturas->nrocontrol,
											muestrafecha($facturas->fechafac),
											$facturas->iva,
											muestrafloat($facturas->montofac),
											muestrafloat($facturas->base_imponible),
											muestrafloat($facturas->monto_excento),
											muestrafloat($facturas->monto_iva),
											muestrafloat($facturas->iva_retenido)));
}
$pdf->Ln(1);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(151,4, 'TOTAL FACTURA: ',0, '','R');
$pdf->Cell(25,4, muestrafloat($total),0, '','C');
$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');*/

$pdf->Ln(2);



$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln(5);
$pdf->Ln(2);
$pdf->Cell(87,15, '___________________',0, '','C');
$pdf->Cell(88,15, '___________________',0, '','C');
$pdf->Ln();

$pdf->Cell(87,4, 'DIRECTOR DE',0, '','C');
$pdf->Cell(88,4, 'JEFE DE',0,'','C');

$pdf->Ln();

$pdf->Cell(87,4, 'ADMINISTRACION',0, '','C');
$pdf->Cell(88,4, 'PRESUPUESTO',0,'','C');




$pdf->Output();
?>
