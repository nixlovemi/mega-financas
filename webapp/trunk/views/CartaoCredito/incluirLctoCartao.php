<?php
$id = (isset($id)) ? $viewModel->get("id_conta"): $viewModel->get("id");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$descricao = (isset($descricao)) ? $descricao: $viewModel->get("descricao");
$dtDespesa = (isset($dtDespesa)) ? $dtDespesa: $viewModel->get("dataDespesa");
$vlrDespesa = (isset($vlrDespesa)) ? $vlrDespesa: $viewModel->get("vlrDespesa");
$idCartao = (isset($idCartao)) ? $idCartao: $viewModel->get("idCartao");
$parcela = (isset($parcela)) ? $parcela: $viewModel->get("parcela");
$tot_parcela = (isset($tot_parcela)) ? $tot_parcela: $viewModel->get("tot_parcela");
$str_parcela = ($parcela != "" && $tot_parcela != "") ? " @ Parcela $parcela/$tot_parcela": "";
$disabVcto = ($str_parcela != "") ? "disabled": "";

$arr_lista_cartao = (isset($arr_lista_cartao)) ? $arr_lista_cartao: $viewModel->get("arr_lista_cartao");
$arr_cat_saida = (isset($arr_cat_saida)) ? $arr_cat_saida: $viewModel->get("arr_cat_saida");
$html_combo_categoria = (isset($html_combo_categoria)) ? $html_combo_categoria: $viewModel->get("html_combo_categoria");
$html_combo_subcategoria = (isset($html_combo_subcategoria)) ? $html_combo_subcategoria: $viewModel->get("html_combo_subcategoria");
$escondeInptParcelamento = ($id > 0);
?>

<form id="frm-novo-cartao" class="form-horizontal bordered-group" role="form">
	<div class="panel">
		<div class="panel-heading border">
			<?php echo $titulo_janela . $str_parcela; ?>
		</div>
		<div class="panel-body">
			<div class="row no-margin">
				<div class="col-lg-12">
					<div class="form-group">
						<label class="col-sm-2 control-label">Descri&ccedil;&atilde;o</label>
						<div class="col-sm-10">
							<input class="form-control" name="inpt-ccm_descricao" maxlength="80" value="<?php echo $descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Data Despesa</label>
						<div class="col-sm-4">
							<input <?php echo $disabVcto; ?> name="inpt-ccm_data" class="form-control inpt-date-picker" value="<?php echo $dtDespesa; ?>" />
						</div>
						<label class="col-sm-2 control-label">Valor</label>
						<div class="col-sm-4">
							<input name="inpt-ccm_valor" class="form-control inpt-auto-numeric" value="<?php echo $vlrDespesa; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Cart&atilde;o</label>
						<div class="col-sm-10">
							<select class="form-control" name="inpt-ccf_cc_id">
								<option value=""></option>
								<?php
								foreach($arr_lista_cartao as $cartao){
									$v_cc_id = $cartao["cc_id"];
									$v_cc_descricao = $cartao["cc_descricao"];
									$sel = ($idCartao == $v_cc_id) ? "selected": "";
									
									echo "<option $sel value='$v_cc_id'>$v_cc_descricao</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Categoria</label>
						<div class="col-sm-10" id="dv-hold-mov-cat" data-spn="#spn-mov-subcategoria" data-nome="mov_subcat">
							<?php echo $html_combo_categoria; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Subcategoria</label>
						<div class="col-sm-10">
							<span id="spn-mov-subcategoria">
								<!--<select class="form-control" name="inpt-ccm_mc_id"><option value=""></option></select>-->
								<?php echo $html_combo_subcategoria; ?>
							</span>
						</div>
					</div>
					
					<?php
					if(!$escondeInptParcelamento){
						?>	
						<div class="form-group">
							<label class="col-sm-2 control-label">
								<input id="chbx-movimentacao-repetir" type="checkbox">&nbsp;Repetir por
							</label>
							<div class="col-sm-10">
								<div class="input-group">
									<input id="mov_repetir_por" name="mov_repetir_por" class="form-control inpt-only-int" disabled="disabled" />
									<span class="input-group-addon">meses</span>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					
					<div class="form-group">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							<input type="hidden" name="hddn-id-conta" value="<?php echo $id; ?>" />
							<button id="<?php echo $id_button; ?>" type="button" class="btn btn-primary btn-sm btn-icon mr5">
								<i class="fa fa-floppy-o"></i>
								<span>Gravar</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>