<?
include("comun/ini.php");

$id_cta_banc = $_GET['id_cta'];
$anio = $_GET['anio'];
$mes = $_GET['mes'];
$resumen = $_GET['resumen'];
$detalle = $_GET['detalle'];

$q = "SELECT id FROM contabilidad.conciliacion WHERE id_cta_banc = $id_cta_banc AND fecha_desde = '$anio-$mes-01'";
$r = $conn->Execute($q);
$id_conc = $r->fields['id'];
$cuenta_banc = new cuentas_bancarias;
$cuenta_banc->get($conn, $id_cta_banc);
$conciliacion = new conciliacionBancaria;
$conciliacion->get($conn, $id_conc);

if ($resumen)
{
	/*$qr = "SELECT COALESCE(SUM(debe), 0) AS debe, COALESCE(SUM(haber), 0) AS haber FROM contabilidad.com_det ";
	$qr.= "INNER JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id) ";
	$qr.= "WHERE com_enc.id_conciliacion = $id_conc AND ";
	$qr.= "com_det.id_cta = (SELECT id_plan_cuenta FROM finanzas.cuentas_bancarias WHERE id = $id_cta_banc) ";
	
	$rr = $conn->Execute($qr);*/
}

if ($detalle)
{
	$qd = "SELECT com_enc.origen, com_enc.num_doc, com_enc.fecha, com_enc.descrip, (CASE com_det.debe WHEN 0 THEN -com_det.haber else com_det.debe END) as monto FROM contabilidad.com_enc ";
	$qd.= "INNER JOIN contabilidad.com_det ON (com_det.id_com = com_enc.id) ";
	$qd.= "WHERE com_enc.id_conciliacion = $id_conc AND com_enc.status = 'T' ";
	$qd.= "AND com_det.id_cta = (SELECT id_plan_cuenta FROM finanzas.cuentas_bancarias where id = $id_cta_banc) ";
	$qd.= "ORDER BY com_enc.origen, com_enc.fecha ";

	$rd = $conn->Execute($qd);
}

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

class PDF extends FPDF
{
	var $anio;
	var $mes;
	var $resumen;
	var $detalle;
	var $id_cta_banc;
	
	// Definen el ancho de las celdas en el reporte resumido
	var $tituloRW = 100;
	var $mesAnioRW = 40;
	var $saldoLibroRW = 60;
	var $saldoBancoRW = 60;
	var $descRW = 70;
	
	// Definen el ancho de las celdas en el reporte detallado
	var $tituloDW = 100;
	var $cuentaDW = 57;
	var $transDW = 13;
	var $numDocDW = 25;
	var $fechaDW = 20;
	var $conceptoDW = 101;
	var $ejePerTcCompDW = 30;	// Preguntar que significa esto
	var $auxiliarDW = 44;	
	var $edoCtaDW = 44;
	
	//Cabecera de página
	function Header()
	{
		$this->SetFillColor(240);
		$this->SetLeftMargin(15);
		$this->SetFont('Courier','',6);
		$this->Ln(1);
		$this->Image ("images/logoa.jpg",15,4,26);//logo a la izquierda 
		$this->SetXY(42, 20); 
		$textoCabecera = "REPUBLICA BOLIVARIANA DE VENEZUELA\n";
		$textoCabecera.= "TOCUYITO, ESTADO CARABOBO\n";
		$textoCabecera.= "ALCALDIA DEL MUNICIPIO LIBERTADOR\n";
		$this->MultiCell(50,2, $textoCabecera, 0, 'L');

		$this->SetXY(215, 20); 
		$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
		//$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
		$this->MultiCell(50,2, $textoDerecha, 0, 'L');
		
		$this->Ln(12);
		$this->SetFont('Courier','b',12);
		$titulo = "Conciliación Bancaria";
		$this->MultiCell(0, 2, utf8_decode($titulo), 0, 'C');
	}

