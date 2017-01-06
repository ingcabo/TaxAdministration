<? //SE7H[ENE][2006]
include ("comun/ini.php");
require('lib/fpdf.php');
set_time_limit(0);

$id_proveedores=$_REQUEST['id_proveedores'];
$sql="SELECT  
  proveedores.nombre, 
  proveedores.nit, 
  proveedores.nro_trabajadores,
  proveedores.direccion,
  proveedores.id_estado,
  proveedores.fecha,  
  proveedores.id_municipio,
  proveedores.id_parroquia,
  proveedores.status,
  proveedores.datos_reg,
  proveedores.ci_representante,
  proveedores.nombre_representante,
  proveedores.registro_const,
  proveedores.contacto,
  proveedores.accionistas,
  proveedores.junta_directiva,
  proveedores.telefono,
  proveedores.fax,
  proveedores.email,
  proveedores.ci_comisario,
  proveedores.nombre_comisario,
  proveedores.ci_responsable,
  proveedores.nombre_responsable,
  proveedores.cap_suscrito,
  proveedores.cap_pagado,
  proveedores.id_grupo_prove,
  proveedores.rif
FROM
 proveedores
WHERE id=".$id_proveedores;
 
$rs = @$conn->Execute($sql);  
$nombre=strtoupper($rs->fields['nombre']);
$rif=$rs->fields['rif'];
$nit=$rs->fields['nit'];
$fecha=$rs->fields['fecha'];
$entidad=$rs->fields['id_estado'].$rs->fields['id_municipio'].$rs->fields['id_parroquia'];

class PDF extends FPDF
{
//Cabecera de página
	function Header()
	{
			$this->SetLeftMargin(15);
			$this->SetFont('Courier','',6);
			$this->Ln(1);
			$this->Image ("images/logo_alc.jpg",15,4,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$this->MultiCell(50,2,"REPUBLICA BOLIVARIANA DE VENEZUELA\nESTADO CARABOBO\nALCALDIA DEL MUNICIPIO LIBERTADOR", 0, 'L');
			
			$this->SetXY(150, 20); 
			$this->MultiCell(50,2,"Fecha: ".date('d/m/y')."\nPág: ".$this->PageNo()." de {nb}\nHora: ".date('h:i:s A'), 0, 'L');
			
			$this->SetFont('Courier','b',8);
			$this->Text(80, 40, 'FICHA DE DATOS DEL PROVEEDOR');
			$this->Line(15, 41, 190, 41);
			$this->Ln(16);
			
	}

//Pie de página
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
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$pdf->SetLeftMargin(15);

			$pdf->SetFillColor(232 , 232, 232);
			$pdf->Cell(25,4, 'Código',1, '','C', 1 );
			$pdf->Cell(25,4, 'Contraloría',1, '','C', 1 );
			$pdf->Cell(20,4, 'Fecha',1, '','C', 1 );
			$pdf->Cell(105,4, 'Nombre del Proveedor',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(25,4, $id_proveedores,1, '','C');
			$pdf->Cell(25,4, '_?_?-?-',1, '','C');
			$pdf->Cell(20,4, $fecha,1, '','C');
			$pdf->Cell(105,4, $nombre,1, '','C');
			
			$pdf->Ln();
			$pdf->Cell(25,4, 'Rif',1, '','C', 1 );
			$pdf->Cell(25,4, 'Nit',1, '','C', 1 );
			$pdf->Cell(20,4, 'Nº Trab',1, '','C', 1 );
			$pdf->Cell(105,4, 'Dirección',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(25,4, $rif,1, '','C');
			$pdf->Cell(25,4, $nit,1, '','C');
			$pdf->Cell(20,4, $rs->fields['nro_trabajadores'],1, '','C');
			$pdf->MultiCell(105,4, $rs->fields['direccion'],1, 'L');
			
			$pdf->Ln();
			$pdf->Cell(25,4, 'Entidad',1, '','C', 1 );
			$pdf->Cell(25,4, 'Fecha Reg',1, '','C', 1 );
			$pdf->Cell(125,4, 'Datos del Registro',1, '','C', 1 );

			$pdf->Ln();
			$pdf->Cell(25,4, $entidad,1, '','C');
			$pdf->Cell(25,4, $rs->fields['registro_const'],1, '','C');
			$pdf->Cell(125,4, $rs->fields['datos_reg'],1, '','C');	
			
			$pdf->Ln();
			$pdf->Cell(25,4, 'Cédula Rep.',1, '','C', 1 );
			$pdf->Cell(150,4, 'Nombre Representante',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(25,4,str_replace(' ','',$rs->fields['ci_representante']),1, '','C');
			$pdf->MultiCell(150,4, $rs->fields['nombre_representante'],1, '','C');	
			
			$pdf->Ln();
			$pdf->Cell(175,4, 'Contactos',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(175,4, $rs->fields['contacto'],1, '','C');
			
			$pdf->Ln();
			$pdf->Cell(175,4, 'Accionistas',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(175,4, $rs->fields['accionistas'],1, '','C');
			
			$pdf->Ln();
			$pdf->Cell(175,4, 'Junta Directiva',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(175,4,$rs->fields['junta_directiva'],1, '','C');
			
			$pdf->Ln();
			$pdf->Cell(58,4, 'Teléfonos',1, '','C', 1 );
			$pdf->Cell(58,4, 'Fax',1, '','C', 1 );
			$pdf->Cell(59,4, 'E-mail',1, '','C', 1 );

			$pdf->Ln();
			$pdf->Cell(58,4,$rs->fields['telefono'],1, '','C');
			$pdf->Cell(58,4,$rs->fields['fax'],1, '','C');
			$pdf->Cell(59,4,$rs->fields['email'],1, '','C');
			
			$pdf->Ln();
			$pdf->Cell(25,4, 'Cédula Comis.',1, '','C', 1 );
			$pdf->Cell(150,4, 'Nombre Comisario',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(25,4,$rs->fields['ci_comisario'],1, '','C');
			$pdf->Cell(150,4,$rs->fields['nombre_comisario'],1, '','C');			

			$pdf->Ln();
			$pdf->Cell(25,4, 'Cédula Resp.',1, '','C', 1 );
			$pdf->Cell(150,4, 'Nombre Responsable',1, '','C', 1 );
			
			$pdf->Ln();
			$pdf->Cell(25,4,$rs->fields['ci_responsable'],1, '','C');
			$pdf->Cell(150,4,$rs->fields['nombre_responsable'],1, '','C');	
			
			$pdf->Ln();
			$pdf->Cell(35,4, 'Capital Suscrito',1, '','C', 1 );
			$pdf->Cell(35,4, 'Capital Pagado',1, '','C', 1 );
			$pdf->Cell(12,4, 'Status',1, '','C', 1 );
			$pdf->Cell(20,4, 'Grupo Prov',1, '','C', 1 );
			$pdf->Cell(73,4, 'Firma Y Sello',1, '','C', 1 );			
			
			$pdf->Ln();
			$pdf->Cell(35,4, $rs->fields['cap_suscrito'],1, '','C');
			$pdf->Cell(35,4, $rs->fields['cap_pagado'],1, '','C');
			$pdf->Cell(12,4, $rs->fields['status'],1, '','C');
			$pdf->Cell(20,4, $rs->fields['id_grupo_prove'],1, '','C');	
			$pdf->Cell(73,4, '',1, '','C');												

#	

$pdf->Output();
?> 