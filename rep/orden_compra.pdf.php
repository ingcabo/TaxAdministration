<? 
include("comun/ini.php");
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
  
  function ver_anexo($presupuesto, $productos, $pdf){
	//die(var_dump($contabilidad));
	$pdf->AddPage();
	$pdf->Ln(2);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(102,4, 'Presupuesto:',0, '','R');
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(50,4, 'Categoria / Partida ',0, '','C');
	$pdf->Cell(90,4, 'Descripcion',0, '','C');
	$pdf->Cell(20,4, 'Monto',0, '','C');
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->SetFont('Arial','',8);
	foreach($presupuesto as $partida){
	$desc_partida = dividirStr($partida->partida, intval(90/$pdf->GetStringWidth('M')));
	$pdf->Ln();
	$pdf->Cell(50,4, $partida->id_categoria."-".$partida->id_partida,0, '','C');
	$pdf->Cell(90,4, $desc_partida[0],0, '','L');
	$pdf->Cell(20,4, muestraFloat($partida->monto),0, '','R');
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
	
	$pdf->Ln(6);
	$pdf->Cell(175,0.2, '',1, '','C');
	
	$pdf->Ln();
	$pdf->Cell(130,4, 'TOTAL',0, '','R');
	$pdf->Cell(28,4, muestraFloat($montoTotal),0, '','R');
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln(2);
			
	
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(102,4, 'Productos:',0, '','R');
	
	$pdf->Ln();
	$pdf->Cell(175,0.2, '',1, '','C');
	
	$pdf->Ln(2);
	
	$pdf->SetAligns('C');
	$pdf->SetWidths(array(10,37,16,14,27,23,27));
	$pdf->SetFont('Arial','B',10);
	$pdf->RowNL(array('Cod.','     Descrip.','Und/Med','Cant.','Precio Unit.',' IVA','  Precio',' Total'));
	
	$pdf->Ln(2);
	$pdf->Cell(175,0.2, '',1, '','C');
	$pdf->Ln(2);
	
	$pdf->SetWidths(array(7,40,18,14,22,25,25,25));
	$pdf->SetAligns('J');
	$pdf->SetFont('Arial','',10);
	foreach($productos as $producto){
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
}

class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,55,20);//logo a la izquierda 
			$this->SetXY(15, 22); 
			$textoCabecera = "GERENCIA DE ADMINISTRACION\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$oCompra = unserialize($_SESSION['pdf']);

			$this->SetXY(160, 12); 
			$this->SetFont('Courier','',10);
			$textoDerecha = "ORDEN DE COMPRA\n";
			$textoDerecha.= $oCompra->nrodoc."\n";
			$textoDerecha.= "Fecha:".muestrafecha($oCompra->fecha_aprobacion)."\n";
			//$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,4, $textoDerecha, 0, 'L');
					
			$this->Ln(4);
			
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
$pdf->SetFont('Arial','B',10);
$pdf->SetLeftMargin(15);


//DATOS DEL PROVEEDOR
$oProveedor = new proveedores;
$oProveedor->get($conn, $oOrden->id_proveedor);
$pdf->Cell(90,4,utf8_decode('Señores:'),0,'','L');
$pdf->Cell(90,4,utf8_decode('Descripcion:'),0,'','L');
$pdf->Ln();

$pdf->SetFont('Arial','',8);
$observacion = dividirStr($oOrden->observaciones, intval(100/$pdf->GetStringWidth('M')));
$nomb_proveedor = dividirStr($oProveedor->nombre, intval(120/$pdf->GetStringWidth('M')));
$pdf->Cell(15,4,'Proveedor:',0,'','L');
$pdf->Cell(80,4,utf8_decode($nomb_proveedor[0]),0,'','L');
$indice = 0;
$pdf->Cell(80,4,utf8_decode($observacion[$indice]),0,'','L');

			$linea = 0;
			$hay_ue = next($nomb_proveedor);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$linea+= 3;
				$indice += 1;
				$pdf->Ln();
				$pdf->Cell(15,4,'',0,'','L');
				$pdf->Cell(80,4, $nomb_proveedor[$i], 0, '', 'L');
				$pdf->Cell(80,4, $observacion[$indice], 0, '', 'L');
				$hay_ue = next($nomb_proveedor);
			}
$pdf->Ln();
$pdf->Cell(15,4,'RIF:',0,'','L');
$pdf->Cell(80,4,$oProveedor->rif,0,'','L');
$indice += 1;
$pdf->Cell(80,4, $observacion[$indice], 0, '', 'L');
$pdf->Ln();
$dir_proveedor = dividirStr($oProveedor->direccion, intval(100/$pdf->GetStringWidth('M')));
$pdf->Cell(15,4,'Direccion:',0,'','L');
$pdf->Cell(80,4,utf8_decode($dir_proveedor[0]),0, '','L');
$indice += 1;
$pdf->Cell(80,4, $observacion[$indice], 0, '', 'L');
			$linea = 0;
			$hay_ue = next($dir_proveedor);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$linea+= 3;
				$pdf->Ln();
				$indice += 1;
				$pdf->Cell(15,4, '', 0, '', 'L');
				$pdf->Cell(80,4, $dir_proveedor[$i], 0, '', 'L');
				$pdf->Cell(80,4, $observacion[$indice], 0, '', 'L');
				$hay_ue = next($dir_proveedor);
			}
