<?
include("comun/ini.php");
include("Constantes.php");
set_time_limit(0);
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
class PDF extends FPDF{
//Cabecera de página
	function Header(){

			$conn = $_SESSION['conex'];
			$q = "SELECT cont_nom,nom_fec_ini,nom_fec_fin,cont_cod FROM rrhh.historial_nom WHERE int_cod=".$_GET['id'];
			$rN = $conn->Execute($q);
			
			$this->SetLeftMargin(5);
			$this->SetFont('Courier','',10);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,26,20);//logo a la izquierda 
			$this->SetXY(37, 7); 
			$textoCabecera = ENTE."\n\n";
			$textoCabecera.= ORGANISMO_NOMBRE."\n\n";
			$textoCabecera.= "Oficina de Recursos Humanos\n\n";
			$this->MultiCell(100,2, $textoCabecera, 0, 'L');

			$this->Image ("images/logoa.jpg",170,4,26,20);//logo a la izquierda 
			$this->SetXY(150, 7); 
			//$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',14);
			$this->Cell(0, 0, $rN->fields['cont_nom'],0,0,'C');
			$this->Ln(6);
			$this->SetFont('Courier','B',12);
			if(($rN->fields['cont_cod'] == 7) || ($rN->fields['cont_cod'] == 9)){
				$this->Cell(190, 0, "Periodo: 2006 - 2007",0,0,'R');
			} else {
				$this->Cell(190, 0, "Periodo: ".muestrafecha($rN->fields['nom_fec_ini'])." a ".muestrafecha($rN->fields['nom_fec_fin']),0,0,'R');
			}
			$this->Line(5, 37, 195, 37);
			//$this->Line(15, 42, 185, 42);
			$this->Ln(7);
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
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetFont('Courier','',12);
//$q = "SELECT A.int_cod,A.dep_nom FROM rrhh.departamento as A INNER JOIN rrhh.division as B ON A.div_cod=B.int_cod  WHERE B.emp_cod=".$_SESSION['EmpresaL']." AND A.dep_estatus=0 ORDER BY A.dep_ord";
$q = "SELECT DISTINCT dep_cod,dep_nom,dep_ord FROM rrhh.hist_nom_tra_sueldo WHERE hnom_cod=".$_GET['id']." ORDER BY dep_ord";
//die($q);
$rD = $conn->Execute($q);
$TotalNomina=0;

while(!$rD->EOF){
	$TotalDepatamento=0;
	if($_GET['id'] < 178 ){ //Esto se hizo para optimizacion de reporte a partir del 28/01/2008 debe eliminarse y tomar solo else para futuros proyectos
		$q = "SELECT DISTINCT ON (B.tra_cod) B.tra_cod,A.tra_ced,B.tra_nom,A.tra_sueldo,A.car_nom, A.fun_nom, A.tra_vac, A.tra_tipo FROM rrhh.hist_nom_tra_sueldo AS A INNER JOIN rrhh.hist_nom_tra_conc AS B ON A.tra_cod=B.tra_cod WHERE A.hnom_cod=".$_GET['id']." 
		AND B.hnom_cod= ".$_GET['id']." AND A.dep_cod=".$rD->fields['dep_cod']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) ORDER BY B.tra_cod";
		}
	else{
		$q = "SELECT  A.tra_tipo 
		FROM rrhh.hist_nom_tra_sueldo AS A
		WHERE A.hnom_cod=".$_GET['id']." LIMIT 1";
		$rT = $conn->Execute($q);
		if($rT->fields['tra_tipo']){
			$q = "SELECT A.tra_cod,A.tra_ced,B.tra_nom,B.tra_ape,A.tra_sueldo,A.car_nom, A.fun_nom, A.tra_vac, A.tra_tipo 
			FROM rrhh.hist_nom_tra_sueldo AS A 
			INNER JOIN rrhh.trabajador AS B 
			ON A.tra_cod=B.int_cod 
			WHERE A.hnom_cod=".$_GET['id']." AND A.dep_cod=".$rD->fields['dep_cod']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 )";
		}
		else{
			$q = "SELECT A.tra_cod,A.tra_ced,B.tra_nom,B.tra_ape,A.tra_sueldo,A.car_nom, A.fun_nom, A.tra_vac, A.tra_tipo 
			FROM rrhh.hist_nom_tra_sueldo AS A 
			INNER JOIN rrhh.trabajador AS B 
			ON A.tra_cod=B.int_cod 
			INNER JOIN rrhh.dep_carg AS C 
			ON B.car_cod=C.car_cod 
			WHERE A.hnom_cod=".$_GET['id']." AND A.dep_cod=".$rD->fields['dep_cod']." AND C.dep_cod=".$rD->fields['dep_cod']." AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 )
			ORDER BY C.orden";
		}
	}
	//die($q);
	$rT = $conn->Execute($q);
	if(!$rT->EOF){
		while(!$rT->EOF){
			if($contadorPaginas==3 || $contadorConceptos >= 9){
				$pdf->AddPage();
				$contadorPaginas=0;
				$contadorConceptos=0;
			}
			$desc= dividirStr('Departamento: '.utf8_decode($rD->fields['dep_nom']), intval(190/$pdf->GetStringWidth('M')));
			$pdf->Cell(190,5, $desc[0],0, 0,'L');	
			$pdf->Ln(5);
			for($i=1;$i<count($desc);$i++){
				$pdf->Cell(190,5,$desc[$i],0, 0,'L');
				$pdf->Ln(5);
			}
			if($rT->fields['tra_tipo']==1){
				$pdf->Cell(190,5,"Ciudadano: ".utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0, 0,'L');	
			}
			else{
				$pdf->Cell(190,5,"Trabajador: ".utf8_decode($rT->fields['tra_nom'])." ".utf8_decode($rT->fields['tra_ape']),0, 0,'L');	
			}
			if($rT->fields['tra_vac']!=1){	
				$pdf->Ln(5);
				$pdf->Cell(190,5, "Cedula: ".utf8_decode($rT->fields['tra_ced']),0, 0,'L');	
			}
			$pdf->Ln(5);
			$desc= dividirStr( $rT->fields['tra_tipo']==1 ? "Funcion: ".utf8_decode($rT->fields['fun_nom']) :"Cargo: ".utf8_decode($rT->fields['car_nom']), intval(190/$pdf->GetStringWidth('M')));
			$pdf->Cell(190,5,$desc[0],0, 0,'L');
			$pdf->Ln(5);
			for($i=1;$i<count($desc);$i++){
				$pdf->Cell(190,5,$desc[$i],0, 0,'L');
				$pdf->Ln(5);
			}
			$pdf->Cell(190,5,$rT->fields['tra_tipo']==1 ? "Monto: ".muestrafloat($rT->fields['tra_sueldo']): "Sueldo Mensual: ".muestrafloat($rT->fields['tra_sueldo']),0, 0,'L');	
			$pdf->Ln(8);
			$q = "SELECT conc_desc, (CASE WHEN (conc_tipo=1) THEN (conc_val::numeric(20,2)*-1) ELSE (conc_val::numeric(20,2)) END) AS valor,conc_tipo FROM rrhh.hist_nom_tra_conc WHERE hnom_cod=".$_GET['id']." AND conc_val<>0 AND tra_cod=".$rT->fields['tra_cod']." AND (conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) ORDER BY conc_tipo, conc_cod";
			$rC = $conn->Execute($q);
			//die($q);
			$TotalTrabajador=0;
			$Asignacion=0;
			$Deduccion=0;
			$pdf->SetFont('Courier','B',12);
			$pdf->Cell(10,5,'',0, 0,'R');	
			$pdf->Cell(60,5,"Concepto",0, 0,'L');	
			$pdf->Cell(40,5,"Asignacion",0, 0,'R');	
			$pdf->Cell(40,5,"Deduccion",0, 0,'R');	
			$pdf->Cell(40,5,"Neto",0, 0,'R');	
			$pdf->SetFont('Courier','',12);
			$pdf->Ln(7); 
			while(!$rC->EOF) {
				$pdf->Cell(10,5,'',0, 0,'R');	
				$pdf->Cell(60,5,utf8_decode($rC->fields['conc_desc']),0, 0,'L');	
				$pdf->Cell(40,5,$rC->fields['conc_tipo']==0 ? muestrafloat($rC->fields['valor']) : '',0, 0,'R');	
				$pdf->Cell(40,5,$rC->fields['conc_tipo']==1 ? muestrafloat($rC->fields['valor']) : '',0, 0,'R');	
				$pdf->Cell(40,5,'',0, 0,'R');
					
				//Esto se hace para que no se asigne los dias de reintegro
				if($rC->fields['valor'] > 0.15)	
					$Asignacion+= $rC->fields['conc_tipo']==0 ? $rC->fields['valor'] : 0;
				//Esto se hace para que no se descuente del presupuesto los dias no trabajados
				if($rC->fields['valor']<=-0.11)
					$Deduccion+=  $rC->fields['conc_tipo']==1 ? $rC->fields['valor'] : 0;
				if($rC->fields['valor']<=-0.11 || $rC->fields['valor']>0.15)
					$TotalTrabajador+=$rC->fields['valor'];
					
				$pdf->Ln(5); 
				$contadorConceptos++;
				$rC->movenext();
			}
			$TotalDepatamento+=$TotalTrabajador;
			//die( $TotalDepatamento);
			$pdf->SetFont('Courier','B',12);
			$pdf->Cell(70,5,'',0, 0,'R');	
			$pdf->Cell(40,5,muestrafloat($Asignacion),'T', 0,'R');	
			$pdf->Cell(40,5,muestrafloat($Deduccion),'T', 0,'R');	
			$pdf->Cell(40,5,muestrafloat($TotalTrabajador),'T', 0,'R');	
			$pdf->SetFont('Courier','',12);
			$pdf->Ln(5); 
			$pdf->Cell(190,5,'','B', 0,'L');	
			$pdf->Ln(8);
			$contadorPaginas++;
			$rT->movenext(); 
		}
		$pdf->AddPage();
		$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte,A.conc_cod,C.conc_nom,C.conc_tipo FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.hist_nom_tra_sueldo AS B ON A.tra_cod=B.tra_cod INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod AND A.hnom_cod=B.hnom_cod WHERE B.dep_cod=".$rD->fields['dep_cod']." AND A.hnom_cod=".$_GET['id']. " AND (A.conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) GROUP BY A.conc_cod,C.conc_nom,C.conc_tipo ORDER BY C.conc_tipo,A.conc_cod,C.conc_nom ";
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
		$pdf->Cell(80,0,"Conceptos",0, 0,'L');	
		$pdf->Cell(30,0,"Aportes",0, 0,'R');	
		$pdf->Cell(40,0,"Asignaciones",0, 0,'R');	
		$pdf->Cell(40,0,"Deducciones",0, 0,'R');	
		$pdf->Ln(5); 
		$asignaciones=0;
		$deducciones=0;
		$aportes=0;
		while(!$rCD->EOF) {
			if($rCD->fields['valor']>0 || $rCD->fields['aporte']>0){
				$pdf->Cell(80,0, utf8_decode($rCD->fields['conc_nom']),0, 0,'L');	
				$pdf->Cell(30,0, $rCD->fields['aporte']!=0 ? muestrafloat($rCD->fields['aporte']): "",0, 0,'R');	
				$pdf->Cell(40,0, $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==0 ?  muestrafloat($rCD->fields['valor']) : '') : "",0, 0,'R');	
				$pdf->Cell(40,0, $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==1 ?  muestrafloat($rCD->fields['valor']*-1) : '') : "",0, 0,'R');	
				$pdf->Ln(5); 
				$deducciones+= $rCD->fields['conc_tipo']==1 ? $rCD->fields['valor'] : 0;
				$asignaciones+= $rCD->fields['conc_tipo']==0 ? $rCD->fields['valor'] : 0;
				$aportes+=$rCD->fields['aporte'];
			}	
			$rCD->movenext();
		}
		$pdf->Ln(2);
		$pdf->Cell(70,5,"Totales: ",0, 0,'R');	
		$pdf->Cell(40,5,$aportes!=0 ? muestrafloat($aportes) : '','T', 0,'R');	
		$pdf->Cell(40,5,$asignaciones!=0 ? muestrafloat($asignaciones) : '','T', 0,'R');	
		$pdf->Cell(40,5,$deducciones!=0 ? muestrafloat($deducciones*-1) : '','T', 0,'R');	
		$pdf->Ln(7); 
		$TotalNomina+=$TotalDepatamento;
		$pdf->Cell(100,3, '' ,0, 0,'R');	
		$pdf->Cell(90,5, " Total Departamento: ".muestrafloat($TotalDepatamento) ,'T', 0,'R');	
		$pdf->SetFont('Courier','',12);
		$pdf->Ln(4);
		$contadorPaginas = 3;
	}
	$rD->movenext();
} 
$pdf->AddPage();
$q = "SELECT sum(A.conc_val::numeric(20,2)) AS valor,sum(A.conc_aporte::numeric(20,2)) AS aporte,A.conc_cod,C.conc_nom,C.conc_tipo FROM rrhh.hist_nom_tra_conc AS A INNER JOIN rrhh.concepto AS C ON A.conc_cod=C.int_cod WHERE A.hnom_cod=".$_GET['id']. " AND (A.conc_cod=".$_GET['Conc']." OR ".$_GET['Conc']."=-1 ) AND (A.tra_cod=".$_GET['Tra']." OR ".$_GET['Tra']."=-1 ) GROUP BY A.conc_cod,C.conc_nom,C.conc_tipo ORDER BY C.conc_tipo,A.conc_cod,C.conc_nom";
//die($q);
$rCD = $conn->Execute($q);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(80,0,"Resumen General:",0, 0,'L');	
$pdf->Cell(30,0,"Aportes",0, 0,'R');	
$pdf->Cell(40,0,"Asignaciones",0, 0,'R');	
$pdf->Cell(40,0,"Deducciones",0, 0,'R');	
$pdf->Ln(5); 
$asignaciones=0;
$deducciones=0;
$aportes=0;
while(!$rCD->EOF) {
	if($rCD->fields['valor']>0 || $rCD->fields['aporte']>0){
		$pdf->Cell(80,0, $rCD->fields['conc_nom'],0, 0,'L');	
		$pdf->Cell(30,0, $rCD->fields['aporte']!=0 ? muestrafloat($rCD->fields['aporte']): "",0, 0,'R');	
		$pdf->Cell(40,0, $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==0 ?  muestrafloat($rCD->fields['valor']) : '') : "",0, 0,'R');	
		$pdf->Cell(40,0, $rCD->fields['valor']!=0 ? ($rCD->fields['conc_tipo']==1 ?  muestrafloat($rCD->fields['valor']*-1) : '') : "",0, 0,'R');	
		$pdf->Ln(5); 
		//Esto se hace para que no refleje la cantidad de dias de reintegro
		if($rCD->fields['valor']>0.15)	
		$asignaciones+= $rCD->fields['conc_tipo']==0 ? $rCD->fields['valor'] : 0;
		//Esto se hace para que no descuente la cantidad de dias de inasistencia como monto en el presupuesto
		if($rCD->fields['valor']>0.15)	
			$deducciones+= $rCD->fields['conc_tipo']==1 ? $rCD->fields['valor'] : 0;
		$aportes+=$rCD->fields['aporte'];
	}
	$rCD->movenext();
}
$pdf->Ln(2);
$pdf->Cell(70,5,"Totales: ",0, 0,'R');	
$pdf->Cell(40,5,$aportes!=0 ? muestrafloat($aportes) : '','T', 0,'R');	
$pdf->Cell(40,5,$asignaciones!=0 ? muestrafloat($asignaciones) : '','T', 0,'R');	
$pdf->Cell(40,5,$deducciones!=0 ? muestrafloat($deducciones*-1) : '','T', 0,'R');	
$pdf->Ln(7); 
$pdf->Cell(190,7, "Total Nomina: ".muestrafloat($TotalNomina) ,'T', 0,'R');	
$pdf->Output();
?>
