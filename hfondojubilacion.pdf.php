<?
include("comun/ini.php");
$_SESSION['conex'] = $conn;
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$conn = $_SESSION['conex'];
			$q = "SELECT cont_nom,nom_fec_ini,nom_fec_fin FROM rrhh.historial_nom WHERE int_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			$q = "SELECT conc_nom FROM rrhh.concepto WHERE int_cod=".$_GET['Conc'];
			//die($q);
			$rC = $conn->Execute($q);
			
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',12);
			$this->Cell(0, 0, $rC->fields['conc_nom'],0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',8);
			$this->Cell(85, 0, "Contrato: ".$rN->fields['cont_nom'],0,0,'L');
			$this->Cell(85, 0, "Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
			$this->Line(15, 37, 275, 37);
			$this->SetFont('Courier','B',10);
			$this->Ln(5);
			$this->Cell(65,0,"Trabajador",0, 0,'L');
			$this->Cell(30,0,"Cedula",0, 0,'R');
			$this->Cell(35,0,"Fecha N.",0, 0,'R');	
			$this->Cell(40,0,"Aporte",0, 0,'R');	
			$this->Cell(40,0,"Retencion",0, 0,'R');
			$this->Cell(40,0,"Total",0, 0,'R');		
			$this->Line(15, 42, 275, 42);
			$this->Ln(5);
	}

	function Footer()
	{
		
		$this->SetFont('Arial','I',8);
		//Número de página
		$this->Cell(180,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetLeftMargin(15);
$pdf->SetFont('Courier','',8);

$q = "SELECT DISTINCT A.tra_cod, A.tra_nom,B.tra_ced, B.tra_fec_nac FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE A.hnom_cod=".$_GET['id']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) AND B.tra_vac=0 ORDER BY A.tra_nom";
$rT = $conn->Execute($q);
$TotalNomina=0;
$TotalNominaAporte=0;
$TotalNominaJubilacion=0;
$Contador=0;
while(!$rT->EOF){
	$q = "SELECT conc_val AS valor, conc_aporte AS aporte FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." AND (conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) ORDER BY conc_tipo, conc_cod";
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	$TotalTrabajadorAporte=0;
	$TotalTrabajadorJubilacion=0;
	while(!$rC->EOF) {
		$pdf->Cell(65,0, utf8_decode($rT->fields['tra_nom']),0, 0,'L');	
		$pdf->Cell(30,0, $rT->fields['tra_ced'],0, 0,'R');
		$pdf->Cell(35,0, $rT->fields['tra_fec_nac'],0, 0,'R');	
		$pdf->Cell(40,0,  muestrafloat($rC->fields['aporte']),0, 0,'R');	
		$pdf->Cell(40,0,  muestrafloat($rC->fields['valor']),0, 0,'R');
		$pdf->Cell(40,0,  muestrafloat($rC->fields['valor']+$rC->fields['aporte']),0, 0,'R');		
		$TotalTrabajador+=$rC->fields['valor'];
		$TotalTrabajadorAporte+=$rC->fields['aporte'];
		$TotalTrabajadorJubilacion+=$rC->fields['valor'] + $rC->fields['aporte'];
		$pdf->Ln(5);
		$rC->movenext();
		$Contador++;
	}
	$TotalNomina+=$TotalTrabajador;
	$TotalNominaAporte+=$TotalTrabajadorAporte;
	$TotalNominaJubilacion+=$TotalTrabajadorJubilacion;
	$rT->movenext();
} 
$pdf->Cell(30,5, "Total Registros:",'T', 0,'L');	
$pdf->Cell(20,5,$Contador,'1', 0,'C');	
$pdf->Cell(90,5, "Totales:",'T', 0,'R');	
$pdf->Cell(40,5, muestrafloat($TotalNominaAporte) ,1, 0,'R');	
$pdf->Cell(40,5, muestrafloat($TotalNomina) ,1, 0,'R');
$pdf->Cell(40,5, muestrafloat($TotalNominaJubilacion) ,1, 0,'R');	
$pdf->Output();
?>
