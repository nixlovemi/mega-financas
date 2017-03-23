<?php
$id_movimentacao = (isset($id_movimentacao)) ? $id_movimentacao: $viewModel->get("id_movimentacao");
$id_form = (isset($id_form)) ? $id_form: $viewModel->get("id_form");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$combo_contas = (isset($combo_contas)) ? $combo_contas: $viewModel->get("combo_contas");
$combo_categoria = (isset($combo_categoria)) ? $combo_categoria: $viewModel->get("combo_categoria");
$combo_subcategoria = (isset($combo_subcategoria)) ? $combo_subcategoria: $viewModel->get("combo_subcategoria");

$descricao = (isset($descricao)) ? $descricao: "";
$vencimento = (isset($vencimento)) ? $vencimento: "";
$valor = (isset($valor)) ? $valor: "";
$pagamento = (isset($pagamento)) ? $pagamento: "";
$valor_pago = (isset($valor_pago)) ? $valor_pago: "";
$str_parcela = (isset($str_parcela)) ? $str_parcela: "";
$eh_incluir = ($id_button == "btn-post-nova-despesa" || $id_button == "btn-post-nova-receita");

$titulo_janela = ($str_parcela != "") ? $titulo_janela . " @ Parcela $str_parcela": $titulo_janela;
?>

<form id="<?php echo $id_form; ?>" class="form-horizontal bordered-group" role="form">
	<div class="panel">
		<div class="panel-heading border">
			<?php echo $titulo_janela; ?>
		</div>
		<div class="panel-body">
			<div class="row no-margin">
				<div class="col-lg-12">
					<div class="form-group">
						<label class="col-sm-2 control-label">Conta</label>
						<div class="col-sm-10">
							<?php
							echo $combo_contas;
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Descri&ccedil;&atilde;o</label>
						<div class="col-sm-10">
							<input name="mov_descricao" class="form-control" value="<?php echo $descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Categoria</label>
						<div class="col-sm-10" id="dv-hold-mov-cat" data-spn="#spn-mov-subcategoria" data-nome="mov_subcat">
							<?php
							echo $combo_categoria;
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Subcategoria</label>
						<div class="col-sm-10">
							<span id="spn-mov-subcategoria">
								<?php
								echo $combo_subcategoria;
								?>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Vencimento</label>
						<div class="col-sm-4">
							<input name="mov_dt_vencimento" class="form-control inpt-date-picker" value="<?php echo $vencimento; ?>" />
						</div>
						<label class="col-sm-2 control-label">Valor</label>
						<div class="col-sm-4">
							<input name="mov_valor" class="form-control inpt-auto-numeric" value="<?php echo $valor; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pagamento</label>
						<div class="col-sm-4">
							<input name="mov_dt_pagamento" class="form-control inpt-date-picker" value="<?php echo $pagamento; ?>" />
						</div>
						<label class="col-sm-2 control-label">Valor</label>
						<div class="col-sm-4">
							<input name="mov_valor_pago" class="form-control inpt-auto-numeric" value="<?php echo $valor_pago; ?>" />
						</div>
					</div>
					
					<?php
					if($eh_incluir){
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
						<div class="col-sm-12">
							<input type="hidden" name="hddn-id-movimentacao" value="<?php echo $id_movimentacao; ?>" />
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