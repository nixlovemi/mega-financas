<?php
$id_movimentacao = $viewModel->get("id_movimentacao");
$titulo_janela = $viewModel->get("titulo_janela");
$str_movimentacao = $viewModel->get("str_movimentacao");
$str_conta = $viewModel->get("str_conta");
$str_categoria = $viewModel->get("str_categoria");
$str_descricao = $viewModel->get("str_descricao");
$str_competencia = $viewModel->get("str_competencia");
$str_vencimento = $viewModel->get("str_vencimento");
$str_valor = $viewModel->get("str_valor");

$str_competencia = ( strlen($str_competencia) == 10 ) ? date("d/m/Y", strtotime($str_competencia)): "&nbsp;";
$str_vencimento = ( strlen($str_vencimento) == 10 ) ? date("d/m/Y", strtotime($str_vencimento)): "&nbsp;";
$str_valor = (is_numeric($str_valor)) ? number_format($str_valor, 2, ",", "."): "&nbsp;";
?>

<form id="frm-editar-mov-competencia" class="form-horizontal bordered-group" role="form">
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
							<input disabled="disabled" name="emc_conta" class="form-control" value="<?php echo $str_conta; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Descri&ccedil;&atilde;o</label>
						<div class="col-sm-10">
							<input disabled="disabled" name="emc_descricao" class="form-control" value="<?php echo $str_descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Categoria</label>
						<div class="col-sm-10">
							<input disabled="disabled" name="emc_categoria" class="form-control" value="<?php echo $str_categoria; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Vencimento</label>
						<div class="col-sm-4">
							<input disabled="disabled" name="emc_vencimento" class="form-control inpt-date-picker" value="<?php echo $str_vencimento; ?>" />
						</div>
						<label class="col-sm-2 control-label">Valor</label>
						<div class="col-sm-4">
							<input disabled="disabled" name="emc_valor" class="form-control inpt-auto-numeric" value="<?php echo $str_valor; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Compet&ecirc;ncia</label>
						<div class="col-sm-10">
							<input name="emc_competencia" class="form-control inpt-date-picker" value="<?php echo $str_competencia; ?>" />
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-sm-12">
							<input type="hidden" name="hddn-id-movimentacao" value="<?php echo $id_movimentacao; ?>" />
							<button id="btn-post-editar-mov-competencia" data-str-movimentacao="<?php echo $str_movimentacao; ?>" type="button" class="btn btn-primary btn-sm btn-icon mr5">
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