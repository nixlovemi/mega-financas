BEGIN;


--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: fnc_trig_acerta_parcelado_del(); Type: FUNCTION; Schema: public; Owner: megafina_nix
--

CREATE FUNCTION fnc_trig_acerta_parcelado_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	vIdParcelado integer;
  vProssegue bool;
  vTotParcelas integer;
  vMovId integer;
  vCnt integer;

	vRecord record;
BEGIN
	vIdParcelado = 0;
  vProssegue = FALSE;

	IF TG_OP = 'DELETE' THEN
		vIdParcelado = OLD.mov_id_parcelado;
    vProssegue = TRUE;
  ELSEIF TG_OP = 'UPDATE' THEN
  	vIdParcelado = NEW.mov_id_parcelado;
    vProssegue = (OLD.mov_dt_vencimento <> NEW.mov_dt_vencimento) OR (NEW.mov_deletado = TRUE);
  END IF;

	IF vIdParcelado > 0 AND vProssegue THEN
  	vTotParcelas = 0;

  	SELECT COUNT(mov_id_parcelado)
    INTO vTotParcelas
		FROM tb_movimentacao
		WHERE mov_id_parcelado = vIdParcelado
		AND mov_deletado = FALSE
		GROUP BY mov_id_parcelado;

    IF vTotParcelas > 0 THEN
    	vCnt = 1;

    	FOR vRecord IN
      	SELECT mov_id
        FROM tb_movimentacao
        WHERE mov_id_parcelado = vIdParcelado
        AND mov_deletado = FALSE
        ORDER BY mov_dt_vencimento
      LOOP
      	vMovId = vRecord.mov_id;

        UPDATE tb_movimentacao
        SET mov_parcela = vCnt
        WHERE mov_id = vMovId;

        vCnt = vCnt + 1;
      END LOOP;
    END IF;
	END IF;

  IF TG_OP = 'DELETE' THEN
		RETURN OLD;
  ELSEIF TG_OP = 'UPDATE' THEN
  	RETURN NEW;
  END IF;
END;
$$;


ALTER FUNCTION public.fnc_trig_acerta_parcelado_del() OWNER TO megafina_nix;

--
-- Name: fnc_trig_acerta_transferencia(); Type: FUNCTION; Schema: public; Owner: megafina_nix
--

CREATE FUNCTION fnc_trig_acerta_transferencia() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
  vTransId int;
  vTipoTrig text;
  vPrecisaAcertar bool;

  vDtComp date;
  vDtVcto date;
  vValor numeric;
  vDtPgto date;
  vVlrPg numeric;
  vDeletado bool;

  vRecord record;
BEGIN
  IF TG_OP = 'DELETE' THEN
    vTransId = OLD.mov_transferencia_id;
    vTipoTrig = 'D';
  ELSEIF TG_OP = 'UPDATE' THEN
    vTransId = NEW.mov_transferencia_id;
    vTipoTrig = 'U';
  END IF;

  -- verifica se precisa acertar o parcelado
  vPrecisaAcertar = (vTransId > 0);
  -- =======================================

  IF vPrecisaAcertar THEN
    IF vTipoTrig = 'D' THEN

      PERFORM mov_id
      FROM tb_movimentacao
      WHERE mov_id = vTransId;

      IF FOUND THEN
      	DELETE FROM tb_movimentacao
        WHERE mov_id = vTransId;
      END IF;

    ELSEIF vTipoTrig = 'U' THEN

      vDtComp = NEW.mov_dt_competencia;
      vDtVcto = NEW.mov_dt_vencimento;
      vValor = NEW.mov_valor;
      vDtPgto = NEW.mov_dt_pagamento;
      vVlrPg = NEW.mov_valor_pago;
      vDeletado = NEW.mov_deletado;

      UPDATE tb_movimentacao
      SET mov_dt_competencia = vDtComp
      	  ,mov_dt_vencimento = vDtVcto
          ,mov_valor = vValor
          ,mov_dt_pagamento = vDtPgto
          ,mov_valor_pago = vVlrPg
          ,mov_deletado = vDeletado
      WHERE mov_id = vTransId
      AND (
      	mov_dt_competencia <> vDtComp
        OR mov_dt_vencimento <> vDtVcto
        OR mov_valor <> vValor
        OR mov_dt_pagamento <> vDtPgto
        OR mov_valor_pago <> vVlrPg
        OR mov_deletado <> vDeletado
      );

    END IF;
  END IF;

  IF vTipoTrig = 'D' THEN
    RETURN OLD;
  ELSE
    RETURN NEW;
  END IF;
END;
$$;


ALTER FUNCTION public.fnc_trig_acerta_transferencia() OWNER TO megafina_nix;

--
-- Name: fnc_trig_usuario_nome_null(); Type: FUNCTION; Schema: public; Owner: megafina_nix
--

CREATE FUNCTION fnc_trig_usuario_nome_null() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
  vNome text;
  vSobrenome text;
BEGIN

  -- seta os padroes
  vNome = 'Nome';
  vSobrenome = 'Sobrenome';
  -- ===============

  IF NEW.usu_nome IS NULL OR NEW.usu_nome = '' THEN
    NEW.usu_nome = vNome;
  END IF;

  IF NEW.usu_sobrenome IS NULL OR NEW.usu_sobrenome = '' THEN
    NEW.usu_sobrenome = vSobrenome;
  END IF;

  RETURN NEW;

