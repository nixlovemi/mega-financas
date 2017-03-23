<?php
$id = (isset($id)) ? $viewModel->get("id_conta"): $viewModel->get("id_conta");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$descricao = (isset($descricao)) ? $descricao: $viewModel->get("descricao");
$saldo_inicial = (isset($saldo_inicial)) ? $saldo_inicial: $viewModel->get("saldo_inicial");
$cor = (isset($cor)) ? $cor: $viewModel->get("cor");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
?>

<form id="frm-nova-conta" class="form-horizontal bordered-group" role="form">
	<div class="panel">
		<div class="panel-heading border">
			<?php echo $titulo_janela; ?>
		</div>
		<div class="panel-body">
			<div class="row no-margin">
				<div class="col-lg-12">
					<div class="form-group">
						<label class="col-sm-2 control-label">Descri&ccedil;&atilde;o</label>
						<div class="col-sm-10">
							<input class="form-control" name="inpt-con-nome" maxlength="40" value="<?php echo $descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Saldo Inicial</label>
						<div class="col-sm-10">
							<input class="form-control inpt-auto-numeric" name="inpt-con-saldo-inicial" value="<?php echo $saldo_inicial; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Cor</label>
						<div class="col-sm-10">
							<input class="form-control inpt-color-picker" name="inpt-con-cor" value="<?php echo $cor; ?>" />
							<?php
							/*
							<div class="input-group dv-color-picker">
							    <input type="text" value="" class="form-control" />
							    <span class="input-group-addon"><i></i></span>
							</div>
							*/
							?>
						</div>
					</div>
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