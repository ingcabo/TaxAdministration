<?
include("comun/ini.php");
include ("Constantes.php");

$tipo = $_GET['tipo'];
$anio = $_GET['anio'];
$mes = $_GET['mes'];
$detallado = $_GET['detallado'];

if ($tipo == 'D')
{
	if ($detallado == 'N')
	{
		$sql = "SELECT consolidado.*, plan_cuenta.descripcion, plan_cuenta.movim FROM contabilidad.consolidado ";
		$sql.= "INNER JOIN contabilidad.plan_cuenta ON (plan_cuenta.codcta = consolidado.cod_cta) ";
		$sql.= "WHERE plan_cuenta.movim = 'N' ";
	}
	else
	{
		$sql = "SELECT consolidado.*, plan_cuenta.descripcion, plan_cuenta.movim FROM contabilidad.consolidado ";
		$sql.= "INNER JOIN contabilidad.plan_cuenta ON (consolidado.cod_cta = plan_cuenta.codcta) ";
		$sql.= "WHERE consolidado.ano = $anio AND consolidado.mes = $mes ";
	}
		
	$sql.= "ORDER BY cod_cta::char(16)";
}
else
{
	$sql = "SELECT plan_cuenta.descripcion, plan_cuenta.codcta, COALESCE(consolidado.saldo_act, 0) AS saldo_act FROM contabilidad.plan_cuenta ";
	$sql.= "LEFT JOIN contabilidad.consolidado ON (consolidado.cod_cta = plan_cuenta.codcta) ";
	$sql.= "WHERE LENGTH(codcta) = 5 AND (consolidado.ano = $anio OR consolidado.ano IS NULL) AND (consolidado.mes = $mes OR consolidado.mes IS NULL) ";
	$sql.= "ORDER BY substring(codcta, 1, 1)::char, substring(codcta, 2, 1)::char, substring(codcta, 3, 3)::char ";
}
//die($sql);

