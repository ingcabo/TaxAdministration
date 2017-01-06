<?
	include("comun/ini.php");
	include("Constantes.php");
	$id=$_REQUEST['id'];//die($nro_recibo);
$q = "SELECT publicidad.espectaculo.*, vehiculo.contribuyente.* FROM publicidad.espectaculo 
		INNER JOIN vehiculo.contribuyente ON (publicidad.espectaculo.id_contribuyente=vehiculo.contribuyente.id) 
		WHERE publicidad.espectaculo.id_espectaculo = '$id'"; //die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$id_contribuyente = $r->fields['id_contribuyente'];
			$identificacion = $r->fields['identificacion'];
			$rif = $r->fields['rif'];
			$patente = $r->fields['patente'];
			$fecha_registro = $r->fields['fecha_registro']; 
			$tipo_espectaculo = $r->fields['tipo_espectaculo'];
			$fec_ini = $r->fields['fecha_inicio'];
			$fec_fin = $r->fields['fecha_fin'];
			$lugar_evento = $r->fields['cod_lugar_evento'];
			$id_solicitud = $r->fields['id_solicitud'];     
			$nombre_contribuyente = $r->fields['primer_nombre']." ".$r->fields['segundo_nombre']." ".$r->fields['primer_apellido']." ".$r->fields['segundo_apellido'];
			$razon_contribuyente = $r->fields['razon_social'];
			$ciudad_contribuyente = $r->fields['ciudad_domicilio'];
			$telefono_contribuyente = $r->fields['telefono'];
			$domiciliotrib_contribuyente = $r->fields['domicilio_fiscal'];
			$cod_espectaculo = $r->fields['cod_espectaculo'];	
	}
$s="SELECT  publicidad.lugar_evento.descripcion FROM publicidad.espectaculo	INNER JOIN publicidad.lugar_evento  ON (publicidad.espectaculo.cod_lugar_evento=publicidad.lugar_evento.cod_lugar_evento) WHERE publicidad.espectaculo.id_espectaculo = '$id'";//die($s);
			$t = $conn->Execute($s);
					if(!$t->EOF){
			$lugar_evento_des = $t->fields['descripcion'];
}

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
			$textoCabecera.= $id_espectaculo;
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(150, 20); 
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
				$tipoOrden = "RECIBO DE PAGO";
			
			$this->Text(85, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
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
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

//$oespectaculo = new espectaculo;
//$oespectaculo_id_contribuyente = $oespectaculo->id_contribuyente;die($oespectaculo_id_contribuyente);
//$oespectaculo->get($conn, $oespectaculo->id_contribuyente);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $oespectaculo->id_contribuyente);
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
$pdf->Cell(52,4, $nombre_contribuyente,0, '','C');

$pdf->Cell(46,4, 'Patente Nro.:',0, '','C' );
$pdf->Cell(5,4, $patente,0, '','R');

$pdf->Ln();
$pdf->Cell(16,4, 'C.I/RIF:',0, '','C' );
$pdf->Cell(40.5,4, $identificacion ." / ".$rif,0, '','C');

$pdf->Cell(63.5,4, 'Razon Social:',0, '','C' );
$pdf->Cell(8,4, $razon_contribuyente,0, '','C');

$pdf->Ln();
$pdf->Cell(20,4, 'Direccion:',0, '','C');
$pdf->Cell(34,4, $ciudad_contribuyente ." - ". $domiciliotrib_contribuyente,0, '','C');

$pdf->Cell(62,4, 'Telefono:',0, '','C' );
$pdf->Cell(1,4, $telefono_contribuyente,0, '','C');



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

//$oespectaculo = new espectaculo;      
//$oespectaculo->get($conn, $id_espectaculo);

 
//$sql_tipo_solicitud = $conn->Execute("SELECT * FROM publicidad.tipo_orden WHERE id='$oespectaculo->id_solicitud' LIMIT 1");
//$tipo_solicitud = $sql_tipo_solicitud->fields['descripcion'];//die($tipo_solicitud);
$e="select publicidad.tipo_espectaculo.descripcion from publicidad.espectaculo inner join publicidad.tipo_espectaculo on (publicidad.espectaculo.tipo_espectaculo=publicidad.tipo_espectaculo.cod_espectaculo) where publicidad.espectaculo.id_espectaculo='$id'";//die($e);
$t = $conn->Execute($e);
		if(!$t->EOF){
			$espectaculo_desc = $t->fields['descripcion'];
}
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(48,4, 'Datos del Espectaculo:',0, '','C' );
$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(6);

$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, 'Tipo de Solicitud:',0, '','C' );

