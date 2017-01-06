<?
include("comun/ini.php");

set_time_limit(0);
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
			$q = "SELECT B.cont_nom,A.nom_fec_ini,A.nom_fec_fin FROM rrhh.nomina AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod WHERE A.cont_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			
			$q_empresa = "SELECT emp_nom FROM rrhh.empresa WHERE emp_cod=".$_GET['empresa'];
			$rE = $conn->Execute($q_empresa);
			
			$this->SetLeftMargin(5);
			$this->SetFont('Courier','',10);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,26,20);//logo a la izquierda 
			$this->SetXY(37, 7); 
			$textoCabecera = "Alcaldia del Municipio Maracaibo\n\n";
			$textoCabecera.= "Direccion de Admnistracion y Finanzas\n\n";
			$textoCabecera.= "oficina de Recursos Humanos\n\n";
			$this->MultiCell(100,5, $rE->fields['emp_nom'], 0, 'L');

			$this->Image ("images/logoa.jpg",170,4,26,20);//logo a la izquierda 
			$this->SetXY(150, 7); 
//			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',14);
			$this->Cell(0, 0, "Nomina de ".$rN->fields['cont_nom'],0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',12);
			$this->Cell(190, 0, "Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
			$this->Line(5, 37, 195, 37);
			//$this->Line(15, 42, 185, 42);
			$this->Ln(7);
	}

	function Footer()
	{
		$this->SetFont('Courier','I',12);
		//Número de página
		$this->Cell(190,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
} 
//Creación del objeto de la clase heredada
$pdf=new PDF('p','mm');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetFont('Courier','',12);
$q = "SELECT A.int_cod,A.dep_nom FROM rrhh.departamento as A INNER JOIN rrhh.division as B ON A.div_cod=B.int_cod  WHERE B.emp_cod=".$_SESSION['EmpresaL']." AND A.dep_estatus=0 ORDER BY dep_ord";
//die($q);
$rD = $conn->Execute($q);
$TotalNomina=0;
$Nro_trabajadores=0;
while(!$rD->EOF){
	$TotalDepatamento=0;
	//$q = "SELECT DISTINCT ON (C.int_cod) C.int_cod,A.tra_cod,C.tra_ced,C.tra_nom,C.tra_ape,tra_sueldo,C.tra_vac, tra_tipo  FROM ((rrhh.nom_tra_conc AS A INNER JOIN rrhh.nomina AS B ON A.nom_cod=B.int_cod) INNER JOIN rrhh.trabajador AS C ON A.tra_cod=C.int_cod)  WHERE B.cont_cod=".$_GET['id']. " AND C.dep_cod=".$rD->fields['int_cod']." ORDER BY C.int_cod";
	$q = "SELECT B.int_cod, C.tra_cod, B.tra_ced, B.tra_nom, B.tra_ape, B.tra_sueldo, B.tra_vac, B.tra_tipo FROM rrhh.trabajador AS B 
		INNER JOIN rrhh.cont_tra AS C 
		ON B.int_cod = C.tra_cod 
		WHERE B.dep_cod = ".$rD->fields['int_cod']."  AND C.cont_cod = ".$_GET['id']. "  AND (B.tra_estatus = 0 OR B.tra_estatus = 3)";
		$rT = $conn->Execute($q);
	if(!$rT->fields['tra_tipo']){
		$q = "SELECT  B.int_cod, C.tra_cod, B.tra_ced, B.tra_nom, B.tra_ape, B.tra_sueldo, B.tra_vac, B.tra_tipo, B.tra_fec_ing, B.tra_num_cta 
			FROM rrhh.dep_carg AS A
			INNER JOIN rrhh.trabajador AS B
			ON A.car_cod = B.car_cod 
			INNER JOIN rrhh.cont_tra AS C 
			ON B.int_cod = C.tra_cod
			WHERE A.dep_cod = ".$rD->fields['int_cod']."  AND B.dep_cod = ".$rD->fields['int_cod']."  AND C.cont_cod = ".$_GET['id']. "  AND (B.tra_estatus = 0 OR B.tra_estatus = 3) 
			ORDER BY A.orden";
			//die($q);
			$rT = $conn->Execute($q);
	}
	
	if(!$rT->EOF){
		while(!$rT->EOF){
			if($contadorPaginas==3){
				$pdf->AddPage();
				$contadorPaginas=0;
			}
			$desc= dividirStr('Departamento: '.utf8_decode($rD->fields['dep_nom']), intval(190/$pdf->GetStringWidth('M')));
			$pdf->Cell(190,5, $desc[0],0, 0,'L');	
			$pdf->Ln(5);
			for($i=1;$i<count($desc);$i++){
				$pdf->Cell(190,5,$desc[$i],0, 0,'L');
				$pdf->Ln(5);
			}
			if($rT->fields['tra_tipo']==1){
				$pdf->Cell(100,5,"Ciudadano: ".utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0, 0,'L');
				$Nro_trabajadores++;
			}
			else{
				$pdf->Cell(120,5,"Trabajador: ".utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0, 0,'L');	
				$Nro_trabajadores++;
			}
			if($rT->fields['tra_vac']!=1){	
				$pdf->Cell(70,5, "Cedula: ".utf8_decode($rT->fields['tra_ced']),0, 0,'L');	
			}
			

			$pdf->Ln(5);
			if($rT->fields['tra_tipo']==1){
				$q = "SELECT B.fun_nom  FROM rrhh.trabajador AS A JOIN rrhh.funciones AS B ON (A.fun_cod=B.int_cod)
					  WHERE A.int_cod = ".$rT->fields['tra_cod']." ";
					  //die($q);
					  $rA = $conn->Execute($q);
				$desc= dividirStr("Funcion: ".utf8_decode($rA->fields['fun_nom']), intval(190/$pdf->GetStringWidth('M')));
				}
			else{
				$q = "SELECT B.car_nom  FROM rrhh.trabajador AS A JOIN rrhh.cargo AS B ON (A.car_cod=B.int_cod)
					  WHERE A.int_cod = ".$rT->fields['tra_cod']." ";
					  $rA = $conn->Execute($q);
				$desc= dividirStr("Cargo: ".utf8_decode($rA->fields['car_nom']), intval(190/$pdf->GetStringWidth('M')));
			}
			$pdf->Cell(120,5,$desc[0],0, 0,'L');
			for($i=1;$i<count($desc);$i++)
			{
				$pdf->Cell(120,5,$desc[$i],0, 0,'L');
				$pdf->Ln(5);
			}
			if($rT->fields['tra_tipo']==1){
				$pdf->Cell(70,5,"Monto: ".muestrafloat($rT->fields['tra_sueldo']),0, 0,'L');
			}
			else{
				$pdf->Cell(70,5,"Sueldo Mensual: ".muestrafloat($rT->fields['tra_sueldo']),0, 0,'L');
			}		
			
			//------------------------------------------------------------------------------------------------------------------------------

			$pdf->Ln(5);
			$pdf->Cell(120,5, "Fecha de Ingreso: ".date("d/m/Y",strtotime($rT->fields['tra_fec_ing'])),0, 0,'L');
			$q = "SELECT B.descripcion FROM rrhh.trabajador AS A JOIN vehiculo.forma_pago AS B ON (A.tra_tip_pag=B.id)
					  WHERE A.int_cod = ".$rT->fields['tra_cod']." ";
					  //die($q);
			$rA = $conn->Execute($q);
			$pdf->Cell(70,5, "Forma de Pago: ".utf8_decode($rA->fields['descripcion']),0, 0,'L');			
			$pdf->Ln(5);
			$q = "SELECT B.descripcion FROM rrhh.trabajador AS A JOIN public.banco AS B ON (A.ban_cod=B.id)
					  WHERE A.int_cod = ".$rT->fields['tra_cod']." ";
					  //die($q);
			$rA = $conn->Execute($q);
			$pdf->Cell(120,5, "Banco: ".utf8_decode($rA->fields['descripcion']),0, 0,'L');	
			$pdf->Cell(70,5, "Cta.: ".utf8_decode($rT->fields['tra_num_cta']),0, 0,'L');
			
			//------------------------------------------------------------------------------------------------------------------------------
			$pdf->Ln(8);
			$q = "SELECT A.conc_desc, (CASE WHEN (C.conc_tipo=1) THEN (A.conc_val::numeric(20,2)*-1) ELSE (A.conc_val::numeric(20,2)) END) AS valor,C.conc_tipo, A.conc_aporte FROM (rrhh.nom_tra_conc AS A INNER JOIN rrhh.nomina AS B ON A.nom_cod=B.int_cod) INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE B.cont_cod=".$_GET['id']." AND A.conc_val<>0 AND A.tra_cod=".$rT->fields['tra_cod']."  ORDER BY C.conc_tipo, A.conc_cod";
			//die($q);
			$rC = $conn->Execute($q);
			$TotalTrabajador=0;
			$Asignacion=0;
			$Deduccion=0;
			$asig='';
			$deducc='';
			$Aportes=0;

			$pdf->SetFont('Courier','B',10);
			//$pdf->SetFillColor  (250,250,250);
			$pdf->SetDrawColor (255,255,255);
			$pdf->SetWidths(array(5,89,24,24,24,24));			
			$pdf->SetAligns(array('C','L','C','C','C','C'));
			$pdf->Row(array("","Concepto","Asignacion","Deduccion","Aportes Patronales","Neto"),11);
			
			while(!$rC->EOF) {
				if($rC->fields['valor']>=0.16)
				{
					if($rC->fields['conc_tipo']==0)
						$asig = muestrafloat($rC->fields['valor']);
					else
						$asig = '';
				}
				else
					$asig = '';
				if($rC->fields['valor']<=-0.1)
				{
					if ($rC->fields['conc_tipo']==1)
						$deducc = muestrafloat($rC->fields['valor']);
					else
						$deducc = '';
				}
				else
					$deducc = '';
				//Esto se hace para que no se asigne los dias de reintegro
				if($rC->fields['valor'] >= 0.16)	
					$Asignacion+= $rC->fields['conc_tipo']==0 ? $rC->fields['valor'] : 0;
				//Esto se hace para que no se descuente del presupuesto los dias no trabajados
				if($rC->fields['valor']<=-0.01)
					$Deduccion+=  $rC->fields['conc_tipo']==1 ? $rC->fields['valor'] : 0;
				if($rC->fields['valor']<=-0.01 || $rC->fields['valor']>0.16)
					$TotalTrabajador+=$rC->fields['valor'];
				
				$Aportes+=$rC->fields['conc_aporte'];
	
				$pdf->SetFont('Courier','',10);
				$pdf->SetWidths(array(5,89,24,24,24,24));				
				$pdf->SetAligns(array('C','L','R','R','R','R'));
				$pdf->Row(array("",$rC->fields['conc_desc'],$asig,$deducc,$rC->fields['conc_aporte']==0 ? '' : muestrafloat($rC->fields['conc_aporte']),""),11);
			
				$rC->movenext();
			}
			$TotalDepatamento+=$TotalTrabajador;
			$pdf->Ln(1);
			$pdf->SetFont('Courier','B',10);
			$pdf->SetX(99); 
			$pdf->SetDrawColor (0,0,0);
			$pdf->Cell(96,1,'','T', 0,'R');	
			$pdf->Ln(2); 
			$pdf->SetDrawColor (255,255,255);
			$pdf->SetWidths(array(5,89,24,24,24,24));					
			$pdf->SetAligns(array('C','L','R','R','R','R'));
			$pdf->Row(array("","",muestrafloat($Asignacion),muestrafloat($Deduccion),muestrafloat($Aportes),muestrafloat($TotalTrabajador)),11);
			$pdf->SetFont('Courier','',12);
			$pdf->Ln(5); 
			$contadorPaginas++;
			$rT->movenext(); 
		}
		//$pdf->AddPage();
		$pdf->SetDrawColor (0,0,0);
		$pdf->Cell(190,1,'','T', 0,'R');
		$pdf->Ln(6);
		$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte,A.conc_cod,C.conc_nom,C.conc_tipo FROM rrhh.nom_tra_conc AS A INNER JOIN rrhh.trabajador AS B ON A.tra_cod=B.int_cod INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod INNER JOIN rrhh.nomina AS D ON A.nom_cod=D.int_cod  WHERE B.dep_cod=".$rD->fields['int_cod']." AND D.cont_cod=".$_GET['id']. " GROUP BY A.conc_cod,C.conc_nom,C.conc_tipo ORDER BY C.conc_tipo,A.conc_cod,C.conc_nom";
		//die($q);
		$rCD = $conn->Execute($q);
		$pdf->SetFont('Courier','B',12);
		$desc= dividirStr('Departamento: '.utf8_decode($rD->fields['dep_nom']), intval(180/$pdf->GetStringWidth('M')));
		$pdf->Cell(190,0, $desc[0],0, 0,'L');	
		$pdf->Ln(5);
		for($i=1;$i<count($desc);$i++){
			$pdf->Cell(190,0,$desc[$i],0, 0,'L');
			$pdf->Ln(5);
		}
		$pdf->Ln(5); 
		$pdf->SetFont('Courier','B',10);
		$pdf->SetDrawColor (255,255,255);
		$pdf->SetWidths(array(88,34,34,34));						
		$pdf->SetAligns(array('L','C','C','C'));
		$pdf->Row(array("Conceptos","Asignaciones","Deducciones","Aportes"),11);
		$asignaciones=0;
		$deducciones=0;
		$aportes=0;
		while(!$rCD->EOF) {
			if($rCD->fields['valor']>0 || $rCD->fields['aporte']>0 ){
				$pdf->SetFont('Courier','',10);
				$pdf->SetDrawColor (255,255,255);
				$pdf->SetWidths(array(88,34,34,34));					
				$pdf->SetAligns(array('L','R','R','R'));
				$pdf->Row(array(utf8_decode($rCD->fields['conc_nom']), $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==0 ?  muestrafloat($rCD->fields['valor']) : '') : "",$rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==1 ?  muestrafloat($rCD->fields['valor']*-1) : '') : "",$rCD->fields['aporte']!=0 ? muestrafloat($rCD->fields['aporte']): ""),11);
				if($rCD->fields['valor']>=0.16)
					$asignaciones+= $rCD->fields['conc_tipo']==0 ? $rCD->fields['valor'] : 0;
				if($rCD->fields['valor']*-1<=-0.01)
					$deducciones+= $rCD->fields['conc_tipo']==1 ? $rCD->fields['valor'] : 0;
				$aportes+=$rCD->fields['aporte'];
			}
			$rCD->movenext();
		}
		$pdf->Ln(1);
		$pdf->SetFont('Courier','B',10);
		$pdf->SetX(99); 
		$pdf->SetDrawColor (0,0,0);
		$pdf->Cell(96,1,'','T', 0,'R');	
		$pdf->Ln(2); 
		$pdf->SetDrawColor (255,255,255);
		$pdf->SetWidths(array(88,34,34,34));					
		$pdf->SetAligns(array('R','R','R','R'));
		$pdf->Row(array("Totales: ",$aportes!=0 ? muestrafloat($aportes) : '',$asignaciones!=0 ? muestrafloat($asignaciones) : '',$deducciones!=0 ? muestrafloat($deducciones*-1) : ''),11);

		$pdf->Ln(7); 
		$TotalNomina+=$TotalDepatamento;
		$pdf->Cell(100,5, '' ,0, 0,'R');	
		$pdf->Cell(90,5, " Total Departamento: ".muestrafloat($TotalDepatamento) ,'T', 0,'R');	
		$pdf->SetFont('Courier','',12);
		$pdf->Ln(4);
		$contadorPaginas = 3;
	}
		$contadorPaginas++;
	$rD->movenext();
} 
$pdf->AddPage();
$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte,A.conc_cod,C.conc_nom,C.conc_tipo FROM rrhh.nom_tra_conc AS A INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod INNER JOIN rrhh.nomina AS D ON A.nom_cod=D.int_cod  WHERE D.cont_cod=".$_GET['id']. " GROUP BY A.conc_cod,C.conc_nom,C.conc_tipo ORDER BY C.conc_tipo,A.conc_cod,C.conc_nom ";
//die($q);
$rCD = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->SetDrawColor (255,255,255);
$pdf->SetWidths(array(88,34,34,34));						
$pdf->SetAligns(array('L','C','C','C'));
$pdf->Row(array("Resumen General:","Asignaciones","Deducciones","Aportes"),11);
$pdf->Ln(5); 
$asignaciones=0;
$deducciones=0;
$aportes=0;
while(!$rCD->EOF) {
	if($rCD->fields['valor']>0 || $rCD->fields['aporte']>0){
		$pdf->SetFont('Courier','',10);
		$pdf->SetDrawColor (255,255,255);
		$pdf->SetWidths(array(88,34,34,34));					
		$pdf->SetAligns(array('L','R','R','R'));
		$pdf->Row(array($rCD->fields['conc_nom'],  $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==0 ?  muestrafloat($rCD->fields['valor']) : '') : "",$rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==1 ?  muestrafloat($rCD->fields['valor']*-1) : '') : "",$rCD->fields['aporte']!=0 ? muestrafloat($rCD->fields['aporte']): ""),11);

		//Esto se hace para que no refleje la cantidad de dias de reintegro
		if($rCD->fields['valor']>0.1)	
			$asignaciones+= $rCD->fields['conc_tipo']==0 ? $rCD->fields['valor'] : 0;
		//Esto se hace para que no descuente la cantidad de dias de inasistencia como monto en el presupuesto
		if($rCD->fields['valor']>0.1)	
			$deducciones+= $rCD->fields['conc_tipo']==1 ? $rCD->fields['valor'] : 0;
		$aportes+=$rCD->fields['aporte'];
	}
	$rCD->movenext();
}
$pdf->Ln(1);
$pdf->SetFont('Courier','B',10);
$pdf->SetX(99); 
$pdf->SetDrawColor (0,0,0);
$pdf->Cell(96,1,'','T', 0,'R');	
$pdf->Ln(2); 
$pdf->SetDrawColor (255,255,255);
$pdf->SetWidths(array(88,34,34,34));					
$pdf->SetAligns(array('R','R','R','R'));
$pdf->Row(array("Totales: ",$aportes!=0 ? muestrafloat($aportes) : '',$asignaciones!=0 ? muestrafloat($asignaciones) : '',$deducciones!=0 ? muestrafloat($deducciones*-1) : ''),11);

$pdf->Ln(7); 
$pdf->Cell(190,7, "Numero de Trabajadores: ".$Nro_trabajadores  ,'T', 0,'R');	
$pdf->Ln(7); 
$pdf->Cell(190,7, "Total Nomina: ".muestrafloat($TotalNomina) ,'T', 0,'R');	
$pdf->Ln(4);

$pdf->Output();
?>
