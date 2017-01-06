--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: finanzas; Type: SCHEMA; Schema: -; Owner: puser
--

CREATE SCHEMA finanzas;


ALTER SCHEMA finanzas OWNER TO puser;

--
-- Name: SCHEMA finanzas; Type: COMMENT; Schema: -; Owner: puser
--

COMMENT ON SCHEMA finanzas IS 'esquema para la gestión finaciera';


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- Name: publicidad; Type: SCHEMA; Schema: -; Owner: puser
--

CREATE SCHEMA publicidad;


ALTER SCHEMA publicidad OWNER TO puser;

--
-- Name: SCHEMA publicidad; Type: COMMENT; Schema: -; Owner: puser
--

COMMENT ON SCHEMA publicidad IS 'Esquema para Publicidad y Propaganda';


--
-- Name: puser; Type: SCHEMA; Schema: -; Owner: puser
--

CREATE SCHEMA puser;


ALTER SCHEMA puser OWNER TO puser;

--
-- Name: SCHEMA puser; Type: COMMENT; Schema: -; Owner: puser
--

COMMENT ON SCHEMA puser IS 'esquema para el usuario puser';


--
-- Name: vehiculo; Type: SCHEMA; Schema: -; Owner: puser
--

CREATE SCHEMA vehiculo;


ALTER SCHEMA vehiculo OWNER TO puser;

--
-- Name: SCHEMA vehiculo; Type: COMMENT; Schema: -; Owner: puser
--

COMMENT ON SCHEMA vehiculo IS 'esquema para el modulo de vehiculo. by se7h';


SET search_path = finanzas, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: condiciones_pago; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE condiciones_pago (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE finanzas.condiciones_pago OWNER TO puser;

--
-- Name: relacion_retenciones_solicitud; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_retenciones_solicitud (
    id serial NOT NULL,
    idsolic character varying(10) NOT NULL,
    codret character varying(3) NOT NULL,
    mntret numeric(18,2) NOT NULL,
    mntbas numeric(18,2) NOT NULL,
    porcen numeric(18,2) NOT NULL,
    anio character varying(4) NOT NULL
);


ALTER TABLE finanzas.relacion_retenciones_solicitud OWNER TO puser;

--
-- Name: TABLE relacion_retenciones_solicitud; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON TABLE relacion_retenciones_solicitud IS 'guarda el detalle de las retenciones en las solicitudes de pago';


--
-- Name: relacion_retenciones_solicitud_id_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_retenciones_solicitud', 'id'), 1, false);


--
-- Name: retenciones_adiciones; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE retenciones_adiciones (
    id character varying(10) NOT NULL,
    abrevi character varying(10),
    descri character varying(100),
    codcta character varying(14),
    condic character varying(4000),
    porcen numeric,
    sustra numeric,
    tipofv character varying(1),
    tipper character varying(1),
    ctapre character varying(23)
);


ALTER TABLE finanzas.retenciones_adiciones OWNER TO puser;

--
-- Name: TABLE retenciones_adiciones; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON TABLE retenciones_adiciones IS 'retenciones y adiciones';


--
-- Name: COLUMN retenciones_adiciones.abrevi; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.abrevi IS 'abreviatura';


--
-- Name: COLUMN retenciones_adiciones.descri; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.descri IS 'descripcion';


--
-- Name: COLUMN retenciones_adiciones.codcta; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.codcta IS 'codigo cuenta contable';


--
-- Name: COLUMN retenciones_adiciones.condic; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.condic IS 'condicion';


--
-- Name: COLUMN retenciones_adiciones.porcen; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.porcen IS 'porcentaje';


--
-- Name: COLUMN retenciones_adiciones.sustra; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.sustra IS 'sustraendo';


--
-- Name: COLUMN retenciones_adiciones.tipofv; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.tipofv IS 'fija o variable';


--
-- Name: COLUMN retenciones_adiciones.tipper; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.tipper IS 'tipo persona J/N/A';


--
-- Name: COLUMN retenciones_adiciones.ctapre; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN retenciones_adiciones.ctapre IS 'cuenta presupuestaria';


--
-- Name: solicitud_pago; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE solicitud_pago (
    nrodoc character varying NOT NULL,
    nroref character varying NOT NULL,
    fecha date NOT NULL,
    status integer NOT NULL,
    id_condicion_pago character varying NOT NULL,
    id_tipo_solicitud character varying NOT NULL,
    id_tipo_solicitud_si character varying NOT NULL,
    monto_si integer
);


ALTER TABLE finanzas.solicitud_pago OWNER TO puser;

--
-- Name: TABLE solicitud_pago; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON TABLE solicitud_pago IS 'solicitudes de pago';


--
-- Name: COLUMN solicitud_pago.id_tipo_solicitud_si; Type: COMMENT; Schema: finanzas; Owner: puser
--

COMMENT ON COLUMN solicitud_pago.id_tipo_solicitud_si IS 'sin imputación presupuestaria';


--
-- Name: tipos_solicitud_sin_imp; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE tipos_solicitud_sin_imp (
    id character varying(10) NOT NULL,
    descripcion character varying,
    anio character varying(4) NOT NULL,
    cuenta_contable character varying(20) NOT NULL
);


ALTER TABLE finanzas.tipos_solicitud_sin_imp OWNER TO puser;

--
-- Name: tipos_solicitudes; Type: TABLE; Schema: finanzas; Owner: puser; Tablespace: 
--

CREATE TABLE tipos_solicitudes (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE finanzas.tipos_solicitudes OWNER TO puser;

SET search_path = publicidad, pg_catalog;

--
-- Name: base_calculo_pub; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE base_calculo_pub (
    id integer NOT NULL,
    vig_desde date,
    vig_hasta character varying,
    recargo double precision,
    und_tarifa character varying(1),
    desc_tipo character varying,
    aplica_desde integer,
    aplica_hasta integer,
    art_ref character varying,
    anio integer
);


ALTER TABLE publicidad.base_calculo_pub OWNER TO puser;

--
-- Name: clasificacion; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE clasificacion (
    cod_cla serial NOT NULL,
    tipo character varying,
    descripcion text,
    status character varying
);


ALTER TABLE publicidad.clasificacion OWNER TO puser;

--
-- Name: TABLE clasificacion; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE clasificacion IS 'Tabla que almacena el tipo de clasifiacion de los espectaculos.';


--
-- Name: clasificacion_cod_cla_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('clasificacion', 'cod_cla'), 3, true);


--
-- Name: entradas; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE entradas (
    id serial NOT NULL,
    descripcion character varying,
    max_per integer,
    tiene_precio character varying,
    imp_exon character varying,
    aforo double precision
);


ALTER TABLE publicidad.entradas OWNER TO puser;

--
-- Name: TABLE entradas; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE entradas IS 'Tabla donde se cargan la descripcion de las entradas a ofrecer en el evento.';


--
-- Name: entradas_cod_ent_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('entradas', 'id'), 3, true);


--
-- Name: esp_costo_pub; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE esp_costo_pub (
    id serial NOT NULL,
    id_publicidad integer,
    monto double precision,
    fecha_desde date,
    fecha_hasta date
);


ALTER TABLE publicidad.esp_costo_pub OWNER TO puser;

--
-- Name: esp_costo_pub_id_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('esp_costo_pub', 'id'), 1, false);


--
-- Name: espectaculo; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE espectaculo (
    id serial NOT NULL,
    descripcion character varying NOT NULL,
    status integer NOT NULL,
    aforo double precision NOT NULL,
    paforo integer NOT NULL
);


ALTER TABLE publicidad.espectaculo OWNER TO puser;

--
-- Name: TABLE espectaculo; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE espectaculo IS 'Tabla donde salen todos los tipos de espectaculos.';


--
-- Name: espectaculo_cod_esp_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('espectaculo', 'id'), 3, true);


--
-- Name: inspector; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE inspector (
    cod_ins serial NOT NULL,
    nombre character varying,
    apellido character varying,
    status integer
);


ALTER TABLE publicidad.inspector OWNER TO puser;

--
-- Name: TABLE inspector; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE inspector IS 'Tabla de inspectores a hacer las medidas.';


--
-- Name: inspector_cod_ins_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('inspector', 'cod_ins'), 2, true);


--
-- Name: multa; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE multa (
    cod_mul serial NOT NULL,
    descripcion character varying,
    status integer,
    monto double precision,
    indic character varying
);


ALTER TABLE publicidad.multa OWNER TO puser;

--
-- Name: TABLE multa; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE multa IS 'Tabla donde cargo todas las multas disponibles';


--
-- Name: multas_id_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('multa', 'cod_mul'), 3, true);


--
-- Name: propaganda; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE propaganda (
    id serial NOT NULL,
    descripcion character varying,
    aforo double precision,
    status integer
);


ALTER TABLE publicidad.propaganda OWNER TO puser;

--
-- Name: TABLE propaganda; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE propaganda IS 'Tabla que carga todas las propagandas con su respectivo monto';


--
-- Name: propaganda_cod_pro_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('propaganda', 'id'), 4, true);


--
-- Name: publicidad; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE publicidad (
    id serial NOT NULL,
    fecha date,
    patente character varying,
    id_contribuyente integer,
    id_solicitud character varying(10),
    anio_pago integer,
    hastaanio integer
);


ALTER TABLE publicidad.publicidad OWNER TO puser;

--
-- Name: TABLE publicidad; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE publicidad IS 'Tabla donde guardo todos los registros de publicidad fija y eventual';


--
-- Name: publicidad_id_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('publicidad', 'id'), 217, true);


--
-- Name: relacion_publicidad_espectaculo; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_publicidad_espectaculo (
    id serial NOT NULL,
    fec_ini date,
    hor_ini character varying,
    fec_fin date,
    hor_fin character varying,
    aforo double precision,
    total_esp double precision,
    id_espectaculo integer,
    id_entrada integer,
    cant4 integer,
    id_publicidad integer,
    aforo1 double precision,
    paforo character varying
);


ALTER TABLE publicidad.relacion_publicidad_espectaculo OWNER TO puser;

--
-- Name: TABLE relacion_publicidad_espectaculo; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE relacion_publicidad_espectaculo IS 'Tabla que relaciona el maestro de publicidad con los espectaculos';


--
-- Name: relacion_publicidad_espectaculo_id_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_publicidad_espectaculo', 'id'), 62, true);


--
-- Name: relacion_publicidad_publicidades; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_publicidad_publicidades (
    id serial NOT NULL,
    cant1 character varying,
    unid1 character varying,
    cant2 character varying,
    unid2 character varying,
    cant3 character varying,
    unid3 character varying,
    total_medida character varying,
    total_publicidad character varying,
    aforo double precision,
    id_publicidad integer,
    id_propaganda integer
);


ALTER TABLE publicidad.relacion_publicidad_publicidades OWNER TO puser;

--
-- Name: TABLE relacion_publicidad_publicidades; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE relacion_publicidad_publicidades IS 'Tabla que relaciona las publicidades disponibles con el maestro de publicidades';


--
-- Name: COLUMN relacion_publicidad_publicidades.id_publicidad; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON COLUMN relacion_publicidad_publicidades.id_publicidad IS 'Guarda el id de la orden de publicidad q la solicito';


--
-- Name: COLUMN relacion_publicidad_publicidades.id_propaganda; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON COLUMN relacion_publicidad_publicidades.id_propaganda IS 'Trae el id de la prop fija o eventual';


--
-- Name: relacion_publicidad_publicidades_id_seq; Type: SEQUENCE SET; Schema: publicidad; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_publicidad_publicidades', 'id'), 59, true);


--
-- Name: tipo_orden; Type: TABLE; Schema: publicidad; Owner: puser; Tablespace: 
--

CREATE TABLE tipo_orden (
    id character varying(10),
    descripcion character varying,
    abreviacion character varying(2)
);


ALTER TABLE publicidad.tipo_orden OWNER TO puser;

--
-- Name: TABLE tipo_orden; Type: COMMENT; Schema: publicidad; Owner: puser
--

COMMENT ON TABLE tipo_orden IS 'almacena los tipos de ordenes, fija, eventual o espectaculos';


SET search_path = puser, pg_catalog;

--
-- Name: activo_inactivo_producto; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE activo_inactivo_producto (
    id serial NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.activo_inactivo_producto OWNER TO puser;

--
-- Name: TABLE activo_inactivo_producto; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE activo_inactivo_producto IS 'valor A o I, activo o inactivo, de productos, la relaciono con id_activo_inactivo_producto, de la tabla porductos';


--
-- Name: activo_inactivo_producto_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('activo_inactivo_producto', 'id'), 2, true);


--
-- Name: alcaldia; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE alcaldia (
    id character varying(10) NOT NULL,
    descripcion character varying,
    razon character varying,
    domicilio character varying,
    fecha_creacion date,
    ciudad character varying,
    estado character varying,
    telefono character varying,
    fax character varying,
    web_site character varying,
    alcalde character varying,
    personal text,
    concejales text,
    cpostal character varying
);


ALTER TABLE puser.alcaldia OWNER TO puser;

--
-- Name: TABLE alcaldia; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE alcaldia IS 'Almacena los datos de la institucion';


--
-- Name: COLUMN alcaldia.razon; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN alcaldia.razon IS 'razon social';


--
-- Name: COLUMN alcaldia.domicilio; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN alcaldia.domicilio IS 'domicilio legal';


--
-- Name: COLUMN alcaldia.web_site; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN alcaldia.web_site IS 'pagina web';


--
-- Name: COLUMN alcaldia.alcalde; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN alcaldia.alcalde IS 'nombre del alcalde';


--
-- Name: COLUMN alcaldia.cpostal; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN alcaldia.cpostal IS 'codigo postal';


--
-- Name: asignaciones; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE asignaciones (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.asignaciones OWNER TO puser;

--
-- Name: caja_chica_id_seq; Type: SEQUENCE; Schema: puser; Owner: puser
--

CREATE SEQUENCE caja_chica_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE puser.caja_chica_id_seq OWNER TO puser;

--
-- Name: caja_chica_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval('caja_chica_id_seq', 3, true);


--
-- Name: caja_chica; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE caja_chica (
    id integer DEFAULT nextval('caja_chica_id_seq'::regclass) NOT NULL,
    id_tipo_documento character varying NOT NULL,
    id_unidad_ejecutora character varying NOT NULL,
    id_ciudadano integer NOT NULL,
    id_usuario character varying NOT NULL,
    descripcion character varying NOT NULL,
    fecha date NOT NULL,
    fecha_aprobacion date,
    nrodoc character varying,
    observaciones character varying
);


ALTER TABLE puser.caja_chica OWNER TO puser;

--
-- Name: cargos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE cargos (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.cargos OWNER TO puser;

--
-- Name: categorias_programaticas; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE categorias_programaticas (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    objetivo_sectorial text,
    destinada_programa_social boolean,
    ano character(4) NOT NULL,
    presupuesto_original double precision,
    causados double precision,
    pagados double precision,
    disponible double precision,
    aumentos double precision,
    disminuciones double precision,
    compromisos double precision
);


ALTER TABLE puser.categorias_programaticas OWNER TO puser;

--
-- Name: TABLE categorias_programaticas; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE categorias_programaticas IS 'Almacena las categorias programaticas para cada una de las unidades del organismo con su descripcion, definir el objetivo, asignar el presupuesto original por cada una, indicar si dicha categoria esta asociada a algun programa social o no';


--
-- Name: categorias_programaticas_hist; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE categorias_programaticas_hist (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    objetivo_sectorial text,
    destinada_programa_social boolean,
    ano character(4) NOT NULL,
    presupuesto_original double precision,
    causados double precision,
    pagados double precision,
    disponible double precision,
    aumentos double precision,
    disminuciones double precision,
    compromisos double precision
);


ALTER TABLE puser.categorias_programaticas_hist OWNER TO puser;

--
-- Name: TABLE categorias_programaticas_hist; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE categorias_programaticas_hist IS 'Almacena las categorias programaticas para cada una de las unidades del organismo con su descripcion, definir el objetivo, asignar el presupuesto original por cada una, indicar si dicha categoria esta asociada a algun programa social o no';


--
-- Name: ciudadanos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE ciudadanos (
    id character varying NOT NULL,
    nombre character varying,
    tlf character varying,
    direccion character varying
);


ALTER TABLE puser.ciudadanos OWNER TO puser;

--
-- Name: co_requis; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE co_requis (
    id serial NOT NULL,
    descripcion character varying,
    obs character varying,
    fecha character varying,
    tip_sol serial NOT NULL
);


ALTER TABLE puser.co_requis OWNER TO puser;

--
-- Name: TABLE co_requis; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE co_requis IS 'codigo de requisito, para relacionarla con co_rtpro';


--
-- Name: co_requis_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('co_requis', 'id'), 1, false);


--
-- Name: co_requis_tip_sol_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('co_requis', 'tip_sol'), 1, false);


--
-- Name: co_rtpro; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE co_rtpro (
    id serial NOT NULL,
    id_grupo_proveedores integer,
    id_requisitos integer,
    fecha character varying
);


ALTER TABLE puser.co_rtpro OWNER TO puser;

--
-- Name: TABLE co_rtpro; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE co_rtpro IS 'relación grupo proveedor con requisitos (id_co_requis)';


--
-- Name: co_rtpro_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('co_rtpro', 'id'), 6, true);


--
-- Name: condicion_pago; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE condicion_pago (
    id character varying NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.condicion_pago OWNER TO puser;

--
-- Name: TABLE condicion_pago; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE condicion_pago IS 'almacena las condiciones de pago que se seleccionan en Orden de Servicio/Trabajo';


--
-- Name: contrato_obras; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE contrato_obras (
    id serial NOT NULL,
    id_tipo_documento character varying NOT NULL,
    id_unidad_ejecutora character varying NOT NULL,
    id_proveedor integer NOT NULL,
    id_obra character varying NOT NULL,
    id_usuario character varying NOT NULL,
    descripcion character varying NOT NULL,
    fecha date NOT NULL,
    fecha_aprobacion date,
    nrodoc character varying,
    id_tipo_fianza character varying NOT NULL,
    observaciones character varying
);


ALTER TABLE puser.contrato_obras OWNER TO puser;

--
-- Name: contrato_obras_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('contrato_obras', 'id'), 3, true);


--
-- Name: contrato_servicio_id_seq; Type: SEQUENCE; Schema: puser; Owner: puser
--

CREATE SEQUENCE contrato_servicio_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE puser.contrato_servicio_id_seq OWNER TO puser;

--
-- Name: contrato_servicio_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval('contrato_servicio_id_seq', 3, true);


--
-- Name: contrato_servicio; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE contrato_servicio (
    id integer DEFAULT nextval('contrato_servicio_id_seq'::regclass) NOT NULL,
    id_tipo_documento character varying NOT NULL,
    id_unidad_ejecutora character varying NOT NULL,
    id_proveedor integer NOT NULL,
    id_servicio character varying NOT NULL,
    id_usuario character varying NOT NULL,
    descripcion character varying NOT NULL,
    fecha date NOT NULL,
    fecha_aprobacion date,
    nrodoc character varying,
    observaciones character varying
);


ALTER TABLE puser.contrato_servicio OWNER TO puser;

--
-- Name: entidades; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE entidades (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.entidades OWNER TO puser;

--
-- Name: escenarios; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE escenarios (
    id character varying(10) NOT NULL,
    id_base character varying(10),
    ano character(4),
    descripcion character varying,
    detalle text,
    factor real,
    formulacion boolean,
    aprobado boolean DEFAULT false NOT NULL
);


ALTER TABLE puser.escenarios OWNER TO puser;

--
-- Name: TABLE escenarios; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE escenarios IS 'almacena las unidades ejecutoras';


--
-- Name: escenarios_hist; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE escenarios_hist (
    id character varying(10) NOT NULL,
    id_base character varying(10),
    ano character(4),
    descripcion character varying,
    detalle text,
    factor real,
    formulacion boolean,
    aprobado boolean DEFAULT false NOT NULL
);


ALTER TABLE puser.escenarios_hist OWNER TO puser;

--
-- Name: TABLE escenarios_hist; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE escenarios_hist IS 'almacena las unidades ejecutoras';


--
-- Name: estado; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE estado (
    id serial NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.estado OWNER TO puser;

--
-- Name: TABLE estado; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE estado IS 'estados';


--
-- Name: estado_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('estado', 'id'), 2, true);


--
-- Name: financiamiento; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE financiamiento (
    descripcion character varying,
    genera_retenciones boolean,
    genera_pagos_parciales boolean,
    id character varying(10) NOT NULL
);


ALTER TABLE puser.financiamiento OWNER TO puser;

--
-- Name: TABLE financiamiento; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE financiamiento IS 'fuentes de financiamiento';


--
-- Name: gacetas; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE gacetas (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.gacetas OWNER TO puser;

--
-- Name: grupos_proveedores; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE grupos_proveedores (
    id character varying(10) NOT NULL,
    nombre character varying NOT NULL,
    descripcion character varying NOT NULL,
    fecha character varying
);


ALTER TABLE puser.grupos_proveedores OWNER TO puser;

--
-- Name: id_grupo_prove; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE id_grupo_prove (
    id serial NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.id_grupo_prove OWNER TO puser;

--
-- Name: TABLE id_grupo_prove; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE id_grupo_prove IS 'grupo de proveedor';


--
-- Name: id_grupo_prove_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('id_grupo_prove', 'id'), 3, true);


--
-- Name: manejo_almacen_producto; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE manejo_almacen_producto (
    id serial NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.manejo_almacen_producto OWNER TO puser;

--
-- Name: TABLE manejo_almacen_producto; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE manejo_almacen_producto IS 'S o N, asumo si o no! ;)';


--
-- Name: manejo_almacen_producto_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('manejo_almacen_producto', 'id'), 2, true);


--
-- Name: modulos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE modulos (
    id serial NOT NULL,
    descripcion character varying NOT NULL,
    orden integer
);


ALTER TABLE puser.modulos OWNER TO puser;

--
-- Name: TABLE modulos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE modulos IS 'modulos del sistema';


--
-- Name: modulos_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('modulos', 'id'), 7, true);


--
-- Name: momentos_presupuestarios; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE momentos_presupuestarios (
    id character varying(10) NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.momentos_presupuestarios OWNER TO puser;

--
-- Name: TABLE momentos_presupuestarios; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE momentos_presupuestarios IS 'almacena los registros de momentos presupuestarios utilizados en la pantalla de tipos de documentos';


--
-- Name: movimientos_presupuestarios; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE movimientos_presupuestarios (
    id serial NOT NULL,
    nrodoc character varying(21) NOT NULL,
    tipdoc character varying(3) NOT NULL,
    tipref character varying(3) DEFAULT 0 NOT NULL,
    nroref character varying(30) DEFAULT 0 NOT NULL,
    fechadoc date,
    descripcion character varying(500),
    status character(1),
    id_unidad_ejecutora character varying(10),
    ano character varying(4) NOT NULL,
    id_usuario character varying(10),
    fecharef character varying,
    id_ciudadano character varying(10),
    id_proveedor integer
);


ALTER TABLE puser.movimientos_presupuestarios OWNER TO puser;

--
-- Name: COLUMN movimientos_presupuestarios.nrodoc; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.nrodoc IS 'numero de documento';


--
-- Name: COLUMN movimientos_presupuestarios.tipdoc; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.tipdoc IS 'tipo de documento';


--
-- Name: COLUMN movimientos_presupuestarios.tipref; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.tipref IS 'tipo de referencia';


--
-- Name: COLUMN movimientos_presupuestarios.nroref; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.nroref IS 'numero de referencia';


--
-- Name: COLUMN movimientos_presupuestarios.id_unidad_ejecutora; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.id_unidad_ejecutora IS 'unidad ejecutora';


--
-- Name: COLUMN movimientos_presupuestarios.ano; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.ano IS 'año del presupuesto';


--
-- Name: COLUMN movimientos_presupuestarios.id_usuario; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN movimientos_presupuestarios.id_usuario IS 'usuario que crea el movimiento presupuestario';


--
-- Name: movimientos_presupuestarios_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('movimientos_presupuestarios', 'id'), 188, true);


--
-- Name: municipios; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE municipios (
    id serial NOT NULL,
    id_estado integer NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.municipios OWNER TO puser;

--
-- Name: TABLE municipios; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE municipios IS 'municipios de estados';


--
-- Name: municipios_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('municipios', 'id'), 4, true);


--
-- Name: nomina; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE nomina (
    id serial NOT NULL,
    id_tipo_documento character varying,
    fecha date,
    nrodoc character varying,
    descripcion character varying,
    fecha_pago date,
    id_usuario character varying,
    id_unidad_ejecutora character varying,
    rif character varying,
    fecha_aprobacion date,
    nro_ref character varying,
    observaciones character varying,
    id_proveedor integer
);


ALTER TABLE puser.nomina OWNER TO puser;

--
-- Name: nomina_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('nomina', 'id'), 8, true);


--
-- Name: objeto_empresa; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE objeto_empresa (
    id serial NOT NULL,
    id_proveedor integer,
    objeto_empresa character varying,
    fecha date
);


ALTER TABLE puser.objeto_empresa OWNER TO puser;

--
-- Name: TABLE objeto_empresa; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE objeto_empresa IS 'objeto de la empresa para el maestro de proveedores';


--
-- Name: objeto_empresa_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('objeto_empresa', 'id'), 23, true);


--
-- Name: obras; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE obras (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    id_unidad_ejecutora character varying(10) NOT NULL,
    id_parroquia character varying(10) NOT NULL,
    ctotal double precision,
    caa double precision,
    eaa double precision,
    epre double precision,
    inicio date,
    culminacion date,
    cav double precision,
    eav double precision,
    epos double precision,
    id_situacion integer,
    denominacion character varying,
    ano character(4),
    responsable character varying,
    id_financiamiento integer NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.obras OWNER TO puser;

--
-- Name: TABLE obras; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE obras IS 'almacena de manera detallada toda la informacion referente a obras, bajo el escenario indicado por la oficina de presupuesto';


--
-- Name: COLUMN obras.ctotal; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.ctotal IS 'costo total';


--
-- Name: COLUMN obras.caa; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.caa IS 'comprometido años anteriores';


--
-- Name: COLUMN obras.eaa; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.eaa IS 'ejecucion años anteriores';


--
-- Name: COLUMN obras.epre; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.epre IS 'estimado presupuestario';


--
-- Name: COLUMN obras.inicio; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.inicio IS 'fecha de inicio';


--
-- Name: COLUMN obras.culminacion; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.culminacion IS 'fecha de culminacion';


--
-- Name: COLUMN obras.cav; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.cav IS 'comprometido año vigente';


--
-- Name: COLUMN obras.eav; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.eav IS 'ejecucion año vigente';


--
-- Name: COLUMN obras.epos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.epos IS 'estimado posterior';


--
-- Name: COLUMN obras.responsable; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN obras.responsable IS 'funcionario responsable';


--
-- Name: operaciones; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE operaciones (
    id serial NOT NULL,
    descripcion character varying,
    pagina character varying,
    id_sistema character(1),
    orden integer,
    tipo character(1),
    nivel integer,
    id_padre integer,
    id_modulo integer
);


ALTER TABLE puser.operaciones OWNER TO puser;

--
-- Name: TABLE operaciones; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE operaciones IS 'almacena las operaciones que se le pueden asignar a los usuario';


--
-- Name: COLUMN operaciones.tipo; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN operaciones.tipo IS 'si su valor es ''V'', es tipo vinculo, si el valor es ''C'' es tipo Carpeta y debe contener operaciones hijas';


--
-- Name: COLUMN operaciones.id_padre; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN operaciones.id_padre IS 'se refiere a la carpeta padre de esta operacion (la operacion puede ser tambien una carpeta)';


--
-- Name: operaciones_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('operaciones', 'id'), 84, true);


--
-- Name: orden_compra; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE orden_compra (
    id serial NOT NULL,
    fecha date NOT NULL,
    ano integer NOT NULL,
    f_entrega date NOT NULL,
    l_entrega character varying NOT NULL,
    c_pago character varying NOT NULL,
    f_solicitud date NOT NULL,
    nrodoc character varying,
    observaciones text NOT NULL,
    rif character varying NOT NULL,
    id_unidad_ejecutora character varying NOT NULL,
    fecha_aprobacion date,
    nrosol character varying
);


ALTER TABLE puser.orden_compra OWNER TO puser;

--
-- Name: TABLE orden_compra; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE orden_compra IS 'Tabla que guarda las caracteristicas de las ordenes de compra';


--
-- Name: orden_compra_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('orden_compra', 'id'), 68, true);


--
-- Name: orden_servicio_trabajo; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE orden_servicio_trabajo (
    id serial NOT NULL,
    id_tipo_documento character varying(10) NOT NULL,
    id_unidad_ejecutora character varying(10) NOT NULL,
    fecha_entrega date,
    lugar_entrega character varying,
    condicion_pago character varying,
    rif character varying,
    observaciones character varying,
    nro_requisicion character varying,
    fecha_requisicion character varying,
    nro_cotizacion character varying,
    nro_factura character varying,
    fecha_factura character varying,
    condicion_operacion character varying,
    cod_contraloria character varying,
    fecha date,
    nrodoc character varying,
    id_ciudadano character varying,
    id_usuario character varying,
    fecha_aprobacion date,
    id_proveedor integer
);


ALTER TABLE puser.orden_servicio_trabajo OWNER TO puser;

--
-- Name: orden_servicio_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('orden_servicio_trabajo', 'id'), 34, true);


--
-- Name: organismos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE organismos (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.organismos OWNER TO puser;

--
-- Name: parroquias; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE parroquias (
    descripcion character varying,
    id character varying(10) NOT NULL,
    id_municipio character varying
);


ALTER TABLE puser.parroquias OWNER TO puser;

--
-- Name: TABLE parroquias; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE parroquias IS 'info de parroquias';


--
-- Name: partidas_presupuestarias; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE partidas_presupuestarias (
    id character varying(13) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    detalle text,
    gastos_inv boolean,
    id_contraloria character varying(10),
    presupuesto_original double precision,
    aumentos double precision,
    disminuciones double precision,
    compromisos double precision,
    causados double precision,
    pagados double precision,
    disponible double precision,
    ano character(4)
);


ALTER TABLE puser.partidas_presupuestarias OWNER TO puser;

--
-- Name: TABLE partidas_presupuestarias; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE partidas_presupuestarias IS 'almacena las partidas y las relaciona con un escenario';


--
-- Name: politicas_disposiciones; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE politicas_disposiciones (
    id_escenario character varying(10),
    id_tipo_gaceta character varying(10),
    ano character(4),
    texto1 text,
    texto2 text,
    texto3 text,
    texto4 text,
    id serial NOT NULL
);


ALTER TABLE puser.politicas_disposiciones OWNER TO puser;

--
-- Name: TABLE politicas_disposiciones; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE politicas_disposiciones IS 'Carga informacion correspondiente a las politicas, disposiciones y presupuesto de ingresos y gastos del organismo, bajo el escenario indicado';


--
-- Name: politicas_disposiciones_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('politicas_disposiciones', 'id'), 20, true);