$pdf->SetFont('Courier','',8);
$pdf->Cell(50,4,'Espectaculo Publico',0, '','L' ); 

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'Espectaculo:',0, '','C' );

//$sql_tipo_espectaculo = $conn->Execute("SELECT * FROM publicidad.tipo_espectaculo WHERE cod_espectaculo='$oespectaculo->tipo_espectaculo' LIMIT 1");
//$tipo_espectaculo = $sql_tipo_espectaculo->fields['descripcion'];//die($tipo_solicitud);

$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, $espectaculo_desc,0, '','C' );

//$sql_lugar_evento = $conn->Execute("SELECT * FROM publicidad.lugar_evento WHERE cod_lugar_evento='$oespectaculo->lugar_evento' LIMIT 1");
//$lugar_evento = $sql_lugar_evento->fields['descripcion'];//die($tipo_solicitud);

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(27,4, 'Lugar Evento:',0, '','C' );
$pdf->Cell(30,4, $lugar_evento_des,0, '','C' );

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(28,4, 'Periodo Desde:',0, '','C' );
$pdf->Cell(20,4, muestrafecha($fec_inicio),0, '','C' );

$pdf->Ln(6);
$pdf->SetFont('Courier','',8);
$pdf->Cell(28,4, 'Periodo Hasta:',0, '','C' );
$pdf->Cell(20,4, muestrafecha($fec_fin),0, '','C' );



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

//$sql_entradas_1 = $conn->Execute("SELECT * FROM publicidad.entradas WHERE id='$oespectaculo->entradas_1' LIMIT 1");
//$entradas_1 = $sql_entradas_1->fields['descripcion'];//die($entradas_1);

//$sql_entradas_2 = $conn->Execute("SELECT * FROM publicidad.entradas WHERE id='$oespectaculo->entradas_2' LIMIT 1");
//$entradas_2 = $sql_entradas_2->fields['descripcion'];//die($entradas_2);

//$sql_entradas_3 = $conn->Execute("SELECT * FROM publicidad.entradas WHERE id='$oespectaculo->entradas_3' LIMIT 1");
//$entradas_3 = $sql_entradas_3->fields['descripcion'];//die($entradas_3);


$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, 'Tipo Entradas',0, '','C' );
$pdf->Cell(50,4, 'Cantidad Entradas',0, '','C' );
$pdf->Cell(20,4, 'Monto Entradas',0, '','C');
$pdf->Cell(42,4, 'UT',0, '','C');
$pdf->Cell(32,4, 'Total Impuesto Entradas',0, '','C');

//$pdf->Cell(20,4, 'Recargo',0, '','L');
//$pdf->Cell(30,4, 'Total Anual',0, '','L');

//$pdf->Cell(20,4, 'IVA',0, '','C');
//$pdf->Cell(20,4, 'Precio',0, '','C');
//$pdf->Cell(20,4, 'Total',0, '','C');

$pdf->Ln(5);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(4);



//$cimpuesto = $oliquidacion->Cuantifica($conn, $ultimo_pago,$tipo_vehiculo,$primera_vez,$ovehiculo->id_contribuyente,$ovehiculo->id);

$pdf->SetFont('Courier','',8);


