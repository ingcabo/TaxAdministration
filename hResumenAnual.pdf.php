<?
include("comun/ini.php");
include("Constantes.php");
set_time_limit(0);
$_SESSION['conex'] = $conn;
class PDF extends FPDF{
//Cabecera de página
	function Header(){

			$conn = $_SESSION['conex'];
			$q = "SELECT cont_cod, cont_nom FROM rrhh.historial_nom WHERE int_cod=".$_GET['Nomina'];
			$rN = $conn->Execute($q);
			
			$this->SetLeftMargin(5);
			$this->SetFont('Courier','',10);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,26,20);//logo a la izquierda 
			$this->SetXY(37, 7); 
			$textoCabecera = ENTE."\n\n";
			$textoCabecera.= ORGANISMO_NOMBRE."\n\n";
			$textoCabecera.= DEPARTAMENTO."\n\n";
			$this->MultiCell(100,2, $textoCabecera, 0, 'L');

			$this->Image ("images/logoa.jpg",170,4,26,20);//logo a la izquierda 
			$this->SetXY(150, 7); 
			$this->Ln(19);
			$this->SetFont('Courier','b',14);
			$this->Cell(0, 0, "Resumen Anual ",0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','',8);
			$this->Ln(5);
			$this->Cell(95, 0, "Contrato: ".$rN->fields['cont_nom'],0,0,'L');
			$this->Cell(95, 0, "Periodo: ".$_REQUEST['FecIni']." AL ".$_REQUEST['FecFin'] ,0,0,'R');
			$this->Ln(5);

			$this->Line(5, 39, 195, 39);
			//$this->Line(15, 42, 185, 42);
			$this->Ln(2);
	}

