<?
// Librerias logicas
require ($appRoot . "/lib/unidades_ejecutoras.class.php");
require ($appRoot . "/lib/escenarios.class.php");
require ($appRoot . "/lib/politicas_disposiciones.class.php");
require ($appRoot . "/lib/gacetas.class.php");
require ($appRoot . "/lib/categorias_programaticas.class.php");
require ($appRoot . "/lib/relacion_ue_cp.class.php");
require ($appRoot . "/lib/relacion_pp_cp.class.php");
require ($appRoot . "/lib/partidas_presupuestarias.class.php");
require ($appRoot . "/lib/alcaldia.class.php");
require ($appRoot . "/lib/usuarios.class.php");
require ($appRoot . "/lib/profesiones.class.php");
require ($appRoot . "/lib/parroquias.class.php");
require ($appRoot . "/lib/organismos.class.php");
require ($appRoot . "/lib/financiamiento.class.php");
require ($appRoot . "/lib/momentos_presupuestarios.class.php");
require ($appRoot . "/lib/tipos_documentos.class.php");
require ($appRoot . "/lib/obras.class.php");
require ($appRoot . "/lib/requisitos.class.php");
require ($appRoot . "/lib/grupos_proveedores.class.php");
require ($appRoot . "/lib/proveedores.class.php");//se7h
require ($appRoot . "/lib/tipo_producto.class.php");//se7h
require ($appRoot . "/lib/productos.class.php");//se7h
require ($appRoot . "/lib/operaciones.class.php");
require ($appRoot . "/lib/movimientos_presupuestarios.class.php");
require ($appRoot . "/lib/nomina.class.php");
require ($appRoot . "/lib/ciudadanos.class.php");
require ($appRoot . "/lib/condicion_pago.class.php");
require ($appRoot . "/lib/modulos.class.php");
require ($appRoot . "/lib/estado.class.php"); //Ismael Depablos 20/11/06
require ($appRoot . "/lib/territorios.class.php"); //Ismael Depablos 20/11/06

//gestion presupuestaria
require ($appRoot . "/lib/formulacion.class.php");//fecs 27/09/06
require ($appRoot . "/lib/aprobacion_metas.class.php");//fecs 05/10/06
require ($appRoot . "/lib/entes.class.php");//iodg 05/11/08
require ($appRoot . "/lib/transferencias.class.php");//iodg 05/11/08

// gestion financiera
require ($appRoot . "/lib/ordcompra.class.php");//jmcc
require ($appRoot . "/lib/orden_servicio_trabajo.class.php");
require ($appRoot . "/lib/tipos_fianzas.class.php");
require ($appRoot . "/lib/contrato_obras.class.php");
require ($appRoot . "/lib/contrato_servicio.class.php");
require ($appRoot . "/lib/caja_chica.class.php");
require ($appRoot . "/lib/tipos_solicitudes.class.php");
require ($appRoot . "/lib/tipos_solicitud_sin_imp.class.php");
require ($appRoot . "/lib/condiciones_pago.class.php");
require ($appRoot . "/lib/entidades.class.php");
require ($appRoot . "/lib/retenciones_adiciones.class.php");
require ($appRoot . "/lib/solicitud_pago.class.php");
require ($appRoot . "/lib/tipo_movimiento.class.php");
require ($appRoot . "/lib/clasificacion_cuenta.class.php");
require ($appRoot . "/lib/iva_retenciones.class.php");
require ($appRoot . "/lib/motivo_anulacion_orden_pago.class.php");
require ($appRoot . "/lib/iva.class.php");
require ($appRoot . "/lib/tipos_cuentas_bancarias.class.php");
require ($appRoot . "/lib/cuentas_bancarias.class.php");
require ($appRoot . "/lib/control_chequera.class.php");
require ($appRoot . "/lib/orden_pago.class.php");
require ($appRoot . "/lib/cheque.class.php");
require ($appRoot . "/lib/otros_pagos.class.php");
require ($appRoot . "/lib/ayudas.class.php");
require ($appRoot . "/lib/documentos_generales.class.php");
require ($appRoot . "/lib/cheque_anteriores.class.php");
require ($appRoot . "/lib/otros_pagos_anteriores.class.php");
require ($appRoot . "/lib/transferencia.class.php");//Deivis Laya 02/07/2008
require ($appRoot . "/lib/traFondosTerceros.class.php");// Ismael D. 02/07/2008
//vehiculo
require ($appRoot . "/lib/vehiculo.class.php");//se7h:20/03/2006
require ($appRoot . "/lib/contribuyente.class.php");//se7h:21/03/2006
require ($appRoot . "/lib/tasa_inscripcion.class.php");//se7h:24/03/2006
require ($appRoot . "/lib/costo_calcomania.class.php");//se7h:24/03/2006
require ($appRoot . "/lib/forma_pago.class.php");//se7h:27/03/2006
require ($appRoot . "/lib/tipo_transaccion.class.php");//se7h:27/03/2006
require ($appRoot . "/lib/ramo_transaccion.class.php");//se7h:28/03/2006
require ($appRoot . "/lib/solvencia.class.php"); // afab 09/05/06
require ($appRoot . "/lib/costo_vehiculo.class.php"); // afab 10/05/06
require ($appRoot . "/lib/liquidacion.class.php"); // afab 15/05/06  
require ($appRoot . "/lib/banco.class.php"); // afab 17/05/06  
require ($appRoot . "/lib/pago.vehiculo.class.php"); // afab 18/05/06
require ($appRoot . "/lib/desincorporacion.class.php"); // fecs 12/07/06 
require ($appRoot . "/lib/reincorporacion.class.php"); // fecs 12/07/06 
require ($appRoot . "/lib/veh_color.class.php");//24/08/2006 modificado JB
require ($appRoot . "/lib/veh_modelo.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/veh_marca.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/veh_tipo.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/veh_uso.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/veh_desincorporacion.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_exoneracion.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_linea.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_sanciones.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_tasabanco.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/veh_tvehgaceta.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/ramo_imp.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_tipocambio.class.php");//25/08/2006 modificado JB 
require ($appRoot . "/lib/veh_cambios.class.php");//25/08/2006 modificado JB
require ($appRoot . "/lib/municipios.class.php");//25/08/2006 modificado JB

