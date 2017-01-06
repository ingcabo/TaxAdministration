<?
include("comun/ini.php");
$_SESSION['conex'] = $conn;

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib/ExcelClasses/');
//set_include_path('/lib/ExcelClasses/');

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
$objPHPExcel->getProperties()->setTitle("Comprobante de Diario");
$objPHPExcel->getProperties()->setSubject("Comprobante de Diario");
$objPHPExcel->getProperties()->setDescription("Documento generado desde el sistema gestion gubernamental SCGWEB.");
$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
$objPHPExcel->getProperties()->setCategory("Documentos de oficina");

$jsonRec = new Services_JSON();

$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta']; 

// Creamos la data a ser insertada en el documento
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C2', 'COMPROBANTE DE DIARIO');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Desde el: '.muestrafecha($fecha_desde).' Hasta el: '.muestrafecha($fecha_hasta));
$objPHPExcel->getActiveSheet()->setCellValue('H1', time());
$objPHPExcel->getActiveSheet()->getStyle('H1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYYSLASH);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Intendencia Minicipal');


$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Fecha Emision');
$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Nº Comprobante');
$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Descripcion del Asiento');
$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Tipo Documento');
$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Nº Documento');
$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Cuenta Contable');
$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Descripcion');
$objPHPExcel->getActiveSheet()->setCellValue('C6', '');
$objPHPExcel->getActiveSheet()->setCellValue('D6', 'Debe');
$objPHPExcel->getActiveSheet()->setCellValue('E6', 'Haber');

$oComprobante = new comprobante($conn);
$cComprobantes = $oComprobante->get_all('', '', '', $fecha_desde, $fecha_hasta);

foreach($cComprobantes as $comprobante)
$r = $conn->Execute($q);
$i=4;
while(!$r->EOF){
	$oInscripcion = new Inscripcion($conn);
	$oInscripcion->get($r->fields['id']);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $oInscripcion->inscripcion);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $oInscripcion->contrato);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $oInscripcion->filial);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $oInscripcion->estatus);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $oInscripcion->cliNom." ".$oInscripcion->cliApe);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $oInscripcion->grupo);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $oInscripcion->empleado);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $oInscripcion->fecSolEmp);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $oInscripcion->fecTransEmp);
	$objPHPExcel->getActiveSheet()->getCell('J'.$i)->setValueExplicit($oInscripcion->montoTotFloat, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->getActiveSheet()->getCell('K'.$i)->setValueExplicit($oInscripcion->montoInsFloat, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->getActiveSheet()->getCell('L'.$i)->setValueExplicit($oInscripcion->montoGarFloat, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	
	$i++;
	$r->movenext();
} 
$j = $i+2;
$tot = $i-4;
$objPHPExcel->getActiveSheet()->setCellValue('J'.$j, 'Total de Registros: '.$tot);	

//Aplicamos colores de relleno a las celdas
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FFCCCCCC');
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A1'), 'B1:L2' );

$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('FFFF0000');
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A3'), 'B3:L3' );

// Establecemos el alto de las filas
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(15);

// Damos formato a las celdas

$objPHPExcel->getActiveSheet()->getStyle('H1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYYSLASH);

$objPHPExcel->getActiveSheet()->getStyle('J4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('J4'), 'J5:J'.$i );
$objPHPExcel->getActiveSheet()->getStyle('K4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('K4'), 'K5:K'.$i );
$objPHPExcel->getActiveSheet()->getStyle('L4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VZLA_SIMPLE);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('L4'), 'L5:L'.$i );

// Establecemos el ancho de las columnas
//echo date('H:i:s') . " Set column widths\n";
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);

// Establecemos la fuente de la letra
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);

$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);

$objPHPExcel->getActiveSheet()->getStyle('J'.$j)->getFont()->setBold(true);

// Establecemos la alineacion del texto
$objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A3'), 'B3:L3' );

$objPHPExcel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('F4'), 'F5:F'.$i );

//$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setShrinkToFit(true);



// Agregamos el logo
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setPath('./imagenes/logo.jpg');
$objDrawing->setHeight(50);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

// Aplicamos seguridad a las celdas
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	// Needs to be set to true in order to enable any worksheet protection!
$objPHPExcel->getActiveSheet()->protectCells('A3:L'.$i, 'samat');

// Definimos la orietacion y tipo de hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Renombramos la Hoja
$objPHPExcel->getActiveSheet()->setTitle('Control de Inscripciones');

// Predeterminamos la primera hoja (Obligatorio asi no existan  ma
$objPHPExcel->setActiveSheetIndex(0);

$archivo = str_replace('.php', '.xlsx', __FILE__);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->setOffice2003Compatibility(true);
$objWriter->save($archivo);

header ("Content-Disposition: attachment; filename=inscripcionExcel.xlsx"); 
header ("Content-Type: application/download ");
readfile($archivo);
?>