<?
include("comun/ini.php");
//include_once("lib/veh_modelo.class.php");
$xml = new DomDocument();
switch ($_REQUEST['combo']){
	case "catpro":
		$fila = $_GET['fila'];
		$fila = empty($fila) ? "0" : $fila;
		if(empty($_GET['escenario'])){
			$cCategorias = categorias_programaticas::get_all_by_ue($conn, $_GET['ue'], $escEnEje);
			$onchange = 'traeParPreDesdeUpdater(this.value, '.$fila.')';
			$nombre = 'categorias_programaticas[]';
		}else{
			$cCategorias = categorias_programaticas::get_all_by_esc($conn, $_GET['escenario']);
			$onchange = "traeParPreDesdeUpdater(this.value, $('id_escenario').value)";
			$nombre = 'categorias_programaticas';
		}
		$nodoSelect = helpers::xmlCombo($cCategorias, 
																$id_seleccionado='', 
																$nombre, 
																$id='categorias_programaticas_'.$fila, 
																$style='',
																$onchange);
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "tipdoc":
		$mp = $_GET['mp'];
		$nombre = $_GET['nombre'];
		if($nombre == 'tipdoc')
			$onchange = "setNroDoc();";
		else{
			$onchange = "traeNroRefDesdeXML($('unidad_ejecutora').value, $('proveedores').value, this.value)";
			// en caso de que el tipo de movimiento sea aumento, en el tipo de ref debe salir "No referenciado"
			$mp = ($mp == 4 || $mp==6) ? "1" : $mp; 
			$mp--;
		}
		$cTiposDocs = tipos_documentos::get_all_by_mp($conn, $mp);
		$nodoSelect = helpers::xmlCombo($cTiposDocs, 
																$id_seleccionado='', 
																$nombre, 
																$nombre, 
																$style='',
																$onchange,
																$descValor = 'id',
																$descDescripcion = 'descripcion',
																$union=true);
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "parpre":
		$cont = empty($_GET['cont']) ? "0" : $_GET['cont'];
		if(empty($_GET['escenario'])){
			$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $escEnEje);
			$onchange = "traeParCatDesdeXML($('categorias_programaticas_$cont').value, this.value, this.id)";
			$nombre = 'partidas_presupuestarias[]';
		}else{
			$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $_GET['escenario']);
			$onchange = "";
			$nombre = 'partidas_presupuestarias';
		}
		$nodoSelect = helpers::xmlCombo($cPartidas, 
																$id_seleccionado='', 
																$nombre, 
																$id="partidas_presupuestarias_$cont", 
																$style='',
																$onchange);
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "tipref":
		$status = $_GET['status'];
		$oMovimientos = new movimientos_presupuestarios();
		if($status == 5){
			$onchange = "chequeaMontos(this.value)";
			$cMovimientosAum = $oMovimientos->get_all_aumentos($conn);
			$cuenta = count($cMovimientosAum);
			if($cuenta>0){
				foreach($cMovimientosAum as $movAum){
					if(!$oMovimientos->has_referencia($conn, $movAum->nrodoc))
						$cMovimientos[] = $movAum;
				}
			}
		}else{
			$cMovimientos = $oMovimientos->get_all_by_ue_status_prov_tipref($conn, $_GET['ue'], $status, $_GET['id_proveedor'], $_GET['tipref']);
			if ($status=='2'){
				$onchange= 'CargarGridPPPagado(this.value);';
			}else{
				$onchange = "CargarGridPP(this.value)";
			}
		}
		$nodoContenedor = $xml->createElement('div');
		$nodoContenedor->setAttribute('id', "cont_nroref");
		
		if ($status=='2'){
			$nodoSelect = helpers::xmlCombo($cMovimientos, 
																$id_seleccionado='', 
																$nombre='nroref', 
																$id="nroref", 
																$style='',
																$onchange,
																$descValor = 'nroref',
																$descDescripcion = 'descripcion',
																$union=true,
																25);
		}else{
		
			$nodoSelect = helpers::xmlCombo($cMovimientos, 
																$id_seleccionado='', 
																$nombre='nroref', 
																$id="nroref", 
																$style='',
																$onchange,
																$descValor = 'nrodoc',
																$descDescripcion = 'descripcion',
																$union=true,
																25);
		
		}
											
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		$nodoContenedor->appendChild($nodoSelectImp);
		echo($xml->saveXML($nodoContenedor));
		break;
	case "unidades":
		$oUnidades_ejecutoras = new unidades_ejecutoras();
		$cUnidades = $oUnidades_ejecutoras->get_all_by_esc($conn, $_GET['escenario']);
		$nodoSelect = helpers::xmlCombo($cUnidades, 
																$id_seleccionado='', 
																$nombre='unidades_ejecutoras', 
																$id="unidades_ejecutoras",
																'',
																'',
																'id',
																'descripcion',
																true);
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "catproPorEsc":
		$oCategoriasProgramaticas = new categorias_programaticas();
		$cCategorias = $oCategoriasProgramaticas->get_all_by_esc($conn, $_GET['escenario']);
		$onchange = "";
		$nodoSelect = helpers::xmlCombo($cCategorias, 
																$id_seleccionado='', 
																$nombre='categorias_programaticas', 
																$id="categorias_programaticas",
																$style='',
																$onchange,
																'id',
																'descripcion',
																'true',
																'80');
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "parprePorEsc":
		$generica = $_GET['generica'];
		if(empty($generica)){
			$cPartidas = partidas_presupuestarias::get_all_by_esc($conn, $_GET['escenario'], $_GET['madre'], 0, 0, "id", $_GET['relacion']);
	    }else {
			$q = "SELECT a.id_partida_presupuestaria, b.descripcion FROM puser.relacion_pp_cp a ";
			$q.= "INNER JOIN puser.partidas_presupuestarias b ON (a.id_partida_presupuestaria = b.id AND a.id_escenario = b.id_escenario) ";
			$q.= "WHERE a.id_categoria_programatica =  '".$_GET['categoria']."' AND a.id_escenario = '".$_GET['escenario']."' AND a.id_partida_presupuestaria LIKE '".$generica."%'";
			//echo $q;
			$r = $conn->Execute($q);
			$coleccion = array();
			while (!$r->EOF){
				$pp = new partidas_presupuestarias;
				$pp->id = $r->fields['id_partida_presupuestaria'];
				$pp->descripcion = $r->fields['descripcion'];
				//var_dump($pp);
				//$coleccion[] = array('id' => $r->fields['id_partida_presupuestaria'], 'descripcion' => $r->fields['descripcion']);
				$coleccion[] = $pp;
				$r->movenext();
			}
			$cPartidas = $coleccion;
		}
		//die(var_dump($cPartidas));
	//	die($_GET['madre']);
		$onchange = "";
		$nombre = 'partidas_presupuestarias';
		$nodoSelect = helpers::xmlCombo($cPartidas, 
																$id_seleccionado='', 
																$nombre, 
																$id="partidas_presupuestarias_$cont", 
																$style='',
																$onchange,
																'id',
																'descripcion',
																'true',
																'80');
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "parpreFormulacion":
		$orelacion_conc_pp = new relacion_conc_pp();
		$cPartidas = $orelacion_conc_pp->get_pp($conn, '', $_GET['escenario'], '401');
		//$onchange = "traeParCatDesdeXML($('categorias_programaticas_$fila').value, this.value, this.id)";
		$nombre = 'partidas_presupuestarias';
		$nodoSelect = helpers::xmlCombo($cPartidas, 
																$id_seleccionado='', 
																$nombre, 
																$id="partidas_presupuestarias", 
																$style='',
																$onchange,
																'id',
																'descripcion',
																'true',
																'80');
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "parpreNomina":
		$fila = empty($_GET['fila']) ? "0" : $_GET['fila'];
		$cPartidas = partidas_presupuestarias::getAllParpreNomina($conn, $_GET['cp'], $escEnEje);
		$onchange = "traeParCatDesdeXML($('categorias_programaticas_$fila').value, this.value, this.id)";
		$nombre = 'partidas_presupuestarias[]';
		$nodoSelect = helpers::xmlCombo($cPartidas, 
																$id_seleccionado='', 
																$nombre, 
																$id="partidas_presupuestarias_$fila", 
																$style='',
																$onchange);
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	case "padreOperaciones":
		$id_modulo = $_GET['id_modulo'];
		$nodoSelect = helpers::xmlCombo(operaciones::getCarpetas($conn, $id_modulo), 
																$id_seleccionado='', 
																$nombre="padre", 
																$id="padre",
																'',
																'desactivaNivel1();');
		// importamos el nodo select
		$nodoSelectImp = $xml->importNode($nodoSelect, true);
		echo($xml->saveXML($nodoSelectImp));
		break;
	
	#ESTE CASE ES PARA CUANDO SE BUSCA UN MOVIMIENTO PRESUPUESTARIO POR PROVEEDOR Y TIPO DE DOCUMENTO#
	case "MovProTipDoc":
		$id_proveedor = $_GET['id_proveedor'];
		$tipdoc = $_GET['tipdoc'];
		echo helpers::superComboObj(movimientos_presupuestarios::GetMov($conn, $id_proveedor,$tipdoc),'', 'documentos', 'documentos','',"getInfo(this.value,this.id);",'nrodoc','descripcion', true, '18');
		break;
	//ESTE CASO BUSCA LOS MODELOS DE VEHICULOS POR MARCA	
	case "buscaPorMarca":
		$id_marca= $_GET['id_marcas'];
		//$v_mod= new veh_modelo;
		//$Av_mod=v_mod->get($conn)
		echo helpers::superComboObj(veh_modelo::get_marca($conn, $id_marca),'', 'modelos', 'modelos', '', '', 'id', 'mod_nom', false, '18');
		break;
	
	#ESTE COMBO TRAE LAS CUENTAS BANCARIAS POR BANCO#
	case "cuentas_bancarias":
		//echo "entro";
		$id_banco = $_REQUEST['id_banco'];
		$id_cuenta = $_REQUEST['id_cuenta'];
		$disabled = $_REQUEST['disabled'];
		$style = $_REQUEST['style'];
		$conciliacion = $_REQUEST['conciliacion'];
		$onChange = empty($_REQUEST['onchange']) ? 'traeUltimoCheque(this.value)': ($_REQUEST['onchange']==-1 ? '' : stripslashes($_REQUEST['onchange']));
		$cb = new cuentas_bancarias;
		$cuentas = $cb->cuentasxbanco($conn, $id_banco, 2);
		echo helpers::superComboObj($cuentas,$id_cuenta, 'nro_cuenta', 'nro_cuenta',$style,$onChange,'id','nro_cuenta', false, '20',$disabled);
		break;

	#ESTE COMBO TRAE LAS CUENTAS BANCARIAS POR BANCO#
	case "cuentas_bancarias2":
		$id_banco = $_REQUEST['id_banco'];
		$id_cuenta = $_REQUEST['id_cuenta'];
		$cb = new cuentas_bancarias;
		$cuentas = $cb->cuentasxbanco($conn, $id_banco, 1);
		echo helpers::superComboObj($cuentas,$id_cuenta, 'busca_nro_cuenta', 'busca_nro_cuenta','width:150px',"comboNroCuentas()",'id','nro_cuenta', false, '20');
		break;
		
	#ESTE COMBO TRAE LAS CUENTAS BANCARIAS POR BANCO POR CHEQUERA#
	case "cuentas_bancarias3":
		$id_banco = $_REQUEST['id_banco'];
		$id_cuenta = $_REQUEST['id_cuenta'];
		$disabled = $_REQUEST['disabled'];
		$style = $_REQUEST['style'];
		$conciliacion = $_REQUEST['conciliacion'];
		$onChange = empty($_REQUEST['onchange']) ? 'traeUltimoCheque(this.value)': ($_REQUEST['onchange']==-1 ? '' : stripslashes($_REQUEST['onchange']));
		$cb = new cuentas_bancarias;
		$cuentas = $cb->cuentasxbancoxchequera($conn, $id_banco, 2);
		echo helpers::superComboObj($cuentas,$id_cuenta, 'nro_cuenta', 'nro_cuenta',$style,$onChange,'id','nro_cuenta', false, '20',$disabled);
		break;	
		
	#ESTE COMBO TRAE LAS CUENTAS BANCARIAS POR BANCO para la orden de pago#
	case "cuentas_bancarias4":
		$id_banco = $_REQUEST['id_banco'];
		$id_cuenta = $_REQUEST['id_cuenta'];
		$disabled = $_REQUEST['disabled'];
		$style = $_REQUEST['style'];
		$conciliacion = $_REQUEST['conciliacion'];
		//$onChange = empty($_REQUEST['onchange']) ? 'traeUltimoCheque(this.value)': ($_REQUEST['onchange']==-1 ? '' : stripslashes($_REQUEST['onchange']));
		$cb = new cuentas_bancarias;
		$cuentas = $cb->cuentasxbanco($conn, $id_banco, 2);
		echo helpers::superComboObj($cuentas,$id_cuenta, 'nro_cuenta', 'nro_cuenta',$style,'','id','nro_cuenta', false, '20',$disabled);
		break;	

		
	#ESTE COMBO TRAE LAS ORDENES DE PAGO APROBADAS POR PROVEEDOR#
	case "ordenes_pagos":
		$id_proveedor = $_REQUEST['id_proveedor'];
		$op = new orden_pago;
		$ordenes = $op->OrdenesxProveedor($conn, $id_proveedor,2);
		echo helpers::superComboObj($ordenes,'', 'ordenes_pago', 'ordenes_pago','width:150px','CargarGridSP(this.value);','nrodoc','nrodoc', false, '20');
		break;
	
	
	#ESTE CASO SE TRAE UN COMBO CON LAS CATEGORIAS PROGRAMATICAS POR UNIDAD EJECUTORA#
	case "categorias_programaticas":
		$cp = new categorias_programaticas;
		$cCategorias = $cp->get_all_by_ue($conn, $_GET['ue'], $escEnEje);//die(print_r ($cCategorias));
		echo helpers::superComboObj($cCategorias,'', 'categorias_programaticas', 'categorias_programaticas','width:150px','traePartidasPresupuestarias(this.value);','id','descripcion', false, '20');
	break;
	
	#ESTE CASO SE TRAE UN COMBO CON LAS METAS FORMULADAS#
	case "metas":
		$metas = new aprobacion_metas;
		$cMetas = $metas->get_metas($conn, $_REQUEST['id_formulacion']);//die(print_r ($cMetas));
		echo helpers::superComboObj($cMetas,'', 'id_meta', 'id_meta','width:100px','traeDescripcionMeta(this.value)','id','id', false, '');
	break;
	
	#ESTE CASO SE TRAE UN COMBO CON LAS FORMULACIONES DE METAS APROBADAS#
	case "formulacionAprobada":
		$formulacion = new aprobacion_metas;
		$cFormulacion = $formulacion->get_formulacion($conn, $_REQUEST['id_aprobacion_meta']);//die(print_r ($cMetas));
		echo helpers::superComboObj($cFormulacion,'', 'id_formulacion', 'id_formulacion','width:100px','traeMetas(this.value)','id','id', false, '');
	break;
	
	#ESTE CASO SE TRAE UN COMBO CON LAS METAS APROBADAS#
	case "metasAprobadas":
		$metas = new aprobacion_metas;
		$cMetas = $metas->get_metas($conn, $_REQUEST['id_formulacion']);//die(print_r ($cMetas));
		echo helpers::superComboObj($cMetas,'', 'id_meta', 'id_meta','width:100px','','id_cp','id_cp', false, '');
	break;
	
	#ESTE CASO TRAE LOS ID DE LAS UNIDADES EJECUTORAS
	case "id_unidades":
		$oUnidades_ejecutoras = new unidades_ejecutoras();
		$cUnidades = $oUnidades_ejecutoras->get_all_by_esc($conn, $_REQUEST['escenario'],$usuario->id_unidad_ejecutora);
		echo helpers::superComboObj($cUnidades,'', 'id_ue', 'id_ue','width:320px','traeCodigoDesdeXML(this.value)','id','descripcion', true, '');?>  <input type="hidden" style="text-align:right" name="cant_metas" id="cant_metas" value="<?=$cant_metas?>" size="4" /><?
	break;
	
	#ESTE CASO TRAE LOS ID DE LAS UNIDADES EJECUTORAS PARA ESTIMACION DE GASTOS
	case "id_unidades_gastos":
		$oUnidades_ejecutoras = new unidades_ejecutoras();
		$cUnidades = $oUnidades_ejecutoras->get_all_by_esc($conn, $_REQUEST['escenario'],'');
		echo helpers::superComboObj($cUnidades,'', 'id_ue', 'id_ue','width:320px','traeCargosxUnidad(this.value); CargarGrid();','id','descripcion', false, '');
	break;
	
	#ESTE CASO TRAE LOS ID DE LAS UNIDADES EJECUTORAS PARA REPORTES DE FORMULACION
	case "id_unidades_reportes":
		$oUnidades_ejecutoras = new unidades_ejecutoras();
		$cUnidades = $oUnidades_ejecutoras->get_all_by_esc($conn, $_REQUEST['escenario'],$usuario->id_unidad_ejecutora);
		echo helpers::superComboObj($cUnidades,'', 'id_ue', 'id_ue','width:320px','traeCodigoFormulacion(this.value, '.$_REQUEST['escenario'].');','id','descripcion', true, '');
	break;
	
	#ESTE CASO TRAE LOS ID DE LOS TRABAJADORES POR UNIDAD EJECUTORA
	case "id_trabajadores":
		$oTrabajadores = new trabajador();
		$cTrabajadores = $oTrabajadores->get_all_by_ue($conn, $_REQUEST['unidad']);
		echo helpers::superComboObj($cTrabajadores,'', 'Trabajador', 'Trabajador','width:200px','CargarGrid()','int_cod','tra_descrip', false, '');
	break;
	
	#ESTE CASO TRAE LOS CARGOS POR UNIDAD EJECUTORA
	case "cargos_estimacion":
		$oCargos = new cargo();
		$cCargos = $oCargos->getAll_by_UE($conn, $_REQUEST['unidad']);
		//die(var_dump($cCargos));
		echo helpers::superComboObj($cCargos,'', 'Cargo', 'Cargo','width:200px','CargarGrid()','id','descripcion', false, '');
	break;
	
	#ESTE CASO TRAE LOS ID DE LAS FORMULACIONES POR UNIDAD EJECUTORA
	case "id_formulacion":
		$oFormulacion = new formulacion();
		$cFormulacion = $oFormulacion->get_all_by_unidad($conn, $_REQUEST['unidad'], $_REQUEST['escenario'], 2);
		echo helpers::superComboObj($cFormulacion,'', 'Formulacion', 'Formulacion', 'width:200px', 'traeMetas(this.value)', 'id_formulacion', 'id_formulacion', false, '');
	break;
	
	#ESTE CASO SE TRAE UN COMBO TODAS LA PARTIDAS PRESUPUESTARIAS POR CATEGORIA PROGRAMATICA#
	case "partidas_presupuestarias":
		$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $escEnEje, $_GET['idp']);
		echo helpers::superComboObj($cPartidas,'', 'partidas_presupuestarias', 'partidas_presupuestarias','width:150px','traerDisponiblePartidas($(\'categorias_programaticas\').value, this.value);','id','descripcion', false, '20');
		break;
	case "partidas_presupuestarias2":
		$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $escEnEje, $_GET['idp']);
		echo helpers::superComboObj($cPartidas,'', 'partidas_presupuestarias', 'partidas_presupuestarias','width:150px','','id','descripcion', true, '100px');
		break;
		
	#ESTE CASO TRAE LOS PRODUCTOS EN EL COMBO
	case "productos":
		//$cProductos = productos::get_all($conn);
		echo helpers::superCombo($conn,"SELECT DISTINCT puser.relacion_pp_cp.id_categoria_programatica,
		puser.relacion_pp_cp.id_partida_presupuestaria, puser.productos.id, puser.productos.descripcion FROM puser.relacion_pp_cp Inner Join puser.tipo_producto ON puser.relacion_pp_cp.id_partida_presupuestaria = puser.tipo_producto.id_partidas_presupuestarias Inner Join puser.productos ON puser.tipo_producto.id = puser.productos.id_tipo_producto WHERE puser.relacion_pp_cp.id_categoria_programatica = '".$_GET['cp']."'", '', 'productos', 'productos', 'width:120px');
		break;
		
	#ESTE CASO SE TRAE UN COMBO CON LAS CATEGORIAS PROGRAMATICAS POR UNIDAD EJECUTORA#
	case "categorias_programaticas_x_productos":
		$cp = new categorias_programaticas;
		$cCategorias = $cp->get_all_by_ue($conn, $_GET['ue'], $escEnEje); 
		echo helpers::superComboObj($cCategorias,'', 'categorias_programaticas', 'categorias_programaticas','width:150px','','id','descripcion', false, '20');
	break;
	
	case "municipios":
		$ide = $_REQUEST['ide'];
		$idm = $_REQUEST['idm'];
		echo helpers::superCombo($conn, "SELECT * FROM puser.municipios WHERE id_estado = '$ide'", $idm, 'municipios', 'municipios', 'width:120px', "traeParroquias(this.value);");
	break;
	
	case "municipios_buscador":
		$ide = $_REQUEST['ide'];
		echo helpers::superCombo($conn, "SELECT * FROM puser.municipios WHERE id_estado = '$ide'", '', 'search_municipio', 'search_municipio', 'width:200px');
	break;
	
	case "parroquias":
		$idm = $_REQUEST['idm'];
		$idp = $_REQUEST['idp'];
		//die(print_r($_REQUEST));
		echo helpers::superCombo($conn, "SELECT * FROM puser.parroquias WHERE id_municipio = '$idm'", $idp, 'parroquias', 'parroquias', 'width:120px',"traeTerritorios(this.value);");
	break;

	case "territorios":
		$idp = $_REQUEST['idp'];
		$idt = $_REQUEST['idt'];
		//die(print_r($_REQUEST));
		echo helpers::superCombo($conn, "SELECT * FROM puser.territorios WHERE id_parroquia = '$idp'", $idt, 'territorios', 'territorios', 'width:120px');
	break;
	
	case "plan_cuentas":
		$id_escenario = $_REQUEST['id_escenario'];
		$name = $_REQUEST['name'];
		$id = $_REQUEST['id'];
		$movim = $_REQUEST['movim'];
		$relacion = $_REQUEST['relacion'];
		$q = "SELECT id, (codcta || ' - ' || descripcion)::varchar AS descripcion FROM contabilidad.plan_cuenta WHERE id_escenario ='$id_escenario' ";
		if (!empty($movim))
			$q.= " AND movim = '$movim' ";
		if (!empty($relacion))
		{
			$q.= " AND id NOT IN (SELECT COALESCE(id_cuenta_contable::int8, 0) FROM contabilidad.relacion_cc_pp WHERE id_escenario = '$id_escenario') ";
			$q.= " AND id NOT IN (SELECT COALESCE(id_plan_cuenta::int8, 0) FROM finanzas.cuentas_bancarias) ";
			$q.= " AND id NOT IN (SELECT COALESCE(cta_contable::int8, 0) FROM puser.proveedores) ";
			$q.= " AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM finanzas.tipos_solicitud_sin_imp) ";
			$q.= " AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM rrhh.concepto) ";
			$q.= " AND id NOT IN (SELECT COALESCE(id_cta::int8, 0) FROM finanzas.retenciones_adiciones) ";
		}

		$q.= "ORDER BY codcta::text";

		echo helpers::superComboSQL($conn, 
										'',
										'',
										$name,
										$id,
										'',
										'',
										'id',
										'descripcion',
										false,
										'',
										$q, 
										80);
		break;
	
	
	//ESTA CASO CREA UN COMBO DEPENDIENDO DEL TIPO DE BENEFICIARIO PARA LA ORDEN DE PAGO
	case "beneficiario":
		$tipo = $_REQUEST['tipo'];
		$id_sel = $_REQUEST['id_seleccionado'];
		if($tipo==1)
			$q = "proveedores";
		else
			$q = "ciudadanos";
		
		echo helpers::superCombo($conn, $q, $id_sel, 'beneficiarios', 'beneficiarios','width:150px', "TraeSPDesdeXML(this.value,'014');",'id','nombre','',55);
		break;
	
	case "balance_comp_anios":
		$tipo = $_GET['tipo'];
		$status = stripslashes($_GET['status']);

		$sql = "SELECT DISTINCT ano FROM contabilidad.consolidado ";
		$sql.= ($tipo == 'G') ? "WHERE status = 'C' ":"WHERE status IN ($status)";
		
		echo helpers::superComboSQL($conn,
												'',
												'',
												'anios',
												'anios',
												'',
												'traeMesesDesdeUpdater(this.value)',
												'ano',
												'ano',
												false,
												'ano ASC',
												$sql);
		break;
		
	case "balance_comp_meses":
		$anio = $_GET['anio'];
		$tipo = $_GET['tipo'];
		$status = stripslashes($_GET['status']);
		
		$sql = "SELECT DISTINCT mes FROM contabilidad.consolidado WHERE ano = $anio ";
		$sql.= ($tipo == 'G') ? " AND status = 'C' ":" AND status IN ($status)";

		echo helpers::superComboSQL($conn,
												'',
												'',
												'meses',
												'meses',
												'',
												'',
												'mes',
												'mes',
												false,
												'mes ASC',
												$sql);
		break;
	case "catProPorUnidad":
		$cp = new categorias_programaticas;
		$cCategorias = $cp->get_all_by_ue($conn, $_GET['ue'], $_GET['esc']);
		echo helpers::superComboObj($cCategorias,'', 'catPro', 'catPro','width:250px',
											"traeParPre('pinicial',this.value); traeParPre('pfinal',this.value);",
											'id','descripcion', false);
	break;
	case "parPreByCatPro":
		$cPartidas = partidas_presupuestarias::get_all_by_cp($conn, $_GET['cp'], $escEnEje, $_GET['idp']);
		echo helpers::superComboObj($cPartidas,'', $_GET['npartida'], $_GET['npartida'],'width:300px','','id','descripcion', true);
		break;
		
	case "ano_conc":
		$id_cta_banc = $_REQUEST['id_cta'];
		$onChange = stripslashes($_REQUEST['onchange']);
		$sql = "SELECT DISTINCT substring(fecha_hasta, 1, 4)::varchar AS ano FROM contabilidad.conciliacion WHERE id_cta_banc = $id_cta_banc ORDER BY ano";
		echo helpers::superComboSQL($conn, 
											'',
											'',
											'anio',
											'anio',
											'',
											$onChange,
											'ano',
											'ano',
											false,
											'',
											$sql);
		break;
		
	case "mes_conc":
		$id_cta_banc = $_REQUEST['id_cta'];
		$anio = $_REQUEST['anio'];
		$sql = "SELECT DISTINCT substring(fecha_hasta, 6, 2)::varchar AS mes FROM contabilidad.conciliacion WHERE id_cta_banc = $id_cta_banc AND substring(fecha_hasta, 1, 4)::varchar = '$anio' ORDER BY mes";

		$sql = "SELECT DISTINCT substring(fecha_hasta, 6, 2)::varchar AS mes, ";
		$sql.= "(CASE substring(fecha_hasta, 6, 2)::int WHEN 1 THEN 'Enero' ";
		$sql.= "WHEN 2 THEN 'Febrero' ";
		$sql.= "WHEN 3 THEN 'Marzo' ";
		$sql.= "WHEN 4 THEN 'Abril' ";
		$sql.= "WHEN 5 THEN 'Mayo' ";
		$sql.= "WHEN 6 THEN 'Junio' ";
		$sql.= "WHEN 7 THEN 'Julio' ";
		$sql.= "WHEN 8 THEN 'Agosto' ";
		$sql.= "WHEN 9 THEN 'Septiembre' ";
		$sql.= "WHEN 10 THEN 'Octubre' ";
		$sql.= "WHEN 11 THEN 'Noviembre' ";
		$sql.= "WHEN 12 THEN 'Diciembre' END)::varchar as desc_mes ";
		$sql.= "FROM contabilidad.conciliacion WHERE id_cta_banc = $id_cta_banc AND substring(fecha_hasta, 1, 4)::varchar = '$anio' ORDER BY mes ";

		echo helpers::superComboSQL($conn, 
											'',
											'',
											'mes',
											'mes',
											'',
											'',
											'mes',
											'desc_mes',
											false,
											'',
											$sql);
		break;
		
	case "periodosContrato":
		$id_contrato = $_REQUEST['id_contrato'];
		$name = empty($_REQUEST['name']) ? 'periodos':$_REQUEST['name'];
		$id = empty($_REQUEST['id']) ? 'periodos':$_REQUEST['id'];
		$sql = "SELECT int_cod AS id, (substring(nom_fec_ini, 9, 2) || '/' || substring(nom_fec_ini, 6, 2) || '/' || substring(nom_fec_ini, 1, 4) || ' - ' || substring(nom_fec_fin, 9, 2) || '/' || substring(nom_fec_fin, 6, 2) || '/' || substring(nom_fec_ini, 1, 4))::varchar AS descripcion FROM rrhh.historial_nom WHERE cont_cod = $id_contrato";
//		echo $sql."<br />";
		echo helpers::superComboSQL($conn,
											'',
											'',
											$name,
											$id,
											'',
											'',
											'id',
											'descripcion',
											false,
											'',
											$sql);
		break;
		
		case "cuentasProveedores":
		$tipo = $_GET['tipo'];
		$ctaContable = $_GET['ctaContable'];
		//$status = stripslashes($_GET['status']);

		$q = "SELECT id, (codcta || ' - ' || descripcion)::varchar AS descripcion FROM contabilidad.plan_cuenta WHERE id_escenario ='$escEnEje' AND movim='S' ";
		$q.= "AND id NOT IN (SELECT COALESCE(id_cuenta_contable::int8, 0) FROM contabilidad.relacion_cc_pp WHERE id_escenario = '$escEnEje') ";
		$q.= "AND id NOT IN (SELECT COALESCE(id_plan_cuenta::int8, 0) FROM finanzas.cuentas_bancarias) ";
		$q.= ($tipo!='S') ? "AND id NOT IN (SELECT COALESCE(cta_contable::int8, 0) FROM puser.proveedores ".(!empty($ctaContable) ? "WHERE cta_contable <> ".$ctaContable : "").") " : "";
		$q.= "AND id NOT IN (SELECT COALESCE(cuenta_contable::int8, 0) FROM finanzas.tipos_solicitud_sin_imp) ";
		$q.= "AND id NOT IN (SELECT COALESCE(id_cta::int8, 0) FROM finanzas.retenciones_adiciones) ";
		$q.= "ORDER BY codcta::text ";
		
		echo helpers::superComboSQL($conn, 
										  '',
										  $ctaContable,
										  'cta_contable', 
										  'cta_contable',
										  'width:420px;',
										  '',
										  'id',
										  'descripcion',
										  false,
										  '',
										  $q,
										  60);
		break;
		
		case "unidadesEjecutoras":
			$esc = $_REQUEST['escenario'];
			$unidad = $_REQUEST['unidad'];
			$q = "SELECT id, id||' - '||descripcion AS descripcion FROM puser.unidades_ejecutoras WHERE id_escenario = '$esc'";
			//die($q);
			echo helpers::superCombo($conn, "SELECT id, id||' - '||descripcion AS descripcion FROM puser.unidades_ejecutoras WHERE id_escenario = '$esc' ORDER BY id", $unidad, 'unidad_ejecutora', 'unidad_ejecutora', 'width:180px', "mostrarBuscarCat(); traeResponsable(this.value)");
		break;
		
		case "mesConciliar":
		$idCuenta = $_REQUEST['id_cuenta'];
		//die(print_r($_REQUEST));
		/*die("SELECT  MIN(date_part('month', fech_inicio)) AS descripcion, id FROM contabilidad.estado_cuenta 
										 WHERE id_cuenta = $idCuenta AND id_conciliacion is null
										 GROUP BY id, fech_inicio
										 ORDER BY fech_inicio ASC
										 LIMIT 1");*/
		echo helpers::superCombo($conn, "SELECT  MIN(date_part('month', fech_inicio)) AS descripcion, id FROM contabilidad.estado_cuenta 
										 WHERE id_cuenta = $idCuenta AND id_conciliacion is null
										 GROUP BY id, fech_inicio
										 ORDER BY fech_inicio ASC
										 LIMIT 1", '', 'num_mes', 'num_mes', 'width:100px',"buscaConciliacion(this.value, $idCuenta)",'id', 'descripcion');
		break;
		
		case "cuentasReceptoras":
		$idCuenta = $_REQUEST['id_cuenta'];		
		//echo "SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb INNER JOIN public.banco as b ON cb.id_banco=b.id WHERE cb.id != $idCuenta";
		//echo 'Esta aqui';
		echo helpers::superComboSQL($conn, 
											'',
											$idCuenta, 
											'id_cuenta_receptora',
											'id_cuenta_receptora',
											'width:400px', 
											'',
											id,
											'descripcion',
											false,
											'',
											"SELECT cb.id, (b.descripcion || ' - ' || cb.nro_cuenta)::varchar as descripcion FROM finanzas.cuentas_bancarias as cb INNER JOIN public.banco as b ON cb.id_banco=b.id WHERE cb.id != $idCuenta  order by descripcion",
											80);
		break;
		
		case "banco":
			//die('entro');
			$id_banco = $_REQUEST['id_banco'];
			$bn = new banco;
			$oBancos = $bn->get_all($conn);
			$div = "'divnrocuenta'";
			echo helpers::superComboObj($oBancos, $id_banco, 'banco', 'banco','width:150px',"traeCuentasBancarias(this.value,$div,'','')",'id','descripcion', false, 20);
		break;		
}

?>
