// variaveis
var lista = {
		id       : "",
		filtro   : "",
		vars     : "",
		sql      : "",
		pagAtual : 1,
		logId	 : 0,
		
		campo_ord : {
			campo : "",
			ordem : ""
		} 
	};

var listAnteriorObj = {
		lista: new Array() 
};

var str_root_folder = 'birds';
// ---------

$( document ).ready(function() {
	
	if( $("table[id^='tbWis_']").length > 0 ){
		
		getLastList();
		openWaitModal();
		setTimeout("listAnterior();", 1200);
		
	}	
	
});

function listAnterior() {

	var ja_tem_no_array;
	var id_ja_tem_no_array;
	
	if( typeof $.session.get('listAnterior') == "undefined" ){
	}
	else{
		listAnteriorObj = JSON.parse($.session.get('listAnterior'));
	}	
	
	$("table[id^='tbWis_']").each(function(index, value) {
		var id_table = $(value).prop("id");

		ja_tem_no_array = false;
		id_ja_tem_no_array = -1;
		for (i = 0; i < listAnteriorObj.lista.length; i++) {

			if (listAnteriorObj.lista[i].id == id_table) {

				ja_tem_no_array = true;
				id_ja_tem_no_array = i;
				i = 999999999;

			}

		}

		lista.id = id_table;
		lista.sql = $("#" + id_table + "__s").val();
		lista.vars = $("#" + id_table + "__v").val();
		lista.filtro = $("#filter_" + id_table + " table tr.linha-filtro-lista");
		lista.pagAtual = $("#" + id_table + " tfoot").find("a[disabled]").html();

		lista.campo_ord.campo = $(value).find("th > img").prev().html() || $(value).find("th:nth-child(2) > a").html();

		lista.campo_ord.ordem = "ASC";
		if (typeof $(value).find("th > img").prop("src") !== 'undefined') {
			lista.campo_ord.ordem = ($(value).find("th > img").prop("src").search("-up") !== -1) ? ("ASC"): ("DESC");
		}

		if (!ja_tem_no_array) {

			listAnteriorObj.lista.push(lista);
			// console.log('push');

		} else {
			if (id_ja_tem_no_array >= 0) {

				listAnteriorObj.lista[id_ja_tem_no_array] = lista;
				// console.log('edit');

			}

		}

	});

	$.session.set('listAnterior', JSON.stringify(listAnteriorObj));
	// console.log(JSON.stringify(listAnteriorObj));
	closeWaitModal();

}

function getLastList() {
	var cont = 0;
	var filtro = '';
	var id;
	var pagAtual;
	var ordem;
	var campo;

	$("table[id^='tbWis_']").each(function(index, table) {
		id = $(table).prop("id");
		
		if( typeof $.session.get('listAnterior') == "undefined" ){
		}
		else{
			listAnteriorObj2 = JSON.parse($.session.get('listAnterior'));

			if (JSON.stringify(listAnteriorObj2.lista) != "[]") {
				for (cont = 0; cont < listAnteriorObj2.lista.length; cont++) {
					if (id == listAnteriorObj2.lista[cont].id) {
						listId = listAnteriorObj2.lista[cont].id;
						pagAtual = listAnteriorObj2.lista[cont].pagAtual;
						campo = listAnteriorObj2.lista[cont].campo_ord.campo;
						ordem = listAnteriorObj2.lista[cont].campo_ord.ordem;

						$('#' + listId + '__s').val(listAnteriorObj2.lista[cont].sql);
						$("#" + listId + "__v").val(listAnteriorObj2.lista[cont].vars);
						$("#filter_" + listId + " table tr.linha-filtro-lista").html(listAnteriorObj2.lista[cont].filtro);

						$("#filter_" + listId + " table tr.linha-filtro-lista").each(function(index) {
							var slc_campos_filtro_tb = $(this).find('#slc_campos_filtro_tb').val();
							var slc_tipo_filtro_tb = $(this).find('#slc_tipo_filtro_tb').val();
							var txt_campos_filtro_tb = $(this).find('#txt_campos_filtro_tb').val();
		
							filtro += slc_campos_filtro_tb + '(@)' + slc_tipo_filtro_tb + '(@)' + txt_campos_filtro_tb + '(@@)';
						});

						fncObjLista__order(campo, listId, ordem, pagAtual, filtro, false);

					}

				}
			}
		}
	});
}

