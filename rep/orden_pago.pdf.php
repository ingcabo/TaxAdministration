<?php
	include("comun/ini.php");
	include("Constantes.php");
	set_time_limit(0);
	
	function dividirStr($str, $max)
	{
		$strArray = array();
		do
		{
			if (strlen($str) > $max)
				$posF = strrpos( substr($str, 0, $max), ' ' );
			else
				$posF = -1;
			if ($posF===false || $posF==-1)
			{
				$strArray[] = substr($str, 0);
				$str = substr($str, 0);
				$posF = -1;
			}
			else
			{
				$strArray[] = substr($str, 0, $posF);
				$str = substr($str, $posF+1 );
			}
		}
		while ($posF != -1);
		return ($strArray);
	}
	
	$_SESSION['conex'] = $conn;

	$oOrdenPago = new orden_pago();
	$oOrdenPago->get($conn, $_GET['id'], $escEnEje);
	//var_dump($oOrdenPago->nrodoc);
	if(empty($oOrdenPago->nrodoc))
		header ("location: orden_pago.php");
		
	$_SESSION['pdf'] = serialize($oOrdenPago);
	
	// Crea un array donde cada posicion es un string de tamaño 'max' caracteres,
	// teniendo en cuenta de no cortar una palabra, busca el espacio en blanco  
	// mas cerca del tamaño 'max' y ahi corta el string

 	function ver_anexo($presupuesto, $contabilidad, $pdf)
	{
		//die(print_r($contabilidad));
		$pdf->AddPage();
		$sec = 5;
		$pro = 5;
		$act = 5;
		$gen = 5;
		$par = 7;
		$gene = 5;
		$espe= 5;
		$sesp1 = 5;
		$sesp2 = 8;
		$desc_part = 80;
		$pdf->Cell(175,4,'IMPUTACION PRESUPUESTARIA',0,'','C');
		$pdf->Ln();
		//$pdf->Ln();

		foreach($presupuesto as $prep)
		{
			$desc_partida = dividirStr($prep->partida_presupuestaria, intval($desc_part/$pdf->GetStringWidth('M')));
			$sec_val = substr($prep->id_categoria_programatica,0,2);
			$pro_val = substr($prep->id_categoria_programatica,2,2);
			$act_val = substr($prep->id_categoria_programatica,6,2);
			$gen_val = substr($prep->id_categoria_programatica,8,2);
			$par_val = substr($prep->id_partida_presupuestaria,0,3);
			$gene_val = substr($prep->id_partida_presupuestaria,3,2);
			$espe_val = substr($prep->id_partida_presupuestaria,5,2);
			$sesp1_val = substr($prep->id_partida_presupuestaria,7,2);
			$sesp2_val = substr($prep->id_partida_presupuestaria,9,4);
			/*$pdf->Ln();
			$pdf->Cell(50,3,$relaciones->id_categoria_programatica.' - '.$relaciones->id_partida_presupuestaria,0,'','C');*/
			$pdf->Ln();
			
			$pdf->Cell($sec,3,$sec_val,0,'','C');
			$pdf->Cell($pro,3,$pro_val,0,'','C');
			$pdf->Cell($act,3,$act_val,0,'','C');
			$pdf->Cell($gen,3,$gen_val,0,'','C');
			$pdf->Cell($par,3,$par_val,0,'','C');
			$pdf->Cell($gene,3,$gene_val,0,'','C');
			$pdf->Cell($espe,3,$espe_val,0,'','C');
			$pdf->Cell($sesp1,3,$sesp1_val,0,'','C');
			$pdf->Cell($sesp2,3,$sesp2_val,0,'','C');
			$pdf->Cell(2,3,'',0,'','C');
			$pdf->Cell($desc_part,3,utf8_decode($desc_partida[0]),0,'','L');
			$pdf->Cell(40,3,muestraFloat($prep->monto),0,'','R');
			$total+= $prep->monto; 
		}
		$ancho = $sec + $pro + $act + $gen + $par + $gene + $espe + $sesp1 + $sesp2 + 2 + $desc_part;
		$pdf->Ln();
		$pdf->Cell($ancho,4,'TOTAL:',0,'','R');
		$pdf->Cell(40,4,muestraFloat($total),0,'','R');
		$pdf->Ln(12);
		$pdf->Cell(175,4,'IMPUTACION CONTABLE',0,'','C');
		$pdf->Ln();

		while(!$contabilidad->EOF)
		{
			//die($contabilidad->fields['descripcion']);
			$desc_contabilidad = dividirStr($contabilidad->fields['descripcion'], intval(75/$pdf->GetStringWidth('M')));
			$sumaDebe+=$contabilidad->fields['debe'];
			$sumaHaber+=$contabilidad->fields['haber'];
			$pdf->Cell(5,4, utf8_decode(substr($contabilidad->fields['codcta'],1,1)),0, '','C');
			$pdf->Cell(8,4, utf8_decode(substr($contabilidad->fields['codcta'],5,2)),0, '','C');
			$pdf->Cell(8,4, utf8_decode(substr($contabilidad->fields['codcta'],7,2)),0, '','C');
			$pdf->Cell(8,4, utf8_decode(substr($contabilidad->fields['codcta'],9,2)),0, '','C');
			$pdf->Cell(8,4, utf8_decode(substr($contabilidad->fields['codcta'],11,2)),0, '','C');
			$pdf->Cell(8,4, utf8_decode(substr($contabilidad->fields['codcta'],13,3)),0, '','C');
			$pdf->Cell(75,4, utf8_decode($desc_contabilidad[0]),0, '','C');
			$pdf->Cell(40,4, utf8_decode(muestraFloat($contabilidad->fields['debe'])),0, '','C');
			$pdf->Cell(40,4, utf8_decode(muestraFloat($contabilidad->fields['haber'])),0, '','C');
			$pdf->Ln();
			$contabilidad->movenext();
		}
		$pdf->Ln();
		$pdf->Cell(120,4,'',0,'','C');
		$pdf->Cell(40,4,muestraFloat($sumaDebe),0,'','C');
		$pdf->Cell(40,4,muestraFloat($sumaHaber),0,'','C');	
	}
		

	class PDF extends FPDF
	{
		//Cabecera de página
		function Header()
		{
			$conn = $_SESSION['conex'];
			$q = $conn->Execute("SELECT * FROM finanzas.orden_pago WHERE nrodoc='".$_GET['id']."'");
			//var_dump($q);
			
			//$oOrden = unserialize($_SESSION['pdf']);
			//var_dump($oOrden);
			$this->SetLeftMargin(5);
			$this->SetFont('Courier','',10);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,30,20);//logo a la izquierda 
			$this->Ln(13);
			$this->Cell(50,2, DEPARTAMENTO, 0, 'L');
			$this->SetXY(37, 7); 
			$textoCabecera = "ORGANISMO_NOMBRE\n\n";
			//$textoCabecera.= "Administracion Tributaria\n\n";
			//$textoCabecera.= "oficina de Recursos Humanos\n\n";
			$this->MultiCell(100,2, $textoCabecera, 0, 'L');
			
			$this->SetFont('Courier','',14);
			$this->SetXY(160, 7); 
			$this->Cell(50,2, "ORDEN DE PAGO", 0, 'C');
			$this->SetFont('Courier','',13);
			$this->Ln(5);
			$this->SetX(161); 
			$this->Cell(50,2, $q->fields['nrodoc'], 0, 'C');
			$this->SetFont('Courier','',12);
			$this->Ln(5);
			$this->SetX(157); 
			$this->Cell(50,2, "Fecha: ".muestrafecha($q->fields['fecha']), 0, 'C');
			$this->Ln(15);
			$this->SetFont('Courier','',10);
			$this->SetDrawColor (255,255,255);
			$this->SetWidths(array(165));			
			$this->SetAligns(array('L'));
			$this->Row(array("He recibido del Servicio Autonomo Municipal de Administración Tributaria - S.A.M.A.T la cantidad de:	**********".muestraFloat($q->fields['montodoc'])." son: ".utf8_decode(num2letras($q->fields['montodoc'],false))),11);
			$this->SetFont('Arial','',10);
			$this->Ln(10);
				
		}

	//Pie de página
	function Footer()
	{
		$this->SetDrawColor (150,150,150);
		$this->SetFont('Arial','',8);
		$this->SetY(260);
		$this->Cell(42.5,5,"ADMINISTRADOR",1,0,'C');
		$this->SetX(57.5);
		$this->Cell(42.5,5,"INTENDENTE",1,0,'C');
		$this->SetX(110);
		$this->Cell(42.5,5,"CONTROL INTERNO",1,0,'C');
		$this->SetX(162.5);
		$this->Cell(42.5,5,"BENEFICIARIO",1,0,'C');
		$this->Ln(5);
		$this->Cell(42.5,20,"",1,0,'C');
		$this->SetX(57.5);
		$this->Cell(42.5,20,"",1,0,'C');
		$this->SetX(110);
		$this->Cell(42.5,20,"",1,0,'C');
		$this->SetX(162.5);
		$this->Cell(42.5,20,"",1,0,'C');
		$this->SetFont('Arial','',6);
		$this->Ln(15);
		$this->Cell(42.5,5,GTEADMINISTRACION,0,0,'C');
		$this->SetX(57.5);
		$this->Cell(42.5,5,INTENDENTE,0,0,'C');
		$this->SetX(110);
		$this->Cell(42.5,5,CONTROLINTERNO,0,0,'C');
		//Arial italic 8
	//	$this->SetFont('Arial','B',10);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(5);

#NOMBREE  DEL PROVEEDOR#
$oProveedor = new proveedores;
$oProveedor->get($conn, $oOrdenPago->id_proveedor);
//var_dump($oProveedor);
$oMP = new movimientos_presupuestarios;
$oMP->get($conn, $oOrdenPago->nrorefcomp, '1');

$pdf->SetFont('Arial','',8);

$pdf->Ln(6);
$pdf->SetDrawColor (150,150,150);
$pdf->SetXY(5, 52);
$pdf->Cell(140,12,utf8_decode($oProveedor->nombre),'L'.'T'.'B',0,'L');
$pdf->SetXY(145, 52);
$pdf->Cell(30,12, $oProveedor->rif,'T'.'B',0,'C');
$pdf->SetXY(175, 52);
$pdf->Cell(30,12, $oProveedor->nit,'R'.'T'.'B',0,'C');
$pdf->SetXY(7, 49); 
$pdf->SetFillColor (255,255,255);
$pdf->SetDrawColor (255,255,255);
$pdf->Cell(20,4,"A favor de:",1,0,'L',1);
$pdf->Ln(20);
$pdf->SetDrawColor (150,150,150);
$pdf->Cell(200,5,"CONCEPTO",1,0,'C');
$pdf->Ln(5);
$pdf->SetDrawColor (255,255,255);
$pdf->SetWidths(array(25,20,20,75,20,20,20));			
$pdf->SetAligns(array('C','C','C','L','C','C','C'));
$pdf->Row(array("Documento", "Soporte", "Fecha", "Descripción", "Monto", "Retenciones", "Monto"),11);
$pdf->Ln(5);
$pdf->SetAligns(array('C','C','C','L','R','R','R'));
$Total=0;
if($oOrdenPago->id_tipo_solicitud_si==0){

	$orden_c="	SELECT 
					B.nrodoc, 
					B.fecha,
					B.descripcion,
					A.monto,
					A.nrofactura,
					A.iva_retenido
				FROM
					finanzas.orden_pago AS B
					INNER Join finanzas.facturas AS A ON B.nrodoc = A.nrodoc
				WHERE
					B.nrodoc = '".$oOrdenPago->nrodoc."'";
	//var_dump($orden_c);
	$r_orden_c=$conn->Execute($orden_c);
	
	while(!$r_orden_c->EOF)
	{
		$pdf->Row(array($r_orden_c->fields['nrofactura'], $oOrdenPago->nrorefcomp, muestrafecha($r_orden_c->fields['fecha']), $r_orden_c->fields['descripcion'],  muestraFloat($r_orden_c->fields['monto']), "", ""),11);
		$pdf->Row(array("","","", "I.V.A.", "", muestraFloat($r_orden_c->fields['iva_retenido']), ""),11);
		$Total+=$r_orden_c->fields['monto']-$r_orden_c->fields['iva_retenido'];
		$q_retencion="	SELECT
							B.mntret,
							A.descri,
							B.codret,
							A.es_iva 
						FROM
							finanzas.retenciones_adiciones AS A
							Inner Join finanzas.relacion_retenciones_orden AS B ON B.codret = A.id
						WHERE
							B.nrofactura =  '".$r_orden_c->fields['nrofactura']."' AND
							B.nrodoc = '".$oOrdenPago->nrodoc."'";
		//var_dump($q_retencion);	
		$r_retencion=$conn->Execute($q_retencion);
		while(!$r_retencion->EOF)
		{
			//echo $r_retencion->fields['es_iva'].'<\br>';
			switch($r_retencion->fields['es_iva']){
				case '1': $nomRet = 'IVA';
					break;
				case '2': $nomRet = 'ISLR';
					break;
				case '3': $nomRet = 'Impuesto Municipal';
					break;
				case '4': $nomRet = 'Impuesto Nacional';
					break;
			}			 
			//$r_retencion->fields['es_iva']==1?'IVA':$r_retencion->fields['es_iva']==2?'ISLR':$r_retencion->fields['es_iva']==3?'Retencion Municipal':$r_retencion->fields['es_iva']==4?'Retencion Nacional':''
			$pdf->Row(array("","", "", $nomRet, "", muestraFloat($r_retencion->fields['mntret']), ""),11);
			$Total-=$r_retencion->fields['mntret'];
			$r_retencion->movenext();
		}
		$r_orden_c->movenext();
	}
}else{
	//echo 'entro else<\br>';
	$pdf->Row(array('', '', muestrafecha($oOrdenPago->fecha), $oOrdenPago->descripcion,  muestraFloat($oOrdenPago->montodoc), "", ""),11);
	$Total+=$oOrdenPago->montodoc-$oOrdenPago->montoret;
}
//echo 'salio<\br>';
//$pdf->Line();
$pdf->SetFont('Arial','B',8);
$pdf->SetAligns(array('C','C','C','R','R','R','R'));
$pdf->Row(array("","", "", "Total a Pagar","" , "",  muestraFloat($Total)),T,11);
$pdf->SetDrawColor (150,150,150);
$pdf->SetXY(5, 74);
$pdf->Cell(200,5,"",1,0,'C');
$pdf->Ln(5);
$pdf->Cell(200,90,"",1,0,'C');
$pdf->Ln(95);
$pdf->SetDrawColor (150,150,150);
$pdf->Cell(200,5,"BANCO",1,0,'C');
$oCB = new cuentas_bancarias;
$oCB->get($conn, $oOrdenPago->id_nro_cuenta);
//die(var_dump($oCB->banco));
$pdf->Ln(5);
$pdf->SetDrawColor (150,150,150);
$pdf->Cell(90,10,utf8_decode($oCB->banco->descripcion),'LBT',0,'L');
$pdf->Cell(80,10,utf8_decode($oCB->nro_cuenta),'BT',0,'C');
$pdf->Cell(30,10,muestraFloat($Total),'BTR',0,'R');
$pdf->Ln(15);
$pdf->Cell(90,5,"CONTABILIDAD",1,0,'C');
$pdf->SetX(115);
$pdf->Cell(90,5,"PRESUPUESTO",1,0,'C');
$pdf->Ln(5);
$pdf->Cell(90,50,"",1,0,'C');
$pdf->SetX(115);
$pdf->Cell(90,50,"",1,0,'C');
//$pdf->Ln(60);



		

//$pdf->Ln(12);

/*$sec = 6;
$pro = 6;
$act = 6;
$gen = 6;
$par = 8;
$gene = 6;
$espe= 6;
$sesp1 = 6;
$sesp2 = 9;*/
$espacio = 10;
//$desc_part = 90;*/
if($oOrdenPago->id_tipo_solicitud_si==0)
{
	$oRelacion = orden_pago::getRelacionPartidas($conn,$_GET['id'],$escEnEje);
		$pdf->SetFont('Arial','',6);
	$cant_lineas = 0;
	foreach($oRelacion as $relaciones)
	{
		$desc_partida = dividirStr($relaciones->partida_presupuestaria, intval($desc_part/$pdf->GetStringWidth('M')));
		$cant_lineas+= count($desc_partida);
	}
	$pdf->SetXY(116,200);
		foreach($oRelacion as $relaciones)
		{
			//$desc_partida = dividirStr($relaciones->partida_presupuestaria, intval($desc_part/$pdf->GetStringWidth('M')));
			$sec_val = substr($relaciones->id_categoria_programatica,0,2);
			$pro_val = substr($relaciones->id_categoria_programatica,2,2);
			$x= substr($relaciones->id_categoria_programatica,4,2);
			$act_val = substr($relaciones->id_categoria_programatica,6,2);
			$gen_val = substr($relaciones->id_categoria_programatica,8,2);
			$par_val = substr($relaciones->id_partida_presupuestaria,0,1);
			$y_val = substr($relaciones->id_partida_presupuestaria,1,2);
			$gene_val = substr($relaciones->id_partida_presupuestaria,3,2);
			$espe_val = substr($relaciones->id_partida_presupuestaria,5,2);
			$sesp1_val = substr($relaciones->id_partida_presupuestaria,7,2);
			$sesp2_val = substr($relaciones->id_partida_presupuestaria,10,3);
			/*$pdf->Ln();
			$pdf->Cell(50,3,$relaciones->id_categoria_programatica.' - '.$relaciones->id_partida_presupuestaria,0,'','C');*/
			$pdf->SetDrawColor (255,255,255);
			$pdf->SetWidths(array(8,55,15));			
			$pdf->SetAligns(array('L','L','R'));
			$pdf->Row(array($anoCurso,$sec_val.'.'.$pro_val.'.'.$x.'.'.$act_val.'.'.$gen_val.'.'.$par_val.'.'.$y_val.'.'.$gene_val.'.'.$espe_val.'.'.$sesp1_val.'.'.$sesp2_val, muestraFloat($relaciones->monto)),11);
			//$pdf->Cell(90,3,$sec_val.'.'.$pro_val.'.'.$x.'.'.$act_val.'.'.$gen_val.'.'.$par_val.'.'.$y_val.'.'.$gene_val.'.'.$espe_val.'.'.$sesp1_val.'.'.$sesp2_val.'					'.muestraFloat($relaciones->monto),1,0,'','L');
			//$pdf->Ln();
			$pdf->SetX(116);
					
	/*		$totales += $relaciones->monto;	
				$hay_ue = next($desc_partida);
			$ancho = $sec + $pro + $act + $gen + $par + $gene + $espe + $sesp1 + $sesp2 + 2;
			for ($i=1; $hay_ue!==false; $i++)
			{
				$pdf->Ln();
				$pdf->Cell($ancho+10,4, '',0, '','C');
				$pdf->Cell($desc_part,4, $desc_partida[$i], 0, '', 'L');
				$pdf->Cell(40,4, '',0, '','R');
				$hay_ue = next($desc_partida);
			}*/
		}
/*	} 
	else 
	{
		//$pdf->SetXY(90,90);
		//$pdf->Cell(30,4,'VER ANEXO',0,'','L');
		foreach($oRelacion as $relaciones)
		{
			$totales += $relaciones->monto;	
		}
	}
	//$pdf->Ln();
	//$pdf->SetXY($pdf->GetX(),135);
	//$pdf->Cell(170,4, '',0, '','R');
	//$pdf->Cell(40,4, muestrafloat($totales),0, '','C');*/
} 
else 
{
	$pdf->SetXY(135,222);
	$pdf->Cell(30,4,'ORDEN DE PAGO SIN IMPUTACION',0,'','L');
	//$pdf->SetXY(15,135);
}

//PARTE CONTABLE C.E.P.V
$q="SELECT C.codcta,C.descripcion,B.debe,B.haber,A.numcom FROM contabilidad.com_enc AS A INNER JOIN contabilidad.com_det AS B ON A.id=B.id_com INNER JOIN contabilidad.plan_cuenta AS C ON B.id_cta=C.id  WHERE A.origen='OP' AND A.num_doc='".$_GET['id']."' AND A.id_escenario = '".$escEnEje."'";
//die($q);
//echo $cant_lineas."<br>";
$r= $conn->Execute($q);
$cant_lineas = $r->RecordCount();
//die(var_dump($r));
$sumaDebe=0;
$sumaHaber=0;
$pdf->Ln(12);
//echo 'aqui '.$cant_lineas;
//if($cant_lineas <= 5){
	$pdf->SetXY(11,200);
	while(!$r->EOF){
		//$desc_contabilidad = dividirStr($r->fields['descripcion'], intval(72/$pdf->GetStringWidth('M')));
		$sumaDebe+=$r->fields['debe'];
		$sumaHaber+=$r->fields['haber'];
			$pdf->SetFont('Arial','',6);
		$pdf->SetDrawColor (255,255,255);
		$pdf->SetWidths(array(50,15,15));			
		$pdf->SetAligns(array('L','R','R'));
		$pdf->Row(array($r->fields['codcta'], utf8_decode(muestraFloat($r->fields['debe'])),utf8_decode(muestraFloat($r->fields['haber']))),11);
		$pdf->SetX(11);
		//$pdf->Cell(72,5, utf8_decode($desc_contabilidad[0]),0, '','L');
		//$pdf->Cell(40,5, utf8_decode(muestraFloat($r->fields['debe'])),0, '','C');
		//$pdf->Cell(40,5, utf8_decode(muestraFloat($r->fields['haber'])),0, '','C');
			/*$hay_cont = next($desc_contabilidad);
			for ($i=1; $hay_cont!==false; $i++)
			{
				//$pdf->Ln();
				//$pdf->Cell(55,4, '',0, '','L');
				//$pdf->Cell(72,4, utf8_decode($desc_contabilidad[$i]), 0, '', 'L');
				//$pdf->Cell(40,4, '',0, '','R');
				$hay_cont = next($desc_contabilidad);
			}*/
		$r->movenext();
		//$pdf->Ln();
	}
/*} else {
	while(!$r->EOF){
		$sumaDebe+=$r->fields['debe'];
		$sumaHaber+=$r->fields['haber'];
		$r->movenext();
	}
	$pdf->SetXY(90,150);
//	$pdf->Cell(70,4,'VER ANEXO',0,'','L');
}*/
//$pdf->SetXY(15,200);
//$pdf->Cell(120,5, utf8_decode($r->fields['numcom']),0, '','C');
//$pdf->Cell(40,5, muestraFloat($sumaDebe),0, '','C');
//$pdf->Cell(40,5, muestraFloat($sumaHaber),0, '','C'); 


//echo 'termino';


//$pdf->SetXY(15,210);
//$pdf->Cell(1,4,'',0,'C');
//$pdf->Cell(120,4, utf8_decode(num2letras($totales,false)),0,'','L');
//$pdf->ln(12);
//$pdf->Cell(30,4,'',0,'','C');
//$pdf->Cell(40,4, muestraFloat($oOrdenPago->montodoc),0,'','C');
//$pdf->Cell(20,4,'',0,'','C');
//$pdf->Cell(40,4, muestraFloat($oOrdenPago->montoret),0,'','C');
//$pdf->Cell(40,4,'',0,'','C');
//$pagar = $oOrdenPago->montodoc - $oOrdenPago->montoret;
//$pdf->Cell(40,4, muestraFloat($pagar),0,'','C');


$pdf->Ln(20);
$pdf->Output();

?>