--
-- Name: productos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE productos (
    id serial NOT NULL,
    id_tipo_producto integer,
    descripcion character varying,
    tiempo_entrega character varying,
    garantia character varying,
    forma_pago integer,
    contrib character varying,
    unidad_medida character varying,
    rop character varying,
    roq character varying,
    ctd_minimo character varying,
    ctd_maximo character varying,
    ubic_fisica character varying,
    ctd_actual character varying,
    id_status_producto integer,
    id_activo_inactivo_producto integer,
    id_manejo_almacen_prodcuto integer,
    costo_std character varying,
    costo_prm character varying,
    ultimo_costo double precision,
    fecha date
);


ALTER TABLE puser.productos OWNER TO puser;

--
-- Name: TABLE productos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE productos IS 'descricpcion de productos... ';


--
-- Name: productos_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('productos', 'id'), 16, true);


--
-- Name: profesiones; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE profesiones (
    id character varying(10) NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.profesiones OWNER TO puser;

--
-- Name: provee_contrat; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE provee_contrat (
    id character varying NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.provee_contrat OWNER TO puser;

--
-- Name: TABLE provee_contrat; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE provee_contrat IS 'esto es si el proveedor es proveedor o contratista! ';


--
-- Name: provee_contrib_munic; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE provee_contrib_munic (
    id character varying,
    descripcion character varying
);


ALTER TABLE puser.provee_contrib_munic OWNER TO puser;

--
-- Name: TABLE provee_contrib_munic; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE provee_contrib_munic IS 'provee_contrib_munic?  del formulario de provvedores... (proveedores.php) Si o No ';


--
-- Name: proveedores; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE proveedores (
    nombre character varying NOT NULL,
    fecha character varying,
    rif_letra character varying NOT NULL,
    rif_numero character varying NOT NULL,
    rif_control character varying NOT NULL,
    nit character varying NOT NULL,
    nro_trabajadores character varying,
    direccion character varying NOT NULL,
    id_estado integer NOT NULL,
    id_municipio bigint NOT NULL,
    id_parroquia integer NOT NULL,
    provee_contrib_munic character varying NOT NULL,
    provee_contrat character varying NOT NULL,
    status character varying,
    datos_reg character varying,
    ci_representante character(20) NOT NULL,
    nombre_representante character varying,
    contacto character varying,
    accionistas character varying NOT NULL,
    junta_directiva character varying NOT NULL,
    telefono character varying NOT NULL,
    fax character varying,
    email character varying,
    ci_comisario character varying,
    nombre_comisario character varying,
    ci_responsable character varying,
    nombre_responsable character varying,
    cap_suscrito double precision,
    cap_pagado double precision,
    id_grupo_prove character varying NOT NULL,
    id serial NOT NULL,
    registro_const character varying,
    rif character varying NOT NULL
);


ALTER TABLE puser.proveedores OWNER TO puser;

--
-- Name: proveedores_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('proveedores', 'id'), 134, false);


--
-- Name: relacion_caja_chica_id_seq; Type: SEQUENCE; Schema: puser; Owner: puser
--

CREATE SEQUENCE relacion_caja_chica_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE puser.relacion_caja_chica_id_seq OWNER TO puser;

--
-- Name: relacion_caja_chica_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval('relacion_caja_chica_id_seq', 8, true);


--
-- Name: relacion_caja_chica; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_caja_chica (
    id integer DEFAULT nextval('relacion_caja_chica_id_seq'::regclass) NOT NULL,
    id_parcat integer,
    id_caja_chica character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL
);


ALTER TABLE puser.relacion_caja_chica OWNER TO puser;

--
-- Name: relacion_contrato_obras_id_seq; Type: SEQUENCE; Schema: puser; Owner: puser
--

CREATE SEQUENCE relacion_contrato_obras_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE puser.relacion_contrato_obras_id_seq OWNER TO puser;

--
-- Name: relacion_contrato_obras_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval('relacion_contrato_obras_id_seq', 14, true);


--
-- Name: relacion_contrato_obras; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_contrato_obras (
    id integer DEFAULT nextval('relacion_contrato_obras_id_seq'::regclass) NOT NULL,
    id_parcat integer,
    id_contrato_obras character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL
);


ALTER TABLE puser.relacion_contrato_obras OWNER TO puser;

--
-- Name: relacion_contrato_servicio_id_seq; Type: SEQUENCE; Schema: puser; Owner: puser
--

CREATE SEQUENCE relacion_contrato_servicio_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE puser.relacion_contrato_servicio_id_seq OWNER TO puser;

--
-- Name: relacion_contrato_servicio_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval('relacion_contrato_servicio_id_seq', 6, true);


--
-- Name: relacion_contrato_servicio; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_contrato_servicio (
    id integer DEFAULT nextval('relacion_contrato_servicio_id_seq'::regclass) NOT NULL,
    id_parcat integer,
    id_contrato_servicio character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL
);


ALTER TABLE puser.relacion_contrato_servicio OWNER TO puser;

--
-- Name: relacion_movimientos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_movimientos (
    id serial NOT NULL,
    nrodoc character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL,
    id_parcat integer
);


ALTER TABLE puser.relacion_movimientos OWNER TO puser;

--
-- Name: relacion_movimientos_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_movimientos', 'id'), 194, true);


--
-- Name: relacion_movimientos_productos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_movimientos_productos (
    id serial NOT NULL,
    nrodoc character varying NOT NULL,
    id_producto character varying NOT NULL,
    cantidad character varying NOT NULL,
    precio_base double precision NOT NULL,
    precio_iva double precision NOT NULL,
    precio_total double precision NOT NULL
);


ALTER TABLE puser.relacion_movimientos_productos OWNER TO puser;

--
-- Name: relacion_movimientos_productos_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_movimientos_productos', 'id'), 57, true);


--
-- Name: relacion_nomina; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_nomina (
    id serial NOT NULL,
    id_nomina character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL,
    id_parcat integer
);


ALTER TABLE puser.relacion_nomina OWNER TO puser;

--
-- Name: relacion_nomina_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_nomina', 'id'), 13, true);


--
-- Name: relacion_obras; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_obras (
    id serial NOT NULL,
    monto double precision NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    id_obra character varying(10) NOT NULL
);


ALTER TABLE puser.relacion_obras OWNER TO puser;

--
-- Name: relacion_obras_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_obras', 'id'), 66, true);


--
-- Name: relacion_ord_serv_trab; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ord_serv_trab (
    id serial NOT NULL,
    id_ord_serv_trab character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL,
    id_parcat integer
);


ALTER TABLE puser.relacion_ord_serv_trab OWNER TO puser;

--
-- Name: TABLE relacion_ord_serv_trab; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ord_serv_trab IS 'guardo las partidas y su respectivo monto, está relacionada con la tabla orden_servicio_trabajo';


--
-- Name: relacion_ord_serv_trab_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ord_serv_trab', 'id'), 36, true);


--
-- Name: relacion_ord_serv_trab_productos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ord_serv_trab_productos (
    id serial NOT NULL,
    id_ord_serv_trab character varying NOT NULL,
    id_producto character varying NOT NULL,
    cantidad integer NOT NULL,
    precio_base double precision NOT NULL,
    precio_iva double precision NOT NULL,
    precio_total double precision NOT NULL
);


ALTER TABLE puser.relacion_ord_serv_trab_productos OWNER TO puser;

--
-- Name: TABLE relacion_ord_serv_trab_productos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ord_serv_trab_productos IS 'guarda los productos de la orden de servicio o trabajo creada pero aun no aprobada, se relaciona con   la tabla orden_servicio_trabajo';


--
-- Name: relacion_ord_serv_trab_productos_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ord_serv_trab_productos', 'id'), 39, true);


--
-- Name: relacion_ordcompra; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ordcompra (
    id serial NOT NULL,
    id_ord_compra character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL
);


ALTER TABLE puser.relacion_ordcompra OWNER TO puser;

--
-- Name: TABLE relacion_ordcompra; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ordcompra IS 'guardo las partidas y su respectivo monto, está relacionada con la tabla orden_compra';


--
-- Name: relacion_ordcompra_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ordcompra', 'id'), 130, true);


--
-- Name: relacion_ordcompra_prod; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ordcompra_prod (
    id serial NOT NULL,
    id_ord_compra character varying NOT NULL,
    id_producto character varying NOT NULL,
    cantidad integer NOT NULL,
    precio_base double precision NOT NULL,
    precio_iva double precision NOT NULL,
    precio_total double precision NOT NULL
);


ALTER TABLE puser.relacion_ordcompra_prod OWNER TO puser;

--
-- Name: TABLE relacion_ordcompra_prod; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ordcompra_prod IS 'Relacion de las ordenes de compra con los productos';


--
-- Name: relacion_ordcompra_prod_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ordcompra_prod', 'id'), 103, true);


--
-- Name: relacion_pp_cp; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_pp_cp (
    id_escenario character varying(10) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    ano character(4),
    presupuesto_original double precision,
    aumentos double precision,
    disminuciones double precision,
    compromisos double precision,
    causados double precision,
    pagados double precision,
    disponible double precision,
    id_asignacion character varying(10),
    aingresos boolean,
    agastos boolean,
    id serial NOT NULL
);


ALTER TABLE puser.relacion_pp_cp OWNER TO puser;

--
-- Name: TABLE relacion_pp_cp; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_pp_cp IS 'relaciona las partidas presupuestarias con categoria programatica y escenario';


--
-- Name: COLUMN relacion_pp_cp.aingresos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN relacion_pp_cp.aingresos IS 'afecta ingresos';


--
-- Name: COLUMN relacion_pp_cp.agastos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN relacion_pp_cp.agastos IS 'afecta gastos';


--
-- Name: relacion_pp_cp_hist; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_pp_cp_hist (
    id_escenario character varying(10) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    ano character(4),
    presupuesto_original double precision,
    aumentos double precision,
    disminuciones double precision,
    compromisos double precision,
    causados double precision,
    pagados double precision,
    disponible double precision,
    id_asignacion character varying(10),
    aingresos boolean,
    agastos boolean,
    id serial NOT NULL
);


ALTER TABLE puser.relacion_pp_cp_hist OWNER TO puser;

--
-- Name: TABLE relacion_pp_cp_hist; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_pp_cp_hist IS 'relaciona las partidas presupuestarias con categoria programatica y escenario';


--
-- Name: COLUMN relacion_pp_cp_hist.aingresos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN relacion_pp_cp_hist.aingresos IS 'afecta ingresos';


--
-- Name: COLUMN relacion_pp_cp_hist.agastos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN relacion_pp_cp_hist.agastos IS 'afecta gastos';


--
-- Name: relacion_pp_cp_hist_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_pp_cp_hist', 'id'), 1, false);


--
-- Name: relacion_pp_cp_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_pp_cp', 'id'), 49, true);


--
-- Name: relacion_producto_prove; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_producto_prove (
    id serial NOT NULL,
    id_proveedores integer,
    id_productos integer
);


ALTER TABLE puser.relacion_producto_prove OWNER TO puser;

--
-- Name: TABLE relacion_producto_prove; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_producto_prove IS 'relacion de productos a proveedores';


--
-- Name: relacion_producto_prove_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_producto_prove', 'id'), 2, true);


--
-- Name: relacion_req_gp; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_req_gp (
    id_grupo_proveedor character varying(10) NOT NULL,
    id_requisito character varying(10) NOT NULL,
    id serial NOT NULL,
    fecha_emi date,
    fecha_vcto date,
    prorroga integer
);


ALTER TABLE puser.relacion_req_gp OWNER TO puser;

--
-- Name: TABLE relacion_req_gp; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_req_gp IS 'almacena requisitos por cada grupo de proveedores';


--
-- Name: relacion_req_gp_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_req_gp', 'id'), 65, true);


--
-- Name: relacion_req_prov; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_req_prov (
    id serial NOT NULL,
    id_proveedores integer,
    id_requisitos character varying,
    x_fecha_emi character varying,
    x_fecha_vcto character varying,
    prorroga integer DEFAULT 0,
    fecha_emi date,
    fecha_vcto date
);


ALTER TABLE puser.relacion_req_prov OWNER TO puser;

--
-- Name: TABLE relacion_req_prov; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_req_prov IS 'relacion de requisitos con proveedores, requisitos para un provedor';


--
-- Name: relacion_req_prov_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_req_prov', 'id'), 782, true);


--
-- Name: relacion_solicitud_pago; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_solicitud_pago (
    id serial NOT NULL,
    id_parcat integer,
    id_solicitud_pago character varying(15) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_partida_presupuestaria character varying(13) NOT NULL,
    monto double precision NOT NULL
);


ALTER TABLE puser.relacion_solicitud_pago OWNER TO puser;

--
-- Name: relacion_solicitud_pago_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_solicitud_pago', 'id'), 1, false);


--
-- Name: relacion_ue_cp; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ue_cp (
    id_unidad_ejecutora character varying(10) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    id serial NOT NULL
);


ALTER TABLE puser.relacion_ue_cp OWNER TO puser;

--
-- Name: TABLE relacion_ue_cp; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ue_cp IS 'relaciona las tablas Categorias Programaticas y unidades ejecutoras';


--
-- Name: relacion_ue_cp_hist; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_ue_cp_hist (
    id_unidad_ejecutora character varying(10) NOT NULL,
    id_categoria_programatica character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    id serial NOT NULL
);


ALTER TABLE puser.relacion_ue_cp_hist OWNER TO puser;

--
-- Name: TABLE relacion_ue_cp_hist; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_ue_cp_hist IS 'relaciona las tablas Categorias Programaticas y unidades ejecutoras';


--
-- Name: relacion_ue_cp_hist_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ue_cp_hist', 'id'), 1, false);


--
-- Name: relacion_ue_cp_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_ue_cp', 'id'), 57, true);


--
-- Name: relacion_us_op; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE relacion_us_op (
    id serial NOT NULL,
    id_usuario character varying(10) NOT NULL,
    id_operacion integer NOT NULL
);


ALTER TABLE puser.relacion_us_op OWNER TO puser;

--
-- Name: TABLE relacion_us_op; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE relacion_us_op IS 'asigna a cada usuario las operaciones que puede realizar dentro del sistema';


--
-- Name: relacion_us_op_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('relacion_us_op', 'id'), 245, true);


--
-- Name: requisitos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE requisitos (
    id character varying(10) NOT NULL,
    nombre character varying NOT NULL,
    descripcion character varying NOT NULL,
    fecha date DEFAULT ('now'::text)::date NOT NULL,
    vencido boolean DEFAULT false
);


ALTER TABLE puser.requisitos OWNER TO puser;

--
-- Name: retencion_iva; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE retencion_iva (
    id serial NOT NULL,
    id_proveedor integer,
    tipo_contribuyente character varying,
    fecha date,
    ingreso_periodo_fiscal double precision,
    cant_unid_tributaria double precision
);


ALTER TABLE puser.retencion_iva OWNER TO puser;

--
-- Name: TABLE retencion_iva; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE retencion_iva IS 'retención del iva, de los proveedores... los relaciono por el id del proveedor';


--
-- Name: retencion_iva_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('retencion_iva', 'id'), 56, true);


--
-- Name: rif_letra; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE rif_letra (
    id character varying NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.rif_letra OWNER TO puser;

--
-- Name: TABLE rif_letra; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE rif_letra IS 'letra del RIF';


--
-- Name: sistema; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE sistema (
    id serial NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.sistema OWNER TO puser;

--
-- Name: sistema_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('sistema', 'id'), 5, true);


--
-- Name: situaciones; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE situaciones (
    id character varying(10) NOT NULL,
    descripcion character varying,
    abreviacion character(1)
);


ALTER TABLE puser.situaciones OWNER TO puser;

--
-- Name: solvencias; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE solvencias (
    id serial NOT NULL,
    descripcion character varying
);


ALTER TABLE puser.solvencias OWNER TO puser;

--
-- Name: solvencias_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('solvencias', 'id'), 1, true);


--
-- Name: status_producto; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE status_producto (
    id serial NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.status_producto OWNER TO puser;

--
-- Name: TABLE status_producto; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE status_producto IS 'estatus de un producto, q puede ser, S:servicio, A: almacen, C:compras';


--
-- Name: statu_producto_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('status_producto', 'id'), 3, true);


--
-- Name: status; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE status (
    id integer NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.status OWNER TO puser;

--
-- Name: TABLE status; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE status IS 'estatus de usuarios ';


--
-- Name: status_proveedor; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE status_proveedor (
    id_ serial NOT NULL,
    id character varying,
    descripcion character varying
);


ALTER TABLE puser.status_proveedor OWNER TO puser;

--
-- Name: status_proveedor_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('status_proveedor', 'id_'), 3, true);


--
-- Name: tipo_producto; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE tipo_producto (
    id serial NOT NULL,
    descripcion character varying NOT NULL,
    observacion character varying NOT NULL,
    id_tipo_producto_clasif integer NOT NULL,
    fecha date,
    codigo character varying NOT NULL,
    id_partidas_presupuestarias character varying NOT NULL
);


ALTER TABLE puser.tipo_producto OWNER TO puser;

--
-- Name: TABLE tipo_producto; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE tipo_producto IS 'descripcion tipo producto...';


--
-- Name: COLUMN tipo_producto.codigo; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN tipo_producto.codigo IS 'codigo del tipo de producto, a mano';


--
-- Name: tipo_producto_clasif; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE tipo_producto_clasif (
    id serial NOT NULL,
    descripcion character varying,
    observacion character varying,
    fecha date
);


ALTER TABLE puser.tipo_producto_clasif OWNER TO puser;

--
-- Name: TABLE tipo_producto_clasif; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE tipo_producto_clasif IS 'clasificacion del tipo de producto';


--
-- Name: tipo_producto_clasif_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipo_producto_clasif', 'id'), 2, true);


--
-- Name: tipo_producto_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipo_producto', 'id'), 13, true);


--
-- Name: tipos_documentos; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE tipos_documentos (
    id_momento_presupuestario integer,
    descripcion character varying,
    observacion text,
    colocar_op boolean,
    id character varying(10) NOT NULL,
    abreviacion character varying(2)
);


ALTER TABLE puser.tipos_documentos OWNER TO puser;

--
-- Name: TABLE tipos_documentos; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE tipos_documentos IS 'almacena tipos de documentos';


--
-- Name: tipos_fianzas; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE tipos_fianzas (
    id character varying NOT NULL,
    descripcion character varying NOT NULL
);


ALTER TABLE puser.tipos_fianzas OWNER TO puser;

--
-- Name: TABLE tipos_fianzas; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE tipos_fianzas IS 'almacena los tipos de fianzas utilizados en Contrato de Obras';


--
-- Name: unidades_ejecutoras; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE unidades_ejecutoras (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    responsable character varying
);


ALTER TABLE puser.unidades_ejecutoras OWNER TO puser;

--
-- Name: TABLE unidades_ejecutoras; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE unidades_ejecutoras IS 'almacena las unidades ejecutoras';


--
-- Name: unidades_ejecutoras_hist; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE unidades_ejecutoras_hist (
    id character varying(10) NOT NULL,
    id_escenario character varying(10) NOT NULL,
    descripcion character varying,
    responsable character varying
);


ALTER TABLE puser.unidades_ejecutoras_hist OWNER TO puser;

--
-- Name: TABLE unidades_ejecutoras_hist; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE unidades_ejecutoras_hist IS 'almacena las unidades ejecutoras';


--
-- Name: usuarios; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE usuarios (
    nombre character varying,
    apellido character varying,
    id_unidad_ejecutora character varying(10) NOT NULL,
    id_profesion character varying(10) NOT NULL,
    id_cargo character varying(10) NOT NULL,
    id character varying(10) NOT NULL,
    ind character(1),
    "login" character varying(200),
    "password" character varying(64),
    status character(1) DEFAULT 1,
    cedula integer NOT NULL
);


ALTER TABLE puser.usuarios OWNER TO puser;

--
-- Name: TABLE usuarios; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE usuarios IS 'informacion de usuarios';


--
-- Name: COLUMN usuarios.ind; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN usuarios.ind IS 'Indica si el usuario es (V)enezolano o (E)xtrangero';


--
-- Name: COLUMN usuarios.status; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON COLUMN usuarios.status IS '1 si el usuario esta activo, 0 si está inactivo';


--
-- Name: ut; Type: TABLE; Schema: puser; Owner: puser; Tablespace: 
--

CREATE TABLE ut (
    id serial NOT NULL,
    fecha_desde date,
    fecha_hasta date,
    ut double precision
);


ALTER TABLE puser.ut OWNER TO puser;

--
-- Name: TABLE ut; Type: COMMENT; Schema: puser; Owner: puser
--

COMMENT ON TABLE ut IS 'unidad trbutaria por pedodo';


--
-- Name: ut_id_seq; Type: SEQUENCE SET; Schema: puser; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('ut', 'id'), 3, true);


SET search_path = vehiculo, pg_catalog;

--
-- Name: banco; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE banco (
    id serial NOT NULL,
    descripcion character varying,
    nombre_corto character varying,
    codigo character varying,
    status integer
);


ALTER TABLE vehiculo.banco OWNER TO puser;

--
-- Name: banco_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('banco', 'id'), 5, true);


--
-- Name: base_calculo_veh; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE base_calculo_veh (
    id serial NOT NULL,
    vig_desde date,
    vig_hasta date,
    recargo double precision,
    und_tarifa character varying(1),
    desc_tipo character varying,
    aplica_desde integer,
    aplica_hasta integer,
    art_ref character varying,
    anio integer
);


ALTER TABLE vehiculo.base_calculo_veh OWNER TO puser;

--
-- Name: TABLE base_calculo_veh; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE base_calculo_veh IS 'base de cálculo para vehículos, art 29.';


--
-- Name: COLUMN base_calculo_veh.recargo; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN base_calculo_veh.recargo IS 'porcentaje a aplicar';


--
-- Name: COLUMN base_calculo_veh.aplica_desde; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN base_calculo_veh.aplica_desde IS 'desde numero de dias del año';


--
-- Name: COLUMN base_calculo_veh.aplica_hasta; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN base_calculo_veh.aplica_hasta IS 'hasta numero de dias del año';


--
-- Name: base_calculo_veh_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('base_calculo_veh', 'id'), 12, true);


--
-- Name: colores; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE colores (
    cod_col serial NOT NULL,
    descripcion character varying NOT NULL,
    cod_cambio integer,
    status integer DEFAULT 0
);


ALTER TABLE vehiculo.colores OWNER TO puser;

--
-- Name: TABLE colores; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE colores IS 'colores...';


--
-- Name: COLUMN colores.status; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN colores.status IS 'activo:1, inactivo:0';


--
-- Name: colores_cod_col_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('colores', 'cod_col'), 162, true);


--
-- Name: contribuyente; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE contribuyente (
    id serial NOT NULL,
    tipo_persona character varying,
    nacionalidad character varying,
    tipo_identificacion character varying,
    identificacion character varying NOT NULL,
    id_estado integer,
    id_parroquia integer,
    id_municipio integer,
    contribuyente character varying,
    razon_social text,
    fec_nacimiento character varying,
    pasaporte character varying,
    primer_nombre character varying,
    segundo_nombre character varying,
    primer_apellido character varying,
    segundo_apellido character varying,
    direccion text,
    domicilio_fiscal text,
    fec_registro character varying,
    fec_desincorporacion character varying,
    direccion_eventual text,
    ciudad_nacimiento character varying,
    pais_nacimiento character varying,
    ciudad_domicilio character varying,
    edo_domicilio character varying,
    fec_crea character varying,
    usr_crea integer,
    fec_modif character varying,
    usr_modif integer,
    telefono character varying,
    celular character varying,
    email character varying,
    rif_letra character varying,
    rif_numero character varying,
    rif_control character varying,
    rif character varying,
    fax character varying
);


ALTER TABLE vehiculo.contribuyente OWNER TO puser;

--
-- Name: TABLE contribuyente; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE contribuyente IS 'datos del contribuyente';


--
-- Name: COLUMN contribuyente.tipo_persona; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN contribuyente.tipo_persona IS 'juridica natural';


--
-- Name: COLUMN contribuyente.usr_crea; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN contribuyente.usr_crea IS 'id del usuario q creó el registro';


--
-- Name: COLUMN contribuyente.usr_modif; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN contribuyente.usr_modif IS 'id del usuario q modifica el registro';


--
-- Name: contribuyente_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('contribuyente', 'id'), 55, true);


--
-- Name: costo_calcomania; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE costo_calcomania (
    id serial NOT NULL,
    anio integer,
    fecha_desde character varying,
    fecha_hasta character varying,
    monto double precision,
    status integer
);


ALTER TABLE vehiculo.costo_calcomania OWNER TO puser;

--
-- Name: TABLE costo_calcomania; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE costo_calcomania IS 'costo calcomania';


--
-- Name: costo_calcomania_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('costo_calcomania', 'id'), 7, true);


--
-- Name: descuento; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE descuento (
    cod_des serial NOT NULL,
    dias integer,
    fecha date,
    porcentaje double precision
);


ALTER TABLE vehiculo.descuento OWNER TO puser;

--
-- Name: TABLE descuento; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE descuento IS 'tabla de descuentos; porcentaje de descuento por dias';


--
-- Name: COLUMN descuento.fecha; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN descuento.fecha IS 'fecha de creacion/modificación? se entiende q es para la auditoria.(log)';


--
-- Name: descuento_cod_des_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('descuento', 'cod_des'), 11, true);


--
-- Name: desincorporacion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE desincorporacion (
    cod_des serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 1
);


ALTER TABLE vehiculo.desincorporacion OWNER TO puser;

--
-- Name: TABLE desincorporacion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE desincorporacion IS 'motivos de desincorporacion';


--
-- Name: desincorporacion_cod_des_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('desincorporacion', 'cod_des'), 10, true);


--
-- Name: det_forma_pago; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE det_forma_pago (
    id serial NOT NULL,
    num_pago integer,
    mto_pago double precision,
    nro_doc character varying(20),
    st_depo character varying(1),
    deposito character varying(20),
    chq_anul character varying(1),
    pto_venta character varying(3),
    cod_banco integer,
    tipo_pago integer
);


ALTER TABLE vehiculo.det_forma_pago OWNER TO puser;

--
-- Name: det_forma_pago_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('det_forma_pago', 'id'), 27, true);


--
-- Name: det_pago; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE det_pago (
    id serial NOT NULL,
    num_pago integer,
    id_mov integer,
    cod_tip character varying(2),
    anio integer,
    id_base_calculo integer,
    monto_pago double precision,
    usr_crea character varying(32),
    fec_crea date,
    usr_mod character varying(32),
    fec_mod date,
    id_tipo_transaccion integer,
    nro_declaracion character varying,
    cod_tipo_inmueble character varying(3),
    cod_fiscaliz character varying(10),
    nro_reng integer,
    id_fiscal character varying(20),
    cod_tasas character varying(3),
    cantidad integer
);


ALTER TABLE vehiculo.det_pago OWNER TO puser;

--
-- Name: det_pago_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('det_pago', 'id'), 183, true);


--
-- Name: esp_costo_veh; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE esp_costo_veh (
    cod_esp serial NOT NULL,
    cod_veh integer,
    monto double precision,
    fecha_desde date,
    fecha_hasta date
);


ALTER TABLE vehiculo.esp_costo_veh OWNER TO puser;

--
-- Name: TABLE esp_costo_veh; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE esp_costo_veh IS 'especificaciones costo del vehículo: se relaciona con la tabla; ''tipo_veh_segun_gaceta'' a través del campo ''cod_veh''';


--
-- Name: esp_costo_veh_cod_esp_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('esp_costo_veh', 'cod_esp'), 11, true);


--
-- Name: exoneracion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE exoneracion (
    cod_exo serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 1
);


ALTER TABLE vehiculo.exoneracion OWNER TO puser;

--
-- Name: TABLE exoneracion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE exoneracion IS 'motimos de exoneración';


--
-- Name: exoneracion_cod_exo_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('exoneracion', 'cod_exo'), 11, true);


--
-- Name: forma_pago; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE forma_pago (
    id serial NOT NULL,
    descripcion character varying,
    efectivo character varying,
    nombre_corto character varying,
    status integer
);


ALTER TABLE vehiculo.forma_pago OWNER TO puser;

--
-- Name: TABLE forma_pago; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE forma_pago IS 'formas de pago';


--
-- Name: COLUMN forma_pago.efectivo; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN forma_pago.efectivo IS 'S-N: si es o no efectivo';


--
-- Name: COLUMN forma_pago.status; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN forma_pago.status IS '1: activo, 0: inactivo';


--
-- Name: forma_pago_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('forma_pago', 'id'), 8, true);


--
-- Name: id_movimiento_seq; Type: SEQUENCE; Schema: vehiculo; Owner: puser
--

CREATE SEQUENCE id_movimiento_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE vehiculo.id_movimiento_seq OWNER TO puser;

--
-- Name: id_movimiento_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval('id_movimiento_seq', 524, true);


--
-- Name: imp_liq; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE imp_liq (
    id serial NOT NULL,
    id_mov double precision,
    cot_tip character varying,
    id_contribuyente integer,
    anio integer,
    periodo integer,
    monto_bs double precision,
    usr_crea integer,
    fec_crea date,
    usr_mod integer,
    fec_mod date,
    sts_liq character varying,
    nro_declaracion character varying,
    ramo character varying,
    id_fiscal character varying,
    cod_tipo_inmueble character varying,
    cod_fiscaliz character varying,
    nro_reng double precision,
    cod_tasas character varying,
    cantidad double precision,
    medida double precision,
    prec_terr date,
    ind_liqexp character varying,
    id_base_calculo_veh integer,
    id_tipo_transaccion integer,
    id_base_calculo_pub integer
);


ALTER TABLE vehiculo.imp_liq OWNER TO puser;

--
-- Name: imp_liq_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('imp_liq', 'id'), 3927, true);


--
-- Name: imp_pago; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE imp_pago (
    id serial NOT NULL,
    num_pago integer NOT NULL,
    fecha_pago date,
    sts_pago character varying(1),
    ramo numeric,
    mto_tot_pago double precision,
    id_contribuyente integer,
    ent_cod character varying(3),
    nro_caja integer,
    observa character varying(400),
    chq_dvto character varying(1),
    usr_crea character varying(32),
    fec_crea date,
    usr_mod character varying(32),
    fec_mod date,
    cod_anul character varying(3),
    fec_anul date,
    usr_anul character varying(32),
    und_cod character varying(10),
    calcomania character varying
);


