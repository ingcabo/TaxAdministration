<?
function muestrafecha($fecha){
	$fecha = (empty($fecha)) ? date("Y-m-d") : $fecha;
	$x = date("d/m/Y", strtotime($fecha));
	return $x;
}
function guardafecha($fecha){
	$ano = substr ($fecha, 6, 4);
	$mes = substr ($fecha, 3, 2);
	$dia = substr ($fecha, 0, 2);
	$fecha="$ano-$mes-$dia";

	if($fecha=='--'){ $fecha=''; }
	
	return $fecha;
	
}

function muestrafloat($monto){//se7ho
	return number_format($monto, 2, ',', '.');
}

//Para mostrar las retenciones que tienen mas de 3 decimales
function muestrafloat3($monto){//se7ho
	return number_format($monto, 3, ',', '.');
}

function guardafloat($monto){//se7ho
	return str_replace(',','.',str_replace('.','',$monto));
}

function getLastId($conn, $id, $tabla){
	$q = "SELECT max($id) AS id FROM $tabla ";
	if($r = $conn->execute($q))
		return $r->fields[$id];
	else
		return false;
}
// 23.10.06.CEPV.SN
function getCorrelativo($conn, $campo, $tabla, $id){
	$q = "SELECT $campo FROM $tabla ORDER BY $id desc";
	if($r = $conn->execute($q)){
		$Codigo=$r->fields[$campo];
		$Digitos=strlen($r->fields[$campo]);
		do{
			$Codigo=$Codigo+1;
			$Digitos2=strlen($Codigo);
			while($Digitos>$Digitos2){
				$Codigo="0".$Codigo;
				$Digitos2=strlen($Codigo);
			}
			$r = $conn->execute("SELECT $campo FROM $tabla WHERE $campo='$Codigo'");
		}while(!$r->EOF);
		return $Codigo;
	}
	else
		return false;
}
function DiaFin($Mes){
	switch ($Mes)
	{ 
		Case 01:
			$DiaFin = "31";
			break;
		Case 02 :
			$DiaFin = "28";
			break;
		Case 03:
			$DiaFin = "31";
			break;
		Case 04:
			$DiaFin = "30";
			break;
		Case 05:
			$DiaFin = "31";
			break;
		Case 06:
			$DiaFin = "30";
			break;
		Case 07:
			$DiaFin = "31";
			break;
		Case "08":
			$DiaFin = "31";
			break;
		Case "09":
			$DiaFin = "30";
			break;
		Case 10:
			$DiaFin = "31";
			break;
		Case 11:
			$DiaFin = "30";
			break;
		Case 12:
			$DiaFin = "31";
			break;
	}
	return($DiaFin);
}
function Formula($conn,$CFormula,$Trabajador,$Contrato,$FechaIni,$FechaFin,$FechaIngresoTrabajador,$FechaEgresoTrabajador){
	//BUSCO LOS CODIGOS DE LAS VARIABLES EN LA FORMULA DEL CONCEPTO
	$IndexVar=0;
	for($i=0;$i<strlen($CFormula);$i++){
		$CharActual=substr($CFormula,$i,1);
		if($CharActual=="["){
			$Variable="";
			while($CharActual!="]"){
				$Variable.=$CharActual;
				$i++;
				$CharActual=substr($CFormula,$i,1);
			}
			$Variable.=$CharActual;
			$IndexVar++;
			$Variables[$IndexVar]=$Variable;
		}
	}
	//BUSCO LOS VALORES DE LAS VARIABLES EN DB.
	for($i=1;$i<=$IndexVar;$i++){
		$TipoVar=substr($Variables[$i],1,4);
		$Codigo=split("_",$Variables[$i]);
		$Codigo=split(":",$Codigo[0]);
		$Valor[$i]= BuscarValor($conn,$TipoVar,$Codigo[1],$Trabajador,$Contrato,$FechaIni,$FechaFin,$FechaIngresoTrabajador,$FechaEgresoTrabajador);
		
	}
	for($i=1;$i<=$IndexVar;$i++){
		$CFormula= str_replace($Variables[$i],$Valor[$i],$CFormula);
	}
	return $CFormula;
}
function BuscarValor($conn,$TipoVar,$Codigo,$Trabajador,$Contrato,$FechaIni,$FechaFin,$FechaIngresoTrabajador,$FechaEgresoTrabajador){
	switch($TipoVar){
		case "Vari": { //VARIABLES
			$q = "SELECT var_tra_val AS valor FROM rrhh.var_tra WHERE var_cod=$Codigo AND tra_cod=$Trabajador";
			$rAux = $conn->Execute($q);
			return (!$rAux->EOF ? $rAux->fields['valor'] : 0); 
			//ESTE CODIGO LO UTILICE PARA GENERAR LOS ACUMULADOS EN BASE A HISTORICOS SN
/*			$MES=split("/",$FechaIni);
			$MES=$MES[1];
			$q = "SELECT sum(B.var_tra_val) as valor FROM rrhh.historial_nom AS A INNER JOIN rrhh.hist_nom_var_tra AS B On A.int_cod=B.hnom_cod WHERE to_char(A.nom_fec_fin,'MM') = '$MES' AND B.tra_cod=$Trabajador AND B.var_cod=$Codigo";
			$rAux = $conn->Execute($q);
			if(!$rAux->EOF){
				if(!(empty($rAux->fields['valor']) || $rAux->fields['valor']==0)){
					return $rAux->fields['valor']; 
				}else{
					$q = "SELECT var_tra_val AS valor FROM rrhh.var_tra WHERE var_cod=$Codigo AND tra_cod=$Trabajador";
					$rAux = $conn->Execute($q);
					return (!$rAux->EOF ? $rAux->fields['valor'] : 0); 
				}
				
			}else{
				$q = "SELECT var_tra_val AS valor FROM rrhh.var_tra WHERE var_cod=$Codigo AND tra_cod=$Trabajador";
				$rAux = $conn->Execute($q);
				return (!$rAux->EOF ? $rAux->fields['valor'] : 0); 
			} */
			//ESTE CODIGO LO UTILICE PARA GENERAR LOS ACUMULADOS EN BASE A HISTORICOS EN
				
		}
		case "Cons": { //CONSTANTES
			$q = "SELECT cons_val AS valor FROM rrhh.constante WHERE int_cod=$Codigo";
			$rAux = $conn->Execute($q);
			return (!$rAux->EOF ? $rAux->fields['valor'] : 0);
		}
		case "Conc": { //CONCEPTOS ACUMULADOS
			$q = "SELECT (SUM(conc_val) - (CASE WHEN (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE A.tra_cod=$Trabajador AND B.conc_cod=$Codigo) IS NULL THEN 0 ELSE (SELECT sum(B.conc_val) AS conc_val FROM rrhh.pago_acumulado AS A INNER JOIN rrhh.pago_acumulado_conc AS B ON A.int_cod=B.pago_acum_cod WHERE A.tra_cod=$Trabajador AND B.conc_cod=$Codigo) END)) AS total FROM rrhh.acum_tra_conc WHERE tra_cod=$Trabajador AND conc_cod=$Codigo";
			$rAux = $conn->Execute($q);
			if(!$rAux->EOF){
				return (!empty($rAux->fields['total']) ? $rAux->fields['total'] : 0);
			}else{
				return 0;
			}
		}
		case "Gvar": { //VARIABLES GLOBALES
			switch($Codigo){
				case 1: { //CALCULO DE LA VARIABLE GLOBAL: SUELDO MENSUAL
					$q = "SELECT tra_sueldo FROM rrhh.trabajador WHERE int_cod=$Trabajador";
					$rAux = $conn->Execute($q); 
					return (!empty($rAux->fields['tra_sueldo']) ? $rAux->fields['tra_sueldo'] : 0); 
					//ESTE CODIGO LO UTILICE PARA GENERAR LOS ACUMULADOS EN BASE A HISTORICOS SN
					
/*					$MES=split("/",$FechaIni);
					$MES=$MES[1];
					$q = "SELECT B.tra_sueldo FROM rrhh.historial_nom AS A INNER JOIN rrhh.hist_nom_tra_sueldo AS B On A.int_cod=B.hnom_cod WHERE to_char(A.nom_fec_fin,'MM') = '$MES' AND B.tra_cod=$Trabajador";
					$rAux = $conn->Execute($q); 
					$i=0;
					$Sueldo=0;
					while(!$rAux->EOF){
						$i++;
						$Sueldo+=(!empty($rAux->fields['tra_sueldo']) ? $rAux->fields['tra_sueldo'] : 0);
						$rAux->movenext();
					}
					
					return $i==0? 0 : $Sueldo/$i; */
					//ESTE CODIGO LO UTILICE PARA GENERAR LOS ACUMULADOS EN BASE A HISTORICOS EN
					
				}
				case 2: { //CALCULO DE LA VARIABLE GLOBAL: TIPO DE CONTRATO
					$q = "SELECT cont_tipo FROM rrhh.contrato WHERE int_cod=$Contrato";
					$rAux = $conn->Execute($q);
					return  ($rAux->fields['cont_tipo']);
				}
				case 3: { //CALCULO DE LA VARIABLE GLOBAL: DIAS NO CONTABILIZABLES POR INGRESO Y/O EGRESO
					$Fecha=split("/",$FechaIni,20);
					$FechaNomIni=$Fecha[2]."-".$Fecha[1]."-".$Fecha[0];
					$Fecha=split("/",$FechaFin,20);
					$FechaNomFin=$Fecha[2]."-".$Fecha[1]."-".$Fecha[0]; 
					$segundos  = strtotime($FechaIngresoTrabajador)-strtotime($FechaNomIni);
					$diasI= intval($segundos/86400);
					$segundos  = strtotime($FechaNomFin)-strtotime($FechaIngresoTrabajador);
					$diasF= intval($segundos/86400);
					if($diasI>=0 && $diasF>=0){
						$ValorI=$diasI; 
					}else{
						$ValorI=0; 
					}
					$segundos  = strtotime($FechaEgresoTrabajador)-strtotime($FechaNomIni);
					$diasI= intval($segundos/86400);
					$segundos  = strtotime($FechaNomFin)-strtotime($FechaEgresoTrabajador);
					$diasF= intval($segundos/86400);
					if($diasI>=0 && $diasF>=0){
						$ValorF=$diasF ; 
					}else{
						$ValorF=0; 
					}
					return ($ValorI + $ValorF);
				}
				case 4: { //CALCULO DE LA VARIABLE GLOBAL: NUMERO DE LUNES EN EL PERIODO DE LA NOMINA
					$NroLunes=0;
					$diasI= intval((strtotime($FechaIngresoTrabajador)-strtotime(guardafecha($FechaIni)))/86400);
					$diasF= intval((strtotime(guardafecha($FechaFin))-strtotime($FechaIngresoTrabajador))/86400);
					if($diasI>=0 && $diasF>=0){
						$FechaI=$FechaIngresoTrabajador;
					}else{
						$FechaI=guardafecha($FechaIni);
					}
					$diasI= intval(strtotime($FechaEgresoTrabajador)-strtotime(guardafecha($FechaIni))/86400);
					$diasF= intval(strtotime(guardafecha($FechaFin))-strtotime($FechaEgresoTrabajador)/86400);
					if($diasI>=0 && $diasF>=0){
						$FechaF=$FechaEgresoTrabajador;
					}else{
						$FechaF=guardafecha($FechaFin);
					}
					$Dias= intval((strtotime($FechaF)-strtotime($FechaI))/86400);
					$Dia=date("d",strtotime($FechaI));
					$Mes=date("m",strtotime($FechaI));
					$Ano=date("Y",strtotime($FechaI));
					for($i=0;$i<=$Dias;$i++){
						if(date("l",mktime(0,0,0,$Mes,$Dia,$Ano))=="Monday"){
							$NroLunes++;
						}
						$Dia++;
						if($Dia>DiaFin($Mes)){
							$Dia=1;
							$Mes++;
							if($Mes>12){
								$Mes=1;
								$Ano++;
							}
						}
					}
					return $NroLunes;
				}
				case 5: { //CALCULO DE LA VARIABLE GLOBAL: NUMERO DE LUNES EN El MES (Tomando Fecha Inicio de Nomina)
					$NroLunes=0;
					$Fecha=split("/",$FechaIni,20);
					$Dia=1;
					$Mes=$Fecha[1];
					$Ano=$Fecha[2];
					while($Dia<=DiaFin($Mes)){
						if(date("l",mktime(0,0,0,$Mes,$Dia,$Ano))=="Monday"){
							$NroLunes++;
						}
						$Dia++;
					}
					return $NroLunes;
				}
				case 6: { //CALCULO DE LA VARIABLE GLOBAL: PRESTAMO
					$q = "SELECT B.cuota_monto,B.int_cod FROM rrhh.prestamo AS A INNER JOIN rrhh.prestamo_cuotas AS B ON A.int_cod=B.pres_cod WHERE A.cont_cod=$Contrato AND A.tra_cod=$Trabajador AND A.pres_estatus=1 AND B.cuota_nom_fec_ini='$FechaIni' AND B.cuota_nom_fec_fin='$FechaFin' AND B.cuota_estatus='Por Cobrar'";
					$r = $conn->Execute($q);
					if(!$r->EOF){
						$Cuota=$r->fields['int_cod'];
						$q = "UPDATE rrhh.prestamo_cuotas SET cuota_estatus='Cobrando' WHERE int_cod=$Cuota";
						$rAux = $conn->Execute($q);
					}
					return (!$r->EOF ? $r->fields['cuota_monto'] : 0);
				}
				case 7: { //CALCULO DE LA VARIABLE GLOBAL: NRO DE CUOTAS DE PRESTAMO
					$q = "SELECT B.cuota_nro FROM rrhh.prestamo AS A INNER JOIN rrhh.prestamo_cuotas AS B ON A.int_cod=B.pres_cod WHERE A.cont_cod=$Contrato AND A.tra_cod=$Trabajador AND A.pres_estatus=1 AND B.cuota_nom_fec_ini='$FechaIni' AND B.cuota_nom_fec_fin='$FechaFin' AND B.cuota_estatus!='Cancelado'";
					$rAux = $conn->Execute($q);
					return (!$rAux->EOF ? $rAux->fields['cuota_nro'] : 0);
				}
				case 8: { //CALCULO DE LA VARIABLE GLOBAL: TOTAL DE CUOTAS DE PRESTAMO
					$q = "SELECT pres_cuotas FROM rrhh.prestamo WHERE cont_cod=$Contrato AND tra_cod=$Trabajador AND pres_estatus=1 ";
					$rAux = $conn->Execute($q);
					return (!$rAux->EOF ? $rAux->fields['pres_cuotas'] : 0);
				}
				case 9: { //CALCULO DE LA VARIABLE GLOBAL: MESES DE ANTIGUEDAD
					return (strtotime(guardafecha($FechaFin)) - strtotime($FechaIngresoTrabajador))/2592000;
				}
				case 10: { //CALCULO DE LA VARIABLE GLOBAL: AÑOS DE ANTIGUEDAD
					return (strtotime(guardafecha($FechaFin)) - strtotime($FechaIngresoTrabajador))/31536000;
				}
				case 11: { //CALCULO DE LA VARIABLE GLOBAL: AÑOS DE ANTIGUEDAD PARA VACACIONES
					return (int)((strtotime(guardafecha($FechaIni)) - strtotime($FechaIngresoTrabajador))/31536000);
				}
				case 12: { //CALCULO DE LA VARIABLE GLOBAL: NRO DE DIAS FERIADOS CONTANDO SABADOS Y DOMINGOS EN EL PERIODO DE LA NOMINA
					$Dias= intval((strtotime(guardafecha($FechaFin))-strtotime(guardafecha($FechaIni)))/86400);
					$Dia=date("d",strtotime(guardafecha($FechaIni)));
					$Mes=date("m",strtotime(guardafecha($FechaIni)));
					$Ano=date("Y",strtotime(guardafecha($FechaIni)));
					$DiasTotal=0;
					for($i=0;$i<=$Dias;$i++){
						$DiaI= strlen($Dia)<2 ? "0".$Dia : $Dia;
						$MesI= strlen($Mes)<2 ? "0".$Mes : $Mes;
						$Fecha=$Ano."-".$MesI."-".$DiaI;
						$q = "SELECT * FROM rrhh.feriados WHERE fecha='$Fecha'";
						$rF = $conn->Execute($q);
						if(!$rF->EOF){
							$DiasTotal++;
						}
						$Dia++;
						if($Dia>DiaFin($Mes)){
							$Dia=1;
							$Mes++;
							if($Mes>12){
								$Mes=1;
								$Ano++;
							}
						}
					}
					return $DiasTotal;
				}
				case 13: { //CALCULO DE LA VARIABLE GLOBAL: NUMERO DE SABADOS Y DOMINGOS EN EL PERIODO DE LA NOMINA
					$NroSyD=0;
					$diasI= intval((strtotime($FechaIngresoTrabajador)-strtotime(guardafecha($FechaIni)))/86400);
					$diasF= intval((strtotime(guardafecha($FechaFin))-strtotime($FechaIngresoTrabajador))/86400);
					if($diasI>=0 && $diasF>=0){
						$FechaI=$FechaIngresoTrabajador;
					}else{
						$FechaI=guardafecha($FechaIni);
					}
					$diasI= intval(strtotime($FechaEgresoTrabajador)-strtotime(guardafecha($FechaIni))/86400);
					$diasF= intval(strtotime(guardafecha($FechaFin))-strtotime($FechaEgresoTrabajador)/86400);
					if($diasI>=0 && $diasF>=0){
						$FechaF=$FechaEgresoTrabajador;
					}else{
						$FechaF=guardafecha($FechaFin);
					}
					$Dias= intval((strtotime($FechaF)-strtotime($FechaI))/86400);
					$Dia=date("d",strtotime($FechaI));
					$Mes=date("m",strtotime($FechaI));
					$Ano=date("Y",strtotime($FechaI));
					for($i=0;$i<=$Dias;$i++){
						if(date("l",mktime(0,0,0,$Mes,$Dia,$Ano))=="Saturday" || date("l",mktime(0,0,0,$Mes,$Dia,$Ano))=="Sunday"){
							$NroSyD++;
						}
						$Dia++;
						if($Dia>DiaFin($Mes)){
							$Dia=1;
							$Mes++;
							if($Mes>12){
								$Mes=1;
								$Ano++;
							}
						}
					}
					return $NroSyD;
				}
				case 14: { //CALCULO DE LA VARIABLE GLOBAL: NUMERO DE DIAS EN EL PERIODO DE LA NOMINA
					return	(intval((strtotime(guardafecha($FechaFin))-strtotime(guardafecha($FechaIni)))/86400))+1;
				}
				case 15: { //SUELDO ANTERIOR AL ACTUAL
					$q = "SELECT tra_sueldo FROM rrhh.hist_nom_tra_sueldo WHERE tra_cod=$Trabajador ORDER BY int_cod DESC";
					$r = $conn->Execute($q);
					return	!$r->EOF ? $r->fields['tra_sueldo'] : 0;
				}
			}
		}
	}
}
function FechaIni($Contrato,$Operador,$Fecha,$FormatoEnt,$FormatoSal){
	$Fecha=split ($Operador ,$Fecha);
	if($FormatoEnt==1){
		$DiaIndex=0;
		$MesIndex=1;
		$AnoIndex=2;
	}
	if($FormatoEnt==2){
		$DiaIndex=2;
		$MesIndex=1;
		$AnoIndex=0;
	}
	$UltimoDiaMes=DiaFin($Fecha[$MesIndex]);
	switch(true){
		case ($Contrato=="0" OR $Contrato=="3"):{
			$Dia=$Fecha[$DiaIndex]+1;
			if($Dia>$UltimoDiaMes){
				$Dia=$Dia-$UltimoDiaMes;
				$Mes=$Fecha[$MesIndex]+1;
				if($Mes<10 && strlen($Mes)<2){
					$Mes="0".$Mes;
				}
				if($Mes>12){
					$Mes='01';
					$Ano=$Fecha[$AnoIndex]+1;
				}else{
					$Ano=$Fecha[$AnoIndex];
				}
			}else{
				$Mes=$Fecha[$MesIndex];
				$Ano=$Fecha[$AnoIndex];
			}
			if($Dia<10 && strlen($Dia)<2){
				$Dia="0".$Dia;
			}
			return $Dia."/".$Mes."/".$Ano;
		}
		case $Contrato=="1":{
			if($Fecha[$DiaIndex]=='15'){
				return "16/".$Fecha[$MesIndex]."/".$Fecha[$AnoIndex];
			}
			if($Fecha[$DiaIndex]==$UltimoDiaMes){
				$Mes=$Fecha[$MesIndex]+1;
				if($Mes>12 ){
					$Mes='01';
					$Ano=$Fecha[$AnoIndex]+1;
				}else{
					$Ano=$Fecha[$AnoIndex];
				}
				if($Mes<10 && strlen($Mes)<2){
					$Mes="0".$Mes;
				}
				return "01/".$Mes."/".$Ano;
			}
		}
		case $Contrato=="2":{
			$Mes=$Fecha[$MesIndex]+1;
			if($Mes>12 ){
				$Mes='01';
				$Ano=$Fecha[$AnoIndex]+1;
			}else{
				$Ano=$Fecha[$AnoIndex];
			}
			if($Mes<10 && strlen($Mes)<2){
				$Mes="0".$Mes;
			}
			return "01/".$Mes."/".$Ano;
		}
	}
}
function FechaFin($Contrato,$Operador,$Fecha,$FormatoEnt,$FormatoSal,$conn = -1,$ContratoC=-1){
	$Fecha=split($Operador ,$Fecha);
	if($FormatoEnt==1){
		$DiaIndex=0;
		$MesIndex=1;
		$AnoIndex=2;
	}
	if($FormatoEnt==2){
		$DiaIndex=2;
		$MesIndex=1;
		$AnoIndex=0;
	}
	$UltimoDiaMes=DiaFin($Fecha[$MesIndex]);
	switch($Contrato){
		case "0":{
			$Dia= $Fecha[$DiaIndex]+6;
			if($Dia>$UltimoDiaMes){
				$Dia=$Dia-$UltimoDiaMes;
				$Mes=$Fecha[$MesIndex]+1;
				if($Mes<10 && strlen($Mes)<2){
					$Mes="0".$Mes;
				}
				if($Mes>12){
					$Mes='01';
					$Ano=$Fecha[$AnoIndex]+1;
				}else{
					$Ano=$Fecha[$AnoIndex];
				}
			}else{
				$Mes=$Fecha[$MesIndex];
				$Ano=$Fecha[$AnoIndex];
			}
			if($Dia<10 && strlen($Dia)<2){
				$Dia="0".$Dia;
			}
			return $Dia."/".$Mes."/".$Ano;
		}
		case "1":{
			if($Dia<10 && strlen($Dia)<2){
				$Dia="0".$Dia;
			}
			if($Fecha[$DiaIndex]=='01'){
				return "15/".$Fecha[$MesIndex]."/".$Fecha[$AnoIndex];
			}
			if($Fecha[$DiaIndex]=='16'){
				return $UltimoDiaMes."/".$Fecha[$MesIndex]."/".$Fecha[$AnoIndex];
			}
		}
		case "2":{
			return $UltimoDiaMes."/".$Fecha[$MesIndex]."/".$Fecha[$AnoIndex];
			break;
		}
		case "3":{
			$q = "SELECT * FROM rrhh.nomina WHERE cont_cod=$ContratoC";
			$r = $conn->Execute($q);
			return !$r->EOF ? muestrafecha($r->fields['nom_fec_fin']) : date("d/m/Y");
		}
	}
}
function calculafloat($monto,$decimales){//se7ho
	return number_format($monto, $decimales, '.', '');
}
function Cadena($Cadena){
	$Vector=split(" ",$Cadena,500);
	if($Vector[1]){
		$Palabra=$Vector[1];
		$Palabra=$Palabra[0].".";
	}
	return $Vector[0]." ".$Palabra; 
}
function FechaFinVacaciones($conn,$FechaIni,$DiasF){
	$DiaFin=date("d",strtotime(guardafecha($FechaIni)));
	$MesFin=date("m",strtotime(guardafecha($FechaIni)));
	$AnoFin=date("Y",strtotime(guardafecha($FechaIni)));
	for($i=$DiasF;$i>0;$i--){
		$Bandera=false;
		while(!$Bandera){
			$DiaFin++;
			if($DiaFin>DiaFin($MesFin)){
				$DiaFin=1;
				$MesFin++;
				if($MesFin>12){
					$MesFin=1;
					$AnoFin++;
				}
			}
			$DiaFin=strlen($DiaFin)<2 ? "0".$DiaFin : $DiaFin;
			$MesFin=strlen($MesFin)<2 ? "0".$MesFin : $MesFin;
			$Fecha=$AnoFin."-".$MesFin."-".$DiaFin;
			$q = "SELECT * FROM rrhh.feriados WHERE fecha='$Fecha'";
			$rF = $conn->Execute($q);
			$Bandera= $rF->EOF ;
		}
	}
	$DiaFin=strlen($DiaFin)<2 ? "0".$DiaFin : $DiaFin;
	$MesFin=strlen($MesFin)<2 ? "0".$MesFin : $MesFin;
	return $DiaFin."/".$MesFin."/".$AnoFin;
}

