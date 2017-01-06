<?
include("comun/ini.php");
$oprestamo = new prestamo;
$oprestamo->get($conn, $_GET['id']);
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

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');

			
			$this->SetFont('Courier','b',15);

			$titulo = "Prestamo";

			$this->Text(84, 33, $titulo);
			$this->SetFont('Courier','b',12);
			$this->Line(15, 37, 190, 37);
			$this->ln(20);
			
	}

	//Pie de página
	function Footer()
	{
		
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


$q = "SELECT A.emp_nom, D.tra_ced FROM ((rrhh.empresa AS A INNER JOIN rrhh.division AS B ON A.int_cod=B.emp_cod) INNER JOIN rrhh.departamento AS C ON B.int_cod=C.div_cod) INNER JOIN rrhh.trabajador AS D ON C.int_cod=D.dep_cod WHERE D.int_cod=".$oprestamo->tra_cod;
$rT = $conn->Execute($q);
if(!$rT->EOF){
	$Empresa = $rT->fields['emp_nom'];
	$tra_ced = $rT->fields['tra_ced'];
}else{
	$Empresa = "";
	$tra_ced = "";
}


$pdf->Ln(20);
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(180,4, 'Yo, '.utf8_decode($oprestamo->tra_nom).', titular de la cedula de identidad Nro '.utf8_decode($tra_ced).', en mi condicion de empleado de '.utf8_decode($Empresa).', por medio de la presente me comprometo en cancelar el prestamo de '.muestraFloat($oprestamo->pres_monto).' Bs que mantengo pendiente con dicha empresa, el pago se hara de la manera siguiente: ',0, '','L' );

$pdf->Ln(3);

$pdf->SetFont('Courier','B',12);
$pdf->Cell(102,4, 'Cuotas:',0, '','R');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln(2);

$pdf->SetAligns('C');
$pdf->SetWidths(array(10,40,40,30,30));
$pdf->SetFont('Courier','B',8);
$pdf->RowNL(array('Nro.','Fecha Inicio Nomina','Fecha Fin Nomina','Porcentaje','Monto'));

$pdf->Ln(2);
$pdf->Cell(175,0.2, '',1, '','C');
$pdf->Ln(2);

$pdf->SetWidths(array(10,40,40,30,30));
$pdf->SetAligns('J');

$JsonRec = new Services_JSON();
$JsonRec=$JsonRec->decode(str_replace("\\","",$oprestamo->pres_det));
foreach($JsonRec as $Cuotas){
	$pdf->RowNL(array($Cuotas[5],muestrafecha($Cuotas[0]),muestrafecha($Cuotas[1]),muestrafloat($Cuotas[2]),muestrafloat($Cuotas[3])));      
}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(40,25, '',0, '','R');
$pdf->Cell(40,25, '___________________',0, '','C');
$pdf->Cell(40,25, '___________________',0, '','C');
$pdf->Ln();
$pdf->Cell(40,0, '',0, '','R');
$pdf->Cell(40,0, 'Empleado',0, '','C');
$pdf->Cell(40,0, 'Analista RRHH',0, '','C');
$pdf->Ln(); 
$pdf->Output();
?>
