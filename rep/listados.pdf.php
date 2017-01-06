<?
include("comun/ini.php");
include("Constantes.php");
set_time_limit(0);
function dividirStr($str, $max){
	$strArray = array();
    do{
    	if (strlen($str) > $max)
        	$posF = strrpos( substr($str, 0, $max), ' ' );
		else
        	$posF = -1;
		if ($posF===false || $posF==-1){
	    	$strArray[] = substr($str, 0);
        	$str = substr($str, 0);
        	$posF = -1;
      	}else{
        	$strArray[] = substr($str, 0, $posF);
        	$str = substr($str, $posF+1 );
      	}
    }while ($posF != -1);
    return ($strArray);
}
class PDF extends FPDF{
//Cabecera de página
	function Header()
	{
		//CAPTURO VARIABLES
		$Titulo=$_REQUEST['Titulo'];
		$Logo=$_REQUEST['logo'];
		$Fecha=$_REQUEST['fecha'];
		$tamaño_letra = $_REQUEST['tamano_letra'];
		$oJSON = new Services_JSON();
		$campos = $oJSON->decode(stripslashes($_REQUEST['fields']));

		//LOGO
		$this->SetLeftMargin(5);
		$this->SetFont('Courier','',10);
		$this->Ln(1);
		if($Logo=='true'){
			$this->Image ("images/logoa.jpg",5,4,26);//logo a la izquierda 
			$this->SetXY(37, 7); 
			$textoCabecera = ENTE."\n\n";
			$textoCabecera.= DEPARTAMENTO."\n\n";
			$textoCabecera.= "Oficina de Recursos Humanos\n\n";
			$this->MultiCell(100,2, $textoCabecera, 0, 'L');
		}
		//FECHA
		if($Fecha=='true'){
			$this->SetXY(180, 7); 
			$this->Cell(180,5, "Fecha: ".date('d/m/Y'), 0, 'R');
		}
		//TITULO
		if($Logo=='true'){
			$this->Ln(30);
		}else{
			$this->Ln(5);
		}
		$this->SetFont('Courier','b',$tamaño_letra+2);
		$this->Cell(0, 5, $Titulo ,0,0,'C');
		$this->Ln(10);
		//TITULOS DE CAMPOS
		$this->SetFont('Courier','B',$tamaño_letra+1);
		for($i=0;$i<count($campos);$i++){
			$this->Cell($campos[$i][2],5,$campos[$i][1],1, 0,$campos[$i][3]);
		}
		$this->Ln(5);
	}

	function Footer()
	{
		//Número de página
		$NroP=$_REQUEST['nroP'];
		if($NroP=='true'){
			$this->SetFont('Arial','I',$tamaño_letra);
			$this->Cell(180,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
} 

//Creación del objeto de la clase heredada
$orientacion = $_REQUEST['orientacion_hoja'];
$tipo_hoja = $_REQUEST['tipo_hoja'];
$tamaño_letra = $_REQUEST['tamano_letra'];
//die($tipo_hoja);
$pdf=new PDF($orientacion,'mm',$tipo_hoja);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetFont('Courier','',$tamaño_letra);
//CAPTURO VARIABLES
$oJSON = new Services_JSON();
$campos = $oJSON->decode(stripslashes($_REQUEST['fields']));
$condiciones = $oJSON->decode(stripslashes($_REQUEST['condiciones']));
$orden = $oJSON->decode(stripslashes($_REQUEST['orders']));
$objeto = $_REQUEST['tabla'];
$oObjeto = get_all_listado($conn,$objeto,$condiciones,$orden);
//var_dump($oObjeto);
$cantCampos=count($campos);
if(is_array($oObjeto)){
	
	foreach($oObjeto as $objeto){
		$x=0;
		for($i=0;$i<$cantCampos;$i++){
			$desc[$i] = dividirStr($objeto->$campos[$i][0], intval($campos[$i][2]/$pdf->GetStringWidth('M')));
			$x= (count($desc[$i])>=$x) ? count($desc[$i]) : $x; 
		}
		for($j=0;$j<$x;$j++){
			$xy=5;
			for($i=0;$i<$cantCampos;$i++){
				//$b = $j+1==$x ? 'B' : '';
				$pdf->Cell($campos[$i][2],5, $desc[$i][$j] ? utf8_decode($desc[$i][$j]) : '' ,0, 0,$campos[$i][3]);
				//$pdf->Line($xy, $pdf->GetY(), $xy, $pdf->GetY()+5);
				$xy+=$campos[$i][2];
			}
			//$pdf->Line($xy, $pdf->GetY(), $xy, $pdf->GetY()+5);
			$pdf->Ln(5);
		}
	} 
}else{
	$pdf->Ln(5);
	$pdf->Cell(0,5,'No se encontraron registros',0, 0,'L');
}
$pdf->Output();
?>
