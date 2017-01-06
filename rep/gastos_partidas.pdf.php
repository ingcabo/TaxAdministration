<?
	include("comun/ini.php");
	include("Constantes.php");
	
	$q="SELECT A.id as partida, A.descripcion as descripcion, A.ano, A.madre as madre, B.id , B.id_categoria_programatica, sum(C.monto) as monto, substring(B.id_categoria_programatica,1,2) as sector 
		FROM puser.partidas_presupuestarias as A
		INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
		INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
		INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
		where A.id_escenario = '1111' AND  A.id like '4%' AND (D.status = '2') AND B.id_asignacion = 1  
		group by A.id, A.descripcion, A.ano, A.madre, B.id , B.id_categoria_programatica 
		UNION(SELECT A.id as partida, A.descripcion as descripcion, A.ano, A.madre as madre, B.id , B.id_categoria_programatica, sum(C.monto) as monto, substring(B.id_categoria_programatica,1,2) as sector 
		FROM puser.partidas_presupuestarias as A
		INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
		INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
		INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
		where A.id_escenario = '1111' AND  A.id like '4%' AND (D.status = '1') AND B.id_asignacion = 4  
		group by A.id, A.descripcion, A.ano, A.madre, B.id , B.id_categoria_programatica )
		UNION (SELECT E.id, E.descripcion, E.ano, E.madre, 0 as id, '0' as id_categoria_programatica, 0 as  monto, '0' as sector 
		FROM puser.partidas_presupuestarias as E  
		where E.id_escenario = '1111' AND  E.id LIKE '4%') 
		order by 1,6";		
		//die($q);
		$rparprecatsect = $conn->Execute($q);
		
	$q = "SELECT A.id as partida, A.descripcion as descripcion, A.ano, A.madre as madre, sum(C.monto) as monto
		FROM puser.partidas_presupuestarias as A
		INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
		INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
		INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
		where A.id_escenario = '1111' AND  A.id LIKE '4%' AND D.status = '2' AND B.id_asignacion = 1 
		group by A.id, A.descripcion, A.ano, A.madre
		UNION(SELECT A.id as partida, A.descripcion as descripcion, A.ano, A.madre as madre, sum(C.monto) as monto
		FROM puser.partidas_presupuestarias as A
		INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
		INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
		INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
		where A.id_escenario = '1111' AND  A.id LIKE '4%' AND D.status = '1' AND B.id_asignacion = 4 
		group by A.id, A.descripcion, A.ano, A.madre)
		order by 1";	
		$rparprecat = $conn->Execute($q);
		$totalpartida = array();
		while(!$rparprecat->EOF){
			$totalpartida[$rparprecat->fields['partida']]=$rparprecat->fields['monto'];
			$rparprecat->movenext();
		}
		$acumuladoMadreSector = array();
		//die(var_dump($rparpre));
	
class PDF extends FPDF
{
  var $leftMargin = 10;
  var $rightMargin = 350;
  var $fontStyle = 'courier';
  var $fontBodySize = 7;
  var $fontHeaderSize = 6;
  var $fontHeaderTitleSize = 12;
  var $cellColWidth = 4;
  var $cellDescWidth = 71;
  var $cellTotalWidth = 28;
  var $cellSectWidth = 27;
  var $codSector;
  var $descSector;
  var $escEnEje;
  var $i;
  
