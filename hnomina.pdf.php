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
			
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',12);
			$this->Cell(0, 0, "Historial de Nomina ",0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',8);
			$this->Cell(85, 0, $rN->fields['cont_nom'],0,0,'L');
			$this->Cell(85, 0, "Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
			$this->Line(15, 37, 185, 37);
			$this->SetFont('Courier','B',10);
			$this->Ln(5);
			$this->Cell(70,0,"Trabajador",0, 0,'L');
			$this->Cell(70,0,"Concepto",0, 0,'L');	
			$this->Cell(30,0,"Monto",0, 0,'R');	
			$this->Line(15, 42, 185, 42);
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
$pdf->AddPage();
$pdf->SetLeftMargin(15);
$pdf->SetFont('Courier','',8);

$q = "SELECT DISTINCT tra_cod,tra_nom  FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND (tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) ORDER BY tra_nom";
$rT = $conn->Execute($q);
$TotalNomina=0;
while(!$rT->EOF){
	$pdf->Cell(70,0, utf8_decode($rT->fields['tra_nom']),0, 0,'L');	
	$q = "SELECT conc_desc, (CASE WHEN (conc_tipo=1) THEN (conc_val*-1) ELSE (conc_val) END) AS valor FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." AND (conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) ORDER BY conc_tipo, conc_cod";
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	while(!$rC->EOF) {
		if($TotalTrabajador!=0) {
			$pdf->Cell(70,0, '',0, 0,'L');	
		}
		$pdf->Cell(70,0, $rC->fields['conc_desc'],0, 0,'L');	
		$pdf->Cell(30,0,  muestrafloat($rC->fields['valor']),0, 0,'R');	
		$TotalTrabajador+=$rC->fields['valor'];
		$pdf->Ln(5);
		$rC->movenext();
	}
	$TotalNomina+=$TotalTrabajador;
	$pdf->Cell(140,3, '' ,0, 0,'R');	
	$pdf->Cell(30,3, "Total: ".muestrafloat($TotalTrabajador) ,'T', 0,'R');	
	$pdf->Ln(4);
	$pdf->Cell(170,0, '' ,'B', 0,'L');	
	$pdf->Ln(4);
	$rT->movenext();
} 
$pdf->Cell(170,0, "Total Nomina: ".muestrafloat($TotalNomina) ,0, 0,'R');	
$pdf->Output();
?>
