<?
set_time_limit(0);
include("comun/ini.php");
function dividirStr($str, $max){
	$strArray = array();
    do{
    	if (strlen($str) > $max)
        	$posF = strrpos( substr($str, 0, $max), ' ' );
		else
        	$posF = -1;
		if ($posF===false || $posF==-1){
	    	$strArray[] = substr($str, 0);
        	$str = substr($str, 0);
        	$posF = -1;
      	}else{
        	$strArray[] = substr($str, 0, $posF);
        	$str = substr($str, $posF+1 );
      	}
    }while ($posF != -1);
    return ($strArray);
}
//Creación del objeto de la clase heredada
$pdf=new FPDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetLeftMargin(10);
$q = "SELECT cont_nom,nom_fec_ini,nom_fec_fin,cont_cod FROM rrhh.historial_nom WHERE int_cod=".$_GET['id'];
$rN = $conn->Execute($q);

$q = "SELECT DISTINCT A.tra_cod,A.tra_nom,B.tra_ced,B.tra_sueldo,B.car_nom,D.tra_fec_ing, B.dep_nom,B.dep_ord FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.hist_nom_tra_sueldo AS B ON A.tra_cod=B.tra_cod 
	INNER JOIN rrhh.trabajador AS D ON D.int_cod = B.tra_cod  WHERE A.hnom_cod=".$_GET['id']." AND B.hnom_cod=".$_GET['id']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) AND B.tra_vac=0 ORDER BY B.dep_ord,A.tra_cod ";
//die($q);
$rT = $conn->Execute($q);
$pag=1;
while(!$rT->EOF){
	$pdf->Ln(2);
	$pdf->SetFont('Courier','',12);
	$pdf->Image ("images/logoa.jpg",15,$pdf->GetY()-8,26);//logo a la izquierda 
	$pdf->MultiCell(190,0, "Fecha: ".date('d/m/Y'), 0, 'R');
	$pdf->Ln(15);
	$pdf->SetFont('Courier','b',16);
	$pdf->Cell(170, 0, "RECIBO DE PAGO ",0,0,'C');
	$pdf->Ln(10);
	$pdf->SetFont('Courier','',10);
	$pdf->Cell(95, 0,"NOMINA: ".$rN->fields['cont_nom'],0,0,'L');
	$pdf->Cell(95, 0,"PERIODO: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
	$pdf->Ln(5);
	$pdf->Cell(95, 0, "TRABAJADOR: ". utf8_decode($rT->fields['tra_nom']),0,0,'L');
	$pdf->Cell(95, 0, "CEDULA: ". utf8_decode($rT->fields['tra_ced']),0,0,'R');
	$pdf->Ln(5);
	$pdf->Cell(95,0,"CARGO: ". utf8_decode($rT->fields['car_nom']), 0,0,'L');
	$pdf->Cell(95,0,"F.I.: ". muestrafecha($rT->fields['tra_fec_ing']), 0,0,'R');
	$pdf->Ln(5);
	$desc= dividirStr("DEPARTAMENTO: ".utf8_decode($rT->fields['dep_nom']), intval(170/$pdf->GetStringWidth('M')));
	$pdf->Cell(190,0, $desc[0],0, 0,'L');	
	$pdf->Ln(5);
	for($i=1;$i<count($desc);$i++){
		$pdf->Cell(190,0,$desc[$i],0, 0,'L');
		$pdf->Ln(5);
	}
	
	//esto se hace para calcular el sueldo integral en las nominas de vacaciones
	$q = "SELECT conc_desc, (CASE WHEN (conc_tipo=1) THEN (conc_val*-1) ELSE (conc_val) END) AS valor, conc_tipo FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." AND (conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) ORDER BY conc_tipo, conc_cod";
	$rC = $conn->Execute($q);
	$Asignacion=0;
	$Deduccion=0;	
	$pdf->Cell(190, 0, "SUELDO MESUAL: ".muestrafloat($rT->fields['tra_sueldo']),0,0,'R');
	$pdf->Ln(5);
	$pdf->SetFont('Courier','B',12);
	$pdf->Cell(70,5,"CONCEPTO",'B', 0,'L');
	$pdf->Cell(40,5,"ASIGNACION",'B', 0,'R');	
	$pdf->Cell(40,5,"DEDUCION",'B', 0,'R');	
	$pdf->Cell(40,5,"MONTO A PAGAR",'B', 0,'R');		
	$pdf->Ln(10);
	$TotalTrabajador=0;
	$nroConceptos=0;
	$rC->MoveFirst();
	while(!$rC->EOF) {
		$pdf->SetFont('Courier','',12);
		$pdf->Cell(70,5, $rC->fields['conc_desc'],0, 0,'L');
		$pdf->Cell(40,5,$rC->fields['conc_tipo']==0 ? muestrafloat($rC->fields['valor']) : '',0, 0,'R');	
		$pdf->Cell(40,5,$rC->fields['conc_tipo']==1 ? muestrafloat($rC->fields['valor']) : '',0, 0,'R');	
		$pdf->Cell(40,5,'',0, 0,'R');
		$Asignacion+= $rC->fields['conc_tipo']==0 ? $rC->fields['valor'] : 0;
		$Deduccion+=  $rC->fields['conc_tipo']==1 ? $rC->fields['valor'] : 0;		
		$TotalTrabajador+=$rC->fields['valor'];			
		$nroConceptos++;
		$pdf->Ln(5);
		$rC->movenext();
	}
	$pdf->SetFont('Courier','B',12);
		$pdf->Cell(70,5,'','T', 0,'R');	
		$pdf->Cell(40,5,muestrafloat($Asignacion),'T', 0,'R');	
		$pdf->Cell(40,5,muestrafloat($Deduccion),'T', 0,'R');	
		$pdf->Cell(40,5,muestrafloat($TotalTrabajador),'T', 0,'R');	
	$pdf->Ln(15);
	$pdf->Cell(70,5,'',0, 0,'C');	
	$pdf->Cell(50,5,"RECIBI CONFORME",'T', 0,'C');	
	if($nroConceptos<=4){
		$pdf->Ln(60);
	}elseif($nroConceptos>=5 && $nroConceptos<=9 ){
		$pdf->Ln(40);
	}elseif($pag!=2){
		$pdf->AddPage();
	}
	if($pag==2){
		$pdf->AddPage();
		$pag=0;
	}
	$pag++;
	$rT->movenext();
} 
$pdf->Output();
?>
