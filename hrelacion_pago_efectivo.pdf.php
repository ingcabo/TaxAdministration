<?
include("comun/ini.php");
include("Constantes.php");
$_SESSION['conex'] = $conn;
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$conn = $_SESSION['conex'];
			$q = "SELECT cont_nom,nom_fec_fin FROM rrhh.historial_nom WHERE int_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			$q = "SELECT A.nro_cuenta,B.id,B.descripcion,C.descripcion AS tc FROM (finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id) INNER JOIN puser.tipos_cuentas_bancarias AS C ON A.id_tipo_cuenta=C.id   WHERE A.id=".$_GET['Cuenta'];
			$rB = $conn->Execute($q);
			$_SESSION['Ban']=$rB->fields['id'];
			$fecha =split("-",$rN->fields['nom_fec_fin']);
			$dia=$fecha[2]-1;
			$fecha=$dia."/".$fecha[1]."/".$fecha[0];
			$this->SetLeftMargin(15);
			$this->SetFont('Times','',12);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(25);
			$this->Cell(0, 0,"Señores",0,0,'L');
			$this->Ln(4);
			$this->SetFont('Times','b',12);
			$this->Cell(0, 0, '',0,0,'L');
			$this->Ln(4);
			$this->SetFont('Times','',12);
			$this->Cell(0, 0,"Ciudad.-",0,0,'L');
			$this->Ln(6);
			$this->Cell(170, 0,"Asunto: Pago de ".$rN->fields['cont_nom'],0,0,'J');
			$this->Ln(6);
			$this->Cell(70, 4,utf8_decode("Pago en Efectivo de ".$rN->fields['cont_nom']." de fecha ".$fecha.",  por los siguientes montos: "),0,'J');
			$this->Ln(6);			
	}

	function Footer()
	{
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(15);
$pdf->SetFont('Times','',12);

$pdf->Cell(100,5,"Nombre y Apellido",1, 0,'C');
$pdf->Cell(30,5,"Cedula",1, 0,'C');	
$pdf->Cell(40,5,"Monto a Cobrar",1, 0,'C');	
$pdf->Ln(5);

$q = "SELECT DISTINCT A.tra_cod,B.tra_nom,B.tra_ape,B.tra_ced FROM rrhh.hist_nom_tra_conc AS A 
INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod 
INNER JOIN rrhh.historial_nom as C ON A.hnom_cod=C.int_cod 
WHERE A.hnom_cod = ".$_GET['id']." AND B.tra_tip_pag = 0 AND B.tra_vac = 0 ORDER BY B.tra_nom";
//die($q);

$rT = $conn->Execute($q);
$TotalNomina=0;
$TotalRegistro=0;

while(!$rT->EOF){
	$pdf->Cell(100,5, utf8_decode($rT->fields['tra_nom']).' '.utf8_decode($rT->fields['tra_ape']),1, 0,'L');	
	$pdf->Cell(30,5, utf8_decode($rT->fields['tra_ced']),1, 0,'C');	
	$q = "SELECT conc_desc, conc_cod, (CASE WHEN (conc_tipo=1) THEN (conc_val::numeric(20,2)*-1) ELSE (conc_val::numeric(20,2)) END) AS valor FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." ORDER BY conc_tipo, conc_cod";


	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	while(!$rC->EOF) {
		$rC->fields['conc_cod'] ==  '71' ?  $TotalTrabajador+= 0 : $TotalTrabajador+=$rC->fields['valor'];
		$rC->movenext();
	}
	$TotalNomina+=$TotalTrabajador;
	$TotalRegistro++;
	$pdf->Cell(40,5, muestrafloat($TotalTrabajador),1, 0,'R');	
	$pdf->Ln(5);
	$rT->movenext();
} 
$pdf->Cell(50,5,"Totales Registros:",0, 0,'L');
$pdf->Cell(30,5,$TotalRegistro,0, 0,'L');
$pdf->Cell(40,5,"Totales Bolivares:",0, 0,'R');	
$pdf->Cell(50,5,muestrafloat($TotalNomina),0, 0,'R');	
$pdf->Ln(15);
$pdf->Cell(175,5, 'Atentamente','', '','C');
$pdf->Ln(15);
$pdf->Cell(45,5, '','', '','C');
$pdf->Cell(70,5, JEFERRHH,'T', '','C');
$pdf->Cell(45,5, '','', '','C');
$pdf->Ln(5);
$pdf->Cell(45,5, '','', '','C');
$pdf->Cell(70,5, 'Coordinador de Recursos Humanos',0, '','C');
$pdf->Cell(45,5, '','', '','C');

$pdf->Output();
?>
