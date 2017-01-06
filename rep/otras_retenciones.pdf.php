<?
	include("comun/ini.php");
	include ("Constantes.php");
	$id=$_REQUEST['id'];//die($nro_recibo);
	$oOrdenPago = new orden_pago();
	$oOrdenPago->get($conn, $_GET['id'], $escEnEje);
	
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
			$this->SetLeftMargin(15);
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

			$this->SetXY(150, 20); 
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
				$tipoOrden = "COMPROBANTE DE RETENCIONES";
			
			$this->Text(85, 40, $tipoOrden);
			$this->Line(15, 41, 190, 41);
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
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

//$oespectaculo = new espectaculo;
//$oespectaculo_id_contribuyente = $oespectaculo->id_contribuyente;die($oespectaculo_id_contribuyente);
//$oespectaculo->get($conn, $oespectaculo->id_contribuyente);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $oespectaculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
//$pdf->Ln();//$pdf->Ln();
//$pdf->Ln(5);

$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, utf8_decode('RETENCION Nº:'),0, '','L' );
$q = "SELECT rr.nrocorret FROM finanzas.relacion_retenciones_orden rr ";
$q.= "INNER JOIN finanzas.retenciones_adiciones ra ON (rr.codret = ra.id) ";
$q.= "WHERE nrodoc = '$id' AND ra.es_iva <> '1'";
$row = $conn->Execute($q);
$pdf->Cell(60,4, $row->fields['nrocorret'],0, '','L');
$pdf->Ln();

$pdf->SetFont('Courier','',8);
$pdf->Cell(40,4, 'NOMBRE O RAZON SOCIAL:',0, '','L' );
$pdf->Cell(60,4, $oOrdenPago->proveedor,0, '','L');

$pdf->Cell(20,4, 'RIF: ',0, '','L' );
$pdf->Cell(30,4, $oOrdenPago->rif_proveedor,0, '','C');

$pdf->Ln();
$pdf->Cell(40,4, 'TIPO DE CONTRIBUYENTE:',0, '','L' );
if($oOrdenPago->tipo_contribuyente== 'ORDINARIO')
	$tipo_contrib = 'JURIDICO';
else
	$tipo_contrib = 'NATURAL';
$pdf->Cell(55,4, $tipo_contrib,0, '','L');


$pdf->Ln();
$pdf->Cell(40,4, 'DIRECCION FISCAL:',0, '','L');
$pdf->Cell(55,4, utf8_decode($oOrdenPago->dir_proveedor),0, '','L');

$pdf->Ln();
$pdf->Cell(40,4,'TIPO DE DOCUMENTO:',0,'','L');
$pdf->Cell(55,4, $oOrdenPago->tipdoc->descripcion,0,'','L');

$pdf->Ln();
$pdf->Cell(40,4,'DESCRIPCION:',0,'','L');
	$desc_descripcion = dividirStr($oOrdenPago->descripcion, intval(140/$pdf->GetStringWidth('M')));
	$pdf->Cell(140,4, utf8_decode($desc_descripcion[0]),0,'','L');
	$hay_desc = next($desc_descripcion);
	for($i=1; $hay_desc!==false; $i++){
		$pdf->Ln();
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(140,4,utf8_decode($desc_descripcion[$i]),0,0,'L');
		$hay_desc = next($desc_descripcion);
	}

$pdf->Ln(4);
$pdf->Cell(175,0.1, '',1, '','C');


$e="SELECT SUM(base_imponible) AS base, SUM(monto_iva) AS monto_iva FROM finanzas.facturas WHERE nrodoc = '$id'";//die($e);
$t = $conn->Execute($e);
		if($t){
			$total_base = $t->fields['base'];
			$total_iva = $t->fields['monto_iva'];
			
		}
		
$pdf->Ln();
$pdf->Cell(120,8,'MONTO SIN IVA',0,'','C');
$pdf->Cell(55,8, muestraFloat($total_base),1,'LRBT','R');		
$pdf->Ln(8);
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(100,4, 'TIPO DE IMPUESTO',1, 'LRBT','C' );
$pdf->Cell(30,4, '%',1, 'LRBT','C' );
$pdf->Cell(45,4, ' ',1, 'LRBT','C' );
$pdf->Ln();

			$q = "SELECT rr.*, ra.descri AS descripcion FROM finanzas.relacion_retenciones_orden rr ";
			$q.= "INNER JOIN finanzas.retenciones_adiciones ra ON (rr.codret = ra.id) ";
			$q.= "WHERE nrodoc = '$id' AND ra.es_iva <> '1'";
			//die($q);
			$r = $conn->Execute($q) or die($q);
	$total_or = 0;
	while(!$r->EOF){
		$pdf->SetFont('Courier','',8);
		$desc_descripcion_ret = dividirStr($r->fields['descripcion'], intval(100/$pdf->GetStringWidth('M')));
		$hay_desc = next($desc_descripcion_ret);
		if($hay_desc==true)
			$aux = 'LR';
		else
			$aux = 'LRB';
		//die($aux);
		$pdf->Cell(100,4, utf8_decode($desc_descripcion_ret[0]),$aux,0,'L' );
		$pdf->Cell(30,4,muestraFloat($r->fields['porcen']),$aux,0,'C' ); 
		$pdf->Cell(45,4, muestraFloat($r->fields['mntret']),$aux,0,'R' );
		//Para ajustar el texto al campo
		//$hay_desc = next($desc_descripcion_ret);
		for($i=1; $hay_desc!==false; $i++){
			$pdf->Ln();
			$pdf->Cell(100,4, utf8_decode($desc_descripcion_ret[$i]),'LRB',0,'L' );
			$pdf->Cell(30,4,'','LRB',0,'C');
			$pdf->Cell(45,4,'','LRB',0,'C');
			$hay_desc = next($desc_descripcion_ret);
		}
		$total_or+= $r->fields['mntret'];
		$pdf->Ln();
		$r->movenext();
	}
	$pdf->Cell(130,4, 'TOTAL',1,'LRB','C');
	$pdf->Cell(45,4, muestraFloat($total_or),1,'LRB','R');