	function Header()
	{
			$this->SetLeftMargin($this->leftMargin);
			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Ln(1);
			$this->Rect($this->leftMargin, 4, $this->rightMargin-$this->leftMargin, 45);
			$this->Image ("images/logoa.jpg",$this->leftMargin+1,5,26);//logo a la izquierda 
			$this->SetXY(42, 20); 
			$textoCabecera = PAIS."\n";
			$textoCabecera.= UBICACION."\n";
			$textoCabecera.= ENTE."\n";
			$this->MultiCell(50,2, $textoCabecera, 0, 'L');

			$this->SetXY(225, 20); 
			$textoDerecha = "Fecha: ".date('d/m/Y')."\n";
			$textoDerecha.= "Pag: ".$this->PageNo()." de {nb}\n";
			$this->MultiCell(120,2, "DIRECCION DE PLANIFICACION Y PROYECTOS\nPLANIFICACION DE ADMINISTRACION", 0, 'R');
			
			$this->Ln(8);

			$this->SetFont($this->fontStyle, 'B', $this->fontHeaderTitleSize);
			$this->MultiCell($this->rightMargin-$this->leftMargin,2, "GASTOS DEL MUNICIPIO\n\nPOR SECTORES A NIVEL DE PARTIDAS Y SUB-PARTIDAS", 0, 'C');

			$this->SetFont($this->fontStyle, '', $this->fontHeaderSize);
			$this->Text(16, 48, 'Presupuesto: Año ' . date ('Y'));
      //$this->Text(16, 48, 'Presupuesto: Año ' . $this->escEnEje);
			//---- Parte donde va el código y el nombre del progarma.
			//$this->Rect($this->leftMargin, 51, $this->rightMargin-$this->leftMargin, 4);
			$this->SetY(51);
			//$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			//$this->Cell(($this->cellColWidth*5)+1, 4, 'Codigo', RB, '', 'L');
			//$this->Cell($this->cellColWidth, 4, $this->codSector, R, '', 'C');
			//$this->Cell($this->cellDescWidth+$this->cellTotalWidth, 4, 'Denominacion del Sector: ' . $this->descSector, B, '', 'C');
			//$this->Ln(4);
			//---- fin Parte donde va el código y el nombre del progarma.
			
			//---- Cabeceras de partidas y subpartidas, denominación y monto
			$this->Cell($this->cellColWidth+1, 4, '',  TLR);
			$this->Cell($this->cellColWidth*5, 4, 'Subpartidas', TRB);
			$this->Cell($this->cellDescWidth, 4, '', TR);
			$this->Cell($this->cellTotalWidth, 4, '', TR);
			$this->Cell($this->cellSectWidth*8, 4, 'SECTORES', TRB,'', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'G', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');	
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'A', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'U', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, 'R', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'N', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'B', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Ln();

			$this->Cell($this->cellColWidth+1, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 4, '', LR, '', 'C');
			$this->SetFont($this->fontStyle, 'B', $this->fontBodySize);
			$this->Cell($this->cellDescWidth, 4, 'Denominacion', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 4, 'Total Gastos', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 4, '01', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '08', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '09', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '11', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '12', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '13', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 4, '14', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 4, '15', LR, '', 'C');
			$this->SetFont($this->fontStyle, '', $this->fontBodySize);
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'E', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'S', LR, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LR, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LR, '', 'C');
			$this->Ln();
			
			$this->Cell($this->cellColWidth+1, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellColWidth, 3, 'P', LRB, '', 'C');
			$this->Cell($this->cellDescWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellTotalWidth, 3, '', LRB, '', 'C');
			//sectores
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');			
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Cell($this->cellSectWidth, 3, '', LRB, '', 'C');
			$this->Ln();
			//---- fin Cabeceras de partidas y subpartidas, denominación y monto
	}

	function Footer()
	{	
		$this->Line($this->leftMargin, $this->GetY(), $this->rightMargin, $this->GetY());
		$this->SetFont($this->fontStyle, '', $this->fontBodySize);
	}
}
//Creación del objeto de la clase heredada
$pdf=new PDF('L','mm','legal');
$pdf->codSector = '00';
$pdf->descSector = 'Falta';
$pdf->escEnEje = $escEnEje;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetLeftMargin($pdf->leftMargin);

$tPresupuesto = 0;
$control = 0;
$y=48;
$acumulado = array();
$totalSectores=array();

