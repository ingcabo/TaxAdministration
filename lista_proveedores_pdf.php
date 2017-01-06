<?php
	include("comun/ini.php");
	include("Constantes.php");
	
	$oescenarios = new escenarios;
	$rescenarios = $oescenarios->get($conn, $escEnEje);
	
	$oalcaldia = new alcaldia;
	$ralcaldia = $oalcaldia->get($conn, '02');

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
    	}
		while ($posF != -1);
		return ($strArray);
  	}

	class PDF extends FPDF
	{
		function Header()
		{
			$this->SetLeftMargin(5);
			$this->SetFont('Courier','',10);
			$this->Ln(1);
			$this->Image ("images/logoa.jpg",5,4,50,20);//logo a la izquierda 
			$this->Ln(12);
			$this->Cell(50,2, "GERENCIA DE ADMINISTRACION Y CONTABILIDAD", 0, 'L');
			$this->SetXY(55, 7); 
			$textoCabecera = ENTE."\n";
			$textoCabecera.= "Direccion de Admnistracion y Finanzas\n\n";
			$textoCabecera.= "oficina de Recursos Humanos\n\n";
			$this->MultiCell(100,2, $textoCabecera, 0, 'L');
			$this->SetXY(250, 7);
			$this->Cell(50,2, "Fecha: ".date('d/m/Y'), 0, 'L');
			$this->Ln(4);
			$this->SetX(250);
			$this->Cell(50,2, "Hora: ".date('h:m:s a'), 0, 'L');
			$this->Ln(4);
			$this->SetX(250);
			$this->Cell(50,2,'Pagina '.$this->PageNo().'/{nb}',0,'L');
			
			$this->Ln(20);
			$this->SetFont('Courier','b',14);
			$this->Cell(0, 0, " RELACION DE PROVEEDORES " ,0,0,'C');
			$this->Ln(10);
			$this->SetFont('Courier','B',12);
			$this->Cell(190, 0, "CONTENIDO: ",0,0,'L');
			$this->Ln(10);
			$this->SetFont('Courier','B',10);
			//$pdf->SetFillColor  (250,250,250);
			$this->SetDrawColor (255,255,255);
			$this->SetWidths(array(11,70,25,80,25,50,25));			
			$this->SetAligns(array('C','L','C','L','C','L','C'));
			$this->Row(array("Cod.","Nombre o Razon Social","Cedula/RIF","Direccion","Telefono","Representante Legal","Cedula"),11);
			$this->Ln(1);
			$this->SetDrawColor (150,150,150);
			$this->Cell(286, 0, "",'T',0,'L');
			$this->Ln(1);
		}
		
		function Footer()
		{	
			$this->Ln(1);
			$this->SetDrawColor (150,150,150);
			$this->Cell(286, 0, "",'T',0,'L');
		}

	}
	//Creación del objeto de la clase heredada
	$pdf=new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	$pdf->SetFont('Courier', '', 8);
	//$pdf->SetLeftMargin(20);
	if($_GET['status']=='')
		$prov="SELECT id, nombre, rif_letra, rif_numero, direccion, telefono, nombre_representante, ci_representante FROM puser.proveedores ORDER BY id ASC ";
	else
		$prov="SELECT id, nombre, rif_letra, rif_numero, direccion, telefono, nombre_representante, ci_representante FROM puser.proveedores WHERE status = '".$_GET['status']."' ORDER BY id ASC ";
	$r_prov=$conn->Execute($prov);
	
	while(!$r_prov->EOF) 
	{
		$pdf->SetDrawColor (255,255,255);
		$pdf->SetWidths(array(11,70,25,80,25,50,25));			
		$pdf->SetAligns(array('C','L','C','L','C','L','C'));
		$pdf->Row(array($r_prov->fields['id'], $r_prov->fields['nombre'], $r_prov->fields['rif_letra'].' - '.$r_prov->fields['rif_numero'], $r_prov->fields['direccion'], $r_prov->fields['telefono'], $r_prov->fields['nombre_representante'], $r_prov->fields['ci_representante']),11);
		$r_prov->movenext();
	}
$pdf->Output();
?>