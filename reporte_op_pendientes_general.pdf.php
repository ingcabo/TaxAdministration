<?
	include("comun/ini.php");
	include("Constantes.php");
	$fecha_desde = $_REQUEST['fecha_desde'];
	$fecha_hasta = $_REQUEST['fecha_hasta'];
	$id_proveedor = $_REQUEST['id_proveedor'];
	$id_ue = $_REQUEST['id_ue'];
	$tipoCom = $_REQUEST['tipocom'];
	$tipoProv = $_REQUEST['tipoprov'];
	$id_status = $_REQUEST['status'];
	
	
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
			$this->SetLeftMargin(18);
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

			$this->SetXY(300, 20); 
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
				$tipoOrden = "REPORTE ORDENES DE PAGO PENDIENTES";
			
			$this->Text(130, 40, $tipoOrden);
			$this->Line(18, 41, 365, 41);
			$this->Ln(20);
			//$this->Text(160, 40, '#');
			//$this->Text(175, 40, $id);
	}

	function Footer()
	{	

		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//N�mero de p�gina
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creaci�n del objeto de la clase heredada
$pdf=new PDF('l','mm','A3');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','B',10);
$pdf->SetLeftMargin(18);

//ORDENES DE PAGO
$q = "SELECT DISTINCT op.nrodoc as nrodoc, op.id_proveedor, op.id_unidad_ejecutora, op.fecha, ue.descripcion AS unidad_ejecutora, p.nombre AS proveedor,  op.montodoc AS montodoc, ";
$q.= " mp.nrodoc AS nrodoccom, mp.fechadoc AS fechacom, op.status, td.abreviacion ";
$q.= "FROM finanzas.orden_pago op ";
$q.= "INNER JOIN puser.unidades_ejecutoras ue ON (op.id_unidad_ejecutora = ue.id AND ue.id_escenario = '$escEnEje') ";
$q.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
$q.= "LEFT JOIN finanzas.solicitud_pago sp ON (sp.nrodoc = op.nroref) ";
$q.= "LEFT JOIN puser.movimientos_presupuestarios mp ON (mp.nrodoc = sp.nroref) ";
$q.= "LEFT JOIN puser.tipos_documentos td ON (substr(sp.nroref,1,3) = td.id )";
$q.= "WHERE (1=1) ";
$q.= !empty($id_status) ? "AND op.status = '$id_status' " : ""; 
$q.= !empty($fecha_desde) ? "AND op.fecha >= '".guardaFecha($fecha_desde)."' " : "";
$q.= !empty($fecha_hasta) ? "AND op.fecha <= '".guardaFecha($fecha_hasta)."' " : "";
$q.= !empty($id_proveedor) ? "AND op.id_proveedor = '$id_proveedor' " : "";
$q.= !empty($id_ue) ? "AND op.id_unidad_ejecutora = '$id_ue' " : "";
$q.= !empty($tipoCom) ? "AND COALESCE(mp.tipdoc,'0') = '$tipoCom' " : "";
$q.= !empty($tipoProv) ? "AND p.provee_contrat = '$tipoProv' " : "";
$q.= "ORDER BY op.nrodoc ";
//die($q);
$r = $conn->Execute($q);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(340,8,'ORDENES DE PAGO SIN PAGOS RELACIONADOS',1,1,'C');
//$pdf->Ln();
$pdf->Cell(65,4,'Unidad Ejecutora',1,0,'C');
$pdf->Cell(30,4,'N� Documento',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(35,4,'Monto',1,0,'C');
$pdf->Cell(65,4,'Beneficiario',1,0,'C');
$pdf->Cell(25,4,'Tipo. Doc.',1,0,'C');
$pdf->Cell(25,4,'Compromiso',1,0,'C');
$pdf->Cell(25,4,'Fecha',1,0,'C');
$pdf->Cell(20,4,'Status',1,0,'C');
$pdf->Ln();
$pdf->SetFont('Courier','',8);
while(!$r->EOF){
	$desc_unidad = dividirStr($r->fields['unidad_ejecutora'], intval(65/$pdf->GetStringWidth('M')));
	$pdf->Cell(65,4,utf8_decode($desc_unidad[0]),0,0,'L');
	$pdf->Cell(30,4,$r->fields['nrodoc'],0,0,'C');
	$pdf->Cell(25,4,muestraFecha($r->fields['fecha']),0,0,'C');
	$pdf->Cell(35,4,muestraFloat($r->fields['montodoc']),0,0,'R');
	$desc_proveedor = dividirStr($r->fields['proveedor'], intval(65/$pdf->GetStringWidth('M')));
	$pdf->Cell(65,4,utf8_decode($desc_proveedor[0]),0,0,'L');
	$pdf->Cell(25,4,$r->fields['abreviacion'],0,0,'C');
	$nrodoccom = (!empty($r->fields['nrodoccom'])) ? $r->fields['nrodoccom'] : "OP";
		$pdf->Cell(25,4,$nrodoccom,0,0,'C');
	$fechacom = (!empty($r->fields['fechacom'])) ? muestraFecha($r->fields['fechacom']) : "SIN IMPUTACION";
		$pdf->Cell(25,4,$fechacom,0,0,'C');
	switch($r->fields['status']){
	 	case "1":
			$status = "Registrada";
		break;
		case "2":
			$status = "Aprobada";
		break;
		case "3":
			$status = "Anulada";
		break;
	}
	if($r->fields['status'] != 3)
		$total_sp += $r->fields['montodoc'];
	
	$pdf->Cell(20,4,$status,0,0,'C');
	$hay_ue = next($desc_unidad);
	$hay_pv = next($desc_proveedor);
  		for ($i=1; $hay_ue!==false; $i++)
  		{
    		$pdf->Ln();
			$pdf->Cell(65,4, $desc_unidad[$i],0, 0,'L');
    		$pdf->Cell(30,4, '',0, '','L');
    		$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(35,4, '',0, '','L');
			$pdf->Cell(65,4, $desc_proveedor[$i],0, '','L');
			$pdf->Cell(25,4,'',0,0,'C');
			$pdf->Cell(30,4, '',0, '','L');
			$pdf->Cell(25,4, '',0, '','L');
			$pdf->Cell(20,4, '',0, '','C');
    		$hay_ue = next($desc_unidad);
			$hay_pv = next($desc_proveedor);
  		}
	$r->movenext();
	$pdf->Ln();
	
}
$pdf->Ln();
$pdf->SetFont('Courier','B',10);
$pdf->Cell(120,4,'Total:',0,0,'C');
$pdf->Cell(35,4,muestraFloat($total_sp),0,0,'R');
if($anoCurso == 2007){
	$pdf->Ln();
	$pdf->Cell(155,4,'Bs.F.: '.muestraFloat($total_sp/1000),0,0,'R');
}


$pdf->Output();
?>