$pdf->Ln(4);
$pdf->Ln(4);

$mont_menos_ret = $total_base - $total_or;
//die($mont_menos_ret);
$pdf->Cell(130,15,'MONTO MENOS RETENCION',0,'','L');
$pdf->Cell(45,15, muestraFloat($mont_menos_ret),0,'','C');
$pdf->Ln(2);
$pdf->Ln(2);
$pdf->Cell(130,15,'IMPUESTO AL VALOR AGREGADO',0,'','L');
$pdf->Cell(45,15, muestraFloat($total_iva),0,'','C');
$pdf->Ln(2);
$pdf->Ln(2);
$sql = "SELECT SUM(mntret) AS monto_ret FROM finanzas.relacion_retenciones_orden ro ";
$sql.= "INNER JOIN finanzas.retenciones_adiciones ra ON (ro.codret = ra.id) ";
$sql.= "WHERE nrodoc = '$id' AND ra.es_iva = '1'";
$row = $conn->Execute($sql);
$pdf->Cell(130,15,'IMPUESTO AL VALOR AGREGADO RETENIDO',0,'','L');
$pdf->Cell(45,15, muestraFloat($row->fields['monto_ret']),0,'','C');
$pdf->Ln(2);
$pdf->Ln(2);
$pdf->Cell(130,15,'NETO A PAGAR',0,'','L');
$pdf->Cell(45,15, muestraFloat($mont_menos_ret + $total_iva - $row->fields['monto_ret']),0,'','C');

$pdf->Ln(7.5);
$pdf->Ln(7.5);
$pdf->Cell(175,0.1, '',1, '','C');
/*$anio_head = mb_convert_encoding("Año", "ISO-8859-1", "UTF-8");
$pdf->Cell(15,4, $anio_head.':',0, '','L' );
$pdf->SetFont('Courier','',8);
$pdf->Cell(15,4, $ovehiculo->anio_veh,0, '','L' ); */

$pdf->Ln(); 
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Courier','B',10);

$pdf->Cell(58,6, '',1, 'LRBT','C');
$pdf->Cell(59,6, '',1, 'LRBT','C');
$pdf->Cell(58,6, '',1, 'LRBT','C');
$pdf->Ln(); 
$pdf->Cell(58,6, 'ELABORADO',1, 'LRB','C');
$pdf->Cell(59,6, 'SELLO',1, 'LRB','C');
$pdf->Cell(58,6, 'APROBADO',1, 'LRB','C');

//$pdf->Ln();
//$pdf->Cell(175,0.2, '',1, '','C');

/*$pdf->Ln();
$pdf->SetFont('Courier','B',12);
$pdf->Cell(175,4, 'IMPUTACION PRESUPUESTARIA',0, '','C');

$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->SetFont('Courier','B',8);
$pdf->Cell(40,4, 'Partida Presupuestaria',0, '','C');
$pdf->Cell(100,4, 'Descripcion',0, '','L');
$pdf->Cell(35,4, 'Monto',0, '','C');

$cPartidas = $oOrden->getRelacionPartidas($conn, $oOrden->id, $escEnEje);

foreach($cPartidas as $partida){
	$pdf->Ln();
	$pdf->Cell(40,4, $partida->id_categoria_programatica."-".$partida->id_partida_presupuestaria,0, '','C');
	$pdf->Cell(100,4, $partida->partida_presupuestaria,0, '','L');
	$pdf->Cell(35,4, $partida->monto,0, '','C');
	$montoTotal += $partida->monto;
}
$pdf->Ln();
$pdf->Cell(175,0.2, '',1, '','C');

$pdf->Ln();
$pdf->Cell(140,4, 'TOTAL',0, '','R');
$pdf->Cell(35,4, $montoTotal,0, '','C');
$pdf->Ln();
*/

$pdf->Output();
?>