/*$r=$conn->Execute("SELECT a.anio,a.monto_pago,b.descripcion,a.id_base_calculo,b.id AS id_tipo_transaccion
		   FROM vehiculo.det_pago a, vehiculo.tipo_transaccion b 
   		   WHERE (num_pago='$nro_recibo' AND a.id_tipo_transaccion=b.id)
		   ORDER BY anio DESC");*/

//if( (!$sql_entradas_1->EOF) || (!$sql_entradas_2->EOF) || (!$sql_entradas_3->EOF) ){	

//if(is_array($cimpuesto)){

//foreach($cimpuesto as $impuesto){

//if ($r->fields['id_tipo_transaccion']=='6'){

//	$monto_calcomania = $r->fields['monto_pago'];
//}

//if ($r->fields['id_tipo_transaccion']=='5'){

//	$monto_inscripcion = $r->fields['monto_pago'];
//}
$o="SELECT publicidad.calculo_espectaculo.*, publicidad.entradas.descripcion FROM publicidad.calculo_espectaculo inner join publicidad.entradas on (publicidad.calculo_espectaculo.id_entradas=publicidad.entradas.id) WHERE publicidad.calculo_espectaculo.id_espectaculo = '$id'";// die($o);
			$p = $conn->Execute($o);
$i=0;
while(!$p->EOF){

	$pdf->Ln();
	$pdf->Cell(27,4, $p->fields['descripcion'],0, '','C');
	$pdf->Cell(45,4, muestrafloat($p->fields['cant_entradas']),0, '','C');
	$pdf->Cell(30,4, muestrafloat($p->fields['costo_entrada']).' Bs.',0, '','C');
	$pdf->Cell(30,4, muestrafloat($p->fields['ut_espectaculo']).'',0, '','C');
	$pdf->Cell(48,4, muestrafloat((($p->fields['cant_entradas'])*($p->fields['costo_entrada']))*($p->fields['ut_espectaculo']/100)).'Bs.',0, '','L');
	$i++;
	$p->movenext();
				}
	/*$pdf->Ln();
	$pdf->Cell(27,4, $entradas_2,0, '','C');
	$pdf->Cell(45,4, $oespectaculo->cant_2,0, '','C');
	$pdf->Cell(30,4, muestrafloat($oespectaculo->aforo_1).' Bs.',0, '','C');
	$pdf->Cell(30,4, muestrafloat($oespectaculo->ut).' Bs.',0, '','C');
	$pdf->Cell(48,4, muestrafloat($oespectaculo->total_entradas_2).' Bs.',0, '','C');
	
	$pdf->Ln();
	$pdf->Cell(35.5,4, $entradas_3,0, '','C');
	$pdf->Cell(28,4, $oespectaculo->cant_3,0, '','C');
	$pdf->Cell(47,4, muestrafloat($oespectaculo->aforo_1).' Bs.',0, '','C');
	$pdf->Cell(13,4, muestrafloat($oespectaculo->ut).' Bs.',0, '','C');
	$pdf->Cell(65,4, muestrafloat($oespectaculo->total_entradas_3).' Bs.',0, '','C');*/
//	$pdf->Cell(20,4, muestrafloat($impuesto->recargo).' Bs.',0, '','R');
//	$pdf->Cell(30,4, muestrafloat($impuesto->impuesto_mas_recargo).' Bs.',0, '','R');
//	$pdf->Cell(20,4, $producto->precio_iva,0, '','C');
//	$pdf->Cell(20,4, $producto->precio_total,0, '','C');
//	$totalFila = $producto->precio_iva + $producto->precio_total;
//	$pdf->Cell(20,4, $totalFila,0, '','C');
//	$precioTotal += $totalFila;

//}

//} else {   $pdf->Cell(15,4, 'NO TIENE DEUDAS',0, '','L');    }
$pdf->Ln();$pdf->Ln();
//$pdf->Cell(175,0.1, '',1, '','C');

