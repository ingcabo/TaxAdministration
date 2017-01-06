<?
define("REG_ADD_OK", "Registro insertado con &eacute;xito");
define("REG_SET_OK", "Registro actualizado con &eacute;xito");
define("REG_DEL_OK", "Registro eliminado con &eacute;xito");
define("OK", "Operacion Realizada con Exito");
define("ERROR", "Ha ocurrido un error");
define("ERROR_ADD", "Ha ocurrido un error en el proceso de inseci&oacute;n de registro");
define("ERROR_SET", "Ha ocurrido un error actualizando el registro");
define("ERROR_DEL", "Ha ocurrido un error eliminando el registro");
define("ESC_APR", "Escenario aprobado con &eacute;xito");
define("DUPLICATED", 'Ha intentado insertar un registro existente');//se7h
define("ORDEN_APROBADA", 'Orden Aprobada con &eacute;xito');
define("ORDEN_COMPRA_ANULADA", "Orden anulada con &eacute;xito");
define("NOMINA_APROBADA", 'N&oacute;mina Aprobada con &eacute;xito');
define("CO_APROBADA", 'Contrato de Obras aprobado con &eacute;xito');
define("CS_APROBADA", 'Contrato de Servicio aprobado con &eacute;xito');
define("CC_APROBADA", 'Caja chica aprobada con &eacute;xito');
define("AY_APROBADA", 'Ayuda aprobada con &eacute;xito');
define("DG_APROBADA", 'Documento General aprobado con &eacute;xito');
define("ERROR_ORDEN_APR", "Ha ocurrido un error en la aprobaci&oacute;n de la orden");
define("ERROR_NOMINA_APR", "Ha ocurrido un error en la aprobaci&oacute;n de la n&oacute;mina");
define("ERROR_CO_APR", "Ha ocurrido un error en la aprobaci&oacute;n del contrato de obras");
define("ERROR_CS_APR", "Ha ocurrido un error en la aprobaci&oacute;n del contrato de servicio");
define("ERROR_CC_APR", "Ha ocurrido un error en la aprobaci&oacute;n de la caja chica");
define("ERROR_ORDEN_APR_NO_DISP", "No es posible aprobar la orden debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_ORDEN_APR_NO_FIANZA", "No es posible aprobar la orden debido a que no ha introducido fianza");
define("ERROR_NOMINA_APR_NO_DISP", "No es posible aprobar la n&oacute;mina debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_CO_APR_NO_DISP", "No es posible realizar la transacci&oacute;n debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_CS_APR_NO_DISP", "No es posible aprobar el contrato de servicio debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_CC_APR_NO_DISP", "No es posible aprobar caja chica debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_AY_APR_NO_DISP", "No es posible aprobar el documento de ayuda debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
define("ERROR_DG_APR_NO_DISP", "No es posible aprobar el documento general debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s)");
// CEPV.230606.SN
define("ERROR_CATCH_VFK", "ERROR... Posible Causa: 'Violacion de integridad referencial'");
define("ERROR_CATCH_VUK", "ERROR... Posible causa: 'El codigo ya existe'");
define("ERROR_CATCH_RELVUK", "ERROR... Posible causa: 'Relaci&oacute;n ya existente'");
define("ERROR_CATCH_GENERICO", "ERROR... No se pudo realizar la operacion");
define("ERROR_SOLICITUD_PAGO_APR_NO_DISP","No es posible aprobar la solicitud de Pago debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s) ");
define("ERROR_ORDEN_PAGO_APR_NO_DISP","No es posible aprobar la Orden de Pago debido a que actualmente no hay disponibilidad en la(s) partida(s) seleccionada(s) ");
define("SOLICITUD_PAGO_APROBADA","Solicitud de pago aprobada con &eacute;xito");
define("ORDEN_PAGO_APROBADA","Orden de pago aprobada con &eacute;xito");
// CEPV.230606.EN
define("CODIGO_YA_EXISTE","El c&oacute;digo ingresado ya existe, intente ingresar otro c&oacute;digo");
define("AGREGADA_PARAMETRIZACION_PARTIDA", "Esta partida ya esta parametrizada");
define("SOLICITUD_ANULADA", "La solicitud de pago anulada con &eacute;xito");
define("SOLICITUD_NO_ANULADA", "Error en el proceso la solicitud no se pudo anular");
define("ERROR_ORDEN_NO_APROBADA","Ocurrio un error al generar la orden de pago");
define("ORDEN_NO_ANULADA", "Error en el proceso la orden de pago no se pudo anular");
define("ORDEN_NO_APROBADA", "Error en el proceso la orden de pago no se pudo aprobar");
define("ORDEN_ANULADA", "Orden de pago anulada con &eacute;xito");
define("ESCENARIO_APROBADO", "Escenario aprobado con &eacute;xito");
define("ENTIDAD_DUPLICADA", "Ya existe un registro con el mismo nombre");
define("REGISTROS_PAGINA", 25);
// IODG 01/03/07 manejo de anulaciones
define("AY_ANULADA", 'Ayuda Anulada con &eacute;xito');
define("CO_ANULADA", 'Contrato de Obras Anulado con &eacute;xito');
define("DG_ANULADA", 'Documento General Anulado con &eacute;xito');
define("CS_ANULADA", 'Contrato de Servicios Anulado con &eacute;xito');
define("OT_ANULADA", 'Orden de Trabajo Anulada con &eacute;xito');
define("OS_ANULADA", 'Orden de Servicio Anulada con &eacute;xito');
define("CC_ANULADA", 'Orden de Servicio Anulada con &eacute;xito');
define("OC_ANULADA", 'Orden de Servicio Anulada con &eacute;xito');
define("OC_APROBADA", 'Orden de Compra Aprobada con &eacute;xito');
define("RQ_ANULADA", 'Requisici&oacute;n Anulada con &eacute;xito');
define("RQ_APROBADA", 'Requisici&oacute;n Aprobada con &eacute;xito');
define("ERROR_OBRA_VFK", 'No se puede eliminar la obra ya que existe un Contrato de Obras relacionado'); 
define("ERROR_PROVEEDOR_VFK", 'No se puede eliminar el Proveedor ya que existe(n) Documento(s) relacionado(s) a el');
?>