while(!$rparprecatsect->EOF)
{

	$acumulado= array_fill(0, 8, 0);
	$id = $rparprecatsect->fields['partida'];
	$descripcion = $rparprecatsect->fields['descripcion'];
	$madre = $rparprecatsect->fields['madre'];
	$sector = $rparprecatsect->fields['sector'];
	if($sector == '01' || '0'){
			$acumulado[0]+= $rparprecatsect->fields['monto'];
			$tSectorA+=$rparprecatsect->fields['monto'];
			}
	if($sector == '08'){
			$acumulado[1]+= $rparprecatsect->fields['monto'];
			$tSectorB+=$rparprecatsect->fields['monto'];
			}
	if($sector == '09'){
			$acumulado[2]+= $rparprecatsect->fields['monto'];
			$tSectorC+=$rparprecatsect->fields['monto'];
			}
	if($sector == '11'){
			$acumulado[3]+= $rparprecatsect->fields['monto'];
			$tSectorD+=$rparprecatsect->fields['monto'];
			}
	if($sector == '12'){
			$acumulado[4]+= $rparprecatsect->fields['monto'];
			$tSectorE+=$rparprecatsect->fields['monto'];
			}
	if($sector == '13'){
			$acumulado[5]+= $rparprecatsect->fields['monto'];
			$tSectorF+=$rparprecatsect->fields['monto'];
			}
	if($sector == '14'){
			$acumulado[6]+= $rparprecatsect->fields['monto'];
			$tSectorG+=$rparprecatsect->fields['monto'];
			}
	if($sector == '15'){
			$acumulado[7]+= $rparprecatsect->fields['monto'];
			$tSectorH+=$rparprecatsect->fields['monto'];
			}
	$madre = $rparprecatsect->fields['madre'];
	
	$rparprecatsect->movenext();
	
	while(!$rparprecatsect->EOF && $id == $rparprecatsect->fields['partida']){
		$id = $rparprecatsect->fields['partida'];
		$descripcion = 	$rparprecatsect->fields['descripcion'];
		$madre = $rparprecatsect->fields['madre'];
		$sector = $rparprecatsect->fields['sector'];
		
		while(!$rparprecatsect->EOF && $sector == $rparprecatsect->fields['sector']){
			$sector = $rparprecatsect->fields['sector'];
			if($sector == '01' || '0'){
					$acumulado[0]+= $rparprecatsect->fields['monto'];
					$tSectorA+=$rparprecatsect->fields['monto'];
					}
			if($sector == '08'){
					$acumulado[1]+= $rparprecatsect->fields['monto'];
					$tSectorB+=$rparprecatsect->fields['monto'];
					}
			if($sector == '09'){
					$acumulado[2]+= $rparprecatsect->fields['monto'];
					$tSectorC+=$rparprecatsect->fields['monto'];
					}
			if($sector == '11'){
					$acumulado[3]+= $rparprecatsect->fields['monto'];
					$tSectorD+=$rparprecatsect->fields['monto'];
					}
			if($sector == '12'){
					$acumulado[4]+= $rparprecatsect->fields['monto'];
					$tSectorE+=$rparprecatsect->fields['monto'];
					}
			if($sector == '13'){
					$acumulado[5]+= $rparprecatsect->fields['monto'];
					$tSectorF+=$rparprecatsect->fields['monto'];
					}
			if($sector == '14'){
					$acumulado[6]+= $rparprecatsect->fields['monto'];
					$tSectorG+=$rparprecatsect->fields['monto'];
					}
			if($sector == '15'){
					$acumulado[7]+= $rparprecatsect->fields['monto'];
					$tSectorH+=$rparprecatsect->fields['monto'];
					}
			$rparprecatsect->movenext();
			}
		//$rparprecatsect->movenext();
	}
	if($madre=='1'){
		$acumuladoMadre=0;
		$acumuladoMadreSector= array_fill(0, 8, 0);
		$q = "SELECT sum(C.monto) as monto
			FROM puser.partidas_presupuestarias as A
			INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
			INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
			INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
			where A.id_escenario = '1111' AND  A.id LIKE '".substr($id, 0, 3)."%' AND D.status = '2' AND B.id_asignacion = 1 
			UNION(SELECT sum(C.monto) as monto
			FROM puser.partidas_presupuestarias as A
			INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
			INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
			INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
			where A.id_escenario = '1111' AND  A.id LIKE '".substr($id, 0, 3)."%' AND D.status = '1' AND B.id_asignacion = 4 )
			order by 1";
			//die($q);
			$rmadreparprecat = $conn->Execute($q);

			while(!$rmadreparprecat->EOF){
				$acumuladoMadre+= $rmadreparprecat->fields['monto'];
				$rmadreparprecat->movenext();
			}
		$q = "SELECT sum(C.monto) as monto, substring(B.id_categoria_programatica,1,2) as sector 
			FROM puser.partidas_presupuestarias as A
			INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
			INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
			INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
			where A.id_escenario = '1111' AND  A.id like '".substr($id, 0, 3)."%' AND (D.status = '2') AND B.id_asignacion = 1  
			group by sector
			UNION(SELECT sum(C.monto) as monto, substring(B.id_categoria_programatica,1,2) as sector 
			FROM puser.partidas_presupuestarias as A
			INNER JOIN puser.relacion_pp_cp AS B ON (A.id = B.id_partida_presupuestaria AND A.id_escenario = B.id_escenario)
			INNER JOIN puser.relacion_movimientos AS C ON (B.id = C.id_parcat) 
			INNER JOIN puser.movimientos_presupuestarios AS D ON (C.nrodoc =  D.nrodoc)
			where A.id_escenario = '1111' AND  A.id like '".substr($id, 0, 3)."%' AND (D.status = '1') AND B.id_asignacion = 4  
			group by sector)
			order by 2";
			$rmadreparprecatsector = $conn->Execute($q);
			while(!$rmadreparprecatsector->EOF){
			$sectorMadre = $rmadreparprecatsector->fields['sector'];
			if($sectorMadre == '01'){
					$acumuladoMadreSector[0]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '08'){
					$acumuladoMadreSector[1]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '09'){
					$acumuladoMadreSector[2]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '11'){
					$acumuladoMadreSector[3]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '12'){
					$acumuladoMadreSector[4]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '13'){
					$acumuladoMadreSector[5]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '14'){
					$acumuladoMadreSector[6]+= $rmadreparprecatsector->fields['monto'];
					}
			if($sectorMadre == '15'){
					$acumuladoMadreSector[7]+= $rmadreparprecatsector->fields['monto'];
					}
			$rmadreparprecatsector->movenext();
			}
	}
  $maxPal = intval($pdf->cellDescWidth/$pdf->GetStringWidth('0'));
    if (strlen($descripcion) >= $maxPal)
    {
      $strArray = array();
      do
      {
        if (strlen($descripcion) >= $maxPal)
          $posF = strrpos( substr( $descripcion, 0, $maxPal ), ' ' );
        else
          $posF = -1;
        
        if ($posF===false || $posF==-1)
        {
          $strArray[] = substr( $descripcion, 0 );
          $descripcion = substr( $descripcion, 0 );
          $posF = -1;
        }
        else
        {
          $strArray[] = substr( $descripcion, 0, $posF );
          $descripcion = substr( $descripcion, $posF );
        }
      }while ($posF != -1);

	 
    if ($madre == '1')
			$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
	    $pdf->Cell($pdf->cellColWidth+1, 4, substr($id, 0, 3), L, '', 'C');
	    $pdf->Cell($pdf->cellColWidth, 4, substr($id, 3, 2), L, '', 'C');
	    $pdf->Cell($pdf->cellColWidth, 4, substr($id, 5, 2), L, '', 'C');
	    $pdf->Cell($pdf->cellColWidth, 4, substr($id, 7, 2), L, '', 'C');
	    $pdf->Cell($pdf->cellColWidth, 4, substr($id, 9, 2), L, '', 'C');
	    $pdf->Cell($pdf->cellColWidth, 4, substr($id, 11, 2), L, '', 'C');
		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[0]), L, '','L' );
		$pdf->Cell($pdf->cellTotalWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadre):(!empty($totalpartida[$id])?muestrafloat($totalpartida[$id]):''), LR, '','R');
		//sectores
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[0]) :(!empty($acumulado[0])?muestrafloat($acumulado[0]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[1]) :(!empty($acumulado[1])?muestrafloat($acumulado[1]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[2]) :(!empty($acumulado[2])?muestrafloat($acumulado[2]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[3]) :(!empty($acumulado[3])?muestrafloat($acumulado[3]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[4]) :(!empty($acumulado[4])?muestrafloat($acumulado[4]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[5]) :(!empty($acumulado[5])?muestrafloat($acumulado[5]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[6]) :(!empty($acumulado[6])?muestrafloat($acumulado[6]):''), L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[7]) :(!empty($acumulado[7])?muestrafloat($acumulado[7]):''), LR, '', 'R');

  	  for ($i=1,$lon=count($strArray); next($strArray); $i++)
  	  {
    	  $pdf->Ln();
        $pdf->Cell($pdf->cellColWidth+1, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellColWidth, 4, '', L, '','C' );
    		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($strArray[$i]), L, '','L' );
    	  $pdf->Cell($pdf->cellTotalWidth, 4, '', LR, '','R' );
		//sectores
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');		  
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');	
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');	
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');	
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');	
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');
			$pdf->Cell($pdf->cellSectWidth, 4,'', L, '', 'R');	
			$pdf->Cell($pdf->cellSectWidth, 4,'', LR, '', 'R');			
      }
	 }
	 else{
		if ($madre == '1')
		  $pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
		$pdf->Cell($pdf->cellColWidth+1, 4, substr($id, 0, 3), L, '', 'C');
		$pdf->Cell($pdf->cellColWidth, 4, substr($id, 3, 2), L, '', 'C');
		$pdf->Cell($pdf->cellColWidth, 4, substr($id, 5, 2), L, '', 'C');
		$pdf->Cell($pdf->cellColWidth, 4, substr($id, 7, 2), L, '', 'C');
		$pdf->Cell($pdf->cellColWidth, 4, substr($id, 9, 2), L, '', 'C');
		$pdf->Cell($pdf->cellColWidth, 4, substr($id, 11, 2), L, '', 'C');
		$pdf->Cell($pdf->cellDescWidth, 4, utf8_decode($descripcion), L, '','L' );
		$pdf->Cell($pdf->cellTotalWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadre) : (!empty($totalpartida[$id])?muestrafloat($totalpartida[$id]):''), LR, '','R');
		//sectores
		
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[0]):(!empty($acumulado[0])?muestrafloat($acumulado[0]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[1]):(!empty($acumulado[1])?muestrafloat($acumulado[1]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[2]):(!empty($acumulado[2])?muestrafloat($acumulado[2]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[3]):(!empty($acumulado[3])?muestrafloat($acumulado[3]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[4]):(!empty($acumulado[4])?muestrafloat($acumulado[4]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[5]):(!empty($acumulado[5])?muestrafloat($acumulado[5]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[6]):(!empty($acumulado[6])?muestrafloat($acumulado[6]):''), L, '', 'R');
				$pdf->Cell($pdf->cellSectWidth, 4, $madre == '1' ? muestrafloat($acumuladoMadreSector[7]):(!empty($acumulado[7])?muestrafloat($acumulado[7]):''), LR, '', 'R');
	 
	 }
	$pdf->ln();
	      $pdf->SetFont($pdf->fontStyle, '', $pdf->fontBodySize);	 
	//$rparprecatsect->movenext();	
}

$pdf->SetFont($pdf->fontStyle, 'B', $pdf->fontBodySize);
$pdf->Cell(($pdf->cellColWidth*6)+1+$pdf->cellDescWidth, 4, 'TOTALES', 1, '', 'C' );
$pdf->Cell($pdf->cellTotalWidth, 4, muestrafloat(array_sum($totalpartida)), 1, '', 'R' );
//sectores
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorA), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorB), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorC), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorD), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorE), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorF), 1, '', 'R');			
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorG), 1, '', 'R');
$pdf->Cell($pdf->cellSectWidth, 4, muestrafloat($tSectorH), 1, '', 'R');
$pdf->Ln();
$pdf->Output();
?>
