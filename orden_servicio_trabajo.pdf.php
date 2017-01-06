<?
include("comun/ini.php");
include("Constantes.php");
$oOrden = new orden_servicio_trabajo();
$mPresupuestarios = new movimientos_presupuestarios;
$oOrden->get($conn, $_GET['id'], $escEnEje);
if(empty($oOrden->nrodoc))
	header ("location: orden_servicio_trabajo.php");
$_SESSION['pdf'] = serialize($oOrden);

// Crea un array donde cada posicion es un string de tamaño 'max' caracteres,
// teniendo en cuenta de no cortar una palabra, busca el espacio en blanco  
// mas cerca del tamaño 'max' y ahi corta el string

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
    }while ($posF != -1);
    
    return ($strArray);
  }

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
			$tipo = $oOrden->id_tipo_documento;

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oOrden->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			if($tipo == '002')
				$tipoOrden = "Orden de Servicio";
			elseif($tipo == '009')
				$tipoOrden = "Orden de Trabajo";
			$this->Text(80, 40, $tipoOrden);
			$this->Text(153, 40, "Nro.:".$oOrden->nrodoc."\n");
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
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);
$oOrden->id_tipo_documento;

// Orden de Servicio
//if($oOrden->id_tipo_documento == '002'){

$oProveedor = new proveedores;
$oProveedor->get($conn, $oOrden->id_proveedor);
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Cell(25,4, 'Sres.:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, utf8_decode($oProveedor->nombre),0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Rif:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oOrden->rif,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Nit:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->nit,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Fax:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->fax,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Tlf:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oProveedor->telefono,0, '','L' );

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, utf8_decode('Dirección'),0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4, utf8_decode($oProveedor->direccion),0, '','L');

//}

/*if($oOrden->id_tipo_documento == '009'){
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln();
	$pdf->SetFont('Courier','B',12);
	$pdf->Cell(123,4, 'Referencias del Beneficiario:',0, '','R');
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');

	$pdf->Ln(2);
	$ciudadano = dividirStr(utf8_decode($oOrden->ciudadano), intval(60/$pdf->GetStringWidth('M')));
	/*$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'Nombre:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(60,4, $oOrden->ciudadano,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'RIF o CED:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(40,4, $oOrden->rif,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'Nombre:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(60,4, $ciudadano[0],0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, 'RIF o CED:',0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(40,4, $oOrden->rif,0, '','L');
	while (next($ciudadano))
	{
		$pdf->Ln();
		$pdf->Cell(20,4, '',0, '','L');
		$pdf->Cell(60,4, current($ciudadano),0, '','L');
		$pdf->Cell(20,4, '',0, '','L');
		$pdf->Cell(40,4, '',0, '','L');
	}
	
	$pdf->Ln();
	$direccion = dividirStr(utf8_decode($oOrden->dir_ciudadano), intval(60/$pdf->GetStringWidth('M')));
	/*$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, utf8_decode('Dirección:'),0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(60,4, $oOrden->dir_ciudadano,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, utf8_decode('Teléfono:'),0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(40,4, $oOrden->tlf_ciudadano,0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, utf8_decode('Dirección:'),0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(60,4, $direccion[0],0, '','L');
	$pdf->SetFont('Courier','B',8);
	$pdf->Cell(20,4, utf8_decode('Teléfono:'),0, '','L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(40,4, $oOrden->tlf_ciudadano,0, '','L');
	while (next($direccion))
	{
		$pdf->Ln();
		$pdf->Cell(20,4, '',0, '','L');
		$pdf->Cell(60,4, current($direccion),0, '','L');
		$pdf->Cell(20,4, '',0, '','L');
		$pdf->Cell(40,4, '',0, '','L');
	}
}*/


$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
//$pdf->SetFont('Courier','B',8);
//$pdf->Cell(50,4, 'Condiciones de la Operacion:',0, '','L');
//$pdf->SetFont('Courier','',8);
//$pdf->Cell(120,4, $oOrden->condicion_operacion,0, '','L');

$pdf->Ln(4);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Lugar de Entrega:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oOrden->lugar_entrega,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Fecha de Entrega:',0, '','R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, muestrafecha($oOrden->fecha_entrega),0, '','L');

if($oOrden->id_tipo_documento != '009'){
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Nro de Factura:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oOrden->nro_factura,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Fecha de Factura:',0, '','R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, muestrafecha($oOrden->fecha_factura),0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Nro de Cotizacion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, $oOrden->nro_cotizacion,0, '','L');
}
if($oOrden->status=='1') {
	$msj= "Registrada";
	} elseif($oOrden->status=='2') {
		 $msj= "Aprobada";
		 } else {
		 	$msj= "Anulada";
		}

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Status:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, $msj,0, '','L');