	//Pie de página
	function Footer()
	{
		//Arial italic 8
		$this->SetFont('Arial','I', 7);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

//Creación del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->anio = $anio;
$pdf->mes = $mes;
$pdf->resumen = $resumen;
$pdf->detalle = $detalle;

if ($resumen)
{
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetLeftMargin(10);
	$pdf->SetFillColor(240);
	$pdf->Ln(6);
	$relleno = 40;
	
	$pdf->SetFont('Courier','B', 9);
	$pdf->Multicell(0, 6, utf8_decode($cuenta_banc->banco->descripcion)." / ".$cuenta_banc->nro_cuenta, 0, 'C');
	$pdf->Ln(6);
	
	$pdf->SetFont('Courier','', 8);
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->mesAnioRW, 6, utf8_decode("Mes:".$mes." / Año:".$anio), 0, '', 'C');
	$pdf->Ln(5);

	$pdf->SetFont('Courier', 'B', 8);
	$pdf->Cell($pdf->descRW, 6, '', 0);
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->saldoLibroRW, 6, "Saldo S/Libro", 0, '', 'C');
	$pdf->Cell($pdf->saldoBancoRW, 6, "Saldo S/Banco", 0, '', 'C');
	$pdf->Ln();
	
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->descRW, 6, 'Saldos Finales', 0);
	$pdf->SetFont('Courier', '', 8);
	$pdf->Cell($pdf->saldoLibroRW, 6, muestraFloat($conciliacion->saldo_final_libro), 0, '', 'R');
	$pdf->Cell($pdf->saldoBancoRW, 6, muestraFloat($conciliacion->saldo_final_banco), 0, '', 'R');
	$pdf->Ln(8);

	$pdf->SetFont('Courier', 'B', 8);
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->descRW, 6, utf8_decode('Partidas de Conciliación'), 0);
	$pdf->Ln();
	
	$pdf->SetFont('Courier', '', 8);
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->descRW, 6, utf8_decode("Partidas en Tránsito"), 0);
	$pdf->Cell($pdf->saldoLibroRW, 6, '', 0);
	$pdf->Cell($pdf->saldoBancoRW, 6, muestraFloat($conciliacion->saldo_transitorio), 0, '', 'R');
	$pdf->Ln();
	
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->descRW, 6, "Partidas no Registradas", 0);
	$pdf->SetFont('Courier', '', 8);
	$pdf->Cell($pdf->saldoLibroRW, 6, muestraFloat($conciliacion->saldo_no_registrado), 0, '', 'R');
	$pdf->Cell($pdf->saldoBancoRW, 6, '', 0);
	$pdf->Ln(10);
	
	$pdf->SetFont('Courier', 'B', 8);
	$pdf->Cell($relleno, 4, '');
	$pdf->Cell($pdf->descRW, 4, "Saldo Conciliado", 0);
	$pdf->Cell($pdf->saldoLibroRW, 4, muestraFloat($conciliacion->saldo_conciliado), TB, '', 'R');
	$pdf->Cell($pdf->saldoBancoRW, 4, muestraFloat($conciliacion->saldo_conciliado), TB, '', 'R');
	$pdf->Ln();
	$pdf->Line(10+$relleno+$pdf->descRW, $pdf->GetY()+1, 10+$relleno+$pdf->descRW+$pdf->saldoLibroRW+$pdf->saldoBancoRW, $pdf->GetY()+1);
}

