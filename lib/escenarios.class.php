<?
class escenarios{

	// Propiedades

	var $id;
	var $id_base;
	var $base;
	var $ano; 
	var $descripcion;
	var $detalle;
	var $factor;
	var $formulacion;
	var $aprobado;
	var $msj;

	var $total;

	function get($conn, $id){
		$q = "SELECT * FROM escenarios WHERE id='$id' ";//die($q);
		$r = $conn->Execute($q);
		if(!$r->EOF){
			$this->id = $r->fields['id'];
			$this->id_base = $r->fields['id_base'];
			$descripcionBase = $this->get_descripcion($conn, $r->fields['id_base']);
			$this->base = empty($descripcionBase) ? "No posee" : $descripcionBase;
			$this->ano = $r->fields['ano'];
			$this->descripcion = $r->fields['descripcion'];
			$this->detalle = $r->fields['detalle'];
			$this->factor = $r->fields['factor'];
			$this->formulacion = $r->fields['formulacion'];
			$this->aprobado = $r->fields['aprobado'];
			return true;
		}else
			return false;
	}

	function get_all($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM escenarios ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		
		while(!$r->EOF){
			$ue = new escenarios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}

	function add($conn, $id, $id_base, $descripcion, $ano, $detalle, $factor, $formulacion){
		$formulacion = ($formulacion == 'on')? "true" : "false";
		if(relacion_pp_cp::has_escenario($conn, $id_base) and ($id != $id_base)){ // si partcat tiene contiene escenarios con ese ID
			// los elimino
			relacion_pp_cp::del_escenario($conn, $id);
			categorias_programaticas::del_escenario($conn, $id);
			partidas_presupuestarias::del_escenario($conn, $id);
			relacion_ue_cp::del_escenario($conn, $id);
			$this->del($conn, $id);
		}
		$q = "INSERT INTO escenarios ";
		$q.= "(id, id_base, descripcion, ano, detalle, factor, formulacion) ";
		$q.= "VALUES ('$id', '$id_base', '$descripcion', '$ano', '$detalle', '$factor', $formulacion) ";
		//die($q);
		if($conn->Execute($q)){
			$oCatpro = new categorias_programaticas;
			$oParPre = new partidas_presupuestarias;
			$oParCat = new relacion_pp_cp;
			if($id_base != 0){
				$catProByEsc = $oCatpro->get_all_by_esc($conn, $id_base);
				if(is_array($catProByEsc)){
					foreach($catProByEsc as $a){
						$oCatpro->add_esc($conn, 
													$a->id, 
													$id, 
													$a->descripcion, 
													$a->os, 
													false, 
													$a->po * $factor, 
												0, 0, 0, 0, 0, 0,
													$ano,
													$a->dp);
					}
				}
				$parPreByEsc = $oParPre->get_all_by_esc($conn, $id_base);
				if(is_array($parPreByEsc)){
					foreach($parPreByEsc as $a){
						$oParPre->add_esc($conn, 
												$a->id, 
												$id, 
												$a->descripcion, 
												$a->detalle, 
												$a->gastos_inv, 
												$a->id_contraloria, 
												$a->presupuesto_original * $factor, 
												0, 0, 0, 0, 0, 0,
												$ano);
					}
				}
				$parCatByEsc = $oParCat->get_all_by_esc($conn, $id_base);
				if(is_array($parCatByEsc)){
					foreach($parCatByEsc as $a){
						$oParCat->add($conn, 
												$id, 
												$a->id_categoria_programatica, 
												$a->id_partida_presupuestaria, 
												$a->id_asignacion, 
												$a->presupuesto_original * $factor, 
												0, 0, 0, 0, 0, $a->presupuesto_original * $factor,
												$a->aingresos,
												$a->agastos,
												$ano);
					}
				}
				$oUndEje = new unidades_ejecutoras;
				$undEjeByEsc = $oUndEje->get_all_by_esc($conn, $id_base);
				if(is_array($undEjeByEsc)){
					foreach($undEjeByEsc as $a){
						$oUndEje->add($conn, 
												$a->id, 
												$id, 
												$a->descripcion, 
												$a->responsable);
					}
				}
				$oUndCat = new relacion_ue_cp;
				$undCatByEsc = $oUndCat->get_all_by_esc($conn, $id_base);
				if(is_array($undCatByEsc)){
					foreach($undCatByEsc as $a){
						$oUndCat->add($conn, 
												$id, 
												$a->id_categoria_programatica, 
												$a->id_unidad_ejecutora, 
												$a->descripcion);
					}
				}
			}else{
				// Actualizo las tablas categorias_programaticas, relacion_pp_cp y partidas_presupuestarias
				relacion_pp_cp::set_by_esc($conn, $id_base, $factor);
				categorias_programaticas::set_by_esc($conn, $id_base, $factor);
				partidas_presupuestarias::set_by_esc($conn, $id_base, $factor);
			}
			$this->msj = REG_ADD_OK;
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function set($conn, $id_nuevo, $id, $id_base, $descripcion, $ano, $detalle, $factor, $formulacion){
		$formulacion = ($formulacion == 'on')? "true" : "false";
		$q = "UPDATE escenarios SET id = '$id_nuevo', id_base='$id_base', ";
		$q.= "descripcion = '$descripcion', ano = '$ano', ";
		$q.= "detalle = '$detalle', factor = '$factor', formulacion = $formulacion ";
		$q.= "WHERE id='$id' ";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_SET_OK;
			return true;
		}else{
			$this->msj = ERROR;
			return false;
		}
	}

	function del($conn, $id){
		$q = "DELETE FROM escenarios WHERE id='$id'";
		//die($q);
		if($conn->Execute($q)){
			$this->msj = REG_DEL_OK;
			return true;
		} else {
			$this->msj = ERROR;
			return false;
		}
	}
	function get_descripcion($conn, $id){
		$q = "SELECT descripcion FROM escenarios WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF)
			return $r->fields['descripcion'];
		else
			return false;
	}

	function get_ano($conn, $id){
		$q = "SELECT ano FROM escenarios WHERE id='$id'";
		$r = $conn->Execute($q);
		if(!$r->EOF)
			return $r->fields['ano'];
		else
			return false;
	}

	function get_all_sin_aprobar($conn, $from=0, $max=0,$orden="id"){
		$q = "SELECT * FROM escenarios ";
		$q.= "WHERE aprobado = false ";
		$q.= "ORDER BY $orden ";
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new escenarios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		$this->total = $r->RecordCount();
		return $coleccion;
	}
	
	function aprobar($conn, $id, $descripcion, $ano, $detalle, $escEnEje, $anoActual){
		if($id == $escEnEje)	
			return false;
		$sql = "SELECT factor FROM puser.escenarios WHERE id = '$id'";
			if($row = $conn->Execute($sql))
				$factor = $row->fields['factor'];
			else
				$factor = 0;
			
		$q = "UPDATE escenarios SET ";
		//$q.= "ano = '$ano', ";
		$q.= "descripcion = 'Escenario Ejecucion', ";
		$q.= "detalle = '$detalle', aprobado = true, ";
		$q.= "formulacion = false, id = 1111 ";
		$q.= "WHERE ano='$ano' ";
		
		$cierreMalo = $this->validaCierreEscenario($conn);
		$cont = 1;
		if(count($cierreMalo) > 0){
			$text = "Se ha detectado error en las siguientes partidas: ";
			foreach($cierreMalo as $cierre){
				$text.= $cont.". ".$cierre->partidas.",\n ";
				$cont++;	
			}	
			$this->msj = $text;
			return false;
		} else {	
			//die('Sin errores');
			$this->insertaTablasHistoricas($conn, $anoActual);
			$this->borrarData($conn, $escEnEje);
			//$this->actualizaEscenario($conn, $id, $ano, $factor);
			//$this->borrarData($conn, $id);
			$this->del($conn, $escEnEje);	
			//die("TERMINO LINDO Y BELLO");
			if($conn->Execute($q)){
				$this->msj = ESCENARIO_APROBADO;
				return true;
			}else{
				$this->msj = ERROR;
				return false;
			}
		}
	}

	function insertaTablasHistoricas($conn, $ano=''){
		
		#INSERTAR EN TABLA POLITICAS Y DISPOSICIONES
		$q = "INSERT INTO historico.politicas_disposiciones ";
		$q.= "(id_tipo_gaceta, texto1, texto2, texto3, texto4, id, ano) ";
		$q.= "SELECT id_tipo_gaceta, texto1, texto2, texto3, texto4, id, $ano FROM puser.politicas_disposiciones ";
		
		$r = $conn->Execute($q) or die($q);
		
		
		#INSERTAR EN CATEGORIAS_PROGRAMATICAS_HIST#
		$q = "INSERT INTO historico.categorias_programaticas ";
		$q.= "(id, id_escenario, descripcion, objetivo_sectorial, destinada_programa_social, presupuesto_original, ";
		$q.= "aumentos, disminuciones, compromisos, causados, pagados, disponible, ano)  ";
		$q.= "SELECT id, id_escenario, descripcion, objetivo_sectorial, destinada_programa_social, presupuesto_original, ";
		$q.= "aumentos, disminuciones, compromisos, causados, pagados, disponible, ano ";
		$q.= "FROM categorias_programaticas ";
		$q.= "WHERE id_escenario = '1111' ";
			
		$r = $conn->Execute($q) or die($q);
		#INSERTAR EN ESCENARIO_HIST#
		$q1 = "INSERT INTO historico.escenarios (id, id_base, ano, descripcion, detalle, factor, formulacion, aprobado) ";
		$q1.= "SELECT id, id_base, ano, descripcion, detalle, factor, formulacion, aprobado ";
		$q1.= "FROM escenarios ";
		$q1.= "WHERE id = '1111' ";
		$r = $conn->Execute($q1) or die($q1);

		#ESTO ES PARA EL CASO DE LAS UNIDADES EJECUTORAS#
		$q2 = "INSERT INTO historico.unidades_ejecutoras ";
		$q2.= "(id, id_escenario, descripcion, responsable, ano) ";
		$q2.= "SELECT id, id_escenario, descripcion, responsable, ano ";
		$q2.= "FROM unidades_ejecutoras ";
		$q2.= "WHERE id_escenario = '1111' ";

		
		$r = $conn->Execute($q2) or die($q2);
		
		#ESTO ES PARA LA PARTE DE RELACION_PP_CP#
		$q3 = "INSERT INTO historico.relacion_pp_cp ";
		$q3 .= "(id_escenario, id_categoria_programatica, id_partida_presupuestaria, ano, 
				presupuesto_original, aumentos, disminuciones, compromisos, causados, pagados, disponible,id_asignacion,
				aingresos, agastos, id) ";
		$q3 .= "SELECT id_escenario, id_categoria_programatica, id_partida_presupuestaria, ano, 
				presupuesto_original, aumentos, disminuciones, compromisos, causados, pagados, disponible,id_asignacion,
				aingresos, agastos, id FROM relacion_pp_cp ";
		$q3 .= "WHERE id_escenario = '1111' ";
		
		$r = $conn->Execute($q3) or die($q3);
		
		#ESTO ES PARA ALA PARTE DE RELACION_UE_CP#
		$q4 = "INSERT INTO historico.relacion_ue_cp";
		$q4.= "(id_unidad_ejecutora, id_categoria_programatica, id_escenario, descripcion, id, ano) ";
		$q4.= "SELECT id_unidad_ejecutora, id_categoria_programatica, id_escenario, descripcion, id, $ano FROM relacion_ue_cp ";
		$q4.= "WHERE id_escenario = '1111' ";
		
		$r = $conn->Execute($q4) or die($q4);
		
		$q5 = "INSERT INTO historico.partidas_presupuestarias ";
		$q5 .= " (id, id_escenario, descripcion, detalle, gastos_inv, id_contraloria, presupuesto_original, aumentos, disminuciones, compromisos, causados, pagados, disponible, ano, madre, check_ing, ingreso, check_transferencia)";
		$q5 .= " SELECT id, id_escenario, descripcion, detalle, gastos_inv, id_contraloria, presupuesto_original, aumentos, disminuciones, compromisos, causados, pagados, disponible, ano, madre, check_ing, ingreso, check_transferencia ";
		$q5 .= " FROM partidas_presupuestarias WHERE id_escenario='1111'";
		
		$r = $conn->Execute($q5) or die($q5);
		
		// ****AQUI SE GENERAN LOS HISTORICOS PARA LA PARTE FINANCIERA*****
		
		// TABLAS DE REQUISICIONES
		
		$q6 = "INSERT INTO historico.requisiciones ";
		$q6.= "(id, id_unidad_ejecutora, ano, fecha_r, motivo, status, fecha_aprobacion, id_usuario, nroreqgbl) ";
		$q6.= " SELECT id, id_unidad_ejecutora, $ano, fecha_r, motivo, status, fecha_aprobacion, id_usuario, nroreqgbl ";
		$q6.= "FROM puser.requisiciones ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// RELACION REQUISICIONES
		
		$q6 = "INSERT INTO historico.relacion_requisiciones ";
		$q6.= "(id, id_requisicion, id_categoria, id_partida, id_producto, cantidad, cantidad_despachada, cantidad_despachada_anterior, ano) ";
		$q6.= " SELECT id, id_requisicion, id_categoria, id_partida, id_producto, cantidad, cantidad_despachada, cantidad_despachada_anterior, $ano ";
		$q6.= "FROM puser.relacion_requisiciones ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// REQUISICION GLOBAL
		
		$q6 = "INSERT INTO historico.gbl_requisicion ";
		$q6.= "(id, ano, fecha_r, motivo, status, id_usuario) ";
		$q6.= " SELECT id, $ano, fecha_r, motivo, status, id_usuario ";
		$q6.= "FROM puser.gbl_requisicion ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// RELACION REQUISICION GLOBAL
		
		$q6 = "INSERT INTO historico.relacion_gbl_requisicion ";
		$q6.= "(id, id_gbl_requisicion, id_producto, cantidad, cantidad_despachada, ano) ";
		$q6.= " SELECT id, id_gbl_requisicion, id_producto, cantidad, cantidad_despachada, $ano ";
		$q6.= "FROM puser.relacion_gbl_requisicion ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// PROVEEDORES QUE COTIZARON
		
		$q6 = "INSERT INTO historico.proveedores_requisicion ";
		$q6.= "(id, id_requisicion, id_proveedor, id_producto, iva, costo, ano) ";
		$q6.= " SELECT id, id_requisicion, id_proveedor, id_producto, iva, costo, $ano ";
		$q6.= "FROM puser.proveedores_requisicion ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// GANADORES REQUISICIONES
		
		$q6 = "INSERT INTO historico.ganadores_co_re ";
		$q6.= "(id, id_requisicion, id_proveedor, id_producto, ano) ";
		$q6.= " SELECT id, id_requisicion, id_proveedor, id_producto, $ano ";
		$q6.= "FROM puser.ganadores_co_re ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// RECEPCION ORDEN DE COMPRA
		
		$q6 = "INSERT INTO historico.recepcion_orden_compra ";
		$q6.= "(id, id_ordcompra, num_fact, fecha, comentario, id_usuario, total_parcial, num_control, ano) ";
		$q6.= " SELECT id, id_ordcompra, num_fact, fecha, comentario, id_usuario, total_parcial, num_control, $ano ";
		$q6.= "FROM puser.recepcion_orden_compra ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// TABLA DE ORDENES DE COMPRA bien
		
		$q6 = "INSERT INTO historico.orden_compra ";
		$q6.= "(id, fecha, ano, f_entrega, l_entrega, c_pago, f_solicitud, nrodoc, observaciones, rif, ";
		$q6.= "id_unidad_ejecutora, fecha_aprobacion, nrosol, status, nrorequi ) ";
		$q6.= " SELECT id, fecha, $ano, f_entrega, l_entrega, c_pago, f_solicitud, nrodoc, observaciones, rif, ";
		$q6.= "id_unidad_ejecutora, fecha_aprobacion, nrosol, status, nrorequi ";
		$q6.= "FROM puser.orden_compra ";
		
		$r = $conn->Execute($q6) or die($q6);
		
		// TABLA RELACION DE ORDEN DE COMPRAS bien
		
		$q7 = "INSERT INTO historico.relacion_ordcompra ";
		$q7.= "(id, id_ord_compra, id_categoria_programatica, id_partida_presupuestaria, monto, idparcat, id_producto, ";
		$q7.= "cantidad, precio_base, precio_iva, iva_porc, ano) ";
		$q7.= "SELECT id, id_ord_compra, id_categoria_programatica, id_partida_presupuestaria, monto, idparcat, id_producto, ";
		$q7.= "cantidad, precio_base, precio_iva, iva_porc, $ano FROM puser.relacion_ordcompra";
		
		$r = $conn->Execute($q7) or die($q7);
		
		//TABLA OBRAS
		
		$q7 = "INSERT INTO historico.obras ";
		$q7.= "(id_unidad_ejecutora, id_parroquia, ctotal, caa, eaa, epre, inicio, culminacion, cav, eav, epos, id_situacion, denominacion, responsable, id_financiamiento, ";
  		$q7.= "descripcion, id, ano) ";
		$q7.= "SELECT id_unidad_ejecutora, id_parroquia, ctotal, caa, eaa, epre, inicio, culminacion, cav, eav, epos, id_situacion, denominacion, responsable, id_financiamiento, ";
		$q7.= "descripcion, id, $ano FROM puser.obras";
		
		$r= $conn->Execute($q7) or die($q7);
		
		//TABLA RELACION OBRAS
		
		$q7 = "INSERT INTO historico.relacion_obras ";
		$q7.= "(id, monto, id_categoria_programatica, id_partida_presupuestaria, id_parcat, id_obra, ano) ";		
		$q7.= "SELECT id, monto, id_categoria_programatica, id_partida_presupuestaria, id_parcat, id_obra, $ano FROM puser.relacion_obras";
		
		$r = $conn->Execute($q7) or die($q7);
		
		//TABLA CONTRATO DE OBRAS bien
		
		$q8 = "INSERT INTO historico.contrato_obras ";
		$q8.= "(id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_obra, id_usuario, descripcion, fecha, ";
		$q8.= "fecha_aprobacion, nrodoc, id_tipo_fianza, observaciones, ano) ";
		$q8.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_obra, id_usuario, descripcion, fecha, ";
		$q8.= "fecha_aprobacion, nrodoc, id_tipo_fianza, observaciones, $ano FROM puser.contrato_obras  ";
		
		$r= $conn->Execute($q8) or die ($q8);
		
		//TABLA RELACION CONTRATO DE OBRAS bien
		
		$q9 = "INSERT INTO historico.relacion_contrato_obras ";
		$q9.= "(id, id_parcat, id_contrato_obras, id_categoria_programatica, id_partida_presupuestaria, monto, porc_iva, ";
		$q9.= "monto_exc, ano) ";
		$q9.= "SELECT id, id_parcat, id_contrato_obras, id_categoria_programatica, id_partida_presupuestaria, monto, porc_iva, ";
		$q9.= "monto_exc, $ano FROM puser.relacion_contrato_obras ";
		
		$r= $conn->Execute($q9) or die($q9);
		
		//TABLA CONTRATO DE SERVICIO bien
		
		$q10 = "INSERT INTO historico.contrato_servicio ";
		$q10.= "(id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_servicio, id_usuario, descripcion, ";
		$q10.= "fecha, fecha_aprobacion, nrodoc, observaciones, status, ano) ";
		$q10.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_servicio, id_usuario, descripcion, ";
		$q10.= "fecha, fecha_aprobacion, nrodoc, observaciones, status, $ano FROM puser.contrato_servicio ";
		
		$r = $conn->Execute($q10) or die($q10);
		
		//TABLA RELACION CONTRATO DE SERVICIO bien
		
		$q11 = "INSERT INTO historico.relacion_contrato_servicio ";
		$q11.= "(id, id_parcat, id_contrato_servicio, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q11.= "porc_iva, monto_exc, ano) ";
		$q11.= "SELECT id, id_parcat, id_contrato_servicio, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q11.= "porc_iva, monto_exc, $ano FROM puser.relacion_contrato_servicio ";
		
		$r = $conn->Execute($q11) or die($q11);
		
		//TABLA ORDEN DE SERVICIO Y TRABAJO bien
		
		$q12 = "INSERT INTO historico.orden_servicio_trabajo ";
		$q12.= "(id, id_tipo_documento, id_unidad_ejecutora, fecha_entrega, lugar_entrega, condicion_pago, rif, observaciones, ";
		$q12.= "nro_requisicion, fecha_requisicion, nro_cotizacion, nro_factura, fecha_factura, condicion_operacion, cod_contraloria, ";
		$q12.= "fecha, nrodoc, id_ciudadano, id_usuario, fecha_aprobacion, id_proveedor, status, ano) ";
		$q12.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, fecha_entrega, lugar_entrega, condicion_pago, rif, observaciones, ";
		$q12.= "nro_requisicion, fecha_requisicion, nro_cotizacion, nro_factura, fecha_factura, condicion_operacion, cod_contraloria, ";
		$q12.= "fecha, nrodoc, id_ciudadano, id_usuario, fecha_aprobacion, id_proveedor, status, $ano FROM puser.orden_servicio_trabajo ";
		
		$r = $conn->Execute($q12) or die($q12);
		
		//TABLA RELACION ORDEN DE SERVICIO Y TRABAJO bien
		
		$q13 = "INSERT INTO historico.relacion_ord_serv_trab ";
		$q13.= "(id, id_ord_serv_trab, id_categoria_programatica, id_partida_presupuestaria, monto, id_parcat, ";
		$q13.= "porc_iva, monto_exc, ano) ";
		$q13.= "SELECT id, id_ord_serv_trab, id_categoria_programatica, id_partida_presupuestaria, monto, id_parcat, ";
		$q13.= "porc_iva, monto_exc, $ano FROM puser.relacion_ord_serv_trab ";
		
		$r = $conn->Execute($q13) or die($q13);
		
		//TABLA RELACION ORDEN SERVICIO TRABAJO PRODUCTOS bien
		
		$q14 = "INSERT INTO historico.relacion_ord_serv_trab_productos ";
		$q14.= "(id, id_ord_serv_trab, descripcion, precio_base, precio_iva, precio_total, ano) ";
		$q14.= "SELECT id, id_ord_serv_trab, descripcion, precio_base, precio_iva, precio_total, $ano ";
		$q14.= "FROM puser.relacion_ord_serv_trab_productos";
		
		$r = $conn->Execute($q14) or die($q14);
		
		// TABLA AYUDAS bein
		
		$q15 = "INSERT INTO historico.ayudas ";
		$q15.= "(id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_usuario, descripcion, fecha, ";
		$q15.= "fecha_aprobacion, nrodoc, observaciones, status, nombre_benef, cedula_benef, ano) ";
		$q15.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_usuario, descripcion, fecha, ";
		$q15.= "fecha_aprobacion, nrodoc, observaciones, status, nombre_benef, cedula_benef, $ano FROM puser.ayudas ";
		
		$r = $conn->Execute($q15) or die($q15);
		
		// TABLA RELACION AYUDAS bien
		
		$q16 = "INSERT INTO historico.relacion_ayudas ";
		$q16.= "(id, id_parcat, id_ayuda, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q16.= "porc_iva, monto_exc, ano) ";
		$q16.= "SELECT id, id_parcat, id_ayuda, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q16.= "porc_iva, monto_exc, $ano FROM puser.relacion_ayudas ";
		
