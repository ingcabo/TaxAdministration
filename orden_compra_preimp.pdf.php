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
			$oOrden = unserialize($_SESSION['pdf']);
			$this->SetLeftMargin(15);
			$this->SetFont('Arial','',10); 
			$this->Text(176, 22, muestraFecha($oOrden->fecha)."\n");
			$this->SetFont('Arial','B',12);
			$this->Text(180, 30, $oOrden->nrodoc);
			$this->Ln(40);
			
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

$pdf->SetFont('Arial','',10);
$nomb_proveedor = dividirStr($oProveedor->nombre, intval(100/$pdf->GetStringWidth('M')));
$pdf->Cell(100,4,utf8_decode($nomb_proveedor[0]),0, '','L');
$pdf->Cell(20,4,muestraFecha($oOrden->f_entrega),0,'','L');
$pdf->Cell(50,4,utf8_decode($oOrden->l_entrega),0,'','L');

			$linea = 0;
			$hay_ue = next($nomb_proveedor);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$linea+= 3;
				$pdf->Ln();
				$pdf->Cell(100,4, $nomb_proveedor[$i], 0, '', 'L');
				$hay_ue = next($nomb_proveedor);
			}
$pdf->Ln(10 - $linea);
$pdf->Cell(100,4,$oProveedor->rif,0,'','L');
$pdf->cell(80,4,utf8_decode($oOrden->forma_pago),0,'','L');
$pdf->Ln();
$dir_proveedor = dividirStr($oProveedor->direccion, intval(120/$pdf->GetStringWidth('M')));
$pdf->Cell(120,4,utf8_decode($dir_proveedor[0]),0, '','L');
			$linea = 0;
			$hay_ue = next($dir_proveedor);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$linea+= 3;
				$pdf->Ln();
				$pdf->Cell(120,4, $dir_proveedor[$i], 0, '', 'L');
				$hay_ue = next($dir_proveedor);
			}
$pdf->Ln(10 - $linea);
$pdf->Cell(90,4,utf8_decode($proveedor->estado.' - '.$proveedor->municipio),0,'','L');
$unid_ejecutora = dividirStr($oOrden->unidad_ejecutora, intval(50/$pdf->GetStringWidth('M')));
$pdf->Cell(50,4,utf8_decode($unid_ejecutora[0]),0,'','L');
$pdf->Cell(25,4,$oOrden->id_requisicion,0,'','C');
$sql = "SELECT fecha_r AS fecha FROM puser.requisiciones WHERE id = '$oOrden->unidad_ejecutora'";
$row = $conn->Execute($sql);
$pdf->Cell(25,4,muestraFecha($row->fields['fecha']),0,'','C');
	$linea = 0;
			$hay_ue = next($unid_ejecutora);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$linea+= 4;
				$pdf->Ln();
				$pdf->Cell(90,4, '', 0, '', 'L');
				$pdf->Cell(50,4, $unid_ejecutora[$i], 0, '', 'L');
				$hay_ue = next($unid_ejecutora);
			}
$pdf->Ln();
$pdf->Cell(90,4,$proveedor->telefono,0,'','L');

$pdf->SetXY(30,100);
$pdf->Cell(50,4,'VER ANEXO',0,'','C');

$pdf->SetXY(30,140);
$pdf->Cell(50,4,'VER ANEXO',0,'','C');

$JsonPresupuesto = $mPresupuestario->getImputacionReportes($conn,$oOrden->nrodoc,$escEnEje);


$JsonProductos = $oOrden->relacionProductoReporte;

$sub_total = 0;
$totaliva = 0;
$total = 0;
foreach($JsonProductos as $producto){
	$sub_total+= $producto['precio_base'] * $producto['cantidad'];
	$total_iva+=$producto['precio_iva'];
	$total+=$producto['monto'];
}

$pdf->SetXY(20,188);
$observacion = dividirStr($oOrden->observaciones, intval(150/$pdf->GetStringWidth('M')));
$pdf->Cell(140,4,utf8_decode($observacion[0]),0,'','L');
$pdf->Cell(40,4, muestraFloat($sub_total),0, '','R');
$pdf->Ln();
$pdf->Cell(145,4,utf8_decode($observacion[1]),0,'','L');
$pdf->Cell(40,4, muestraFloat($total_iva),0, '','R');
$pdf->Ln();
$pdf->Cell(145,4,utf8_decode($observacion[2]),0,'','L');
$pdf->Cell(40,4, muestraFloat($total),0, '','R');
	/*$hay_ue = next($observacion);
			for ($i=1; $hay_ue!==false; $i++)
			{
				$pdf->Ln();
				$pdf->Cell(90,4, $observacion[$i], 0, '', 'L');
				$hay_ue = next($observacion);
			}*/

$pdf->SetXY(120,170);









ver_anexo($JsonPresupuesto, $JsonProductos, $pdf);



$pdf->Output();

?>