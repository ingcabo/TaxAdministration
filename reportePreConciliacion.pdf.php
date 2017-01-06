<?
include("comun/ini.php");
include("Constantes.php");

$idEdoCta = $_GET['idEdoCta'];
$saldoIniBook = $_GET['saldoIniBook'];
$saldoFinBook = $_GET['saldoFinBook'];

$oEdoCta = new estadoCuenta;

$oEdoCta->get($conn, $idEdoCta);
//var_dump($oEdoCta).'<br>';

//$oConciliacion = new conciliacionBancaria2;

//PARA OBTENER MESES ANTERIORES A PARTIR DEL ESTADO DE CUENTA ACTUAL
$idEdosCta = conciliacionBancaria2::mesesAnteriores($conn, $idEdoCta, $oEdoCta->cta_bancaria);
$idEdoCta = implode(',',$idEdosCta);
//$oConciliacion = conciliacionBancaria2::getAsientosConciliar($conn, $idEdoCta);

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

	function nombOrigen($abrev){
		if ($abrev == "CHQ")
			$origen_desc = "Cheque";
		else if ($abrev == "CHM")
			$origen_desc = "Transferencia por Cheque";
		else if ($abrev == "DEP")
			$origen_desc = "Depósito";
		else if ($abrev == "TRM")
			$origen_desc = "Transferencia";
		else if ($abrev == "TRA")
			$origen_desc = "Pago Electronico";
		else if ($abrev == "ND")
			$origen_desc = "Nota de Débito";
		else if ($abrev == "NC")
			$origen_desc = utf8_decode("Nota de Crédito");
		return $origen_desc;
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
	var $transDW = 30;
	var $numDocDW = 35;
	var $fechaDW = 25;
	var $fechalibDW = 25;
	var $comprobanteDW = 30;
	var $montoDW = 35;
	var $transNLW = 50;
	var $numDocNLW = 35;
	var $fechaNLW = 35;
	var $debitosNLW = 30;
	var $creditosNLW = 30;
	var $transNBW = 35;
	var $comprobanteNBW = 30;
	var $numDocNBW = 30;
	var $fechaNBW = 25;
	var $debeNBW = 30;
	var $haberNBW = 30;

	

	
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

		$this->SetXY(215, 20); 
		$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
		//$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
		$this->MultiCell(50,2, $textoDerecha, 0, 'L');
		
		$this->Ln(12);
		$this->SetFont('Courier','b',12);
		$titulo = "Pre - Conciliación Bancaria";
		$this->MultiCell(0, 2, $titulo, 0, 'C');
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
$pdf = new PDF();

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(10);
$pdf->SetFillColor(240);
$pdf->Ln(8);

$pdf->SetFont('Courier', 'B', 8);
$pdf->MultiCell(0, 4, "Cuenta: ".$oEdoCta->cuenta->banco->descripcion." / ".$oEdoCta->cuenta->nro_cuenta, 0);
$pdf->Ln();
$pdf->Cell(35,4,"Fecha Desde:",0,'','L');
$pdf->Cell(30,4,$oEdoCta->fecha_desde,0,'','C');
$pdf->Cell(35,4,"Fecha Hasta:",0,'','L');
$pdf->Cell(30,4,$oEdoCta->fecha_hasta,0,'','C');
$pdf->Ln();
$pdf->Cell(35,4,"Saldo Inicial Libro:",0,'','L');
$pdf->Cell(30,4,muestraFloat($saldoIniBook),0,'','C');
$pdf->Cell(35,4,"Saldo Inicial Banco:",0,'','L');
$pdf->Cell(30,4,muestraFloat($oEdoCta->saldo_inicial_banco),0,'','C');
$pdf->Ln();

$pdf->Cell(180,4,"Movimientos Conciliables",BT,'','C');
$pdf->Ln();

$oConciliacion = conciliacionBancaria2::getAsientosConciliar($conn, $idEdoCta);

$pdf->Cell($pdf->transDW, 4, "Tipo Doc.", LBR, '', 'C');
$pdf->Cell($pdf->fechaDW, 4, "Fecha Banco", LRB, '', 'C');
$pdf->Cell($pdf->numDocDW, 4, "Num. Documento", LRB, '', 'C');
$pdf->Cell($pdf->montoDW, 4, "Monto", LRB, '', 'C');
$pdf->Cell($pdf->fechalibDW, 4, "Fecha Libro", LRB, '', 'C');
$pdf->Cell($pdf->comprobanteDW, 4, utf8_decode("Nº Comprobante"), LRB, '', 'C');
$pdf->Ln();

$pdf->SetFont('Courier','', 8);

foreach($oConciliacion as $auxConciliacion){
	$tipo = nombOrigen($auxConciliacion->tipo_doc);
	$pdf->Cell($pdf->transDW,4,$tipo,LR,'','C');
	$pdf->Cell($pdf->fechaDW,4,muestrafecha($auxConciliacion->fecha_doc),LR,'','C');
	$pdf->Cell($pdf->numDocDW,4,$auxConciliacion->num_doc,LR,'','C');
	$pdf->Cell($pdf->montoDW,4,muestraFloat($auxConciliacion->monto),LR,'','R');	
	$pdf->Cell($pdf->fechalibDW,4,muestrafecha($auxConciliacion->fecha_libro),LR,'','C');
	$pdf->Cell($pdf->comprobanteDW,4,$auxConciliacion->numcom,LR,'','C');
	$pdf->Ln();
}
$pdf->Cell($pdf->transDW+$pdf->fechaDW+$pdf->numDocDW+$pdf->montoDW+$pdf->fechalibDW+$pdf->comprobanteDW,4,'',T,'','C');
$pdf->Ln(12);

$oConciliacion = conciliacionBancaria2::partEdoCtanoLibro($conn,$idEdoCta,$oEdoCta->cta_bancaria,$fecha);
//die(var_dump($oConciliacion));
$pdf->Cell(180,4,"Partidas No en Libro",BT,'','C');
$pdf->Ln();

$pdf->Cell($pdf->transNLW, 4, "Tipo Doc.", LBR, '', 'C');
$pdf->Cell($pdf->fechaNLW, 4, "Fecha Banco", LRB, '', 'C');
$pdf->Cell($pdf->numDocNLW, 4, "Num. Documento", LRB, '', 'C');
$pdf->Cell($pdf->debitosNLW, 4, "Debitos (debe)", LRB, '', 'C');
$pdf->Cell($pdf->creditosNLW, 4, "Creditos (haber)", LRB, '', 'C');
$pdf->Ln();

$cheqNoLibro = 0;
$tranNoLibro = 0;
$depoNoLibro = 0;
$nDebNoLibro = 0;
$nCreNoLibro = 0;
foreach($oConciliacion as $auxNoLibro){
	$tipo = nombOrigen($auxNoLibro->tipo_doc);
	if($auxNoLibro->tipo_doc == 'CHQ')
		$cheqNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;
	elseif($auxNoLibro->tipo_doc == 'CHM')
		$cheqtNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;
	elseif($auxNoLibro->tipo_doc == 'TRA')
		$pagoeNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;
	elseif($auxNoLibro->tipo_doc == 'TRM')
		$tranNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;
	elseif($auxNoLibro->tipo_doc == 'DEP')
		$depoNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;	
	elseif($auxNoLibro->tipo_doc == 'ND')
		$nDebNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;	
	elseif($auxNoLibro->tipo_doc == 'NC')
		$nCreNoLibro += $auxNoLibro->creditos - $auxNoLibro->debitos;	
	$pdf->Cell($pdf->transNLW, 4, $tipo, LR,'','C');
	$pdf->Cell($pdf->fechaNLW, 4, muestrafecha($auxNoLibro->fecha_doc), LR,'','C');
	$pdf->Cell($pdf->numDocNLW, 4, $auxNoLibro->num_doc, LR,'','C');
	$pdf->Cell($pdf->debitosNLW, 4, muestrafloat($auxNoLibro->debitos), LR,'','R');
	$pdf->Cell($pdf->creditosNLW, 4, muestrafloat($auxNoLibro->creditos), LR,'','R');
	$pdf->Ln();
}
$pdf->Cell($pdf->transNLW+$pdf->fechaNLW+$pdf->numDocNLW+$pdf->debitosNLW+$pdf->creditosNLW,4,'',T,'','C');
$pdf->Ln();
$pdf->Cell(40,4,'Total Cheques:',0,'','L');
$pdf->Cell(30,4,muestraFloat($cheqNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Transferencia por Cheques:',0,'','L');
$pdf->Cell(30,4,muestraFloat($cheqtNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Pagos Electronicos:',0,'','L');
$pdf->Cell(30,4,muestraFloat($pagoeNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Transferencias:',0,'','L');
$pdf->Cell(30,4,muestraFloat($tranNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Depositos:',0,'','L');
$pdf->Cell(30,4,muestraFloat($depoNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Notas de Debito:',0,'','L');
$pdf->Cell(30,4,muestraFloat($nDebNoLibro),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Notas de Credito:',0,'','L');
$pdf->Cell(30,4,muestraFloat($nCreNoLibro),0,'','R');
$pdf->Ln(8);

$oConciliacion = conciliacionBancaria2::partLibronoEdoCta($conn, $oEdoCta->cuenta->plan_cuenta, $idEdoCta, $oEdoCta->fecha_hasta, $oEdoCta->fecha_desde);
//die(var_dump($oConciliacion));
$pdf->Cell(180,4,"Partidas No en Banco",BT,'','C');
$pdf->Ln();

$cheqNoBanco = 0;
$tranNoBanco = 0;
$depoNoBanco = 0;
$nDebNoBanco = 0;
$nCreNoBanco = 0;

$pdf->Cell($pdf->transNBW, 4, "Tipo Doc.", LBR, '', 'C');
$pdf->Cell($pdf->fechaNBW, 4, "Fecha Libro", LRB, '', 'C');
$pdf->Cell($pdf->comprobanteNBW, 4, utf8_decode("Nº Comprobante"), LRB, '', 'C');
$pdf->Cell($pdf->numDocNBW, 4, "Num. Documento", LRB, '', 'C');
$pdf->Cell($pdf->debeNBW, 4, "Debe", LRB, '', 'C');
$pdf->Cell($pdf->haberNBW, 4, "Haber", LRB, '', 'C');
$pdf->Ln();

foreach($oConciliacion as $auxNoBanco){
	$tipo = nombOrigen($auxNoBanco->tipo_doc);
	if($auxNoBanco->tipo_doc == 'CHQ')
		$cheqNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'CHM')
		$cheqtNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'TRA')
		$pagoeNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'TRM')
		$tranNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'DEP')
		$depoNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'ND')
		$nDebNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	elseif($auxNoBanco->tipo_doc == 'NC')
		$nCreNoBanco += $auxNoBanco->creditos - $auxNoBanco->debitos;
	$pdf->Cell($pdf->transNBW, 4, $tipo, LR,'','C');
	$pdf->Cell($pdf->fechaNBW, 4, muestrafecha($auxNoBanco->fecha_doc), LR,'','C');
	$pdf->Cell($pdf->comprobanteNBW, 4, $auxNoBanco->numcom, LR, '', 'C');
	$pdf->Cell($pdf->numDocNBW, 4, $auxNoBanco->num_doc, LR,'','C');
	$pdf->Cell($pdf->debeNBW, 4, muestrafloat($auxNoBanco->creditos), LR,'','R');
	$pdf->Cell($pdf->haberNBW, 4, muestrafloat($auxNoBanco->debitos), LR,'','R');
	$pdf->Ln();
}
$pdf->Cell($pdf->transNBW+$pdf->fechaNBW+$pdf->comprobanteNBW+$pdf->numDocNBW+$pdf->debeNBW+$pdf->haberNBW,4,'',T,'','C');
$pdf->Ln();
$pdf->Cell(40,4,'Total Cheques:',0,'','L');
$pdf->Cell(30,4,muestraFloat($cheqNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Transferencia por Cheques:',0,'','L');
$pdf->Cell(30,4,muestraFloat($cheqtNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Pagos Electronicos:',0,'','L');
$pdf->Cell(30,4,muestraFloat($pagoeNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Transferencias:',0,'','L');
$pdf->Cell(30,4,muestraFloat($tranNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Depositos:',0,'','L');
$pdf->Cell(30,4,muestraFloat($depoNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Notas de Debito:',0,'','L');
$pdf->Cell(30,4,muestraFloat($nDebNoBanco),0,'','R');
$pdf->Ln();
$pdf->Cell(40,4,'Total Notas de Credito:',0,'','L');
$pdf->Cell(30,4,muestraFloat($nCreNoBanco),0,'','R');
$pdf->Ln(8);


$pdf->AddPage();
$pdf->Ln();
$pdf->SetFont('Courier', 'B', 8);
$totalTrans = 0;

$pdf->Cell(80,4,'Saldo Final Libro:',0,'','L',1);
$pdf->Cell(25,4,muestraFloat($saldoFinBook),0,'','R',1);
$pdf->Cell(35,4,'Saldo Final Banco:',0,'','L',1);
$pdf->Cell(45,4,muestraFloat($oEdoCta->saldo_final_banco),0,'','R',1);
$transitoBanco = 0;
$transitoLibro = 0;
$pdf->Ln();
$pdf->SetFont('Courier', '', 8);
if($cheqNoLibro!=0){
	$pdf->Cell(80,4,'Cheques No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($cheqNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $cheqNoLibro;
	$pdf->Ln();
} if($cheqNoBanco!=0){
	$pdf->Cell(80,4,'Cheques No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($cheqNoBanco),0,'','R',1);
	$transitoBanco += $cheqNoBanco;
	$pdf->Ln();
} if($cheqtNoLibro!=0){
	$pdf->Cell(80,4,'Cheques por transferencia No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($cheqtNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $cheqtNoLibro;
	$pdf->Ln();
} if($cheqtNoBanco!=0){
	$pdf->Cell(80,4,'Cheques por transferencia No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($cheqtNoBanco),0,'','R',1);
	$transitoBanco += $cheqtNoBanco;
	$pdf->Ln();
} if($pagoeNoLibro!=0){
	$pdf->Cell(80,4,'Pago Electronico No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($pagoeNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $pagoeNoLibro;
	$pdf->Ln();
} if($pagoeNoBanco!=0){
	$pdf->Cell(80,4,'Pago Electronico No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($pagoeNoBanco),0,'','R',1);
	$transitoBanco += $pagoeNoBanco;
	$pdf->Ln();
}if($tranNoLibro!=0){
	$pdf->Cell(80,4,'Transferencias No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($tranNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $tranNoLibro;
	$pdf->Ln();
} if($tranNoBanco!=0){
	$pdf->Cell(80,4,'Transferencias No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($tranNoBanco),0,'','R',1);
	$transitoBanco += $tranNoBanco;
	$pdf->Ln();
} if($depoNoLibro!=0){
	$pdf->Cell(80,4,'Depositos No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($depoNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $depoNoLibro;
	$pdf->Ln();
} if($depoNoBanco!=0){
	$pdf->Cell(80,4,'Depositos No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($depoNoBanco),0,'','R',1);
	$transitoBanco += $depoNoBanco;
	$pdf->Ln();
} if($nDebNoLibro!=0){
	$pdf->Cell(80,4,'Notas de Debito No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($nDebNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $nDebNoLibro;
	$pdf->Ln();
} if($nDebNoBanco!=0){
	$pdf->Cell(80,4,'Notas de Debito No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($nDebNoBanco),0,'','R',1);
	$transitoBanco += $nDebNoBanco;
	$pdf->Ln();
} if($nCreNoLibro!=0){
	$pdf->Cell(80,4,'Notas de Credito No en Libro:',0,'','L',1);
	$pdf->Cell(25,4,muestraFloat($nCreNoLibro),0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,'',0,'','R',1);
	$transitoLibro += $nCreNoLibro;
	$pdf->Ln();
} if($nCreNoBanco!=0){
	$pdf->Cell(80,4,'Notas de Credito No en Banco:',0,'','L',1);
	$pdf->Cell(25,4,'',0,'','R',1);
	$pdf->Cell(35,4,'',0,'','L',1);
	$pdf->Cell(45,4,muestraFloat($nCreNoBanco),0,'','R',1);
	$transitoBanco += $nCreNoBanco;
	$pdf->Ln();
}
$pdf->Cell(80,5,'Total Partidas en Transito:',T,'','L',1);
$pdf->Cell(25,5,muestraFloat($transitoLibro),T,'','R',1);
$pdf->Cell(35,5,'',T,'','L',1);
$pdf->Cell(45,5,muestraFloat($transitoBanco),T,'','R',1);
$pdf->Ln();
	

$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(80,6,'Saldo Conciliado', LBT, '', 'L', 1);
$pdf->Cell(25, 6, muestraFloat($saldoFinBook + $transitoLibro), TB, '', 'R', 1);
$pdf->Cell(35, 6,'' , TB, '', 'R', 1);
$pdf->Cell(45, 6,muestraFloat($oEdoCta->saldo_final_banco + $transitoBanco), TRB, '', 'R', 1);

$pdf->Output();
?>
