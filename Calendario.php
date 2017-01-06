<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
	$Ano=$_REQUEST['Ano'];
	$Res= "<table width=\"600\" border=\"0\">\n";
	for($tr=1;$tr<=3;$tr++){
		$Res.= "<tr valign=\"top\" >\n";
		for($td=1;$td<=4;$td++){
			if($tr==1 && $td==1){
				$MesNum=01;
				$MesText="Enero";
			}
			if($tr==1 && $td==2){
				$MesNum=02;
				$MesText="Febrero";
			}
			if($tr==1 && $td==3){
				$MesNum=03;
				$MesText="Marzo";
			}
			if($tr==1 && $td==4){
				$MesNum=04;
				$MesText="Abril";
			}
			if($tr==2 && $td==1){
				$MesNum=05;
				$MesText="Mayo";
			}
			if($tr==2 && $td==2){
				$MesNum=06;
				$MesText="Junio";
			}
			if($tr==2 && $td==3){
				$MesNum=07;
				$MesText="Julio";
			}
			if($tr==2 && $td==4){
				$MesNum='08';
				$MesText="Agosto";
			}
			if($tr==3 && $td==1){
				$MesNum='09';
				$MesText="Septiembre";
			}
			if($tr==3 && $td==2){
				$MesNum=10;
				$MesText="Octubre";
			}
			if($tr==3 && $td==3){
				$MesNum=11;
				$MesText="Noviembre";
			}
			if($tr==3 && $td==4){
				$MesNum=12;
				$MesText="Diciembre";
			}
			$Res.= "<td >\n";
				$Res.= "<table style=\"border:solid 1px;height:150px;border-color:#000000\"  >\n";
				$Res.= "<tr >\n";
				$Res.= "<td colspan=\"7\"  align=\"center\">$MesText</td>\n";
				$Res.= "</tr>\n";
				$Res.= "<tr>\n";
				$Res.= "<td>Dom</td>\n";
				$Res.= "<td>Lun</td>\n";
				$Res.= "<td>Mar</td>\n";
				$Res.= "<td>Mie</td>\n";
				$Res.= "<td>Jue</td>\n";
				$Res.= "<td>Vie</td>\n";
				$Res.= "<td>Sab</td>\n";
				$Res.= "</tr>\n";
				$Filas=1;
				if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Sunday"){
					$Res.= "<tr align=\"center\">\n";
					$Res.="<td>&nbsp;</td>\n";
					if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Monday"){
						$Res.="<td>&nbsp;</td>\n";
						if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Tuesday"){
							$Res.="<td>&nbsp;</td>\n";
							if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Wednesday"){
								$Res.="<td>&nbsp;</td>\n";
								if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Thursday"){
									$Res.="<td>&nbsp;</td>\n";
									if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Friday"){
										$Res.="<td>&nbsp;</td>\n";
										if(date("l",mktime(0,0,0,$MesNum,1,$Ano))!="Saturday"){
											$Res.="<td>&nbsp;</td>\n";
										}	
									}
								}
							}
						}
					}
				}
				for($Dia=1;$Dia<=DiaFin($MesNum);$Dia++){
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Sunday"){
						$Res.= "<tr align=\"center\">\n";
					}
					$q="SELECT * FROM rrhh.feriados WHERE fecha='$Ano-$MesNum-$Dia'";
					$r= $conn->Execute($q);
					$Color="transparent";
					if(!$r->EOF){
						$Color="#FFD5D5";
					}
					//DOMINGO
					$Res.= "<td id=\"".$Ano."_".$MesNum."_".$Dia."\" style=\"cursor:pointer;background-color:$Color \" onClick=\"Guardar('$Ano-$MesNum-$Dia','".$Ano."_".$MesNum."_".$Dia."')\" >";
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Sunday"){
						$Res.= $Dia;
					}
					//LUNES
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Monday"){
						$Res.= $Dia;
					}
					//Martes
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Tuesday"){
						$Res.= $Dia;
					}
					//Miercoles
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Wednesday"){
						$Res.= $Dia;
					}
					//Jueves
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Thursday"){
						$Res.= $Dia;
					}
					//Viernes
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Friday"){
						$Res.= $Dia;
					}
					//Sabado
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Saturday"){
						$Res.= $Dia;
					}
					$Res.= "</td>\n";
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Saturday"){
						$Res.= "</tr>\n";
					}
					
				}
				$Dia--;
				while(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))!="Saturday"){
					$Res.="<td>&nbsp;</td>\n";
					$Dia++;
					if(date("l",mktime(0,0,0,$MesNum,$Dia,$Ano))=="Saturday"){
						$Res.= "</tr>\n";
					}
				}
				$Res.= "</table>\n";
			$Res.= "</td>\n";
		}
		$Res.= "</tr>\n";
	}
	$Res.= "</table>\n";
	echo $Res;
?>
