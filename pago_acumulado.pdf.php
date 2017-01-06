<?
include("comun/ini.php");
$q = "SELECT A.fecha,A.descripcion,A.tipo,A.fecha,A.observaciones,B.tra_nom,B.tra_ape,B.tra_ced,B.tra_fec_ing,B.tra_fec_egr,B.tra_sueldo,C.dep_nom,D.car_nom FROM ((rrhh.pago_acumulado AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod) INNER JOIN rrhh.departamento AS C ON B.dep_cod=C.int_cod) INNER JOIN rrhh.cargo AS D ON B.car_cod=D.int_cod  WHERE A.int_cod=".$_GET['id'];
$rT=$conn->Execute($q);
$_SESSION['conex'] = $rT;
if($rT->fields['descripcion']=='' || empty($rT->fields['descripcion'])){
	if($rT->fields['tipo']==0){
		$Titulo="Pago de Conceptos Acumulados";
	}else if($rT->fields['tipo']==1){
		$Titulo="Anticipo de Conceptos Acumulados";
	}else{
		$Titulo="Liquidacion";
	}
}else{
	$Titulo=$rT->fields['descripcion'];
}
$_SESSION['T'] = $Titulo;
$q = "SELECT * FROM rrhh.empresa WHERE int_cod=".$_SESSION['EmpresaL'];
$rE=$conn->Execute($q);

class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$rT = $_SESSION['conex'];
			$Titulo = $_SESSION['T'];
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".muestrafecha($rT->fields['fecha']), 0, 'L');
			$this->Ln(20);
			$this->SetFont('Courier','',8);
			$this->Cell(0, 0, utf8_decode($Titulo),0,0,'C');
			$this->Ln(5);
			$this->Cell(90, 5, utf8_decode("Trabajador: ".$rT->fields['tra_nom']." ".$rT->fields['tra_ape']),'T',0,'L');
			$this->Cell(90, 5, utf8_decode("Cedula: ".$rT->fields['tra_ced']),'T',0,'L');
			$this->Ln(7);
			$this->Cell(90, 0, utf8_decode("Departamento: ".$rT->fields['dep_nom']),0,0,'L');
			$this->Cell(90, 0, utf8_decode("Cargo: ".$rT->fields['car_nom']),0,0,'L');
			$this->Ln(5);
			$this->Cell(90, 0, utf8_decode("Sueldo Mensual: ".muestrafloat($rT->fields['tra_sueldo'])),0,0,'L');
			$this->Cell(90, 0, utf8_decode("Sueldo Diario: ".muestrafloat($rT->fields['tra_sueldo']/30)),0,0,'L');
			$this->Ln(5);
			$this->Cell(90, 0, utf8_decode("Fecha de Ingreso: ".muestrafecha($rT->fields['tra_fec_ing'])),0,0,'L');
			if(!empty($rT->fields['tra_fec_egr']))
			$this->Cell(90, 0, utf8_decode("Fecha de Egreso: ".muestrafecha($rT->fields['tra_fec_egr'])),0,0,'L');
			$this->Ln(5);
			$this->Cell(180, 0, utf8_decode("Observaciones: ".$rT->fields['observaciones']),0,0,'L');
	}

	function Footer()
	{
/*		$this->Ln(10);			
		$conn = $_SESSION['conex'];
		$q = "SELECT C.emp_dir, C.emp_telf FROM (rrhh.historial_nom AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod) INNER JOIN rrhh.empresa AS C ON B.emp_cod=C.int_cod WHERE A.int_cod=".$_GET['id'];
		$rC = $conn->Execute($q);
		$this->SetFont('Courier','',6);
		$this->Cell(170,0,'','T', 0,'C');	
		$this->Ln(1);			
		$this->MultiCell(170,5, utf8_decode($rC->fields['emp_dir'])." - Telefonos: ".utf8_decode($rC->fields['emp_telf']), 0, 'L');
		$this->Ln(1);			
		$this->Cell(170,0,'Valencia - Edo. Carabobo',0, 0,'C');	
		//Número de página */
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(15);
$pdf->Ln(10);
$pdf->SetFont('Courier','',8);
$pdf->Cell(130,5,"Concepto",1, 0,'C');
$pdf->Cell(50,5,"Monto",1, 0,'C');	
$q = "SELECT A.conc_val,A.conc_tipo,B.conc_nom FROM rrhh.pago_acumulado_conc AS A INNER JOIN rrhh.concepto AS B ON A.conc_cod=B.int_cod WHERE A.pago_acum_cod=".$_GET['id'];
$rC = $conn->Execute($q);
$TotalNomina=0;
$pdf->Ln(5);
while(!$rC->EOF){
	$pdf->Cell(130,5, utf8_decode($rC->fields['conc_nom']),1, 0,'L');	
	$pdf->Cell(50,5, $rC->fields['conc_tipo']!=1 ? muestrafloat($rC->fields['conc_val']) : "-".muestrafloat($rC->fields['conc_val']) ,1, 0,'R');	
	$rC->fields['conc_tipo']!=1 ? $TotalNomina+=$rC->fields['conc_val'] : $TotalNomina-=$rC->fields['conc_val'];
	$rC->movenext();
} 
$pdf->Ln(5);
$pdf->Cell(130,5,"Totales:",0, 0,'R');	
$pdf->Cell(50,5,muestrafloat($TotalNomina),1, 0,'R');	
$pdf->SetFont('Courier','B',8);
$pdf->Ln(15);
$pdf->Cell(0,0, "Conformidad por parte del trabajador:", 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Courier','',8);
$pdf->MultiCell(0,4, utf8_decode("Por cuanto con fecha ".muestrafecha($rT->fields['fecha'])." declaro que he recibido de ".$rE->fields['emp_nom']." la cantidad de  ".muestrafloat($TotalNomina)." Bs correspondiente a \"".$Titulo."\" conforme a las previciones de la Ley Organica del Trabajo y su Reglamento General y los detalles arriba suministrador, con los cuales declaro estar \"COMFORME\"."), 0, 'J');
$pdf->Ln(10);
$pdf->MultiCell(0,4,utf8_decode("Para dar fe de esta conformidad suscribo al pie de la misma con mi firma autografa y me identifico, con mi puño y letra, tanto con mis nombre y apellidos como con mi numero de cedula de identidad"), 0, 'J');
$pdf->Ln(10);
$pdf->Cell(80,7, "Nombres y Apellidos: ", 1, 'L');
$pdf->Cell(50,7, "Nro Cedula: ", 1, 'C');
$pdf->Cell(50,7, "Firma: ", 1, 'R');
$pdf->Output();
?>
