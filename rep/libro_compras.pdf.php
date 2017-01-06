<?
	include("comun/ini.php");
	include("Constantes.php");
	$mes=$_REQUEST['mes'];
	
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
			$this->SetLeftMargin(13);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",13,4,26);//logo a la izquierda 
			$this->SetXY(42, 12); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$textoCabecera.= DEPARTAMENTO."\n";
			$textoCabecera.= "CONTRIBUYENTE: Especial\n";
			$textoCabecera.= DIRECCION."\n";
			$textoCabecera.= RIF."\n";
			$this->MultiCell(70,2, $textoCabecera, 0, 'L');

			//$ovehiculo = unserialize($_SESSION['pdf']);
			//$tipo = $ovehiculo->id_tipo_documento;

			$this->SetXY(300, 12); 
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
				$tipoOrden = "LIBRO DE COMPRAS";
			
			//$this->Text(130, 40, $tipoOrden);
			$this->Line(13, 35, 340, 35);
			$this->Ln(20);
			//$this->Text(160, 40, '#');
			//$this->Text(175, 40, $id);
	}

	function Footer()
	{	

		//$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Número de página
		//$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF('l','mm','legal');
$pdf->AliasNbPages();
//$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(10);

//$oespectaculo = new espectaculo;
//$oespectaculo_id_contribuyente = $oespectaculo->id_contribuyente;die($oespectaculo_id_contribuyente);
//$oespectaculo->get($conn, $oespectaculo->id_contribuyente);

//$ocontribuyente = new contribuyente;
//$ocontribuyente->get($conn, $oespectaculo->id_contribuyente);
//$pdf->SetFillColor(232 , 232, 232);
//$pdf->Ln();//$pdf->Ln();
//$pdf->Ln(5);

for($i=1;$i<=$mes;$i++){
	$pdf->AddPage();
	$pdf->SetFont('Courier','',8);
	$nombMes = obtieneMes($i);
	$pdf->Ln(6);
	$pdf->Cell(70,4,'',0,'','C');
	$ano = date('Y');
	$pdf->Cell(210,4,'LIBRO DE COMPRAS CORRESPONDIENTE AL MES DE '.$nombMes.' DEL AÑO '.$ano,0,'','C');
	$pdf->Ln();
	
	$pdf->Ln();
	//BUSCAMOS EL ULTIMO DIA DEL MES
	$ultimo_dia = 28;
	while (checkdate($i,$ultimo_dia + 1,$ano)){
			   $ultimo_dia++;
			}
	$strMes = sprintf("%02d", $i);		
	$fechaIni = $ano.'-'.$strMes.'-01';
	$fechaFin = $ano.'-'.$strMes.'-'.$ultimo_dia;		
	
				
		$pdf->SetFont('Courier','',6);
		
		$pdf->Cell(8,4,'','LRT','','C');
		$pdf->Cell(15,4,'FECHA DE','LRT','','C');
		$pdf->Cell(17,4,'RIF','LRT','','C');
		$pdf->Cell(30,4,'NOMBRE','LRT','','C');
		$pdf->Cell(21,4,'NUMERO DE','LRT','','C');
		$pdf->Cell(15,4,'NUMERO DE','LRT','','C');
		$pdf->Cell(17,4,'NUMERO DE','LRT','','C');
		$pdf->Cell(13,4,'NUMERO','LRT','','C');
		$pdf->Cell(13,4,'NUMERO','LRT','','C');
		$pdf->Cell(12,4,'TIPO','LRT','','C');
		$pdf->Cell(15,4,utf8_decode('Nº FACTURA'),'LRT','','C');
		$pdf->Cell(29,4,'TOTAL COMPRAS','LRT','','C');
		$pdf->Cell(29,4,'COMPRAS SIN','LRT','','C');
		$pdf->Cell(30,4,'BASE','LRT','','C');
		$pdf->Cell(17,4,'%','LRT','','C');
		$pdf->Cell(25,4,'IMPUESTO','LRT','','C');
		$pdf->Cell(22,4,'IVA','LRT','','C');
		$pdf->Ln();
		$pdf->Cell(8,4,'','LRB','','C');
		$pdf->Cell(15,4,'FACTURA','LRB','','C');
		$pdf->Cell(17,4,'PROVEEDOR','LRB','','C');
		$pdf->Cell(30,4,'PROVEEDOR','LRB','','C');
		$pdf->Cell(21,4,'COMPROBANTE','LRB','','C');
		$pdf->Cell(15,4,'FACTURA','LRB','','C');
		$pdf->Cell(17,4,'CONTROL','LRB','','C');
		$pdf->Cell(13,4,'N/D','LRB','','C');
		$pdf->Cell(13,4,'N/C','LRB','','C');
		$pdf->Cell(12,4,'TRANS','LRB','','C');
		$pdf->Cell(15,4,utf8_decode('AFECTADA'),'LRB','','C');
		$pdf->Cell(29,4,'INCLUYENO EL IVA','LRB','','C');
		$pdf->Cell(29,4,'DERECHO A CRED. IVA','LRB','','C');
		$pdf->Cell(30,4,'IMPONIBLE','LRB','','C');
		$pdf->Cell(17,4,'ALICUOTA','LRB','','C');
		$pdf->Cell(25,4,'IVA','LRB','','C');
		$pdf->Cell(22,4,'RETENIDO','LRB','','C');
		$pdf->Ln();
		$pdf->SetFont('Courier','',6);
		$sql = "SELECT f.*, p.rif, p.nombre "; 
		$sql.= "FROM finanzas.facturas f ";
		$sql.= "INNER JOIN finanzas.retenciones_adiciones ra ON (f.id_retencion = ra.id) ";
		$sql.= "INNER JOIN finanzas.orden_pago op ON (f.nrodoc = op.nrodoc) ";
		$sql.= "INNER JOIN puser.proveedores p ON (op.id_proveedor = p.id) ";
		$sql.= "WHERE op.fecha>='$fechaIni' AND op.fecha<='$fechaFin' AND ra.es_iva = '1' ";
		//if($i=='6')
			//die($sql);
		$cfact = 1;
		$r = $conn->Execute($sql); //or die($q);
		while(!$r->EOF){
			
			$nombProv = dividirStr($r->fields['nombre'], intval(30/$pdf->GetStringWidth('M')));
			$lineaFin = count($nombProv);
			$marco = ($lineaFin>1) ? "'LR'" : "'LRB'";
			//die("$pdf->Cell(12,4,'01-REG',$marco,0,'C');");	
			$pdf->Cell(8,4,$cfact,$marco,0,'C');
			$pdf->Cell(15,4,muestraFecha($r->fields['fecha']),$marco,0,'C');
			$pdf->Cell(17,4,$r->fields['rif'],$marco,0,'C');
			$pdf->Cell(30,4,utf8_decode($nombProv[0]),$marco,0,'C');
			$pdf->Cell(21,4,$r->fields['nrocorret'],$marco,0,'C');
			$pdf->Cell(15,4,$r->fields['nrofactura'],$marco,0,'C');
			$pdf->Cell(17,4,$r->fields['nrocontrol'],$marco,0,'C');
			$pdf->Cell(13,4,'',$marco,0,'C');
			$pdf->Cell(13,4,'',$marco,0,'C');
			$pdf->Cell(12,4,'01-REG',$marco,0,'C');
			$pdf->Cell(15,4,'',$marco,0,'C');
			$pdf->Cell(29,4,muestraFloat($r->fields['monto']),$marco,0,'R');
			$pdf->Cell(29,4,muestraFloat($r->fields['monto_excento']),$marco,0,'R');
			$pdf->Cell(30,4,muestraFloat($r->fields['base_imponible']),$marco,0,'R');
			$pdf->Cell(17,4,muestraFloat($r->fields['iva']),$marco,0,'R');
			$pdf->Cell(25,4,muestraFloat($r->fields['monto_iva']),$marco,0,'R');
			$pdf->Cell(22,4,muestraFloat($r->fields['iva_retenido']),$marco,0,'R');
				$hay_prov = next($nombProv);
				for ($j=1; $hay_prov!==false; $i++)
				{
					$marco2 = ($lineaFin == $j+1) ? 'LRB' : 'LR';
					$pdf->Ln();
					$pdf->Cell(8,4,'',$marco2,0,'C');
					$pdf->Cell(15,4,'',$marco2,0,'C');
					$pdf->Cell(17,4,'',$marco2,0,'C');
					$pdf->Cell(30,4,utf8_decode($nombProv[$j]),$marco2,0,'C');
					$pdf->Cell(21,4,'',$marco2,0,'C');
					$pdf->Cell(15,4,'',$marco2,0,'C');
					$pdf->Cell(17,4,'',$marco2,0,'C');
					$pdf->Cell(13,4,'',$marco2,0,'C');
					$pdf->Cell(13,4,'',$marco2,0,'C');
					$pdf->Cell(12,4,'',$marco2,0,'C');
					$pdf->Cell(15,4,'',$marco2,0,'C');
					$pdf->Cell(29,4,'',$marco2,0,'C');
					$pdf->Cell(29,4,'',$marco2,0,'C');
					$pdf->Cell(30,4,'',$marco2,0,'C');
					$pdf->Cell(17,4,'',$marco2,0,'C');
					$pdf->Cell(25,4,'',$marco2,0,'C');
					$pdf->Cell(22,4,'',$marco2,0,'C');
					$hay_prov = next($nombProv);
				}
			$total_fact+= $r->fields['monto'];
			$total_bi+= $r->fields['base_imponible'];
			$total_iva+= $r->fields['monto_iva'];
			$total_ret+= $r->fields['iva_retenido'];
			$cfact++;
			$pdf->Ln();
			$r->movenext();
		}
		$cfact--;
		$pdf->Ln();
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(176,4, 'Total Operaciones: '.$cfact,0,'','L');
		$pdf->Cell(29,4, muestraFloat($total_fact),0,'','R');
		$pdf->Cell(29,4, '',0,'','C');
		$pdf->Cell(30,4, muestraFloat($total_bi),0,'','R');
		$pdf->Cell(17,4, '',0,'','C');
		$pdf->Cell(25,4, muestraFloat($total_iva),0,'','R');
		$pdf->Cell(22,4, muestraFloat($total_ret),0,'','R');
		
		

}	


$pdf->Output();
?>