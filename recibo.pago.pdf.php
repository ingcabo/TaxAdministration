<?
	include("comun/ini.php");
	
	
	$nro_recibo=$_GET['nro_recibo'];

	$r = $conn->Execute("SELECT nro_declaracion FROM vehiculo.det_pago WHERE num_pago='$nro_recibo' LIMIT 1");
	
	$veh = $r->fields['nro_declaracion'];


	$ovehiculo = new vehiculo;

/*
	$ultimo_pago=$_GET['ultimo_pago'];

	
	$ultimo_pago.='-12-31';
	$tipo_vehiculo=$_GET['tipo'];
	
	if($_GET['primera']=='false'){$primera_vez=false;} else {$primera_vez=true;}
	
	

//	$veh = $_GET['veh'];

*/

	$ovehiculo->get($conn, $veh);

/*
//if(empty($oOrden->nrodoc))
//	header ("location: orden_servicio_trabajo.php");

//$_SESSION['pdf'] = serialize($ovehiculo);
//$_SESSION['pdf'] = serialize($oliquidacion);

*/

class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = "REPUBLICA BOLIVARIANA DE VENEZUELA\n";
			$textoCabecera.= "VALENCIA, ESTADO CARABOBO\n";
			$textoCabecera.= "ALCALDIA DEL MUNICIPIO LIBERTADOR\n";
			$textoCabecera.= "DIRECCION DE HACIENDA";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "RECIBO DE PAGO";
			$this->Text(85, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
			$this->Ln(20);
			
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
$ocontribuyente->get($conn, $ovehiculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
//$pdf->Ln();//$pdf->Ln();
//$pdf->Ln(5);


$pdf->SetFont('Courier','B',10);
$pdf->Cell(52,4, 'Datos del Contribuyente:',0, '','C' );
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(6);

$pdf->SetFont('Courier','',8);
$pdf->Cell(13,4, 'Sr(a):',0, '','C' );
$pdf->Cell(47,4, $ocontribuyente->primer_apellido.", ".$ocontribuyente->primer_nombre,0, '','C');

$pdf->Ln();
$pdf->Cell(16,4, 'C.I/RIF:',0, '','C' );
$pdf->Cell(25,4, $ocontribuyente->identificacion,0, '','C');

$pdf->Ln();
$pdf->Cell(20,4, 'Direccion:',0, '','C');
$pdf->Cell(19,4, $ocontribuyente->direccion,0, '','C');

$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');

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

$ovehiculo = new vehiculo;

$ovehiculo->get($conn, $veh);

$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(42,4, 'Datos del Vehiculo:',0, '','C' );
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(6);

$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, 'Placa:',0, '','C' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->placa,0, '','C' ); 


$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, 'Marca:',0, '','C' );


$r = $conn->Execute("SELECT descripcion FROM vehiculo.marca WHERE cod_mar='$ovehiculo->cod_mar'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(10,4, $r->fields['descripcion'],0, '','C' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(80,4, 'Serial Carroceria:',0, '','C' );
$pdf->Cell(2,4, $ovehiculo->serial_carroceria,0, '','C' );

$pdf->Ln(6);
$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(12,4, $anio_head.':',0, '','C' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->anio_veh,0, '','C' ); 


$pdf->SetFont('Courier','',8);
$pdf->Cell(48,4, 'Modelo:',0, '','C' );

$r = $conn->Execute("SELECT descripcion FROM vehiculo.modelo WHERE cod_mod='$ovehiculo->cod_mod'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(8,4, $r->fields['descripcion'],0, '','C' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(66,4, 'Serial Motor:',0, '','C' ); 
$pdf->Cell(25,4, $ovehiculo->serial_motor,0, '','C' );


$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, 'Color:',0, '','C' );


$r = $conn->Execute("SELECT descripcion FROM vehiculo.colores WHERE cod_col='$ovehiculo->cod_col'");
$pdf->SetFont('Courier','',8);
$pdf->Cell(30,4, $r->fields['descripcion'],0, '','C' );

$pdf->Ln(7.5);
$pdf->Cell(175,0.1, '',1, '','C');
/*$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(15,4, $anio_head.':',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->anio_veh,0, '','L' ); */

$pdf->Ln(8);
 

/*$pdf->Ln();
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, 'Serial de Carroceria:',0, '','L' );
$pdf->Cell(20,4, $ovehiculo->serial_carroceria,0, '','L' );*/


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

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Detalle del Pago:',0, '','L' );
$pdf->Ln(7);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();
$pdf->Ln();


$pdf->SetFont('Courier','B',8);
$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(15,4, $anio_head,0, '','L');

$desc_head = mb_convert_encoding("Descripción", "ISO-8859-1", "UTF-8");
$pdf->Cell(100,4, $desc_head,0, '','L');
$pdf->Cell(34,4, 'Monto',0, '','R');
//$pdf->Cell(20,4, 'Recargo',0, '','L');
//$pdf->Cell(30,4, 'Total Anual',0, '','L');

//$pdf->Cell(20,4, 'IVA',0, '','C');
//$pdf->Cell(20,4, 'Precio',0, '','C');
//$pdf->Cell(20,4, 'Total',0, '','C');

$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);

$oliquidacion = new liquidacion;

//$cimpuesto = $oliquidacion->Cuantifica($conn, $ultimo_pago,$tipo_vehiculo,$primera_vez,$ovehiculo->id_contribuyente,$ovehiculo->id);