$pdf->Ln(6);
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
if($oOrden->id_tipo_documento != '009'){
$pdf->Cell(35,4, 'Nro de Requisicion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $oOrden->nro_requisicion,0, '','L');
}
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Unidad Solicitante:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(60,4, $oOrden->id_unidad_ejecutora." - ". utf8_decode($oOrden->unidad_ejecutora),0, '','L');


/*$pdf->Ln();
$pdf->SetFont('Courier','',8);

$pdf->Cell(100,4, $oOrden->id_unidad_ejecutora." - ". utf8_decode($oOrden->unidad_ejecutora),0, '','L');
*/$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',12);
$pdf->Cell(102,4, 'Servicios:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);

$pdf->SetAligns(array('C','C','C','C'));
$pdf->SetWidths(array(65,35,35,35));
$pdf->SetFont('Courier','B',8);
$pdf->RowNL(array(utf8_decode('Descripción'),'Precio',' IVA',' Total'));

$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetWidths(array(65,35,35,35));
$pdf->SetAligns(array('C','C','C','C'));
$JsonPro = new Services_JSON();
//die(print_r($oOrden->relacionProductos));
$JsonPro = $JsonPro->decode(str_replace("\\","",$oOrden->relacionProductos));

$pdf->SetFont('Courier','B',7);
foreach($JsonPro as $producto){
	$sumCosto += $producto[1];
	$sumIva += $producto[2];  
	$totalFila = $producto[1]+ $producto[2];
	$precioTotal += $totalFila;
	$pdf->RowNL(array($producto[0],muestraFloat($producto[1]),
									muestraFloat($producto[2]),
									muestraFloat($totalFila)));
}
$pdf->Ln(1);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(147,4, 'SUB TOTAL',0, '','R');
$pdf->Cell(25,4, muestrafloat($sumCosto),0, '','R');
$pdf->Ln();
$pdf->Cell(147,4, 'TOTAL IVA',0, '','R');
$pdf->Cell(25,4, muestrafloat($sumIva),0, '','R');
$pdf->Ln();
$pdf->Cell(147,4, 'TOTAL',0, '','R');
$pdf->Cell(25,4, muestrafloat($precioTotal),0, '','R');
$pdf->Ln();

if($anoCurso == 2007){
	$pdf->Cell(147,4, 'TOTAL Bs.F.',0, '','R');
	$pdf->Cell(25,4, muestrafloat($precioTotal/1000),0, '','R');
	$pdf->Ln();
}

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'IMPUTACION PRESUPUESTARIA',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(100,4, utf8_decode('Descripción'),0, '','C');
$pdf->Cell(35,4, 'Monto',0, '','C');

$cPartidas = $mPresupuestarios->getImputacionReportes($conn,$oOrden->nrodoc,$escEnEje);
//$cPartidas = $oOrden->getRelacionPartidas($conn, $oOrden->id, $escEnEje);

foreach($cPartidas as $partida){
	$desc_partida = dividirStr($partida->partida, intval(90/$pdf->GetStringWidth('M')));
	$pdf->Ln();
	$pdf->Cell(50,4, $partida->id_categoria." - ".$partida->id_partida,0, '','L');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(30,4, muestrafloat($partida->monto),0, '','R');
	$montoTotal += $partida->monto;
	
	$hay_ue = next($desc_partida);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(50,4, '',0, '','C');
    		$pdf->Cell(90, 4, $desc_partida[$i], 0, '', 'L');
    		$pdf->Cell(30,4, '',0, '','R');
    		$hay_ue = next($desc_partida);
  		}
	
}
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(35,4, muestrafloat($montoTotal),0, '','R');
$pdf->Ln();

if($anoCurso == 2007){
	$pdf->Cell(140,4, 'TOTAL Bs.F.',0, '','R');
	$pdf->Cell(35,4, muestrafloat($montoTotal/1000),0, '','R');
	$pdf->Ln();
}
if($oOrden->id_tipo_documento == '009'){
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(30,8, '',0, '','R');
$pdf->Cell(50,8, '___________________',0, '','C');
$pdf->Cell(70,8, '___________________',0, '','C');
$pdf->Ln();
$pdf->Cell(30,8, '',0, '','C');
$pdf->Cell(50,8, utf8_decode('Jefe de Unidad Solicitante'),0, '','C');
$pdf->Cell(70,8, utf8_decode('Responsable'),0, '','C');
$pdf->Ln(15);
$pdf->Cell(30,8, '',0, '','R');
$pdf->Cell(30,8, '___________________',0, '','C');
$pdf->Cell(60,8, '___________________',0, '','C');
$pdf->Cell(30,8, '___________________',0, '','C');
$pdf->Ln();
$pdf->Cell(30,8, '',0, '','C');
$pdf->Cell(30,8, utf8_decode('Control Previo'),0, '','C');
$pdf->Cell(60,8, utf8_decode('Dir. de Administración'),0, '','C');
$pdf->Cell(30,8, utf8_decode('Contraloria'),0, '','C');

}else{
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(50,8, '',0, '','R');
$pdf->Cell(30,8, '___________________',0, '','C');
$pdf->Cell(70,8, '___________________',0, '','C');

$pdf->Ln();
$pdf->Cell(50,8, '',0, '','C');
$pdf->Cell(30,8, utf8_decode('Presupuesto'),0, '','C');
$pdf->Cell(70,8, utf8_decode('Div. Superior'),0, '','C');

$pdf->Ln(15);
$pdf->Cell(30,8, '',0, '','R');
$pdf->Cell(30,8, '___________________',0, '','C');
$pdf->Cell(60,8, '___________________',0, '','C');
$pdf->Cell(30,8, '___________________',0, '','C');
$pdf->Ln();
$pdf->Cell(30,8, '',0, '','C');
$pdf->Cell(30,8, utf8_decode('Control Previo'),0, '','C');
$pdf->Cell(60,8, utf8_decode('Dir. de Administración'),0, '','C');
$pdf->Cell(30,8, utf8_decode('Contraloria'),0, '','C');
}

$pdf->Output();
?>
