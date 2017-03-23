<?php
$id_movimentacao = (isset($id_movimentacao)) ? $id_movimentacao: $viewModel->get("id_movimentacao");
$id_form = (isset($id_form)) ? $id_form: $viewModel->get("id_form");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$combo_contas_orig = (isset($combo_contas_orig)) ? $combo_contas_orig: $viewModel->get("combo_contas_orig");
$combo_contas_dest = (isset($combo_contas_dest)) ? $combo_contas_dest: $viewModel->get("combo_contas_dest");

$vencimento = (isset($vencimento)) ? $vencimento: "";
$valor = (isset($valor)) ? $valor: "";
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
						<label class="col-sm-2 control-label">Conta Origem</label>
						<div class="col-sm-10">
							<?php
							echo $combo_contas_orig;
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Conta Destino</label>
						<div class="col-sm-10">
							<?php
							echo $combo_contas_dest;
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Data</label>
						<div class="col-sm-4">
							<input name="mov_dt_vencimento" class="form-control inpt-date-picker" value="<?php echo $vencimento; ?>" />
						</div>
						<label class="col-sm-2 control-label">Valor</label>
						<div class="col-sm-4">
							<input name="mov_valor" class="form-control inpt-auto-numeric" value="<?php echo $valor; ?>" />
						</div>
					</div>
					
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