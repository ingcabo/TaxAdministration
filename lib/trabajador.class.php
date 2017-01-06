<?
class trabajador{

	// Propiedades
	var $int_cod;
	var $tra_cod;
	var $tra_nom;
	var $tra_ape ;
	var $tra_ced;
	var $tra_descrip;
	var $tra_nac;
	var $tra_fec_nac;
	var $tra_sex ;
	var $tra_telf;
	var $tra_dir;
	var $tra_fec_ing;
	var $tra_fec_egr;
	var $tra_estatus ;
	var $tra_num_cta;
	var $tra_tip_pag;
	var $car_cod;
	var $car_nom;
	var $fun_cod;
	var $fun_nom;
	var $ban_cod;
	var $ban_nom;
	var $dep_cod;
	var $dep_nom;
	var $div_cod;
	var $familiar;
	var $tra_tipo_cta;
	var $tra_sueldo;
	var $tra_vac;
	var $id_territorio;
	var $territorio;
	var $id_parroquia;
	var $id_municipio;
	var $id_estado;
	var $tra_tipo;
	var $cont_cod;
	var $total;
	//PARA LISTADO
	var $tra_nac_desc;
	var $tra_fec_nac_for;
	var $tra_sex_desc;
	var $tra_fec_ing_for;
	var $tra_fec_egr_for;
	var $tra_estatus_desc;
	var $tra_tip_pag_desc;
	var $tra_tipo_cta_desc;
	var $tra_vac_desc;
	var $cont_nom;