function openWaitModal(){
	
	$('#birds-overlay').css({'display':'block'});
	
}

function closeWaitModal(){
	
	$('#birds-overlay').css({'display':'none'});
	
}

function fncObjLista__order(campo, id_tabela, tipo_ordenacao, pagina_atual, filtros, scroll) {

	if ((typeof (filtros) == "undefined") || (filtros == null)) {
		filtros = "";
	}
	
	var tipo;
	if(filtros == 'LIMPAR_FILTRO'){
		tipo = 'FiltrarLista';
		filtros = '';
	}
	else if(filtros != ''){
		tipo = 'FiltrarLista';
	}
	else{
		tipo = 'OrdenarLista';
	}

	var sql = $('#' + id_tabela + '__s').val();
	var vars = $('#' + id_tabela + '__v').val();
	
	var controller_nome = $('#' + id_tabela ).find("button[controller!='']:first").attr("controller");

	$
	.ajax({
		url : getUrlHome() + "classes-birds/library/obj-lista/ObjLista.class.php",
		data : 'tipo=' + tipo + '&sql=' + sql + '&vars=' + vars
				+ '&campo_ord=' + campo + '&tipo_ordenacao='
				+ tipo_ordenacao + '&pagina_atual=' + pagina_atual + '&filtro=' + filtros + '&controller_nome='+ controller_nome,
		beforeSend : function() {
			$('#div_' + id_tabela).html('<center><img src="'+getUrlHome()+'html/images_birds/hex-loader2.gif" /></center>');
		},
		done : function() {
			$('#div_' + id_tabela).html('');
		},
		success : function(response) {
			
			if ( typeof scroll !== 'undefined' && scroll){
				$('html, body').animate({
					scrollTop : ($('#div_' + id_tabela).offset().top)
				}, 1000);
			}

			var tempRows = $('#div_' + id_tabela).closest(".tabela-temp").attr('grupo-temp');

			var tempTable = ( typeof tempRows !== typeof undefined && tempRows !== false );
			var tempArray = ( typeof listarTemp !== typeof undefined && listarTemp !== false );
			tempArray = ( tempArray && typeof listarTemp[ tempRows ] !== typeof undefined && listarTemp[ tempRows ] !== false );
	
			if( tempTable && tempArray ){
				response = response.replace(/<tbody>/g, "<tbody>"+listarTemp[ tempRows ]);
			}
			
			// response = response.replace(/>Sim</g, "><i class='fa fa-check fa-15x'></i><");
			// response = response.replace(/>Não</g, "><i class='fa fa-times fa-15x'></i><");
			
			$('#div_' + id_tabela).html(response);
			setTimeout("listAnterior();", 1200);
			
			// $visualizar = !$("#salvar").prop("id");
			// setListaPermissao($visualizar);
			// loadPermissoes();
			
			if( tempTable ){
				setPermissaoTbTemp();
			}
		}
	});
}

$(document).on('click', '.btn-slide-filter', function() {
	var id = $(this).attr('attr-tb-id');
	$("#filter_" + id).slideToggle("slow", function() {
	});
});

$(document).on('click', '.btn-add-linha-filtro-lista', function() {
	var id = $(this).attr('attr-tb-id');
	var tr = decodeURIComponent($('#' + id + '__trp').val());

	$("#filter_" + id + " table").append(tr);
});

$(document).on('click', '.btn-delete-linha-filtro', function() {
	$(this).parent().parent().remove();
});

$(document).on('click', '.panel-export-xls', function() {
	var id = $(this).attr('attr-tb-id');
	var sql = $('#' + id + '__exp_xls').val();
	
	window.open("./index_exp_xls.php?s=" + sql, "Exportar XLS", "width=200, height=100");
});