END;
$$;


ALTER FUNCTION public.fnc_trig_usuario_nome_null() OWNER TO megafina_nix;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: tb_atalho; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_atalho (
    ata_id bigint NOT NULL,
    ata_nome character varying(20) NOT NULL,
    ata_fa_icone character varying(25) NOT NULL,
    ata_controller character varying(60) NOT NULL,
    ata_action character varying(60) NOT NULL,
    ata_ativo boolean DEFAULT true NOT NULL
);


ALTER TABLE tb_atalho OWNER TO megafina_nix;

--
-- Name: tb_atalho_ata_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_atalho_ata_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_atalho_ata_id_seq OWNER TO megafina_nix;

--
-- Name: tb_atalho_ata_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_atalho_ata_id_seq OWNED BY tb_atalho.ata_id;


--
-- Name: tb_bandeira_cartao; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_bandeira_cartao (
    bc_id bigint NOT NULL,
    bc_descricao character varying(30) NOT NULL,
    bc_mini_imagem character varying(100),
    bc_ativo boolean DEFAULT true NOT NULL
);


ALTER TABLE tb_bandeira_cartao OWNER TO megafina_nix;

--
-- Name: tb_bandeira_cartao_bc_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_bandeira_cartao_bc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_bandeira_cartao_bc_id_seq OWNER TO megafina_nix;

--
-- Name: tb_bandeira_cartao_bc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_bandeira_cartao_bc_id_seq OWNED BY tb_bandeira_cartao.bc_id;


--
-- Name: tb_cartao_credito; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_cartao_credito (
    cc_id bigint NOT NULL,
    cc_descricao character varying(40) NOT NULL,
    cc_usu_id bigint NOT NULL,
    cc_bc_id bigint NOT NULL,
    cc_limite double precision NOT NULL,
    cc_dia_fechamento bigint NOT NULL,
    cc_dia_pagamento bigint NOT NULL,
    cc_deletado boolean DEFAULT false NOT NULL
);


ALTER TABLE tb_cartao_credito OWNER TO megafina_nix;

--
-- Name: tb_cartao_credito_cc_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_cartao_credito_cc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_cartao_credito_cc_id_seq OWNER TO megafina_nix;

--
-- Name: tb_cartao_credito_cc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_cartao_credito_cc_id_seq OWNED BY tb_cartao_credito.cc_id;


--
-- Name: tb_cartao_credito_fat; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_cartao_credito_fat (
    ccf_id bigint NOT NULL,
    ccf_cc_id bigint NOT NULL,
    ccf_mes bigint NOT NULL,
    ccf_ano bigint NOT NULL,
    ccf_total double precision DEFAULT 0,
    ccf_mov_id bigint,
    ccf_fechado boolean DEFAULT false NOT NULL
);


ALTER TABLE tb_cartao_credito_fat OWNER TO megafina_nix;

--
-- Name: tb_cartao_credito_fat_ccf_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_cartao_credito_fat_ccf_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_cartao_credito_fat_ccf_id_seq OWNER TO megafina_nix;

--
-- Name: tb_cartao_credito_fat_ccf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_cartao_credito_fat_ccf_id_seq OWNED BY tb_cartao_credito_fat.ccf_id;


--
-- Name: tb_cartao_credito_mov; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_cartao_credito_mov (
    ccm_id bigint NOT NULL,
    ccm_ccf_id bigint NOT NULL,
    ccm_descricao character varying(80) NOT NULL,
    ccm_valor double precision NOT NULL,
    ccm_mc_id bigint NOT NULL,
    ccm_id_parcelado bigint,
    ccm_parcela bigint,
    ccm_deletado boolean DEFAULT false NOT NULL,
    ccm_pro_id bigint,
    ccm_data date NOT NULL
);


ALTER TABLE tb_cartao_credito_mov OWNER TO megafina_nix;

--
-- Name: COLUMN tb_cartao_credito_mov.ccm_data; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON COLUMN tb_cartao_credito_mov.ccm_data IS 'data da despesa';


--
-- Name: tb_cartao_credito_mov_ccm_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_cartao_credito_mov_ccm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_cartao_credito_mov_ccm_id_seq OWNER TO megafina_nix;

--
-- Name: tb_cartao_credito_mov_ccm_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_cartao_credito_mov_ccm_id_seq OWNED BY tb_cartao_credito_mov.ccm_id;


--
-- Name: tb_conta; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_conta (
    con_id bigint NOT NULL,
    con_usu_id bigint NOT NULL,
    con_nome character varying(40) NOT NULL,
    con_saldo_inicial double precision NOT NULL,
    con_cor character varying(6) NOT NULL,
    con_ativo boolean DEFAULT true NOT NULL
);


ALTER TABLE tb_conta OWNER TO megafina_nix;

--
-- Name: tb_conta_con_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_conta_con_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_conta_con_id_seq OWNER TO megafina_nix;

--
-- Name: tb_conta_con_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_conta_con_id_seq OWNED BY tb_conta.con_id;


