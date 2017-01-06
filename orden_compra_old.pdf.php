<?
include("comun/ini.php");
include("Constantes.php");
$oOrden = new ordcompra();
$mPresupuestario = new movimientos_presupuestarios;
$oOrden->get($conn, $_GET['id'], $escEnEje);
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
			$this->SetXY(42, 7); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= "DPTO. COMPRAS";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oOrden = unserialize($_SESSION['pdf']);
			$tipo = $oOrden->id_tipo_documento;

			$this->SetXY(150, 7); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Nro Solic.:".$oOrden->id_requisicion."\n";
			$textoDerecha.= "Fecha de Entrega:".muestrafecha($oOrden->f_entrega)."\n";
			$textoDerecha.= "Fecha de Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',15);

				$tipoOrden = "Orden de Compra";

			$this->Text(84, 33, $tipoOrden);
			$this->SetFont('Courier','b',12);
			$this->Text(153, 33, "Nro.:".$oOrden->nrodoc."\n");
			$this->Line(15, 37, 190, 37);
			$this->Ln(20);
			
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
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

$oProveedor = new proveedores;
$oProveedor->get($conn, $oOrden->id_proveedor);
//$pdf->SetFillColor(232 , 232, 232);

$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();

$pdf->SetFont('Courier','B',12);
$pdf->Cell(123,4, utf8_decode('Identificación del Proveedor'),0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, utf8_decode('Razón Social:'),0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $oProveedor->id." - ".utf8_decode($oProveedor->nombre),0, '','L');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, utf8_decode('Dirección:'),0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4, $oProveedor->direccion,0, '','L');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(16,4, 'Estado:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, utf8_decode($oOrden->id_municipio_proveedor),0, '','L' );
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(10,4, 'Rif:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(20,4, $oOrden->nrif,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(15,4, 'Nit:',0, '','R' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $oProveedor->nit,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(15,4, 'Fax:',0, '','R' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $oProveedor->fax,0, '','L' );
$pdf->SetFont('Courier','B',8);
$pdf->Cell(15,4, 'Tlf:',0, '','R' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $oProveedor->telefono,0, '','L' );
$pdf->Ln(6);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();

$pdf->SetFont('Courier','B',12);
$pdf->Cell(123,4, 'Condicion de Operacion:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->Ln();

$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Fecha de Entrega:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, muestrafecha($oOrden->f_entrega),0, '','L');
if($oOrden->status=='1') {
	$msj= "Registrada";
	} elseif($oOrden->status=='2') {
		 $msj= "Aprobada";
		 } elseif($oOrden->status=='3') {
		 	$msj= "Anulada";
			} else {
				$msj= "Recibida";
			}
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Status:',0,'', 'R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, $msj,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Lugar de Entrega:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, $oOrden->l_entrega,0, '','L');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Forma de Pago:',0,'', 'R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, utf8_decode($oOrden->forma_pago),0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(35,4, 'Fecha de Solicitud:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(35,4, muestrafecha($oOrden->f_solicitud),0, '','L');			
$pdf->Ln(6);


$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(50,4, 'Unidad Ejecutora: ',0, '','L');
$pdf->Cell(100,4, $oOrden->id_unidad_ejecutora." - ". utf8_decode($oOrden->unidad_ejecutora),0, '','L');

$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Ln();

$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',12);
$pdf->Cell(125,4, 'Imputacion Presupuestaria:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();


$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(60,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(90,4, 'Descripcion',0, '','L');
$pdf->Cell(6,4, 'Monto',0, '','R');

$JsonPro = $mPresupuestario->getImputacionReportes($conn,$oOrden->nrodoc,$escEnEje);

foreach($JsonPro as $partida){
	$desc_partida = dividirStr($partida->partida, intval(90/$pdf->GetStringWidth('M')));
	$pdf->Ln();
	$pdf->Cell(60,4, $partida->id_categoria."-".$partida->id_partida,0, '','C');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(8,4, muestraFloat($partida->monto),0, '','R');
	$montoTotal += $partida->monto;
	
	$hay_ue = next($desc_partida);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(60,4, '',0, '','C');
    		$pdf->Cell(90, 4, $desc_partida[$i], 0, '', 'L');
    		$pdf->Cell(8,4, '',0, '','R');
    		$hay_ue = next($desc_partida);
  		}
}

/*$JsonPro = $oOrden->relacionProductoReporte;

foreach($JsonPro as $partida){
	$desc_partida = dividirStr($partida['partida_presupuestaria'], intval(90/$pdf->GetStringWidth('M')));
	$pdf->Ln();
	$pdf->Cell(60,4, $partida['id_categoria_programatica']."-".$partida['id_partida_presupuestaria'],0, '','C');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(8,4, muestraFloat($partida['monto']),0, '','R');
	$montoTotal += $partida['monto'];
	
	$hay_ue = next($desc_partida);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(60,4, '',0, '','C');
    		$pdf->Cell(90, 4, $desc_partida[$i], 0, '', 'L');
    		$pdf->Cell(8,4, '',0, '','R');
    		$hay_ue = next($desc_partida);
  		}
}*/
$pdf->Ln(6);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(130,4, 'TOTAL',0, '','R');
$pdf->Cell(28,4, muestraFloat($montoTotal),0, '','R');
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetFont('Courier','B',12);
$pdf->Cell(102,4, 'Productos:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);

$pdf->SetAligns('C');
$pdf->SetWidths(array(10,37,16,14,27,23,27));
$pdf->SetFont('Courier','B',8);
$pdf->RowNL(array('Cod.','     Descrip.','Und/Med','Cant.','Precio Unit.',' IVA','  Precio',' Total'));

$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetWidths(array(7,40,18,14,22,25,25,25));
$pdf->SetAligns('J');

$JsonPro = new Services_JSON();
//die(print_r($oOrden->relacionProductos));
//print_r($oOrden->relacionProductos)."<br><br>";
$JsonPro = $oOrden->relacionProductoReporte;
//die(print_r($JsonPro));
//$cProductos = $oOrden->getRelacionProductos($conn, $oOrden->id);

foreach($JsonPro as $producto){
$totalFila = $producto['monto'];
$precioTotal += $totalFila;
$sub = $producto['precio_base'] * $producto['cantidad'];
//die("aqui ".$sub);
$pdf->RowNL(array($producto['id_producto'],$producto['desc_producto'],$producto['unidad_medida'],$producto['cantidad'],muestraFloat($producto['precio_base']),muestraFloat($producto['precio_iva']),muestraFloat($sub),muestraFloat($totalFila))); 
     }
$pdf->Ln(1);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(31,4, muestraFloat($precioTotal),0, '','R');
$pdf->Ln();

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

/*$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Courier','B',12);
$pdf->Cell(125,4, 'Imputacion Presupuestaria:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();


$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(60,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(90,4, 'Descripcion',0, '','L');
$pdf->Cell(6,4, 'Monto',0, '','R');

$JsonPro = $oOrden->relacionProductoReporte;

foreach($JsonPro as $partida){
	$pdf->Ln();
	$pdf->Cell(60,4, $partida['id_categoria_programatica']."-".$partida['id_partida_presupuestaria'],0, '','C');
	$pdf->Cell(90,4, $partida['partida_presupuestaria'],0, '','L');
	$pdf->Cell(8,4, muestraFloat($partida['monto']),0, '','R');
	$montoTotal += $partida['monto'];
}
$pdf->Ln(6);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(130,4, 'TOTAL',0, '','R');
$pdf->Cell(28,4, muestraFloat($montoTotal),0, '','R');
$pdf->Ln();*/

$pdf->Ln(11);

$pdf->Cell(10,8, '',0, '','R');
$pdf->Cell(20,8, '___________________',0, '','C');
$pdf->Cell(70,8, '___________________',0, '','C');
$pdf->Cell(20,8, '___________________',0, '','C');
$pdf->Cell(60,8, '___________________',0, '','C');
$pdf->Ln();
$pdf->Cell(10,4, '',0, '','C');
$pdf->Cell(20,4, utf8_decode('Sección de Compras'),0, '','C');
$pdf->Cell(70,4, utf8_decode('Control de presupuesto'),0, '','C');
$pdf->Cell(20,4, 'Unidad solicitante',0, '','C');
$pdf->Cell(60,4, 'Unidad Superior',0, '','C');
$pdf->Ln();
$pdf->Cell(175,5, '',0, '','C');
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(175,4, utf8_decode('CONTRALORÍA'),0, '','C');
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');


$pdf->Ln(10);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,8, '',0, '','R');
$pdf->Cell(20,8, '___________________',0, '','C');
$pdf->Cell(70,8, '___________________',0, '','C');
$pdf->Cell(20,8, '___________________',0, '','C');

$pdf->Ln();
$pdf->Cell(30,4, '',0, '','C');
$pdf->Cell(20,4, utf8_decode('Control de presupuesto'),0, '','C');
$pdf->Cell(70,4, utf8_decode('Control Previo'),0, '','C');
$pdf->Cell(20,4, 'Contralor',0, '','C');
$pdf->Output();
?>