$(document).on(
		'click',
		'.btn-exec-filtro-lista',
		function() {
			var id = $(this).attr('attr-tb-id');
			var filtro = '';

			$("#filter_" + id + " table tr.linha-filtro-lista").each(
					function(index) {
						var slc_campos_filtro_tb = $(this).find(
								'#slc_campos_filtro_tb').val();
						var slc_tipo_filtro_tb = $(this).find(
								'#slc_tipo_filtro_tb').val();
						var txt_campos_filtro_tb = $(this).find(
								'#txt_campos_filtro_tb').val();

						filtro += slc_campos_filtro_tb + '(@)'
								+ slc_tipo_filtro_tb + '(@)'
								+ txt_campos_filtro_tb + '(@@)';
					});

			if (filtro != '') {
				filtro = filtro.substr(0, filtro.length - 4);
			}
			else{
				filtro = 'LIMPAR_FILTRO';
			}

			fncObjLista__order('', id, '', 1, filtro);
			setTimeout("listAnterior();", 1200);
		});

$(document)
		.on(
			'change',
			'.cl_slc_campos_filtro_tb',
			function() {
				var campo = $(this).val();
				var slc_tipo_filtro_tb = $(this).parent().parent().find(
						'#slc_tipo_filtro_tb');
				var txt_campos_filtro_tb = $(this).parent().parent().find(
						'#txt_campos_filtro_tb');
				var id = $(this).parent().parent().parent().parent()
						.parent().find('#hidden_aux_tp_filtro_id').val();
				var tipo_campo = $(
						'select[id="slct_hidden_' + id
								+ '"] > option:contains("' + campo + '")')
						.val();

				if (campo == '') {
					slc_tipo_filtro_tb.find('option').remove().end()
							.append('<option value=""></option>').val('');
					txt_campos_filtro_tb.val('').attr('disabled', true);
				} else {
					$
					.ajax({
						url : getUrlHome() + "classes-birds/library/obj-lista/ObjLista.class.php",
						data : 'tipo=GeraStrComboTpFiltro&tipo_campo='
								+ tipo_campo,
						beforeSend : function() {
						},
						done : function() {
						},
						success : function(response) {
							var arrResp = response.split('##@@##');
							var slct = arrResp[0];
							var inpt = arrResp[1];

							slc_tipo_filtro_tb.parent().html(slct);
							
							if( $('.linha-filtro-lista .input-group').length > 0 ){
								var parent = $('.linha-filtro-lista .input-group').parent();
								$('.linha-filtro-lista .input-group').remove();
								parent.append(inpt);
							}
							else{
								txt_campos_filtro_tb.parent().html(inpt);
							}

							/*
							 * @TODO pegar as mascaras e criar essas funcoes
							 */
							setTimeout("initDatePicker(); initOnlyNumbers(); initMaskMoney(); initDatePicker(); initMascaras();",150);
						}
					});
				}
			});

// botoes da lista
$( document ).on( "click", "button.btn-visualizar, button.btn-editar", function() {
  var url_del = getUrlHome();
  var controller = $(this).attr("controller");
  var action = $(this).attr("action");
  var id = $(this).attr("attr-lista-td-id");
  var uid = "frm-lista-visualizar";
  
  $("body").append("<form method='post' action='" + url_del + controller + '/' + action + "' id='" + uid + "'> <input type='hidden' name='post_id' value='" + id + "' /> </form>");
  setTimeout(" $('#" + uid + "').submit(); ", 150);
});

$( document ).on( "click", "button.btn-deletar", function() {
  var controller = $(this).attr("controller");
  var action = $(this).attr("action");
  var id = $(this).attr("attr-lista-td-id");
  var uid = "frm-lista-visualizar";
  
  AlertaConfirme ('Deseja mesmo deletar o item id ' + id + '?', 'Confirmar', 'Cancelar', 'processaBtnDeletar(\#' + controller + '\#, \#' + action + '\#, ' + id + ')', '');
});

function processaBtnDeletar(controller, action, id){
	
	var url_del = getUrlHome();
	
	ajax.url = url_del + controller + "/" + action;
	ajax.vars = "id=" + id;
	ajax.loadingPage = true;
	ajaxRequest();
	
	ajax.url = url_del;
	
}