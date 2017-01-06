<?
	include("comun/ini.php");
	include("Constantes.php");
	$fecha_desde = $_REQUEST['fecha_desde'];
	$fecha_hasta = $_REQUEST['fecha_hasta'];
	$id_proveedor = $_REQUEST['id_proveedor'];
	$tipoProv = $_REQUEST['tipoprov'];
	$id_status = $_REQUEST['status'];
	$id_cuenta = $_REQUEST['id_cuenta'];
	
	
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
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$textoCabecera.= $id_espectaculo;
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(300, 20); 
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
				$tipoOrden = "LISTADO DE PAGOS";
			
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

//BUSCA LOS  CHEQUES
$q = "SELECT ch.nrodoc,rch.nroref AS nroop, ch.id_proveedor, ch.fecha, p.nombre AS proveedor, ch.nombre_beneficiario, ch.concepto, SUM(rch.monto) AS monto, ch.status, ch.nro_cheque, cb.nro_cuenta AS cuenta, b.descripcion AS banco ";
$q.= "FROM finanzas.cheques ch ";
$q.= "INNER JOIN finanzas.relacion_cheque rch ON (ch.nrodoc = rch.nrodoc) ";
$q.= "INNER JOIN puser.proveedores p ON (ch.id_proveedor = p.id) ";
$q.= "INNER JOIN finanzas.cuentas_bancarias cb ON (ch.nro_cuenta = cb.id) ";
$q.= "INNER JOIN public.banco b ON (ch.id_banco = b.id) ";
$q.= "WHERE 1=1 ";
$q.= !empty($id_status) ? "AND ch.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND ch.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND ch.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND ch.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_cuenta) ? "AND ch.nro_cuenta = '$id_cuenta' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "GROUP BY 1,2,3,4,5,6,7,9,10,11,12 ";
$q.= "ORDER BY ch.nrodoc ";
//die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(390,8,'LISTADO DE CHEQUES',1,1,'C');
//$pdf->Ln();
$pdf->Cell(20,4,'Fecha',1,0,'C');
$pdf->Cell(25,4,'Nº OP',1,0,'C');
$pdf->Cell(20,4,'Nº Doc',1,0,'C');
$pdf->Cell(20,4,'Nº Ch',1,0,'C');
$pdf->Cell(60,4,'Proveedor',1,0,'C');
$pdf->Cell(60,4,'Beneficiario',1,0,'C');
$pdf->Cell(65,4,'Concepto',1,0,'C');
$pdf->Cell(35,4,'Monto',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Cell(40,4,'Banco',1,0,'C');
$pdf->Cell(25,4,'Cuenta',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
while(!$r->EOF){
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(60/$pdf->GetStringWidth('M')));
	$desc_benef = dividirStr($r->fields['nombre_beneficiario'], intval(60/$pdf->GetStringWidth('M')));
	$desc_concepto = dividirStr($r->fields['concepto'], intval(60/$pdf->GetStringWidth('M')));
	$desc_banco = dividirStr($r->fields['banco'], intval(40/$pdf->GetStringWidth('M')));
	$aProv = count($desc_proveedor);
	$aBenef = count($desc_benef);
	$aConcep = count($desc_concepto);
	$aBanco = count($desc_banco);
	
	if(($aProv>=$aBenef) && ($aProv>=$aConcep) && ($aProv>=$aBanco)){
		$aux = $aProv;
	}elseif(($aBenef>$aProv) && ($aBenef>=$aConcep) && ($aBenef>=$aBanco)){
		$aux = $aBenef;
	}elseif(($aBanco>$aProv) && ($aBanco>=$aConcep) && ($aBanco>$aBenef)){
		$aux = $aBanco;
	}else{
		$aux = $aConcep;
	}
			
	$pdf->Cell(20,4,muestrafecha($r->fields['fecha']),0,0,'L');
	$pdf->Cell(25,4,$r->fields['nroop'],0,0,'C');
	$pdf->Cell(20,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(20,4,$r->fields['nro_cheque'],0,0,'C');
	$pdf->Cell(60,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(60,4,utf8_decode($desc_benef[0]),0,0,'L');
	$pdf->Cell(65,4,utf8_decode($desc_concepto[0]),0,0,'L');
	$pdf->Cell(35,4,muestrafloat($r->fields['monto']),0,0,'R');
	switch($r->fields['status']){
	 	case "0":
			$status = "Emitido";
		break;
		case "1":
			$status = "Anulado";
		break;
	}
	$pdf->Cell(20,4,$status,0,0,'C');
	$pdf->Cell(40,4,utf8_decode($desc_banco[0]),0,0,'L');
	$pdf->Cell(25,4,$r->fields['cuenta'],0,0,'C');
	$total_ch += $r->fields['monto'];
	
	for($i=1;$i<=$aux-1;$i++){
		$pdf->Ln();
		$pdf->Cell(20,4,'',0,0,'L');
		$pdf->Cell(25,4,'',0,0,'C');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(60,4,utf8_decode($desc_proveedor[$i]),0,0,'L');
		$pdf->Cell(60,4,utf8_decode($desc_benef[$i]),0,0,'L');
		$pdf->Cell(65,4,utf8_decode($desc_concepto[$i]),0,0,'L');
		$pdf->Cell(35,4,'',0,0,'R');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(40,4,utf8_decode($desc_banco[$i]),0,0,'L');
		$pdf->Cell(20,4,'',0,0,'C');
	}
	
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(120,4,'Total:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($total_ch),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(155,4,'Bs.F.: '.muestraFloat($total_ch/1000),0,0,'R');
}

$pdf->Ln(12);
//die();
//BUSCA LOS  OTROS PAGOS
$q = "SELECT op.nrodoc, rop.nroref AS nroop, op.id_proveedor, op.fecha, p.nombre AS proveedor, op.descripcion AS concepto, SUM(rop.monto) AS monto, op.status, op.nro_otros_pagos, op.nro_control, cb.nro_cuenta AS cuenta, b.descripcion AS banco ";
$q.= "FROM finanzas.otros_pagos op ";
$q.= "INNER JOIN finanzas.relacion_otros_pagos rop ON (op.nrodoc = rop.nrodoc) ";
$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
$q.= "INNER JOIN finanzas.cuentas_bancarias cb ON (op.nro_cuenta = cb.id) ";
$q.= "INNER JOIN public.banco b ON (op.id_banco = b.id) ";
$q.= "WHERE 1=1 ";
$q.= !empty($id_status) ? "AND op.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND op.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND op.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND op.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_cuenta) ? "AND op.nro_cuenta = '$id_cuenta' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "GROUP BY 1,2,3,4,5,6,8,9,10,11,12 ";
$q.= "ORDER BY op.nrodoc ";
// die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(375,8,'LISTADO DE TRANSFERENCIAS',1,1,'C');
//$pdf->Ln();
$pdf->Cell(20,4,'Fecha',1,0,'C');
$pdf->Cell(25,4,'Nº OP',1,0,'C');
$pdf->Cell(20,4,'Nº Doc',1,0,'C');
$pdf->Cell(20,4,'Nº Trans',1,0,'C');
$pdf->Cell(65,4,'Proveedor',1,0,'C');
$pdf->Cell(70,4,'Concepto',1,0,'C');
$pdf->Cell(35,4,'Monto',1,0,'C');
$pdf->Cell(35,4,'Nº Control',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Cell(40,4,'Banco',1,0,'C');
$pdf->Cell(25,4,'Cuenta',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
while(!$r->EOF){
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(65/$pdf->GetStringWidth('M')));
	$desc_concepto = dividirStr($r->fields['concepto'], intval(70/$pdf->GetStringWidth('M')));
	$desc_banco = dividirStr($r->fields['banco'], intval(40/$pdf->GetStringWidth('M')));
	$aProv = count($desc_proveedor);
	$aConcep = count($desc_concepto);
	$aBanco = count($desc_banco);
	
	if(($aProv>=$aConcep) && ($aProv>=$aBanco)){
		$aux = $aProv;
	}elseif(($aBanco>$aProv) && ($aBanco>=$aConcep)){
		$aux = $aBanco;
	}else{
		$aux = $aConcep;
	}
			
	$pdf->Cell(20,4,muestrafecha($r->fields['fecha']),0,0,'L');
	$pdf->Cell(25,4,$r->fields['nroop'],0,0,'L');
	$pdf->Cell(20,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(20,4,$r->fields['nro_otros_pagos'],0,0,'C');
	$pdf->Cell(65,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(70,4,utf8_decode($desc_concepto[0]),0,0,'L');
	$pdf->Cell(35,4,muestrafloat($r->fields['monto']),0,0,'R');
	$pdf->Cell(35,4,$r->fields['nro_control'],0,0,'C');
	switch($r->fields['status']){
	 	case "0":
			$status = "Transferido";
		break;
		case "1":
			$status = "Anulado";
		break;
	}
	$pdf->Cell(20,4,$status,0,0,'C');
	$pdf->Cell(40,4,utf8_decode($desc_banco[0]),0,0,'L');
	$pdf->Cell(25,4,$r->fields['cuenta'],0,0,'C');
	$total_op += $r->fields['monto'];
	
	for($i=1;$i<=$aux-1;$i++){
		$pdf->Ln();
		$pdf->Cell(20,4,'',0,0,'L');
		$pdf->Cell(25,4,'',0,0,'C');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(65,4,utf8_decode($desc_proveedor[$i]),0,0,'L');
		$pdf->Cell(70,4,utf8_decode($desc_concepto[$i]),0,0,'L');
		$pdf->Cell(35,4,'',0,0,'R');
		$pdf->Cell(35,4,'',0,0,'C');
		$pdf->Cell(20,4,'',0,0,'C');
		$pdf->Cell(40,4,utf8_decode($desc_banco[$i]),0,0,'L');
	}
	
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(120,4,'Total:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($total_op),0,0,'R');

if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(155,4,'Bs.F.: '.muestraFloat($total_op/1000),0,0,'R');
}

$pdf->SetFont('Courier','',8);


	


$pdf->Output();
?>