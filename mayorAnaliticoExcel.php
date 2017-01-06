<?
include("comun/ini.php");
$_SESSION['conex'] = $conn;

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
//set_include_path(get_include_path() . PATH_SEPARATOR . 'lib/ExcelClasses/');
set_include_path($appRoot .'/lib/ExcelClasses/');

/** PHPExcel */
require_once 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

/** PHPExcel_RichText */
//require_once 'PHPExcel/RichText.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("SAMAT");
$objPHPExcel->getProperties()->setLastModifiedBy($usuario->nombre.' '.$usuario->apellido);
$objPHPExcel->getProperties()->setTitle("Mayor Analitico");
$objPHPExcel->getProperties()->setSubject("Mayor Analitico");
$objPHPExcel->getProperties()->setDescription("Documento generado desde el sistema gestion gubernamental SCGWEB.");
$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
$objPHPExcel->getProperties()->setCategory("Documentos de oficina");



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
		$q.= (count($ctas)>0 || count($acums)>0) ?  " AND id NOT IN (".(count($ctas)>0 ? implode(',', $ctas): "").(count($ctas)>0 && count($acums)>0 ? ",":"").(count($acums)>0 ? implode(',', $acums):"").")":""; 
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
	$sql.= "ORDER BY plan_cuenta.codcta::text ASC, com_enc.fecha ASC, com_enc.numcom ASC";
	
}
//die($sql);
$r = $conn->Execute($sql);

// Creamos la data a ser insertada en el documento
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C2', 'MAYOR ANALITICO');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Desde el: '.muestrafecha($fecha_desde).' Hasta el: '.muestrafecha($fecha_hasta));
$objPHPExcel->getActiveSheet()->setCellValue('H1', date('d/m/Y'));
//$objPHPExcel->getActiveSheet()->getStyle('H1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Intendencia Minicipal');

$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Cuenta Contable');
$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Saldo');
$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Fecha Emision');
$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Nº Comprobante');
$objPHPExcel->getActiveSheet()->setCellValue('C6', 'Descripcion del Asiento');
$objPHPExcel->getActiveSheet()->setCellValue('D6', 'Nº Documento');
$objPHPExcel->getActiveSheet()->setCellValue('E6', 'Debe');
$objPHPExcel->getActiveSheet()->setCellValue('F6', 'Haber');


$i = 8;
$totalAcum = 0;
while(!$r->EOF){

	$codCtaAct = $r->fields['codcta'];
	$saldoCta = $r->fields['saldo_inicial'];

	
	//$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $r->fields['codcta']);
	$objPHPExcel->getActiveSheet()->getCell('A'.$i)->setValueExplicit($r->fields['codcta'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $r->fields['descripcion']);
	$objPHPExcel->getActiveSheet()->getCell('G'.$i)->setValueExplicit($r->fields['saldo_inicial'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$i), 'B'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
	$i=$i+2;
	$totalDebe = 0;
	$totalHaber = 0;
	while (!$r->EOF && $codCtaAct==$r->fields['codcta']){
	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $r->fields['fecha']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $r->fields['numcom']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $r->fields['descrip']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $r->fields['num_doc']);
		$objPHPExcel->getActiveSheet()->getCell('E'.$i)->setValueExplicit($r->fields['debe'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$objPHPExcel->getActiveSheet()->getCell('F'.$i)->setValueExplicit($r->fields['haber'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('E8'), 'E9:E'.$i );
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
		//$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('F8'), 'F9:F'.$i );
		$i++;
		$totalDebe += $r->fields['debe'];
		$totalHaber += $r->fields['haber'];
		$naturaleza = $r->fields['naturaleza'];
		$r->movenext();
	}
	$i=$i+2;
	
	//Aplicamos relleno a la fila del total de los saldos
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->getStartColor()->setARGB('FFCCCCCC');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$i), 'B'.$i.':H'.$i);
	
	$objPHPExcel->getActiveSheet()->getCell('E'.$i)->setValueExplicit($totalDebe, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->getActiveSheet()->getCell('F'.$i)->setValueExplicit($totalHaber, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	if ($naturaleza == 'D'){
		$objPHPExcel->getActiveSheet()->getCell('G'.$i)->setValueExplicit($saldoCta + $totalDebe - $totalHaber, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	}else{
		$objPHPExcel->getActiveSheet()->getCell('G'.$i)->setValueExplicit($saldoCta - $totalDebe + $totalHaber, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	}
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
	
	$totalAcum += $saldoCta + $totalDebe - $totalHaber;
	
	if ($movim=='N')
	{	
		$i=$i+2;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->getStartColor()->setARGB('FFCCCCCC');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$i), 'B'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, descripcionCta);
		$objPHPExcel->getActiveSheet()->getCell('F'.$i)->setValueExplicit($totalAcum, PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);	
		
	}
	
}

// Establecemos la alineacion del texto
$objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A3'), 'B3:H3' );


//Aplicamos colores de relleno a las celdas
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FFCCCCCC');
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A1'), 'A1:H2' );

$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('000000FF');
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A3'), 'B3:H3' );

$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A5'), 'A5:G6');

// Establecemos el alto de las filas
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(15);

// Damos formato a las celdas

$objPHPExcel->getActiveSheet()->getStyle('H1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYYSLASH);

/*$objPHPExcel->getActiveSheet()->getStyle('J4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('J4'), 'J5:J'.$i );
$objPHPExcel->getActiveSheet()->getStyle('K4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('K4'), 'K5:K'.$i );
$objPHPExcel->getActiveSheet()->getStyle('L4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('L4'), 'L5:L'.$i );*/

// Establecemos el ancho de las columnas
//echo date('H:i:s') . " Set column widths\n";
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);


// Establecemos la fuente de la letra
$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);

$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);

//$objPHPExcel->getActiveSheet()->getStyle('J'.$j)->getFont()->setBold(true);



//$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setShrinkToFit(true);



// Agregamos el logo
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setPath('./images/logoa.jpg');
$objDrawing->setHeight(60);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

// Aplicamos seguridad a las celdas
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	// Needs to be set to true in order to enable any worksheet protection!
$objPHPExcel->getActiveSheet()->protectCells('A3:H'.$i, 'samat');

// Definimos la orietacion y tipo de hoja
//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Renombramos la Hoja
$objPHPExcel->getActiveSheet()->setTitle('Mayor Analitico');

// Predeterminamos la primera hoja (Obligatorio asi no existan  ma
$objPHPExcel->setActiveSheetIndex(0);

$archivo = str_replace('.php', '.xlsx', __FILE__);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->setOffice2003Compatibility(true);
$objWriter->save($archivo);

header ("Content-Disposition: attachment; filename=mayoranalitico.xlsx"); 
header ("Content-Type: application/download ");
readfile($archivo);
?>