--
-- Name: tb_movimentacao; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_movimentacao (
    mov_id bigint NOT NULL,
    mov_pro_id bigint,
    mov_con_id bigint NOT NULL,
    mov_usu_id bigint NOT NULL,
    mov_mc_id bigint,
    mov_descricao character varying(80) NOT NULL,
    mov_observacao text,
    mov_dt_competencia date NOT NULL,
    mov_dt_vencimento date NOT NULL,
    mov_valor double precision NOT NULL,
    mov_dt_pagamento date,
    mov_valor_pago double precision,
    mov_id_parcelado bigint,
    mov_parcela bigint,
    mov_deletado boolean DEFAULT false NOT NULL,
    mov_transferencia_id integer,
    mov_transferencia_tipo integer
);


ALTER TABLE tb_movimentacao OWNER TO megafina_nix;

--
-- Name: COLUMN tb_movimentacao.mov_dt_competencia; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON COLUMN tb_movimentacao.mov_dt_competencia IS 'data da competência, se vazia, é igual ao vencimento (ela é a base do extrato)';


--
-- Name: COLUMN tb_movimentacao.mov_id_parcelado; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON COLUMN tb_movimentacao.mov_id_parcelado IS 'id parcelado é uma sequence que agrupa despesas parceladas';


--
-- Name: COLUMN tb_movimentacao.mov_transferencia_id; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON COLUMN tb_movimentacao.mov_transferencia_id IS 'ID da movimentação vinculada à essa transf.';


--
-- Name: COLUMN tb_movimentacao.mov_transferencia_tipo; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON COLUMN tb_movimentacao.mov_transferencia_tipo IS 'receita=entrada; despesa=saida';


--
-- Name: tb_movimentacao_anexo; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_movimentacao_anexo (
    ma_id bigint NOT NULL,
    ma_mov_id bigint NOT NULL,
    ma_arquivo character varying(100) NOT NULL
);


ALTER TABLE tb_movimentacao_anexo OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_anexo_ma_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_movimentacao_anexo_ma_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_movimentacao_anexo_ma_id_seq OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_anexo_ma_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_movimentacao_anexo_ma_id_seq OWNED BY tb_movimentacao_anexo.ma_id;


--
-- Name: tb_movimentacao_cat; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_movimentacao_cat (
    mc_id bigint NOT NULL,
    mc_usu_id bigint NOT NULL,
    mc_descricao character varying(50) NOT NULL,
    mc_ativo boolean DEFAULT true NOT NULL,
    mc_id_pai bigint,
    mc_mt_id bigint NOT NULL
);


ALTER TABLE tb_movimentacao_cat OWNER TO megafina_nix;

--
-- Name: TABLE tb_movimentacao_cat; Type: COMMENT; Schema: public; Owner: megafina_nix
--

COMMENT ON TABLE tb_movimentacao_cat IS 'fazer tabela com valores default';


--
-- Name: tb_movimentacao_cat_mc_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_movimentacao_cat_mc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_movimentacao_cat_mc_id_seq OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_cat_mc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_movimentacao_cat_mc_id_seq OWNED BY tb_movimentacao_cat.mc_id;


--
-- Name: tb_movimentacao_id_parcelado_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_movimentacao_id_parcelado_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_movimentacao_id_parcelado_seq OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_mov_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_movimentacao_mov_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_movimentacao_mov_id_seq OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_mov_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_movimentacao_mov_id_seq OWNED BY tb_movimentacao.mov_id;


--
-- Name: tb_movimentacao_tipo; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_movimentacao_tipo (
    mt_id bigint NOT NULL,
    mt_descricao character varying(25) NOT NULL,
    mt_ativo boolean DEFAULT true NOT NULL
);


ALTER TABLE tb_movimentacao_tipo OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_tipo_mt_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_movimentacao_tipo_mt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_movimentacao_tipo_mt_id_seq OWNER TO megafina_nix;

--
-- Name: tb_movimentacao_tipo_mt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_movimentacao_tipo_mt_id_seq OWNED BY tb_movimentacao_tipo.mt_id;


--
-- Name: tb_projeto; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_projeto (
    pro_id bigint NOT NULL,
    pro_descricao character varying(60) NOT NULL,
    pro_usu_id bigint NOT NULL,
    pro_finalizado boolean DEFAULT false NOT NULL,
    pro_observacao text,
    pro_deletado boolean DEFAULT false NOT NULL
);


ALTER TABLE tb_projeto OWNER TO megafina_nix;

--
-- Name: tb_projeto_pro_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_projeto_pro_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_projeto_pro_id_seq OWNER TO megafina_nix;

--
-- Name: tb_projeto_pro_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_projeto_pro_id_seq OWNED BY tb_projeto.pro_id;


--
-- Name: tb_usuario; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_usuario (
    usu_id bigint NOT NULL,
    usu_nome character varying(80) DEFAULT 'Nome'::character varying NOT NULL,
    usu_sobrenome character varying(80) DEFAULT 'Sobrenome'::character varying NOT NULL,
    usu_email character varying(100) NOT NULL,
    usu_senha character varying(40) NOT NULL,
    usu_salt character varying(16) NOT NULL,
    usu_cad_liberado boolean DEFAULT false NOT NULL
);


ALTER TABLE tb_usuario OWNER TO megafina_nix;

--
-- Name: tb_usuario_atalho; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_usuario_atalho (
    ua_id bigint NOT NULL,
    ua_ata_id bigint NOT NULL,
    ua_usu_id bigint NOT NULL
);


ALTER TABLE tb_usuario_atalho OWNER TO megafina_nix;

