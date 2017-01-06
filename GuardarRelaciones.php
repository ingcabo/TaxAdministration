<?
include('adodb/adodb-exceptions.inc.php'); 
require ("comun/ini.php");
if(isset($_POST['JsonEnv'])){
	$JsonRec = new Services_JSON();
	$JsonRec=$JsonRec->decode(str_replace("\\","",$_POST['JsonEnv']));
	//die(var_dump($JsonRec));
	try {
		switch($JsonRec->Forma){
			case 0:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.grupoconceptos WHERE int_cod NOT IN (SELECT gconc_cod FROM rrhh.cont_gconc WHERE cont_cod=$JsonRec->Contrato)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
						
							$q= "INSERT INTO rrhh.cont_gconc (cont_cod,gconc_cod) VALUES ($JsonRec->Contrato,".$r->fields['int_cod'].")";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->GConceptos AS $GConceptoAux) {
							$q= "INSERT INTO rrhh.cont_gconc (cont_cod,gconc_cod) VALUES ($JsonRec->Contrato,$GConceptoAux)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->GConceptos AS $GConceptoAux) {
							$q= "DELETE FROM rrhh.cont_gconc WHERE cont_cod=$JsonRec->Contrato AND gconc_cod=$GConceptoAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM rrhh.cont_gconc WHERE cont_cod=$JsonRec->Contrato";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
			case 1:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.concepto WHERE int_cod NOT IN (SELECT conc_cod FROM rrhh.gconc_conc WHERE gconc_cod=$JsonRec->GConceptos)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
							$q= "INSERT INTO rrhh.gconc_conc (gconc_cod,conc_cod) VALUES ($JsonRec->GConceptos,".$r->fields['int_cod'].")";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->Conceptos AS $ConceptoAux) {
							$q= "INSERT INTO rrhh.gconc_conc (gconc_cod,conc_cod) VALUES ($JsonRec->GConceptos,$ConceptoAux)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->Conceptos AS $ConceptoAux) {
							$q= "DELETE FROM rrhh.gconc_conc WHERE gconc_cod=$JsonRec->GConceptos AND conc_cod=$ConceptoAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM rrhh.gconc_conc WHERE gconc_cod=$JsonRec->GConceptos";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
			case 2:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.trabajador WHERE int_cod NOT IN (SELECT tra_cod FROM rrhh.cont_tra WHERE cont_cod=$JsonRec->Contrato)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
							$q= "INSERT INTO rrhh.cont_tra (cont_cod,tra_cod) VALUES ($JsonRec->Contrato,".$r->fields['int_cod'].")";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->Trabajadores AS $TrabajadorAux) {
							$q= "INSERT INTO rrhh.cont_tra (cont_cod,tra_cod) VALUES ($JsonRec->Contrato,$TrabajadorAux)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->Trabajadores AS $TrabajadorAux) {
							$q= "DELETE FROM rrhh.cont_tra WHERE cont_cod=$JsonRec->Contrato AND tra_cod=$TrabajadorAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM rrhh.cont_tra WHERE cont_cod=$JsonRec->Contrato";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
			case 3:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.empresa WHERE int_cod NOT IN (SELECT emp_cod FROM emp_usu WHERE usu_cod=$JsonRec->Usuario)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
							$q= "INSERT INTO emp_usu (usu_cod,emp_cod) VALUES ($JsonRec->Usuario,".$r->fields['int_cod'].")";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->Empresas AS $EmpresaAux) {
							$q= "INSERT INTO emp_usu (usu_cod,emp_cod) VALUES ($JsonRec->Usuario,$EmpresaAux)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->Empresas AS $EmpresaAux) {
							$q= "DELETE FROM emp_usu WHERE usu_cod=$JsonRec->Usuario AND emp_cod=$EmpresaAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM emp_usu WHERE usu_cod=$JsonRec->Usuario";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
			case 4:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.concepto WHERE int_cod NOT IN (SELECT conc_cod FROM rrhh.tra_conc WHERE tra_cod=$JsonRec->Trabajador)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
							$q= "INSERT INTO rrhh.tra_conc (tra_cod,conc_cod) VALUES ($JsonRec->Trabajador,".$r->fields['int_cod'].")";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->Conceptos AS $ConceptoAux) {
							$q= "INSERT INTO rrhh.tra_conc (tra_cod,conc_cod) VALUES ($JsonRec->Trabajador,$ConceptoAux)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->Conceptos AS $ConceptoAux) {
							$q= "DELETE FROM rrhh.tra_conc WHERE tra_cod=$JsonRec->Trabajador AND conc_cod=$ConceptoAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM rrhh.tra_conc WHERE tra_cod=$JsonRec->Trabajador";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
			case 5:	{
				switch($JsonRec->Accion){
					case 1: {
						$q= "SELECT int_cod FROM rrhh.trabajador WHERE int_cod NOT IN (SELECT tra_cod FROM rrhh.tra_conc WHERE conc_cod=$JsonRec->Concepto)";
						$r = $conn->Execute($q);
						while(!$r->EOF) {
							$q= "INSERT INTO rrhh.tra_conc (tra_cod,conc_cod) VALUES (".$r->fields['int_cod'].",$JsonRec->Concepto)";				
							$conn->Execute($q);
							$r->movenext();
						}
						break;
					}
					case 2: {
						foreach($JsonRec->Trabajadores AS $TrabajadorAux) {
							$q= "INSERT INTO rrhh.tra_conc (tra_cod,conc_cod) VALUES ($TrabajadorAux,$JsonRec->Concepto)";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 3: {
						foreach($JsonRec->Trabajadores AS $TrabajadorAux) {
							$q= "DELETE FROM rrhh.tra_conc WHERE conc_cod=$JsonRec->Concepto AND tra_cod=$TrabajadorAux";				
							$r = $conn->Execute($q);
						}
						break;
					}
					case 4: {
						$q= "DELETE FROM rrhh.tra_conc WHERE conc_cod=$JsonRec->Concepto";				
						$r = $conn->Execute($q);
						break;
					}
				}
				break;
			}
		}
	}
	catch( ADODB_Exception $e ){
		//echo ERROR_CATCH_GENERICO;
		echo $e;
	}
} 
?>