if ($detalle)
{
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetLeftMargin(10);
	$pdf->SetFillColor(240);
	$pdf->Ln(8);

	$pdf->SetFont('Courier', 'B', 8);
	$pdf->MultiCell(0, 4, "Cuenta: ".$cuenta_banc->banco->descripcion." / ".$cuenta_banc->nro_cuenta, 0);
	$pdf->Ln();
	
	$pdf->Cell($pdf->transDW, 4, "Trans.", TLB, '', 'C');
	$pdf->Cell($pdf->numDocDW, 4, utf8_decode("Nº"), TB, '', 'C');
	$pdf->Cell($pdf->fechaDW, 4, "Fecha", TB, '', 'C');
	$pdf->Cell($pdf->conceptoDW, 4, "Concepto", TB, '', 'C');
	$pdf->Cell($pdf->ejePerTcCompDW, 4, "Eje/Per/Tc/Comp", TB, '', 'C');
	$pdf->Cell($pdf->auxiliarDW, 4, "Auxiliar", TB, '', 'C');
	$pdf->Cell($pdf->edoCtaDW, 4, "Edo. Cuenta", TBR, '', 'C');
	$pdf->Ln();
	
	$pdf->SetFont('Courier','', 8);
	$pdf->Cell($pdf->transDW+$pdf->numDocDW+$pdf->fechaDW, 4, '', L);
	$pdf->Cell($pdf->conceptoDW+$pdf->ejePerTcCompDW, 4, "Saldo Final...", 0);
	$pdf->Cell($pdf->auxiliarDW, 4, muestraFloat($conciliacion->saldo_final_libro), 0, '', 'R');
	$pdf->Cell($pdf->edoCtaDW, 4, muestraFloat($conciliacion->saldo_final_banco), R, '', 'R');
	$pdf->Ln();
	
	$pdf->SetFont('Courier', 'B', 8);
	$pdf->multiCell(0, 4, utf8_decode("Partidas en Tránsito"), LR);
	$totalTrans = 0;
	while (!$rd->EOF)
	{
		$tipo = $rd->fields['origen'];
		if ($tipo == "CHQ")
			$origen_desc = "Cheque";
		else if ($tipo == "DEP")
			$origen_desc = utf8_decode("Depósito");
		else if ($tipo == "TRA" || $tipo == "TRM")
			$origen_desc = "Transferencia";
		else if ($tipo == "OP")
			$origen_desc = utf8_decode("Orden de Pago");
		else if ($tipo == "ND")
			$origen_desc = utf8_decode("Nota de Débito");
		else if ($tipo == "NC")
			$origen_desc = utf8_decode("Nota de Crédito");
		
		$pdf->SetFont('Courier', 	'', 8);
		$pdf->Cell($pdf->transDW, 4, $tipo, L);
		$pdf->Cell($pdf->numDocDW+$pdf->fechaDW, 4, $origen_desc, 0);
		$pdf->Cell($pdf->conceptoDW+$pdf->ejePerTcCompDW+$pdf->auxiliarDW+$pdf->edoCtaDW, 4, '', R);
		$pdf->Ln();
		$sum = 0;
		while (!$rd->EOF && substr($tipo, 0, 2)==substr($rd->fields['origen'], 0, 2))
		{
			$descCom = dividirStr(utf8_decode($rd->fields['descrip']), intval($pdf->conceptoDW/$pdf->GetStringWidth('M')));

			$pdf->Cell($pdf->transDW, 4, '', L);
			$pdf->Cell($pdf->numDocDW, 4, $rd->fields['num_doc'], 0);
			$pdf->Cell($pdf->fechaDW, 4, muestraFecha($rd->fields['fecha']), 0, '', 'C');
			$pdf->Cell($pdf->conceptoDW, 4, $descCom[0], 0);
			$pdf->Cell($pdf->ejePerTcCompDW, 4, '', 0);
			$pdf->Cell($pdf->auxiliarDW, 4, '', 0);
			$pdf->Cell($pdf->edoCtaDW, 4, muestraFloat($rd->fields['monto']), R, '', 'R');
			$sum += $rd->fields['monto'];
			$pdf->Ln();
			
			while(next($descCom))
			{
				$pdf->Ln();
				$pdf->Cell($pdf->transDW, 4, '', L);
				$pdf->Cell($pdf->numDocDW, 4, '', 0);
				$pdf->Cell($pdf->fechaDW, 4, '', 0, '', 'C');
				$pdf->Cell($pdf->conceptoDW, 4, current($descCom), 0);
				$pdf->Cell($pdf->ejePerTcCompDW, 4, '', 0);
				$pdf->Cell($pdf->auxiliarDW, 4, '', 0);
				$pdf->Cell($pdf->edoCtaDW, 4, '', R, '', 'R');
			}
			
			$rd->movenext();
		}

		$pdf->SetFont('Courier', 'B', 8);
		$pdf->Cell($pdf->transDW+$pdf->numDocDW+$pdf->fechaDW, 4, '', L, '', 'L', 1);
		$pdf->Cell($pdf->conceptoDW+$pdf->ejePerTcCompDW+$pdf->auxiliarDW, 4, "Total ".$origen_desc, 0, '', 'L', 1);
		$pdf->Cell($pdf->edoCtaDW, 4, muestraFloat($sum), R, '', 'R', 1);
		$pdf->Ln();
		$totalTrans += $sum;
	}
	
	$pdf->Cell($pdf->transDW+$pdf->numDocDW+$pdf->fechaDW+$pdf->conceptoDW+$pdf->ejePerTcCompDW+$pdf->auxiliarDW+$pdf->edoCtaDW, 2, '', LR);
	$pdf->Ln();
	$pdf->SetFont('Courier', 'B', 8);
	$pdf->Cell($pdf->transDW+$pdf->numDocDW, 4, '', L, '', 'L', 1);
	$pdf->Cell($pdf->fechaDW+$pdf->conceptoDW+$pdf->ejePerTcCompDW+$pdf->auxiliarDW, 4, utf8_decode("Total Partidas en tránsito"), 0, '', 'L', 1);
	$pdf->Cell($pdf->edoCtaDW, 4, muestraFloat($totalTrans), R, '', 'R', 1);
	$pdf->Ln();
	$pdf->Cell($pdf->transDW+$pdf->numDocDW+$pdf->fechaDW+$pdf->conceptoDW+$pdf->ejePerTcCompDW+$pdf->auxiliarDW+$pdf->edoCtaDW, 1, '', LR);
	$pdf->Ln();
	
	$pdf->SetFont('Courier', 'B', 8);
	$pdf->Cell($pdf->transDW+$pdf->numDocDW+$pdf->fechaDW, 4, '', LB, '', 'L', 1);
	$pdf->Cell($pdf->conceptoDW+$pdf->ejePerTcCompDW, 4, "Saldo Final Conciliado ", B, '', 'L', 1);
	$pdf->Cell($pdf->auxiliarDW, 4, muestraFloat($conciliacion->saldo_conciliado), B, '', 'R', 1);
	$pdf->Cell($pdf->edoCtaDW, 4, muestraFloat($conciliacion->saldo_conciliado), RB, '', 'R', 1);
}

$pdf->Output();
?>