--
-- Name: tb_usuario_atalho_ua_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_usuario_atalho_ua_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_usuario_atalho_ua_id_seq OWNER TO megafina_nix;

--
-- Name: tb_usuario_atalho_ua_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_usuario_atalho_ua_id_seq OWNED BY tb_usuario_atalho.ua_id;


--
-- Name: tb_usuario_fcbk; Type: TABLE; Schema: public; Owner: megafina_nix; Tablespace:
--

CREATE TABLE tb_usuario_fcbk (
    uf_id bigint NOT NULL,
    uf_usu_id bigint NOT NULL,
    uf_fb_usu_id character varying(40),
    uf_fb_prim_nome character varying(80),
    uf_fb_sobrenome character varying(80),
    uf_fb_nomecompleto character varying(160),
    uf_fb_email character varying(100),
    uf_fb_sexo character varying(20),
    uf_fb_foto text
);


ALTER TABLE tb_usuario_fcbk OWNER TO megafina_nix;

--
-- Name: tb_usuario_fcbk_uf_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_usuario_fcbk_uf_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_usuario_fcbk_uf_id_seq OWNER TO megafina_nix;

--
-- Name: tb_usuario_fcbk_uf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_usuario_fcbk_uf_id_seq OWNED BY tb_usuario_fcbk.uf_id;


--
-- Name: tb_usuario_usu_id_seq; Type: SEQUENCE; Schema: public; Owner: megafina_nix
--

CREATE SEQUENCE tb_usuario_usu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tb_usuario_usu_id_seq OWNER TO megafina_nix;

--
-- Name: tb_usuario_usu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: megafina_nix
--

ALTER SEQUENCE tb_usuario_usu_id_seq OWNED BY tb_usuario.usu_id;


--
-- Name: ata_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_atalho ALTER COLUMN ata_id SET DEFAULT nextval('tb_atalho_ata_id_seq'::regclass);


--
-- Name: bc_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_bandeira_cartao ALTER COLUMN bc_id SET DEFAULT nextval('tb_bandeira_cartao_bc_id_seq'::regclass);


--
-- Name: cc_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito ALTER COLUMN cc_id SET DEFAULT nextval('tb_cartao_credito_cc_id_seq'::regclass);


--
-- Name: ccf_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_fat ALTER COLUMN ccf_id SET DEFAULT nextval('tb_cartao_credito_fat_ccf_id_seq'::regclass);


--
-- Name: ccm_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_mov ALTER COLUMN ccm_id SET DEFAULT nextval('tb_cartao_credito_mov_ccm_id_seq'::regclass);


--
-- Name: con_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_conta ALTER COLUMN con_id SET DEFAULT nextval('tb_conta_con_id_seq'::regclass);


--
-- Name: mov_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao ALTER COLUMN mov_id SET DEFAULT nextval('tb_movimentacao_mov_id_seq'::regclass);


--
-- Name: ma_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_anexo ALTER COLUMN ma_id SET DEFAULT nextval('tb_movimentacao_anexo_ma_id_seq'::regclass);


--
-- Name: mc_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_cat ALTER COLUMN mc_id SET DEFAULT nextval('tb_movimentacao_cat_mc_id_seq'::regclass);


--
-- Name: mt_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_tipo ALTER COLUMN mt_id SET DEFAULT nextval('tb_movimentacao_tipo_mt_id_seq'::regclass);


--
-- Name: pro_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_projeto ALTER COLUMN pro_id SET DEFAULT nextval('tb_projeto_pro_id_seq'::regclass);


--
-- Name: usu_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario ALTER COLUMN usu_id SET DEFAULT nextval('tb_usuario_usu_id_seq'::regclass);


--
-- Name: ua_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario_atalho ALTER COLUMN ua_id SET DEFAULT nextval('tb_usuario_atalho_ua_id_seq'::regclass);


--
-- Name: uf_id; Type: DEFAULT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario_fcbk ALTER COLUMN uf_id SET DEFAULT nextval('tb_usuario_fcbk_uf_id_seq'::regclass);


--
-- Data for Name: tb_atalho; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_atalho (ata_id, ata_nome, ata_fa_icone, ata_controller, ata_action, ata_ativo) FROM stdin;
\.


--
-- Name: tb_atalho_ata_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_atalho_ata_id_seq', 1, false);


--
-- Data for Name: tb_bandeira_cartao; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_bandeira_cartao (bc_id, bc_descricao, bc_mini_imagem, bc_ativo) FROM stdin;
1	Visa	html/images/bandeira-cartao/bandeira-visa.jpg	t
2	MasterCard	html/images/bandeira-cartao/bandeira-mastercard.jpg	t
3	American Express	html/images/bandeira-cartao/bandeira-american-express.jpg	t
4	Elo	html/images/bandeira-cartao/bandeira-elo.jpg	t
5	Hipercard	html/images/bandeira-cartao/bandeira-hipercard.jpg	t
6	Outros	html/images/bandeira-cartao/bandeira-outros.jpg	t
\.


--
-- Name: tb_bandeira_cartao_bc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_bandeira_cartao_bc_id_seq', 6, true);


--
-- Data for Name: tb_cartao_credito; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_cartao_credito (cc_id, cc_descricao, cc_usu_id, cc_bc_id, cc_limite, cc_dia_fechamento, cc_dia_pagamento, cc_deletado) FROM stdin;
3	Cartão Saraiva	26	1	1450	13	25	f
4	Cartão Magazine	26	2	2100	6	16	f
\.


