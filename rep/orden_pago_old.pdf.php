<?
include("comun/ini.php");
$oOrdenPago = new orden_pago();
$oOrdenPago->get($conn, $_GET['id'], $escEnEje);
if(empty($oOrdenPago->nrodoc))
	header ("location: orden_pago.php");
$_SESSION['pdf'] = serialize($oOrdenPago);
// Crea un array donde cada posicion es un string de tamaño 'max' caracteres,
// teniendo en cuenta de no cortar una palabra, busca el espacio en blanco  
// mas cerca del tamaño 'max' y ahi corta el string

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
    }while ($posF != -1);
    
    return ($strArray);
  }
  
  function ver_anexo($presupuesto, $contabilidad, $pdf){
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
	foreach($presupuesto as $prep){
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
	while(!$contabilidad->EOF){
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
			$oOrden = unserialize($_SESSION['pdf']);
			$this->SetLeftMargin(5);
			$this->SetXY(160, 16);
			$this->SetFont('Arial','',10);
			$this->Cell(20,4,$oOrden->nrodoc,0,'','C');
			$this->SetXY(160, 20);
			$this->Cell(20,6,muestrafecha($oOrden->fecha),0,'','C');
			
			$this->SetFont('Arial','',10);
			$this->Ln(10);
			
	}

	//Pie de página
	function Footer()
	{
		
		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','B',10);
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
$pdf->Ln(8);
$oProveedor = new proveedores;
$oProveedor->get($conn, $oOrdenPago->id_proveedor);

$pdf->SetFont('Arial','',10);
$pdf->Cell(150,4,utf8_decode($oProveedor->nombre),0, '','L');
$pdf->Cell(25,4,$oOrdenPago->nrorefcomp,0,'','C');
$oMP = new movimientos_presupuestarios;
$oMP->get($conn, $oOrdenPago->nrorefcomp, '1');
$pdf->Cell(25,4,muestraFecha($oMP->fecharef),0,'','R');

$pdf->Ln(15);

$pdf->MultiCell(155,6,utf8_decode($oOrdenPago->descripcion),0,'J',0);
$pdf->Ln(12);

$sec = 6;
$pro = 6;
$act = 6;
$gen = 6;
$par = 8;
$gene = 6;
$espe= 6;
$sesp1 = 6;
$sesp2 = 9;
$espacio = 10;
$desc_part = 90;
if($oOrdenPago->id_tipo_solicitud_si==0){
	$oRelacion = orden_pago::getRelacionPartidas($conn,$_GET['id'],$escEnEje);
	$pdf->SetFont('Arial','',10);
	//die(var_dump(count($oRelacion)));	
	$cant_lineas = 0;
	foreach($oRelacion as $relaciones){
		$desc_partida = dividirStr($relaciones->partida_presupuestaria, intval($desc_part/$pdf->GetStringWidth('M')));
		$cant_lineas+= count($desc_partida);
	}
	if($cant_lineas <= 11){
		//die(var_dump($cant_lineas));
		foreach($oRelacion as $relaciones){
			$desc_partida = dividirStr($relaciones->partida_presupuestaria, intval($desc_part/$pdf->GetStringWidth('M')));
			$sec_val = substr($relaciones->id_categoria_programatica,0,2);
			$pro_val = substr($relaciones->id_categoria_programatica,2,2);
			$act_val = substr($relaciones->id_categoria_programatica,6,2);
			$gen_val = substr($relaciones->id_categoria_programatica,8,2);
			$par_val = substr($relaciones->id_partida_presupuestaria,0,3);
			$gene_val = substr($relaciones->id_partida_presupuestaria,3,2);
			$espe_val = substr($relaciones->id_partida_presupuestaria,5,2);
			$sesp1_val = substr($relaciones->id_partida_presupuestaria,7,2);
			$sesp2_val = substr($relaciones->id_partida_presupuestaria,9,4);
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
			$pdf->Cell($espacio,3,'',0,'','C');
			$pdf->Cell($desc_part,3,utf8_decode($desc_partida[0]),0,'','L');
			$pdf->Cell(40,3,muestraFloat($relaciones->monto),0,'','R');
			
			$totales += $relaciones->monto;	
				$hay_ue = next($desc_partida);
			$ancho = $sec + $pro + $act + $gen + $par + $gene + $espe + $sesp1 + $sesp2 + 2;
			for ($i=1; $hay_ue!==false; $i++)
			{
				$pdf->Ln();
				$pdf->Cell($ancho+10,4, '',0, '','C');
				$pdf->Cell($desc_part,4, $desc_partida[$i], 0, '', 'L');
				$pdf->Cell(40,4, '',0, '','R');
				$hay_ue = next($desc_partida);
			}
		}
	} else {
		$pdf->SetXY(90,90);
		$pdf->Cell(30,4,'VER ANEXO',0,'','L');
		foreach($oRelacion as $relaciones){
			$totales += $relaciones->monto;	
		}
	}
	

$pdf->Ln();
$pdf->SetXY($pdf->GetX(),135);
$pdf->Cell(170,4, '',0, '','R');
$pdf->Cell(40,4, muestrafloat($totales),0, '','C');
} else {
	$pdf->SetXY(90,90);
	$pdf->Cell(30,4,'ORDEN DE PAGO SIN IMPUTACION',0,'','L');
	$pdf->SetXY(15,135);
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
if($cant_lineas <= 5){
	while(!$r->EOF){
		$desc_contabilidad = dividirStr($r->fields['descripcion'], intval(72/$pdf->GetStringWidth('M')));
		$sumaDebe+=$r->fields['debe'];
		$sumaHaber+=$r->fields['haber'];
		$pdf->Cell(5,5, utf8_decode(substr($r->fields['codcta'],1,1)),0, '','C');
		$pdf->Cell(8,5, utf8_decode(substr($r->fields['codcta'],5,2)),0, '','C');
		$pdf->Cell(8,5, utf8_decode(substr($r->fields['codcta'],7,2)),0, '','C');
		$pdf->Cell(8,5, utf8_decode(substr($r->fields['codcta'],9,2)),0, '','C');
		$pdf->Cell(8,5, utf8_decode(substr($r->fields['codcta'],11,2)),0, '','C');
		$pdf->Cell(8,5, utf8_decode(substr($r->fields['codcta'],13,3)),0, '','C');
		$pdf->Cell(10,5, '',0, '','C');
		$pdf->Cell(72,5, utf8_decode($desc_contabilidad[0]),0, '','L');
		$pdf->Cell(40,5, utf8_decode(muestraFloat($r->fields['debe'])),0, '','C');
		$pdf->Cell(40,5, utf8_decode(muestraFloat($r->fields['haber'])),0, '','C');
			$hay_cont = next($desc_contabilidad);
			for ($i=1; $hay_cont!==false; $i++)
			{
				$pdf->Ln();
				$pdf->Cell(55,4, '',0, '','L');
				$pdf->Cell(72,4, utf8_decode($desc_contabilidad[$i]), 0, '', 'L');
				$pdf->Cell(40,4, '',0, '','R');
				$hay_cont = next($desc_contabilidad);
			}
		$r->movenext();
		$pdf->Ln();
	}
} else {
	while(!$r->EOF){
		$sumaDebe+=$r->fields['debe'];
		$sumaHaber+=$r->fields['haber'];
		$r->movenext();
	}
	$pdf->SetXY(90,150);
	$pdf->Cell(70,4,'VER ANEXO',0,'','L');
}
$pdf->SetXY(15,200);
$pdf->Cell(120,5, utf8_decode($r->fields['numcom']),0, '','C');
$pdf->Cell(40,5, muestraFloat($sumaDebe),0, '','C');
$pdf->Cell(40,5, muestraFloat($sumaHaber),0, '','C'); 





$pdf->SetXY(15,210);
//$pdf->Cell(1,4,'',0,'C');
$pdf->Cell(120,4, utf8_decode(num2letras($totales,false)),0,'','L');
$pdf->ln(12);
$pdf->Cell(30,4,'',0,'','C');
$pdf->Cell(40,4, muestraFloat($oOrdenPago->montodoc),0,'','C');
$pdf->Cell(20,4,'',0,'','C');
$pdf->Cell(40,4, muestraFloat($oOrdenPago->montoret),0,'','C');
$pdf->Cell(40,4,'',0,'','C');
$pagar = $oOrdenPago->montodoc - $oOrdenPago->montoret;
$pdf->Cell(40,4, muestraFloat($pagar),0,'','C');
$pdf->Ln();


$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

if($cant_lineas > 5){
	$r->MoveFirst();
	ver_anexo($oRelacion, $r, $pdf);
}


$pdf->Output();

?>