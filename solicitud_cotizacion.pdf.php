<?
include("comun/ini.php");
include("Constantes.php");
$id_requisicion = $_REQUEST['id_requisicion'];
if(empty($id_requisicion))
	header ("location: revision_requisicion.php");
$_SESSION['pdf'] = serialize($oCajaChica);
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

			$oReviRequi = unserialize($_SESSION['pdf']);

			$this->SetXY(150, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			/*$textoDerecha.= "Fecha Generac.:".muestrafecha($oCajaChica->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oCajaChica->fecha_aprobacion)."\n";*/
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			/*$this->SetFont('Courier','b',12);
			$this->Text(80, 40, "Caja Chica");
			$this->Text(153, 40, "Nro.:".$oCajaChica->nrodoc."\n");
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);*/
			
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

$pdf->SetFont('Courier','B',8);
$pdf->SetLeftMargin(15);


//OBTIENE LA CANTIDAD DE EMPRESAS SELECCIONADAS PARA COTIZAR
$q="SELECT DISTINCT puser.proveedores.nombre, puser.proveedores.rif, puser.proveedores.direccion, puser.proveedores.contacto, puser.proveedores.telefono ";
$q.="FROM puser.proveedores_requisicion ";
$q.="Inner Join puser.proveedores ON puser.proveedores_requisicion.id_proveedor = puser.proveedores.id ";
$q.="WHERE puser.proveedores_requisicion.id_requisicion =  '$id_requisicion'";
//die($q);
$provee= $conn->Execute($q);
$numero = $provee->RecordCount();

//OBTIENE LOS PRODUCTOS DE LA REQUISICION
$q = "SELECT DISTINCT puser.proveedores_requisicion.id_requisicion, puser.productos.descripcion,puser.relacion_gbl_requisicion.cantidad, productos.unidad_medida ";
$q.= "FROM puser.proveedores_requisicion ";
$q.= "Inner Join puser.relacion_gbl_requisicion ON puser.proveedores_requisicion.id_requisicion = puser.relacion_gbl_requisicion.id_gbl_requisicion ";
$q.= "Inner Join puser.productos ON puser.relacion_gbl_requisicion.id_producto = puser.productos.id ";
$q.= "WHERE puser.proveedores_requisicion.id_requisicion =  '$id_requisicion'";

$cArticulo= $conn->Execute($q);
$cantidad= $cArticulo->RecordCount();


for($i=0;$i<$numero;$i++){
$pdf->AddPage();
$pdf->Ln(15);
$pdf->Ln(3);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'SOLICITUD DE COTIZACION',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Sr(a).:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,$provee->fields['nombre'],0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'RIF',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $provee->fields['rif'],0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Direccion',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4, $provee->fields['direccion'],0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(7,4, 'Tlf:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(25,4, $provee->fields['telefono'],0, '','L' );
$pdf->Ln();
$pdf->Ln(3);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'ARTICULOS INCLUIDOS EN LA COTIZACION',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(110,4,'',0,'','L');
$pdf->Cell(30,4,'Unidad',0,'','C');
$pdf->Ln();
$pdf->Cell(110,4, 'Descripcion','L');
$pdf->Cell(30,4,'Medida',0,'','C');
$pdf->Cell(35,4, 'Cantidad',0, '','R');
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();

$pdf->SetFont('Courier','',8);
for ($j=0;$j<$cantidad;$j++){
	
	$pdf->Ln();
	$pdf->Cell(110,4, $cArticulo->fields['descripcion'],0, '','L');
	$pdf->Cell(30,4, $cArticulo->fields['unidad_medida'],0, '','C');
	$pdf->Cell(35,4, $cArticulo->fields['cantidad'],0, '','R');
	$cArticulo->movenext();
}
	$cArticulo->MoveFirst();

$pdf->Ln();
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');


$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln(5);
$pdf->Cell(175,20,'____________________',0,'','C');
$pdf->Ln();
$pdf->Cell(175,2, 'JEFE DE COMPRAS',0, '','C');
$pdf->Ln();
$provee->movenext();
//$pdf->AddPage();
}

$pdf->Output();
?>