	function get($conn, $int_cod){
		try {
			$q = "SELECT * FROM rrhh.trabajador WHERE int_cod=$int_cod";
			$r = $conn->Execute($q);
			if(!$r->EOF){
				$this->int_cod = $r->fields['int_cod'];
				$this->tra_cod = $r->fields['tra_cod'];
				$this->tra_nom = $r->fields['tra_nom'];
				$this->tra_ape = $r->fields['tra_ape'];
				$this->tra_ced = $r->fields['tra_ced'];
				// solo para estimacion de gastos;
				$this->tra_descrip = $r->fields['tra_nom'].' '.$r->fields['tra_ape'].' - '.$r->fields['tra_ced'];
				$this->tra_nac = $r->fields['tra_nac'];
				$this->tra_nac_desc = $r->fields['tra_nac']==0 ? 'Venezolano' : 'Extrajero';
				$this->tra_fec_nac = $r->fields['tra_fec_nac'];
				$this->tra_fec_nac_for = empty($r->fields['tra_fec_nac']) ? '' : muestrafecha($r->fields['tra_fec_nac']);
				
				$this->tra_sex = $r->fields['tra_sex'];
				
				$this->tra_sex_desc = $r->fields['tra_sex']==0 ? 'Masculino' : 'Femenino';
				
				$this->tra_telf = $r->fields['tra_tel'];
				$this->tra_dir = $r->fields['tra_dir'];
				$this->tra_fec_ing = $r->fields['tra_fec_ing'];
				$this->tra_fec_ing_for = empty($r->fields['tra_fec_ing']) ? '' : muestrafecha($r->fields['tra_fec_ing']);
				$this->tra_fec_egr = $r->fields['tra_fec_egr'];
				$this->tra_fec_egr_for = empty($r->fields['tra_fec_egr']) ? '' : muestrafecha($r->fields['tra_fec_egr']);
				$this->tra_estatus = $r->fields['tra_estatus'];
				$this->tra_estatus_desc = $r->fields['tra_estatus']==0 ? 'Activo' : ($r->fields['tra_estatus']==1 ? 'Vacaciones' : ($r->fields['tra_estatus']==2 ? 'Reposo' : ($r->fields['tra_estatus']==3 ? 'Por Egresar' : ($r->fields['tra_estatus']==4 ? 'Egresado' : 'Inactivo'))));
				$this->tra_num_cta = $r->fields['tra_num_cta'];
				$this->tra_tip_pag = $r->fields['tra_tip_pag'];
				$this->tra_tip_pag_desc = $r->fields['tra_tip_pag']==0 ? 'Efectivo' : ($r->fields['tra_tip_pag']==1 ? 'Cheque' : 'Deposito');
				$this->car_cod = $r->fields['car_cod'];
				$this->fun_cod = $r->fields['fun_cod'];
				$this->ban_cod = $r->fields['ban_cod'];
				$this->dep_cod = $r->fields['dep_cod'];
				$this->tra_tipo_cta = $r->fields['tra_tipo_cta'];
				$this->tra_tipo_cta_desc = $r->fields['tra_tipo_cta']==0 ? 'Corriente' : 'Ahorro';
				$this->tra_sueldo =  muestrafloat($r->fields['tra_sueldo']);
				$this->tra_vac =  $r->fields['tra_vac'];
				$this->tra_vac_desc = $r->fields['tra_vac']==1 ? 'Vacante' : '';
				$this->tra_tipo = empty($r->fields['tra_tipo']) ? 0 : $r->fields['tra_tipo'];				
				$this->id_territorio =  $r->fields['id_territorio'];

				// 
				if($this->id_territorio){
					$q="SELECT ";
					$q.="A.id, ";
					$q.="A.descripcion, ";
					$q.="A.id_parroquia, ";
					$q.="B.id_municipio, ";
					$q.="C.id_estado ";
					$q.="FROM puser.territorios AS A ";
					$q.="INNER JOIN puser.parroquias AS B ON A.id_parroquia = B.id ";
					$q.="INNER JOIN puser.municipios AS C ON B.id_municipio = C.id ";
					$q.="WHERE A.id = ".$this->id_territorio;
					//die($q);
					$rPME = $conn->Execute($q);
					if(!$rPME->EOF){
						$this->territorio =  $rPME->fields['descripcion'];
						$this->id_parroquia =  $rPME->fields['id_parroquia'];
						$this->id_municipio =  $rPME->fields['id_municipio'];
						$this->id_estado =  $rPME->fields['id_estado'];
					}
				}
				//

				$qD = "SELECT * FROM rrhh.departamento WHERE int_cod=$this->dep_cod";
				$rD = $conn->Execute($qD);
				$this->div_cod = $rD->fields['div_cod'];
				$this->dep_nom = $rD->fields['dep_nom'];
				if($this->tra_tipo){
					$qD = "SELECT fun_nom FROM rrhh.funciones WHERE int_cod=$this->fun_cod";
					$rD = $conn->Execute($qD);
					$this->fun_nom = $rD->fields['fun_nom'];
					}
					else{
						$qD = "SELECT * FROM rrhh.cargo WHERE int_cod=$this->car_cod";
						$rD = $conn->Execute($qD);
						$this->car_nom = $rD->fields['car_nom'];						
						}
				$qD = "SELECT * FROM rrhh.cont_tra AS A INNER JOIN rrhh.contrato AS B ON A.cont_cod=B.int_cod WHERE tra_cod=$this->int_cod";
				//die($qD);
				$rD = $conn->Execute($qD);
				$this->cont_nom = $rD->EOF ? '' : $rD->fields['cont_nom'];
				$this->cont_cod = $rD->EOF ? '' : $rD->fields['int_cod'];
				
				$qD = "SELECT * FROM public.banco WHERE id= $this->ban_cod";
				$rD = $conn->Execute($qD);
				$this->ban_nom = $rD->fields['descripcion'];


				$q = "SELECT * FROM rrhh.carga_familiar WHERE tra_cod=$int_cod";
				$rCF = $conn->Execute($q);
				$i=0;
				while(!$rCF->EOF){
					$FamiliarAux[$i][0] = $rCF->fields['f_tipo'];
					$FamiliarAux[$i][1] = $rCF->fields['f_nom'];
					$FamiliarAux[$i][2] = $rCF->fields['f_ape'];
					$FamiliarAux[$i][3] = date("d/m/Y",strtotime($rCF->fields['f_fec_nac']));
					$FamiliarAux[$i][4] = $rCF->fields['f_sexo'];
					$i++;
					$rCF->movenext();
				}
				$this->familiar = new Services_JSON();
				$this->familiar = is_array($FamiliarAux) ? $this->familiar->encode($FamiliarAux) : false;
			}
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
	function get_all($conn,$emp_cod, $orden="A.int_cod",$Tipo,$Valor,$TipoE, $max=10, $from=1){
		try {
			$TipoE = empty($TipoE) ? 0 : $TipoE;
			if(empty($Valor)){
				$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
				$q.= "ORDER BY $orden ";
			}elseif($Tipo==0){
				$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
				$q.= " AND A.tra_ced ILIKE '$Valor%' ORDER BY $orden ";
			}elseif($Tipo==1){
				$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
				$q.= " AND A.tra_nom ILIKE '$Valor%' ORDER BY $orden ";
			}elseif($Tipo==2){
				$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
				$q.= " AND A.tra_ape ILIKE '$Valor%' ORDER BY $orden ";
			}elseif($Tipo==3){
				$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
				$q.= " AND B.dep_nom ILIKE '$Valor%' ORDER BY $orden ";
			}
			//die($q);
			$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new trabajador;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
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
	function add($conn, $tra_cod, $tra_nom, $tra_ape, $tra_nac, $tra_ced, $tra_fec_nac, $tra_sex, $tra_telf, $tra_dir, $tra_fec_ing, $tra_fec_egr, $tra_estatus, $tra_num_cta, $tra_tip_pag, $car_cod, $fun_cod,$ban_cod, $dep_cod,$familiar,$tra_tipo_cta,$tra_sueldo,$tra_vac,$id_territorio,$tra_tipo){
		try {
			if($ban_cod==-1 || empty($ban_cod)){
				$ban_cod='null';
			}
			$tra_fec_egr= empty($tra_fec_egr) ? 'null' : "'".$tra_fec_egr."'";
			if($tra_vac==1){
				$tra_ced='null';
			}else{
				$tra_ced="'".$tra_ced."'";
				$tra_vac=0;
			}
			if($id_territorio==-1 || empty($id_territorio)){
				$id_territorio='null';
			}
			if($tra_tipo){
						$q = "INSERT INTO rrhh.trabajador ";
						$q.= "(tra_cod, tra_nom, tra_ape, tra_nac, tra_ced, tra_fec_nac, tra_sex, tra_tel, tra_dir, tra_fec_ing, tra_fec_egr, tra_estatus, tra_num_cta, tra_tip_pag, fun_cod, ban_cod, dep_cod,tra_tipo_cta,tra_sueldo,tra_vac,id_territorio, tra_tipo) ";
						$q.= "VALUES ";
						$q.= "('$tra_cod', '$tra_nom', '$tra_ape', '$tra_nac', $tra_ced, '$tra_fec_nac', '$tra_sex', '$tra_telf', '$tra_dir', '$tra_fec_ing', $tra_fec_egr, '$tra_estatus', '$tra_num_cta',$tra_tip_pag, $fun_cod, $ban_cod, $dep_cod,$tra_tipo_cta,$tra_sueldo,$tra_vac,$id_territorio,'$tra_tipo') ";}
			else{
						$q = "INSERT INTO rrhh.trabajador ";
						$q.= "(tra_cod, tra_nom, tra_ape, tra_nac, tra_ced, tra_fec_nac, tra_sex, tra_tel, tra_dir, tra_fec_ing, tra_fec_egr, tra_estatus, tra_num_cta, tra_tip_pag, car_cod, ban_cod, dep_cod,tra_tipo_cta,tra_sueldo,tra_vac,id_territorio, tra_tipo) ";
						$q.= "VALUES ";
						$q.= "('$tra_cod', '$tra_nom', '$tra_ape', '$tra_nac', $tra_ced, '$tra_fec_nac', '$tra_sex', '$tra_telf', '$tra_dir', '$tra_fec_ing', $tra_fec_egr, '$tra_estatus', '$tra_num_cta',$tra_tip_pag, $car_cod, $ban_cod, $dep_cod,$tra_tipo_cta,$tra_sueldo,$tra_vac,$id_territorio,'0') ";			
			}
			//die($q);
			$conn->Execute($q);
			$this->GuardarCargaFamiliar($conn,$tra_cod,$dep_cod,$familiar);
		}
		catch( ADODB_Exception $e ){
			if($e->getCode()==-1)
				return ERROR_CATCH_VFK;
			elseif($e->getCode()==-5)
				return ERROR_CATCH_VUK;
			else
				return ERROR_CATCH_GENERICO;
			return $e;
		}
	}

	function set($conn, $int_cod, $tra_cod, $tra_nom, $tra_ape, $tra_nac, $tra_ced, $tra_fec_nac, $tra_sex, $tra_telf, $tra_dir, $tra_fec_ing, $tra_fec_egr, $tra_estatus, $tra_num_cta, $tra_tip_pag, $car_cod, $fun_cod, $ban_cod, $dep_cod,$familiar,$tra_tipo_cta,$tra_sueldo,$tra_vac,$id_territorio,$crearVacante, $tra_tipo,$cargoAux,$cont_cod, $departamentoAux, $sueldoAux){
		try {
			if($ban_cod==-1 || empty($ban_cod)){
				$ban_cod='null';
			}
			$tra_fec_egr= empty($tra_fec_egr) ? 'null' : "'".$tra_fec_egr."'";
			if($tra_vac==1){
				$tra_ced='null';
			}else{
				$tra_ced="'".$tra_ced."'";
				$tra_vac=0;
			}
			if($id_territorio==-1 || empty($id_territorio)){
				$id_territorio='null';
			}
			if($tra_tipo){
					$q = "UPDATE rrhh.trabajador SET tra_cod='$tra_cod',tra_nom='$tra_nom',tra_ape='$tra_ape',tra_nac='$tra_nac',tra_ced=$tra_ced,tra_fec_nac='$tra_fec_nac',tra_sex='$tra_sex',tra_tel='$tra_telf',tra_dir='$tra_dir',tra_fec_ing='$tra_fec_ing',tra_fec_egr=$tra_fec_egr,tra_estatus='$tra_estatus',tra_num_cta='$tra_num_cta',tra_tip_pag='$tra_tip_pag',fun_cod=$fun_cod,ban_cod=$ban_cod,dep_cod=$dep_cod,tra_tipo_cta=$tra_tipo_cta,tra_sueldo=$tra_sueldo,tra_vac=$tra_vac,id_territorio=$id_territorio,tra_tipo='$tra_tipo' ";
					$q.= "WHERE int_cod=$int_cod";
					}
			else{
					$q = "UPDATE rrhh.trabajador SET tra_cod='$tra_cod',tra_nom='$tra_nom',tra_ape='$tra_ape',tra_nac='$tra_nac',tra_ced=$tra_ced,tra_fec_nac='$tra_fec_nac',tra_sex='$tra_sex',tra_tel='$tra_telf',tra_dir='$tra_dir',tra_fec_ing='$tra_fec_ing',tra_fec_egr=$tra_fec_egr,tra_estatus='$tra_estatus',tra_num_cta='$tra_num_cta',tra_tip_pag='$tra_tip_pag',car_cod=$car_cod,ban_cod=$ban_cod,dep_cod=$dep_cod,tra_tipo_cta=$tra_tipo_cta,tra_sueldo=$tra_sueldo,tra_vac=$tra_vac,id_territorio=$id_territorio,tra_tipo='0' ";
					$q.= "WHERE int_cod=$int_cod";
	
			}
			$conn->Execute($q);
			$this->GuardarCargaFamiliar($conn,$tra_cod,$dep_cod,$familiar);
			if($crearVacante=='true'&& empty($cargoAux)){
				$tra_cod=getCorrelativo($conn, 'tra_cod', 'rrhh.trabajador', 'int_cod');
			 	$this->add($conn, $tra_cod, 'vacante', '', '0', '', date('Y-m-d'), '0', '', '', date('Y-m-d'), '', '0', '', '0', $car_cod, $fun_cod,-1, $dep_cod,'',0,$tra_sueldo,1,-1, $tra_tipo);
				$this->guardaContrato($conn,$cont_cod,'');
			}
			if($crearVacante=='true'&& !empty($cargoAux)){
				$tra_cod=getCorrelativo($conn, 'tra_cod', 'rrhh.trabajador', 'int_cod');
			 	$this->add($conn, $tra_cod, 'vacante', '', '0', '', date('Y-m-d'), '0', '', '', date('Y-m-d'), '', '0', '', '0', $cargoAux, $fun_cod,-1, $departamentoAux,'',0,$sueldoAux,1,-1, $tra_tipo);
				$this->guardaContrato($conn,$cont_cod,'');
			}
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
	function del($conn, $int_cod){
		try {
			$q = "DELETE FROM rrhh.trabajador WHERE int_cod='$int_cod'";
			$conn->Execute($q);
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
	function GuardarCargaFamiliar($conn,$tra_cod,$dep_cod,$familiar){
		try {
			$JsonRec = new Services_JSON();
			$JsonRec=$JsonRec->decode(str_replace("\\","",$familiar));
			if(is_array($JsonRec->familiar)){
				$q = "SELECT int_cod FROM rrhh.trabajador WHERE tra_cod=$tra_cod AND dep_cod=$dep_cod ";
				$r = $conn->Execute($q);
				$TrabajadorAux=$r->fields['int_cod'];
				$q = "DELETE FROM rrhh.carga_familiar WHERE tra_cod=$TrabajadorAux";
				$r = $conn->Execute($q);
				foreach($JsonRec->familiar as $familiarAux){
					$q = "INSERT INTO rrhh.carga_familiar ";
					$q.= "(tra_cod, f_tipo, f_nom, f_ape, f_fec_nac, f_sexo) ";
					$q.= "VALUES ";
					$q.= "($TrabajadorAux, '$familiarAux[0]', '$familiarAux[1]', '$familiarAux[2]','". guardafecha($familiarAux[3])."', '$familiarAux[4]') ";
					//die($q);
					$conn->Execute($q);
				}
			}
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
	function getListadoKeys(){
		try {
			//PARA LISTADO
			$listado[0]['C'] = 'tra_cod'; $listado[0]['C2'] = 'tra_cod'; $listado[0]['D'] = 'Codigo'; 

			$listado[1]['C'] = 'tra_nom'; $listado[1]['C2'] = 'tra_nom'; $listado[1]['D'] = 'Nombre'; 
			
			$listado[2]['C'] = 'tra_ape'; $listado[2]['C2'] = 'tra_ape'; $listado[2]['D'] = 'Apellido'; 

			$listado[3]['C'] = 'tra_ced'; $listado[3]['C2'] = 'tra_ced'; $listado[3]['D'] = 'Cedula'; 
			
			$listado[4]['C'] = 'tra_nac_desc'; $listado[4]['C2'] = 'tra_nac'; $listado[4]['D'] = 'Nacionalidad'; 

			$listado[5]['C'] = 'tra_fec_nac_for'; $listado[5]['C2'] = 'tra_fec_nac';	$listado[5]['D'] = 'F. Nacimiento'; 

			$listado[6]['C'] = 'tra_sex_desc'; $listado[6]['C2'] = 'tra_sex'; $listado[6]['D'] = 'Sexo'  ;

			$listado[7]['C'] = 'tra_tel'; $listado[7]['C2'] = 'tra_tel'; $listado[7]['D'] = 'Telefenos'; 

			$listado[8]['C'] = 'tra_dir'; $listado[8]['C2'] = 'tra_dir'; $listado[8]['D'] = 'Direccion'; 
			
			$listado[9]['C'] = 'tra_fec_ing_for'; $listado[9]['C2'] = 'tra_fec_ing'; $listado[9]['D'] = 'F. Ingreso'; 

			$listado[10]['C'] = 'tra_fec_egr_for'; $listado[10]['C2'] = 'tra_fec_egr'; $listado[10]['D'] = 'F. Egreso'; 

			$listado[11]['C'] = 'tra_estatus_desc'; $listado[11]['C2'] = 'tra_estatus'; $listado[11]['D'] = 'Estatus'; 

			$listado[12]['C'] = 'tra_num_cta'; $listado[12]['C2'] = 'tra_num_cta'; $listado[12]['D'] = 'Nro Cta Bancaria'; 

			$listado[13]['C'] = 'tra_tip_pag_desc'; $listado[13]['C2'] = 'tra_tip_pag'; $listado[13]['D'] = 'Tipo Pago'; 

			$listado[14]['C'] = 'tra_tipo_cta_desc'; $listado[14]['C2'] = 'tra_tipo_cta'; $listado[14]['D'] = 'Tipo Cta'; 

			$listado[15]['C'] = 'tra_sueldo'; $listado[15]['C2'] = 'tra_sueldo'; $listado[15]['D'] = 'Sueldo Mensual'; 

			$listado[16]['C'] = 'tra_vac_desc';	$listado[16]['C2'] = 'tra_vac';	$listado[16]['D'] = 'Vacante'; 

			$listado[17]['C'] = 'territorio'; $listado[17]['C2'] = 'id_territorio'; $listado[17]['D'] = 'Territorio'; 

			$listado[18]['C'] = 'dep_nom'; $listado[18]['C2'] = 'dep_cod'; $listado[18]['D'] = 'Departamento'; 

			$listado[19]['C'] = 'car_nom'; $listado[19]['C2'] = 'car_cod'; $listado[19]['D'] = 'Cargo'; 

			$listado[20]['C'] = 'ban_nom'; $listado[20]['C2'] = 'ban_cod'; $listado[20]['D'] = 'Banco'; 

			$listado[21]['C'] = 'cont_nom'; $listado[21]['C2'] = 'cont_cod'; $listado[21]['D'] = 'Contrato'; 

			return $listado;
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
	function get_all_by_ue($conn, $unidad){
		try {
			$q = "SELECT T.int_cod, T.tra_nom FROM (rrhh.trabajador AS T INNER JOIN rrhh.departamento AS D ON  D.int_cod = T.dep_cod)
				  WHERE D.unidad_ejecutora_cod = '$unidad' order by T.tra_nom";
			//die($q);
			$r = $conn->Execute($q);
			$collection=array();
			while(!$r->EOF){
				$ue = new trabajador;
				$ue->get($conn, $r->fields['int_cod']);
				$coleccion[] = $ue;
				$r->movenext();
			}
			$this->total = $r->RecordCount();
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
	
	function total_registro_busqueda($conn, $emp_cod, $Tipo, $Valor, $TipoE, $orden="B.dep_ord,A.int_cod")
	{
		$TipoE = empty($TipoE) ? 0 : $TipoE;
		if(empty($Valor)){
			$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
			$q.= "ORDER BY $orden ";
		}elseif($Tipo==0){
			$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
			$q.= " AND A.tra_ced ILIKE '$Valor%' ORDER BY $orden ";
		}elseif($Tipo==1){
			$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
			$q.= " AND A.tra_nom ILIKE '$Valor%' ORDER BY $orden ";
		}elseif($Tipo==2){
			$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
			$q.= " AND A.tra_ape ILIKE '$Valor%' ORDER BY $orden ";
		}elseif($Tipo==3){
			$q = "SELECT A.int_cod FROM (rrhh.trabajador as A INNER JOIN rrhh.departamento as B ON A.dep_cod=B.int_cod) INNER JOIN rrhh.division as C ON B.div_cod=C.int_cod WHERE C.emp_cod=$emp_cod AND A.tra_estatus=$TipoE ";
			$q.= " AND B.dep_nom ILIKE '$Valor%' ORDER BY $orden ";
		}
		//die($q);
		$r = $conn->Execute($q);
		$total = $r->RecordCount();
		return $total;
	}
	
	function guardaContrato($conn,$cont_cod, $tra_cod)
	{
		try {
			if(empty($tra_cod))
			{
				$q = "SELECT int_cod FROM rrhh.trabajador  ORDER BY  int_cod DESC LIMIT 1";
				$r = $conn->Execute($q);
				$tra_cod= $r->fields[int_cod];
				$q= "INSERT INTO rrhh.cont_tra (cont_cod,tra_cod) VALUES ($cont_cod, $tra_cod)";
			}
			else{
				$q= "UPDATE rrhh.cont_tra SET cont_cod='$cont_cod' WHERE tra_cod = $tra_cod";
			}
			//die($q);
			$conn->Execute($q);
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
}
?>