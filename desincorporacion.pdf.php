<?
	include("comun/ini.php");
	include("Constantes.php");
	
	
	$odesincorporacion = new desincorporacion;

	$veh = $_REQUEST['nro_recibo'];

	$odesincorporacion->get($conn, $_REQUEST['nro_recibo']);

//if(empty($oOrden->nrodoc))
//	header ("location: orden_servicio_trabajo.php");

//$_SESSION['pdf'] = serialize($ovehiculo);
//$_SESSION['pdf'] = serialize($oliquidacion);

class PDF extends FPDF
{
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
			$textoCabecera.= DEPARTAMENTO."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$oOrden->nrodoc."\n";
			//$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "DESINCORPORACION DE VEHICULO";
			$this->Text(60, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);
			
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
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

$ocontribuyente = new contribuyente;
$ocontribuyente->get($conn, $odesincorporacion->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'DATOS DEL CONTRIBUYENTE:',0, '','L' );
$pdf->Ln();

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'Sr(a):',0, '','L' );
$pdf->Cell(100,4, $ocontribuyente->primer_apellido.", ".$ocontribuyente->primer_nombre,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'C.I/RIF:',0, '','L' );
$pdf->Cell(100,4, $ocontribuyente->identificacion,0, '','L');

$pdf->Ln();
$pdf->Cell(25,4, 'DIRECCION:',0, '','L');
$pdf->MultiCell(100,4, $ocontribuyente->direccion,0, '','L');

//$pdf->Ln();
//$pdf->Cell(175,0.2, '',1, '','C');

//$ovehiculo = new vehiculo;

//$ovehiculo->get($conn, $veh);

//$pdf->Cell(25,4, 'Placa:',0, '','L' );
//$pdf->Cell(100,4, $ovehiculo->placa."  Serial Motor: ".$ovehiculo->serial_motor." Serial Carroceria: ".$ovehiculo->serial_carroceria,0, '','L');

//$pdf->Ln();
//$pdf->Cell(25,4, 'Marca',0, '','L');
//$pdf->MultiCell(100,4, $ovehiculo->direccion,0, '','L');

//$pdf->Ln();
//$pdf->Cell(175,0.2, '',1, '','C');

$odesincorporacion = new desincorporacion;

$odesincorporacion->get($conn, $veh);

$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'DATOS DEL VEHICULO:',0, '','L' );
$pdf->Ln();

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'PLACA:',0, '','L' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $odesincorporacion->placa,0, '','L' ); $pdf->Ln();


$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'MARCA:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM vehiculo.marca WHERE cod_mar='$odesincorporacion->cod_mar'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $r->fields['descripcion'],0, '','L' );

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'MODELO:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM vehiculo.modelo WHERE cod_mod='$odesincorporacion->cod_mod'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $r->fields['descripcion'],0, '','L' );

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'COLOR:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM vehiculo.colores WHERE cod_col='$odesincorporacion->cod_col'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $r->fields['descripcion'],0, '','L' );

$pdf->Ln();
$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(25,4, $anio_head.':',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $odesincorporacion->anio_veh,0, '','L' ); 


$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'MOTIVO DESINCORPORACION:',0, '','L' );