ALTER TABLE vehiculo.imp_pago OWNER TO puser;

--
-- Name: imp_pago_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('imp_pago', 'id'), 73, true);


--
-- Name: linea; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE linea (
    cod_lin serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 1
);


ALTER TABLE vehiculo.linea OWNER TO puser;

--
-- Name: TABLE linea; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE linea IS 'lineas de transporte';


--
-- Name: linea_cod_lin_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('linea', 'cod_lin'), 8, true);


--
-- Name: marca; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE marca (
    cod_mar serial NOT NULL,
    descripcion character varying,
    cod_cambio integer,
    status integer DEFAULT 0
);


ALTER TABLE vehiculo.marca OWNER TO puser;

--
-- Name: TABLE marca; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE marca IS 'marca de vehiculo';


--
-- Name: marca_cod_mar_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('marca', 'cod_mar'), 278, true);


--
-- Name: mod_mar; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE mod_mar (
    id serial NOT NULL,
    cod_mod integer,
    cod_mar integer
);


ALTER TABLE vehiculo.mod_mar OWNER TO puser;

--
-- Name: TABLE mod_mar; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE mod_mar IS 'relacion de modelos por marcas';


--
-- Name: mod_mar_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('mod_mar', 'id'), 4, true);


--
-- Name: modelo; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE modelo (
    cod_mod serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 0
);


ALTER TABLE vehiculo.modelo OWNER TO puser;

--
-- Name: TABLE modelo; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE modelo IS 'modelos de vehiculo';


--
-- Name: modelo_cod_mod_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('modelo', 'cod_mod'), 1446, true);


--
-- Name: num_pago_seq; Type: SEQUENCE; Schema: vehiculo; Owner: puser
--

CREATE SEQUENCE num_pago_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE vehiculo.num_pago_seq OWNER TO puser;

--
-- Name: num_pago_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval('num_pago_seq', 81, true);


--
-- Name: ramo_imp; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE ramo_imp (
    id serial NOT NULL,
    ramo numeric NOT NULL,
    descripcion character varying,
    tipo_imp character varying,
    anio integer,
    status integer
);


ALTER TABLE vehiculo.ramo_imp OWNER TO puser;

--
-- Name: TABLE ramo_imp; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE ramo_imp IS 'ramo ¿?¿?¿?¿?';


--
-- Name: COLUMN ramo_imp.tipo_imp; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN ramo_imp.tipo_imp IS '¿?:consecuencias de una no-planificación, 1byte';


--
-- Name: ramo_imp_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('ramo_imp', 'id'), 7, true);


--
-- Name: ramo_transaccion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE ramo_transaccion (
    id serial NOT NULL,
    id_ramo_imp integer,
    id_tipo_transaccion integer NOT NULL,
    anio integer
);


ALTER TABLE vehiculo.ramo_transaccion OWNER TO puser;

--
-- Name: TABLE ramo_transaccion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE ramo_transaccion IS 'relación ramo transacción, me gustaria saber q estoy haciendo ;)';


--
-- Name: ramo_transaccion_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('ramo_transaccion', 'id'), 14, true);


--
-- Name: sancion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE sancion (
    cod_san serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 1,
    anio integer,
    monto double precision
);


ALTER TABLE vehiculo.sancion OWNER TO puser;

--
-- Name: TABLE sancion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE sancion IS 'sanciones ¿?';


--
-- Name: sancion_cod_san_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('sancion', 'cod_san'), 13, true);


--
-- Name: solvencia; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE solvencia (
    id serial NOT NULL,
    fecha_desde date,
    fecha_hasta date,
    monto_normal double precision,
    monto_habilitado double precision
);


ALTER TABLE vehiculo.solvencia OWNER TO puser;

--
-- Name: TABLE solvencia; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE solvencia IS 'solvencia';


--
-- Name: solvencia_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('solvencia', 'id'), 4, true);


--
-- Name: tasa_bancaria; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE tasa_bancaria (
    cod_tas serial NOT NULL,
    mes integer,
    anio integer,
    monto double precision
);


ALTER TABLE vehiculo.tasa_bancaria OWNER TO puser;

--
-- Name: TABLE tasa_bancaria; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE tasa_bancaria IS 'tabla de tasas bancarias';


--
-- Name: tasa_bancaria_cod_tas_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tasa_bancaria', 'cod_tas'), 6, true);


--
-- Name: tasa_inscripcion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE tasa_inscripcion (
    id serial NOT NULL,
    fecha_desde character varying,
    fecha_hasta character varying,
    monto double precision
);


ALTER TABLE vehiculo.tasa_inscripcion OWNER TO puser;

--
-- Name: TABLE tasa_inscripcion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE tasa_inscripcion IS 'tasas de inscripción, en donde? ;)';


--
-- Name: tasa_inscripcion_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tasa_inscripcion', 'id'), 6, true);


--
-- Name: tipo; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE tipo (
    cod_tip serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 0
);


ALTER TABLE vehiculo.tipo OWNER TO puser;

--
-- Name: TABLE tipo; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE tipo IS 'tipos de vehiculos';


--
-- Name: tipo_cod_tip_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipo', 'cod_tip'), 72, true);


--
-- Name: tipo_transaccion; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE tipo_transaccion (
    id serial NOT NULL,
    descripcion character varying NOT NULL,
    id_par_pre character varying(13),
    tipo_trans character varying NOT NULL,
    anio integer,
    status integer
);


ALTER TABLE vehiculo.tipo_transaccion OWNER TO puser;

--
-- Name: TABLE tipo_transaccion; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE tipo_transaccion IS '?¿?¿?¿?¿?¿?¿?';


--
-- Name: COLUMN tipo_transaccion.id_par_pre; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN tipo_transaccion.id_par_pre IS 'se relaciona con partidas_presupuestarias';


--
-- Name: tipo_transaccion_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipo_transaccion', 'id'), 8, true);


--
-- Name: tipo_veh_segun_gaceta; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE tipo_veh_segun_gaceta (
    cod_veh serial NOT NULL,
    descripcion character varying,
    status integer
);


ALTER TABLE vehiculo.tipo_veh_segun_gaceta OWNER TO puser;

--
-- Name: TABLE tipo_veh_segun_gaceta; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE tipo_veh_segun_gaceta IS 'tipo de vehiculo segun gaceta';


--
-- Name: tipo_veh_segun_gaceta_cod_veh_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipo_veh_segun_gaceta', 'cod_veh'), 16, true);


--
-- Name: uso; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE uso (
    cod_uso serial NOT NULL,
    descripcion character varying,
    status integer DEFAULT 0
);


ALTER TABLE vehiculo.uso OWNER TO puser;

--
-- Name: TABLE uso; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE uso IS 'uso del vehiculo, proposito';


--
-- Name: uso_cod_uso_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('uso', 'cod_uso'), 43, true);


--
-- Name: vehiculo; Type: TABLE; Schema: vehiculo; Owner: puser; Tablespace: 
--

CREATE TABLE vehiculo (
    id serial NOT NULL,
    serial_carroceria character varying,
    placa character varying NOT NULL,
    fec_crea date,
    id_contribuyente integer,
    usr_crea integer,
    fec_mod date,
    usr_mod character varying,
    anio_veh integer,
    serial_motor character varying,
    cod_mar integer,
    cod_mod integer,
    cod_col integer,
    cod_uso integer,
    cod_tip integer,
    fec_compra character varying,
    peso_kg double precision,
    cant_eje integer,
    precio double precision,
    observacion text,
    exento character varying,
    status integer,
    anio_pago integer,
    cod_lin integer,
    id_estado integer,
    id_parroquia integer,
    id_municipio integer,
    concesionario character varying,
    puestos integer,
    primera_vez integer
);


ALTER TABLE vehiculo.vehiculo OWNER TO puser;

--
-- Name: TABLE vehiculo; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON TABLE vehiculo IS 'datos de vehículos';


--
-- Name: COLUMN vehiculo.usr_crea; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.usr_crea IS 'usuario q creo el registro, cargo el id.';


--
-- Name: COLUMN vehiculo.serial_motor; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.serial_motor IS 'serial del motor';


--
-- Name: COLUMN vehiculo.cod_tip; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.cod_tip IS 'tip_veh';


--
-- Name: COLUMN vehiculo.fec_compra; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.fec_compra IS 'fecha compra';


--
-- Name: COLUMN vehiculo.peso_kg; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.peso_kg IS 'peso en kilogramos';


--
-- Name: COLUMN vehiculo.cant_eje; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.cant_eje IS 'cantidad de ejes';


--
-- Name: COLUMN vehiculo.anio_pago; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.anio_pago IS 'año de pago';


--
-- Name: COLUMN vehiculo.cod_lin; Type: COMMENT; Schema: vehiculo; Owner: puser
--

COMMENT ON COLUMN vehiculo.cod_lin IS 'codigo de línea, síes una linea';


--
-- Name: vehiculo_id_seq; Type: SEQUENCE SET; Schema: vehiculo; Owner: puser
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('vehiculo', 'id'), 16, true);


SET search_path = finanzas, pg_catalog;

--
-- Data for Name: condiciones_pago; Type: TABLE DATA; Schema: finanzas; Owner: puser
--

INSERT INTO condiciones_pago VALUES ('001', 'Pago a 90 días');
INSERT INTO condiciones_pago VALUES ('002', 'Pago en efectivo');
INSERT INTO condiciones_pago VALUES ('003', 'Pago a 30 días');


--
-- Data for Name: relacion_retenciones_solicitud; Type: TABLE DATA; Schema: finanzas; Owner: puser
--



--
-- Data for Name: retenciones_adiciones; Type: TABLE DATA; Schema: finanzas; Owner: puser
--

INSERT INTO retenciones_adiciones VALUES ('001', 'IVA', 'Impuesto a las Ventas 14%', 's', 'x', 14, 1, '', '', '1');


--
-- Data for Name: solicitud_pago; Type: TABLE DATA; Schema: finanzas; Owner: puser
--

INSERT INTO solicitud_pago VALUES ('xxx-xxxx-xxxx', '001-0029', '2006-05-31', 1, '001', '01', '001', NULL);


--
-- Data for Name: tipos_solicitud_sin_imp; Type: TABLE DATA; Schema: finanzas; Owner: puser
--

INSERT INTO tipos_solicitud_sin_imp VALUES ('001', 'Anticipo Contratista', '2006', '11111111-0000001');


--
-- Data for Name: tipos_solicitudes; Type: TABLE DATA; Schema: finanzas; Owner: puser
--

INSERT INTO tipos_solicitudes VALUES ('01', 'SOLICITUD DE PAGO POR APORTES ECONÓMICOS PARA PAGAR ESTUDIOS');


SET search_path = publicidad, pg_catalog;

--
-- Data for Name: base_calculo_pub; Type: TABLE DATA; Schema: publicidad; Owner: puser
--



--
-- Data for Name: clasificacion; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO clasificacion VALUES (1, 'A', 'De libre presentacion para todo publico', '1');
INSERT INTO clasificacion VALUES (3, 'C', 'Mayores de dieciocho(18) años', '1');
INSERT INTO clasificacion VALUES (2, 'B', 'De libre presentacion para mayores de 14 años', '1');


--
-- Data for Name: entradas; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO entradas VALUES (1, 'Entradas de Cortesia', 10, 'No', 'Si', 0);
INSERT INTO entradas VALUES (2, 'Entradas Azules', 0, 'Si', 'No', 15000);
INSERT INTO entradas VALUES (3, 'Entradas Verdes', 10, 'Si', 'Si', 10000);


--
-- Data for Name: esp_costo_pub; Type: TABLE DATA; Schema: publicidad; Owner: puser
--



--
-- Data for Name: espectaculo; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO espectaculo VALUES (1, 'CIRCOS INTERNACIONALES', 1, 0, 4);
INSERT INTO espectaculo VALUES (2, 'CIRCOS NACIONALES', 1, 0, 3);
INSERT INTO espectaculo VALUES (3, 'PRESENTACION DE TV', 1, 250000, 0);


--
-- Data for Name: inspector; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO inspector VALUES (1, 'MARIA MILAGROS', 'MARTINEZ', 1);
INSERT INTO inspector VALUES (2, 'Joan', 'Casas', 0);


--
-- Data for Name: multa; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO multa VALUES (1, 'CONTRARIO AL ORDEN PUBLICO', 0, 100000, 'PU');
INSERT INTO multa VALUES (3, 'Multa de Publicidad', 0, 50000, 'PU');


--
-- Data for Name: propaganda; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO propaganda VALUES (1, 'VALLA CARTEL, PUB, METAL, VIDRIO, PARED, PLASTICO, NEON', 250000, 1);
INSERT INTO propaganda VALUES (2, 'Propaganda y Espectaculos', 200000, 1);
INSERT INTO propaganda VALUES (3, 'Propaganda y Espectaculos 1', 100000, 1);
INSERT INTO propaganda VALUES (4, 'Propaganda y Espectaculos 2', 50000, 0);


--
-- Data for Name: publicidad; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO publicidad VALUES (172, '2006-05-24', '12345-1', 34, '001', NULL, NULL);
INSERT INTO publicidad VALUES (173, '2006-05-24', '12345-1', 34, '002', NULL, NULL);
INSERT INTO publicidad VALUES (180, '2006-05-25', '', 34, '003', NULL, NULL);
INSERT INTO publicidad VALUES (181, '2006-05-25', '12345', 34, '002', NULL, NULL);
INSERT INTO publicidad VALUES (182, '2006-05-25', '12345', 34, '002', NULL, NULL);
INSERT INTO publicidad VALUES (185, '2006-05-26', '12345', 34, '001', NULL, NULL);
INSERT INTO publicidad VALUES (208, '2006-05-29', '', 34, '003', 2005, 2006);
INSERT INTO publicidad VALUES (213, '2006-05-31', '12345', 34, '003', 2006, NULL);
INSERT INTO publicidad VALUES (215, '2006-05-31', '12345', 34, '001', 2006, NULL);
INSERT INTO publicidad VALUES (217, '2006-05-31', '12345', 34, '001', 2006, NULL);


