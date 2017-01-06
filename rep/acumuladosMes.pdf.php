<?
include("comun/ini.php");
$_SESSION['conex'] = $conn;
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/tc_logo.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',12);
			$this->Cell(0, 0, "Conceptos Acumulados ",0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',8);
			$this->Cell(170, 0, "Periodo: ".$_GET['Periodo'],0,0,'R');
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
$q = "SELECT DISTINCT A.tra_cod,C.tra_nom,C.tra_ape FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.trabajador AS C ON A.tra_cod=C.int_cod WHERE B.periodo='".$_GET['Periodo']."' ORDER BY tra_nom";
$rT = $conn->Execute($q);
$TotalNomina=0;
while(!$rT->EOF){
	$q = "SELECT C.conc_nom, A.conc_val::numeric(20,2),A.conc_desc FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE B.periodo='".$_GET['Periodo']."' AND A.conc_val<>0 AND A.tra_cod=".$rT->fields['tra_cod']." ORDER BY A.conc_cod";
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	if(!$rC->EOF){
		$pdf->Cell(70,0,utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0, 0,'L');	
		while(!$rC->EOF) {
			if($TotalTrabajador!=0) {
				$pdf->Cell(70,0, '',0, 0,'L');	
			}
			$pdf->Cell(70,0, empty($rC->fields['conc_desc']) ? $rC->fields['conc_nom'] : $rC->fields['conc_desc'],0, 0,'L');	
			$pdf->Cell(30,0,  muestrafloat($rC->fields['conc_val']),0, 0,'R');	
			$TotalTrabajador+=calculafloat($rC->fields['conc_val'],2);
			$pdf->Ln(5);
			$rC->movenext();
		}
		$TotalNomina+=calculafloat($TotalTrabajador,2);
		$pdf->Cell(140,3, '' ,0, 0,'R');	
		$pdf->Cell(30,3, "Total: ".muestrafloat($TotalTrabajador) ,'T', 0,'R');	
		$pdf->Ln(4);
		$pdf->Cell(170,0, '' ,'B', 0,'L');	
		$pdf->Ln(4);
	}
	$rT->movenext();
} 
$pdf->Cell(170,0, "Total: ".muestrafloat($TotalNomina) ,0, 0,'R');	
$pdf->Output();
?>