$pdf->SetFont('Courier','',8);


$r=$conn->Execute("SELECT a.anio,a.monto_pago,b.descripcion,a.id_base_calculo,b.id AS id_tipo_transaccion
		   FROM vehiculo.det_pago a, vehiculo.tipo_transaccion b 
   		   WHERE (num_pago='$nro_recibo' AND a.id_tipo_transaccion=b.id)
		   ORDER BY anio DESC");

while(!$r->EOF){	

//if(is_array($cimpuesto)){

//foreach($cimpuesto as $impuesto){

$descripcion_latin1 = mb_convert_encoding($r->fields['descripcion'], "ISO-8859-1", "UTF-8");

$base = $r->fields['id_base_calculo'];



if ($r->fields['id_tipo_transaccion']=='3'){
	$h=$conn->Execute("SELECT * FROM vehiculo.base_calculo_veh WHERE id='$base'");
	$otra_descripcion = $h->fields['art_ref'].' '.$h->fields['desc_tipo'].' a partir de '.muestrafecha($h->fields['vig_desde']);
} else {
	$otra_descripcion = '';
}

//if ($r->fields['id_tipo_transaccion']=='6'){

//	$monto_calcomania = $r->fields['monto_pago'];
//}

//if ($r->fields['id_tipo_transaccion']=='5'){

//	$monto_inscripcion = $r->fields['monto_pago'];
//}


	$pdf->Ln();
	$pdf->Cell(15,4, $r->fields['anio'],0, '','L');
	$pdf->Cell(100,4, $descripcion_latin1.' '.$otra_descripcion,0, '','L');
	$pdf->Cell(40,4, muestrafloat($r->fields['monto_pago']).' Bs.',0, '','R');
//	$pdf->Cell(20,4, muestrafloat($impuesto->recargo).' Bs.',0, '','R');
//	$pdf->Cell(30,4, muestrafloat($impuesto->impuesto_mas_recargo).' Bs.',0, '','R');
//	$pdf->Cell(20,4, $producto->precio_iva,0, '','C');
//	$pdf->Cell(20,4, $producto->precio_total,0, '','C');
//	$totalFila = $producto->precio_iva + $producto->precio_total;
//	$pdf->Cell(20,4, $totalFila,0, '','C');
//	$precioTotal += $totalFila;

	$r->movenext();
}

//} else {   $pdf->Cell(15,4, 'NO TIENE DEUDAS',0, '','L');    }
$pdf->Ln();$pdf->Ln();
//$pdf->Cell(175,0.1, '',1, '','C');


$r=$conn->Execute("SELECT mto_tot_pago FROM vehiculo.imp_pago WHERE num_pago='$nro_recibo'");
$monto_total_pago = $r->fields['mto_tot_pago'];

$pdf->Ln(1);
$pdf->Cell(175,0.1, '',1, '','C');


//$pdf->Ln();
//$pdf->Cell(140,4, 'Tasa de inscripcion',0, '','R');
//$pdf->Cell(30,4, muestrafloat($monto_inscripcion).' Bs.',0, '','R');
//$pdf->Ln();
//$pdf->Cell(140,4, 'Calcomania',0, '','R');
//$pdf->Cell(30,4, muestrafloat($monto_calcomania).' Bs.',0, '','R');
//$pdf->Ln();
$pdf->Ln(1);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total',0, '','R');
$pdf->Cell(30,4, muestrafloat($monto_total_pago).' Bs.',0, '','R');
$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

//$pdf->Ln();
//$pdf->Ln();
//$pdf->Ln();
//$pdf->Ln();



//$pdf->SetFont('Courier','B',10);
//$pdf->Cell(25,4, 'Liquidador:_________________                            Cajero:__________________',0, '','L' );	

//$pdf->Cell(25,4, 'Liquidador:_________________');

$pdf->Ln(8);

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Forma de Pago:',0, '','L' );

$pdf->Ln();
$pdf->Ln();


$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(1.5);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4, 'Tipo',0, '','L');
$pdf->Cell(30,4, 'Banco',0, '','R');
$pdf->Cell(50,4, 'Nro Documento',0, '','R');
$pdf->Cell(50,4, 'Monto',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

$pdf->SetFont('Courier','',8);

$r=$conn->Execute("SELECT * from vehiculo.det_forma_pago WHERE num_pago='$nro_recibo'");

while(!$r->EOF){	

	$pdf->Ln(2);
	$tipo=$r->fields['tipo_pago'];
	$w=$conn->Execute("SELECT descripcion FROM vehiculo.forma_pago WHERE id='$tipo'");
	$pdf->Cell(20,4, $w->fields['descripcion'],0, '','L');
	
	$banco=$r->fields['cod_banco'];
	$w=$conn->Execute("SELECT descripcion FROM vehiculo.banco WHERE id='$banco'");
	$pdf->Cell(40,4, $w->fields['descripcion'],0, '','L');
	$pdf->Cell(30,4, $r->fields['nro_doc'],0, '','L');
	$pdf->Cell(65.5,4, muestrafloat($r->fields['mto_pago']).' Bs.',0, '','R');

	$r->movenext();
}


$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total',0, '','R');
$pdf->Cell(30.5,4, muestrafloat($monto_total_pago).' Bs.',0, '','R');

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Cajero:_________________');


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