--
-- Data for Name: relacion_publicidad_espectaculo; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO relacion_publicidad_espectaculo VALUES (36, '2006-05-01', '2:30 AM', '2006-05-31', '6:30 PM', 15000, 75000, 2, 2, 5, 194, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (37, '2006-05-01', '2:30 AM', '2006-05-31', '6:30 PM', 10000, 40000, 2, 3, 4, 194, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (38, '2006-05-26', '12:00 PM', '2006-05-26', '12:00 PM', 15000, 75000, 2, 2, 5, 195, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (39, '2006-05-26', '2:30 AM', '2006-05-26', '3:30 PM', 15000, 75000, 2, 2, 5, 197, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (40, '2006-05-26', '2:30 AM', '2006-05-26', '3:30 PM', 10000, 30000, 2, 3, 3, 197, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (41, '2006-05-01', '1:00 AM', '2006-05-31', '11:30 PM', 15000, 75000, 2, 2, 5, 200, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (42, '2006-05-01', '1:00 AM', '2006-05-31', '11:30 PM', 10000, 40000, 2, 3, 4, 200, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (43, '2006-05-29', '3:00 AM', '2006-05-29', '10:30 PM', 15000, 75000, 1, 2, 5, 201, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (44, '2006-05-29', '3:00 AM', '2006-05-29', '10:30 PM', 10000, 40000, 1, 3, 4, 201, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (45, '2006-05-29', '3:00 AM', '2006-05-29', '10:30 PM', 15000, 75000, 1, 2, 5, 202, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (46, '2006-05-29', '3:00 AM', '2006-05-29', '10:30 PM', 10000, 40000, 1, 3, 4, 202, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (47, '2006-05-29', '5:00 AM', '2006-05-29', '11:00 PM', 15000, 75000, 1, 2, 5, 203, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (48, '2006-05-29', '5:00 AM', '2006-05-29', '11:00 PM', 10000, 30000, 1, 3, 3, 203, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (49, '2006-05-29', '3:30 AM', '2006-05-29', '10:30 PM', 15000, 75000, 2, 2, 5, 204, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (50, '2006-05-29', '3:30 AM', '2006-05-29', '10:30 PM', 10000, 40000, 2, 3, 4, 204, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (51, '2006-05-29', '3:30 AM', '2006-05-29', '11:30 AM', 15000, 75000, 2, 2, 5, 205, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (52, '2006-05-29', '3:30 AM', '2006-05-29', '11:30 AM', 10000, 40000, 2, 3, 4, 205, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (53, '2006-05-29', '3:00 AM', '2006-05-29', '11:30 PM', 15000, 75000, 2, 2, 5, 206, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (54, '2006-05-29', '3:00 AM', '2006-05-29', '11:30 PM', 10000, 40000, 2, 3, 4, 206, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (55, '2006-05-29', '3:00 AM', '2006-05-29', '11:30 PM', 15000, 75000, 2, 2, 5, 207, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (56, '2006-05-29', '3:00 AM', '2006-05-29', '11:30 PM', 10000, 40000, 2, 3, 4, 207, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (57, '2006-05-01', '6:00 AM', '2006-05-31', '11:00 PM', 15000, 75000, 2, 2, 5, 208, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (58, '2006-05-01', '6:00 AM', '2006-05-31', '11:00 PM', 10000, 40000, 2, 3, 4, 208, 0, '3');
INSERT INTO relacion_publicidad_espectaculo VALUES (59, '2006-05-01', '11:00 PM', '2006-05-31', '4:00 AM', 15000, 75000, 1, 2, 5, 213, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (60, '2006-05-01', '11:00 PM', '2006-05-31', '4:00 AM', 10000, 40000, 1, 3, 4, 213, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (61, '2006-05-01', '11:00 PM', '2006-05-31', '4:00 AM', 15000, 75000, 1, 2, 5, 214, 0, '4');
INSERT INTO relacion_publicidad_espectaculo VALUES (62, '2006-05-01', '11:00 PM', '2006-05-31', '4:00 AM', 10000, 40000, 1, 3, 4, 214, 0, '4');


--
-- Data for Name: relacion_publicidad_publicidades; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO relacion_publicidad_publicidades VALUES (32, '3', '4', '', '0', '', '0', '1', '600000', 200000, 181, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (33, '4', '', '5', '', '4', '', '20', '8000000', 100000, 181, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (34, '3', '4', '', '0', '', '0', '1', '600000', 200000, 182, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (35, '4', '', '5', '', '4', '', '20', '8000000', 100000, 182, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (36, '3', '4', '', '0', '', '0', '1', '600000', 200000, 183, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (37, '4', '', '5', '', '4', '', '20', '8000000', 100000, 183, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (38, '3', '4', '', '0', '', '0', '1', '600000', 200000, 184, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (39, '4', '', '5', '', '4', '', '20', '8000000', 100000, 184, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (40, '5', '3', '', '', '', '', '1', '1250000', 250000, 185, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (41, '3', '', '', '', '', '', '1', '600000', 200000, 185, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (42, '2', '', '', '', '', '', '1', '200000', 100000, 185, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (43, '5', '3', '', '', '', '', '1', '1250000', 250000, 186, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (44, '3', '', '', '', '', '', '1', '600000', 200000, 186, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (45, '2', '', '', '', '', '', '1', '200000', 100000, 186, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (46, '5', '4', '', '1', '', '1', '1', '1000000', 200000, 209, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (47, '3', '', '4', '', '2', '', '8', '6000000', 250000, 209, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (48, '5', '4', '', '1', '', '1', '1', '1000000', 200000, 210, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (49, '3', '', '4', '', '2', '', '8', '6000000', 250000, 210, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (50, '5', '1', '', '', '', '', '12', '15000000', 250000, 211, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (51, '3', '', '', '', '', '', '1', '600000', 200000, 211, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (52, '5', '1', '', '', '', '', '12', '15000000', 250000, 212, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (53, '3', '', '', '', '', '', '1', '600000', 200000, 212, 2);
INSERT INTO relacion_publicidad_publicidades VALUES (54, '5', '2', '', '', '', '', '1', '1250000', 250000, 215, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (55, '3', '', '', '', '', '', '1', '300000', 100000, 215, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (56, '5', '2', '', '', '', '', '1', '1250000', 250000, 216, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (57, '3', '', '', '', '', '', '1', '300000', 100000, 216, 3);
INSERT INTO relacion_publicidad_publicidades VALUES (58, '5', '2', '', '', '', '', '1', '1250000', 250000, 217, 1);
INSERT INTO relacion_publicidad_publicidades VALUES (59, '3', '', '', '', '', '', '1', '300000', 100000, 217, 3);


--
-- Data for Name: tipo_orden; Type: TABLE DATA; Schema: publicidad; Owner: puser
--

INSERT INTO tipo_orden VALUES ('001', 'Publicidad Fija', 'PF');
INSERT INTO tipo_orden VALUES ('002', 'Publicidad Eventual', 'PE');
INSERT INTO tipo_orden VALUES ('003', 'Espectaculos', 'ES');


SET search_path = puser, pg_catalog;

--
-- Data for Name: activo_inactivo_producto; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO activo_inactivo_producto VALUES (1, 'A');
INSERT INTO activo_inactivo_producto VALUES (2, 'I');


--
-- Data for Name: alcaldia; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO alcaldia VALUES ('01', 'Alcaldía del Municipio Libertador.', 'La  Alcaldía del Municipio Libertador, funciona como ente público recaudador de Impuesto Municipales, con la finalidad de realizar obras públicas que puedan mejorar  las condiciones de vida de los ciudadanos que residen en el municipio.', 'Calle Sucre entre Avenida Bolívar y Farriar frente a la Plaza la Victoria.', '1988-01-12', 'Tocuyito', 'Carabobo', '0241-8941831', '(0241)8942016-8942158-8941831', '4', 'Argenis Isaias Loreto Puerta', 'Jose Bennici
Dinorath Salas
Jose antonio Morillo
Jose Antonio Sevilla
Porfirio Alvia', '4', '2034');


--
-- Data for Name: asignaciones; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO asignaciones VALUES ('1', 'Coordinado');
INSERT INTO asignaciones VALUES ('2', 'FIDES');
INSERT INTO asignaciones VALUES ('3', 'LAEE');


--
-- Data for Name: caja_chica; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO caja_chica VALUES (3, '013', '010152', 15992657, '6', 'hola', '2006-05-24', '2006-05-24', '013-0001', 'hola');


--
-- Data for Name: cargos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO cargos VALUES ('2', 'Analista de registro de proveedores');
INSERT INTO cargos VALUES ('1', 'Analista de presupuesto');
INSERT INTO cargos VALUES ('3', 'Jefe de Tesorería.');


--
-- Data for Name: categorias_programaticas; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO categorias_programaticas VALUES ('01010101', '2006', 'VICE-PRESIDENCIA', '', false, '2006', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO categorias_programaticas VALUES ('010010102', '2006', 'SECRETARIA MUNICIPAL', '', false, '2006', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO categorias_programaticas VALUES ('01010102', '2006', 'categoria de prueba', '', false, '2006', NULL, NULL, NULL, NULL, NULL, NULL, NULL);


--
-- Data for Name: categorias_programaticas_hist; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: ciudadanos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO ciudadanos VALUES ('13789643', 'Francisco Calderón', '412-4383842', 'Maracay');
INSERT INTO ciudadanos VALUES ('qwerty', 'qwerty', 'qwerty', 'qwerty');
INSERT INTO ciudadanos VALUES ('15218288', 'Maria Gabriela Camacho', 'Un monte', '412-4383842');
INSERT INTO ciudadanos VALUES ('13381452', 'Joan Casas', 'xxx', '8225850');
INSERT INTO ciudadanos VALUES ('15992657', 'epa', '412-43833333', 'Maracay');


--
-- Data for Name: co_requis; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: co_rtpro; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO co_rtpro VALUES (2, 75, 2, '29/02/1998');
INSERT INTO co_rtpro VALUES (3, 75, 3, '29/02/1998');
INSERT INTO co_rtpro VALUES (4, 75, 4, '29/02/1998');
INSERT INTO co_rtpro VALUES (5, 75, 5, '29/02/1998');
INSERT INTO co_rtpro VALUES (6, 75, 6, '29/02/1998');
INSERT INTO co_rtpro VALUES (1, 1, 1, '29/01/1996');


--
-- Data for Name: condicion_pago; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO condicion_pago VALUES ('1', 'A 15 dias');


--
-- Data for Name: contrato_obras; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO contrato_obras VALUES (2, '011', '010152', 89, '11', '6', 'prueba', '2006-05-17', '2006-05-17', '011-0002', '', NULL);
INSERT INTO contrato_obras VALUES (3, '011', '010152', 89, '2', '6', 'contrato', '2006-05-17', '2006-05-17', '011-0003', '', 'sss');
INSERT INTO contrato_obras VALUES (1, '011', '010152', 89, '333', '6', 'ghghsdddd', '2006-05-11', '2006-05-11', '011-0001', '', 'wwww');


--
-- Data for Name: contrato_servicio; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO contrato_servicio VALUES (1, '012', '010152', 89, '2323', '6', 'lorem ipsum dolor', '2006-05-12', '2006-05-12', '012-0001', 'asasa');
INSERT INTO contrato_servicio VALUES (2, '012', '010153', 132, 'sgsdgsd', '6', 'dgdgdf', '2006-05-17', '2006-05-17', '012-0002', '');
INSERT INTO contrato_servicio VALUES (3, '012', '010152', 86, 'fg', '6', 'gh', '2006-05-17', '2006-05-17', '012-0003', 'ddd');


--
-- Data for Name: entidades; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO entidades VALUES ('01', 'Banco de Venezuela');


--
-- Data for Name: escenarios; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO escenarios VALUES ('2006', '2006', '2006', 'Escenario en Ejecución', '', 1, NULL, false);


--
-- Data for Name: escenarios_hist; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO escenarios_hist VALUES ('1', '1', '2006', 'prueba ', 'test test test test test test test test test test test test test test test test test test test test test test test test test ', 0.30000001, true, true);
INSERT INTO escenarios_hist VALUES ('3', '3', '2005', 'asdsd', 'sdsdsds', 1, true, true);
INSERT INTO escenarios_hist VALUES ('2cvvv', '2', '2007', 'otro escenario', 'texto 		$formulacion = ($formulacion == ''on'')? "1" : $formulacion;
		$formulacion = ($formulacion == ''on'')? "1" : $formulacion;
		$formulacion = ($formulacion == ''on'')? "1" : $formulacion;
		$formulacion = ($formulacion == ''on'')? "1" : $formulacion;aa', 0.89999998, false, false);
INSERT INTO escenarios_hist VALUES ('00005', '1', '2006', 'escenario 2006', '1', 2, true, false);


--
-- Data for Name: estado; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO estado VALUES (1, 'CARABOBO');
INSERT INTO estado VALUES (2, 'PORTUGUESA');


--
-- Data for Name: financiamiento; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO financiamiento VALUES ('APORTE LAEE', false, false, '3');
INSERT INTO financiamiento VALUES ('APORTES FIDES', true, false, '2');
INSERT INTO financiamiento VALUES ('MINFRA', false, false, '4');
INSERT INTO financiamiento VALUES ('TESORERIA MUNICIPAL', false, true, '1');


--
-- Data for Name: gacetas; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO gacetas VALUES ('1', 'PRESUPUESTO DE GASTOS');
INSERT INTO gacetas VALUES ('2', 'POLITICA PRESUPUESTARIA');
INSERT INTO gacetas VALUES ('3', 'PRESUPUESTO DE INGRESOS');
INSERT INTO gacetas VALUES ('4', 'DISPOSICIONES GENERALES');


--
-- Data for Name: grupos_proveedores; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO grupos_proveedores VALUES ('1919', 'Sanitarios', 'Sanitarios', '2006-03-08');
INSERT INTO grupos_proveedores VALUES ('7979', 'Alimento', 'Alimento', '2006-03-08');
INSERT INTO grupos_proveedores VALUES ('3131', 'Salud', 'Salud', '2006-03-08');
INSERT INTO grupos_proveedores VALUES ('1515', 'Obras Civiles', 'Obras Civiles', '2006-03-08');
INSERT INTO grupos_proveedores VALUES ('0000U', 'Textiles', 'Textiles', '2006-03-10');
INSERT INTO grupos_proveedores VALUES ('1235', 'Papeleria', 'fasfaf', '2006-03-31');
INSERT INTO grupos_proveedores VALUES ('3255', 'Asistencia Médica', 'Asistencia Médica', '2006-03-31');
INSERT INTO grupos_proveedores VALUES ('11322', 'Equipos de Informática', '', '2006-04-03');
INSERT INTO grupos_proveedores VALUES ('0123', 'Grupo Prueba', 'Grupo Prueba', '2006-04-03');
INSERT INTO grupos_proveedores VALUES ('001', 'Grupo Prueba2', 'Grupo Prueba2', '2006-04-03');
INSERT INTO grupos_proveedores VALUES ('00001', 'asdfg', 'zzgsfg', '2006-04-20');
INSERT INTO grupos_proveedores VALUES ('xxx_001', 'Grupo G', 'Grupo G_', '2006-04-20');


--
-- Data for Name: id_grupo_prove; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO id_grupo_prove VALUES (1, 'ALIENTOS');
INSERT INTO id_grupo_prove VALUES (3, 'ALFARERIAS ');
INSERT INTO id_grupo_prove VALUES (2, 'AGENCIAS');


--
-- Data for Name: manejo_almacen_producto; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO manejo_almacen_producto VALUES (1, 'S');
INSERT INTO manejo_almacen_producto VALUES (2, 'N');


--
-- Data for Name: modulos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO modulos VALUES (2, 'Gestión Financiera', 2);
INSERT INTO modulos VALUES (3, 'Gestión Nómina', 3);
INSERT INTO modulos VALUES (4, 'Gestión Tributaria', 4);
INSERT INTO modulos VALUES (5, 'Gestión Catastro', 5);
INSERT INTO modulos VALUES (6, 'Gestión Compras', 6);
INSERT INTO modulos VALUES (1, 'Gestión Presupuestaria', 1);
INSERT INTO modulos VALUES (7, 'Administración', 0);


--
-- Data for Name: momentos_presupuestarios; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO momentos_presupuestarios VALUES ('1', 'Compromiso');
INSERT INTO momentos_presupuestarios VALUES ('2', 'Causado');
INSERT INTO momentos_presupuestarios VALUES ('3', 'Pagado');
INSERT INTO momentos_presupuestarios VALUES ('4', 'Aumentos');
INSERT INTO momentos_presupuestarios VALUES ('5', 'Disminuciones');


--
-- Data for Name: movimientos_presupuestarios; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO movimientos_presupuestarios VALUES (135, '009-0001', '009', '', 'x', '2006-05-03', 'epasx', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (136, '009-0002', '009', '', 'DDD', '2006-05-04', 'yuhh', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (137, '009-0003', '009', '', 'ffffffffffff', '2006-05-05', 'dfghdf', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (138, '009-0004', '009', '', 'ffffffffffff', '2006-05-05', 'dfghdf', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (141, '009-0005', '009', '', '123', '2006-05-05', 'observacion1', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (145, '001-0002', '001', '', '0', '2006-05-05', 'observ', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (146, '001-0003', '001', '', '0', '2006-05-05', 'observ', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (147, '001-0004', '001', '', '0', '2006-05-05', 'observ', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (148, '001-0005', '001', '', '0', '2006-05-05', 'observ', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (149, '001-0006', '001', '', '0', '2006-05-05', 'observ', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (150, '001-0007', '001', '', '0', '2006-05-05', 'observacioneswwwwxcxcccc1', '1', '010152', '2006', '6', '2006-05-02', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (151, '001-0008', '001', '', '0', '2006-05-05', 'observ1', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (152, '001-0009', '001', '', '0', '2006-05-05', 'observ1', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (153, '001-0010', '001', '', '0', '2006-05-05', 'observ1', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (154, '001-0011', '001', '', '0', '2006-05-05', 'obserxxxx', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (155, '001-0012', '001', '', '0', '2006-05-05', 'obserxxxx', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (156, '001-0013', '001', '', '0', '2006-05-05', 'thdfgdfgsdfs', '1', '010152', '2006', '6', '2006-05-24', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (157, '001-0014', '001', '', '0', '2006-05-05', 'thdfgdfgsdfs', '1', '010152', '2006', '6', '2006-05-24', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (158, '001-0015', '001', '', '0', '2006-05-05', 'thdfgdfgsdfs', '1', '010152', '2006', '6', '2006-05-24', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (159, '001-0016', '001', '', '0', '2006-05-05', 'thdfgdfgsdfs', '1', '010152', '2006', '6', '2006-05-24', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (160, '010-0001', '010', '0', '1', '2006-05-08', 'ccx', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (161, '009-0006', '009', '', '11111', '2006-05-08', 'observacion', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (162, '009-0007', '009', '', '11111', '2006-05-08', 'observacion', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (163, '001-0017', '001', '', '0', '2006-05-08', 'observa', '1', '010152', '2006', '6', '2006-05-08', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (164, '001-0018', '001', '', '0', '2006-05-08', 'observa', '1', '010152', '2006', '6', '2006-05-08', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (165, '001-0019', '001', '', '0', '2006-05-09', 'observ1', '1', '010152', '2006', '6', '2006-05-01', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (166, '001-0020', '001', '', '0', '2006-05-09', 'Observaciones', '1', '010152', '2006', '6', '2006-05-09', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (167, '002-0001', '002', '', '222', '2006-05-09', 'aaaaa', '1', '010152', '2006', '6', '2006-05-09', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (168, '001-0021', '001', '', '0', '2006-05-09', 'Observaciones sdjflksdfjlsdkjf sdkjfklsdfjlsdkjfkl sdklfjklsdfjklsd sdjflksdjfklsd sjdlfjsdlkfjsd sdlfjsldkfjklsd sdljflsdkjflsdk sldjflsdkfj sdfjsdklfjskl sdfjlsdkjflsd sdjlfsdjlf sldjflsdkf sldjflksdjflksdjflksdjflkdsjfklsdj sdjflsdkjfklsdjfklsdjlfkjsdlkfj', '1', '010152', '2006', '6', '2006-05-09', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (169, '001-0022', '001', '', '0', '2006-05-10', 'obser', '1', '010152', '2006', '6', '2006-05-10', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (170, '011-0001', '011', '0', '0', '2006-05-11', 'ghghsdddd', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (171, '012-0001', '012', '0', '0', '2006-05-12', 'lorem ipsum dolor', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (172, '010-0002', '010', '0', '111', '2006-05-12', 'nomina', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (173, '001-0023', '001', '', '0', '2006-05-17', 'sfdfsd', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (174, '001-0024', '001', '', '0', '2006-05-17', 'sfdfsd', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (175, '001-0025', '001', '', '0', '2006-05-17', 'sfdfsd', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (176, '001-0026', '001', '', '0', '2006-05-17', 'sfdfsd', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (177, '001-0027', '001', '', '0', '2006-05-17', 'xczxczxc', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (178, '011-0002', '011', '0', '0', '2006-05-17', 'prueba', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (179, '012-0002', '012', '0', '0', '2006-05-17', 'dgdgdf', '1', '010153', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (180, '012-0003', '012', '0', '0', '2006-05-17', 'gh', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (181, '002-0002', '002', '', '3', '2006-05-17', '', '1', '010152', '2006', '6', '2006-05-17', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (182, '010-0003', '010', '0', '1', '2006-05-17', 'nomina', '1', '010153', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (183, '011-0003', '011', '0', '0', '2006-05-17', 'contrato', '1', '010152', '2006', '6', NULL, NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (184, '001-0028', '001', '008', '00000000', '2006-05-23', 'descripcion', '1', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (186, '013-0001', '013', '0', '0', '2006-05-24', 'hola', '1', '010152', '2006', '6', NULL, '15992657', 89);
INSERT INTO movimientos_presupuestarios VALUES (185, '001-0029', '001', '', '0', '2006-05-23', 'epa', '1', '010152', '2006', '6', '2006-05-23', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (139, '009-0111', '001', '', '', '2006-05-05', 'asdsd', '1', '010152', '2006', '6', '2006-05-05', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (187, '004-0001', '004', '013', '013-0001', '2006-06-01', 'orden de pago', '2', '010152', '2006', '6', '', NULL, 89);
INSERT INTO movimientos_presupuestarios VALUES (188, '004-0002', '004', '012', '012-0001', '2006-06-01', 'causado francisco', '2', '010152', '2006', '6', '', NULL, 89);


--
-- Data for Name: municipios; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO municipios VALUES (1, 1, 'LIBERTADOR');
INSERT INTO municipios VALUES (2, 1, 'SAN DIEGO');
INSERT INTO municipios VALUES (3, 1, 'NAGUANAGUA');
INSERT INTO municipios VALUES (4, 1, 'VALENCIA');


--
-- Data for Name: nomina; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO nomina VALUES (2, '010', '2006-05-08', NULL, 'cc', '2006-05-08', '6', '010152', 'J-34-4', NULL, NULL, NULL, 89);
INSERT INTO nomina VALUES (3, '010', '2006-05-08', NULL, 'ccssss', '2006-05-08', '6', '010152', 'J-34-4', NULL, NULL, NULL, 89);
INSERT INTO nomina VALUES (4, '010', '2006-05-08', '010-0001', 'cc', '2006-05-08', '6', '010152', 'J-34-4', '2006-05-08', '1111', NULL, 89);
INSERT INTO nomina VALUES (5, '010', '2006-05-08', NULL, 'nomina--.,+', '2006-05-10', '6', '010154', 'J-34-4', NULL, '1111111', NULL, 89);
INSERT INTO nomina VALUES (6, '010', '2006-05-12', '010-0002', 'nomina', '2006-05-12', '6', '010152', 'J-34-4', '2006-05-12', '111', NULL, 89);
INSERT INTO nomina VALUES (7, '010', '2006-05-17', NULL, 'nomina', '2006-05-17', '6', '010152', '89', NULL, '11', NULL, 89);
INSERT INTO nomina VALUES (8, '010', '2006-05-17', '010-0003', 'nomina', '2006-05-17', '6', '010153', NULL, '2006-05-17', '1', NULL, 86);
INSERT INTO nomina VALUES (1, '010', '2006-05-05', NULL, 'd', '2006-05-18', '6', '010152', 'J-34-4', NULL, '1', 'ddd', 89);


--
-- Data for Name: objeto_empresa; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO objeto_empresa VALUES (20, 61, 'ddddd joder ya era hora, jebus rthrtsfwwrf', NULL);
INSERT INTO objeto_empresa VALUES (21, 62, 'tela sfgsdfg', NULL);
INSERT INTO objeto_empresa VALUES (22, 86, 'avena ', NULL);
INSERT INTO objeto_empresa VALUES (23, 132, 'drogas', NULL);


--
-- Data for Name: obras; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO obras VALUES ('gfhjjjj', '1', '0', '1', 3, 6, 6, 6, '2006-02-17', '2006-02-17', 6, 6, 6, 1, 'fff', '333 ', 'ffff', 1, '');
INSERT INTO obras VALUES ('1', '03', '01', '2', 30, 11122, 55555, 63, '2004-05-02', '2004-05-02', 4, 5, 6, 1, 'aa', '2006', 'xxxxxxxxxxxxxxxxxxxxxxxx', 4, 'desc');


--
-- Data for Name: operaciones; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO operaciones VALUES (62, 'Formulación', '', '1', NULL, 'C', 1, NULL, 1);
INSERT INTO operaciones VALUES (63, 'Ejecución', '', '1', NULL, 'C', 1, NULL, 1);
INSERT INTO operaciones VALUES (64, 'Tablas Referenciales', '', '1', NULL, 'C', NULL, 62, 1);
INSERT INTO operaciones VALUES (65, 'Registros', '', '1', NULL, 'C', NULL, 62, 1);
INSERT INTO operaciones VALUES (66, 'Reportes', '', '1', NULL, 'C', NULL, 62, 1);
INSERT INTO operaciones VALUES (3, 'Alcaldia', 'alcaldia.php', '1', 1, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (1, 'Escenarios', 'escenarios.php', '1', 9, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (18, 'Partidas por Categorias', 'relacion_pp_cp.php', '1', 8, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (67, 'Tablas Referenciales', 'no tiene', NULL, NULL, 'C', NULL, 63, 1);
INSERT INTO operaciones VALUES (35, 'Usos', 'uso.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (29, 'Tipos de Documentos', 'tipos_documentos.php', '1', 17, 'V', NULL, 67, 1);
INSERT INTO operaciones VALUES (5, 'Categorías Programáticas', 'categorias_programaticas.php', '1', 5, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (9, 'Operaciones', 'operaciones.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (2, 'Cargos', 'cargos.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (13, 'Usuarios', 'usuarios.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (14, 'Operaciones por usuario', 'agrega_operaciones.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (28, 'Profesiones', 'profesiones.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (57, 'Condición de Pago', 'condicion_pago.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (58, 'Tipos de Fianza', 'tipos_fianzas.php', '3', NULL, 'V', 1, 0, 7);
INSERT INTO operaciones VALUES (4, 'Aprobar Escenario', 'aprobar_escenario.php', '1', 10, 'V', NULL, 0, 1);
INSERT INTO operaciones VALUES (8, 'Obras', 'obras.php', '1', 14, 'V', NULL, 65, 1);
INSERT INTO operaciones VALUES (15, 'Partidas Presupuestarias', 'partidas_presupuestarias.php', '1', 7, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (68, 'Registro', 'no tiene', NULL, NULL, 'C', NULL, 63, 1);
INSERT INTO operaciones VALUES (69, 'Registros', 'no tiene', NULL, NULL, 'C', 1, 0, 2);
INSERT INTO operaciones VALUES (46, 'Movimientos Presupuestarios', 'movimientos_presupuestarios.php', '1', 19, 'V', NULL, 68, 1);
INSERT INTO operaciones VALUES (51, 'Orden de Compra', 'ordcompra.php', '1', 15, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (52, 'Orden de Servicio / Trabajo', 'orden_servicio_trabajo.php', '1', 16, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (53, 'Nómina', 'nomina.php', '1', 18, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (61, 'Contrato de Servicio', 'contrato_servicio.php', '1', NULL, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (60, 'Contrato de Obras', 'contrato_obras.php', '1', NULL, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (70, 'Vehículo', 'no tiene', NULL, NULL, 'C', 1, 0, 4);
INSERT INTO operaciones VALUES (59, 'Propaganda y Espectaculos', 'publicidad.php', '5', NULL, 'C', 1, 0, 4);
INSERT INTO operaciones VALUES (71, 'Registro Único de Proveedores', 'no tiene', NULL, NULL, 'C', 1, 0, 6);
INSERT INTO operaciones VALUES (72, 'Tablas Referenciales', 'no tiene', NULL, NULL, 'C', 1, 0, 6);
INSERT INTO operaciones VALUES (23, 'Tipos de Producto', 'tipo_producto.php', '2', NULL, 'V', NULL, 72, 6);
INSERT INTO operaciones VALUES (24, 'Productos', 'productos.php', '2', NULL, 'V', NULL, 72, 6);
INSERT INTO operaciones VALUES (11, 'Proveedores', 'proveedores.php', '2', NULL, 'V', NULL, 71, 6);
INSERT INTO operaciones VALUES (10, 'Requisitos', 'requisitos.php', '2', NULL, 'V', NULL, 72, 6);
INSERT INTO operaciones VALUES (12, 'Grupos de Proveedores', 'grupos_proveedores.php', '2', NULL, 'V', NULL, 72, 6);
INSERT INTO operaciones VALUES (73, 'Tablas Referenciales', 'no tiene', NULL, NULL, 'C', NULL, 70, 4);
INSERT INTO operaciones VALUES (31, 'Colores', 'color.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (32, 'Marcas', 'marca.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (33, 'Modelos', 'modelo.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (34, 'Tipos', 'tipo.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (36, 'Motivos de Desincorporación', 'desincorporacion.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (37, 'Motivos de Exoneración', 'exoneracion.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (38, 'Lineas', 'linea.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (39, 'Sanciones', 'sancion.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (41, 'Costo Solvencia/Fecha', 'solvencia.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (42, 'Descuentos por Dia (%)', 'descuento.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (43, 'Tasas Bancarias', 'tasa_bancaria.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (44, 'Tipo Veh Según Gaceta', 'tipo_vehiculo.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (45, 'Costo Vehículo (Especificaciones)', 'costo_vehiculo.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (47, 'Matto Vehículo', 'vehiculo.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (48, 'Matto Contribuyente', 'contribuyente.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (49, 'Tasas de Inscripción', 'tasa_inscripcion.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (50, 'Costo Calcomanías', 'costo_calcomania.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (54, 'Formas de PAgo', 'forma_pago.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (55, 'Ramo', 'ramo_imp.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (74, 'Bancos', 'banco.php', NULL, NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (56, 'Tipo Transacciones', 'tipo_transaccion.php', '4', NULL, 'V', NULL, 73, 4);
INSERT INTO operaciones VALUES (30, 'Lista Proveedores [Actualizar]', 'lista_proveedores.php', '2', NULL, 'V', NULL, 72, 6);
INSERT INTO operaciones VALUES (19, 'Politicas / Disposiciones', 'politicas_disposiciones.php', '1', 2, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (17, 'Categorias por Unidad', 'relacion_ue_cp.php', '1', 6, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (26, 'Organismos', 'organismos.php', '1', 11, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (27, 'Parroquias', 'parroquias.php', '1', 13, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (16, 'Unidad Ejecutora', 'unidades_ejecutoras.php', '1', 3, 'V', NULL, 64, 1);
INSERT INTO operaciones VALUES (6, 'Financiamiento', 'financiamiento.php', '1', 12, 'V', NULL, 65, 1);
INSERT INTO operaciones VALUES (76, 'Caja Chica', 'caja_chica.php', NULL, NULL, 'V', NULL, 69, 2);
INSERT INTO operaciones VALUES (78, 'Solicitud de Pago', 'no tiene', NULL, NULL, 'C', 1, 0, 2);
INSERT INTO operaciones VALUES (77, 'Tipos de Solicitudes', 'tipos_solicitudes.php', NULL, NULL, 'V', NULL, 79, 2);
INSERT INTO operaciones VALUES (79, 'Tablas Referenciales', 'no tiene', NULL, NULL, 'C', 1, 0, 2);
INSERT INTO operaciones VALUES (81, 'Solicitud de Pago', 'solicitud_pago.php', NULL, NULL, 'V', NULL, 78, 2);
INSERT INTO operaciones VALUES (83, 'Tipos de Solicitud sin Imputación', 'tipos_solicitud_sin_imp.php', NULL, NULL, 'V', NULL, 79, 2);
INSERT INTO operaciones VALUES (84, 'Retenciones y Adiciones', 'retenciones_adiciones.php', NULL, NULL, 'V', NULL, 79, 2);
INSERT INTO operaciones VALUES (82, 'Condiciones de Pago', 'condiciones_pago.php', NULL, NULL, 'V', NULL, 79, 2);


--
-- Data for Name: orden_compra; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO orden_compra VALUES (41, '2006-05-05', 2006, '2006-05-05', 'lugent', 'formpa', '2006-05-05', '000002', 'observaciones', '131', '010152', NULL, NULL);
INSERT INTO orden_compra VALUES (45, '2006-05-05', 2006, '2006-05-05', 'lugent', 'formpa', '2006-05-05', '000003', 'observacion5', '131', '010152', NULL, NULL);
INSERT INTO orden_compra VALUES (46, '2006-05-05', 2006, '2006-05-05', 'lugent', 'formpa', '2006-05-05', '001-0006', 'observ', '131', '010152', '2006-05-05', NULL);
INSERT INTO orden_compra VALUES (40, '2006-05-05', 2006, '2006-05-02', 'lugent', 'formpa', '2006-05-02', '001-0007', 'observacioneswwwwxcxcccc1', '131', '010152', '2006-05-05', NULL);
INSERT INTO orden_compra VALUES (48, '2006-05-05', 2006, '2006-05-05', 'lugent', 'formpa', '2006-05-05', '001-0010', 'observ1', '132', '010152', '2006-05-05', NULL);
INSERT INTO orden_compra VALUES (49, '2006-05-05', 2006, '2006-05-05', 'lugent', 'formpa', '2006-05-05', '001-0012', 'obserxxxx', '131', '010152', '2006-05-05', NULL);
INSERT INTO orden_compra VALUES (50, '2006-05-05', 2006, '2006-05-24', 'ghcgfvxsdf', 'hjgfhfh', '2006-05-02', '001-0016', 'thdfgdfgsdfs', '131', '010152', '2006-05-05', NULL);
INSERT INTO orden_compra VALUES (51, '2006-05-08', 2006, '2006-05-08', 'lugent', 'formpa', '2006-05-08', '001-0018', 'observa', '131', '010152', '2006-05-08', NULL);
INSERT INTO orden_compra VALUES (60, '2006-05-09', 2006, '2006-05-09', 'lugent', 'formpa', '2006-05-09', '001-0021', 'Observaciones sdjflksdfjlsdkjf sdkjfklsdfjlsdkjfkl sdklfjklsdfjklsd sdjflksdjfklsd sjdlfjsdlkfjsd sdlfjsldkfjklsd sdljflsdkjflsdk sldjflsdkfj sdfjsdklfjskl sdfjlsdkjflsd sdjlfsdjlf sldjflsdkf sldjflksdjflksdjflksdjflkdsjfklsdj sdjflsdkjfklsdjfklsdjlfkjsdlkfj', '132', '010152', '2006-05-09', '00001');
INSERT INTO orden_compra VALUES (61, '2006-05-10', 2006, '2006-05-10', 'lugent', 'formapago', '2006-05-10', '001-0022', 'obser', '131', '010152', '2006-05-10', '00002');
INSERT INTO orden_compra VALUES (62, '2006-05-17', 2006, '2006-05-17', 'lugent', 'formpa', '2006-05-17', '001-0024', 'sfdfsd', '131', '010152', '2006-05-17', '000011');
INSERT INTO orden_compra VALUES (65, '2006-05-22', 2006, '2006-05-22', 'lugent', 'formpa', '2006-05-22', NULL, 'asdas', '131', '010152', NULL, '0000112');
INSERT INTO orden_compra VALUES (68, '2006-05-23', 2006, '2006-05-23', 'x', 'x', '2006-05-23', '001-0029', '', '89', '010152', '2006-05-23', '');


--
-- Data for Name: orden_servicio_trabajo; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO orden_servicio_trabajo VALUES (26, '009', '010152', '2006-05-04', 'DD', 'DDD', 'J-34-4', 'yuhh', 'DDD', NULL, 'F', 'FF', '2006-05-04', 'FFF', 'F', '2006-05-04', '009-0002', '15218288', NULL, '2006-05-04', 89);
INSERT INTO orden_servicio_trabajo VALUES (24, '009', '010152', '2006-05-03', 'x', 'x', 'J-34-4', 'epasx', 'x', NULL, 'x', 'x', '2006-05-03', 'x', 'x', '2006-05-03', '009-0001', '13789643', NULL, '2006-05-03', 89);
INSERT INTO orden_servicio_trabajo VALUES (27, '009', '010152', '2006-05-05', 'gfffffffffffffffff', 'fffffffffffffffff', '0', 'dfghdf', 'ffffffffffff', NULL, '', '', '2006-05-05', 'gggggggggggggggggggggggg', '', '2006-05-05', NULL, '', NULL, NULL, 89);
INSERT INTO orden_servicio_trabajo VALUES (29, '009', '010152', '2006-05-05', 'lugent', 'condpag', '', 'observacion1', '123', NULL, '1', '0001', '2006-05-05', 'ning', '1', '2006-05-05', '009-0005', '13381452', NULL, '2006-05-05', 89);
INSERT INTO orden_servicio_trabajo VALUES (31, '002', '010152', '2006-05-09', '22', '222', '89', 'aaaaa...', '222', NULL, '222', '222', '2006-05-09', 'sss', '222', '2006-05-09', '002-0001', '', '6', '2006-05-09', 89);
INSERT INTO orden_servicio_trabajo VALUES (30, '009', '010152', '2006-05-08', 'valencia', 'no', '0', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', '11111', NULL, '111', ' 1111', '2006-05-08', 'orden', '112', '2006-05-08', '009-0007', '13789643', '6', '2006-05-08', 89);
INSERT INTO orden_servicio_trabajo VALUES (33, '002', '010152', '2006-05-17', 'valencia', '3', NULL, 'yyy', '3', NULL, '3', '3', '2006-05-17', 'condiciones', '3', '2006-05-17', '002-0002', '', '6', '2006-05-17', 89);
INSERT INTO orden_servicio_trabajo VALUES (34, '002', '010152', '2006-05-23', 'test', 'test', NULL, 'hola', 'test', NULL, '2', 'v', '2006-05-23', 'test', '2', '2006-05-23', NULL, '', '6', NULL, 89);


--
-- Data for Name: organismos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO organismos VALUES ('001', '2006', 'TEST1');


--
-- Data for Name: parroquias; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO parroquias VALUES ('Parroqua 3', '3', NULL);
INSERT INTO parroquias VALUES ('Miguel Peña', '0001', '1');
INSERT INTO parroquias VALUES ('Rafael Urdaneta', '0002', NULL);
INSERT INTO parroquias VALUES ('Parroquia 2', '0003', NULL);
INSERT INTO parroquias VALUES ('Carabobo', '0004', '2');
INSERT INTO parroquias VALUES ('CARLOS ARVELO', '020', '4');


--
-- Data for Name: partidas_presupuestarias; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO partidas_presupuestarias VALUES ('010101', '2006', 'Prueba', '', false, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006');
INSERT INTO partidas_presupuestarias VALUES ('4010000000000', '2006', 'SUELDOS Y SALARIOS', '', true, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006');
INSERT INTO partidas_presupuestarias VALUES ('010102', '2006', 'prueba1', 'prueba1', false, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006');
INSERT INTO partidas_presupuestarias VALUES ('010103', '2006', 'sdf', '', true, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006');


--
-- Data for Name: politicas_disposiciones; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO politicas_disposiciones VALUES ('2006', '1', '2005', 'hjjjxd', 'zxbvxcz', '', '', 6);
INSERT INTO politicas_disposiciones VALUES ('2006', '1', '2006', 'sddde ddddd', 'n', '', '', 2);
INSERT INTO politicas_disposiciones VALUES ('2006', '1', '2005', '1.- Inicio del proceso.

2.- El contribuyente solicita los requisitos para la patente de industria y comercio los cuales son entregados por la Supervisora de Registro y Tributación (ver planilla de requisitos). 
Nota: Uno de los requisitos es el Uso Conforme Original y se le notifica que deben tramitarlo por la oficina de  Planeamiento Urbano.

3.- Luego el contribuyente se dirige a la caja a comprar la Planilla de Solicitud de Licencia de Industria y Comercio (ver planilla), la cual tiene un costo de 2000 Bs.

4.- Se debería cargar los datos en el sistema pero debido a que este es un proceso totalmente manual, se le indica al contribuyente como debe llenar la planilla.

5.- Se verifican los datos en la planilla de solicitud y que posea todos los requisitos, estos son anexados a la planilla. Se abre el expediente al contribuyente en una  carpeta, archivo físico, con el nombre de la empresa

6.- Se envía un fiscal para que verifique  la actividad, si tiene propaganda comercial, si tiene todos los servicios públicos, etc.
', '', '2', '43', 1);


--
-- Data for Name: productos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO productos VALUES (14, 9, 'papel', NULL, NULL, NULL, NULL, 'cm', 's', 's', 's', 's', 's', 's', 2, 2, 1, '', '', 1250, '2006-04-20');
INSERT INTO productos VALUES (15, 8, 'Cajas', NULL, NULL, NULL, NULL, 'cm', '1', '1', '1', '1', '1', '1', 2, 2, 1, '1', '1', 1552225.5, '2006-04-20');
INSERT INTO productos VALUES (3, 8, 'Papel', NULL, NULL, NULL, NULL, 'Rollos', '12', '21', '12', '12', '21', '12', 2, 1, 0, '', '', 4000, '2006-03-02');
INSERT INTO productos VALUES (2, 10, 'pasta dental', '5', '1', 2, 'n', 'Cm3', 'dhsdfgd', 'sghdg', '', '', '', '', 0, 0, 0, '', '', 4000, '2006-04-03');
INSERT INTO productos VALUES (4, 2, 'Champu', NULL, NULL, NULL, NULL, 'Lts', 'rop', 'roq', 'mini', 'max', 'ubic', 'ctd act', 1, 1, NULL, 'std', 'prm', 5000, '2006-02-23');
INSERT INTO productos VALUES (1, 0, 'Jabón', '2', '0', 1, 's', 'Unidades', 'rop', 'roq', 'ctd minimo', 'ctd maxi', 'ubic fisc', 'ctd actual', 2, 2, 1, 'costo st', 'costo_prm', 2000, '2006-04-07');
INSERT INTO productos VALUES (16, 1, 'Jabon Hipoalergenico a base de Glicerina de uso externo y bla bla bla', '1', '1', 1, '1', 'Unidades', '1', '1', '1', '1', '1', '1', 1, 1, 1, '1', '1', 10000, '2006-05-09');


--
-- Data for Name: profesiones; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO profesiones VALUES ('01', 'Ingeniero Industrial');
INSERT INTO profesiones VALUES ('02', 'Licenciado');
INSERT INTO profesiones VALUES ('04', 'Liocenciado en Administración');
INSERT INTO profesiones VALUES ('03 ', 'Ingeniero en Informática');


--
-- Data for Name: provee_contrat; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO provee_contrat VALUES ('C', 'Contratista');
INSERT INTO provee_contrat VALUES ('P', 'Proveedor');


--
-- Data for Name: provee_contrib_munic; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO provee_contrib_munic VALUES ('S', 'Si');
INSERT INTO provee_contrib_munic VALUES ('N', 'No');


--
-- Data for Name: proveedores; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO proveedores VALUES ('ABCD', '10/03/2006', 'J', '34', '4', '34', '34', 'Tocuyito frente a la plaza de tocuyito de dos locales de la alcaldia Libertador', 0, 0, 0, '0', '0', 'P', 'e', 'e                   ', 'e', 'e', 'e', 'r', '2', '2', 's', '2', '2', '2', '2', NULL, NULL, '0000U', 89, '10/03/2006', 'J-34-4');
INSERT INTO proveedores VALUES ('Quaker', '08/03/2006', 'J', '12341234', '3', '3424', '13', 'Av. Henry Ford', 2, 2, 2, 'N', 'C', 'P', 'datos reg', '7508558             ', 'abecedario', 'afsd', 'jebus, jabel.... jaser', 'perfilio', '234', '4535', 'a@fff.com', '234', 'ff', '234', 'constantine', NULL, NULL, '1515', 86, '08/03/2006', 'J-12341234-3');
INSERT INTO proveedores VALUES ('Farmacia la Torre', '03/04/2006', 'J', '12548966', '4', '1256387', '40', 'Lateral a la Plaza Bolivar de Valencia Edo. Carabobo.', 1, 1, 2, 'S', 'P', 'A', 'hgagdfag', 'sdfgag              ', 'ggggggggggggsa', 'asg', 'gasd', 'saaaaaaaaaaaaaaaaaa', '0241-8285769', '8285769', 'FAermatorre@cantev.com', 'c vcv', 'igyyift', 'tjtd', 'jstyuj', NULL, NULL, '3255', 132, '03/04/2006', 'J-12548966-4');
INSERT INTO proveedores VALUES ('Panaderia Libertador', '31/03/2006', 'J', '4560578', 'p', '4569872', '9', 'Tocuyito frente a la plaza de tocuyito de dos locales de la alcaldia Libertador', 1, 1, 3, 'N', 'P', 'A', 'lkafjdfklafasdfg45455', '                    ', 'hghdhhhhh', 'dhth', 'dhhhhdhdsz', 'hdzshshd', 'zsdhzh', 'zhdh', 'zhgcd', 'hzdghdz', 'zdhhhhhhhhhhhh', 'dhgzdh', 'zhhhhhhhhhhhhh', NULL, NULL, '7979', 131, '31/03/2006', 'J-4560578-p');
INSERT INTO proveedores VALUES ('julio calvo', '18/04/2006', 'J', '15529540', '1', '154745', '', 'urb. lw. virginia', 0, 0, 0, 'N', 'C', 'A', '', '15529540            ', 'se7h', '', 'jc, mm', 'jc, mm', '212-877875', '', '', '', '', '', '', 1599756.5900000001, 1599756.5900000001, '1919', 133, '18/04/2006', 'J-15529540-1');


--
-- Data for Name: relacion_caja_chica; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_caja_chica VALUES (6, 42, '3', '01010101', '010101', 150);
INSERT INTO relacion_caja_chica VALUES (7, 47, '3', '01010101', '010103', 5);
INSERT INTO relacion_caja_chica VALUES (8, 49, '3', '010010102', '010101', 200);


--
-- Data for Name: relacion_contrato_obras; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_contrato_obras VALUES (10, 43, '2', '010010102', '010102', 255555.22);
INSERT INTO relacion_contrato_obras VALUES (13, 43, '3', '010010102', '010102', 3333.3299999999999);
INSERT INTO relacion_contrato_obras VALUES (14, 42, '1', '01010101', '010101', 4444.4399999999996);


--
-- Data for Name: relacion_contrato_servicio; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_contrato_servicio VALUES (3, 43, '1', '010010102', '010102', 44444.440000000002);
INSERT INTO relacion_contrato_servicio VALUES (4, 43, '2', '010010102', '010102', 222222.22);
INSERT INTO relacion_contrato_servicio VALUES (6, 42, '3', '01010101', '010101', 10000);


--
-- Data for Name: relacion_movimientos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_movimientos VALUES (126, '009-0001', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (127, '009-0002', '01010101', '010101', 14250, NULL);
INSERT INTO relacion_movimientos VALUES (128, '009-0003', '01010101', '010101', 0, NULL);
INSERT INTO relacion_movimientos VALUES (129, '009-0004', '01010101', '010101', 0, NULL);
INSERT INTO relacion_movimientos VALUES (130, '-0001', '010101', '22800', 3, NULL);
INSERT INTO relacion_movimientos VALUES (131, '-0001', '010102', '22800', 4, NULL);
INSERT INTO relacion_movimientos VALUES (132, '009-0005', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (135, '001-0004', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (136, '001-0004', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (137, '001-0005', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (138, '001-0005', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (139, '001-0006', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (140, '001-0006', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (141, '001-0007', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (142, '001-0008', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (143, '001-0008', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (144, '001-0009', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (145, '001-0009', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (146, '001-0010', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (147, '001-0010', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (148, '001-0011', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (149, '001-0011', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (150, '001-0012', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (151, '001-0012', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (152, '001-0013', '010010102', '010102', 1425, NULL);
INSERT INTO relacion_movimientos VALUES (153, '001-0014', '010010102', '010102', 1425, NULL);
INSERT INTO relacion_movimientos VALUES (154, '001-0015', '010010102', '010102', 1425, NULL);
INSERT INTO relacion_movimientos VALUES (155, '001-0016', '010010102', '010102', 1425, NULL);
INSERT INTO relacion_movimientos VALUES (156, '010-0001', '0', '4010000000000', 5000, NULL);
INSERT INTO relacion_movimientos VALUES (157, '009-0006', '01010101', '010101', 1792337.0700000001, NULL);
INSERT INTO relacion_movimientos VALUES (158, '009-0007', '01010101', '010101', 1792337.0700000001, NULL);
INSERT INTO relacion_movimientos VALUES (159, '001-0017', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (160, '001-0017', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (161, '001-0018', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (162, '001-0018', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (163, '001-0019', '01010101', '010103', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (164, '001-0019', '01010102', '4010000000000', 45600, NULL);
INSERT INTO relacion_movimientos VALUES (165, '001-0020', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (166, '002-0001', '01010101', '010101', 114000, NULL);
INSERT INTO relacion_movimientos VALUES (167, '001-0021', '01010101', '010103', 2280000, NULL);
INSERT INTO relacion_movimientos VALUES (168, '001-0021', '01010101', '010101', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (169, '001-0021', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (170, '001-0022', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (171, '011-0001', '01010101', '010101', 4444.4399999999996, NULL);
INSERT INTO relacion_movimientos VALUES (172, '012-0001', '010010102', '010102', 44444.440000000002, NULL);
INSERT INTO relacion_movimientos VALUES (173, '010-0002', '01010102', '4010000000000', 55555.550000000003, NULL);
INSERT INTO relacion_movimientos VALUES (174, '001-0023', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (175, '001-0024', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (176, '001-0025', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (177, '001-0026', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (178, '001-0027', '010010102', '010102', 22800, NULL);
INSERT INTO relacion_movimientos VALUES (179, '011-0002', '010010102', '010102', 255555.22, NULL);
INSERT INTO relacion_movimientos VALUES (180, '012-0002', '010010102', '010102', 222222.22, NULL);
INSERT INTO relacion_movimientos VALUES (181, '012-0003', '01010101', '010101', 10000, NULL);
INSERT INTO relacion_movimientos VALUES (182, '002-0002', '010010102', '010102', 14250, NULL);
INSERT INTO relacion_movimientos VALUES (183, '010-0003', '01010102', '4010000000000', 544.44000000000005, NULL);
INSERT INTO relacion_movimientos VALUES (184, '011-0003', '010010102', '010102', 3333.3299999999999, NULL);
INSERT INTO relacion_movimientos VALUES (185, '001-0028', '010010102', '010102', 1000, NULL);
INSERT INTO relacion_movimientos VALUES (186, '001-0028', '01010101', '010101', 20000, NULL);
INSERT INTO relacion_movimientos VALUES (187, '001-0029', '010010102', '010101', 1425, NULL);
INSERT INTO relacion_movimientos VALUES (188, '013-0001', '01010101', '010101', 150, NULL);
INSERT INTO relacion_movimientos VALUES (189, '013-0001', '01010101', '010103', 5, NULL);
INSERT INTO relacion_movimientos VALUES (190, '013-0001', '010010102', '010101', 200, NULL);
INSERT INTO relacion_movimientos VALUES (191, '004-0001', '01010101', '010101', 140, NULL);
INSERT INTO relacion_movimientos VALUES (192, '004-0001', '01010101', '010103', 5, NULL);
INSERT INTO relacion_movimientos VALUES (193, '004-0001', '010010102', '010101', 150, NULL);
INSERT INTO relacion_movimientos VALUES (194, '004-0002', '010010102', '010102', 50000, NULL);
INSERT INTO relacion_movimientos VALUES (133, '001-0003', '01010101', '010101', 22800, 42);
INSERT INTO relacion_movimientos VALUES (134, '001-0003', '010010102', '010102', 22800, 43);


--
-- Data for Name: relacion_movimientos_productos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_movimientos_productos VALUES (9, '009-0001', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (10, '009-0002', '14', '10', 1250, 1750, 12500);
INSERT INTO relacion_movimientos_productos VALUES (11, '009-0005', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (12, '001-0003', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (13, '001-0003', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (14, '001-0004', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (15, '001-0004', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (16, '001-0005', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (17, '001-0005', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (18, '001-0006', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (19, '001-0006', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (20, '001-0007', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (21, '001-0008', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (22, '001-0008', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (23, '001-0009', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (24, '001-0009', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (25, '001-0010', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (26, '001-0010', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (27, '001-0011', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (28, '001-0011', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (29, '001-0012', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (30, '001-0012', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (31, '001-0013', '14', '1', 1250, 175, 1250);
INSERT INTO relacion_movimientos_productos VALUES (32, '001-0014', '14', '1', 1250, 175, 1250);
INSERT INTO relacion_movimientos_productos VALUES (33, '001-0015', '14', '1', 1250, 175, 1250);
INSERT INTO relacion_movimientos_productos VALUES (34, '001-0016', '14', '1', 1250, 175, 1250);
INSERT INTO relacion_movimientos_productos VALUES (35, '009-0006', '14', '10', 2000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (36, '009-0006', '15', '1', 1552225.5, 217311.57000000001, 1552225.5);
INSERT INTO relacion_movimientos_productos VALUES (37, '009-0007', '14', '10', 2000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (38, '009-0007', '15', '1', 1552225.5, 217311.57000000001, 1552225.5);
INSERT INTO relacion_movimientos_productos VALUES (39, '001-0017', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (40, '001-0017', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (41, '001-0018', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (42, '001-0018', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (43, '001-0019', '2', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (44, '001-0019', '4', '8', 5000, 5600, 40000);
INSERT INTO relacion_movimientos_productos VALUES (45, '001-0020', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (46, '002-0001', '16', '10', 10000, 14000, 100000);
INSERT INTO relacion_movimientos_productos VALUES (47, '001-0021', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (48, '001-0021', '4', '4', 5000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (49, '001-0021', '16', '200', 10000, 280000, 2000000);
INSERT INTO relacion_movimientos_productos VALUES (50, '001-0022', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (51, '001-0023', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (52, '001-0024', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (53, '001-0025', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (54, '001-0026', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (55, '001-0027', '3', '5', 4000, 2800, 20000);
INSERT INTO relacion_movimientos_productos VALUES (56, '002-0002', '14', '10', 1250, 1750, 12500);
INSERT INTO relacion_movimientos_productos VALUES (57, '001-0029', '14', '1', 1250, 175, 1250);


--
-- Data for Name: relacion_nomina; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_nomina VALUES (7, '5', '01010102', '4010000000000', 5000, 44);
INSERT INTO relacion_nomina VALUES (8, '4', '01010102', '4010000000000', 3000, 44);
INSERT INTO relacion_nomina VALUES (9, '6', '01010102', '4010000000000', 55555.550000000003, 44);
INSERT INTO relacion_nomina VALUES (10, '7', '01010102', '4010000000000', 2222.2199999999998, 44);
INSERT INTO relacion_nomina VALUES (11, '8', '01010102', '4010000000000', 544.44000000000005, 44);
INSERT INTO relacion_nomina VALUES (13, '1', '0', '0', 0, 0);


--
-- Data for Name: relacion_obras; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: relacion_ord_serv_trab; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_ord_serv_trab VALUES (25, '29', '010010102', '010102', 22800, 0);
INSERT INTO relacion_ord_serv_trab VALUES (24, '28', '01010101', '010101', 0, 0);
INSERT INTO relacion_ord_serv_trab VALUES (23, '27', '01010101', '010101', 0, 0);
INSERT INTO relacion_ord_serv_trab VALUES (22, '26', '01010101', '010101', 14250, 0);
INSERT INTO relacion_ord_serv_trab VALUES (20, '25', '01010102', '4010000000000', 4560, 0);
INSERT INTO relacion_ord_serv_trab VALUES (19, '24', '01010101', '010101', 22800, 0);
INSERT INTO relacion_ord_serv_trab VALUES (29, '31', '01010101', '010101', 114000, 42);
INSERT INTO relacion_ord_serv_trab VALUES (32, '32', '010010102', '010102', 2850, 43);
INSERT INTO relacion_ord_serv_trab VALUES (33, '30', '01010101', '010101', 1792337.0700000001, 41);
INSERT INTO relacion_ord_serv_trab VALUES (35, '33', '010010102', '010102', 14250, 43);
INSERT INTO relacion_ord_serv_trab VALUES (36, '34', '010010102', '010102', 14250, 43);


--
-- Data for Name: relacion_ord_serv_trab_productos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_ord_serv_trab_productos VALUES (20, '24', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ord_serv_trab_productos VALUES (21, '25', '3', 1, 4000, 560, 4000);
INSERT INTO relacion_ord_serv_trab_productos VALUES (23, '26', '14', 10, 1250, 1750, 12500);
INSERT INTO relacion_ord_serv_trab_productos VALUES (24, '29', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ord_serv_trab_productos VALUES (29, '31', '16', 10, 10000, 14000, 100000);
INSERT INTO relacion_ord_serv_trab_productos VALUES (34, '32', '14', 2, 1250, 350, 2500);
INSERT INTO relacion_ord_serv_trab_productos VALUES (35, '30', '14', 10, 2000, 2800, 20000);
INSERT INTO relacion_ord_serv_trab_productos VALUES (36, '30', '15', 1, 1552225.5, 217311.57000000001, 1552225.5);
INSERT INTO relacion_ord_serv_trab_productos VALUES (38, '33', '14', 10, 1250, 1750, 12500);
INSERT INTO relacion_ord_serv_trab_productos VALUES (39, '34', '14', 10, 1250, 1750, 12500);


--
-- Data for Name: relacion_ordcompra; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_ordcompra VALUES (27, '40', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (32, '000001', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (33, '41', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (34, '41', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (35, '42', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (36, '42', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (37, '000002', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (38, '000002', '010010102', '0', 22800);
INSERT INTO relacion_ordcompra VALUES (39, '43', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (40, '43', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (41, '44', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (42, '44', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (76, '000003', '01010102', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (77, '000003', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (78, '45', '01010101', '010103', 22800);
INSERT INTO relacion_ordcompra VALUES (79, '45', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (80, '46', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (81, '46', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (82, '47', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (83, '47', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (84, '48', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (85, '48', '01010101', '010103', 22800);
INSERT INTO relacion_ordcompra VALUES (86, '49', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (87, '49', '01010101', '010103', 22800);
INSERT INTO relacion_ordcompra VALUES (88, '50', '010010102', '010102', 1425);
INSERT INTO relacion_ordcompra VALUES (89, '51', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (90, '51', '01010101', '010103', 22800);
INSERT INTO relacion_ordcompra VALUES (102, '52', '01010101', '010103', 22800);
INSERT INTO relacion_ordcompra VALUES (103, '52', '01010102', '4010000000000', 45600);
INSERT INTO relacion_ordcompra VALUES (120, '60', '01010101', '010103', 2280000);
INSERT INTO relacion_ordcompra VALUES (121, '60', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (122, '60', '01010101', '010101', 22800);
INSERT INTO relacion_ordcompra VALUES (123, '61', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (124, '62', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (125, '63', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (126, '64', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (127, '65', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (128, '66', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (129, '67', '010010102', '010102', 22800);
INSERT INTO relacion_ordcompra VALUES (130, '68', '010010102', '010101', 1425);


--
-- Data for Name: relacion_ordcompra_prod; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_ordcompra_prod VALUES (28, '40', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (29, '44', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (30, '44', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (49, '000003', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (50, '000003', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (51, '45', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (52, '45', '1', 10, 2000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (53, '46', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (54, '46', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (55, '47', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (56, '47', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (57, '48', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (58, '48', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (59, '49', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (60, '49', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (61, '50', '14', 1, 1250, 175, 1250);
INSERT INTO relacion_ordcompra_prod VALUES (62, '51', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (63, '51', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (75, '52', '2', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (76, '52', '4', 8, 5000, 5600, 40000);
INSERT INTO relacion_ordcompra_prod VALUES (93, '60', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (94, '60', '16', 200, 10000, 280000, 2000000);
INSERT INTO relacion_ordcompra_prod VALUES (95, '60', '4', 4, 5000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (96, '61', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (97, '62', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (98, '63', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (99, '64', '3', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (100, '65', '2', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (101, '66', '2', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (102, '67', '2', 5, 4000, 2800, 20000);
INSERT INTO relacion_ordcompra_prod VALUES (103, '68', '14', 1, 1250, 175, 1250);


--
-- Data for Name: relacion_pp_cp; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_pp_cp VALUES ('2006', '01010102', '4010000000000', '2006', 15000000, 0, 0, 56099.990000000005, 0, 0, 14943900.01, '0', false, false, 44);
INSERT INTO relacion_pp_cp VALUES ('2006', '01010101', '010101', '2006', 10000000, 0, 0, 148594.44, 140, 0, 9851405.5600000005, '0', false, false, 42);
INSERT INTO relacion_pp_cp VALUES ('2006', '01010101', '010103', '2006', 20000000, 0, 0, 5, 5, 0, 19999995, '0', false, false, 47);
INSERT INTO relacion_pp_cp VALUES ('2006', '010010102', '010101', '2006', 7777.7700000000004, 0, 0, 200, 150, 0, 7577.7700000000004, '0', false, false, 49);
INSERT INTO relacion_pp_cp VALUES ('2006', '010010102', '010102', '2006', 15000000, 0, 0, 540805.20999999996, 50000, 0, 14459194.789999999, '0', false, false, 43);


--
-- Data for Name: relacion_pp_cp_hist; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: relacion_producto_prove; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_producto_prove VALUES (2, 57, 2);
INSERT INTO relacion_producto_prove VALUES (1, 57, 1);


--
-- Data for Name: relacion_req_gp; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_req_gp VALUES ('1515', '002', 55, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('1515', '001', 56, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('3255', '002', 57, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('3255', '003', 59, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('3255', '001', 58, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('xxx_001', '002', 64, NULL, NULL, NULL);
INSERT INTO relacion_req_gp VALUES ('xxx_001', '004', 65, NULL, NULL, NULL);


--
-- Data for Name: relacion_req_prov; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_req_prov VALUES (186, 63, '002', NULL, NULL, 10, '2006-01-01', '2006-03-15');
INSERT INTO relacion_req_prov VALUES (209, 62, '0021154', NULL, NULL, 0, '2002-02-02', '2006-03-07');
INSERT INTO relacion_req_prov VALUES (212, 64, '003', NULL, NULL, 0, NULL, '2006-03-07');
INSERT INTO relacion_req_prov VALUES (213, 61, '0021154', NULL, NULL, 0, '2006-03-01', '2006-03-06');
INSERT INTO relacion_req_prov VALUES (214, 61, '002', NULL, NULL, 1, '2006-03-03', '2006-03-04');
INSERT INTO relacion_req_prov VALUES (215, 61, '001', NULL, NULL, 0, '2006-03-01', '2006-03-06');
INSERT INTO relacion_req_prov VALUES (222, 85, '0021154', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (223, 85, '003', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (224, 85, '001', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (225, 85, '002', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (250, 87, '002', NULL, NULL, 11, NULL, '2006-03-06');
INSERT INTO relacion_req_prov VALUES (251, 87, '003', NULL, NULL, 11, NULL, '2006-03-01');
INSERT INTO relacion_req_prov VALUES (252, 87, '001', NULL, NULL, 1, NULL, '2006-03-12');
INSERT INTO relacion_req_prov VALUES (253, 87, '9901', NULL, NULL, 12, NULL, '2006-03-06');
INSERT INTO relacion_req_prov VALUES (753, 86, '002', NULL, NULL, 15, '2006-01-01', NULL);
INSERT INTO relacion_req_prov VALUES (754, 86, '003', NULL, NULL, 25, '2006-01-01', '2006-01-09');
INSERT INTO relacion_req_prov VALUES (755, 86, '001', NULL, NULL, 25, '2006-01-01', NULL);
INSERT INTO relacion_req_prov VALUES (756, 86, '99006', NULL, NULL, 12, '2006-01-01', NULL);
INSERT INTO relacion_req_prov VALUES (776, 89, '002', NULL, NULL, 0, '2006-03-09', '2006-03-09');
INSERT INTO relacion_req_prov VALUES (777, 89, '001', NULL, NULL, 2, '2002-04-09', '2006-03-09');
INSERT INTO relacion_req_prov VALUES (778, 89, '012', NULL, NULL, 22, '2006-03-09', '2006-03-09');
INSERT INTO relacion_req_prov VALUES (779, 89, '99006', NULL, NULL, 2, '2006-03-09', '2006-03-09');
INSERT INTO relacion_req_prov VALUES (780, 132, '002', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (781, 132, '003', NULL, NULL, 0, NULL, NULL);
INSERT INTO relacion_req_prov VALUES (782, 132, '001', NULL, NULL, 0, NULL, NULL);


--
-- Data for Name: relacion_solicitud_pago; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: relacion_ue_cp; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_ue_cp VALUES ('010152', '01010101', '2006', 'vicepresidente', 52);
INSERT INTO relacion_ue_cp VALUES ('010154', '01010102', '2006', 'prueba', 53);
INSERT INTO relacion_ue_cp VALUES ('010153', '01010102', '2006', 'dasads', 54);
INSERT INTO relacion_ue_cp VALUES ('010152', '010010102', '2006', 'fsds', 55);
INSERT INTO relacion_ue_cp VALUES ('010154', '010010102', '2006', 'hhjhhh', 56);


--
-- Data for Name: relacion_ue_cp_hist; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: relacion_us_op; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO relacion_us_op VALUES (23, '1', 9);
INSERT INTO relacion_us_op VALUES (24, '1', 14);
INSERT INTO relacion_us_op VALUES (25, '2', 5);
INSERT INTO relacion_us_op VALUES (27, '2', 1);
INSERT INTO relacion_us_op VALUES (28, '2', 3);
INSERT INTO relacion_us_op VALUES (29, '2', 4);
INSERT INTO relacion_us_op VALUES (30, '2', 6);
INSERT INTO relacion_us_op VALUES (31, '1', 13);
INSERT INTO relacion_us_op VALUES (32, '3', 1);
INSERT INTO relacion_us_op VALUES (33, '3', 3);
INSERT INTO relacion_us_op VALUES (34, '3', 4);
INSERT INTO relacion_us_op VALUES (35, '3', 5);
INSERT INTO relacion_us_op VALUES (36, '3', 6);
INSERT INTO relacion_us_op VALUES (41, '3', 15);
INSERT INTO relacion_us_op VALUES (42, '3', 16);
INSERT INTO relacion_us_op VALUES (43, '3', 17);
INSERT INTO relacion_us_op VALUES (44, '3', 18);
INSERT INTO relacion_us_op VALUES (45, '3', 19);
INSERT INTO relacion_us_op VALUES (47, '4', 10);
INSERT INTO relacion_us_op VALUES (48, '4', 11);
INSERT INTO relacion_us_op VALUES (53, '4', 23);
INSERT INTO relacion_us_op VALUES (54, '4', 24);
INSERT INTO relacion_us_op VALUES (55, '4', 12);
INSERT INTO relacion_us_op VALUES (56, '1', 2);
INSERT INTO relacion_us_op VALUES (59, '3', 8);
INSERT INTO relacion_us_op VALUES (60, '1', 1);
INSERT INTO relacion_us_op VALUES (62, '1', 5);
INSERT INTO relacion_us_op VALUES (63, '1', 15);
INSERT INTO relacion_us_op VALUES (64, '1', 16);
INSERT INTO relacion_us_op VALUES (65, '1', 17);
INSERT INTO relacion_us_op VALUES (67, '1', 18);
INSERT INTO relacion_us_op VALUES (69, '1', 3);
INSERT INTO relacion_us_op VALUES (70, '1', 4);
INSERT INTO relacion_us_op VALUES (71, '1', 6);
INSERT INTO relacion_us_op VALUES (72, '1', 8);
INSERT INTO relacion_us_op VALUES (73, '1', 19);
INSERT INTO relacion_us_op VALUES (74, '1', 26);
INSERT INTO relacion_us_op VALUES (76, '1', 28);
INSERT INTO relacion_us_op VALUES (77, '1', 27);
INSERT INTO relacion_us_op VALUES (80, '4', 30);
INSERT INTO relacion_us_op VALUES (81, '1', 29);
INSERT INTO relacion_us_op VALUES (82, '5', 31);
INSERT INTO relacion_us_op VALUES (83, '5', 32);
INSERT INTO relacion_us_op VALUES (84, '5', 33);
INSERT INTO relacion_us_op VALUES (86, '5', 35);
INSERT INTO relacion_us_op VALUES (87, '5', 36);
INSERT INTO relacion_us_op VALUES (88, '5', 37);
INSERT INTO relacion_us_op VALUES (89, '5', 38);
INSERT INTO relacion_us_op VALUES (90, '5', 39);
INSERT INTO relacion_us_op VALUES (91, '5', 41);
INSERT INTO relacion_us_op VALUES (92, '5', 43);
INSERT INTO relacion_us_op VALUES (93, '5', 42);
INSERT INTO relacion_us_op VALUES (94, '5', 44);
INSERT INTO relacion_us_op VALUES (95, '5', 45);
INSERT INTO relacion_us_op VALUES (96, '1', 46);
INSERT INTO relacion_us_op VALUES (97, '5', 47);
INSERT INTO relacion_us_op VALUES (98, '5', 48);
INSERT INTO relacion_us_op VALUES (99, '5', 49);
INSERT INTO relacion_us_op VALUES (100, '5', 50);
INSERT INTO relacion_us_op VALUES (104, '3', 26);
INSERT INTO relacion_us_op VALUES (105, '3', 27);
INSERT INTO relacion_us_op VALUES (106, '3', 29);
INSERT INTO relacion_us_op VALUES (107, '3', 46);
INSERT INTO relacion_us_op VALUES (108, '6', 2);
INSERT INTO relacion_us_op VALUES (109, '6', 9);
INSERT INTO relacion_us_op VALUES (110, '6', 13);
INSERT INTO relacion_us_op VALUES (111, '6', 14);
INSERT INTO relacion_us_op VALUES (112, '6', 28);
INSERT INTO relacion_us_op VALUES (118, '6', 8);
INSERT INTO relacion_us_op VALUES (119, '6', 15);
INSERT INTO relacion_us_op VALUES (122, '6', 17);
INSERT INTO relacion_us_op VALUES (124, '6', 26);
INSERT INTO relacion_us_op VALUES (125, '6', 27);
INSERT INTO relacion_us_op VALUES (126, '6', 29);
INSERT INTO relacion_us_op VALUES (127, '6', 46);
INSERT INTO relacion_us_op VALUES (128, '6', 10);
INSERT INTO relacion_us_op VALUES (129, '6', 11);
INSERT INTO relacion_us_op VALUES (130, '6', 12);
INSERT INTO relacion_us_op VALUES (132, '6', 24);
INSERT INTO relacion_us_op VALUES (133, '6', 30);
INSERT INTO relacion_us_op VALUES (134, '6', 31);
INSERT INTO relacion_us_op VALUES (135, '6', 32);
INSERT INTO relacion_us_op VALUES (136, '6', 33);
INSERT INTO relacion_us_op VALUES (137, '6', 34);
INSERT INTO relacion_us_op VALUES (138, '6', 35);
INSERT INTO relacion_us_op VALUES (139, '6', 36);
INSERT INTO relacion_us_op VALUES (140, '6', 37);
INSERT INTO relacion_us_op VALUES (141, '6', 38);
INSERT INTO relacion_us_op VALUES (142, '6', 39);
INSERT INTO relacion_us_op VALUES (143, '6', 41);
INSERT INTO relacion_us_op VALUES (144, '6', 42);
INSERT INTO relacion_us_op VALUES (145, '6', 43);
INSERT INTO relacion_us_op VALUES (146, '6', 44);
INSERT INTO relacion_us_op VALUES (147, '6', 45);
INSERT INTO relacion_us_op VALUES (148, '6', 47);
INSERT INTO relacion_us_op VALUES (149, '6', 48);
INSERT INTO relacion_us_op VALUES (150, '6', 49);
INSERT INTO relacion_us_op VALUES (151, '6', 50);
INSERT INTO relacion_us_op VALUES (152, '6', 51);
INSERT INTO relacion_us_op VALUES (153, '6', 52);
INSERT INTO relacion_us_op VALUES (154, '6', 53);
INSERT INTO relacion_us_op VALUES (155, '6', 54);
INSERT INTO relacion_us_op VALUES (156, '6', 55);
INSERT INTO relacion_us_op VALUES (158, '6', 57);
INSERT INTO relacion_us_op VALUES (159, '6', 58);
INSERT INTO relacion_us_op VALUES (161, '6', 60);
INSERT INTO relacion_us_op VALUES (162, '6', 61);
INSERT INTO relacion_us_op VALUES (163, '6', 59);
INSERT INTO relacion_us_op VALUES (164, '6', 56);
INSERT INTO relacion_us_op VALUES (175, '5', 34);
INSERT INTO relacion_us_op VALUES (179, '6', 23);
INSERT INTO relacion_us_op VALUES (180, '6', 19);
INSERT INTO relacion_us_op VALUES (181, '6', 6);
INSERT INTO relacion_us_op VALUES (182, '6', 5);
INSERT INTO relacion_us_op VALUES (190, '6', 62);
INSERT INTO relacion_us_op VALUES (191, '6', 63);
INSERT INTO relacion_us_op VALUES (193, '6', 69);
INSERT INTO relacion_us_op VALUES (194, '6', 70);
INSERT INTO relacion_us_op VALUES (195, '6', 71);
INSERT INTO relacion_us_op VALUES (196, '6', 72);
INSERT INTO relacion_us_op VALUES (199, '6', 76);
INSERT INTO relacion_us_op VALUES (200, '6', 77);
INSERT INTO relacion_us_op VALUES (201, '6', 78);
INSERT INTO relacion_us_op VALUES (202, '6', 79);
INSERT INTO relacion_us_op VALUES (217, '6', 65);
INSERT INTO relacion_us_op VALUES (218, '6', 66);
INSERT INTO relacion_us_op VALUES (220, '6', 68);
INSERT INTO relacion_us_op VALUES (221, '6', 81);
INSERT INTO relacion_us_op VALUES (223, '6', 64);
INSERT INTO relacion_us_op VALUES (224, '6', 73);
INSERT INTO relacion_us_op VALUES (225, '6', 74);
INSERT INTO relacion_us_op VALUES (226, '6', 82);
INSERT INTO relacion_us_op VALUES (227, '6', 83);
INSERT INTO relacion_us_op VALUES (228, '6', 84);
INSERT INTO relacion_us_op VALUES (232, '6', 3);
INSERT INTO relacion_us_op VALUES (236, '6', 18);
INSERT INTO relacion_us_op VALUES (238, '6', 1);
INSERT INTO relacion_us_op VALUES (240, '6', 4);
INSERT INTO relacion_us_op VALUES (243, '6', 16);
INSERT INTO relacion_us_op VALUES (245, '6', 67);


--
-- Data for Name: requisitos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO requisitos VALUES ('002', 'Balance Visado', 'Balance Visado', '2006-02-23', false);
INSERT INTO requisitos VALUES ('003', 'Solvecia Municipal', 'Solvecia Municipal', '2006-02-23', true);
INSERT INTO requisitos VALUES ('012', 'Partida de Nacimiento', 'Partida de Nacimiento', '2006-03-08', true);
INSERT INTO requisitos VALUES ('9901', 'Partida de Defunción', 'Partida de Defunción', '2006-03-08', true);
INSERT INTO requisitos VALUES ('99006', 'Registro Mercantil', 'Registro Mercantil', '2006-03-08', true);
INSERT INTO requisitos VALUES ('001', 'Documento Constitutivo', 'Documento Constitutivo', '2006-04-03', true);
INSERT INTO requisitos VALUES ('004', 'Listado de Maquinaria o Equipos', 'Listado de Maquinaria o Equipos', '2006-04-03', true);


--
-- Data for Name: retencion_iva; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO retencion_iva VALUES (35, 62, 'FORMAL', '2006-03-07', 500, 45);
INSERT INTO retencion_iva VALUES (56, 132, 'ORDINARIO', '2006-04-11', 999999526.12, 33600);
INSERT INTO retencion_iva VALUES (33, 89, 'ORDINARIO', '2006-03-07', 1000000, 45);


--
-- Data for Name: rif_letra; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO rif_letra VALUES ('J', 'J');
INSERT INTO rif_letra VALUES ('V', 'V');
INSERT INTO rif_letra VALUES ('E', 'E');


--
-- Data for Name: sistema; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO sistema VALUES (2, 'Registro de Proveedores y contratistas');
INSERT INTO sistema VALUES (3, 'Sistema');
INSERT INTO sistema VALUES (4, 'Vehículo');
INSERT INTO sistema VALUES (5, 'Publicidad');
INSERT INTO sistema VALUES (1, 'Gestión Presupuestaria');


--
-- Data for Name: situaciones; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO situaciones VALUES ('1', 'Ejecucion', 'E');
INSERT INTO situaciones VALUES ('2', 'A Iniciar', 'A');
INSERT INTO situaciones VALUES ('3', 'Paralizado', 'P');
INSERT INTO situaciones VALUES ('4', 'Terminada', 'T');


--
-- Data for Name: solvencias; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO solvencias VALUES (1, 'PRESTACION DE SERVICIOS PROFESIONALES');


--
-- Data for Name: status; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO status VALUES (0, 'Inactivo');
INSERT INTO status VALUES (1, 'Activo');


--
-- Data for Name: status_producto; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO status_producto VALUES (1, 'S');
INSERT INTO status_producto VALUES (2, 'A');
INSERT INTO status_producto VALUES (3, 'C');


--
-- Data for Name: status_proveedor; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO status_proveedor VALUES (1, 'A', 'Activo');
INSERT INTO status_proveedor VALUES (2, 'I', 'Inactivo');
INSERT INTO status_proveedor VALUES (3, 'P', 'Pendiente');


--
-- Data for Name: tipo_producto; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO tipo_producto VALUES (8, 'test_x', 'E+ otros', 1, '2006-04-20', '1000', '000005');
INSERT INTO tipo_producto VALUES (9, 'more', 'e otros', 1, '2006-04-20', '1555', '000001');
INSERT INTO tipo_producto VALUES (11, 'testing....', 'every one', 1, '2006-04-20', '58', '000002');


--
-- Data for Name: tipo_producto_clasif; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO tipo_producto_clasif VALUES (1, 'Bienes Muebles y Semovientes', 'Bienes Muebles y Semovientes', '2006-02-22');
INSERT INTO tipo_producto_clasif VALUES (2, 'Materiales y Suministros', 'Materiales y Suministros', '2006-02-22');


--
-- Data for Name: tipos_documentos; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO tipos_documentos VALUES (1, 'Contrato de Obras', '', true, '011', 'CO');
INSERT INTO tipos_documentos VALUES (5, 'Traslado de Partida', '', true, '007', 'TP');
INSERT INTO tipos_documentos VALUES (1, 'Contrato de Servicio', '', true, '012', 'CS');
INSERT INTO tipos_documentos VALUES (1, 'Contrato de Hacienda', '', false, '003', NULL);
INSERT INTO tipos_documentos VALUES (3, 'Cheque de pago', '', true, '005', NULL);
INSERT INTO tipos_documentos VALUES (4, 'Reinyección de partida', '', true, '006', NULL);
INSERT INTO tipos_documentos VALUES (1, 'Caja Chica', '', true, '013', 'CC');
INSERT INTO tipos_documentos VALUES (0, 'No referenciado', 'hdfksgksgdfkj', true, '008', NULL);
INSERT INTO tipos_documentos VALUES (3, 'Nomina', '', true, '010', 'NM');
INSERT INTO tipos_documentos VALUES (1, 'Orden de Servicio', '', true, '002', 'OS');
INSERT INTO tipos_documentos VALUES (2, 'Orden de Pago', '', true, '004', 'OP');
INSERT INTO tipos_documentos VALUES (1, 'Orden de Compra', '', true, '001', 'OC');
INSERT INTO tipos_documentos VALUES (1, 'Orden de Trabajo', '', true, '009', 'OT');


--
-- Data for Name: tipos_fianzas; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO tipos_fianzas VALUES ('1', 'Fiel Cumplimiento');


--
-- Data for Name: unidades_ejecutoras; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO unidades_ejecutoras VALUES ('010152', '2006', 'DESPACHO DEL VICEPRESIDENTE', 'PABLO MONTOYA');
INSERT INTO unidades_ejecutoras VALUES ('010153', '2006', 'SECRETARIA MUNICIPAL', 'JESUS VERA');
INSERT INTO unidades_ejecutoras VALUES ('010154', '2006', 'RECURSOS HUMANOS', 'Nadie');


--
-- Data for Name: unidades_ejecutoras_hist; Type: TABLE DATA; Schema: puser; Owner: puser
--



--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO usuarios VALUES ('Usuario', 'Registro de Proveedores', '0', '0', '2', '4', ' ', 'proveedores', '202cb962ac59075b964b07152d234b70', '1', 137896431);
INSERT INTO usuarios VALUES ('Usuario', 'Presupuestos', '0', '0', '1', '3', ' ', 'presupuesto', '202cb962ac59075b964b07152d234b70', '1', 1);
INSERT INTO usuarios VALUES ('Administrador', '', '02', '01', '2', '6', ' ', 'admin', '202cb962ac59075b964b07152d234b70', '1', 11111111);
INSERT INTO usuarios VALUES ('Usuario', 'Vehículo', '010152', '01', '2', '5', 'V', 'vehiculo', '202cb962ac59075b964b07152d234b70', '1', 15529540);


--
-- Data for Name: ut; Type: TABLE DATA; Schema: puser; Owner: puser
--

INSERT INTO ut VALUES (1, '2003-01-01', '2004-01-01', 35000);
INSERT INTO ut VALUES (2, '2004-01-02', '2005-01-01', 37000);
INSERT INTO ut VALUES (3, '2006-01-02', '2007-01-01', 33600);


SET search_path = vehiculo, pg_catalog;

--
-- Data for Name: banco; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO banco VALUES (1, 'BANCO DE VENEZUELA', ' ', '', 1);
INSERT INTO banco VALUES (2, 'MERCANTIL', ' ', '', 1);
INSERT INTO banco VALUES (3, 'PROVINCIAL BBVA', '', '', 1);
INSERT INTO banco VALUES (4, 'BANESCO', '', '', 1);
INSERT INTO banco VALUES (5, 'FONDO COMUN', '', '', 1);


--
-- Data for Name: base_calculo_veh; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO base_calculo_veh VALUES (1, '2006-01-01', '2006-03-31', 0, 'P', 'Sin Recargo, aun no vence el plazo (90) Dias', 1, 90, 'Art. 28', 2006);
INSERT INTO base_calculo_veh VALUES (2, '2006-04-01', '2006-06-30', 10, 'P', 'Recargo del 10%', 91, 151, 'Art. 29', 2006);
INSERT INTO base_calculo_veh VALUES (3, '2006-07-01', '2006-12-31', 20, 'P', 'Recargo del 20%', 152, 365, 'Art. 29', 2006);
INSERT INTO base_calculo_veh VALUES (4, '2006-12-31', '2006-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2006);
INSERT INTO base_calculo_veh VALUES (7, '2005-12-31', '2005-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2005);
INSERT INTO base_calculo_veh VALUES (8, '2004-12-31', '2004-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2004);
INSERT INTO base_calculo_veh VALUES (9, '2003-12-31', '2003-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2003);
INSERT INTO base_calculo_veh VALUES (10, '2002-12-31', '2002-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2002);
INSERT INTO base_calculo_veh VALUES (11, '2001-12-31', '2001-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2001);
INSERT INTO base_calculo_veh VALUES (12, '2000-12-31', '2000-12-31', 30, 'P', 'Recargo del 30%', 365, 365, 'Art. 29', 2000);


--
-- Data for Name: colores; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO colores VALUES (2, 'AGUA MARINA', NULL, 1);
INSERT INTO colores VALUES (3, 'ALMENDRA', NULL, 1);
INSERT INTO colores VALUES (4, 'ALUMINIO', NULL, 1);
INSERT INTO colores VALUES (5, 'AMARILLO', NULL, 1);
INSERT INTO colores VALUES (6, 'AMARILLO Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (7, 'AMARILLO Y VERDE', NULL, 1);
INSERT INTO colores VALUES (8, 'AMBAR', NULL, 1);
INSERT INTO colores VALUES (9, 'ANARANJADO', NULL, 1);
INSERT INTO colores VALUES (10, 'ARENA', NULL, 1);
INSERT INTO colores VALUES (11, 'ARTICO', NULL, 1);
INSERT INTO colores VALUES (12, 'AZUL', NULL, 1);
INSERT INTO colores VALUES (13, 'AZUL C/F ROJAS', NULL, 1);
INSERT INTO colores VALUES (14, 'AZUL DOS TONOS', NULL, 1);
INSERT INTO colores VALUES (15, 'AZUL MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (16, 'AZUL Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (17, 'AZUL Y DORADO', NULL, 1);
INSERT INTO colores VALUES (18, 'AZUL Y GRIS', NULL, 1);
INSERT INTO colores VALUES (19, 'AZUL Y NEGRO', NULL, 1);
INSERT INTO colores VALUES (20, 'AZUL Y PLATA', NULL, 1);
INSERT INTO colores VALUES (21, 'AZUL Y ROJO', NULL, 1);
INSERT INTO colores VALUES (22, 'AZUL Y VERDE', NULL, 1);
INSERT INTO colores VALUES (23, 'AZURITA', NULL, 1);
INSERT INTO colores VALUES (24, 'BEIGE', NULL, 1);
INSERT INTO colores VALUES (25, 'BEIGE C/F DECORATIVAS', NULL, 1);
INSERT INTO colores VALUES (26, 'BEIGE Y MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (27, 'BEIGE Y ROJO', NULL, 1);
INSERT INTO colores VALUES (28, 'BLANCO', NULL, 1);
INSERT INTO colores VALUES (29, 'BLANCO C/F DECORATIVAS', NULL, 1);
INSERT INTO colores VALUES (30, 'BLANCO C/FJAS ROJAS', NULL, 1);
INSERT INTO colores VALUES (31, 'BLANCO SOFIA', NULL, 1);
INSERT INTO colores VALUES (32, 'BLANCO Y AMARILLO', NULL, 1);
INSERT INTO colores VALUES (33, 'BLANCO Y AZUL', NULL, 1);
INSERT INTO colores VALUES (34, 'BLANCO Y MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (35, 'BLANCO Y MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (36, 'BLANCO Y NARANJA', NULL, 1);
INSERT INTO colores VALUES (37, 'BLANCO Y PLATEADO', NULL, 1);
INSERT INTO colores VALUES (38, 'BLANCO Y ROJO', NULL, 1);
INSERT INTO colores VALUES (39, 'BLANCO Y VERDE', NULL, 1);
INSERT INTO colores VALUES (40, 'BODY', NULL, 1);
INSERT INTO colores VALUES (41, 'BRONCE', NULL, 1);
INSERT INTO colores VALUES (43, 'CACAO', NULL, 1);
INSERT INTO colores VALUES (44, 'CAOBA', NULL, 1);
INSERT INTO colores VALUES (45, 'CAOBA CLARO', NULL, 1);
INSERT INTO colores VALUES (46, 'CARAMELO', NULL, 1);
INSERT INTO colores VALUES (47, 'CARBON MET.', NULL, 1);
INSERT INTO colores VALUES (48, 'CARBON MET.TECHO G.', NULL, 1);
INSERT INTO colores VALUES (49, 'CENIZA', NULL, 1);
INSERT INTO colores VALUES (50, 'CERMICO', NULL, 1);
INSERT INTO colores VALUES (51, 'CHAMPAGNE CLARO', NULL, 1);
INSERT INTO colores VALUES (52, 'COBRE', NULL, 1);
INSERT INTO colores VALUES (53, 'COBRE Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (54, 'COBRE Y VINOTINTO', NULL, 1);
INSERT INTO colores VALUES (55, 'CORAL', NULL, 1);
INSERT INTO colores VALUES (56, 'CORAL Y BRONCE', NULL, 1);
INSERT INTO colores VALUES (57, 'CREMA', NULL, 1);
INSERT INTO colores VALUES (58, 'CREMA C/F MARRONES', NULL, 1);
INSERT INTO colores VALUES (59, 'CREMA Y AZUL', NULL, 1);
INSERT INTO colores VALUES (60, 'CREMA Y AZUL OSCURO', NULL, 1);
INSERT INTO colores VALUES (61, 'CREMA Y MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (62, 'DORADO', NULL, 1);
INSERT INTO colores VALUES (63, 'DORADO Y CREMA', NULL, 1);
INSERT INTO colores VALUES (64, 'DORADO/MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (65, 'ESM.ROJO FORMULA', NULL, 1);
INSERT INTO colores VALUES (66, 'GRAFITO', NULL, 1);
INSERT INTO colores VALUES (68, 'GRANATE', NULL, 1);
INSERT INTO colores VALUES (69, 'GRANATE-METALISADO', NULL, 1);
INSERT INTO colores VALUES (70, 'GRIS', NULL, 1);
INSERT INTO colores VALUES (71, 'GRIS DOS TONOS', NULL, 1);
INSERT INTO colores VALUES (72, 'GRIS PERLA', NULL, 1);
INSERT INTO colores VALUES (73, 'GRIS Y AZUL', NULL, 1);
INSERT INTO colores VALUES (74, 'GRIS Y MARRON', NULL, 1);
INSERT INTO colores VALUES (75, 'GRIS Y NEGRO', NULL, 1);
INSERT INTO colores VALUES (76, 'GRIS Y PLATA', NULL, 1);
INSERT INTO colores VALUES (77, 'GRIS Y VERDE', NULL, 1);
INSERT INTO colores VALUES (78, 'JADE', NULL, 1);
INSERT INTO colores VALUES (79, 'JADE CLARO', NULL, 1);
INSERT INTO colores VALUES (80, 'LADRILLO', NULL, 1);
INSERT INTO colores VALUES (82, 'LILA', NULL, 1);
INSERT INTO colores VALUES (83, 'LOBELIA', NULL, 1);
INSERT INTO colores VALUES (84, 'MADERA', NULL, 1);
INSERT INTO colores VALUES (85, 'MADERA CENIZA', NULL, 1);
INSERT INTO colores VALUES (86, 'MAGNE', NULL, 1);
INSERT INTO colores VALUES (87, 'MAGNESIO', NULL, 1);
INSERT INTO colores VALUES (88, 'MALVA', NULL, 1);
INSERT INTO colores VALUES (89, 'MARFIL', NULL, 1);
INSERT INTO colores VALUES (90, 'MARRON', NULL, 1);
INSERT INTO colores VALUES (91, 'MARRON Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (92, 'METALIZADO', NULL, 1);
INSERT INTO colores VALUES (93, 'MORADO', NULL, 1);
INSERT INTO colores VALUES (94, 'MOSTAZA', NULL, 1);
INSERT INTO colores VALUES (95, 'MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (96, 'NARANJA', NULL, 1);
INSERT INTO colores VALUES (97, 'NARANJA/MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (98, 'NEGRO', NULL, 1);
INSERT INTO colores VALUES (99, 'NEGRO Y GRIS', NULL, 1);
INSERT INTO colores VALUES (100, 'NEGRO Y PLATA', NULL, 1);
INSERT INTO colores VALUES (101, 'NEGRO Y ROJO', NULL, 1);
INSERT INTO colores VALUES (102, 'NISPERO MARRON', NULL, 1);
INSERT INTO colores VALUES (103, 'OCRE', NULL, 1);
INSERT INTO colores VALUES (105, 'ORO', NULL, 1);
INSERT INTO colores VALUES (106, 'PENDIENTE', NULL, 1);
INSERT INTO colores VALUES (109, 'PERLA', NULL, 1);
INSERT INTO colores VALUES (110, 'PLATA', NULL, 1);
INSERT INTO colores VALUES (111, 'PLATA Y AZUL', NULL, 1);
INSERT INTO colores VALUES (112, 'PLATA Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (113, 'PLATEADO', NULL, 1);
INSERT INTO colores VALUES (114, 'PLATEADO MET.Y V.T.', NULL, 1);
INSERT INTO colores VALUES (115, 'PLOMO', NULL, 1);
INSERT INTO colores VALUES (116, 'PURPURA', NULL, 1);
INSERT INTO colores VALUES (117, 'ROJO', NULL, 1);
INSERT INTO colores VALUES (118, 'ROJO Y AMARILLO', NULL, 1);
INSERT INTO colores VALUES (119, 'ROJO Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (120, 'ROJO Y GRIS', NULL, 1);
INSERT INTO colores VALUES (121, 'ROJO Y MULTICOLOR', NULL, 1);
INSERT INTO colores VALUES (122, 'ROJO Y PLATA', NULL, 1);
INSERT INTO colores VALUES (123, 'ROSADO', NULL, 1);
INSERT INTO colores VALUES (124, 'ROSADO Y VINO TINTO', NULL, 1);
INSERT INTO colores VALUES (125, 'RUBI  METALICO', NULL, 1);
INSERT INTO colores VALUES (126, 'SABANA', NULL, 1);
INSERT INTO colores VALUES (127, 'SALMON', NULL, 1);
INSERT INTO colores VALUES (128, 'SANDALO', NULL, 1);
INSERT INTO colores VALUES (129, 'SATIN', NULL, 1);
INSERT INTO colores VALUES (130, 'SIN COLOR', NULL, 1);
INSERT INTO colores VALUES (131, 'TERRACOTA', NULL, 1);
INSERT INTO colores VALUES (132, 'TITANIO', NULL, 1);
INSERT INTO colores VALUES (133, 'TURQUESA', NULL, 1);
INSERT INTO colores VALUES (134, 'VERDE', NULL, 1);
INSERT INTO colores VALUES (135, 'VERDE DOS TONOS', NULL, 1);
INSERT INTO colores VALUES (136, 'VERDE Y AZUL', NULL, 1);
INSERT INTO colores VALUES (137, 'VERDE Y BEIGE', NULL, 1);
INSERT INTO colores VALUES (138, 'VERDE Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (139, 'VERDE Y GRIS', NULL, 1);
INSERT INTO colores VALUES (140, 'VERDE Y NEGRO', NULL, 1);
INSERT INTO colores VALUES (141, 'VINO TINTO Y NEGRO', NULL, 1);
INSERT INTO colores VALUES (142, 'VINO-TINTO', NULL, 1);
INSERT INTO colores VALUES (143, 'VINOTINTO Y BLANCO', NULL, 1);
INSERT INTO colores VALUES (144, 'VIOLETA', NULL, 1);
INSERT INTO colores VALUES (145, 'Z-ROSADO ', NULL, 1);
INSERT INTO colores VALUES (1, 'ACUARELA', NULL, 1);
INSERT INTO colores VALUES (147, 'MARON PLATA', NULL, 0);
INSERT INTO colores VALUES (148, 'VERDEGALLO', NULL, 0);


--
-- Data for Name: contribuyente; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO contribuyente VALUES (10, 'N', 'V', 'C', '10762070', NULL, NULL, NULL, 'S', 'dfgde', '1971-08-12', '06', 'Merly', '', 'Melendez', '', '...', '...', '2006-03-23', '', '.....', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000', '0000', '0000', '', '', '', '--', '8646');
INSERT INTO contribuyente VALUES (33, 'N', 'V', 'C', '777778', 0, 0, 0, 'N', 'dd3rw', '2006-03-27', '06', 'jabel', 'we', 'c', 'we', '423', '234', '2006-03-27', '', '2342', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '324', '234', '423', '', '', '', '--', '88');
INSERT INTO contribuyente VALUES (7, 'J', '', 'R', '', 1, 2, 2, 'N', 'chino', '2006-03-22', '06', 'hwuang_x', 'sty', 'fhuywn', 'tu', 'd1', 'd2', '2006-03-22', '', 'd3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '877544', '165465491', 'tmmfm@mp.cn', 'J', '00000008', '5', 'J-00000008-5', '87414');
INSERT INTO contribuyente VALUES (9, 'N', 'V', 'C', '15529540', 2, 1, 2, 'N', '', '1981-09-29', '06', 'Julio', 'César', 'Calvo', 'Farias', 'ls bukrs', 'ls bukrs', '2006-03-23', '', 'around the planet ;)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0241-8781867', '0412-885-7295', 'abc@madriski.rq', '', '', '', '--', '8877815');
INSERT INTO contribuyente VALUES (42, 'N', 'V', '', '16289704', 1, 3, 2, 'N', '', '2006-05-22', '', 'Pedro', '', 'Perez', '', 'Valencia', '', '2006-05-22', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', 'N', '', '', 'N--', '');
INSERT INTO contribuyente VALUES (53, 'N', 'V', 'R', 'V-16289711-3', 0, 0, 0, 'S', '', '1983-09-07', '', 'Pedro', '', 'Acosta', 'Bencomo', 'Trigal', '', '2006-05-23', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5555', '3333', 'prueba@prueba.com', 'V', '16289711', '3', 'V-16289711-3', '');
INSERT INTO contribuyente VALUES (36, 'N', '', 'C', '16289703', 1, 5, 4, 'S', '', '1984-09-07', '', 'Angel', 'Fortunato', 'Acosta', 'Bencomo', 'Prebo', '', '2006-05-10', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0414-4378373', 'angelfortunato@gmail.com', '', '', '', '--', '');
INSERT INTO contribuyente VALUES (34, 'N', 'E', 'C', '13381452', 1, 1, 1, 'N', 'Repuestos Mayuya', '2010-08-30', '06', 'Joan', 'Manuel', 'Casas', 'Cortes', 'Prebo III', 'Prebo III', '2010-09-27', '7-28-6--20', 'Aqui III', 'Caracas', 'Venezuela', 'Valencia', 'Carabobo', '20-04-2006', 1, '20-04-2006', 2, '8225850', '0414-4725098', 'j_casasc@hotmail.com', '', '', '', 'V-13381452', '8225850');


--
-- Data for Name: costo_calcomania; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO costo_calcomania VALUES (1, 2006, '2006-01-01', '2006-12-31', 250, 1);


--
-- Data for Name: descuento; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO descuento VALUES (2, 8, '2006-03-15', 68);
INSERT INTO descuento VALUES (1, 6, '2006-01-20', 27);
INSERT INTO descuento VALUES (3, 35, '2006-03-15', 75);


--
-- Data for Name: desincorporacion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO desincorporacion VALUES (2, 'VENTA', 0);
INSERT INTO desincorporacion VALUES (1, 'ROBO', 0);
INSERT INTO desincorporacion VALUES (5, 'INTERCAMBIO', 0);
INSERT INTO desincorporacion VALUES (4, 'PERDIDA', 0);
INSERT INTO desincorporacion VALUES (3, 'INCENDIO', 1);


--
-- Data for Name: det_forma_pago; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO det_forma_pago VALUES (25, 79, 700, '', NULL, NULL, NULL, NULL, 0, 1);
INSERT INTO det_forma_pago VALUES (26, 79, 7000, '62489321', NULL, NULL, NULL, NULL, 1, 8);
INSERT INTO det_forma_pago VALUES (27, 80, 7700, '12365478932211', NULL, NULL, NULL, NULL, 3, 8);


--
-- Data for Name: det_pago; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO det_pago VALUES (153, 79, NULL, NULL, 2006, 2, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (154, 79, NULL, NULL, 2006, 2, 175, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (155, 79, NULL, NULL, 2005, 7, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (156, 79, NULL, NULL, 2005, 7, 525, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (157, 79, NULL, NULL, 2004, 8, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (158, 79, NULL, NULL, 2004, 8, 375, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (159, 79, NULL, NULL, 2003, 9, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (160, 79, NULL, NULL, 2003, 9, 375, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (161, 79, NULL, NULL, 2006, NULL, 250, 'admin', '2006-05-22', NULL, NULL, 6, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (162, 80, NULL, NULL, 2006, 2, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (163, 80, NULL, NULL, 2006, 2, 175, 'admin', '2006-05-22', NULL, NULL, 3, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (164, 80, NULL, NULL, 2005, 7, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (165, 80, NULL, NULL, 2005, 7, 525, 'admin', '2006-05-22', NULL, NULL, 3, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (166, 80, NULL, NULL, 2004, 8, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (167, 80, NULL, NULL, 2004, 8, 375, 'admin', '2006-05-22', NULL, NULL, 3, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (168, 80, NULL, NULL, 2003, 9, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (169, 80, NULL, NULL, 2003, 9, 375, 'admin', '2006-05-22', NULL, NULL, 3, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (170, 80, NULL, NULL, 2006, NULL, 250, 'admin', '2006-05-22', NULL, NULL, 6, '14', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (171, 81, NULL, NULL, 2006, 2, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (172, 81, NULL, NULL, 2006, 2, 175, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (173, 81, NULL, NULL, 2005, 7, 1750, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (174, 81, NULL, NULL, 2005, 7, 525, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (175, 81, NULL, NULL, 2004, 8, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (176, 81, NULL, NULL, 2004, 8, 375, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (177, 81, NULL, NULL, 2003, 9, 1250, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (178, 81, NULL, NULL, 2003, 9, 375, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (179, 81, NULL, NULL, 2002, 10, 1000, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (180, 81, NULL, NULL, 2002, 10, 300, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (181, 81, NULL, NULL, 2001, 11, 1000, 'admin', '2006-05-22', NULL, NULL, 1, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (182, 81, NULL, NULL, 2001, 11, 300, 'admin', '2006-05-22', NULL, NULL, 3, '12', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO det_pago VALUES (183, 81, NULL, NULL, 2006, NULL, 250, 'admin', '2006-05-22', NULL, NULL, 6, '12', NULL, NULL, NULL, NULL, NULL, NULL);


--
-- Data for Name: esp_costo_veh; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO esp_costo_veh VALUES (2, 1, 1250, '2003-01-01', '2004-12-31');
INSERT INTO esp_costo_veh VALUES (11, 10, 5000, '2007-01-01', '2007-12-31');
INSERT INTO esp_costo_veh VALUES (3, 1, 1000, '2000-01-01', '2002-12-31');
INSERT INTO esp_costo_veh VALUES (4, 1, 1750, '2005-01-01', '2006-12-31');


--
-- Data for Name: exoneracion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO exoneracion VALUES (1, 'PROPIEDAD X', 1);
INSERT INTO exoneracion VALUES (2, 'MOTIVO', 0);
INSERT INTO exoneracion VALUES (3, 'ROBO', 0);
INSERT INTO exoneracion VALUES (5, 'PIANO ENCIMA_', 0);
INSERT INTO exoneracion VALUES (4, 'PERDIDO', 1);


--
-- Data for Name: forma_pago; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO forma_pago VALUES (7, 'CHEQUE', 'S', 'CH', 1);
INSERT INTO forma_pago VALUES (1, 'EFECTIVO', 'S', 'EF', 1);
INSERT INTO forma_pago VALUES (8, 'DEPOSITO', 'S', 'DE', 1);
INSERT INTO forma_pago VALUES (2, 'TARJETA DE CREDITO', 'N', 'DP', 1);


--
-- Data for Name: imp_liq; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO imp_liq VALUES (3870, 426, NULL, 36, 2006, NULL, 1750, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, NULL);
INSERT INTO imp_liq VALUES (3871, 426, NULL, 36, 2006, NULL, 175, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL);
INSERT INTO imp_liq VALUES (3872, 426, NULL, 36, 2005, NULL, 1750, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7, 1, NULL);
INSERT INTO imp_liq VALUES (3873, 426, NULL, 36, 2005, NULL, 525, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7, 3, NULL);
INSERT INTO imp_liq VALUES (3874, 426, NULL, 36, 2004, NULL, 1250, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, NULL);
INSERT INTO imp_liq VALUES (3875, 426, NULL, 36, 2004, NULL, 375, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 3, NULL);
INSERT INTO imp_liq VALUES (3876, 426, NULL, 36, 2003, NULL, 1250, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 1, NULL);
INSERT INTO imp_liq VALUES (3877, 426, NULL, 36, 2003, NULL, 375, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 3, NULL);
INSERT INTO imp_liq VALUES (3878, 426, NULL, 36, 2002, NULL, 1000, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, NULL);
INSERT INTO imp_liq VALUES (3879, 426, NULL, 36, 2002, NULL, 300, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 3, NULL);
INSERT INTO imp_liq VALUES (3880, 426, NULL, 36, 2001, NULL, 1000, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11, 1, NULL);
INSERT INTO imp_liq VALUES (3881, 426, NULL, 36, 2001, NULL, 300, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11, 3, NULL);
INSERT INTO imp_liq VALUES (3882, 426, NULL, 36, 2006, NULL, 250, NULL, NULL, NULL, NULL, NULL, '12', '301040500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL);
INSERT INTO imp_liq VALUES (3883, 426, NULL, 36, 2006, NULL, 250, NULL, NULL, NULL, NULL, NULL, '12', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL);
INSERT INTO imp_liq VALUES (3915, 448, NULL, 42, 2006, NULL, 1750, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, NULL);
INSERT INTO imp_liq VALUES (3916, 448, NULL, 42, 2006, NULL, 175, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL);
INSERT INTO imp_liq VALUES (3917, 448, NULL, 42, 2005, NULL, 1750, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7, 1, NULL);
INSERT INTO imp_liq VALUES (3918, 448, NULL, 42, 2005, NULL, 525, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7, 3, NULL);
INSERT INTO imp_liq VALUES (3919, 448, NULL, 42, 2004, NULL, 1250, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, NULL);
INSERT INTO imp_liq VALUES (3920, 448, NULL, 42, 2004, NULL, 375, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 3, NULL);
INSERT INTO imp_liq VALUES (3921, 448, NULL, 42, 2003, NULL, 1250, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 1, NULL);
INSERT INTO imp_liq VALUES (3922, 448, NULL, 42, 2003, NULL, 375, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 3, NULL);
INSERT INTO imp_liq VALUES (3923, 448, NULL, 42, 2002, NULL, 1000, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, NULL);
INSERT INTO imp_liq VALUES (3924, 448, NULL, 42, 2002, NULL, 300, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 3, NULL);
INSERT INTO imp_liq VALUES (3925, 448, NULL, 42, 2006, NULL, 250, NULL, NULL, NULL, NULL, NULL, '13', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL);
INSERT INTO imp_liq VALUES (3927, 512, NULL, 34, 2006, NULL, 250, NULL, NULL, NULL, NULL, NULL, '15', '301040600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL);


--
-- Data for Name: imp_pago; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO imp_pago VALUES (71, 79, '2006-05-22', NULL, 301040600, 7700, 36, NULL, NULL, NULL, NULL, 'admin', '2006-05-22', NULL, NULL, NULL, NULL, NULL, NULL, '');
INSERT INTO imp_pago VALUES (72, 80, '2006-05-22', NULL, 301040600, 7700, 42, NULL, NULL, NULL, NULL, 'admin', '2006-05-22', NULL, NULL, NULL, NULL, NULL, NULL, '');
INSERT INTO imp_pago VALUES (73, 81, '2006-05-22', NULL, 301040600, 9000, 36, NULL, NULL, NULL, NULL, 'admin', '2006-05-22', NULL, NULL, NULL, NULL, NULL, NULL, '');


--
-- Data for Name: linea; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO linea VALUES (2, 'CEMENTERIO', 1);
INSERT INTO linea VALUES (3, 'ASOMIXUPAL', 1);
INSERT INTO linea VALUES (1, 'BELLA VISTA', 1);
INSERT INTO linea VALUES (4, 'XX', 0);


--
-- Data for Name: marca; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO marca VALUES (4, 'AGAMAR', NULL, 1);
INSERT INTO marca VALUES (5, 'ALFA', NULL, 1);
INSERT INTO marca VALUES (6, 'ALFA ROMEO', NULL, 1);
INSERT INTO marca VALUES (7, 'AMERICAN', NULL, 1);
INSERT INTO marca VALUES (8, 'AMERICAN MOTOR', NULL, 1);
INSERT INTO marca VALUES (9, 'AMERICAN MOTORS', NULL, 1);
INSERT INTO marca VALUES (10, 'AQ', NULL, 1);
INSERT INTO marca VALUES (11, 'ARO', NULL, 1);
INSERT INTO marca VALUES (12, 'ASIA MOTORS', NULL, 1);
INSERT INTO marca VALUES (14, 'AUDI', NULL, 1);
INSERT INTO marca VALUES (15, 'AUTO CLASSICS', NULL, 1);
INSERT INTO marca VALUES (16, 'AUTOGAGO', NULL, 1);
INSERT INTO marca VALUES (17, 'BENTLEY', NULL, 1);
INSERT INTO marca VALUES (18, 'BLUE BIRD', NULL, 1);
INSERT INTO marca VALUES (19, 'BLUE BIRD', NULL, 1);
INSERT INTO marca VALUES (20, 'B.M.W.', NULL, 1);
INSERT INTO marca VALUES (21, 'BMW', NULL, 1);
INSERT INTO marca VALUES (22, 'BORGWARD', NULL, 1);
INSERT INTO marca VALUES (23, 'BRIDGESTONE', NULL, 1);
INSERT INTO marca VALUES (24, 'B.S.A.650', NULL, 1);
INSERT INTO marca VALUES (25, 'BUICK', NULL, 1);
INSERT INTO marca VALUES (26, 'BUICK', NULL, 1);
INSERT INTO marca VALUES (27, 'BUSVEN', NULL, 1);
INSERT INTO marca VALUES (28, 'CADDY', NULL, 1);
INSERT INTO marca VALUES (29, 'CADILLAC', NULL, 1);
INSERT INTO marca VALUES (30, 'CARIBE', NULL, 1);
INSERT INTO marca VALUES (31, 'caroni', NULL, 1);
INSERT INTO marca VALUES (32, 'CASE', NULL, 1);
INSERT INTO marca VALUES (33, 'CENTURY', NULL, 1);
INSERT INTO marca VALUES (34, 'CHEVROLET', NULL, 1);
INSERT INTO marca VALUES (35, 'CHRYSLER', NULL, 1);
INSERT INTO marca VALUES (36, 'CHRYSLER LA BARON', NULL, 1);
INSERT INTO marca VALUES (37, 'CITROEN', NULL, 1);
INSERT INTO marca VALUES (38, 'COMMBER', NULL, 1);
INSERT INTO marca VALUES (39, 'CONDOR', NULL, 1);
INSERT INTO marca VALUES (40, 'CONQUISTADOR', NULL, 1);
INSERT INTO marca VALUES (41, 'COUNTRY', NULL, 1);
INSERT INTO marca VALUES (42, 'COUPE', NULL, 1);
INSERT INTO marca VALUES (43, 'CREANE', NULL, 1);
INSERT INTO marca VALUES (44, 'CUSHAN', NULL, 1);
INSERT INTO marca VALUES (45, 'D INNOCENZO', NULL, 1);
INSERT INTO marca VALUES (46, 'DACIA', NULL, 1);
INSERT INTO marca VALUES (47, 'DACIA', NULL, 1);
INSERT INTO marca VALUES (48, 'DAEWOO', NULL, 1);
INSERT INTO marca VALUES (49, 'DAF', NULL, 1);
INSERT INTO marca VALUES (50, 'DAIHATSU', NULL, 1);
INSERT INTO marca VALUES (51, 'DAIHATSU', NULL, 1);
INSERT INTO marca VALUES (52, 'DAIMLER/BENZ', NULL, 1);
INSERT INTO marca VALUES (53, 'DAMAS', NULL, 1);
INSERT INTO marca VALUES (54, 'DE SOTO', NULL, 1);
INSERT INTO marca VALUES (55, 'DELAGE', NULL, 1);
INSERT INTO marca VALUES (56, 'DODGE', NULL, 1);
INSERT INTO marca VALUES (57, 'DORSAY', NULL, 1);
INSERT INTO marca VALUES (58, 'DORSAY', NULL, 1);
INSERT INTO marca VALUES (59, 'EAGLE', NULL, 1);
INSERT INTO marca VALUES (60, 'EBRO', NULL, 1);
INSERT INTO marca VALUES (61, 'EBRO', NULL, 1);
INSERT INTO marca VALUES (62, 'ELANTRA', NULL, 1);
INSERT INTO marca VALUES (63, 'EMECA', NULL, 1);
INSERT INTO marca VALUES (64, 'EMPIRE', NULL, 1);
INSERT INTO marca VALUES (65, 'ENCAVA', NULL, 1);
INSERT INTO marca VALUES (66, 'ENCAVA', NULL, 1);
INSERT INTO marca VALUES (67, 'EXEC', NULL, 1);
INSERT INTO marca VALUES (68, 'FAB. EXTRANJERA', NULL, 1);
INSERT INTO marca VALUES (69, 'FABR. EXTRANJER', NULL, 1);
INSERT INTO marca VALUES (70, 'FABRICACION NACION', NULL, 1);
INSERT INTO marca VALUES (71, 'FABRIMONCA', NULL, 1);
INSERT INTO marca VALUES (72, 'FARGO', NULL, 1);
INSERT INTO marca VALUES (73, 'FARGO', NULL, 1);
INSERT INTO marca VALUES (74, 'FASA', NULL, 1);
INSERT INTO marca VALUES (75, 'FERRARI', NULL, 1);
INSERT INTO marca VALUES (76, 'FIAT', NULL, 1);
INSERT INTO marca VALUES (77, 'FORD', NULL, 1);
INSERT INTO marca VALUES (78, 'FRAB', NULL, 1);
INSERT INTO marca VALUES (79, 'FRAB', NULL, 1);
INSERT INTO marca VALUES (80, 'FRAHUF', NULL, 1);
INSERT INTO marca VALUES (81, 'FREIGHTIJNER', NULL, 1);
INSERT INTO marca VALUES (82, 'FREIGHTLINER', NULL, 1);
INSERT INTO marca VALUES (83, 'freightliner', NULL, 1);
INSERT INTO marca VALUES (84, 'FRUEAUF', NULL, 1);
INSERT INTO marca VALUES (85, 'FRUEHAUF', NULL, 1);
INSERT INTO marca VALUES (86, 'FULGOVEN', NULL, 1);
INSERT INTO marca VALUES (87, 'GARWOOD', NULL, 1);
INSERT INTO marca VALUES (88, 'GAZ', NULL, 1);
INSERT INTO marca VALUES (89, 'GENERAL MOTOR', NULL, 1);
INSERT INTO marca VALUES (90, 'GINDY', NULL, 1);
INSERT INTO marca VALUES (91, 'G.M.C', NULL, 1);
INSERT INTO marca VALUES (92, 'GMC', NULL, 1);
INSERT INTO marca VALUES (93, 'GREAT DANE', NULL, 1);
INSERT INTO marca VALUES (94, 'GREAT DANE', NULL, 1);
INSERT INTO marca VALUES (95, 'GREAT WALL', NULL, 1);
INSERT INTO marca VALUES (96, 'GROVE', NULL, 1);
INSERT INTO marca VALUES (97, 'grt', NULL, 1);
INSERT INTO marca VALUES (98, 'GTAN', NULL, 1);
INSERT INTO marca VALUES (99, 'GURI', NULL, 1);
INSERT INTO marca VALUES (100, 'GUZZI', NULL, 1);
INSERT INTO marca VALUES (101, 'HACKEY & SON', NULL, 1);
INSERT INTO marca VALUES (102, 'HARLEY DAVIDSON', NULL, 1);
INSERT INTO marca VALUES (103, 'HARLEY DAVIDSON', NULL, 1);
INSERT INTO marca VALUES (104, 'HERO PUCH', NULL, 1);
INSERT INTO marca VALUES (105, 'HESPERO', NULL, 1);
INSERT INTO marca VALUES (106, 'HIGHWAY', NULL, 1);
INSERT INTO marca VALUES (107, 'HILLLMAN', NULL, 1);
INSERT INTO marca VALUES (108, 'HONDA', NULL, 1);
INSERT INTO marca VALUES (109, 'HONDA', NULL, 1);
INSERT INTO marca VALUES (110, 'HOOBS', NULL, 1);
INSERT INTO marca VALUES (111, 'HORNET', NULL, 1);
INSERT INTO marca VALUES (112, 'HUMMER', NULL, 1);
INSERT INTO marca VALUES (113, 'HYUNDAI', NULL, 1);
INSERT INTO marca VALUES (114, 'IMBUS', NULL, 1);
INSERT INTO marca VALUES (115, 'IMMECA', NULL, 1);
INSERT INTO marca VALUES (116, 'INBUS', NULL, 1);
INSERT INTO marca VALUES (117, 'INCA', NULL, 1);
INSERT INTO marca VALUES (118, 'INTERNACIONAL', NULL, 1);
INSERT INTO marca VALUES (119, 'ISUZU', NULL, 1);
INSERT INTO marca VALUES (120, 'IVECO', NULL, 1);
INSERT INTO marca VALUES (121, 'IVECO', NULL, 1);
INSERT INTO marca VALUES (123, 'IZ.', NULL, 1);
INSERT INTO marca VALUES (124, 'JAGUAR', NULL, 1);
INSERT INTO marca VALUES (125, 'JEEP', NULL, 1);
INSERT INTO marca VALUES (126, 'JIALING', NULL, 1);
INSERT INTO marca VALUES (127, 'JIANSHE', NULL, 1);
INSERT INTO marca VALUES (128, 'JUVE', NULL, 1);
INSERT INTO marca VALUES (129, 'KARI COOL', NULL, 1);
INSERT INTO marca VALUES (130, 'KAWAZAKI', NULL, 1);
INSERT INTO marca VALUES (131, 'KI', NULL, 1);
INSERT INTO marca VALUES (132, 'KIA', NULL, 1);
INSERT INTO marca VALUES (133, 'KIA MOTORS', NULL, 1);
INSERT INTO marca VALUES (134, 'KIDROM', NULL, 1);
INSERT INTO marca VALUES (135, 'KODIAK', NULL, 1);
INSERT INTO marca VALUES (136, 'KOLOKA', NULL, 1);
INSERT INTO marca VALUES (137, 'KORACA', NULL, 1);
INSERT INTO marca VALUES (138, 'LADA', NULL, 1);
INSERT INTO marca VALUES (139, 'LANCIA', NULL, 1);
INSERT INTO marca VALUES (140, 'LAND ROVER', NULL, 1);
INSERT INTO marca VALUES (141, 'LANDOLFO', NULL, 1);
INSERT INTO marca VALUES (142, 'LAVAL', NULL, 1);
INSERT INTO marca VALUES (143, 'LAVAL', NULL, 1);
INSERT INTO marca VALUES (144, 'LD', NULL, 1);
INSERT INTO marca VALUES (145, 'LEGACY', NULL, 1);
INSERT INTO marca VALUES (146, 'LEXUS', NULL, 1);
INSERT INTO marca VALUES (147, 'LEXUS', NULL, 1);
INSERT INTO marca VALUES (148, 'LEYLAND', NULL, 1);
INSERT INTO marca VALUES (149, 'LINCOLN', NULL, 1);
INSERT INTO marca VALUES (150, 'LML', NULL, 1);
INSERT INTO marca VALUES (151, 'lml', NULL, 1);
INSERT INTO marca VALUES (152, 'LORAIN', NULL, 1);
INSERT INTO marca VALUES (153, 'MACK', NULL, 1);
INSERT INTO marca VALUES (154, 'MANAURE', NULL, 1);
INSERT INTO marca VALUES (155, 'MANAURE', NULL, 1);
INSERT INTO marca VALUES (156, 'MARA', NULL, 1);
INSERT INTO marca VALUES (157, 'MASERATTI', NULL, 1);
INSERT INTO marca VALUES (158, 'MAZDA', NULL, 1);
INSERT INTO marca VALUES (159, 'MAZON', NULL, 1);
INSERT INTO marca VALUES (160, 'MERCEDES BENZ', NULL, 1);
INSERT INTO marca VALUES (161, 'MERCURY', NULL, 1);
INSERT INTO marca VALUES (162, 'MERCURY', NULL, 1);
INSERT INTO marca VALUES (163, 'MILLER', NULL, 1);
INSERT INTO marca VALUES (164, 'MINI', NULL, 1);
INSERT INTO marca VALUES (165, 'MINICORD', NULL, 1);
INSERT INTO marca VALUES (166, 'MINSK', NULL, 1);
INSERT INTO marca VALUES (167, 'MINSK', NULL, 1);
INSERT INTO marca VALUES (168, 'MITSUBISHI', NULL, 1);
INSERT INTO marca VALUES (169, 'MONTESA', NULL, 1);
INSERT INTO marca VALUES (170, 'MOVILOFFICE', NULL, 1);
INSERT INTO marca VALUES (171, 'MZ', NULL, 1);
INSERT INTO marca VALUES (172, 'NISSAN', NULL, 1);
INSERT INTO marca VALUES (173, 'NOSVI', NULL, 1);
INSERT INTO marca VALUES (174, 'NUFFELD', NULL, 1);
INSERT INTO marca VALUES (175, 'OLDSMOBILE', NULL, 1);
INSERT INTO marca VALUES (176, 'OLDSMOBILE', NULL, 1);
INSERT INTO marca VALUES (177, 'OM', NULL, 1);
INSERT INTO marca VALUES (178, 'OMEGA', NULL, 1);
INSERT INTO marca VALUES (179, 'OPEL', NULL, 1);
INSERT INTO marca VALUES (180, 'OPEL', NULL, 1);
INSERT INTO marca VALUES (181, 'ORINOCO', NULL, 1);
INSERT INTO marca VALUES (182, 'ORINOCO', NULL, 1);
INSERT INTO marca VALUES (183, 'OSHKOSH', NULL, 1);
INSERT INTO marca VALUES (184, 'OXICAR', NULL, 1);
INSERT INTO marca VALUES (185, 'P Y H', NULL, 1);
INSERT INTO marca VALUES (186, 'PEGASO', NULL, 1);
INSERT INTO marca VALUES (187, 'PEGOUT', NULL, 1);
INSERT INTO marca VALUES (188, 'Pendiente', NULL, 1);
INSERT INTO marca VALUES (189, 'PETTIBONE', NULL, 1);
INSERT INTO marca VALUES (190, 'PETTIGONE', NULL, 1);
INSERT INTO marca VALUES (191, 'PEUGEOT', NULL, 1);
INSERT INTO marca VALUES (192, 'P$H', NULL, 1);
INSERT INTO marca VALUES (193, 'PIAGGIO', NULL, 1);
INSERT INTO marca VALUES (194, 'PITMAN', NULL, 1);
INSERT INTO marca VALUES (195, 'PLACENCIA', NULL, 1);
INSERT INTO marca VALUES (196, 'PLAYMUTH', NULL, 1);
INSERT INTO marca VALUES (197, 'PLYMONTH', NULL, 1);
INSERT INTO marca VALUES (198, 'PLYMOUTH', NULL, 1);
INSERT INTO marca VALUES (199, 'PONTIAC', NULL, 1);
INSERT INTO marca VALUES (200, 'PONTIAC', NULL, 1);
INSERT INTO marca VALUES (201, 'PORSCHE', NULL, 1);
INSERT INTO marca VALUES (202, 'RAMBLER', NULL, 1);
INSERT INTO marca VALUES (203, 'RAMBLER', NULL, 1);
INSERT INTO marca VALUES (204, 'RAMBLER', NULL, 1);
INSERT INTO marca VALUES (205, 'RAMBLER', NULL, 1);
INSERT INTO marca VALUES (206, 'RANGE ROVER', NULL, 1);
INSERT INTO marca VALUES (207, 'REBEL', NULL, 1);
INSERT INTO marca VALUES (208, 'REMIVECA', NULL, 1);
INSERT INTO marca VALUES (209, 'RENAULT', NULL, 1);
INSERT INTO marca VALUES (210, 'REO', NULL, 1);
INSERT INTO marca VALUES (211, 'ROBERT', NULL, 1);
INSERT INTO marca VALUES (212, 'ROLLS ROYCE', NULL, 1);
INSERT INTO marca VALUES (213, 'ROVER', NULL, 1);
INSERT INTO marca VALUES (214, 'ROVER', NULL, 1);
INSERT INTO marca VALUES (215, 'RX-115', NULL, 1);
INSERT INTO marca VALUES (216, 'SAAB', NULL, 1);
INSERT INTO marca VALUES (217, 'SCOOTERS', NULL, 1);
INSERT INTO marca VALUES (218, 'SEAT', NULL, 1);
INSERT INTO marca VALUES (219, 'SKODA', NULL, 1);
INSERT INTO marca VALUES (220, 'SQUATDOWN', NULL, 1);
INSERT INTO marca VALUES (221, 'STEYR PUCH', NULL, 1);
INSERT INTO marca VALUES (222, 'STRI', NULL, 1);
INSERT INTO marca VALUES (223, 'SUBARU', NULL, 1);
INSERT INTO marca VALUES (224, 'SUBARU', NULL, 1);
INSERT INTO marca VALUES (225, 'SUBARU', NULL, 1);
INSERT INTO marca VALUES (226, 'SUMBEAN', NULL, 1);
INSERT INTO marca VALUES (227, 'SUMECAN', NULL, 1);
INSERT INTO marca VALUES (228, 'SUPER JOG', NULL, 1);
INSERT INTO marca VALUES (229, 'SUZUKI', NULL, 1);
INSERT INTO marca VALUES (230, 'SWIFT', NULL, 1);
INSERT INTO marca VALUES (231, 'TASCA', NULL, 1);
INSERT INTO marca VALUES (232, 'TASCA', NULL, 1);
INSERT INTO marca VALUES (233, 'TASCA', NULL, 1);
INSERT INTO marca VALUES (234, 'TE AMO', NULL, 1);
INSERT INTO marca VALUES (235, 'TI MPT', NULL, 1);
INSERT INTO marca VALUES (236, 'TITAN', NULL, 1);
INSERT INTO marca VALUES (237, 'TOLEDO SIGNO AUT.', NULL, 1);
INSERT INTO marca VALUES (238, 'TOYOTA', NULL, 1);
INSERT INTO marca VALUES (239, 'TRAIL', NULL, 1);
INSERT INTO marca VALUES (240, 'TRAILER', NULL, 1);
INSERT INTO marca VALUES (241, 'TRAILMOBILE', NULL, 1);
INSERT INTO marca VALUES (242, 'TRAYLER', NULL, 1);
INSERT INTO marca VALUES (243, 'TRAYLER', NULL, 1);
INSERT INTO marca VALUES (244, 'TRIMOVILE', NULL, 1);
INSERT INTO marca VALUES (245, 'TUPAMET', NULL, 1);
INSERT INTO marca VALUES (246, 'uti', NULL, 1);
INSERT INTO marca VALUES (247, 'UTIL', NULL, 1);
INSERT INTO marca VALUES (248, 'UTILITY', NULL, 1);
INSERT INTO marca VALUES (249, 'VALIANT', NULL, 1);
INSERT INTO marca VALUES (250, 'VESPA', NULL, 1);
INSERT INTO marca VALUES (251, 'VESPA', NULL, 1);
INSERT INTO marca VALUES (252, 'VIPSA', NULL, 1);
INSERT INTO marca VALUES (253, 'VOLKSWAGEN', NULL, 1);
INSERT INTO marca VALUES (254, 'VOLVO', NULL, 1);
INSERT INTO marca VALUES (255, 'WALKER', NULL, 1);
INSERT INTO marca VALUES (256, 'WILLYS', NULL, 1);
INSERT INTO marca VALUES (257, 'YAMAHA', NULL, 1);
INSERT INTO marca VALUES (258, 'ZH', NULL, 1);
INSERT INTO marca VALUES (13, 'ASTRA', NULL, 1);
INSERT INTO marca VALUES (259, 'TEST', NULL, 1);
INSERT INTO marca VALUES (3, 'ACURA', NULL, 1);
INSERT INTO marca VALUES (1, 'ACADIAN', NULL, 0);
INSERT INTO marca VALUES (2, 'ACURA', NULL, 1);
INSERT INTO marca VALUES (261, 'HJFDJFDJ', NULL, 1);
INSERT INTO marca VALUES (262, 'MONTECARLO', NULL, 0);
INSERT INTO marca VALUES (264, 'SDGGSDFHF', NULL, 0);
INSERT INTO marca VALUES (265, 'RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR', NULL, 0);
INSERT INTO marca VALUES (122, 'IZ', NULL, 1);
INSERT INTO marca VALUES (260, 'AAAAAB', NULL, 1);


--
-- Data for Name: mod_mar; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO mod_mar VALUES (1, 10, 1);
INSERT INTO mod_mar VALUES (2, 10, 7);
INSERT INTO mod_mar VALUES (3, 2, 10);
INSERT INTO mod_mar VALUES (4, 11, 10);


--
-- Data for Name: modelo; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO modelo VALUES (2, 'AB50XB', 1);
INSERT INTO modelo VALUES (3, 'ACADIAN', 1);
INSERT INTO modelo VALUES (5, 'ACCENT GS 1. 5L AT', 1);
INSERT INTO modelo VALUES (6, 'ACCENT GS 1.5L M-T', 1);
INSERT INTO modelo VALUES (7, 'ACCORD', 1);
INSERT INTO modelo VALUES (8, 'ACCORD EX', 1);
INSERT INTO modelo VALUES (9, 'AD-WAGON VAGONETA', 1);
INSERT INTO modelo VALUES (10, 'ADYSSEY EX', 1);
INSERT INTO modelo VALUES (11, 'AEROSTAR', 1);
INSERT INTO modelo VALUES (12, 'AERO100', 1);
INSERT INTO modelo VALUES (13, 'AGAMAR', 1);
INSERT INTO modelo VALUES (14, 'ALBANO', 1);
INSERT INTO modelo VALUES (15, 'ALFA', 1);
INSERT INTO modelo VALUES (16, 'ALFA RICCO', 1);
INSERT INTO modelo VALUES (17, 'ALFA ROMEO ALFA 146 16V', 1);
INSERT INTO modelo VALUES (18, 'ALFA SPIDER LUS', 1);
INSERT INTO modelo VALUES (19, 'ALFA 146', 1);
INSERT INTO modelo VALUES (20, 'ALFA 14616VT5TX200000', 1);
INSERT INTO modelo VALUES (21, 'ALFA 156 RICCO', 1);
INSERT INTO modelo VALUES (22, 'ALKON', 1);
INSERT INTO modelo VALUES (23, 'ALL AMERICA', 1);
INSERT INTO modelo VALUES (24, 'ALL AMERICAN', 1);
INSERT INTO modelo VALUES (25, 'ALLEGRO', 1);
INSERT INTO modelo VALUES (26, 'ALTEA FSI STYLANCE AUT.', 1);
INSERT INTO modelo VALUES (27, 'ALTIMA', 1);
INSERT INTO modelo VALUES (28, 'ALTO', 1);
INSERT INTO modelo VALUES (29, 'AMBULANCIA', 1);
INSERT INTO modelo VALUES (30, 'AMIGO', 1);
INSERT INTO modelo VALUES (1, 'AB-30-F-', 1);
INSERT INTO modelo VALUES (4, 'ACCENT', 1);
INSERT INTO modelo VALUES (1446, 'LASER EFI', 1);
INSERT INTO modelo VALUES (1439, 'VERDEGALLO', 0);
INSERT INTO modelo VALUES (1436, '14367', 1);


--
-- Data for Name: ramo_imp; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO ramo_imp VALUES (1, 301040500, 'PATENTE DE INDUSTRIA Y COMERCIO', 'IDC', 2006, 1);
INSERT INTO ramo_imp VALUES (4, 301040600, 'PATENTE DE VEHICULOS', 'VH', 2006, 1);
INSERT INTO ramo_imp VALUES (2, 301140401, 'ARRENDAMIENTO DE INMUEBLES', 'AI', 2006, 1);
INSERT INTO ramo_imp VALUES (3, 301021100, 'APUESTAS LICITAS', 'AL', 2006, 1);
INSERT INTO ramo_imp VALUES (7, 301040700, 'PUBLICIDAD Y ESPECTACULOS', 'PE', 2006, 1);


--
-- Data for Name: ramo_transaccion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO ramo_transaccion VALUES (4, 4, 1, 2006);
INSERT INTO ramo_transaccion VALUES (5, 4, 3, 2006);
INSERT INTO ramo_transaccion VALUES (8, 4, 6, 2006);
INSERT INTO ramo_transaccion VALUES (7, 1, 5, 2006);


--
-- Data for Name: sancion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO sancion VALUES (1, 'PAGO', 0, NULL, NULL);
INSERT INTO sancion VALUES (4, 'SANCION', 1, 2006, 1566.98);
INSERT INTO sancion VALUES (2, 'DEUDA', 0, 2006, 0);
INSERT INTO sancion VALUES (6, 'CONTRARIO AL ORDEN PUBLICO', 1, NULL, 100000);
INSERT INTO sancion VALUES (7, 'CONTRARIO AL ORDEN PUBLICO', 1, NULL, 100000);
INSERT INTO sancion VALUES (8, 'CONTRARIO AL ORDEN PUBLICO', 1, NULL, 100000);
INSERT INTO sancion VALUES (5, 'CAMBIO COLOR', 1, 2006, 500);


--
-- Data for Name: solvencia; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO solvencia VALUES (1, '1982-09-29', '2002-09-29', 1555.55, 2789.9899999999998);
INSERT INTO solvencia VALUES (3, '2006-03-15', '2006-03-15', 1550.5599999999999, 2568.0500000000002);
INSERT INTO solvencia VALUES (2, '2006-03-14', '2006-03-14', 1333.96, 7895.5299999999997);
INSERT INTO solvencia VALUES (4, '2006-04-21', '2006-04-21', 12, 1566.8);


--
-- Data for Name: tasa_bancaria; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO tasa_bancaria VALUES (2, 12, 2006, 2006);
INSERT INTO tasa_bancaria VALUES (3, 1, 2005, 2121);
INSERT INTO tasa_bancaria VALUES (4, 10, 2004, 788599878);
INSERT INTO tasa_bancaria VALUES (1, 12, 2006, 2006.1500000000001);
INSERT INTO tasa_bancaria VALUES (6, 10, 2007, 5000.1199999999999);


--
-- Data for Name: tasa_inscripcion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO tasa_inscripcion VALUES (1, '2006-01-01', '2006-12-31', 250);


--
-- Data for Name: tipo; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO tipo VALUES (3, 'AUTOBUSETE', 0);
INSERT INTO tipo VALUES (4, 'BATEA', 0);
INSERT INTO tipo VALUES (7, 'B-750', 0);
INSERT INTO tipo VALUES (8, 'CABINA', 0);
INSERT INTO tipo VALUES (9, 'CAMION', 0);
INSERT INTO tipo VALUES (11, 'CAMIONETA', 0);
INSERT INTO tipo VALUES (12, 'CASA RODANTE', 0);
INSERT INTO tipo VALUES (13, 'CASILLERO', 0);
INSERT INTO tipo VALUES (14, 'CAVA', 0);
INSERT INTO tipo VALUES (15, 'CHASIS', 0);
INSERT INTO tipo VALUES (16, 'CHEVY', 0);
INSERT INTO tipo VALUES (17, 'CHUTO', 0);
INSERT INTO tipo VALUES (18, 'CISTERNA', 0);
INSERT INTO tipo VALUES (19, 'CISTERNA', 0);
INSERT INTO tipo VALUES (20, 'COLECTIVO', 0);
INSERT INTO tipo VALUES (21, 'CONTRY', 0);
INSERT INTO tipo VALUES (22, 'COUPE', 0);
INSERT INTO tipo VALUES (23, 'ENDURO', 0);
INSERT INTO tipo VALUES (24, 'ESTACAS', 0);
INSERT INTO tipo VALUES (25, 'FUNERARIO', 0);
INSERT INTO tipo VALUES (26, 'FURGON', 0);
INSERT INTO tipo VALUES (27, 'FURGON', 0);
INSERT INTO tipo VALUES (28, 'GANDOLA', 0);
INSERT INTO tipo VALUES (29, 'GEOFISICO', 0);
INSERT INTO tipo VALUES (30, 'GRUA', 0);
INSERT INTO tipo VALUES (31, 'ISUZU', 0);
INSERT INTO tipo VALUES (32, 'JAULA', 0);
INSERT INTO tipo VALUES (33, 'LLANERO', 0);
INSERT INTO tipo VALUES (34, 'MAQUINA', 0);
INSERT INTO tipo VALUES (35, 'MEZCLADORA', 0);
INSERT INTO tipo VALUES (36, 'MINIBUS', 0);
INSERT INTO tipo VALUES (37, 'MINIBUS', 0);
INSERT INTO tipo VALUES (38, 'MOTO', 0);
INSERT INTO tipo VALUES (39, 'MOTORHOME', 0);
INSERT INTO tipo VALUES (40, 'PANEL', 0);
INSERT INTO tipo VALUES (41, 'PART', 0);
INSERT INTO tipo VALUES (42, 'PASEO', 0);
INSERT INTO tipo VALUES (43, 'PASEO', 0);
INSERT INTO tipo VALUES (44, 'PENDIENTE', 0);
INSERT INTO tipo VALUES (45, 'PERFORACION', 0);
INSERT INTO tipo VALUES (46, 'PICK-UP', 0);
INSERT INTO tipo VALUES (47, 'PLATAFORMA', 0);
INSERT INTO tipo VALUES (48, 'RANCHERA', 0);
INSERT INTO tipo VALUES (49, 'REMOLQUE', 0);
INSERT INTO tipo VALUES (50, 'REMOLQUE/TANQUE', 0);
INSERT INTO tipo VALUES (51, 'RUSTICO', 0);
INSERT INTO tipo VALUES (52, 'SEDAN', 0);
INSERT INTO tipo VALUES (53, 'SPORT-WAGON', 0);
INSERT INTO tipo VALUES (54, 'TANQUE', 0);
INSERT INTO tipo VALUES (55, 'TECHO DE LONA', 0);
INSERT INTO tipo VALUES (56, 'TECHO DURO', 0);
INSERT INTO tipo VALUES (57, 'TITAN', 0);
INSERT INTO tipo VALUES (58, 'TRACTOR', 0);
INSERT INTO tipo VALUES (59, 'TRACTOR', 0);
INSERT INTO tipo VALUES (60, 'TRAILER', 0);
INSERT INTO tipo VALUES (61, 'TRILLER', 0);
INSERT INTO tipo VALUES (62, 'VAN', 0);
INSERT INTO tipo VALUES (63, 'VEHICULO ESPECIAL', 0);
INSERT INTO tipo VALUES (64, 'VIVIENDA', 0);
INSERT INTO tipo VALUES (65, 'VOLTEO', 0);
INSERT INTO tipo VALUES (10, 'CAMION', 1);
INSERT INTO tipo VALUES (67, 'Z-BUS', 0);
INSERT INTO tipo VALUES (6, 'BUS', 1);
INSERT INTO tipo VALUES (2, 'AUTOBUS', 1);
INSERT INTO tipo VALUES (1, 'AMBULANCIA', 1);
INSERT INTO tipo VALUES (5, 'BLINDADO', 1);
INSERT INTO tipo VALUES (68, 'AMBULANCIA', 0);
INSERT INTO tipo VALUES (66, '4X4', 1);


--
-- Data for Name: tipo_transaccion; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO tipo_transaccion VALUES (1, 'IMPUESTO', '000005', 'IV', 2006, 1);
INSERT INTO tipo_transaccion VALUES (5, 'TASA DE INSCRIPCIÓN', '000005', 'TI', 2006, 1);
INSERT INTO tipo_transaccion VALUES (6, 'CALCOMANÍA', '4010000000000', 'CL', 2006, 1);
INSERT INTO tipo_transaccion VALUES (3, 'RECARGO', '000001', 'RC', 2006, 1);


--
-- Data for Name: tipo_veh_segun_gaceta; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO tipo_veh_segun_gaceta VALUES (2, 'AUTOMOVILES Y CAMIONETAS DE USO PARTICULAR', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (3, 'CAMIONETAS DE PASAJERO O BUSETAS HASTA 32 P', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (4, 'MINIBUSES O AUTOBUSES CAPACIDAD MAYOR DE 32 P', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (5, 'CAMION HASTA TRES (3) TONELADAS -350', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (6, 'CAMION DE TRES (3) A DIEZ (10) TONELADAS 600-750', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (9, 'TRAILERS Y VENTAS AMBULANTES SERVICIOS', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (10, 'TRAILER PARA ACTIVIDAD TURISTICA', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (1, 'MOTOCICLETA', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (8, 'BATEAS', 1);
INSERT INTO tipo_veh_segun_gaceta VALUES (7, 'CHUTOS', 1);


--
-- Data for Name: uso; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO uso VALUES (3, 'BLANCO Y VERDE', 0);
INSERT INTO uso VALUES (4, 'CARGA', 0);
INSERT INTO uso VALUES (5, 'COLECTIVO PRIVADO', 0);
INSERT INTO uso VALUES (6, 'COLECTIVO TURISMO', 0);
INSERT INTO uso VALUES (8, 'GATO HIDRAULICO', 0);
INSERT INTO uso VALUES (10, 'GRUA', 0);
INSERT INTO uso VALUES (11, 'HORGONERO TROMPO', 0);
INSERT INTO uso VALUES (12, 'HORMIGONERO', 0);
INSERT INTO uso VALUES (13, 'HORMIGONERO TROMPO', 0);
INSERT INTO uso VALUES (14, 'LOBELIA', 0);
INSERT INTO uso VALUES (15, 'LOW-BOY', 0);
INSERT INTO uso VALUES (16, 'LOW-BOY', 0);
INSERT INTO uso VALUES (17, 'MARFIL Y NARANJA', 0);
INSERT INTO uso VALUES (18, 'MICROBUS', 0);
INSERT INTO uso VALUES (19, 'OFICIAL', 0);
INSERT INTO uso VALUES (20, 'PARTICULAR', 0);
INSERT INTO uso VALUES (21, 'PEMDIENTE', 0);
INSERT INTO uso VALUES (23, 'TRABAJO (SOLO MOTO)', 0);
INSERT INTO uso VALUES (24, 'TRANP. LIQUIDO', 0);
INSERT INTO uso VALUES (25, 'TRANS. BEB. GASEOSAS', 0);
INSERT INTO uso VALUES (26, 'TRANS.DE GANADO', 0);
INSERT INTO uso VALUES (27, 'TRANSP. ALIMENTOS', 0);
INSERT INTO uso VALUES (28, 'TRANSPORTE DE VALORES', 0);
INSERT INTO uso VALUES (29, 'TRANSPORTE ESCOLAR', 0);
INSERT INTO uso VALUES (30, 'TRANSPORTE MINERALES', 0);
INSERT INTO uso VALUES (31, 'TRANSPORTE PRIVADO', 0);
INSERT INTO uso VALUES (32, 'TRANSPORTE PUBLICO', 0);
INSERT INTO uso VALUES (33, 'TROMPO', 0);
INSERT INTO uso VALUES (9, 'GATO HIDRAUL', 1);
INSERT INTO uso VALUES (34, 'Z-USO', 0);
INSERT INTO uso VALUES (1, 'ANARANJADO', 1);
INSERT INTO uso VALUES (7, 'EXPORTACION', 1);
INSERT INTO uso VALUES (22, 'TAXI', 1);
INSERT INTO uso VALUES (35, 'PASEO', 0);
INSERT INTO uso VALUES (36, 'VIAJE', 1);
INSERT INTO uso VALUES (2, 'BATEA', 0);
INSERT INTO uso VALUES (37, 'ABCDE', 0);


--
-- Data for Name: vehiculo; Type: TABLE DATA; Schema: vehiculo; Owner: puser
--

INSERT INTO vehiculo VALUES (2, '00089', '123', '2006-03-23', 7, NULL, NULL, NULL, 2000, '889800', 4, 5, 24, 7, 66, '01/03/2006', 15500, 2, 0, 'text 2', 'S', NULL, 1999, 2, NULL, NULL, NULL, '1', NULL, NULL);
INSERT INTO vehiculo VALUES (1, '33', 'GGG-987', '2006-03-22', 9, NULL, '2006-04-05', NULL, 2000, '15', 8, 5, 5, 7, 1, '01/03/2006', 9123, 0, 1323123, 'sss', 'N', NULL, 1233, 0, NULL, NULL, NULL, '0', NULL, NULL);
INSERT INTO vehiculo VALUES (14, '12345', 'GAF-40J', '2006-05-22', 42, NULL, '2006-05-22', NULL, 1990, '12345', 251, 22, 12, 36, 1, '09/05/1984', 100, 0, 300000, '', 'N', NULL, 2006, 0, NULL, NULL, NULL, '0', NULL, 1);
INSERT INTO vehiculo VALUES (12, 'ABCD1234', 'GAF-37J', '2006-05-10', 36, NULL, '2006-05-22', NULL, 1996, 'MT1234', 103, 2, 98, 36, 1, '26/12/1996', 300, 0, 10200000, '', 'N', NULL, 0, 0, NULL, NULL, NULL, '0', NULL, 1);
INSERT INTO vehiculo VALUES (13, '12345', 'GAF-39J', '2006-05-22', 42, NULL, '2006-05-22', NULL, 1987, '12345', 250, 27, 5, 22, 1, '22/05/1987', 100, 0, 0, '', 'N', NULL, 1987, 0, NULL, NULL, NULL, '0', NULL, 0);
INSERT INTO vehiculo VALUES (15, 'ABCDEFG', 'XXK-458', '2006-05-30', 34, NULL, '2006-05-30', NULL, 1993, '12345', 35, 16, 76, 36, 2, '01/04/1993', 1400, 0, 1700000, 'Nada', 'N', NULL, 2002, 0, NULL, NULL, NULL, '1', NULL, 0);


SET search_path = finanzas, pg_catalog;

--
-- Name: condiciones_pago_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY condiciones_pago
    ADD CONSTRAINT condiciones_pago_pkey PRIMARY KEY (id);


--
-- Name: fi_retadi_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY retenciones_adiciones
    ADD CONSTRAINT fi_retadi_pkey PRIMARY KEY (id);


--
-- Name: relacion_retenciones_solicitud_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_retenciones_solicitud
    ADD CONSTRAINT relacion_retenciones_solicitud_pkey PRIMARY KEY (id);


--
-- Name: solicitud_pago_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY solicitud_pago
    ADD CONSTRAINT solicitud_pago_pkey PRIMARY KEY (nrodoc);


--
-- Name: tipos_solicitud_sin_imp_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipos_solicitud_sin_imp
    ADD CONSTRAINT tipos_solicitud_sin_imp_pkey PRIMARY KEY (id);


--
-- Name: tipos_solicitudes_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipos_solicitudes
    ADD CONSTRAINT tipos_solicitudes_pkey PRIMARY KEY (id);


SET search_path = publicidad, pg_catalog;

--
-- Name: base_calculo_pub_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY base_calculo_pub
    ADD CONSTRAINT base_calculo_pub_pkey PRIMARY KEY (id);


--
-- Name: clasificacion_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY clasificacion
    ADD CONSTRAINT clasificacion_pkey PRIMARY KEY (cod_cla);


--
-- Name: entradas_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY entradas
    ADD CONSTRAINT entradas_pkey PRIMARY KEY (id);


--
-- Name: esp_costo_pub_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY esp_costo_pub
    ADD CONSTRAINT esp_costo_pub_pkey PRIMARY KEY (id);


--
-- Name: espectaculo_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY espectaculo
    ADD CONSTRAINT espectaculo_pkey PRIMARY KEY (id);


--
-- Name: inspector_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY inspector
    ADD CONSTRAINT inspector_pkey PRIMARY KEY (cod_ins);


--
-- Name: multas_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY multa
    ADD CONSTRAINT multas_pkey PRIMARY KEY (cod_mul);


--
-- Name: propaganda_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY propaganda
    ADD CONSTRAINT propaganda_pkey PRIMARY KEY (id);


--
-- Name: publicidad_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY publicidad
    ADD CONSTRAINT publicidad_pkey PRIMARY KEY (id);


--
-- Name: relacion_publicidad_publicidades_pkey; Type: CONSTRAINT; Schema: publicidad; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_publicidad_publicidades
    ADD CONSTRAINT relacion_publicidad_publicidades_pkey PRIMARY KEY (id);


SET search_path = puser, pg_catalog;

--
-- Name: activo_inactivo_producto_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY activo_inactivo_producto
    ADD CONSTRAINT activo_inactivo_producto_id_key UNIQUE (id);


--
-- Name: asignaciones_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY asignaciones
    ADD CONSTRAINT asignaciones_pkey PRIMARY KEY (id);


--
-- Name: caja_chica_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY caja_chica
    ADD CONSTRAINT caja_chica_pkey PRIMARY KEY (id);


--
-- Name: cargos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY cargos
    ADD CONSTRAINT cargos_pkey PRIMARY KEY (id);


--
-- Name: categorias_programaticas_hist_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY categorias_programaticas_hist
    ADD CONSTRAINT categorias_programaticas_hist_pkey PRIMARY KEY (id, id_escenario);


--
-- Name: categorias_programaticas_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY categorias_programaticas
    ADD CONSTRAINT categorias_programaticas_pkey PRIMARY KEY (id, id_escenario);


--
-- Name: ciudadanos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY ciudadanos
    ADD CONSTRAINT ciudadanos_pkey PRIMARY KEY (id);


--
-- Name: co_requis_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY co_requis
    ADD CONSTRAINT co_requis_id_key UNIQUE (id);


--
-- Name: co_rtpro_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY co_rtpro
    ADD CONSTRAINT co_rtpro_id_key UNIQUE (id);


--
-- Name: condicion_pago_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY condicion_pago
    ADD CONSTRAINT condicion_pago_pkey PRIMARY KEY (id);


--
-- Name: contrato_obras_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY contrato_obras
    ADD CONSTRAINT contrato_obras_pkey PRIMARY KEY (id);


--
-- Name: contrato_servicio_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY contrato_servicio
    ADD CONSTRAINT contrato_servicio_pkey PRIMARY KEY (id);


--
-- Name: entidades_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY entidades
    ADD CONSTRAINT entidades_pkey PRIMARY KEY (id);


--
-- Name: escenarios_hist_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY escenarios_hist
    ADD CONSTRAINT escenarios_hist_pkey PRIMARY KEY (id);


--
-- Name: escenarios_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY escenarios
    ADD CONSTRAINT escenarios_pkey PRIMARY KEY (id);


--
-- Name: estado_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY estado
    ADD CONSTRAINT estado_id_key UNIQUE (id);


--
-- Name: financiamiento_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY financiamiento
    ADD CONSTRAINT financiamiento_id_key UNIQUE (id);


--
-- Name: financiamiento_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY financiamiento
    ADD CONSTRAINT financiamiento_pkey PRIMARY KEY (id);


--
-- Name: gacetas_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY gacetas
    ADD CONSTRAINT gacetas_pkey PRIMARY KEY (id);


--
-- Name: grupos_proveedores_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY grupos_proveedores
    ADD CONSTRAINT grupos_proveedores_pkey PRIMARY KEY (id);


--
-- Name: id_grupo_prove_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY id_grupo_prove
    ADD CONSTRAINT id_grupo_prove_id_key UNIQUE (id);


--
-- Name: intitucion_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY alcaldia
    ADD CONSTRAINT intitucion_pkey PRIMARY KEY (id);


--
-- Name: manejo_almacen_producto_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY manejo_almacen_producto
    ADD CONSTRAINT manejo_almacen_producto_id_key UNIQUE (id);


--
-- Name: modulos_descripcion_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY modulos
    ADD CONSTRAINT modulos_descripcion_key UNIQUE (descripcion);


--
-- Name: modulos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY modulos
    ADD CONSTRAINT modulos_pkey PRIMARY KEY (id);


--
-- Name: momento_presupuestario_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY momentos_presupuestarios
    ADD CONSTRAINT momento_presupuestario_pkey PRIMARY KEY (id);


--
-- Name: movimientos_presupuestarios_nrodoc_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY movimientos_presupuestarios
    ADD CONSTRAINT movimientos_presupuestarios_nrodoc_key UNIQUE (nrodoc);


--
-- Name: movimientos_presupuestarios_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY movimientos_presupuestarios
    ADD CONSTRAINT movimientos_presupuestarios_pkey PRIMARY KEY (id);


--
-- Name: municipios_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY municipios
    ADD CONSTRAINT municipios_id_key UNIQUE (id);


--
-- Name: nomina_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY nomina
    ADD CONSTRAINT nomina_pkey PRIMARY KEY (id);


--
-- Name: objeto_empresa_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY objeto_empresa
    ADD CONSTRAINT objeto_empresa_pkey PRIMARY KEY (id);


--
-- Name: obras_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY obras
    ADD CONSTRAINT obras_pkey PRIMARY KEY (id);


--
-- Name: operaciones_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY operaciones
    ADD CONSTRAINT operaciones_pkey PRIMARY KEY (id);


--
-- Name: orden_compra_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY orden_compra
    ADD CONSTRAINT orden_compra_pkey PRIMARY KEY (id);


--
-- Name: orden_servicio_trabajo_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY orden_servicio_trabajo
    ADD CONSTRAINT orden_servicio_trabajo_pkey PRIMARY KEY (id);


--
-- Name: organismos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY organismos
    ADD CONSTRAINT organismos_pkey PRIMARY KEY (id);


--
-- Name: parroquias_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY parroquias
    ADD CONSTRAINT parroquias_pkey PRIMARY KEY (id);


--
-- Name: partidas_presupuestarias_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY partidas_presupuestarias
    ADD CONSTRAINT partidas_presupuestarias_pkey PRIMARY KEY (id, id_escenario);


--
-- Name: politicas_disposiciones_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY politicas_disposiciones
    ADD CONSTRAINT politicas_disposiciones_pkey PRIMARY KEY (id);


--
-- Name: productos_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY productos
    ADD CONSTRAINT productos_id_key UNIQUE (id);


--
-- Name: profesiones_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY profesiones
    ADD CONSTRAINT profesiones_pkey PRIMARY KEY (id);


--
-- Name: provee_contrat_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY provee_contrat
    ADD CONSTRAINT provee_contrat_pkey PRIMARY KEY (id);


--
-- Name: provee_contrib_munic_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY provee_contrib_munic
    ADD CONSTRAINT provee_contrib_munic_id_key UNIQUE (id);


--
-- Name: proveedores_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY proveedores
    ADD CONSTRAINT proveedores_id_key UNIQUE (id);


--
-- Name: proveedores_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY proveedores
    ADD CONSTRAINT proveedores_pkey PRIMARY KEY (rif);


--
-- Name: relacion_caja_chica_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_caja_chica
    ADD CONSTRAINT relacion_caja_chica_pkey PRIMARY KEY (id);


--
-- Name: relacion_contrato_obras_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_contrato_obras
    ADD CONSTRAINT relacion_contrato_obras_pkey PRIMARY KEY (id);


--
-- Name: relacion_contrato_servicio_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_contrato_servicio
    ADD CONSTRAINT relacion_contrato_servicio_pkey PRIMARY KEY (id);


--
-- Name: relacion_movimientos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_movimientos
    ADD CONSTRAINT relacion_movimientos_pkey PRIMARY KEY (id);


--
-- Name: relacion_movimientos_productos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_movimientos_productos
    ADD CONSTRAINT relacion_movimientos_productos_pkey PRIMARY KEY (id);


--
-- Name: relacion_nomina_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_nomina
    ADD CONSTRAINT relacion_nomina_pkey PRIMARY KEY (id);


--
-- Name: relacion_obras_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_obras
    ADD CONSTRAINT relacion_obras_pkey PRIMARY KEY (id);


--
-- Name: relacion_ord_serv_trab_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ord_serv_trab
    ADD CONSTRAINT relacion_ord_serv_trab_pkey PRIMARY KEY (id);


--
-- Name: relacion_ord_serv_trab_productos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ord_serv_trab_productos
    ADD CONSTRAINT relacion_ord_serv_trab_productos_pkey PRIMARY KEY (id);


--
-- Name: relacion_ordcompra_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ordcompra
    ADD CONSTRAINT relacion_ordcompra_pkey PRIMARY KEY (id);


--
-- Name: relacion_ordcompra_prod_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ordcompra_prod
    ADD CONSTRAINT relacion_ordcompra_prod_pkey PRIMARY KEY (id);


--
-- Name: relacion_pp_cp_hist_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_pp_cp_hist
    ADD CONSTRAINT relacion_pp_cp_hist_pkey PRIMARY KEY (id_escenario, id_categoria_programatica, id_partida_presupuestaria);


--
-- Name: relacion_pp_cp_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_pp_cp
    ADD CONSTRAINT relacion_pp_cp_pkey PRIMARY KEY (id_escenario, id_categoria_programatica, id_partida_presupuestaria);


--
-- Name: relacion_producto_prove_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_producto_prove
    ADD CONSTRAINT relacion_producto_prove_id_key UNIQUE (id);


--
-- Name: relacion_req_gp_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_req_gp
    ADD CONSTRAINT relacion_req_gp_pkey PRIMARY KEY (id);


--
-- Name: relacion_req_prov_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_req_prov
    ADD CONSTRAINT relacion_req_prov_id_key UNIQUE (id);


--
-- Name: relacion_solicitud_pago_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_solicitud_pago
    ADD CONSTRAINT relacion_solicitud_pago_pkey PRIMARY KEY (id);


--
-- Name: relacion_ue_cp_hist_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ue_cp_hist
    ADD CONSTRAINT relacion_ue_cp_hist_pkey PRIMARY KEY (id_unidad_ejecutora, id_categoria_programatica, id_escenario);


--
-- Name: relacion_ue_cp_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_ue_cp
    ADD CONSTRAINT relacion_ue_cp_pkey PRIMARY KEY (id_unidad_ejecutora, id_categoria_programatica, id_escenario);


--
-- Name: relacion_us_op_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY relacion_us_op
    ADD CONSTRAINT relacion_us_op_pkey PRIMARY KEY (id);


--
-- Name: requisitos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY requisitos
    ADD CONSTRAINT requisitos_pkey PRIMARY KEY (id);


--
-- Name: retencion_iva_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY retencion_iva
    ADD CONSTRAINT retencion_iva_pkey PRIMARY KEY (id);


--
-- Name: rif_letra_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY rif_letra
    ADD CONSTRAINT rif_letra_id_key UNIQUE (id);


--
-- Name: sistema_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY sistema
    ADD CONSTRAINT sistema_pkey PRIMARY KEY (id);


--
-- Name: situaciones_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY situaciones
    ADD CONSTRAINT situaciones_pkey PRIMARY KEY (id);


--
-- Name: solvencias_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY solvencias
    ADD CONSTRAINT solvencias_pkey PRIMARY KEY (id);


--
-- Name: statu_producto_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY status_producto
    ADD CONSTRAINT statu_producto_id_key UNIQUE (id);


--
-- Name: status_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY status
    ADD CONSTRAINT status_pkey PRIMARY KEY (id);


--
-- Name: status_proveedor_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY status_proveedor
    ADD CONSTRAINT status_proveedor_id_key UNIQUE (id_);


--
-- Name: tipo_producto_clasif_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo_producto_clasif
    ADD CONSTRAINT tipo_producto_clasif_id_key UNIQUE (id);


--
-- Name: tipo_producto_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo_producto
    ADD CONSTRAINT tipo_producto_id_key UNIQUE (id);


--
-- Name: tipos_documentos_id_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipos_documentos
    ADD CONSTRAINT tipos_documentos_id_key UNIQUE (id);


--
-- Name: tipos_documentos_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipos_documentos
    ADD CONSTRAINT tipos_documentos_pkey PRIMARY KEY (id);


--
-- Name: tipos_fianzas_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipos_fianzas
    ADD CONSTRAINT tipos_fianzas_pkey PRIMARY KEY (id);


--
-- Name: unidades_ejecutoras_hist_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY unidades_ejecutoras_hist
    ADD CONSTRAINT unidades_ejecutoras_hist_pkey PRIMARY KEY (id, id_escenario);


--
-- Name: unidades_ejecutoras_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY unidades_ejecutoras
    ADD CONSTRAINT unidades_ejecutoras_pkey PRIMARY KEY (id, id_escenario);


--
-- Name: usuarios_cedula_key; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_cedula_key UNIQUE (cedula);


--
-- Name: usuarios_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: ut_pkey; Type: CONSTRAINT; Schema: puser; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY ut
    ADD CONSTRAINT ut_pkey PRIMARY KEY (id);


SET search_path = vehiculo, pg_catalog;

--
-- Name: base_calculo_veh_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY base_calculo_veh
    ADD CONSTRAINT base_calculo_veh_pkey PRIMARY KEY (id);


--
-- Name: colores_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY colores
    ADD CONSTRAINT colores_pkey PRIMARY KEY (cod_col);


--
-- Name: contribuyente_id_key; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY contribuyente
    ADD CONSTRAINT contribuyente_id_key UNIQUE (id);


--
-- Name: contribuyente_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY contribuyente
    ADD CONSTRAINT contribuyente_pkey PRIMARY KEY (identificacion);


--
-- Name: costo_calcomania_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY costo_calcomania
    ADD CONSTRAINT costo_calcomania_pkey PRIMARY KEY (id);


--
-- Name: descuento_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY descuento
    ADD CONSTRAINT descuento_pkey PRIMARY KEY (cod_des);


--
-- Name: desincorporacion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY desincorporacion
    ADD CONSTRAINT desincorporacion_pkey PRIMARY KEY (cod_des);


--
-- Name: esp_costo_veh_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY esp_costo_veh
    ADD CONSTRAINT esp_costo_veh_pkey PRIMARY KEY (cod_esp);


--
-- Name: exoneracion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY exoneracion
    ADD CONSTRAINT exoneracion_pkey PRIMARY KEY (cod_exo);


--
-- Name: imp_pago_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY imp_pago
    ADD CONSTRAINT imp_pago_pkey PRIMARY KEY (num_pago);


--
-- Name: linea_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY linea
    ADD CONSTRAINT linea_pkey PRIMARY KEY (cod_lin);


--
-- Name: marca_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY marca
    ADD CONSTRAINT marca_pkey PRIMARY KEY (cod_mar);


--
-- Name: mod_mar_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY mod_mar
    ADD CONSTRAINT mod_mar_pkey PRIMARY KEY (id);


--
-- Name: modelo_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY modelo
    ADD CONSTRAINT modelo_pkey PRIMARY KEY (cod_mod);


--
-- Name: ramo_imp_id_key; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY ramo_imp
    ADD CONSTRAINT ramo_imp_id_key UNIQUE (id);


--
-- Name: ramo_imp_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY ramo_imp
    ADD CONSTRAINT ramo_imp_pkey PRIMARY KEY (ramo);


--
-- Name: ramo_transaccion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY ramo_transaccion
    ADD CONSTRAINT ramo_transaccion_pkey PRIMARY KEY (id, id_tipo_transaccion);


--
-- Name: sancion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY sancion
    ADD CONSTRAINT sancion_pkey PRIMARY KEY (cod_san);


--
-- Name: solvencia_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY solvencia
    ADD CONSTRAINT solvencia_pkey PRIMARY KEY (id);


--
-- Name: tasa_bancaria_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tasa_bancaria
    ADD CONSTRAINT tasa_bancaria_pkey PRIMARY KEY (cod_tas);


--
-- Name: tasa_inscripcion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tasa_inscripcion
    ADD CONSTRAINT tasa_inscripcion_pkey PRIMARY KEY (id);


--
-- Name: tipo_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo
    ADD CONSTRAINT tipo_pkey PRIMARY KEY (cod_tip);


--
-- Name: tipo_transaccion_id_key; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo_transaccion
    ADD CONSTRAINT tipo_transaccion_id_key UNIQUE (id);


--
-- Name: tipo_transaccion_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo_transaccion
    ADD CONSTRAINT tipo_transaccion_pkey PRIMARY KEY (tipo_trans);


--
-- Name: tipo_veh_segun_gaceta_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY tipo_veh_segun_gaceta
    ADD CONSTRAINT tipo_veh_segun_gaceta_pkey PRIMARY KEY (cod_veh);


--
-- Name: uso_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY uso
    ADD CONSTRAINT uso_pkey PRIMARY KEY (cod_uso);


--
-- Name: vehiculo_pkey; Type: CONSTRAINT; Schema: vehiculo; Owner: puser; Tablespace: 
--

ALTER TABLE ONLY vehiculo
    ADD CONSTRAINT vehiculo_pkey PRIMARY KEY (placa);


SET search_path = puser, pg_catalog;

--
-- Name: indice; Type: INDEX; Schema: puser; Owner: puser; Tablespace: 
--

CREATE INDEX indice ON relacion_movimientos USING btree (id);


SET search_path = finanzas, pg_catalog;

--
-- Name: solicitud_pago_id_condicion_pago_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: puser
--

ALTER TABLE ONLY solicitud_pago
    ADD CONSTRAINT solicitud_pago_id_condicion_pago_fkey FOREIGN KEY (id_condicion_pago) REFERENCES condiciones_pago(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: solicitud_pago_id_tipo_solicitud_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: puser
--

ALTER TABLE ONLY solicitud_pago
    ADD CONSTRAINT solicitud_pago_id_tipo_solicitud_fkey FOREIGN KEY (id_tipo_solicitud) REFERENCES tipos_solicitudes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: solicitud_pago_id_tipo_solicitud_si_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: puser
--

ALTER TABLE ONLY solicitud_pago
    ADD CONSTRAINT solicitud_pago_id_tipo_solicitud_si_fkey FOREIGN KEY (id_tipo_solicitud_si) REFERENCES tipos_solicitud_sin_imp(id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = puser, pg_catalog;

--
-- Name: relacion_pp_cp_hist_id_asignacion_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp_hist
    ADD CONSTRAINT relacion_pp_cp_hist_id_asignacion_fkey FOREIGN KEY (id_asignacion) REFERENCES asignaciones(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: relacion_pp_cp_hist_id_escenario_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp_hist
    ADD CONSTRAINT relacion_pp_cp_hist_id_escenario_fkey FOREIGN KEY (id_escenario) REFERENCES escenarios(id);


--
-- Name: relacion_pp_cp_hist_id_partida_presupuestaria_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp_hist
    ADD CONSTRAINT relacion_pp_cp_hist_id_partida_presupuestaria_fkey FOREIGN KEY (id_partida_presupuestaria, id_escenario) REFERENCES partidas_presupuestarias(id, id_escenario);


--
-- Name: relacion_pp_cp_id_categoria_programatica_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp
    ADD CONSTRAINT relacion_pp_cp_id_categoria_programatica_fkey FOREIGN KEY (id_categoria_programatica, id_escenario) REFERENCES categorias_programaticas(id, id_escenario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: relacion_pp_cp_id_escenario_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp
    ADD CONSTRAINT relacion_pp_cp_id_escenario_fkey FOREIGN KEY (id_escenario) REFERENCES escenarios(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: relacion_pp_cp_id_partida_presupuestaria_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_pp_cp
    ADD CONSTRAINT relacion_pp_cp_id_partida_presupuestaria_fkey FOREIGN KEY (id_partida_presupuestaria, id_escenario) REFERENCES partidas_presupuestarias(id, id_escenario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: relacion_req_gp_id_grupo_proveedor_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_req_gp
    ADD CONSTRAINT relacion_req_gp_id_grupo_proveedor_fkey FOREIGN KEY (id_grupo_proveedor) REFERENCES grupos_proveedores(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: relacion_req_gp_id_requisito_fkey; Type: FK CONSTRAINT; Schema: puser; Owner: puser
--

ALTER TABLE ONLY relacion_req_gp
    ADD CONSTRAINT relacion_req_gp_id_requisito_fkey FOREIGN KEY (id_requisito) REFERENCES requisitos(id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = vehiculo, pg_catalog;

--
-- Name: det_pago_num_pago_fkey; Type: FK CONSTRAINT; Schema: vehiculo; Owner: puser
--

ALTER TABLE ONLY det_pago
    ADD CONSTRAINT det_pago_num_pago_fkey FOREIGN KEY (num_pago) REFERENCES imp_pago(num_pago) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

