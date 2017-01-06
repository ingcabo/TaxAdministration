<?
set_time_limit(300);
include("comun/ini.php");
include("Constantes.php");

$fecha_desde = guardafecha($_GET['fecha_desde']);
$fecha_hasta = guardafecha($_GET['fecha_hasta']);
$id_cta_cont = $_GET['id_cta_cont'];

$q = "SELECT movim, descripcion FROM contabilidad.plan_cuenta WHERE id = '$id_cta_cont'";
$r = $conn->Execute($q);
$movim = (!$r->EOF) ? $r->fields['movim']:"";
$descripcionCta = (!$r->EOF) ? $r->fields['descripcion']:"";
if ($movim=='S' || empty($id_cta_cont))
{

	//SE CAMBIO EL IINER  A LEFT PARA QUE SALGA EL SALDO DE LACUENTA
	$sql = "SELECT plan_cuenta.codcta, plan_cuenta.descripcion, plan_cuenta.saldo_inicial, com_enc.*, com_det.debe, com_det.haber, plan_cuenta.naturaleza AS naturaleza FROM contabilidad.plan_cuenta ";
	$sql.= "INNER JOIN contabilidad.com_det ON (plan_cuenta.id = com_det.id_cta) ";
	$sql.= "INNER JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id) ";
	$sql.= "WHERE 1=1 ";
	$sql.= (!empty($id_cta_cont) ? " AND com_det.id_cta = $id_cta_cont ":"");
	$sql.= (!empty($fecha_desde) ? " AND com_enc.fecha >= '$fecha_desde' ":"");
	$sql.= (!empty($fecha_hasta) ? " AND com_enc.fecha <= '$fecha_hasta' ":"");
	$sql.= "AND com_enc.status = 'R' ";
	$sql.= "ORDER BY plan_cuenta.codcta::text, com_enc.fecha ";
}

