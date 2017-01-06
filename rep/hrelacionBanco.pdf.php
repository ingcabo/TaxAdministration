<?
include("comun/ini.php");
$_SESSION['conex'] = $conn;
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
class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{

			$conn = $_SESSION['conex'];
			$q = "SELECT B.cont_nom,A.nom_fec_ini,A.nom_fec_fin FROM rrhh.historial_nom AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod WHERE A.int_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			$q = "SELECT C.emp_nom, C.emp_rif, C.emp_dir, C.emp_telf FROM rrhh.empresa AS C WHERE C.int_cod=".$_SESSION['EmpresaL'];
			$rC = $conn->Execute($q);
			$fecha =split("-",$rN->fields['nom_fec_fin']);
			$dia=$fecha[2]-1;
			$fecha=$dia."/".$fecha[1]."/".$fecha[0];
			$this->SetLeftMargin(5);
			$this->SetRightMargin(5);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,26);//logo a la izquierda 
			$this->Ln(8);
			$this->SetFont('Times','',10);
			$this->Cell(150,2,utf8_decode($rC->fields['emp_nom']), 0, 'L');
			$this->Ln(5);
			$this->Cell(150,2,"UNIDAD DE NOMINA", 0, 'L');
			$this->SetXY(165, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			$this->SetXY(165, 12); 
			$this->MultiCell(50,2, "Hora: ".date('h:i:s'), 0, 'L');
			$this->SetXY(165, 17); 
			$this->MultiCell(50,2, utf8_decode('Página: ').$this->PageNo().'/{nb}', 0, 'L');
			
			$this->Ln(15);
			$this->Cell(0, 0, utf8_decode("RELACION DE PAGOS POR BANCO (DEPOSITOS)"),0,0,'C');
			$this->Ln(5);
			$this->Cell(200, 0, "desde: ".muestrafecha($rN->fields['nom_fec_ini'])." al ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
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
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(5);
$pdf->SetFont('Arial','',12);
$TotalRegistros=0;

$pdf->Cell(25,5,"Banco",1, 0,'C');	
$pdf->Cell(45,5,"Nro Cuenta",1, 0,'C');
$pdf->Cell(20,5,utf8_decode('Cédula'),1, 0,'C');	
$pdf->Cell(90,5,"Nombre y Apellido",1, 0,'C');	
$pdf->Cell(20,5,"Monto",1, 0,'C');		
$pdf->Ln(5);
$q = "SELECT DISTINCT A.tra_cod,B.tra_nom,B.tra_ape,B.tra_ced,B.tra_num_cta,D.nombre_corto AS descripcion FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod INNER JOIN rrhh.historial_nom as C ON A.hnom_cod=C.int_cod INNER JOIN public.banco AS D ON B.ban_cod=D.id WHERE C.int_cod=".$_GET['id']." AND B.tra_tip_pag=2 AND B.ban_cod =".$_GET['Banco']." ORDER BY B.tra_nom";
//die($q);
$rT = $conn->Execute($q);
$TotalDeposito=0;
$pdf->SetFont('Arial','',10);
$descripcion = $rT->fields['descripcion'];
while(!$rT->EOF){
	$nom = dividirStr($rT->fields['tra_nom']." ".$rT->fields['tra_ape'], intval(110/$pdf->GetStringWidth('M')));
	$ban = dividirStr($rT->fields['descripcion'], intval(25/$pdf->GetStringWidth('M')));
	$border= (count($nom)>1 || count($ban)>1) ? 0 : 'B';
	$x = count($nom) > count($ban) ? count($nom) : count($ban);
	
	$pdf->Line(5, $pdf->GetY(), 5, $pdf->GetY()+5);
	$pdf->Cell(25,5, utf8_decode($ban[0]),$border, 0,'L');	
	$pdf->Line(30, $pdf->GetY(), 30, $pdf->GetY()+5);
	$pdf->Cell(45,5, utf8_decode($rT->fields['tra_num_cta']),$border, 0,'C');	
	$pdf->Line(75, $pdf->GetY(), 75, $pdf->GetY()+5);
	$q = "SELECT A.conc_desc, A.int_cod,(CASE WHEN (A.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.historial_nom AS B ON A.hnom_cod=B.int_cod WHERE B.int_cod=".$_GET['id']." AND A.conc_val<>0 AND A.tra_cod=".$rT->fields['tra_cod']." ORDER BY A.conc_tipo, A.conc_cod";
	//die($q);
	$rC = $conn->Execute($q);
	$TotalTrabajador=0;
	while(!$rC->EOF) {
		if($rC->fields['valor']<= -15 || $rC->fields['valor']>= 15)
			$TotalTrabajador+=$rC->fields['valor'];
		$rC->movenext();
	}
	$TotalDeposito+=$TotalTrabajador;
	$TotalRegistros++;

	$pdf->Cell(20,5, utf8_decode($rT->fields['tra_ced']),$border, 0,'R');	
	$pdf->Line(95, $pdf->GetY(), 95, $pdf->GetY()+5);	
	$pdf->Cell(90,5, utf8_decode($nom[0]),$border, 0,'L');	
	$pdf->Line(185, $pdf->GetY(), 185, $pdf->GetY()+5);
	$pdf->Cell(20,5, muestrafloat($TotalTrabajador),$border, 0,'R');	
	$pdf->Line(205, $pdf->GetY(), 205, $pdf->GetY()+5);	
	$pdf->Ln(5);
	for($i=1;$i<$x;$i++){
		$border= $i+1==$x ? 'B' : 0;
		$pdf->Line(5, $pdf->GetY(), 5, $pdf->GetY()+5);
		$pdf->Cell(25,5, $ban[$i] ? utf8_decode($ban[$i]) : '',$border, 0,'L');	
		$pdf->Line(30, $pdf->GetY(), 30, $pdf->GetY()+5);
		$pdf->Cell(45,5, '',$border, 0,'C');		
		$pdf->Line(75, $pdf->GetY(), 75, $pdf->GetY()+5);
		$pdf->Cell(20,5, '',$border, 0,'R');
		$pdf->Line(95, $pdf->GetY(), 95, $pdf->GetY()+5);
		$pdf->Cell(90,5, $nom[$i] ? utf8_decode($nom[$i]) : '',$border, 0,'L');				
		$pdf->Line(185, $pdf->GetY(), 185, $pdf->GetY()+5);
		$pdf->Cell(20,5, '',$border, 0,'R');	
		$pdf->Line(205, $pdf->GetY(), 205, $pdf->GetY()+5);	
		$pdf->Ln(5);
	}
	$rT->movenext();
}
$pdf->Ln(5); 
$pdf->SetFont('Arial','I',10);
$pdf->Cell(180,5,"SUBTOTAL EN: ".$descripcion." Nro. DE TRABAJADORES ".$rT->RecordCount(),0, 0,'R');	
$pdf->Cell(20,5, muestrafloat($TotalDeposito),0, 0,'R');
$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(180,5,"TOTAL RELACION Nro. DE TRABAJADORES ".$rT->RecordCount(),0, 0,'R');	
$pdf->Cell(20,5, muestrafloat($TotalDeposito),0, 0,'R');

$pdf->Output();
?>