	function Footer(){
		$this->SetFont('Courier','I',12);
		//Número de página
		$this->Cell(170,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
} 


//Creación del objeto de la clase heredada
$pdf=new PDF('p','mm');
$pdf->AliasNbPages();
$pdf->SetLeftMargin(15);

$q = "SELECT cont_cod FROM rrhh.historial_nom WHERE int_cod=".$_GET['Nomina'] ;
$rN = $conn->Execute($q);

$FECHAI=split("/",$_REQUEST['FecIni']);
$MESI = $FECHAI[0];
$ANOI = $FECHAI[1];
$FECHAF=split("/",$_REQUEST['FecFin']);
$MESF = $FECHAF[0];
$ANOF = $FECHAF[1];

$q = "SELECT MIN(int_cod), MAX(int_cod), cont_nom, cont_cod FROM rrhh.historial_nom WHERE cont_cod=".$rN->fields['cont_cod']." 
	AND to_char(nom_fec_ini,'MM') BETWEEN '$MESI' AND '$MESF' AND 
	to_char(nom_fec_ini,'YYYY') = '$ANOI' 
	AND to_char(nom_fec_fin,'MM') BETWEEN '$MESI' AND '$MESF' AND 
	to_char(nom_fec_fin,'YYYY') = '$ANOF' 
	GROUP BY cont_nom, cont_cod";
//die($q);
$rN = $conn->Execute($q);

$q = "SELECT DISTINCT ON(A.tra_cod) A.tra_cod,B.tra_nom, B.tra_ape,B.tra_ced,A.tra_sueldo,A.car_nom, A.fun_nom,A.dep_nom,A.dep_ord 
FROM rrhh.hist_nom_tra_sueldo AS A 
INNER JOIN rrhh.trabajador AS B ON A.tra_cod = B.int_cod 
INNER JOIN rrhh.historial_nom AS C ON A.hnom_cod=C.int_cod   
WHERE C.cont_cod = ".$rN->fields['cont_cod']." AND A.hnom_cod  BETWEEN ".$rN->fields['min']." AND ".$rN->fields['max']." AND B.tra_vac = 0 AND (A.tra_cod=".$_GET['Trabajador']." OR ".$_GET['Trabajador']."=-1 ) 
ORDER BY  A.tra_cod, A.dep_ord ";
//die($q);
$rT = $conn->Execute($q);

while(!$rT->EOF){
	$pdf->AddPage();
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(95, 0, "Trabajador: ". utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0,0,'L');
	$pdf->Cell(95, 0, "Cedula: ". utf8_decode($rT->fields['tra_ced']),0,0,'R');
	$pdf->Ln(5);
	$rT->fields['tra_tipo']?$pdf->Cell(95, 0, "Funcion: ". utf8_decode($rT->fields['fun_nom']),0,0,'L'):$pdf->Cell(95, 0, "Cargo: ". utf8_decode($rT->fields['car_nom']),0,0,'L');
	$pdf->Cell(95, 0, "Sueldo Mensual: ".muestrafloat($rT->fields['tra_sueldo']),0,0,'R');
	$pdf->Ln(5);
	$pdf->Cell(190, 0, "Departamento: ". utf8_decode($rT->fields['dep_nom']),0,0,'L');
	$pdf->Ln(3);
	$pdf->SetFont('Courier','B',10);
	$pdf->Cell(120,5,"Concepto",'B', 0,'L');	
	$pdf->Cell(70,5,"Monto",'B', 0,'R');	
	$pdf->Ln(10);
	$q = "SELECT sum((CASE WHEN (b.conc_tipo=1) THEN (b.conc_val*-1) ELSE (b.conc_val) END)::numeric(10,2)) AS valor,B.conc_nom 
		FROM rrhh.historial_nom AS A
		INNER JOIN rrhh.hist_nom_tra_conc AS B ON A.int_cod = B.hnom_cod
		WHERE A.cont_cod = ".$rN->fields['cont_cod']." AND A.int_cod BETWEEN ".$rN->fields['min']." AND ".$rN->fields['max']." 
		AND B.conc_val<>0 AND B.tra_cod = ".$rT->fields['tra_cod']." GROUP BY conc_cod,B.conc_tipo, conc_nom  ORDER BY B.conc_tipo,B.conc_cod";
	//die($q);
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	if($rC->EOF){
		$pdf->Cell(170,0, 'No tiene registros para este rango de fechas',0, 0,'L');	
		$pdf->Ln(5);
	}else{
		while(!$rC->EOF){
			$pdf->SetFont('Courier','',10);
			$pdf->Cell(120,0, utf8_decode($rC->fields['conc_nom']),0, 0,'L');	
			$pdf->Cell(70,0,  muestrafloat($rC->fields['valor']),0, 0,'R');	
			$TotalTrabajador+=$rC->fields['valor'];
			$pdf->Ln(5);
			$rC->movenext();
		}
	}
	$pdf->SetFont('Courier','B',10);
	$pdf->Cell(190,5, "Total Trabajador: ".muestrafloat($TotalTrabajador) ,'T', 0,'R');	
	$pdf->Ln(15);
	$pdf->Cell(40,5,'',0, 0,'C');	
	$pdf->Ln(80);
	$rT->movenext();
}
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->Cell(190, 0, "RESUMEN TOTAL: ",0,0,'L');

$pdf->Ln(5);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(120,5,"Concepto",'B', 0,'L');	
$pdf->Cell(70,5,"Monto",'B', 0,'R');	
$pdf->Ln(10);
$q = "SELECT sum((CASE WHEN (b.conc_tipo=1) THEN (b.conc_val*-1) ELSE (b.conc_val) END)::numeric(10,2)) AS valor,conc_nom 
	FROM rrhh.historial_nom AS A 
	INNER JOIN rrhh.hist_nom_tra_conc AS B ON A.int_cod=B.hnom_cod 
	WHERE A.cont_cod = ".$rN->fields['cont_cod']." AND A.int_cod  BETWEEN ".$rN->fields['min']." AND ".$rN->fields['max']." 
	AND B.conc_val<>0 GROUP BY conc_cod,B.conc_tipo, conc_nom  ORDER BY B.conc_tipo,B.conc_cod";
//die($q);
$rC = $conn->Execute($q);
$TotalNomina = 0;
if($rC->EOF){
	$pdf->Cell(190,0, 'No tiene registros para este rango de fechas',0, 0,'L');	
	$pdf->Ln(5);
}else{
	while(!$rC->EOF){
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(120,0, utf8_decode($rC->fields['conc_nom']),0, 0,'L');	
		$pdf->Cell(70,0,  muestrafloat($rC->fields['valor']),0, 0,'R');	
		$TotalNomina+=$rC->fields['valor'];
		$pdf->Ln(5);
		$rC->movenext();
	}
}
$pdf->SetFont('Courier','B',10);
$pdf->Cell(190,5, "Total Nomina: ".muestrafloat($TotalNomina) ,'T', 0,'R');	
$pdf->Ln(15);
$pdf->Cell(40,5,'',0, 0,'C');
$pdf->Output();
?>