//Publicidad y Propagandas
require ($appRoot . "/lib/publicidad.class.php");//28/08/2006 modificado JB  
require ($appRoot . "/lib/tasa_bancaria_publicidad.class.php");//28/08/2006 modificado JB
require ($appRoot . "/lib/costo_publicidad.class.php");//28/08/2006 modificado JB  
require ($appRoot . "/lib/costo_espectaculo.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/inspeccion.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/tasa_inscripcion_publicidad.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/unidad_tributaria.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/clasificacion_espectaculo.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/espectaculo.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/artsancion.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/insppblicidad.class.php");//28/08/2006 modificado JB
require ($appRoot . "/lib/pago_espectaculo.class.php");//28/08/2006 modificado JB 
require ($appRoot . "/lib/tipoentrada.class.php");//28/08/2006 modificado
require ($appRoot . "/lib/tipo_solicitud.class.php");//28/08/2006 modificado 
require ($appRoot . "/lib/requisitos_publicidad.class.php");//28/08/2006 modificado
require ($appRoot . "/lib/exoneracion_publicidad.class.php");//28/08/2006 modificado


// CEPV.230606.SN Gestion Recursos Humanos
require ($appRoot . "/lib/variable.class.php");
require ($appRoot . "/lib/constante.class.php");
require ($appRoot . "/lib/grupoconceptos.class.php");
require ($appRoot . "/lib/division.class.php");
require ($appRoot . "/lib/cargo.class.php");
require ($appRoot . "/lib/empresa.class.php");
require ($appRoot . "/lib/departamento.class.php");
require ($appRoot . "/lib/contrato.class.php");
require ($appRoot . "/lib/trabajador.class.php");
require ($appRoot . "/lib/concepto.class.php");
require ($appRoot . "/lib/prestamo.class.php");
require ($appRoot . "/lib/funcion.class.php");
require ($appRoot . "/lib/relacion_conc_pp.class.php");

// CEPV.230606.EN

//licores
require ($appRoot . "/lib/tasas.class.php");
require ($appRoot . "/lib/multa.class.php");
require ($appRoot . "/lib/solicitud.class.php");
require ($appRoot . "/lib/expendios.class.php");
require ($appRoot . "/lib/requisitos_licores.class.php");

#CONTABILIDAD#
require ($appRoot . "/lib/plan_cuenta.class.php");
require ($appRoot . "/lib/relacion_cc_pp.class.php");
require ($appRoot . "/lib/comprobante.class.php");
require ($appRoot . "/lib/comprobante_det.class.php");
require ($appRoot . "/lib/conciliacionBancaria.class.php");
require ($appRoot . "/lib/estado_cuenta.class.php");
require ($appRoot . "/lib/conciliacionBancaria2.class.php"); //PAra pruebas de modulo de conciliacion nuevo

// Librerias de interfaz grafica
require ($appRoot . "/lib/validator.class.php");
require ($appRoot . "/lib/paginator.class.php");
require ($appRoot . "/lib/helpers.class.php");
require ($appRoot . "/lib/select.class.php");
require ($appRoot . "/lib/menu.class.php");

// Librerias externas
require ($appRoot . "/lib/fpdf.php"); // Creacion de achivos pdf
require ($appRoot . "/lib/JSON.php"); // clases de manipulacion de data - JavaScript Object Notation
//require ($appRoot . "/lib/ExcelClasses/PHPExcel.php"); //Creacion de archivos exel - xlsx
//require ($appRoot . "/lib/ExcelClasses/PHPExcel/IOFactory.php"); //Creacion de archivos exel - xlsx

// Errores y avisos
require ($appRoot . "/lib/dic.cons.php");

// funciones varias
require ($appRoot . "/lib/funciones.php");

//Compras y Almacen
require ($appRoot . "/lib/requisiciones.class.php");
require ($appRoot . "/lib/revision_requisicion.class.php");
require ($appRoot . "/lib/requisicion_global.class.php");
require ($appRoot . "/lib/actualiza_cotizacion.class.php");
require ($appRoot . "/lib/analisis_cotizacion.class.php");
require ($appRoot . "/lib/recepcion_orden_compra.class.php");
require ($appRoot . "/lib/unidad_medida.class.php");
require ($appRoot . "/lib/clasificacion_bienes.class.php");

?>