$pdf->Ln(1);
$pdf->Cell(175,0.1, '',1, '','C');
$u="select sum((publicidad.calculo_espectaculo.cant_entradas * publicidad.calculo_espectaculo.costo_entrada)*(publicidad.calculo_espectaculo.ut_espectaculo/100)) as total from publicidad.calculo_espectaculo where id_espectaculo='$id'";
$t = $conn->Execute($u);
					if(!$t->EOF){
			$tot_impuesto = $t->fields['total'];
}

//$pdf->Ln();
//$pdf->Cell(140,4, 'Tasa de inscripcion',0, '','R');
//$pdf->Cell(30,4, muestrafloat($monto_inscripcion).' Bs.',0, '','R');
//$pdf->Ln();
//$pdf->Cell(140,4, 'Calcomania',0, '','R');
//$pdf->Cell(30,4, muestrafloat($monto_calcomania).' Bs.',0, '','R');
//$pdf->Ln();
$pdf->Ln(1);//die(muestrafloat($oespectaculo->total_impuesto));
$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total:',0, '','R');
$pdf->Cell(43.5,4, muestrafloat($tot_impuesto).' Bs.',0, '','R');
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

/*$sql_tipo_pago = $conn->Execute("SELECT * FROM vehiculo.forma_pago WHERE id='$oespectaculo->tipo_pago' LIMIT 1");
$tipo_pago = $sql_tipo_pago->fields['descripcion'];//die($tipo_pago);

$sql_banco = $conn->Execute("SELECT * FROM vehiculo.banco WHERE id='$oespectaculo->banco' LIMIT 1");
$banco = $sql_banco->fields['descripcion'];//die($banco);
*/
$pdf->Ln(8);

$pdf->SetFont('Courier','B',10);
$pdf->Cell(15,4, 'Forma de Pago:',0, '','L' );

$pdf->Ln();
$pdf->Ln();


$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln(1.5);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4, 'Tipo',0, '','L');
$pdf->Cell(28,4, 'Banco',0, '','R');
$pdf->Cell(50,4, 'Nro Documento',0, '','R');
$pdf->Cell(50,4, 'Monto',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

$pdf->SetFont('Courier','',8);
$i="select * from publicidad.espectaculo where id_espectaculo='$id'"; //die($i);
$k = $conn->Execute($i);
if(!$k->EOF){
//$tipo_pago = $k->fields['pago'];
//$banco = $k->fields['banco'];
$nro_documento= $k->fields['nro_documento'];
$total_impuesto = $k->fields['monto'];
}
$n="select public.banco.descripcion from publicidad.espectaculo inner join public.banco on (publicidad.espectaculo.banco=public.banco.id) where publicidad.espectaculo.id_espectaculo='$id'";
$k = $conn->Execute($n);
if(!$k->EOF){
$banco = $k->fields['descripcion'];
}
$m="select vehiculo.forma_pago.descripcion from publicidad.espectaculo inner join vehiculo.forma_pago on (publicidad.espectaculo.pago=vehiculo.forma_pago.id) where publicidad.espectaculo.id_espectaculo='$id'";
$k = $conn->Execute($m);
if(!$k->EOF){
$tipo_pago = $k->fields['descripcion'];
}
//if ( (!$sql_tipo_pago->EOF) || (!$sql_banco->EOF) ){	

	$pdf->Ln(2);
	$pdf->Cell(15,4, $tipo_pago,0, '','C');
	$pdf->Cell(60,4, $banco,0, '','C');
	$pdf->Cell(22,4, $nro_documento,0, '','C');
	$pdf->Cell(96,4, muestrafloat($total_impuesto).' Bs.',0, '','C');

//}


$pdf->Ln(6);
$pdf->Cell(175,0.1, '',1, '','C');
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(125,4, 'Total:',0, '','R');
$pdf->Cell(40,4, muestrafloat($total_impuesto).' Bs.',0, '','C');

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