$r = $conn->Execute($sql);
/*while(!$r->EOF)
{
	var_dump($r->fields);
	echo "<br /><br />";
	$r->movenext();
}*/
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
	var $tipo;
	
	// Definen el ancho de las celdas en el balance de comprobacion
	var $codCtaDW = 26;
	var $descCtaDW = 50;
	var $saldoAntDW = 28;
	var $debeDW = 28;
	var $haberDW = 28;
	var $saldoActDW = 28;
	
	// Definen el ancho de las celdas en el balance general
	var $codCtaGW = 7;
	var $descCtaGW = 57;
	var $saldoGW = 28;
	//Cabecera de página
	function Header()
	{
			$this->SetFillColor(240);
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
			
			$this->Ln(12);
			$this->SetFont('Courier','b',12);
			if ($this->tipo == 'G')
				$titulo = "Balance General de la Hacienda Pública Municipal\n\n Al mes ".sprintf("%02d", $this->mes)." del ".$this->anio;
			else
				$titulo = "Balance de Comprobación del Mes ".sprintf("%02d", $this->mes)." del Año ".$this->anio;
			
			$this->MultiCell(0, 2, utf8_decode($titulo), 0, 'C');
			$this->SetLeftMargin(10);
			if ($this->tipo == 'D')
			{
				$this->Ln(5);
				$this->setFont('Courier', 'B', 7);
				$this->Cell($this->codCtaDW, 4, utf8_decode('Código de Cuenta'), 0, '', 'L', 1);
				$this->Cell($this->descCtaDW, 4, utf8_decode('Descripción de la Cuenta'), 0, '', 'L', 1);
				$this->Cell($this->saldoAntDW, 4, utf8_decode('Saldo Anterior'), 0, '', 'C', 1);
				$this->Cell($this->debeDW, 4, utf8_decode('Monto Debe'), 0, '', 'C', 1);
				$this->Cell($this->haberDW, 4, utf8_decode('Monto Haber'), 0, '', 'C', 1);
				$this->Cell($this->saldoActDW, 4, utf8_decode('Saldo Actual'), 0, '', 'C', 1);
				$this->Ln(4.8);
			}
			else
			{
				$this->Ln();
				$this->Line(10, $this->GetY(), 198, $this->GetY());
				$this->Ln();
			}
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
$pdf=new PDF();
$pdf->anio = $anio;
$pdf->mes = $mes;
$pdf->tipo = $tipo;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','', 7);
$pdf->SetLeftMargin(10);
$pdf->SetFillColor(240);

if ($tipo == 'G')
{
	$pdf->SetFont('Courier', '', 10);
	$pdf->Cell(94, 4, "A C T I V O", 0, '', 'C');
	$pdf->Cell(94, 4, "P A S I V O", 0, '', 'C');
	$pdf->Ln(2);

	/*Inicio de Cuentas del Tesoro*/
	$pdf->SetFont('Courier', 'BU', 10);
	$pdf->Cell(188, 4, "CUENTAS DEL TESORO", 0, '', 'C');
	$pdf->SetFont('Courier', '', 7);
	$pdf->Ln(6);
	
	$posxAct = 10;
	$posyAct = $pdf->GetY();
	$posxPas = 106;
	$posyPas = $pdf->GetY();
	
	$totalAct = 0;
	$totalPas = 0;
	
	$tipoCta = substr($r->fields['codcta'], 0, 1);
	//Mientras el primer digito de la cuenta comience en 
	while ($tipoCta == substr($r->fields['codcta'], 0, 1) && !$r->EOF)
	{
		// Es una cuenta de activo
		if (substr($r->fields['codcta'], 1, 1) == '1')
		{
			$pdf->SetXY($posxAct, $posyAct);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalAct += $r->fields['saldo_act'];
			$posyAct += 4;
		}
		// Es una cuenta de pasivo
		else
		{
			$pdf->SetXY($posxPas, $posyPas);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalPas += $r->fields['saldo_act'];
			$posyPas += 4;
		}
		
		$pdf->Ln();
		$r->movenext();
	}
	
	if ($posyAct > $posyPas)
		$pdf->SetXY($posxAct, $posyAct);
	else
		$pdf->SetXY($posxAct, $posyPas);

	$pdf->Ln(1);
	$pdf->SetXY($posxAct, $pdf->GetY());
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "TOTAL Cuentas del Tesoro", T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalAct), T, '', 'R', 1);
	$pdf->Cell(4, 4, '');
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalPas), T, '', 'R', 1);
	
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "Bs.F.:", T, '', 'R', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalAct/1000), T, '', 'R', 1);
		$pdf->Cell(4, 4, '');
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalPas/1000), T, '', 'R', 1);
	}
	
	$pdf->Ln(8);
	/*Fin de Cuentas del Tesoro*/
	
	/*Inicio de Cuentas de la Hacienda*/
	$pdf->SetFont('Courier', 'BU', 10);
	$pdf->Cell(188, 4, "CUENTAS DE LA HACIENDA", 0, '', 'C');
	$pdf->SetFont('Courier', '', 7);
	$pdf->Ln(6);
	
	$posyAct = $pdf->GetY();
	$posyPas = $pdf->GetY();
	
	$totalAct = 0;
	$totalPas = 0;
	
	$tipoCta = substr($r->fields['codcta'], 0, 1);
	while ($tipoCta == substr($r->fields['codcta'], 0, 1) && !$r->EOF)
	{
		// Es una cuenta de activo
		if (substr($r->fields['codcta'], 1, 1) == '1')
		{
			$pdf->SetXY($posxAct, $posyAct);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalAct += $r->fields['saldo_act'];
			$posyAct += 4;
		}
		// Es una cuenta de pasivo
		else
		{
			$pdf->SetXY($posxPas, $posyPas);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalPas += $r->fields['saldo_act'];
			$posyPas += 4;
		}

		$pdf->Ln();		
		$r->movenext();
	}

	if ($posyAct > $posyPas)
		$pdf->SetXY($posxAct, $posyAct);
	else
		$pdf->SetXY($posxAct, $posyPas);

	$pdf->Ln(1);
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "SUBTOTAL Cuentas de la Hacienda", T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalAct), T, '', 'R', 1);
	$pdf->Cell(4, 4, '');
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalPas), T, '', 'R', 1);
	
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "Bs.F.:", T, '', 'R', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalAct/1000), T, '', 'R', 1);
		$pdf->Cell(4, 4, '');
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalPas/1000), T, '', 'R', 1);
	}
	
	$pdf->Ln(8);
	/*Fin de Cuentas de la Hacienda*/

	/*Inicio de Cuentas del Presupuesto*/
	$pdf->SetFont('Courier', 'BU', 10);
	$pdf->Cell(188, 4, "CUENTAS DEL PRESUPUESTO", 0, '', 'C');
	$pdf->SetFont('Courier', '', 7);
	$pdf->Ln(6);
	
	$posyAct = $pdf->GetY();
	$posyPas = $pdf->GetY();
	
	$totalActHac = $totalAct;
	$totalPasHac = $totalPas;
	$totalAct = 0;
	$totalPas = 0;
	
	$tipoCta = substr($r->fields['codcta'], 0, 1);
	while ($tipoCta == substr($r->fields['codcta'], 0, 1) && !$r->EOF)
	{
		// Es una cuenta de activo
		if (substr($r->fields['codcta'], 1, 1) == '4')
		{
			$pdf->SetXY($posxAct, $posyAct);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalAct += $r->fields['saldo_act'];
			$posyAct += 4;
		}
		// Es una cuenta de pasivo
		else
		{
			$pdf->SetXY($posxPas, $posyPas);
			$pdf->Cell($pdf->codCtaGW, 4, substr($r->fields['codcta'], 2, 3));
			$pdf->Cell($pdf->descCtaGW, 4, utf8_decode($r->fields['descripcion']));
			$pdf->Cell($pdf->saldoGW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R');
			
			$totalPas += $r->fields['saldo_act'];
			$posyPas += 4;
		}
		
		$pdf->Ln();
		$r->movenext();
	}
	
	if ($posyAct > $posyPas)
		$pdf->SetXY($posxAct, $posyAct);
	else
		$pdf->SetXY($posxAct, $posyPas);

	$pdf->Ln(1);
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "TOTAL Cuentas de la Hacienda", T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalActHac+$totalAct), T, '', 'R', 1);
	$pdf->Cell(4, 4, '');
	$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
	$pdf->Cell($pdf->saldoGW, 4, muestraFloat($totalPasHac+$totalPas), T, '', 'R', 1);
	
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, "Bs.F.:", T, '', 'R', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat(($totalActHac+$totalAct)/1000), T, '', 'R', 1);
		$pdf->Cell(4, 4, '');
		$pdf->Cell($pdf->codCtaGW+$pdf->descCtaGW, 4, '', T, '', 'L', 1);
		$pdf->Cell($pdf->saldoGW, 4, muestraFloat(($totalPasHac+$totalPas)/1000), T, '', 'R', 1);
	}
	
	/*Fin de Cuentas del Presupuesto*/
}
else
{
	$totalDebe = 0;
	$totalHaber = 0;
/*	for ($j=0; $j<10; $j++)
	{*/
	while (!$r->EOF)
	{
		$descCta = dividirStr(utf8_decode($r->fields['descripcion']), intval($pdf->descCtaDW/$pdf->GetStringWidth('M')));
	
		if ($r->fields['movim'] == 'N')
			$fill = 1;
		else
			$fill = 0;
			
		$pdf->Cell($pdf->codCtaDW, 4, $r->fields['cod_cta'], 0, '', 'L', $fill);
		$pdf->Cell($pdf->descCtaDW, 4, $descCta[0], 0, '', 'L', $fill);
		$pdf->Cell($pdf->saldoAntDW, 4, muestraFloat($r->fields['saldo_ant']), 0, '', 'R', $fill);
		$pdf->Cell($pdf->debeDW, 4, muestraFloat($r->fields['debe']), 0, '', 'R', $fill);
		$pdf->Cell($pdf->haberDW, 4, muestraFloat($r->fields['haber']), 0, '', 'R', $fill);
		$pdf->Cell($pdf->saldoActDW, 4, muestraFloat($r->fields['saldo_act']), 0, '', 'R', $fill);
		
		if(strlen($r->fields['cod_cta']) == 1){
			$totalDebe += $r->fields['debe'];
			$totalHaber += $r->fields['haber'];
		}
		
		$hayCta = next($descCta);
		for ($i=1; $hayCta!==false; $i++)
		{
			$pdf->Ln();
			$pdf->Cell($pdf->codCtaDW, 4, '', 0, '', '', $fill);
			$pdf->Cell($pdf->descCtaDW, 4, $descCta[$i], 0, '', '', $fill);
			$pdf->Cell($pdf->saldoAntDW, 4, '', 0, '', '', $fill);
			$pdf->Cell($pdf->debeDW, 4, '', 0, '', '', $fill);
			$pdf->Cell($pdf->haberDW, 4, '', 0, '', '', $fill);
			$pdf->Cell($pdf->saldoActDW, 4, '', 0, '', '', $fill);
			$hayCta = next($descCta);
		}

		$pdf->Ln();
		if ($fill == 1)
			$pdf->Ln(0.7);
		$r->movenext();
	}
	/*$r->MoveFirst();
	}*/
	
	$pdf->Ln(1);
	$pdf->SetFont('Courier', 'B', 7);
	$pdf->Cell($pdf->codCtaDW+$pdf->descCtaDW, 4, "TOTAL BALANCE DE COMPROBACION", T, '', 'C', 1);
	$pdf->Cell($pdf->saldoAntDW, 4, '0,00', T, '', 'R', 1);
	$pdf->Cell($pdf->debeDW, 4, muestraFloat($totalDebe), T, '', 'R', 1);
	$pdf->Cell($pdf->saldoAntDW, 4, muestraFloat($totalHaber), T, '', 'R', 1);
	$pdf->Cell($pdf->saldoActDW, 4, '0,00', T, '', 'R', 1);
	
	if($anoCurso == 2007){
		$pdf->Ln();
		$pdf->Cell($pdf->codCtaDW+$pdf->descCtaDW, 4, "Bs.F.:", T, '', 'C', 1);
		$pdf->Cell($pdf->saldoAntDW, 4, '0,00', T, '', 'R', 1);
		$pdf->Cell($pdf->debeDW, 4, muestraFloat($totalDebe/1000), T, '', 'R', 1);
		$pdf->Cell($pdf->saldoAntDW, 4, muestraFloat($totalHaber/1000), T, '', 'R', 1);
		$pdf->Cell($pdf->saldoActDW, 4, '0,00', T, '', 'R', 1);
	}
	
	if ($totalDebe != $totalHaber)
	{
		$pdf->Ln(6);
		$pdf->Cell(188, 4, "BALANCE DESCUADRADO", 0, '', 'C');
	}
}

$pdf->Output();
?>