$pdf->Ln(6);
$pdf->Cell(120,4,utf8_decode('Sirvase de despachar/Realizar por órden y cuenta nuestra lo que a continuación detallamos'),0,'','L');
$pdf->Ln(6);
$pdf->Cell(7,4,'Nro.',1,'LRBT','L');
$pdf->Cell(50,4,'Descripcion',1,'LRBT','L');
$pdf->Cell(20,4,'Impuesto',1,'LRBT','L');
$pdf->Cell(15,4,'Cantidad',1,'LRBT','L');
$pdf->Cell(18,4,'Unidad',1,'LRBT','L');
$pdf->Cell(30,4,'Precio Unit.',1,'LRBT','L');
$pdf->Cell(40,4,'Precio Total',1,'LRBT','L');
$pdf->Line(15,$pdf->GetY(),15,210);
$pdf->Line(195,$pdf->GetY(),195,210);
$pdf->Ln();
/*$pdf->SetXY(30,100);
$pdf->Cell(50,4,'VER ANEXO',0,'','C');

$pdf->SetXY(30,140);
$pdf->Cell(50,4,'VER ANEXO',0,'','C');*/

$JsonPresupuesto = $mPresupuestario->getImputacionReportes($conn,$oOrden->nrodoc,$escEnEje);


$JsonProductos = $oOrden->relacionProductoReporte;

$sub_total = 0;
$totaliva = 0;
$total = 0;
$i = 0;
foreach($JsonProductos as $producto){
	$i+=1;
	$pdf->Cell(7,4,$i,0,'','C');
	$descProd = dividirStr($producto['desc_producto'], intval(70/$pdf->GetStringWidth('M')));
	$pdf->Cell(50,4,$descProd[0],0,'','L');
	$pdf->Cell(20,4,muestrafloat($producto['precio_iva']),0,'','R');
	$pdf->Cell(15,4,muestrafloat($producto['cantidad']),0,'','R');
	$pdf->Cell(18,4,$producto['unidad_medida'],0,'','C');
	$pdf->Cell(30,4,muestrafloat($producto['precio_base']),0,'','R');
	$pdf->Cell(40,4,muestrafloat($producto['monto']),0,'','R');
	$sub_total+= $producto['precio_base'] * $producto['cantidad'];
	$total_iva+=$producto['precio_iva'];
	$total+=$producto['monto'];
	$hay_prod = next($descProd);
	for($j=1;$hay_prod!=false;$j++){
		$pdf->Ln();
		$pdf->Cell(7,4,'',0,'','C');
		$pdf->Cell(50,4,$descProd[$j],0,'','L');
		$pdf->Cell(20,4,'',0,'','R');
		$pdf->Cell(15,4,'',0,'','R');
		$pdf->Cell(18,4,'',0,'','C');
		$pdf->Cell(30,4,'',0,'','R');
		$pdf->Cell(40,4,'',0,'','R');
		$hay_prod = next($descProd);	
	}
	$pdf->Ln();
}

$pdf->Line(15,210,195,210);

$pdf->Line(170,210,170,222);
$pdf->Line(195,210,195,222);
$pdf->Line(170,214,195,214);
$pdf->Line(170,218,195,218);
$pdf->Line(170,222,195,222);

$pdf->Text(150,213,'Subtotal');
$pdf->Text(150,217,'Impuestos');
$pdf->Text(150,221,'Total');

$pdf->Text(172,213,muestraFloat($sub_total));
$pdf->Text(172,217,muestraFloat($total_iva));
$pdf->Text(172,221,muestraFloat($total));

$pdf->Line(15,212,75,212);
$pdf->Line(15,212,15,232);
$pdf->Line(75,212,75,232);
$pdf->Line(15,232,75,232);
$pdf->Text(15,215,'Elaborado Por:');
$pdf->Text(15,219,utf8_decode($usuario->nombre.' '.$usuario->apellido));

$pdf->Line(85,212,135,212);
$pdf->Line(85,212,85,232);
$pdf->Line(135,212,135,232);
$pdf->Line(85,232,135,232);
$pdf->Text(85,215,'Aprobado Por:');

$pdf->Line(15,237,100,237);
$pdf->Line(15,237,15,270);
$pdf->Line(100,237,100,270);
$pdf->Line(15,270,100,270);
$pdf->Text(15,240,'Presupuesto');


$pdf->SetXY(15,242);
$pdf->SetFont('Arial','',8);
foreach($JsonPresupuesto as $partida){
	$pdf->Cell(50,3, $partida->id_categoria."-".$partida->id_partida,0, '','L');
	$pdf->Cell(30,3, muestraFloat($partida->monto),0, '','R');
	$pdf->Ln();
}

$pdf->Line(110,237,195,237);
$pdf->Line(110,237,110,270);
$pdf->Line(195,237,195,270);
$pdf->Line(110,270,195,270);
$pdf->Text(112,240,'Recibido por el Proveedor:');
$pdf->Text(112,246,'Nombre y Apellido:');
$pdf->Text(112,252,'Cedula:');
$pdf->Text(112,258,'Firma:');

//ver_anexo($JsonPresupuesto, $JsonProductos, $pdf);





$pdf->Output();

?>