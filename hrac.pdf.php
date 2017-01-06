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
			$this->SetXY(150, 7); 
			$this->MultiCell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
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
$cantCampos=count($campos);
$condiciones = $oJSON->decode(stripslashes($_REQUEST['condiciones']));
$orden = $oJSON->decode(stripslashes($_REQUEST['orders'])); 
$q = "SELECT A.int_cod,A.dep_nom FROM rrhh.departamento as A INNER JOIN rrhh.division as B ON A.div_cod=B.int_cod  WHERE B.emp_cod=".$_SESSION['EmpresaL']." AND A.dep_estatus=0 ORDER BY A.dep_ord";
$rD = $conn->Execute($q);
while(!$rD->EOF){
	$q = "SELECT int_cod FROM rrhh.trabajador WHERE dep_cod=".$rD->fields['int_cod'];
	if(is_array($condiciones)){
		for($i=0;$i<count($condiciones);$i++){
			if($condiciones[$i][0]=='cont_cod' ){
				$q.= " AND int_cod IN (SELECT tra_cod FROM rrhh.cont_tra WHERE cont_cod= ? ) " ;
			}elseif($condiciones[$i][1]!='IN'){
				$q.= " AND ".$condiciones[$i][0]." ".$condiciones[$i][1]." ? " ;
			}else{
				$q.= " AND ".$condiciones[$i][0]." ".$condiciones[$i][1]." (" ;
				$arrayIN= split(",",$condiciones[$i][2]);
				for($j=0;$j<count($arrayIN);$j++){
					$q.= ($j+1!=count($arrayIN)) ? "?," : "?) ";
				}
			}
		}
	}
	if(is_array($orden) && !empty($orden)){
		$q.= " ORDER BY ".implode(",",$orden);
	}
	$array= NULL;
	$rPrep_Sel = $conn->Prepare($q);
	for($i=0;$i<count($condiciones);$i++){
		if($condicones[$i][1]!='IN'){
			$array[]= $condiciones[$i][1]=='ILIKE' ? "%".$condiciones[$i][2]."%" : $condiciones[$i][2]; 
		}else{
			$arrayIN= split(",",$condiciones[$i][2]);
			for($j=0;$j<count($arrayIN);$j++){
				$array[]=$arrayIN[$j];
			}
		}
	}
	$rT = $conn->Execute($rPrep_Sel,$array); 
	if(!$rT->EOF){
		$xyz=0;
		for($i=0;$i<$cantCampos;$i++){
			$xyz+=$campos[$i][2];
		}
		$desc = dividirStr("Departamento: ".$rD->fields['dep_nom'], intval($xyz/$pdf->GetStringWidth('M')));
		for($i=0;$i<count($desc);$i++){
			$pdf->Ln(5);
			$b = $i+1==count($desc) ? 'B' : '';
			$pdf->Cell($xyz,5,utf8_decode($desc[$i]),$b, 0,'L');
		}
		$pdf->Ln(5);
		//TITULOS DE CAMPOS
		$pdf->SetFont('Courier','B',$tamaño_letra+1);
		for($i=0;$i<count($campos);$i++){
			$pdf->Cell($campos[$i][2],5,$campos[$i][1],'B', 0,$campos[$i][3]);
		}
		$pdf->SetFont('Courier','',$tamaño_letra+1);
		$pdf->Ln(5);
		$vacante=0;
		$totalTrabajadores=0;
		while(!$rT->EOF){
			$objeto = new trabajador;
			$objeto->get($conn, $rT->fields['int_cod']);
			$rT->movenext();
			$vacante+= $objeto->tra_vac;
			$totalTrabajadores++;
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
		$pdf->Cell($xyz,5,utf8_decode('Ocupados: '.($totalTrabajadores-$vacante).'      Vacantes: '.$vacante),'T', 0,'L');
		$pdf->Ln(5);
	}
	$rD->movenext();
}
$pdf->Output();
?>
