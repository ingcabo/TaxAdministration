<?
	include("comun/ini.php");
	$fecha_desde = $_REQUEST['fecha_desde'];
	$fecha_hasta = $_REQUEST['fecha_hasta'];
	$id_proveedor = $_REQUEST['id_proveedor'];
	$id_status = $_REQUEST['status'];
	$id_ue = $_REQUEST['id_ue'];
	
	
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

class PDF extends FPDF
{
	function Header()
	{
			$this->SetLeftMargin(18);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 15); 
			$textoCabecera = "INTENDENCIA MUNICIPAL\n";
			
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(300, 15); 
			$textoDerecha = "Fecha Impresion: ".date('d/m/Y')."\n";
			//$textoDerecha.= "Nro.:".$nro_recibo."\n";
			$textoDerecha.= "Fecha Generac.:".muestrafecha($fecha)."\n";
			//$textoDerecha.= "Fecha Aprob.:".muestrafecha($oOrden->fecha_aprobacion)."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln();

			$this->SetFont('Courier','b',12);
			//if($tipo == '002')
			//	$tipoOrden = "Orden de Servicio";
			//elseif($tipo == '009')
				$tipoOrden = "LISTADO DE ORDENES DE COMPRA";
			
			$this->Text(205, 40, $tipoOrden);
			$this->Line(18, 41, 410, 41);
			$this->Ln(20);
			//$this->Text(160, 40, '#');
			//$this->Text(175, 40, $id);
	}

	function Footer()
	{	

		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF('l','mm','A3');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',10);
$pdf->SetLeftMargin(18);

//BUSCA LAS ORDENES DE COMPRA QUE TIENEN ORDEN(ES) DE PAGO(S) ASOCIADAS 
$q = "SELECT oc.id, oc.nrodoc, oc.fecha, oc.rif, p.nombre AS proveedor, SUM(roc.precio_base * roc.cantidad) AS subtotal, SUM(roc.precio_iva) AS iva, SUM(roc.monto) AS total, ue.descripcion AS unidad_ejecutora, op.nrodoc AS orden_pago, op.montoret, op.montopagado, oc.status ";
$q.= "FROM puser.orden_compra oc ";
$q.= "INNER JOIN puser.relacion_ordcompra roc ON (oc.id = roc.id_ord_compra) ";
$q.= "INNER JOIN puser.proveedores p ON (oc.rif = p.id) ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (oc.id_unidad_ejecutora = ue.id AND ue.id_escenario = '1111') ";
$q.= "INNER JOIN finanzas.solicitud_pago sp ON (oc.nrodoc = sp.nroref) ";
$q.= "INNER JOIN finanzas.orden_pago op ON (sp.nrodoc = op.nroref) ";
$q.= "WHERE oc.status = 2 OR oc.status = 4 ";
$q.= (!empty($fecha_desde)) ? "AND oc.fecha >= '".guardafecha($fecha_desde)."' " : "";
$q.= (!empty($fecha_hasta)) ? "AND oc.fecha <= '".guardafecha($fecha_hasta)."' " : "";
$q.= (!empty($id_proveedor)) ? "AND oc.rif = '$id_proveedor' " : "";
$q.= (!empty($id_status)) ? "AND oc.status = '$id_status' " : "";
$q.= (!empty($id_ue)) ? "AND oc.id_unidad_ejecutora = '$id_ue' " : "";
$q.= "GROUP BY 1,2,3,4,5,9,10,11,12,13 ";
$q.= "ORDER BY 2,3 ";
//die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(390,8,'ORDENES DE COMPRA CON ORDENES DE PAGO ASOCIADAS',1,1,'C');
//$pdf->Ln();
$pdf->Cell(20,4,'Nº OC',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(75,4,'Proveedor',1,0,'C');
$pdf->Cell(60,4,'Unidad Solicitante',1,0,'C');
$pdf->Cell(40,4,'Sub-Total',1,0,'C');
$pdf->Cell(30,4,'IVA',1,0,'C');
$pdf->Cell(40,4,'Total',1,0,'C');
$pdf->Cell(40,4,'Abonos',1,0,'C');
$pdf->Cell(40,4,'Total General',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
	$sumSubTotal= 0;
	$sumIva=  0;
	$sumTotal= 0;
	$sumAbono= 0;
	$sumTotalGen= 0; 
while(!$r->EOF){
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(75/$pdf->GetStringWidth('M')));
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(60/$pdf->GetStringWidth('M')));
	$aProv = count($desc_proveedor);
	$aUnidad = count($desc_unidad);
	
	if($aProv>=$aUnidad)
		$aux = $aProv;
	else
		$aux = $aUnidad;
			
	$pdf->Cell(20,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(25,4,muestrafecha($r->fields['fecha']),0,0,'L');
	$pdf->Cell(75,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(60,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(40,4,muestrafloat($r->fields['subtotal']),0,0,'R');
	$pdf->Cell(30,4,muestrafloat($r->fields['iva']),0,0,'R');
	$pdf->Cell(40,4,muestrafloat($r->fields['total']),0,0,'R');
	$abono = ($r->fields['montopagado']>0) ? $r->fields['montopagado'] + $r->fields['montoret'] : 0;
	$total = $r->fields['total'] - $abono;
	$pdf->Cell(40,4,muestrafloat($abono),0,0,'R');
	$pdf->Cell(40,4,muestrafloat($total),0,0,'R');
	switch($r->fields['status']){
	 	case "1":
			$status = "Registrada";
		break;
		case "2":
			$status = "Aprobada";
		break;
		case "3":
			$status = "Anulada";
		break;		
		case "4":
			$status = "Recibida";
		break;
	}
	$pdf->Cell(20,4,$status,0,0,'C');
	
	for($i=1;$i<=$aux-1;$i++){
		$pdf->Ln();
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(25,4,'',0,0,'C');
		$pdf->Cell(75,4,utf8_decode($desc_proveedor[$i]),0,0,'L');
		$pdf->Cell(60,4,utf8_decode($desc_unidad[$i]),0,0,'L');
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(30,4,'',0,0,'R');
		$pdf->Cell(40,4,'',0,0,'C');
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(60,4,'',0,0,'C');
	}
	$sumSubTotal+= $r->fields['subtotal'];
	$sumIva+=  $r->fields['iva'];
	$sumTotal+= $r->fields['total'];
	$sumAbono+= $abono;
	$sumTotalGen+= $total;
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(160,4,'Totales:',0,0,'R');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4,'',0,0,'C');
$pdf->Cell(40,4,muestraFloat($sumSubTotal),0,0,'R');
$pdf->Cell(30,4,muestraFloat($sumIva),0,0,'R');
$pdf->Cell(40,4,muestraFloat($sumTotal),0,0,'R');
$pdf->Cell(40,4,muestraFloat($sumAbono),0,0,'R');
$pdf->Cell(40,4,muestraFloat($sumTotalGen),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(370,4,'Bs.F.: '.muestraFloat($sumTotalGen/1000),0,0,'R');
}
$pdf->Ln(12);


$q = "SELECT oc.id, oc.nrodoc, oc.fecha, oc.rif, p.nombre AS proveedor, SUM(roc.precio_base * roc.cantidad) AS subtotal, SUM(roc.precio_iva) AS iva, SUM(roc.monto) AS total, ue.descripcion AS unidad_ejecutora, oc.status ";
$q.= "FROM puser.orden_compra oc ";
$q.= "INNER JOIN puser.relacion_ordcompra roc ON (oc.id = roc.id_ord_compra) ";
$q.= "INNER JOIN puser.proveedores p ON (oc.rif = p.id) ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (oc.id_unidad_ejecutora = ue.id AND ue.id_escenario = '1111') ";
$q.= "INNER JOIN finanzas.solicitud_pago sp ON (oc.nrodoc = sp.nroref) ";
$q.= "WHERE sp.nrodoc NOT IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.orden_pago) ";
$q.= (!empty($fecha_desde)) ? "AND oc.fecha >= '".guardafecha($fecha_desde)."' " : "";
$q.= (!empty($fecha_hasta)) ? "AND oc.fecha <= '".guardafecha($fecha_hasta)."' " : "";
$q.= (!empty($id_proveedor)) ? "AND oc.rif = '$id_proveedor' " : "";
$q.= (!empty($id_status)) ? "AND oc.status = '$id_status' " : "";
$q.= (!empty($id_ue)) ? "AND oc.id_unidad_ejecutora = '$id_ue' " : "";
$q.= "GROUP BY 1,2,3,4,5,9,10 ";
$q.= "ORDER BY 2,3 ";

$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(390,8,'ORDENES DE COMPRA EN PRESUPUESTO SIN ORDEN DE PAGO',1,1,'C');
//$pdf->Ln();
$pdf->Cell(20,4,'Nº OC',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(75,4,'Proveedor',1,0,'C');
$pdf->Cell(60,4,'Unidad Solicitante',1,0,'C');
$pdf->Cell(40,4,'Sub-Total',1,0,'C');
$pdf->Cell(30,4,'IVA',1,0,'C');
$pdf->Cell(40,4,'Total',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
	$sumSubTotal= 0;
	$sumIva=  0;
	$sumTotal= 0;
while(!$r->EOF){
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(75/$pdf->GetStringWidth('M')));
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(60/$pdf->GetStringWidth('M')));
	$aProv = count($desc_proveedor);
	$aUnidad = count($desc_unidad);
	
	if($aProv>=$aUnidad)
		$aux = $aProv;
	else
		$aux = $aUnidad;
			
	$pdf->Cell(20,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(25,4,muestrafecha($r->fields['fecha']),0,0,'L');
	$pdf->Cell(75,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(60,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(40,4,muestrafloat($r->fields['subtotal']),0,0,'R');
	$pdf->Cell(30,4,muestrafloat($r->fields['iva']),0,0,'R');
	$pdf->Cell(40,4,muestrafloat($r->fields['total']),0,0,'R');
	switch($r->fields['status']){
	 	case "1":
			$status = "Registrada";
		break;
		case "2":
			$status = "Aprobada";
		break;
		case "3":
			$status = "Anulada";
		break;		
		case "4":
			$status = "Recibida";
		break;
	}
	$pdf->Cell(20,4,$status,0,0,'C');
	
	for($i=1;$i<=$aux-1;$i++){
		$pdf->Ln();
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(25,4,'',0,0,'C');
		$pdf->Cell(75,4,utf8_decode($desc_proveedor[$i]),0,0,'L');
		$pdf->Cell(60,4,utf8_decode($desc_unidad[$i]),0,0,'L');
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(30,4,'',0,0,'R');
		$pdf->Cell(40,4,'',0,0,'C');
	}
	$sumSubTotal+= $r->fields['subtotal'];
	$sumIva+=  $r->fields['iva'];
	$sumTotal+= $r->fields['total'];
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(160,4,'Totales:',0,0,'R');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4,'',0,0,'C');
$pdf->Cell(40,4,muestraFloat($sumSubTotal),0,0,'R');
$pdf->Cell(30,4,muestraFloat($sumIva),0,0,'R');
$pdf->Cell(40,4,muestraFloat($sumTotal),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(290,4,'Bs.F.: '.muestraFloat($sumTotal/1000),0,0,'R');
}
$pdf->Ln(12);

$q = "SELECT oc.id, oc.nrodoc, oc.fecha, oc.rif, p.nombre AS proveedor, SUM(roc.precio_base * roc.cantidad) AS subtotal, SUM(roc.precio_iva) AS iva, SUM(roc.monto) AS total, ue.descripcion AS unidad_ejecutora, oc.status ";
$q.= "FROM puser.orden_compra oc ";
$q.= "INNER JOIN puser.relacion_ordcompra roc ON (oc.id = roc.id_ord_compra) ";
$q.= "INNER JOIN puser.proveedores p ON (oc.rif = p.id) ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (oc.id_unidad_ejecutora = ue.id AND ue.id_escenario = '1111') ";
$q.= "WHERE  (oc.nrodoc NOT IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.solicitud_pago) OR oc.nrodoc IS Null) ";
$q.= (!empty($fecha_desde)) ? "AND oc.fecha >= '".guardafecha($fecha_desde)."' " : "";
$q.= (!empty($fecha_hasta)) ? "AND oc.fecha <= '".guardafecha($fecha_hasta)."' " : "";
$q.= (!empty($id_proveedor)) ? "AND oc.rif = '$id_proveedor' " : "";
$q.= (!empty($id_status)) ? "AND oc.status = '$id_status' " : "";
$q.= (!empty($id_ue)) ? "AND oc.id_unidad_ejecutora = '$id_ue' " : "";
$q.= "GROUP BY 1,2,3,4,5,9,10 ";
$q.= "ORDER BY 2,3 ";
//die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(390,8,'ORDENES DE COMPRA QUE NO ESTAN EN PRESUPUESTO',1,1,'C');
//$pdf->Ln();
$pdf->Cell(20,4,'Nº OC',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(75,4,'Proveedor',1,0,'C');
$pdf->Cell(60,4,'Unidad Solicitante',1,0,'C');
$pdf->Cell(40,4,'Sub-Total',1,0,'C');
$pdf->Cell(30,4,'IVA',1,0,'C');
$pdf->Cell(40,4,'Total',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
	$sumSubTotal= 0;
	$sumIva=  0;
	$sumTotal= 0;
while(!$r->EOF){
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(75/$pdf->GetStringWidth('M')));
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(60/$pdf->GetStringWidth('M')));
	$aProv = count($desc_proveedor);
	$aUnidad = count($desc_unidad);
	
	if($aProv>=$aUnidad)
		$aux = $aProv;
	else
		$aux = $aUnidad;
			
	$pdf->Cell(20,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(25,4,muestrafecha($r->fields['fecha']),0,0,'L');
	$pdf->Cell(75,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(60,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(40,4,muestrafloat($r->fields['subtotal']),0,0,'R');
	$pdf->Cell(30,4,muestrafloat($r->fields['iva']),0,0,'R');
	$pdf->Cell(40,4,muestrafloat($r->fields['total']),0,0,'R');
	switch($r->fields['status']){
	 	case "1":
			$status = "Registrada";
		break;
		case "2":
			$status = "Aprobada";
		break;
		case "3":
			$status = "Anulada";
		break;		
		case "4":
			$status = "Recibida";
		break;
	}
	$pdf->Cell(20,4,$status,0,0,'C');
	
	for($i=1;$i<=$aux-1;$i++){
		$pdf->Ln();
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(25,4,'',0,0,'C');
		$pdf->Cell(75,4,utf8_decode($desc_proveedor[$i]),0,0,'L');
		$pdf->Cell(60,4,utf8_decode($desc_unidad[$i]),0,0,'L');
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(30,4,'',0,0,'R');
		$pdf->Cell(40,4,'',0,0,'C');
	}
	$sumSubTotal+= $r->fields['subtotal'];
	$sumIva+=  $r->fields['iva'];
	$sumTotal+= $r->fields['total'];
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(160,4,'Totales:',0,0,'R');
$pdf->SetFont('Courier','B',8);
$pdf->Cell(20,4,'',0,0,'C');
$pdf->Cell(40,4,muestraFloat($sumSubTotal),0,0,'R');
$pdf->Cell(30,4,muestraFloat($sumIva),0,0,'R');
$pdf->Cell(40,4,muestraFloat($sumTotal),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(290,4,'Bs.F.: '.muestraFloat($sumTotal/1000),0,0,'R');
}
$pdf->Ln(12);


$pdf->SetFont('Courier','',8);

$pdf->Output();
?>