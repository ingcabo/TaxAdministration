<?
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
$_SESSION['conex'] = $conn;
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$conn = $_SESSION['conex'];
			$q = "SELECT cont_nom,nom_fec_ini,nom_fec_fin,cont_cod FROM rrhh.historial_nom WHERE int_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			$q = "SELECT A.nro_cuenta,B.id,B.descripcion,C.descripcion AS tc FROM (finanzas.cuentas_bancarias AS A INNER JOIN public.banco AS B ON A.id_banco=B.id) INNER JOIN puser.tipos_cuentas_bancarias AS C ON A.id_tipo_cuenta=C.id   WHERE A.id=".$_GET['Cuenta'];
			$rB = $conn->Execute($q);
			$_SESSION['Ban']=$rB->fields['id'];
			$fecha =split("-",$rN->fields['nom_fec_fin']);
			$dia=$fecha[2]-1;
			$fecha=$dia."/".$fecha[1]."/".$fecha[0];
			$this->SetLeftMargin(5);
			$this->SetRightMargin(5);
			$this->SetFont('Times','',12);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,26);//logo a la izquierda 
			$this->SetXY(42, 7); 

			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(25);
			$this->Cell(0, 0, utf8_decode("Señores"),0,0,'L');
			$this->Ln(4);
			$this->SetFont('Times','B',12);
			$this->Cell(0, 0, utf8_decode($rB->fields['descripcion']),0,0,'L');
			$this->Ln(4);
			$this->SetFont('Times','',12);
			$this->Cell(0, 0,"Ciudad.-",0,0,'L');
			$this->Ln(6);
			if(($rN->fields['cont_cod'] == 7) || ($rN->fields['cont_cod'] == 9)){
				$this->Cell(200, 0,"Asunto: Pago de Nomina de ".$rN->fields['cont_nom']." Periodo: 2006 - 2007",0,0,'R');
			} else {
				$this->Cell(200, 0,"Asunto: Pago de Nomina de ".$rN->fields['cont_nom']." Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
			}	
			$this->Ln(6);
			$this->MultiCell(200, 4,utf8_decode("Nos dirigirnos a Ustedes, con la finalidad de enviarles la información correspondiente al pago de la nómina de  ".$rN->fields['cont_nom']." de fecha ".$fecha.", autorizando al ".utf8_decode($rB->fields['descripcion'])." a Debitar de nuestra ".utf8_decode($rB->fields['tc'])." Nro. ".utf8_decode($rB->fields['nro_cuenta'])." y abonar a las siguientes cuentas:"),0,'J');
			$this->Ln(6);			
	}

	function Footer()
	{
		$this->Ln(10);			
		$conn = $_SESSION['conex'];
		$q = "SELECT C.emp_dir, C.emp_telf FROM (rrhh.historial_nom AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod) INNER JOIN rrhh.empresa AS C ON B.emp_cod=C.int_cod WHERE A.int_cod=".$_GET['id'];
		$rC = $conn->Execute($q);
		$this->SetFont('Times','',12);
		$this->Cell(200,0,'','T', 0,'C');	
		$this->Ln(1);			
		$this->MultiCell(200,5, utf8_decode($rC->fields['emp_dir'])." - Telefonos: ".utf8_decode($rC->fields['emp_telf']), 0, 'L');
		$this->Ln(1);			
		$this->Cell(200,0,'Edo. Aragua',0, 0,'C');	
		//Número de página
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(5);
$pdf->SetFont('Courier','',10);

$pdf->Cell(70,5,"Nombre y Apellido",1, 0,'C');
$pdf->Cell(30,5,"Cedula",1, 0,'C');	
$pdf->Cell(20,5,"Monto",1, 0,'C');	
$pdf->Cell(50,5,"Nro Cuenta",1, 0,'C');	
$pdf->Cell(30,5,"Tipo Cta",1, 0,'C');	
$pdf->Ln(5);
$q = "SELECT DISTINCT A.tra_cod,A.tra_nom,B.tra_ced,B.tra_num_cta,B.tra_tipo_cta FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod WHERE A.hnom_cod=".$_GET['id']." AND B.ban_cod=".$_SESSION['Ban']." AND B.tra_tip_pag=2 ORDER BY A.tra_nom";
$rT = $conn->Execute($q);
$TotalNomina=0;
$TotalRegistros=0;
while(!$rT->EOF){
	$nom = dividirStr($rT->fields['tra_nom'], intval(70/$pdf->GetStringWidth('M')));
	$border= count($nom)>1 ? 0 : 'B';
	$pdf->Line(5, $pdf->GetY(), 5, $pdf->GetY()+5);
	$pdf->Cell(70,5, $nom[0],$border, 0,'L');	
	$pdf->Line(75, $pdf->GetY(), 75, $pdf->GetY()+5);
	$pdf->Cell(30,5, utf8_decode($rT->fields['tra_ced']),$border, 0,'C');	
	$pdf->Line(105, $pdf->GetY(), 105, $pdf->GetY()+5);
	$q = "SELECT conc_desc, (CASE WHEN (conc_tipo=1) THEN (conc_val::numeric(20,2)*-1) ELSE (conc_val::numeric(20,2)) END) AS valor FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." ORDER BY conc_tipo, conc_cod";
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	while(!$rC->EOF) {
		$TotalTrabajador+=$rC->fields['valor'];
		$rC->movenext();
	}
	$TotalNomina+=$TotalTrabajador;
	$TotalRegistros++;
	$TipoCta= $rT->fields['tra_tipo_cta']==0 ? "Corriente" : "Ahorro";
	$pdf->Cell(20,5, muestrafloat($TotalTrabajador),$border, 0,'R');	
	$pdf->Line(125, $pdf->GetY(), 125, $pdf->GetY()+5);
	$pdf->Cell(50,5, utf8_decode($rT->fields['tra_num_cta']),$border, 0,'R');	
	$pdf->Line(175, $pdf->GetY(), 175, $pdf->GetY()+5);
	$pdf->Cell(30,5, utf8_decode($TipoCta),$border, 0,'C');	
	$pdf->Line(205, $pdf->GetY(), 205, $pdf->GetY()+5);
	$pdf->Ln(5);
	for($i=1;$i<count($nom);$i++){
		$border= ($i+1==count($nom)) ? 'B' : 0;
		$pdf->Line(5, $pdf->GetY(), 5, $pdf->GetY()+5);
		$pdf->Cell(70,5, $nom[$i],$border, 0,'L');	
		$pdf->Line(75, $pdf->GetY(), 75, $pdf->GetY()+5);
		$pdf->Cell(30,5, '',$border, 0,'C');	
		$pdf->Line(105, $pdf->GetY(), 105, $pdf->GetY()+5);
		$pdf->Cell(40,5, '',$border, 0,'R');	
		$pdf->Line(145, $pdf->GetY(), 145, $pdf->GetY()+5);
		$pdf->Cell(30,5, '',$border, 0,'R');	
		$pdf->Line(175, $pdf->GetY(), 175, $pdf->GetY()+5);
		$pdf->Cell(30,5, '',$border, 0,'C');	
		$pdf->Line(205, $pdf->GetY(), 205, $pdf->GetY()+5);
		$pdf->Ln(5);
	}
	$rT->movenext();
} 
$pdf->Cell(30,5,"Registros:",0, 0,'L');
$pdf->Cell(40,5,$TotalRegistros,0, 0,'L');
$pdf->Cell(30,5,"Nomina:",0, 0,'R');	
$pdf->Cell(40,5,muestrafloat($TotalNomina),0, 0,'R');	
$pdf->Cell(60,5,'',0, 0,'C');	

$pdf->Ln(15);
$pdf->Cell(175,5, 'Atentamente','', '','C');
$pdf->Ln(15);
$pdf->Cell(50,5, '','', '','C');
$pdf->Cell(100,5, 'Lic. Alejandro Camejo','T', '','C');
$pdf->Cell(50,5, '','', '','C');
$pdf->Ln(5);
$pdf->Cell(50,5, '','', '','C');
$pdf->Cell(100,5, 'Administrador',0, '','C');
$pdf->Cell(50,5, '','', '','C');


$pdf->Output();
?>
