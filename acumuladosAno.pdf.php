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

			$this->SetXY(255, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',14);
			$this->Cell(0, 0, "Conceptos Acumulados ",0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',8);
			$this->Cell(270, 0, "Periodo: ".$_GET['Periodo'],0,0,'R');
			$this->Line(15, 37, 294, 37);
			$this->SetFont('Courier','B',12);
			$this->Ln(5);
			$this->Cell(70,0,"Trabajador",0, 0,'L');
			$this->Ln(5);
			$this->Cell(5,0,"",0, 0,'L');	
			$this->Cell(30,0,"Concepto",0, 0,'L');	
			$this->Ln(5);
			$this->Cell(5,0,"",0, 0,'L');	
			$this->Cell(21,0,"Ene",0, 0,'C');	
			$this->Cell(21,0,"Feb",0, 0,'C');	
			$this->Cell(21,0,"Mar",0, 0,'C');	
			$this->Cell(21,0,"Abr",0, 0,'C');	
			$this->Cell(21,0,"May",0, 0,'C');	
			$this->Cell(21,0,"Jun",0, 0,'C');	
			$this->Cell(21,0,"Jul",0, 0,'C');	
			$this->Cell(21,0,"Ago",0, 0,'C');	
			$this->Cell(21,0,"Sep",0, 0,'C');	
			$this->Cell(21,0,"Oct",0, 0,'C');	
			$this->Cell(21,0,"Nov",0, 0,'C');	
			$this->Cell(21,0,"Dic",0, 0,'C');	
			$this->Cell(21,0,"Total",0, 0,'C');	
			$this->Line(15, 52, 294, 52);
			$this->Ln(5);
			
	}

	function Footer()
	{
		
		$this->SetFont('Arial','I',8);
		//Número de página
		$this->Cell(265,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF('l');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(15);
$pdf->SetFont('Courier','',8);
$q = "SELECT DISTINCT A.tra_cod,C.tra_nom,C.tra_ape,C.tra_fec_ing,E.div_nom FROM (((rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.trabajador AS C ON A.tra_cod=C.int_cod) INNER JOIN rrhh.departamento AS D ON C.dep_cod=D.int_cod) INNER JOIN rrhh.division AS E ON D.div_cod=E.int_cod WHERE substr(B.periodo,4,4)='".$_GET['Periodo']."' AND C.tra_estatus!='4' AND C.tra_estatus!='5' AND (D.div_cod=".$_GET['Division']." OR ".$_GET['Division']."=-1) AND E.emp_cod=".$_SESSION['EmpresaL']."  ORDER BY tra_nom";
$rT = $conn->Execute($q);
$TotalPagos=0;
while(!$rT->EOF){
		$q = "SELECT DISTINCT C.int_cod,C.conc_nom FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE A.tra_cod=".$rT->fields['tra_cod']." ORDER BY C.conc_nom";
		$rC = $conn->Execute($q);
		if(!$rC->EOF){
			$pdf->SetFont('Courier','B',10);
			$pdf->Cell(80,5,utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),'B', 0,'L');	
			$pdf->SetFont('Courier','',8);
			$pdf->Cell(60,5,"Fecha de Ingreso: ".muestrafecha($rT->fields['tra_fec_ing']),'B', 0,'L');	
			$pdf->Cell(60,5,"Division: ".$rT->fields['div_nom'],'B', 0,'L');	
			//PAGOS
			$q = "SELECT B.conc_val::numeric(20,2),C.conc_nom,A.fecha FROM (rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod) INNER JOIN rrhh.concepto AS C ON B.conc_cod=C.int_cod WHERE A.tra_cod=".$rT->fields['tra_cod']." AND to_char(A.fecha,'yyyy')='".$_GET['Periodo']."'";
			$rP = $conn->Execute($q);
			$pdf->SetFont('Courier','B',8);
			$TotalPagosTrabajador=0;
			while(!$rP->EOF){
				$TotalPagosTrabajador+=calculafloat($rP->fields['conc_val'],2);
				$TotalPagos+=calculafloat($rP->fields['conc_val'],2);
				$pdf->Ln(5);
				$pdf->Cell(140,5,"Pago-Antipipo: ".muestrafloat($rP->fields['conc_val'])." Bs sobre ".$rP->fields['conc_nom'].", fecha: ".muestrafecha($rP->fields['fecha']),'B', 0,'L');	
				$rP->movenext();
			}

			for($i=1;$i<=12;$i++){
				$TotalTrabajadorConceptos[$i]=0;
			}
			$j=1;
			while(!$rC->EOF) {
				//VERIFICO QUE EL MONTO TOTAL SEA DISTINTO DE 0 (cero) PARA NO IMPRIMIR LA FILA
				$q = "SELECT sum(A.conc_val::numeric(20,2)) AS conc_val FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE substr(B.periodo,4,4)='".$_GET['Periodo']."' AND A.conc_cod=".$rC->fields['int_cod']." AND A.tra_cod=".$rT->fields['tra_cod']." GROUP BY A.conc_cod ORDER BY A.conc_cod";
				$rAux = $conn->Execute($q);
				if(!$rAux->EOF && $rAux->fields['conc_val']!=0){
					$pdf->SetFont('Courier','B',8);
					$pdf->Ln(5);
					$pdf->Cell(5,0, '',0, 0,'L');	
					$pdf->Cell(30,5, $rC->fields['conc_nom'],'B', 0,'L');	
					$pdf->Ln(5);
					$pdf->Cell(5,0, '',0, 0,'L');	
					$TotalConcepto=0;
					for($i=1;$i<=12;$i++){
						$Periodo= strlen($i)< 2 ? "0".$i."/".$_GET['Periodo'] : $i."/".$_GET['Periodo'];
						$pdf->SetFont('Courier','',7);
						$q = "SELECT A.conc_val::numeric(20,2) FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE B.periodo='".$Periodo."' AND A.conc_cod=".$rC->fields['int_cod']." AND A.tra_cod=".$rT->fields['tra_cod']." ORDER BY A.conc_cod";
						$rV = $conn->Execute($q);
						$Valor= $rV->EOF ? '0,00' : muestrafloat($rV->fields['conc_val']);
						$ValorF= $rV->EOF ? 0 : $rV->fields['conc_val'];
						$pdf->Cell(21,5,  $Valor,1, 0,'R');	
						$TotalTrabajadorConceptos[$i]+=calculafloat($ValorF,2);
						$TotalConcepto+=calculafloat($ValorF,2);
						$j++;
					}
					
					$pdf->Cell(21,5,  muestrafloat($TotalConcepto),1, 0,'R');	
					$pdf->Ln(5);
				}
				$rC->movenext();
			}
			$pdf->SetFont('Courier','B',8);
			$pdf->Ln(5);
			$pdf->Cell(5,0, '',0, 0,'L');	
			$pdf->Cell(30,5, "Total Mes",'B', 0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(5,0, '',0, 0,'L');	
			$TotalConcepto=0;
			for($i=1;$i<=12;$i++){
				$pdf->SetFont('Courier','',7);
				$pdf->Cell(21,5,  muestrafloat($TotalTrabajadorConceptos[$i]),1, 0,'R');	
				$TotalConcepto+=$TotalTrabajadorConceptos[$i];
			}
			$pdf->SetFont('Courier','B',7);
			$pdf->Cell(21,5,  muestrafloat($TotalConcepto-$TotalPagosTrabajador),1, 0,'R');	
			$pdf->Ln(7); 
			$pdf->Cell(280,0, '' ,'B', 0,'L');	
			$pdf->Ln(4); 
		}
		$rT->movenext();
} 
//TOTALESSSSSS
$q = "SELECT DISTINCT C.int_cod,C.conc_nom FROM (rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod ORDER BY C.conc_nom";
$rC = $conn->Execute($q);
if(!$rC->EOF){
	$pdf->SetFont('Courier','B',10);
	$pdf->Cell(90,5,"Total",'B', 0,'L');	
	for($i=1;$i<=12;$i++){
		$TotalTrabajadorConceptos[$i]=0;
	}
	$j=1;
	while(!$rC->EOF) {
		$pdf->SetFont('Courier','B',8);
		$pdf->Ln(5);
		$pdf->Cell(5,0, '',0, 0,'L');
		$pdf->Cell(30,5, $rC->fields['conc_nom'],'B', 0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(5,0, '',0, 0,'L');	
		$TotalConcepto=0;
		for($i=1;$i<=12;$i++){
			$pdf->SetFont('Courier','',7);
			$Periodo= strlen($i)< 2 ? "0".$i."/".$_GET['Periodo'] : $i."/".$_GET['Periodo'];
			$q = "SELECT SUM(A.conc_val::numeric(20,2)) AS conc_val  FROM ((((rrhh.acum_tra_conc AS A INNER JOIN rrhh.acumulado AS B ON A.acum_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod) INNER JOIN rrhh.trabajador AS D ON A.tra_cod=D.int_cod) INNER JOIN rrhh.departamento AS E ON D.dep_cod=E.int_cod) INNER JOIN rrhh.division AS F ON E.div_cod=F.int_cod WHERE B.periodo='".$Periodo."' AND A.conc_cod=".$rC->fields['int_cod']." AND (E.div_cod=".$_GET['Division']." OR ".$_GET['Division']."=-1) AND F.emp_cod=".$_SESSION['EmpresaL']." GROUP BY A.conc_cod ORDER BY A.conc_cod";
			$rV = $conn->Execute($q);
			$Valor= $rV->EOF ? '0,00' : muestrafloat($rV->fields['conc_val']);
			$ValorF= $rV->EOF ? 0 : $rV->fields['conc_val'];
			$pdf->Cell(21,5,  $Valor,1, 0,'R');	
			$TotalTrabajadorConceptos[$i]+=calculafloat($ValorF,2);
			$TotalConcepto+=calculafloat($ValorF,2);
			$j++;
		}
		$pdf->Cell(21,5,  muestrafloat($TotalConcepto),1, 0,'R');	
		$pdf->Ln(5);
		$rC->movenext();
	}
	$pdf->SetFont('Courier','B',8);
	$pdf->Ln(5);
	$pdf->Cell(5,0, '',0, 0,'L');	
	$pdf->Cell(30,5, "Total",'B', 0,'L');	
	$pdf->Ln(5);
	$pdf->Cell(5,0, '',0, 0,'L');	
	$TotalConcepto=0;
	for($i=1;$i<=12;$i++){
		$pdf->SetFont('Courier','',7);
		$pdf->Cell(21,5,  muestrafloat($TotalTrabajadorConceptos[$i]),1, 0,'R');	
		$TotalConcepto+=$TotalTrabajadorConceptos[$i];
	}
	$pdf->SetFont('Courier','B',7);
	$pdf->Cell(21,5,  muestrafloat($TotalConcepto-$TotalPagos),1, 0,'R');	
	$pdf->Ln(8); 
	$pdf->Cell(280,0, '' ,'B', 0,'L');	
	$pdf->Ln(5); 
}
$pdf->Output();
?>
