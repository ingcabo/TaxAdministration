<?
	include("comun/ini.php");
	$fecha_desde = $_REQUEST['fecha_desde'];
	$fecha_hasta = $_REQUEST['fecha_hasta'];
	$id_proveedor = $_REQUEST['id_proveedor'];
	$id_ue = $_REQUEST['id_ue'];
	$tipoCom = $_REQUEST['tipocom'];
	$tipoProv = $_REQUEST['tipoprov'];
	$id_status = $_REQUEST['status'];
	
	
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
				$tipoOrden = "REPORTE ORDENES DE PAGO PENDIENTES";
			
			$this->Text(150, 40, $tipoOrden);
			$this->Line(18, 41, 393, 41);
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
$pdf->SetFont('Courier','B',14);
$pdf->SetLeftMargin(18);

//ORDENES DE PAGO SIN PAGOS RELACIONADOS
$q = "SELECT DISTINCT op.nrodoc as nrodoc, op.id_proveedor, op.id_unidad_ejecutora, op.fecha, ue.descripcion AS unidad_ejecutora, p.nombre AS proveedor,  op.montodoc AS montodoc, ";
$q.= " mp.nrodoc AS nrodoccom, mp.fechadoc AS fechacom, op.status, td.abreviacion ";
$q.= "FROM finanzas.orden_pago op ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (op.id_unidad_ejecutora = ue.id AND ue.id_escenario = '$escEnEje') ";
$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (sp.nrodoc = op.nroref) ";
$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
$q.= "LEFT JOIN puser.tipos_documentos td ON (substr(sp.nroref,1,3) = td.id )";
$q.= "WHERE op.nrodoc NOT IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.relacion_cheque) ";
$q.= "AND op.nrodoc NOT IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.relacion_otros_pagos) ";
$q.= !empty($id_status) ? "AND op.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND op.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND op.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND op.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_ue) ? "AND op.id_unidad_ejecutora = '$id_ue' " : "";
$q.= !empty($tipoCom) ? "AND COALESCE(mp.tipdoc,'0') = '$tipoCom' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "ORDER BY op.nrodoc ";
//die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(375,8,'ORDENES DE PAGO SIN PAGOS RELACIONADOS',1,1,'C');
//$pdf->Ln();
$pdf->Cell(90,5,'Unidad Ejecutora',1,0,'C');
$pdf->Cell(30,5,'Nº Documento',1,0,'C');
$pdf->Cell(25,5,'Fecha',1,0,'C');
$pdf->Cell(30,5,'Monto',1,0,'C');
$pdf->Cell(115,5,'Beneficiario',1,0,'C');
$pdf->Cell(10,5,'T. D.',1,0,'C');
$pdf->Cell(30,5,'Compromiso',1,0,'C');
$pdf->Cell(25,5,'Fecha',1,0,'C');
$pdf->Cell(20,5,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',10);
while(!$r->EOF){
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(65/$pdf->GetStringWidth('M')));
	$pdf->Cell(90,5,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(30,5,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(25,5,muestraFecha($r->fields['fecha']),0,0,'C');
	$pdf->Cell(30,5,muestraFloat($r->fields['montodoc']),0,0,'R');
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(65/$pdf->GetStringWidth('M')));
	$pdf->Cell(115,5,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(10,5,$r->fields['abreviacion'],0,0,'C');
	$nrodoccom = (!empty($r->fields['nrodoccom'])) ? $r->fields['nrodoccom'] : "OP";
		$pdf->Cell(30,5,$nrodoccom,0,0,'C');
	$fechacom = (!empty($r->fields['fechacom'])) ? muestraFecha($r->fields['fechacom']) : "SIN IMPUTACION";
		$pdf->Cell(25,5,$fechacom,0,0,'C');
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
	}

	if($r->fields['status'] != 3)
		$total_sp += $r->fields['montodoc'];
	
	$pdf->Cell(20,5,$status,0,0,'C');
	$hay_ue = next($desc_unidad);
	$hay_pv = next($desc_proveedor);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(65,4, $desc_unidad[$i],0, 0,'L');
    		$pdf->Cell(30,4, '',0, '','L');
    		$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(65,4, $desc_proveedor[$i],0, '','L');
			$pdf->Cell(25,4,'',0,0,'C');
			$pdf->Cell(30,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(20,4, '',0, '','C');
    		$hay_ue = next($desc_unidad);
			$hay_pv = next($desc_proveedor);
  		}
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(120,4,'Total:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($total_sp),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(155,4,'Bs.F.: '.muestraFloat($total_sp/1000),0,0,'R');
}


//ORDENES DE PAGO CON CHEQUES RELACIONADOS
$q = "SELECT op.nrodoc, op.id_proveedor, op.id_unidad_ejecutora, op.fecha, ue.descripcion AS unidad_ejecutora, p.nombre AS proveedor, rc.nrodoc AS nrodocche, ch.nro_cheque, rc.monto AS pagado, op.montodoc AS montodoc, ";
$q.= "mp.nrodoc AS nrodoccom, mp.fechadoc AS fechacom, op.status, td.abreviacion ";
$q.= "FROM finanzas.orden_pago op ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (op.id_unidad_ejecutora = ue.id AND ue.id_escenario = '$escEnEje') ";
$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
$q.= "LEFT JOIN finanzas.relacion_cheque rc ON (op.nrodoc = rc.nroref) ";
$q.= "INNER JOIN finanzas.cheques ch ON (rc.nrodoc = ch.nrodoc) ";
$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (sp.nrodoc = op.nroref) ";
$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
$q.= "LEFT JOIN puser.tipos_documentos td ON (substr(sp.nroref,1,3) = td.id ) ";
$q.= "WHERE op.nrodoc IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.relacion_cheque) ";
$q.= !empty($id_status) ? "AND op.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND op.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND op.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND op.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_ue) ? "AND op.id_unidad_ejecutora = '$id_ue' " : "";
$q.= !empty($tipoCom) ? "AND COALESCE(mp.tipdoc,'099') = '$tipoCom' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "ORDER BY op.nrodoc ";
//die($q);
$r = $conn->Execute($q);
$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(352,8,'ORDENES DE PAGO CON CHEQUES RELACIONADOS',1,1,'C');
//$pdf->Ln();
$pdf->Cell(55,4,'Unidad Ejecutora',1,0,'C');
$pdf->Cell(28,4,'Nº Documento',1,0,'C');
$pdf->Cell(24,4,'Fecha',1,0,'C');
$pdf->Cell(35,4,'Monto',1,0,'C');
$pdf->Cell(55,4,'Beneficiario',1,0,'C');
$pdf->Cell(25,4,'Tipo Doc.',1,0,'C');
$pdf->Cell(25,4,'Compromiso',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(25,4,'Nº Cheque',1,0,'C');
$pdf->Cell(35,4,'Monto Cheque',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
$existe = array();
while(!$r->EOF){
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(55/$pdf->GetStringWidth('M')));
	$pdf->Cell(55,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(28,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(24,4,muestraFecha($r->fields['fecha']),0,0,'C');
	$pdf->Cell(35,4,muestraFloat($r->fields['montodoc']),0,0,'R');
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(55/$pdf->GetStringWidth('M')));
	$pdf->Cell(55,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(25,4,$r->fields['abreviacion'],0,0,'C');
	$nrodoccom = (!empty($r->fields['nrodoccom'])) ? $r->fields['nrodoccom'] : "ORDEN DE PAGO";
		$pdf->Cell(25,4,$nrodoccom,0,0,'C');
	$fechacom = (!empty($r->fields['fechacom'])) ? muestraFecha($r->fields['fechacom']) : "SIN IMPUTACION";
		$pdf->Cell(25,4,$fechacom,0,0,'C');
	$pdf->Cell(25,4,$r->fields['nro_cheque'],0,0,'C');
	$pdf->Cell(35,4,muestraFloat($r->fields['pagado']),0,0,'R');
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
	}

	$pdf->Cell(20,4,$status,0,0,'C');
	/*SE BUSCA SI EL NUMERO DE DOCUMENTO YA SE RELACIONO A OTRO PAGO PARA NO SUMARLO*/
	$indice = array_search($r->fields['nrodoc'],$existe);
	if($indice===false){
		$total_cc+= $r->fields['montodoc'];
		$existe[] = $r->fields['nrodoc'];
	}
	$total_pc+= $r->fields['pagado'];
	$hay_ue = next($desc_unidad);
	$hay_pv = next($desc_proveedor);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(55,4, $desc_unidad[$i],0, 0,'L');
    		$pdf->Cell(28,4, '',0, '','L');
    		$pdf->Cell(24,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(55,4, $desc_proveedor[$i],0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(20,4, '',0, '','L');
    		$hay_ue = next($desc_unidad);
			$hay_pv = next($desc_proveedor);
  		}
	$r->movenext();
	$pdf->Ln();
	
}

$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(60,4,'',0,0,'C');
$pdf->Cell(57,4,'Total Compromiso:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($total_cc),0,0,'R');	
$pdf->Cell(75,4,'',0,0,'C');
$pdf->Cell(60,4,'Total Pagado:',0,0,'R');
$pdf->Cell(35,4,muestraFloat($total_pc),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(152,4,'Bs.F.: '.muestraFloat($total_cc),0,0,'R');
	$pdf->Cell(170,4,'Bs.F.: '.muestraFloat($total_pc),0,0,'R');	
}

// ORDENES DE PAGO CON OTROS PAGOS RELACIONADOS

$q = "SELECT op.nrodoc, op.id_proveedor, op.id_unidad_ejecutora, op.fecha, ue.descripcion AS unidad_ejecutora, p.nombre AS proveedor, rop.nrodoc AS nrodocotp, otp.nro_otros_pagos, rop.monto AS pagado, op.montodoc AS montodoc, ";
$q.= "mp.nrodoc AS nrodoccom, mp.fechadoc AS fechacom, op.status, td.abreviacion ";
$q.= "FROM finanzas.orden_pago op ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (op.id_unidad_ejecutora = ue.id AND ue.id_escenario = '$escEnEje') ";
$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
$q.= "LEFT JOIN finanzas.relacion_otros_pagos rop ON (op.nrodoc = rop.nroref) ";
$q.= "INNER JOIN finanzas.otros_pagos otp ON (rop.nrodoc = otp.nrodoc) ";
$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (sp.nrodoc = op.nroref) ";
$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
$q.= "LEFT JOIN puser.tipos_documentos td ON (substr(sp.nroref,1,3) = td.id ) ";
$q.= "WHERE op.nrodoc IN ( SELECT COALESCE(nroref::char(13)) FROM finanzas.relacion_otros_pagos) ";
$q.= !empty($id_status) ? "AND op.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND op.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND op.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND op.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_ue) ? "AND op.id_unidad_ejecutora = '$id_ue' " : "";
$q.= !empty($tipoCom) ? "AND COALESCE(mp.tipdoc,'099') = '$tipoCom' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "ORDER BY op.nrodoc ";	
$r = $conn->Execute($q);
$pdf->Ln(10);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(352,8,'ORDENES DE PAGO CON OTROS PAGOS RELACIONADOS',1,1,'C');
$pdf->Cell(55,4,'Unidad Ejecutora',1,0,'C');
$pdf->Cell(28,4,'Nº Documento',1,0,'C');
$pdf->Cell(24,4,'Fecha',1,0,'C');
$pdf->Cell(35,4,'Monto',1,0,'C');
$pdf->Cell(55,4,'Beneficiario',1,0,'C');
$pdf->Cell(25,4,'Tipo Doc.',1,0,'C');
$pdf->Cell(25,4,'Compromiso',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(25,4,'Nº Pago',1,0,'C');
$pdf->Cell(35,4,'Monto Pago',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
while(!$r->EOF){
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(55/$pdf->GetStringWidth('M')));
	$pdf->Cell(55,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(28,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(24,4,muestraFecha($r->fields['fecha']),0,0,'C');
	$pdf->Cell(35,4,muestraFloat($r->fields['montodoc']),0,0,'R');
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(55/$pdf->GetStringWidth('M')));
	$pdf->Cell(55,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(25,4,$r->fields['abreviacion'],0,0,'C');
	$nrodoccom = (!empty($r->fields['nrodoccom'])) ? $r->fields['nrodoccom'] : "ORDEN DE PAGO";
		$pdf->Cell(25,4,$nrodoccom,0,0,'C');
	$fechacom = (!empty($r->fields['fechacom'])) ? muestraFecha($r->fields['fechacom']) : "SIN IMPUTACION";
		$pdf->Cell(25,4,$fechacom,0,0,'C');
	$pdf->Cell(25,4,$r->fields['nro_otros_pagos'],0,0,'C');
	$pdf->Cell(35,4,muestraFloat($r->fields['pagado']),0,0,'R');
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
	}

	$pdf->Cell(20,4,$status,0,0,'C');
	/*SE BUSCA SI EL NUMERO DE DOCUMENTO YA SE RELACIONO A OTRO PAGO PARA NO SUMARLO*/
	$indice = array_search($r->fields['nrodoc'],$existe);
	if($indice===false){
		$monto_co+= $r->fields['montodoc'];
		$existe[] = $r->fields['nrodoc'];
	}
	
	$monto_po+= $r->fields['pagado'];
	$hay_ue = next($desc_unidad);
	$hay_pv = next($desc_proveedor);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(55,4, $desc_unidad[$i],0, 0,'L');
    		$pdf->Cell(28,4, '',0, '','L');
    		$pdf->Cell(24,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(55,4, $desc_proveedor[$i],0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(20,4, '',0, '','L');
    		$hay_ue = next($desc_unidad);
			$hay_pv = next($desc_proveedor);
  		}
	$r->movenext();
	$pdf->Ln();
	
}

$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(60,4,'',0,0,'C');
$pdf->Cell(57,4,'Total Compromiso:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($monto_co),0,0,'R');	
$pdf->Cell(75,4,'',0,0,'C');
$pdf->Cell(60,4,'Total Pagado:',0,0,'R');
$pdf->Cell(35,4,muestraFloat($monto_po),0,0,'R');	

if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(152,4,'Bs.F.: '.muestraFloat($monto_co),0,0,'R');
	$pdf->Cell(170,4,'Bs.F.: '.muestraFloat($monto_po),0,0,'R');	
}

$pdf->SetFont('Courier','',8);


	


$pdf->Output();
?>