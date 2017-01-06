<?
include("comun/ini.php");
include("Constantes.php");

$id_contrato = $_REQUEST['id_contrato'];
$id_nomina = $_REQUEST['id_nomina'];

$contrato = new contrato;
$contrato->get($conn, $id_contrato);

$sql = "SELECT nom_fec_ini, nom_fec_fin FROM rrhh.historial_nom WHERE int_cod = $id_nomina";
$r = $conn->Execute($sql);
$fecha_inicio = muestraFecha($r->fields['nom_fec_ini']);
$fecha_fin = muestraFecha($r->fields['nom_fec_fin']);

$sql = "SELECT DISTINCT relacion_ue_cp.id_categoria_programatica AS cp, conc_part.par_cod AS pp, partidas_presupuestarias.descripcion, hist_nom_tra_conc.conc_val AS monto, hist_nom_tra_conc.conc_aporte AS aporte, hist_nom_tra_conc.tra_cod AS codigoTra FROM rrhh.departamento ";
$sql.= "INNER JOIN puser.relacion_ue_cp ON (departamento.unidad_ejecutora_cod = relacion_ue_cp.id_unidad_ejecutora) ";
$sql.= "INNER JOIN rrhh.conc_part ON (relacion_ue_cp.id_categoria_programatica = conc_part.cat_cod) ";
$sql.= "INNER JOIN rrhh.hist_nom_tra_conc ON (conc_part.conc_cod = hist_nom_tra_conc.conc_cod) ";
$sql.= "INNER JOIN rrhh.trabajador ON (hist_nom_tra_conc.tra_cod = trabajador.int_cod) ";
$sql.= "INNER JOIN puser.partidas_presupuestarias ON (partidas_presupuestarias.id = conc_part.par_cod) ";
$sql.= "WHERE relacion_ue_cp.id_escenario = '$escEnEje' AND hist_nom_tra_conc.hnom_cod = $id_nomina AND trabajador.dep_cod = departamento.int_cod ";
$sql.= "ORDER BY relacion_ue_cp.id_categoria_programatica, conc_part.par_cod ";
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
	var $contrato;		// Contrato al que pertenece la nomina
	var $fecha_inicio;
	var $fecha_fin;
	
	// Definen el ancho de las celdas
	var $codPartW = 48;
	var $descPartW = 77;
	var $montoW = 30;
	var $aporteW = 30;

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
			$this->SetFont('Courier', 'B', 12);
			$titulo = "Reporte de Compromiso de Nómina";
			$this->MultiCell(0, 4, utf8_decode($titulo), 0, 'C');
			$this->Ln(6);
			
			$this->SetFont('Courier', '', 8);
			$this->MultiCell(0, 3, utf8_decode("Tipo de Nómina: ".$this->contrato->cont_nom), 0, 'L');
			$this->MultiCell(0, 3, utf8_decode("Período del ".$this->fecha_inicio." al ".$this->fecha_fin), 0, 'L');
			
			$this->Ln();
			$this->SetFont('Courier', 'B', 8);
			$this->Cell($this->codPartW, 4, utf8_decode("Partida Presupuestaria"), TB, '', 'C');
			$this->Cell($this->descPartW, 4, utf8_decode("Descripción"), TB, '', 'C');
			$this->Cell($this->montoW, 4, "Monto", TB, '', 'C');
			$this->Cell($this->aporteW, 4, "Aporte", TB, '', 'C');
			$this->Ln(5);
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
$pdf->contrato = $contrato;
$pdf->fecha_inicio = $fecha_inicio;
$pdf->fecha_fin = $fecha_fin;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','', 7);
$pdf->SetLeftMargin(15);
$pdf->SetFillColor(240);

$i = 0;
$totalMonto = 0;
$totalAporte = 0;
while(!$r->EOF)
{
	$catAct = $r->fields['cp'];
	while (!$r->EOF && $catAct==$r->fields['cp'])
	{
		$partAct = $r->fields['pp'];
		$descPart = $r->fields['descripcion'];
		$sumMonto = 0;
		$sumAporte = 0;
		while (!$r->EOF && $partAct==$r->fields['pp'] && $catAct==$r->fields['cp'])
		{
			$sumMonto += $r->fields['monto'];
			$sumAporte += $r->fields['aporte'];
			$r->movenext();
		}
		
		$descPart = dividirStr(utf8_decode($descPart), intval($pdf->descPartW/$pdf->GetStringWidth('M')));
		$pdf->Cell($pdf->codPartW, 4, $catAct.' - '.$partAct, 0, '', 'C', ($i%2));
		$pdf->Cell($pdf->descPartW, 4, $descPart[0], 0, '', 'L', ($i%2));
		$pdf->Cell($pdf->montoW, 4, muestraFloat($sumMonto), 0, '', 'R', ($i%2));
		$pdf->Cell($pdf->aporteW, 4, muestraFloat($sumAporte), 0, '', 'R', ($i%2));
		$pdf->Ln();
		
		while(next($descPart))
		{
			$pdf->Cell($pdf->codPartW, 4, '', 0, '', 'L', ($i%2));
			$pdf->Cell($pdf->descPartW, 4, current($descPart), 0, '', 'L', ($i%2));
			$pdf->Cell($pdf->montoW, 4, '', 0, '', 'R', ($i%2));
			$pdf->Cell($pdf->aporteW, 4, '', 0, '', 'R', ($i%2));
			$pdf->Ln();
		}

		$i++;
		$totalMonto += $sumMonto;
		$totalAporte += $sumAporte;
	}
}

$pdf->Ln(1);
$pdf->Cell($pdf->codPartW+$pdf->descPartW, 4, '', T, '', 'L', 1);
$pdf->Cell($pdf->montoW, 4, muestraFloat($totalMonto), T, '', 'R', 1);
$pdf->Cell($pdf->aporteW, 4, muestraFloat($totalAporte), T, '', 'R', 1);

$pdf->Output();
?>