else
{
	$q = "SELECT id FROM contabilidad.plan_cuenta WHERE id_acumuladora = $id_cta_cont";
	$r = $conn->Execute($q);
	$array = array();
	while (!$r->EOF)
	{
		$array[] = $r->fields['id'];
		$r->movenext();
	}
	
	$tope = count($array) - 1;
	$ctas = array();
	$acums = array();
	while($tope >= 0)
	{
		$q = "SELECT id FROM contabilidad.plan_cuenta WHERE id_acumuladora = ".$array[$tope];
		$q.= (count($ctas)>0 || count($acums)>0) ? " AND id NOT IN (".implode(',', $ctas).(count($acums)>0 ? ",".implode(',', $acums):"").")":"";
		//echo $q."<br>";
		$r = $conn->Execute($q);

		$copia = array();
		while (!$r->EOF)
		{
			$copia[] = $r->fields['id'];
			$r->movenext();
		}
		
		if (count($copia) > 0)
			$array = array_merge($array, $copia);
		else
		{
			$q = "SELECT movim FROM contabilidad.plan_cuenta WHERE id = ".$array[$tope];
			//echo "Movimiento/acumuladora ".$q."<br>";
			$r = $conn->Execute($q);
			if ($r->fields['movim'] == 'S')
				$ctas[] = array_pop($array);
			else
				$acums[] = array_pop($array);
		}

		$tope = count($array) - 1;
	}

//SE CAMBIO EL IINER  A LEFT PARA QUE SALGA EL SALDO DE LACUENTA
	$sql = "SELECT plan_cuenta.codcta, plan_cuenta.descripcion, plan_cuenta.saldo_inicial, com_enc.*, com_det.debe, com_det.haber, plan_cuenta.naturaleza AS naturaleza FROM contabilidad.plan_cuenta ";
	$sql.= "INNER JOIN contabilidad.com_det ON (plan_cuenta.id = com_det.id_cta) ";
	$sql.= "INNER JOIN contabilidad.com_enc ON (com_det.id_com = com_enc.id) ";
	$sql.= "WHERE com_det.id_cta IN (".implode(",", $ctas).") ";
	$sql.= (!empty($fecha_desde) ? " AND com_enc.fecha >= '".$fecha_desde."' ":"");
	$sql.= (!empty($fecha_hasta) ? " AND com_enc.fecha <= '".$fecha_hasta."' ":"");
	$sql.= "AND com_enc.status = 'R' ";
	$sql.= "ORDER BY plan_cuenta.codcta::text, com_enc.fecha ";
	
}
//die($sql);
//die($sql);
$r = $conn->Execute($sql);

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
	var $fechaDesde;
	var $fechaHasta;
	
	// Definen el ancho de varias celdas que se utilizan en el documento
	var $fechaEmiW = 20;
	var $descW = 45;
	var $numComW = 24;
	var $tipoDocW = 28;
	var $numDocW = 20;
	var $codctaW = 31;
	var $descCtaW = 134;
	var $debeW = 28;
	var $haberW = 28;
	var $saldoW = 28;
	
	
	//Cabecera de página
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
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(163, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(50,2, $textoDerecha, 0, 'L');
			
			$this->Ln(10);
			$this->SetLeftMargin(10);
			$this->SetFont('Courier','b',12);
			$titulo = "Mayor Analítico\n\n ";
			$titulo.= (empty($this->fechaHasta) ? "Desde ":"").(empty($this->fechaDesde) ? "Hasta ":$this->fechaDesde);
			$titulo.= (empty($this->fechaHasta) ? "":(empty($this->fechaDesde) ? "":" Al ").$this->fechaHasta);
			
			$this->MultiCell(0, 2, utf8_decode($titulo), 0, 'C');
			$this->Ln(8);
			
			$this->SetFont('Courier','B',8);
			$this->SetLineWidth(0.3);
			$this->Cell($this->codctaW, 4, "Cuenta Contable", T);
			$this->Cell($this->descCtaW, 4, utf8_decode("Descripción"), T);
			$this->Cell($this->saldoW, 4, "Saldo", T, '', 'C');
			$this->Ln();
			$this->Cell($this->fechaEmiW, 4, utf8_decode('Fecha E.'), B);
			$this->Cell($this->numComW, 4, utf8_decode('Nº Comprob.'), B);
			$this->Cell($this->descW, 4, utf8_decode("Descripción del Asiento"), B);
			$this->Cell($this->numDocW, 4, utf8_decode("Nº Documento"), B);
			$this->Cell($this->debeW, 4, "Debe", B, '', 'C');
			$this->Cell($this->haberW, 4, "Haber", B, '', 'C');
			$this->Cell($this->saldoW, 4, "", B);
			/*$this->Cell($this->tipoDocW, 4, 'Tipo Documento', T);
			$this->Cell($this->numDocW, 4, utf8_decode('Nº Documento'), T);*/
			$this->SetLineWidth(0.2);
			$this->Ln();
			$this->Ln();
	}

	//Pie de página
	function Footer()
	{
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->fechaDesde = $fecha_desde;
$pdf->fechaHasta = $fecha_hasta;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(10);
$pdf->SetFillColor(240);

if (!$r->EOF)
{
	$totalAcum = 0;
	while(!$r->EOF)
	{
		$pdf->SetFont('Courier', 'B', 8);
		// Imprime la informacion de la cuenta contable
		$codCtaAct = $r->fields['codcta'];
		$saldoCta = $r->fields['saldo_inicial'];
		$descCta = dividirStr(utf8_decode($r->fields['descripcion']), intval($pdf->descCtaW/$pdf->GetStringWidth('M')));
		
		$pdf->Cell($pdf->codctaW, 4, $r->fields['codcta'], 0);
		$pdf->Cell($pdf->descCtaW, 4, $descCta[0], 0);
		$pdf->Cell($pdf->saldoW, 4, muestraFloat($r->fields['saldo_inicial']), 0, '', 'R');
		
		while(next($descCta))
		{
			$pdf->Ln();
			$pdf->Cell($pdf->codctaW, 4, '', 0);
			$pdf->Cell($pdf->descCtaW, 4, current($descCta), 0);
			$pdf->Cell($pdf->saldoW, 4, '', 0);
		}
		
		$pdf->Ln();
		$pdf->SetFont('Courier', '', 8);
		$totalDebe = 0;
		$totalHaber = 0;
		// Imprime cada uno de los asientos donde estuvo incluida la cuenta
		while (!$r->EOF && $codCtaAct==$r->fields['codcta'])
		{
			$descCom = dividirStr(utf8_decode($r->fields['descrip']), intval($pdf->descW/$pdf->GetStringWidth('M')));
	
			$pdf->Cell($pdf->fechaEmiW, 4, date('d/m/Y', strtotime($r->fields['fecha'])), 0);
			$pdf->Cell($pdf->numComW, 4, $r->fields['numcom'], 0);
			$pdf->Cell($pdf->descW, 4, $descCom[0], 0);
			$pdf->Cell($pdf->numDocW, 4, $r->fields['num_doc'], 0);
			$pdf->Cell($pdf->debeW, 4, muestraFloat($r->fields['debe']), 0, '', 'R');
			$pdf->Cell($pdf->haberW, 4, muestraFloat($r->fields['haber']), 0, '', 'R');
			
			while(next($descCom))
			{
				$pdf->Ln();
				$pdf->Cell($pdf->fechaEmiW, 4, '', 0);
				$pdf->Cell($pdf->numComW, 4, '', 0);
				$pdf->Cell($pdf->descW, 4, current($descCom), 0);
				$pdf->Cell($pdf->debeW, 4, '', 0);
				$pdf->Cell($pdf->haberW, 4, '', 0);
			}
			
			$totalDebe += $r->fields['debe'];
			$totalHaber += $r->fields['haber'];
			$naturaleza = $r->fields['naturaleza'];
			$r->movenext();
			$pdf->Ln();
		}
		
		$pdf->SetFont('Courier', 'B', 8);
		$pdf->Cell($pdf->fechaEmiW, 4, '', T, '', '', 1);
		$pdf->Cell($pdf->numComW, 4, '', T, '', '', 1);
		$pdf->Cell($pdf->descW, 4, '', T, '', '', 1);
		$pdf->Cell($pdf->numDocW, 4, '', T, '', '', 1);
		$pdf->Cell($pdf->debeW, 4, muestraFloat($totalDebe), T, '', 'R', 1);
		$pdf->Cell($pdf->haberW, 4, muestraFloat($totalHaber), T, '', 'R', 1);
		
			
		//Se realizo este cambio para que las cuentas acreedoras sumen el saldo por el haber
		//die(var_dump($naturaleza));
		if ($naturaleza == 'D'){
			$pdf->Cell($pdf->saldoW, 4, muestraFloat($saldoCta + $totalDebe - $totalHaber), T, '', 'R', 1);
		}else{
			$pdf->Cell($pdf->saldoW, 4, muestraFloat($saldoCta - $totalDebe + $totalHaber), T, '', 'R', 1);
		}
		
		if($anoCurso == 2007){
			$pdf->Ln();
			$pdf->Cell($pdf->descW+$pdf->fechaEmiW+$pdf->numComW+$pdf->numDocW, 4, 'Bs.F.:', T, '', 'R', 1);
			$pdf->Cell($pdf->debeW, 4, muestraFloat($totalDebe/1000), T, '', 'R', 1);
			$pdf->Cell($pdf->haberW, 4, muestraFloat($totalHaber/1000), T, '', 'R', 1);
			if ($naturaleza == 'D'){
				$pdf->Cell($pdf->saldoW, 4, muestraFloat(($saldoCta + $totalDebe - $totalHaber)/1000), T, '', 'R', 1);
			}else{
				$pdf->Cell($pdf->saldoW, 4, muestraFloat(($saldoCta - $totalDebe + $totalHaber)/1000), T, '', 'R', 1);
			}
		}
		$pdf->SetFont('Courier', '', 8);
		$pdf->Ln(8);
		$totalAcum += $saldoCta + $totalDebe - $totalHaber;
	}

	if ($movim=='N')
	{	
		$pdf->Ln();
		$pdf->SetFont('Courier', 'B', 8);
		$pdf->Cell($pdf->fechaEmiW+$pdf->numComW+$pdf->descW+$pdf->numDocW+$pdf->debeW, 4, 'Total '.utf8_decode($descripcionCta), 0, '', '', 1);
		$pdf->Cell($pdf->saldoW+$pdf->haberW, 4, muestraFloat($totalAcum), 0, '', 'R', 1);
		if($anoCurso == 2007){
			$pdf->Ln();
			$pdf->Cell($pdf->fechaEmiW+$pdf->numComW+$pdf->descW+$pdf->numDocW+$pdf->debeW, 4, 'Bs.F.: ', 0, '', 'R', 1);
			$pdf->Cell($pdf->saldoW+$pdf->haberW, 4, muestraFloat($totalAcum/1000), 0, '', 'R', 1);
		}
		$pdf->Ln(8);
		
		
	}
	
}
//die();
$pdf->Output();
?>