$r = $conn->Execute("SELECT descripcion FROM vehiculo.mot_desincorporacion WHERE cod_des='$odesincorporacion->cod_desincorporacion'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $r->fields['descripcion'],0, '','R' );

$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'MOTIVO DESINCORPORACION:',0, '','L' );
//echo $odesincorporacion->id
$r = $conn->Execute("SELECT fecha_desincorporacion FROM vehiculo.veh_desincorporacion WHERE cod_vehiculo='$odesincorporacion->id'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $r->fields['fecha_desincorporacion'],0, '','R' );


/*$pdf->SetFont('Courier','',8);
$pdf->Cell(31,4, 'Serial de Motor:',0, '','R' ); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->serial_motor,0, '','L' ); $pdf->Ln();

$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, 'Serial de Carroceria:',0, '','R' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->serial_carroceria,0, '','L' );/*


/*

if($oOrden->id_tipo_documento == '009'){
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	
	$pdf->Ln();
	$pdf->SetFont('Courier','B',10);
	$pdf->Cell(15,4, 'Referencias del Ciudadano:',0, '','L' );
	
	$pdf->Ln();
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(40,4, 'Cod. del Ciudadano:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, $oOrden->id_ciudadano,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'Nombre:',0, '','R');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(20,4, $oOrden->ciudadano,0, '','L');
	
	$pdf->Ln();
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(40,4, 'Teléfono:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(25,4, $oOrden->tlf_ciudadano,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'Dirección:',0, '','R');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(40,4, $oOrden->dir_ciudadano,0, '','L');
}
*/

/*
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(50,4, 'Condiciones de la Operacion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oOrden->condicion_operacion,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Fecha de Entrega:',0, '','R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, muestrafecha($oOrden->fecha_entrega),0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Lugar de Entrega:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, $oOrden->lugar_entrega,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Codigo de Contraloria:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oOrden->cod_contraloria,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Nro de Factura:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $oOrden->nro_factura,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Fecha de Factura:',0, '','R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, muestrafecha($oOrden->fecha_factura),0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Nro de Cotizacion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, $oOrden->nro_cotizacion,0, '','L');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Descripcion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(150,4, $oOrden->observaciones,0, '','L');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(50,4, 'Nro de Requisicion:',0, '','L');
$pdf->Cell(50,4, 'Unidades Solicitantes:',0, '','L');

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(50,4, $oOrden->nro_requisicion,0, '','L');
$pdf->Cell(100,4, $oOrden->id_unidad_ejecutora." - ".$oOrden->unidad_ejecutora,0, '','L');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
*/
/*
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Calculo del Impuesto:',0, '','L' );

$pdf->Ln();
$pdf->Ln();


$pdf->SetFont('Courier','B',8);
$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(15,4, $anio_head,0, '','L');
$pdf->Cell(90,4, 'Descripcion',0, '','L');
$pdf->Cell(25,4, 'Impuesto',0, '','L');
$pdf->Cell(25,4, 'Recargo',0, '','L');
$pdf->Cell(15,4, 'Total Anual',0, '','L');

//$pdf->Cell(20,4, 'IVA',0, '','C');
//$pdf->Cell(20,4, 'Precio',0, '','C');
//$pdf->Cell(20,4, 'Total',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

$oliquidacion = new liquidacion;

$cimpuesto = $oliquidacion->Cuantifica($conn, $ultimo_pago,$tipo_vehiculo,$primera_vez,$ovehiculo->id_contribuyente,$ovehiculo->id);

$pdf->SetFont('Courier','',8);



if(is_array($cimpuesto)){

foreach($cimpuesto as $impuesto){
	$pdf->Ln();
	$pdf->Cell(15,4, $impuesto->anio,0, '','L');
	$pdf->Cell(90,4, $impuesto->art_ref.' '.$impuesto->desc_tipo.' a partir de '.muestrafecha($impuesto->vig_desde),0, '','L');
	$pdf->Cell(20,4, muestrafloat($impuesto->impuesto).' Bs.',0, '','R');
	$pdf->Cell(22,4, muestrafloat($impuesto->recargo).' Bs.',0, '','R');
	$pdf->Cell(30,4, muestrafloat($impuesto->impuesto_mas_recargo).' Bs.',0, '','R');
	
//	$pdf->Cell(20,4, $producto->precio_iva,0, '','C');
//	$pdf->Cell(20,4, $producto->precio_total,0, '','C');
//	$totalFila = $producto->precio_iva + $producto->precio_total;
//	$pdf->Cell(20,4, $totalFila,0, '','C');
//	$precioTotal += $totalFila;

}

} else {   $pdf->Cell(15,4, 'NO TIENE DEUDAS',0, '','L');    }
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln();


$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Resumen del Impuesto Liquidado:',0, '','L');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(88,4, 'Monto Impuesto Vigente '.$anio_head.' Actual',0, '','R');
$pdf->Cell(58,4, muestrafloat($oliquidacion->miv).' Bs.',0, '','R');
$pdf->Ln();
$pdf->Cell(52,4, 'Deuda Morosa',0, '','R');
$pdf->Cell(94,4, muestrafloat($oliquidacion->dm).' Bs.',0, '','R');
$pdf->Ln();
$pdf->Cell(84.5,4, 'Monto de Recargos por Morosidad',0, '','R');
$pdf->Cell(61.5,4, muestrafloat($oliquidacion->rm).' Bs.',0, '','R');
$pdf->Ln();
/*$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $ovehiculo->anio_head,0, '','L' ); $pdf->Ln();*/
/*
$pdf->Cell(64,4, 'Tasa de inscripcion',0, '','R');
$pdf->Cell(82,4, muestrafloat($oliquidacion->tasa).' Bs.',0, '','R');
$pdf->Ln();
$pdf->Cell(49,4, 'Calcomania',0, '','R');
$pdf->Cell(97,4, muestrafloat($oliquidacion->calcomania).' Bs.',0, '','R');
$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(100,4, 'Total:',0, '','R');
$pdf->Cell(46,4, muestrafloat($oliquidacion->total).' Bs.',0, '','R');*/
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();




$pdf->SetFont('Courier','B',10);
//$pdf->Cell(25,4, 'Liquidador:_________________                            Cajero:__________________',0, '','L' );	

$pdf->Cell(25,4, 'Funcionario:_________________');

//$pdf->Ln();
//$pdf->Cell(175,0.2, '',1, '','C');

/*$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'IMPUTACION PRESUPUESTARIA',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(100,4, 'Descripcion',0, '','L');
$pdf->Cell(35,4, 'Monto',0, '','C');

$cPartidas = $oOrden->getRelacionPartidas($conn, $oOrden->id, $escEnEje);

foreach($cPartidas as $partida){
	$pdf->Ln();
	$pdf->Cell(40,4, $partida->id_categoria_programatica."-".$partida->id_partida_presupuestaria,0, '','C');
	$pdf->Cell(100,4, $partida->partida_presupuestaria,0, '','L');
	$pdf->Cell(35,4, $partida->monto,0, '','C');
	$montoTotal += $partida->monto;
}
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(35,4, $montoTotal,0, '','C');
$pdf->Ln();
*/

$pdf->Output();
?>
