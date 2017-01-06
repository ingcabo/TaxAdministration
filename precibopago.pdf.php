<?
include("comun/ini.php");
//Creación del objeto de la clase heredada
$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->SetLeftMargin(15);
$q = "SELECT B.cont_nom,A.nom_fec_ini,A.nom_fec_fin FROM rrhh.nomina AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod WHERE A.cont_cod=".$_GET['id'];
$rN = $conn->Execute($q);
$q = "SELECT DISTINCT A.tra_cod,B.tra_nom,B.tra_ape,B.tra_ced,B.tra_sueldo, B.tra_fec_ing ,D.car_nom,E.dep_nom FROM (((rrhh.nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod) INNER JOIN rrhh.nomina AS C ON A.nom_cod=C.int_cod) INNER JOIN rrhh.cargo AS D ON B.car_cod=D.int_cod) INNER JOIN rrhh.departamento AS E ON B.dep_cod=E.int_cod WHERE C.cont_cod=".$_GET['id']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) ORDER BY B.tra_nom";
$rT = $conn->Execute($q);
while(!$rT->EOF){
	$pdf->AddPage();
	for($i=0;$i<=1;$i++){
		$pdf->SetFont('Courier','',8);
		$pdf->Image ("images/logoa.jpg",15,4+($pdf->GetY()-12),26);//logo a la izquierda 
		$pdf->MultiCell(165,0, "Fecha: ".date('d/m/Y'), 0, 'R');
		$pdf->Ln(15);
		$pdf->SetFont('Courier','b',12);
		$pdf->Cell(170, 0, "Recibo de Pago ",0,0,'C');
		$pdf->Ln(6);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(85, 0, "Nomina: ".$rN->fields['cont_nom'],0,0,'L');
		$pdf->Cell(85, 0, "Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
		$pdf->Ln(5);
		$pdf->Cell(85, 0, "Trabajador: ". utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0,0,'L');
		$pdf->Cell(85, 0, "Cedula: ". utf8_decode($rT->fields['tra_ced']),0,0,'R');
		$pdf->Ln(5);
		$pdf->Cell(85, 0, "Cargo: ". utf8_decode($rT->fields['car_nom']),0,0,'L');
		$pdf->Cell(85,0,"F.I.: ". muestrafecha($rT->fields['tra_fec_ing']), 0,0,'R');
		$pdf->Ln(5);
		$pdf->Cell(170, 0, "Departamento: ". utf8_decode($rT->fields['dep_nom']),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(170, 0, "Sueldo Mensual: ".muestrafloat($rT->fields['tra_sueldo']),0,0,'R');
		$pdf->Ln(3);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(100,5,"Concepto",'B', 0,'L');	
		$pdf->Cell(70,5,"Monto",'B', 0,'R');	
		$pdf->Ln(10);
		$q = "SELECT A.conc_desc, C.conc_tipo,(CASE WHEN (C.conc_tipo=1) THEN (A.conc_val*-1) ELSE (A.conc_val) END) AS valor FROM (rrhh.nom_tra_conc AS A INNER JOIN rrhh.nomina AS B ON A.nom_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE B.cont_cod=".$_GET['id']." AND A.conc_val<>0 AND A.tra_cod=".$rT->fields['tra_cod']." AND (A.conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1) ORDER BY C.conc_tipo, A.conc_cod";
		$rC = $conn->Execute($q);
		$TotalTrabajador=0;
		while(!$rC->EOF) {
			$pdf->SetFont('Courier','',10);
			$pdf->Cell(100,0,$rC->fields['conc_desc'],0, 0,'L');	
		if(($rC->fields['valor'] > 0.15) && ($rC->fields['conc_tipo'] == 0)) { 
			$pdf->Cell(70,0, muestrafloat($rC->fields['valor']),0, 0,'R');			
			$TotalTrabajador+=$rC->fields['valor'];			
		}
		elseif(($rC->fields['valor'] < -0.10) && ($rC->fields['conc_tipo'] == 1) ){ 
			$pdf->Cell(70,0, muestrafloat($rC->fields['valor']),0, 0,'R');			
			$TotalTrabajador+=$rC->fields['valor'];			
		}else{
			$pdf->Cell(70,0, '',0, 0,'R');
		}
		$pdf->Ln(5);
		$rC->movenext();
		}
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(170,5, "Total: ".muestrafloat($TotalTrabajador) ,'T', 0,'R');	
		$pdf->Ln(15);
		$pdf->Cell(60,5,'',0, 0,'C');	
		$pdf->Cell(50,5,"Recibi Conforme",'T', 0,'C');	
		$pdf->Ln(20);
		if($i==0){
			$pdf->Cell(170,0,'','B', 0,'');	
		}
		$pdf->Ln(20);
	}
	$rT->movenext();
} 
$pdf->Output();
?>