--
-- Name: tb_cartao_credito_cc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_cartao_credito_cc_id_seq', 4, true);


--
-- Data for Name: tb_cartao_credito_fat; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_cartao_credito_fat (ccf_id, ccf_cc_id, ccf_mes, ccf_ano, ccf_total, ccf_mov_id, ccf_fechado) FROM stdin;
1	3	7	2016	0	\N	f
2	3	8	2016	0	\N	f
\.


--
-- Name: tb_cartao_credito_fat_ccf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_cartao_credito_fat_ccf_id_seq', 2, true);


--
-- Data for Name: tb_cartao_credito_mov; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_cartao_credito_mov (ccm_id, ccm_ccf_id, ccm_descricao, ccm_valor, ccm_mc_id, ccm_id_parcelado, ccm_parcela, ccm_deletado, ccm_pro_id, ccm_data) FROM stdin;
\.


--
-- Name: tb_cartao_credito_mov_ccm_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_cartao_credito_mov_ccm_id_seq', 1, false);


--
-- Data for Name: tb_conta; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_conta (con_id, con_usu_id, con_nome, con_saldo_inicial, con_cor, con_ativo) FROM stdin;
1	26	Banco do Brasil	600.879999999999995	e9ed1f	t
2	26	Teste	1000	7a73cc	f
4	26	Caixa Econômica	250.879999999999995	3391ff	f
3	26	Santander	100	ff0000	t
\.


--
-- Name: tb_conta_con_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_conta_con_id_seq', 4, true);


--
-- Data for Name: tb_movimentacao; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_movimentacao (mov_id, mov_pro_id, mov_con_id, mov_usu_id, mov_mc_id, mov_descricao, mov_observacao, mov_dt_competencia, mov_dt_vencimento, mov_valor, mov_dt_pagamento, mov_valor_pago, mov_id_parcelado, mov_parcela, mov_deletado, mov_transferencia_id, mov_transferencia_tipo) FROM stdin;
47	\N	1	26	8	Abastecimento Fiesta	\N	2016-06-01	2016-06-01	65	\N	\N	\N	\N	f	\N	\N
48	\N	1	26	1	Salário Dia 5	\N	2016-06-05	2016-06-05	1080	\N	\N	11	1	f	\N	\N
49	\N	1	26	1	Salário Dia 5	\N	2016-07-05	2016-07-05	1080	\N	\N	11	2	f	\N	\N
50	\N	1	26	1	Salário Dia 5	\N	2016-08-05	2016-08-05	1080	\N	\N	11	3	f	\N	\N
51	\N	1	26	1	Salário Dia 5	\N	2016-09-05	2016-09-05	1080	\N	\N	11	4	f	\N	\N
52	\N	1	26	1	Salário Dia 5	\N	2016-10-05	2016-10-05	1080	\N	\N	11	5	f	\N	\N
42	\N	1	26	8	Teste de Parcelamento	\N	2016-07-30	2016-07-30	39.990000000000002	\N	\N	10	1	f	\N	\N
41	\N	1	26	8	Teste de Parcelamento	\N	2016-07-31	2016-07-31	39.990000000000002	\N	\N	10	2	f	\N	\N
43	\N	1	26	8	Teste de Parcelamento	\N	2016-09-30	2016-09-30	39.990000000000002	\N	\N	10	3	f	\N	\N
46	\N	1	26	8	Teste de Parcelamento	\N	2016-12-31	2016-12-31	39.990000000000002	\N	\N	10	5	f	\N	\N
45	\N	1	26	8	Teste de Parcelamento	\N	2016-11-30	2016-11-30	39.9799999999999969	\N	\N	10	4	f	\N	\N
53	\N	1	26	3	Hora Extra Junho	\N	2016-06-10	2016-06-10	256.870000000000005	2016-06-10	256.990000000000009	\N	\N	f	\N	\N
70	\N	1	26	2	Sorvete	\N	2016-08-24	2016-08-24	5	2016-08-24	5	\N	\N	f	\N	\N
68	\N	3	26	\N	Transferência para Conta Banco do Brasil	\N	2016-08-26	2016-08-26	120	2016-08-26	120	\N	\N	f	69	2
69	\N	1	26	\N	Transferência da Conta Santander	\N	2016-08-26	2016-08-26	120	2016-08-26	120	\N	\N	f	68	1
\.


--
-- Data for Name: tb_movimentacao_anexo; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_movimentacao_anexo (ma_id, ma_mov_id, ma_arquivo) FROM stdin;
\.


--
-- Name: tb_movimentacao_anexo_ma_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_movimentacao_anexo_ma_id_seq', 1, false);


--
-- Data for Name: tb_movimentacao_cat; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_movimentacao_cat (mc_id, mc_usu_id, mc_descricao, mc_ativo, mc_id_pai, mc_mt_id) FROM stdin;
2	26	Supermercado	t	\N	2
5	26	Pague Menos	t	2	2
8	26	Combustível	t	\N	2
9	26	Jobs	f	\N	1
3	26	Sites e Diversos	t	\N	1
4	26	São Vicente	f	2	2
12	26	Mercado Pérola	t	2	2
13	26	São Vicente	t	2	2
14	26	Carro	t	8	2
15	26	Moto	t	8	2
16	26	Hora Extra	t	1	1
1	26	Salário	t	\N	1
\.