#FUNCION PAPA EL CAMBIO DE NUMERO A LETRAS#
#cMx#
function num2letras($num, $fem = false, $dec = true) {
	$numAux=$num;
//if (strlen($num) > 14) die("El n?mero introducido es demasiado grande");
   $matuni[2]  = "dos";
   $matuni[3]  = "tres";
   $matuni[4]  = "cuatro";
   $matuni[5]  = "cinco";
   $matuni[6]  = "seis";
   $matuni[7]  = "siete";
   $matuni[8]  = "ocho";
   $matuni[9]  = "nueve";
   $matuni[10] = "diez";
   $matuni[11] = "once";
   $matuni[12] = "doce";
   $matuni[13] = "trece";
   $matuni[14] = "catorce";
   $matuni[15] = "quince";
   $matuni[16] = "dieciseis";
   $matuni[17] = "diecisiete";
   $matuni[18] = "dieciocho";
   $matuni[19] = "diecinueve";
   $matuni[20] = "veinte";
   $matunisub[2] = "dos";
   $matunisub[3] = "tres";
   $matunisub[4] = "cuatro";
   $matunisub[5] = "quin";
   $matunisub[6] = "seis";
   $matunisub[7] = "sete";
   $matunisub[8] = "ocho";
   $matunisub[9] = "nove";

   $matdec[1] = "diez";
   $matdec[2] = "veinte";
   $matdec[3] = "treinta";
   $matdec[4] = "cuarenta";
   $matdec[5] = "cincuenta";
   $matdec[6] = "sesenta";
   $matdec[7] = "setenta";
   $matdec[8] = "ochenta";
   $matdec[9] = "noventa";
   $matsub[3]  = 'mill';
   $matsub[5]  = 'bill';
   $matsub[7]  = 'mill';
   $matsub[9]  = 'trill';
   $matsub[11] = 'mill';
   $matsub[13] = 'bill';
   $matsub[15] = 'mill';
   $matmil[4]  = 'millones';
   $matmil[6]  = 'billones';
   $matmil[7]  = 'de billones';
   $matmil[8]  = 'millones de billones';
   $matmil[10] = 'trillones';
   $matmil[11] = 'de trillones';
   $matmil[12] = 'millones de trillones';
   $matmil[13] = 'de trillones';
   $matmil[14] = 'billones de trillones';
   $matmil[15] = 'de billones de trillones';
   $matmil[16] = 'millones de billones de trillones';

   $num = trim((string)@$num);
   if ($num[0] == '-') {
      $neg = 'menos ';
      $num = substr($num, 1);
   }else
      $neg = '';
   while ($num[0] == '0') $num = substr($num, 1);
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
   $zeros = true;
   $punt = false;
   $ent = '';
   $fra = '';
   for ($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if (! (strpos(".,'''", $n) === false)) {
         if ($punt) break;
         else{
            $punt = true;
            continue;
         }

      }elseif (! (strpos('0123456789', $n) === false)) {
         if ($punt) {
            if ($n != '0') $zeros = false;
            $fra .= $n;
         }else

            $ent .= $n;
      }else

         break;

   }
   $ent = '     ' . $ent;
   if ($dec and $fra and ! $zeros) {
      $fin = ' con';
      $flag = 0;
	  for ($n = 0; $n < strlen($fra); $n++) {
		if((((strlen($fra)<2) || ($fra[1]=='0')) && $flag == 0)){
			$fin .= ' ' . $matdec[$fra[0]];
			$flag=1;
		} else {
			if($flag==0){
				 if (($s = $fra[$n]) == '0')
					$fin .= ' cero';
				 elseif ($s == '1'){
					if($fra[$n+1]!=''){
						$fin .= ' ' . $matuni[$fra];
						$flag=1;
					}else	
						$fin .= $fem ? ' una' : ' un';
				 }else{
					if ($n==0 && $fra[$n+1]!=''){
						$fin .= ' ' . $matdec[$s].' y ';
					}else{
						$fin .= ' ' . $matuni[$s];
					}
				}
			}
		 }
      }
	  $fin .= ' centimos';
   }else
      $fin = '';
   if ((int)$ent === 0) return 'Cero ' . $fin;
   $tex = '';
   $sub = 0;
   $mils = 0;
   $neutro = false;
   while ( ($num = substr($ent, -3)) != '   ') {
      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = 'una';
         $subcent = 'as';
      }else{
         $matuni[1] = $neutro ? 'un' : 'uno';
         $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {
      }elseif ($n2 < 21)
         $t = ' ' . $matuni[(int)$n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0) $t = 'i' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }else{
         $n3 = $num[2];
         if ($n3 != 0) $t = ' y ' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
	  if ($n == 1) {
	  	if($numAux==100000 || $numAux==100){
	    	$t = ' cien' . $t;
		}else{
			$t = ' ciento' . $t;
		}
      }elseif ($n == 5){
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      }elseif ($n != 0){
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if ($sub == 1) {

      }elseif (! isset($matsub[$sub])) {
         if ($num == 1) {
            $t = ' mil';
         }elseif ($num > 1){
            $t .= ' mil';
         }
      }elseif ($num == 1) {
         $t .= ' ' . $matsub[$sub] . '?n';
      }elseif ($num > 1){
         $t .= ' ' . $matsub[$sub] . 'ones';
      }   
      if ($num == '000') $mils ++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;
   return ucfirst($tex);
}

function obtieneMes($nmes){
	switch ($nmes) {
		case "1" : $mes = "Enero"; break;
		case "2" : $mes = "Febrero"; break;
		case "3" : $mes = "Marzo"; break;
		case "4" : $mes = "Abril"; break;
		case "5" : $mes = "Mayo"; break;
		case "6" : $mes = "Junio"; break;
		case "7" : $mes = "Julio"; break;
		case "8" : $mes = "Agosto"; break;
		case "9" : $mes = "Septiembre"; break;
		case "10" : $mes = "Octubre"; break;
		case "11" : $mes = "Noviembre"; break;
		case "12" : $mes = "Diciembre"; break;
	}
	return $mes;
}

function DiaSemana($fecha, $texto = 1){ 

	list($ano,$mes,$dia) = explode("-",$fecha);
	$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
   
	if ($texto == 0)
		return $numerodiasemana;
  
	switch($numerodiasemana){
		case 0: return "Domingo";
		case 1: return "Lunes";
		case 2: return "Martes";
		case 3: return "Miércoles";
		case 4: return "Jueves";
		case 5: return "Viernes";
		case 6: return "Sábado";
	}
} 
// IODG 30/11/2006
function getCorrelativo2($conn, $campo, $tabla, $id){
	$q = "SELECT $campo FROM $tabla ORDER BY $id desc";
	if($r = $conn->execute($q)){
		$Codigo=$r->fields[$campo];
		$aux = explode("-",$Codigo);
		if(!empty($aux[1]))
			$Codigo = $aux[1];
		else
			$Codigo = $aux[0];
		$Digitos=strlen($Codigo);
		do{
			$Codigo=$Codigo+1;
			$Digitos2=strlen($Codigo);
			while($Digitos>$Digitos2){
				$Codigo="0".$Codigo;
				$Digitos2=strlen($Codigo);
			}
			$r = $conn->execute("SELECT $campo FROM $tabla WHERE $campo='$Codigo'");
		}while(!$r->EOF);
		if(!empty($aux[1]))
			$Codigo = $aux[0]."-".$Codigo;
		return $Codigo;
	}
	else
		return false;
}
#IODG 14/12/06
	function buscaNroDoc($conn, $nrodoc, $tipdoc, $id_ue, $tabla){
		$aux = $tipdoc."-".$id_ue."-".$nrodoc;
		$q= "SELECT id from $tabla WHERE nrodoc = '$aux'";
		//die($q);
		$r->$conn->Execute($q);
		return $r->fields['id'];
	}

// 06.03.07.CEPV.SN
	function get_all_listado($conn,$objeto,$condicones='',$orden='int_cod'){
		try {
			if(!is_array($condicones) || empty($condicones) ){
				$q = "SELECT int_cod FROM rrhh.".$objeto;
			}else{
				$q = "SELECT int_cod FROM rrhh.".$objeto." WHERE 1=1 ";
				for($i=0;$i<count($condicones);$i++){
					if($condicones[$i][0]=='cont_cod' && $objeto=='trabajador' ){
						$q.= "AND int_cod IN (SELECT tra_cod FROM rrhh.cont_tra WHERE cont_cod= ? ) " ;
					}elseif($condicones[$i][1]!='IN'){
						$q.= "AND ".$condicones[$i][0]." ".$condicones[$i][1]." ? " ;
					}else{
						$q.= "AND ".$condicones[$i][0]." ".$condicones[$i][1]." (" ;
						$arrayIN= split(",",$condicones[$i][2]);
						for($j=0;$j<count($arrayIN);$j++){
							$q.= ($j+1!=count($arrayIN)) ? "?," : "?) ";
						}
					}
				}
			}
			if(is_array($orden) && !empty($orden)){
				$q.= " ORDER BY ".implode(",",$orden);
			}
			$rPrep_Sel = $conn->Prepare($q);
			for($i=0;$i<count($condicones);$i++){
				if($condicones[$i][1]!='IN'){
					$array[]= $condicones[$i][1]=='ILIKE' ? "%".$condicones[$i][2]."%" : $condicones[$i][2]; 
				}else{
					$arrayIN= split(",",$condicones[$i][2]);
					for($j=0;$j<count($arrayIN);$j++){
						$array[]=$arrayIN[$j];
					}
				}
			}
			//var_dump($array);
			$r = $conn->Execute($rPrep_Sel,$array);
			$collection=array();
			while(!$r->EOF){
				$ue = new $objeto;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			return $coleccion;
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
		}
			
	}
// 06.03.07.CEPV.EN
?>