		$r = $conn->Execute($q16) or die($q16);
		
		//TABLA DOCUMENTOS GENERALES bien
		
		$q17 = "INSERT INTO historico.documentos_generales ";
		$q17.= "(id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_usuario, descripcion, fecha, ";
		$q17.= "fecha_aprobacion, nrodoc, observaciones, status, ano) ";
		$q17.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, id_proveedor, id_usuario, descripcion, fecha, ";
		$q17.= "fecha_aprobacion, nrodoc, observaciones, status, $ano FROM puser.documentos_generales ";
		
		$r = $conn->Execute($q17) or die($q17);
		
		// TABLA RELACION DOCUMENTOS GENERALES bien
		
		$q18 = "INSERT INTO historico.relacion_doc_generales ";
		$q18.= "(id, id_parcat, id_doc_generales, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q18.= "porc_iva, monto_exc, ano) ";
		$q18.= "SELECT id, id_parcat, id_doc_generales, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q18.= "porc_iva, monto_exc, $ano FROM puser.relacion_doc_generales";
		
		$r = $conn->Execute($q18) or die($q18);
		
		// TABLA CAJA CHICA bien		
		$q19 = "INSERT INTO historico.caja_chica ";
		$q19.= "(id, id_tipo_documento, id_unidad_ejecutora, id_ciudadano, id_usuario, descripcion, fecha, ";
		$q19.= "fecha_aprobacion, nrodoc, observaciones, status, ano) ";
		$q19.= "SELECT id, id_tipo_documento, id_unidad_ejecutora, id_ciudadano, id_usuario, descripcion, fecha, ";
		$q19.= "fecha_aprobacion, nrodoc, observaciones, status, $ano FROM puser.caja_chica ";
		
		$r = $conn->Execute($q19) or die($q19);
		
		// TABLA RELACION CAJA CHICA bien
		
		$q20 = "INSERT INTO historico.relacion_caja_chica ";
		$q20.= "(id, id_parcat, id_caja_chica, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q20.= "porc_iva, monto_exc, ano) ";
		$q20.= "SELECT id, id_parcat, id_caja_chica, id_categoria_programatica, id_partida_presupuestaria, monto, ";
		$q20.= "porc_iva, monto_exc, $ano FROM puser.relacion_caja_chica";
		
		$r = $conn->Execute($q20) or die($q20);
		
		// TABLA RELACION FACTURAS CAJA CHICA bien
		
		$q21 = "INSERT INTO historico.relacion_factura_caja_chica ";
		$q21.= "(id, id_caja_chica, nfact, ncontrol, iva, monto, fecha, ano) ";
		$q21.= "SELECT id, id_caja_chica, nfact, ncontrol, iva, monto, fecha, $ano ";
		$q21.= "FROM puser.relacion_factura_caja_chica ";
		
		$r = $conn->Execute($q21) or die($q21);
		
		// TABLA DE MOVIMIENTOS PRESUPUESTARIOS bien
		
		$q22 = "INSERT INTO historico.movimientos_presupuestarios ";
		$q22.= "(id, nrodoc, tipdoc, tipref, nroref, fechadoc, descripcion, status, id_unidad_ejecutora, ano, ";
		$q22.= "id_usuario, fecharef, id_ciudadano, id_proveedor, status_movimiento, cerrado, fecha_cierre) ";
		$q22.= "SELECT id, nrodoc, tipdoc, tipref, nroref, fechadoc, descripcion, status, id_unidad_ejecutora, ano, ";
		$q22.= "id_usuario, fecharef, id_ciudadano, id_proveedor, status_movimiento, cerrado, fecha_cierre ";
		$q22.= "FROM puser.movimientos_presupuestarios ";
		
		$r = $conn->Execute($q22) or die($q22);
		
		// TABLA RELACION MOVIMIENTOS PRESUPUESTARIOS bien
		
		$q23 = "INSERT INTO historico.relacion_movimientos ";
		$q23.= "(id, nrodoc, id_categoria_programatica, id_partida_presupuestaria, monto, id_parcat, ano) ";
		$q23.= "SELECT id, nrodoc, id_categoria_programatica, id_partida_presupuestaria, monto, id_parcat, $ano ";
		$q23.= "FROM puser.relacion_movimientos";
		
		$r = $conn->Execute($q23) or die($q23);
		
		// TABLA RELACION PRODUCTOS DE MOVIMIENTOS PRESUPUESTARIOS bien
		
		$q24 = "INSERT INTO historico.relacion_movimientos_productos ";
		$q24.= "(id, nrodoc, id_producto, cantidad, precio_base, precio_iva, precio_total, ano) ";
		$q24.= "SELECT id, nrodoc, id_producto, cantidad, precio_base, precio_iva, precio_total, $ano ";
		$q24.= "FROM puser.relacion_movimientos_productos ";
		
		$r = $conn->Execute($q24) or die($q24);
		
		// TABLA SOLICITUD PAGO
		
		$q24 = "INSERT INTO historico.solicitud_pago (nrodoc, nroref, fecha, status, fuente_financiamiento, fecha_aprobacion, descripcion, pago, ";
  		$q24.= "id_proveedor, id_unidad_ejecutora, ano) ";
		$q24.= "SELECT nrodoc, nroref, fecha, status, fuente_financiamiento, fecha_aprobacion, descripcion, pago, ";
  		$q24.= "id_proveedor, id_unidad_ejecutora, $ano ";
		$q24.= "FROM finanzas.solicitud_pago";
		
		$r = $conn->Execute($q24) or die($q24);
		
		// TABLA RELACION SOLICITUD PAGO
		
		$q24 = "INSERT INTO historico.relacion_solicitud_pago (id, id_parcat, id_solicitud_pago, id_categoria_programatica, id_partida_presupuestaria, ";
		$q24.= "monto, nroref, ano) ";
		$q24.= "SELECT id, id_parcat, id_solicitud_pago, id_categoria_programatica, id_partida_presupuestaria, monto, nroref, $ano ";
		$q24.= "FROM puser.relacion_solicitud_pago";
		
		$r = $conn->Execute($q24) or die($q24);
		
		// TABLA ORDEN DE PAGO
		$q25 = "INSERT INTO historico.orden_pago ";
		$q25.= "(nrodoc, nroref, fecha, status, id_condicion_pago, fuente_financiamiento, id_tipo_solicitud_si, ";
		$q25.= "monto_si, fecha_aprobacion, montodoc, montoret, montopagado, motivo, id_proveedor, ";
		$q25.= "id_unidad_ejecutora, descripcion, cuenta_contable_anticipo, monto_anticipo, fecha_anulacion, ano) ";
		$q25.= "SELECT nrodoc, nroref, fecha, status, id_condicion_pago, fuente_financiamiento, id_tipo_solicitud_si, ";
		$q25.= "monto_si, fecha_aprobacion, montodoc, montoret, montopagado, motivo, id_proveedor, ";
		$q25.= "id_unidad_ejecutora, descripcion, cuenta_contable_anticipo, monto_anticipo, fecha_anulacion, $ano FROM finanzas.orden_pago ";
		
		$r = $conn->Execute($q25) or die($q25);
		
		// TABLA RELACION ORDEN DE PAGO
		
		$q26 = "INSERT INTO historico.relacion_orden_pago ";
		$q26.= "(id, id_parcat, id_categoria_programatica, id_partida_presupuestaria, monto, id_orden_pago, ano) ";
		$q26.= "SELECT id, id_parcat, id_categoria_programatica, id_partida_presupuestaria, monto, id_orden_pago, $ano ";
		$q26.= "FROM finanzas.relacion_orden_pago ";
		
		$r = $conn->Execute($q26) or die($q26);
		
		// TABLA RELACION FACTURAS DE ORDENES DE PAGO
		
		$q27 = "INSERT INTO historico.facturas ";
		$q27.= "(id, nrodoc, nrofactura, fecha, monto, base_imponible, monto_excento, monto_iva, iva_retenido, ";
		$q27.= "iva, nrocontrol, ano) ";
		$q27.= "SELECT id, nrodoc, nrofactura, fecha, monto, base_imponible, monto_excento, monto_iva, iva_retenido, ";
		$q27.= "iva, nrocontrol, $ano FROM finanzas.facturas";
		
		$r = $conn->Execute($q27) or die($q27);
		
		// TABLA RELACION RETENCIONES DE ORDEN DE COMPRA
		
		$q28 = "INSERT INTO historico.relacion_retenciones_orden ";
		$q28.= "(id, nrodoc, codret, mntret, mntbas, porcen, anio, porc_ret, aplico_sust, ano) ";
		$q28.= "SELECT id, nrodoc, codret, mntret, mntbas, porcen, anio, porc_ret, aplico_sust, $ano ";
		$q28.= "FROM finanzas.relacion_retenciones_orden";
		
		$r = $conn->Execute($q28) or die($q28);
		
		// HISTORICO DE PAGOS
		
		$q29 = "INSERT INTO historico.cheques ";
		$q29.= "(id, fecha, status, id_proveedor, nro_cuenta, nro_cheque, id_banco, nrodoc, id_escenario, observacion) ";
		$q29.= "SELECT id, fecha, status, id_proveedor, nro_cuenta, nro_cheque, id_banco, nrodoc, $ano, observacion ";
		$q29.= "FROM finanzas.cheques ";
		
		$r = $conn->Execute($q29) or die($q29);
		
		$q30 = "INSERT INTO historico.relacion_cheque ";
		$q30.= "(id, nrodoc, nroref, monto, ano) SELECT id, nrodoc, nroref, monto, $ano ";
		$q30.= "FROM finanzas.relacion_cheque ";
		
		$r = $conn->Execute($q30) or die($q30);
		
		$q31 = "INSERT INTO historico.otros_pagos ";
		$q31.= "(id, fecha, status, id_proveedor, nro_cuenta, id_banco, nrodoc, id_escenario, observacion) ";
		$q31.= "SELECT id, fecha, status, id_proveedor, nro_cuenta, id_banco, nrodoc, $ano, observacion ";
		$q31.= "FROM finanzas.otros_pagos ";
		
		$r = $conn->Execute($q31) or die($q31);
		
		$q32 = "INSERT INTO historico.relacion_otros_pagos ";
		$q32.= "(id, nrodoc, nroref, monto, ano) ";
		$q32.= "SELECT id, nrodoc, nroref, monto, $ano FROM finanzas.relacion_otros_pagos ";
		
		$r = $conn->Execute($q32) or die($q32);
		
		$q33 = "UPDATE contabilidad.com_enc SET id_escenario = $ano WHERE id_escenario = '1111'";
		
		$r = $conn->Execute($q33) or die($q33);
				
		// ****AQUI SE GENERAN LOS HISTORICOS PARA LA PARTE CONTABLE*****
		// TABLA DE EMCABEZADO DEL COMPROBANTE
		
		/*$q33 = "INSERT INTO historico.com_enc ";
		$q33.= "(id_escenario, ano, mes, numcom, descrip, fecha, origen, status, transferido, id, num_doc, ";
		$q33.= "id_conciliacion, fecha_conciliacion) ";
		$q33.= "SELECT $ano, ano, mes, numcom, descrip, fecha, origen, status, transferido, id, num_doc, ";
		$q33.= "id_conciliacion, fecha_conciliacion FROM contabilidad.com_enc ";
		
		$r = $conn->Execute($q33) or die($q33);
		
		// TABLA DE DETALLE DEL COMPROBANTE
		
		$q34 = "INSERT INTO historico.com_det ";
		$q34.= "(id_com, id_cta, debe, haber, docref, descrip, id) ";
		$q34.= "SELECT id_com, id_cta, debe, haber, docref, descrip, id FROM contabilidad.com_det ";
		
		$r = $conn->Execute($q34) or die($q34);
		
		// TABLA DE PLAN DE CUENTAS
		
		$q35 = "INSERT INTO historico.plan_cuenta ";
		$q35.= "(codcta, ano, descripcion, saldo_inicial, naturaleza, movim, nominal, id, id_escenario, id_acumuladora) ";
		$q35.= "SELECT codcta, ano, descripcion, saldo_inicial, naturaleza, movim, nominal, id, $ano, id_acumuladora ";
		$q35.= "FROM contabilidad.plan_cuenta ";
		
		$r = $conn->Execute($q35) or die($q35);*/
		
		// TABLA RELACION CUENTA CONTABLE CON PARTIDA PRESUPUESTARIA
		
		/*$q36 = "INSERT INTO historico.relacion_cc_pp ";
		$q36.= "(id_cuenta_contable, id_partida_presupuestaria, id, id_escenario) ";
		$q36.= "SELECT id_cuenta_contable, id_partida_presupuestaria, id, $ano FROM contabilidad.relacion_cc_pp ";
		
		$r = $conn->Execute($q36) or die($q36);*/
			
			
	}
	
	function borrarData($conn, $id_escenario){
		
		// SE ELIMINAN LOS REGISTROS FINANCIERON DE LA EJECUCION ACTUAL
		
		// TABLAS DE REQUISICION DE COMPRAS
		
		$q = "DELETE FROM puser.recepcion_orden_compra ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.ganadores_co_re ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.proveedores_requisicion ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_gbl_requisicion ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_requisiciones ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.precompromiso_requisiciones ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.requisiciones ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE ORDENES DE COMPRA DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.orden_compra ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_ordcompra ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.gbl_requisicion ";
		$r = $conn->execute($q) or die($q);
		
		
		
		#ELIMINO LOS REGISTROS DE ORDENES DE SERVICIO/TRABAJO DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.orden_servicio_trabajo ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_ord_serv_trab ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_ord_serv_trab_productos ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE CONTRATO DE OBRAS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.contrato_obras_fianza ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.contrato_obras ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_contrato_obras ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE CONTRATO DE SERVICIOS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.contrato_servicio ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_contrato_servicio ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE AYUDAS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.ayudas ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_ayudas ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE DOCUMENTOS GENERALES DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.documentos_generales ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_doc_generales ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE DOCUMENTOS GENERALES DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.caja_chica ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_caja_chica ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_factura_caja_chica ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE MOVIMIENTOS PRESUPUESTARIOS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.movimientos_presupuestarios ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_movimientos ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_movimientos_productos ";
		$r = $conn->execute($q) or die($q);	
		
		#ELIMINO LOS REGISTROS DE SOLICITUD DE PAGO#
		
		$q = "DELETE FROM finanzas.solicitud_pago ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_solicitud_pago";
		$r = $conn->execute($q) or die($q);	
		
		#ELIMINO LOS REGISTROS DE ORDEN DE PAGO DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM finanzas.orden_pago ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM finanzas.relacion_orden_pago ";
		$r = $conn->execute($q) or die($q);	
		
		$q = "DELETE FROM finanzas.facturas ";
		$r = $conn->execute($q) or die($q);	
		
		$q = "DELETE FROM finanzas.relacion_retenciones_orden ";
		$r = $conn->execute($q) or die($q);	
		
		#ELIMINO LOS REGISTROS DE PAGOS HECHOS EN EL ESCENARIO ACTUAL#
		$q = "DELETE FROM finanzas.cheques ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM finanzas.relacion_cheque ";
		$r = $conn->execute($q) or die($q);	
		
		$q = "DELETE FROM finanzas.otros_pagos ";
		$r = $conn->execute($q) or die($q);	
		
		$q = "DELETE FROM finanzas.relacion_otros_pagos";
		$r = $conn->execute($q) or die($q);	
		
		#ELIMINO LOS REGISTROS CONTABLES DEL ESCENARIO ACTUAL#
		/*$q = "DELETE FROM contabilidad.com_enc ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM contabilidad.com_det ";
		$r = $conn->execute($q) or die($q);	
		
		$q = "DELETE FROM contabilidad.relacion_cc_pp ";
		$r = $conn->execute($q) or die($q);	*/

		#ELIMINO LAS OBRAS CARGADAS EN FORMULACION EN EL MAESTRO DE OBRAS
		$q = "DELETE FROM puser.obras ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.relacion_obras ";
		$r = $conn->execute($q) or die($q);
		
		$q = "DELETE FROM puser.politicas_disposiciones ";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE RELACION_PP_CP DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.relacion_pp_cp WHERE id_escenario='$id_escenario'";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE RELACION_UE_CP DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.relacion_ue_cp WHERE id_escenario='$id_escenario'";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE CATEGORIAS PROGRAMATICAS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.categorias_programaticas WHERE id_escenario='$id_escenario'";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE PARTIDAS PRESUPUESTARIAS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.partidas_presupuestarias WHERE id_escenario='$id_escenario'";
		$r = $conn->execute($q) or die($q);
		
		#ELIMINO LOS REGISTROS DE UNIDADES EJECUTORAS DEL ESCENARIO ACTUAL#
		$q = "DELETE FROM puser.unidades_ejecutoras WHERE id_escenario='$id_escenario'";
		$r = $conn->execute($q) or die($q);
		//die('termino');
						
	}
	
	function actualizaEscenario($conn, $id_base, $ano, $factor){

		$oCatpro = new categorias_programaticas;
		$oParPre = new partidas_presupuestarias;
		$oParCat = new relacion_pp_cp;
		
		$catProByEsc = $oCatpro->get_all_by_esc($conn, $id_base);
		
		if(is_array($catProByEsc)){
		foreach($catProByEsc as $a){
					$oCatpro->add_esc($conn, 
													$a->id, 
													'1111', 
													$a->descripcion, 
													$a->os, 
													false, 
													$a->po, 
												0, 0, 0, 0, 0, 0,
													$ano,
													$a->dp);
					}
				}
		$parPreByEsc = $oParPre->get_all_by_esc($conn, $id_base);
		
				if(is_array($parPreByEsc)){
					foreach($parPreByEsc as $a){
						$oParPre->add_esc($conn, 
												$a->id, 
												'1111', 
												$a->descripcion, 
												$a->detalle, 
												$a->gastos_inv, 
												$a->id_contraloria, 
												$a->presupuesto_original, 
												0, 0, 0, 0, 0, 0,
												$ano);
					}
				}
				
				$oUndEje = new unidades_ejecutoras;
				$undEjeByEsc = $oUndEje->get_all_by_esc($conn, $id_base);
				if(is_array($undEjeByEsc)){
					foreach($undEjeByEsc as $a){
						$oUndEje->add($conn, 
												$a->id, 
												'1111', 
												$a->descripcion, 
												$a->responsable);
					}
				}
				
				$parCatByEsc = $oParCat->get_all_by_esc($conn, $id_base);
				if(is_array($parCatByEsc)){
					foreach($parCatByEsc as $a){
						$oParCat->add($conn, 
												'1111', 
												$a->id_categoria_programatica, 
												$a->id_partida_presupuestaria, 
												$a->id_asignacion, 
												$a->presupuesto_original * $factor, 
												0, 0, 0, 0, 0, 
												$a->presupuesto_original * $factor,
												$a->aingresos,
												$a->agastos,
												$ano);
					}
				}
				
				$oUndCat = new relacion_ue_cp;
				$undCatByEsc = $oUndCat->get_all_by_esc($conn, $id_base);
				
				if(is_array($undCatByEsc)){
					foreach($undCatByEsc as $a){
						$oUndCat->add($conn, 
												'1111', 
												$a->id_categoria_programatica, 
												$a->id_unidad_ejecutora, 
												$a->descripcion);
					}
				}
		
		
	}

	function buscar($conn, $id, $id_base, $ano, $descripcion, $orden="id"){
		if(empty($id) && empty($id_base) && empty($ano) && empty($descripcion))
			return false;
		$q = "SELECT * FROM escenarios ";
		$q.= "WHERE  1=1 ";
		$q.= !empty($id) ? "AND id = '$id'  ":"";
		$q.= !empty($id_base) ? "AND id_base = '$id_base'  ":"";
		$q.= !empty($ano) ? "AND ano = '$ano'  ":"";
		$q.= !empty($descripcion) ? "AND descripcion ILIKE '%$descripcion%'  ":"";
		$q.= "ORDER BY $orden ";
		//die($q);
		$r = ($max!=0) ? $conn->SelectLimit($q, $max, $from) : $conn->Execute($q);
		$collection=array();
		while(!$r->EOF){
			$ue = new escenarios;
			$ue->get($conn, $r->fields['id']);
			$coleccion[] = $ue;
			$r->movenext();
		}
		return $coleccion;
	}
	
	function validaCierreEscenario($conn){
		$q = "SELECT rpc.id_categoria_programatica||' - '||rpc.id_partida_presupuestaria AS idpp_cp, cp.descripcion||' - '||pp.descripcion AS nompp_cp ";
		$q.= "FROM puser.relacion_pp_cp rpc ";
		$q.= "INNER JOIN puser.partidas_presupuestarias pp ON (rpc.id_partida_presupuestaria = pp.id AND pp.id_escenario = 1111) ";
		$q.= "INNER JOIN puser.categorias_programaticas cp ON (rpc.id_categoria_programatica = cp.id AND cp.id_escenario = 1111) ";
		//$q.= "WHERE (rpc.compromisos < 0 OR rpc.causados < 0 OR rpc.pagados < 0 OR rpc.disponible < 0)  AND rpc.id_escenario = 1111 ";
		$q.= "WHERE (round(rpc.disponible) < 0)  AND rpc.id_escenario = 1111 ";
		$r = $conn->execute($q);
		$collection = array();
		while(!$r->EOF){
			$esc = new escenarios;
			$esc->codigo = $r->fields['idpp_cp'];
			$esc->partidas = $r->fields['nompp_cp'];
			$collection[] = $esc;
			$r->movenext();
		}
		return $collection;
	}
}
?>