--
-- Name: tb_movimentacao_cat_mc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_movimentacao_cat_mc_id_seq', 16, true);


--
-- Name: tb_movimentacao_id_parcelado_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_movimentacao_id_parcelado_seq', 11, true);


--
-- Name: tb_movimentacao_mov_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_movimentacao_mov_id_seq', 70, true);


--
-- Data for Name: tb_movimentacao_tipo; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_movimentacao_tipo (mt_id, mt_descricao, mt_ativo) FROM stdin;
1	Receita	t
2	Despesa	t
\.


--
-- Name: tb_movimentacao_tipo_mt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_movimentacao_tipo_mt_id_seq', 2, true);


--
-- Data for Name: tb_projeto; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_projeto (pro_id, pro_descricao, pro_usu_id, pro_finalizado, pro_observacao, pro_deletado) FROM stdin;
\.


--
-- Name: tb_projeto_pro_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_projeto_pro_id_seq', 1, false);


--
-- Data for Name: tb_usuario; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_usuario (usu_id, usu_nome, usu_sobrenome, usu_email, usu_senha, usu_salt, usu_cad_liberado) FROM stdin;
26	Leandro	Parra	nixlovemi@gmail.com	D0B912EE2FB2C30FDE2063029324450C71A163D2	Y3(%=!P+	t
28	Leandro - Nix	Sobrenome	@laamebita	6AC5553E9D4D7DA7806626D312261EE4A12EAAE3	!0OBAWRR	t
\.


--
-- Data for Name: tb_usuario_atalho; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_usuario_atalho (ua_id, ua_ata_id, ua_usu_id) FROM stdin;
\.


--
-- Name: tb_usuario_atalho_ua_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_usuario_atalho_ua_id_seq', 1, false);


--
-- Data for Name: tb_usuario_fcbk; Type: TABLE DATA; Schema: public; Owner: megafina_nix
--

COPY tb_usuario_fcbk (uf_id, uf_usu_id, uf_fb_usu_id, uf_fb_prim_nome, uf_fb_sobrenome, uf_fb_nomecompleto, uf_fb_email, uf_fb_sexo, uf_fb_foto) FROM stdin;
17	26	1080435861996882	Leandro	Nix	Leandro Nix	nixlovemi@gmail.com	male	https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xfa1/v/t1.0-1/p50x50/10389226_951685858205217_5684956463622163158_n.jpg?oh=5763d5724e2ce53386d5e387bf583600&oe=56F5E679&__gda__=1458922793_42255b804bbb38118bfc9b44657a18d2
\.


--
-- Name: tb_usuario_fcbk_uf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_usuario_fcbk_uf_id_seq', 18, true);


--
-- Name: tb_usuario_usu_id_seq; Type: SEQUENCE SET; Schema: public; Owner: megafina_nix
--

SELECT pg_catalog.setval('tb_usuario_usu_id_seq', 32, true);


--
-- Name: tb_atalho_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_atalho
    ADD CONSTRAINT tb_atalho_pkey PRIMARY KEY (ata_id);


--
-- Name: tb_bandeira_cartao_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_bandeira_cartao
    ADD CONSTRAINT tb_bandeira_cartao_pkey PRIMARY KEY (bc_id);


--
-- Name: tb_cartao_credito_fat_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_cartao_credito_fat
    ADD CONSTRAINT tb_cartao_credito_fat_pkey PRIMARY KEY (ccf_id);


--
-- Name: tb_cartao_credito_mov_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_cartao_credito_mov
    ADD CONSTRAINT tb_cartao_credito_mov_pkey PRIMARY KEY (ccm_id);


--
-- Name: tb_cartao_credito_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_cartao_credito
    ADD CONSTRAINT tb_cartao_credito_pkey PRIMARY KEY (cc_id);


--
-- Name: tb_conta_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_conta
    ADD CONSTRAINT tb_conta_pkey PRIMARY KEY (con_id);


--
-- Name: tb_movimentacao_anexo_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_movimentacao_anexo
    ADD CONSTRAINT tb_movimentacao_anexo_pkey PRIMARY KEY (ma_id);


--
-- Name: tb_movimentacao_cat_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_movimentacao_cat
    ADD CONSTRAINT tb_movimentacao_cat_pkey PRIMARY KEY (mc_id);


--
-- Name: tb_movimentacao_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT tb_movimentacao_pkey PRIMARY KEY (mov_id);


--
-- Name: tb_movimentacao_tipo_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_movimentacao_tipo
    ADD CONSTRAINT tb_movimentacao_tipo_pkey PRIMARY KEY (mt_id);


--
-- Name: tb_projeto_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_projeto
    ADD CONSTRAINT tb_projeto_pkey PRIMARY KEY (pro_id);


--
-- Name: tb_usuario_atalho_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario_atalho
    ADD CONSTRAINT tb_usuario_atalho_pkey PRIMARY KEY (ua_id);


--
-- Name: tb_usuario_fcbk_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario_fcbk
    ADD CONSTRAINT tb_usuario_fcbk_pkey PRIMARY KEY (uf_id);


--
-- Name: tb_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario
    ADD CONSTRAINT tb_usuario_pkey PRIMARY KEY (usu_id);


--
-- Name: uk_ata_controller_action; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_atalho
    ADD CONSTRAINT uk_ata_controller_action UNIQUE (ata_controller, ata_action);


--
-- Name: uk_ata_nome; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_atalho
    ADD CONSTRAINT uk_ata_nome UNIQUE (ata_nome);


--
-- Name: uk_bc_descricao; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_bandeira_cartao
    ADD CONSTRAINT uk_bc_descricao UNIQUE (bc_descricao);


--
-- Name: uk_cc_usu_desc; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_cartao_credito
    ADD CONSTRAINT uk_cc_usu_desc UNIQUE (cc_descricao, cc_usu_id);


--
-- Name: uk_con_nome_usu; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_conta
    ADD CONSTRAINT uk_con_nome_usu UNIQUE (con_usu_id, con_nome);


--
-- Name: uk_mt_descricao; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_movimentacao_tipo
    ADD CONSTRAINT uk_mt_descricao UNIQUE (mt_descricao);


--
-- Name: uk_pro_descricao_usu; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_projeto
    ADD CONSTRAINT uk_pro_descricao_usu UNIQUE (pro_descricao, pro_usu_id);


--
-- Name: uk_ua_ata_usu; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario_atalho
    ADD CONSTRAINT uk_ua_ata_usu UNIQUE (ua_ata_id, ua_usu_id);


--
-- Name: uk_uf_usu_id; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario_fcbk
    ADD CONSTRAINT uk_uf_usu_id UNIQUE (uf_usu_id);


--
-- Name: uk_usu_email; Type: CONSTRAINT; Schema: public; Owner: megafina_nix; Tablespace:
--

ALTER TABLE ONLY tb_usuario
    ADD CONSTRAINT uk_usu_email UNIQUE (usu_email);


--
-- Name: trig_acerta_parcelado_del; Type: TRIGGER; Schema: public; Owner: megafina_nix
--

CREATE TRIGGER trig_acerta_parcelado_del AFTER DELETE OR UPDATE OF mov_dt_vencimento, mov_deletado ON tb_movimentacao FOR EACH ROW EXECUTE PROCEDURE fnc_trig_acerta_parcelado_del();


--
-- Name: trig_acerta_transferencia; Type: TRIGGER; Schema: public; Owner: megafina_nix
--

CREATE TRIGGER trig_acerta_transferencia AFTER DELETE OR UPDATE ON tb_movimentacao FOR EACH ROW EXECUTE PROCEDURE fnc_trig_acerta_transferencia();


--
-- Name: trig_usuario_nome_null; Type: TRIGGER; Schema: public; Owner: megafina_nix
--

CREATE TRIGGER trig_usuario_nome_null BEFORE INSERT OR UPDATE ON tb_usuario FOR EACH ROW EXECUTE PROCEDURE fnc_trig_usuario_nome_null();


--
-- Name: fk_cc_bc_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito
    ADD CONSTRAINT fk_cc_bc_id FOREIGN KEY (cc_bc_id) REFERENCES tb_bandeira_cartao(bc_id);


--
-- Name: fk_cc_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito
    ADD CONSTRAINT fk_cc_usu_id FOREIGN KEY (cc_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_ccf_cc_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_fat
    ADD CONSTRAINT fk_ccf_cc_id FOREIGN KEY (ccf_cc_id) REFERENCES tb_cartao_credito(cc_id);


--
-- Name: fk_ccf_mov_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_fat
    ADD CONSTRAINT fk_ccf_mov_id FOREIGN KEY (ccf_mov_id) REFERENCES tb_movimentacao(mov_id);


--
-- Name: fk_ccm_ccf_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_mov
    ADD CONSTRAINT fk_ccm_ccf_id FOREIGN KEY (ccm_ccf_id) REFERENCES tb_cartao_credito_fat(ccf_id);


--
-- Name: fk_con_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_conta
    ADD CONSTRAINT fk_con_usu_id FOREIGN KEY (con_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_ma_mov_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_anexo
    ADD CONSTRAINT fk_ma_mov_id FOREIGN KEY (ma_mov_id) REFERENCES tb_movimentacao(mov_id);


--
-- Name: fk_mc_id_pai; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_cat
    ADD CONSTRAINT fk_mc_id_pai FOREIGN KEY (mc_id_pai) REFERENCES tb_movimentacao_cat(mc_id);


--
-- Name: fk_mc_mt_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_cat
    ADD CONSTRAINT fk_mc_mt_id FOREIGN KEY (mc_mt_id) REFERENCES tb_movimentacao_tipo(mt_id);


--
-- Name: fk_mc_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao_cat
    ADD CONSTRAINT fk_mc_usu_id FOREIGN KEY (mc_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_mcc_mc_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_mov
    ADD CONSTRAINT fk_mcc_mc_id FOREIGN KEY (ccm_mc_id) REFERENCES tb_movimentacao_cat(mc_id);


--
-- Name: fk_mcc_pro_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_cartao_credito_mov
    ADD CONSTRAINT fk_mcc_pro_id FOREIGN KEY (ccm_pro_id) REFERENCES tb_projeto(pro_id);


--
-- Name: fk_mov_con_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT fk_mov_con_id FOREIGN KEY (mov_con_id) REFERENCES tb_conta(con_id);


--
-- Name: fk_mov_mc_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT fk_mov_mc_id FOREIGN KEY (mov_mc_id) REFERENCES tb_movimentacao_cat(mc_id);


--
-- Name: fk_mov_pro_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT fk_mov_pro_id FOREIGN KEY (mov_pro_id) REFERENCES tb_projeto(pro_id);


--
-- Name: fk_mov_transferencia_tipo; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT fk_mov_transferencia_tipo FOREIGN KEY (mov_transferencia_tipo) REFERENCES tb_movimentacao_tipo(mt_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: fk_mov_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_movimentacao
    ADD CONSTRAINT fk_mov_usu_id FOREIGN KEY (mov_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_pro_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_projeto
    ADD CONSTRAINT fk_pro_usu_id FOREIGN KEY (pro_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_ua_ata_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario_atalho
    ADD CONSTRAINT fk_ua_ata_id FOREIGN KEY (ua_ata_id) REFERENCES tb_atalho(ata_id);


--
-- Name: fk_ua_uau_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario_atalho
    ADD CONSTRAINT fk_ua_uau_id FOREIGN KEY (ua_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: fk_uf_usu_id; Type: FK CONSTRAINT; Schema: public; Owner: megafina_nix
--

ALTER TABLE ONLY tb_usuario_fcbk
    ADD CONSTRAINT fk_uf_usu_id FOREIGN KEY (uf_usu_id) REFERENCES tb_usuario(usu_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: tb_atalho; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_atalho FROM PUBLIC;
REVOKE ALL ON TABLE tb_atalho FROM megafina_nix;
GRANT ALL ON TABLE tb_atalho TO megafina_nix;


--
-- Name: tb_bandeira_cartao; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_bandeira_cartao FROM PUBLIC;
REVOKE ALL ON TABLE tb_bandeira_cartao FROM megafina_nix;
GRANT ALL ON TABLE tb_bandeira_cartao TO megafina_nix;


--
-- Name: tb_cartao_credito; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_cartao_credito FROM PUBLIC;
REVOKE ALL ON TABLE tb_cartao_credito FROM megafina_nix;
GRANT ALL ON TABLE tb_cartao_credito TO megafina_nix;


--
-- Name: tb_cartao_credito_fat; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_cartao_credito_fat FROM PUBLIC;
REVOKE ALL ON TABLE tb_cartao_credito_fat FROM megafina_nix;
GRANT ALL ON TABLE tb_cartao_credito_fat TO megafina_nix;


--
-- Name: tb_cartao_credito_mov; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_cartao_credito_mov FROM PUBLIC;
REVOKE ALL ON TABLE tb_cartao_credito_mov FROM megafina_nix;
GRANT ALL ON TABLE tb_cartao_credito_mov TO megafina_nix;


--
-- Name: tb_conta; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_conta FROM PUBLIC;
REVOKE ALL ON TABLE tb_conta FROM megafina_nix;
GRANT ALL ON TABLE tb_conta TO megafina_nix;


--
-- Name: tb_movimentacao; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_movimentacao FROM PUBLIC;
REVOKE ALL ON TABLE tb_movimentacao FROM megafina_nix;
GRANT ALL ON TABLE tb_movimentacao TO megafina_nix;


--
-- Name: tb_movimentacao_anexo; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_movimentacao_anexo FROM PUBLIC;
REVOKE ALL ON TABLE tb_movimentacao_anexo FROM megafina_nix;
GRANT ALL ON TABLE tb_movimentacao_anexo TO megafina_nix;


--
-- Name: tb_movimentacao_cat; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_movimentacao_cat FROM PUBLIC;
REVOKE ALL ON TABLE tb_movimentacao_cat FROM megafina_nix;
GRANT ALL ON TABLE tb_movimentacao_cat TO megafina_nix;


--
-- Name: tb_movimentacao_tipo; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_movimentacao_tipo FROM PUBLIC;
REVOKE ALL ON TABLE tb_movimentacao_tipo FROM megafina_nix;
GRANT ALL ON TABLE tb_movimentacao_tipo TO megafina_nix;


--
-- Name: tb_projeto; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_projeto FROM PUBLIC;
REVOKE ALL ON TABLE tb_projeto FROM megafina_nix;
GRANT ALL ON TABLE tb_projeto TO megafina_nix;


--
-- Name: tb_usuario; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_usuario FROM PUBLIC;
REVOKE ALL ON TABLE tb_usuario FROM megafina_nix;
GRANT ALL ON TABLE tb_usuario TO megafina_nix;
--GRANT ALL ON TABLE tb_usuario TO megafina_nix;


--
-- Name: tb_usuario_atalho; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_usuario_atalho FROM PUBLIC;
REVOKE ALL ON TABLE tb_usuario_atalho FROM megafina_nix;
GRANT ALL ON TABLE tb_usuario_atalho TO megafina_nix;
--GRANT ALL ON TABLE tb_usuario_atalho TO megafina_nix;


--
-- Name: tb_usuario_fcbk; Type: ACL; Schema: public; Owner: megafina_nix
--

REVOKE ALL ON TABLE tb_usuario_fcbk FROM PUBLIC;
REVOKE ALL ON TABLE tb_usuario_fcbk FROM megafina_nix;
GRANT ALL ON TABLE tb_usuario_fcbk TO megafina_nix;
--GRANT ALL ON TABLE tb_usuario_fcbk TO megafina_nix;


--
-- PostgreSQL database dump complete
--

commit
