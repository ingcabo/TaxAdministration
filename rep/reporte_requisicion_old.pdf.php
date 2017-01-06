<?
include("comun/ini.php");
include("Constantes.php");
$id_requisicion = $_REQUEST['id_requisicion'];
if(!empty($_REQUEST['preRequisicion']))
	$preRequisicion = true;
else
	$preRequisicion = false;
if(empty($id_requisicion))
	header ("location: revision_requisicion.php");
$oRequisicion = new requisiciones;
$oRequisicion->get($conn,$id_requisicion);
$_SESSION['pdf'] = serialize($oRequisicion);
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
			$textoDerecha.= "Fecha Generac.:".muestrafecha($oReviRequi->fecha)."\n";
			$textoDerecha.= "Fecha Aprob.:".muestrafecha($oReviRequi->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->SetFont('Courier','b',12);
			$this->Text(40, 40, " ",0,'',R);
			$this->Text(115, 40, " Requisicion Nro.:".$oReviRequi->id."\n");
			//$this->Line(15, 41, 190, 41);
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


//OBTIENE LOS PRODUCTOS DE LA REQUISICION
$q = "SELECT DISTINCT puser.productos.descripcion, puser.relacion_requisiciones.cantidad, puser.relacion_requisiciones.id_partida, puser.relacion_requisiciones.id_categoria, puser.productos.unidad_medida FROM puser.relacion_requisiciones Inner Join puser.productos ON puser.relacion_requisiciones.id_producto = puser.productos.id WHERE puser.relacion_requisiciones.id_requisicion =  '$id_requisicion'";
//die($q);
$cArticulo= $conn->Execute($q);
$cantidad= $cArticulo->RecordCount();

$pdf->Ln(3);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',12);
if(!$preRequisicion)
	$titulo = 'REQUISICION';
else
	$titulo = 'PRE-REQUISICION';	
$pdf->Cell(175,4, $titulo,0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');
//$pdf->SetFillColor(232 , 232, 232);
$pdf->Ln(3);
$pdf->SetFont('Courier','B',8);
$pdf->Cell(30,4, 'Unidad Ejecutora:',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,$oRequisicion->unidad_ejecutora,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(14,4, 'Status:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4, $oRequisicion->nom_status,0, '','L');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(23,4, 'Descripcion:',0, '','L');
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(100,4, $oRequisicion->motivo,0, '','L');

$pdf->Ln(3);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'ARTICULOS INCLUIDOS EN LA REQUISICION',0, '','C');

$pdf->Ln(6);
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(25,4, 'Categoria',LR,'','L');
$pdf->Cell(25,4, 'Partida',LR,'','L');
$pdf->Cell(90,4, '  ',LR,'','C');
$pdf->Cell(20,4, 'Unidad',LR,'','C');
$pdf->Cell(15,4, '  ',LR, '','C');
$pdf->Ln();
$pdf->Cell(25,4, 'Programatica',LR,'','L');
$pdf->Cell(25,4, 'Presupuestaria',LR,'','L');
$pdf->Cell(90,4, 'Descripcion',LR,'','C');
$pdf->Cell(20,4, 'Medida',LR,'','C');
$pdf->Cell(15,4, 'Cantidad',LR, '','C');
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();

$pdf->SetFont('Courier','',8);
for ($j=0;$j<$cantidad;$j++){
	
	$pdf->Ln();
	$pdf->Cell(25,4, $cArticulo->fields['id_categoria'],LR, '','L');
	$pdf->Cell(25,4, $cArticulo->fields['id_partida'],LR, '','L');
	$pdf->Cell(90,4, $cArticulo->fields['descripcion'],LR, '','L');
	$pdf->Cell(20,4, $cArticulo->fields['unidad_medida'],LR, '','C');
	$pdf->Cell(15,4, $cArticulo->fields['cantidad'],LR, '','R');
	$cArticulo->movenext();
}

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(25);

if(!$preRequisicion){
	$pdf->Cell(175,4, '_______________________',0,'','C');
	$pdf->Ln();
	$pdf->Cell(175,4, 'JEFE DE COMPRAS',0,'','C');
}



//$pdf->AddPage();


$pdf->Output();
?>
