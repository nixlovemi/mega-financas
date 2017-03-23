<?php
$str_nome = $viewModel->get ("str_nome");
$str_sobrenome = $viewModel->get ("str_sobrenome");
$str_email = $viewModel->get ("str_email");
?>

<form id="frm-configs" class="form-horizontal bordered-group" role="form">
	<div class="panel mb25">
		<div class="panel-heading border">
			<i class="fa fa-cog"></i>&nbsp;Configura&ccedil;&otilde;es
		</div>
		<div class="panel-body">
			<div class="row no-margin">
				<div class="col-lg-12">
					<div class="form-group">
						<label class="col-sm-2 control-label">Nome</label>
						<div class="col-sm-10">
							<input class="form-control" name="inpt-nome" maxlength="80" value="<?php echo $str_nome; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Sobrenome</label>
						<div class="col-sm-10">
							<input class="form-control"name="inpt-sobrenome" maxlength="80" value="<?php echo $str_sobrenome; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<input class="form-control" readonly="readonly" value="<?php echo $str_email; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							<button id="btn-grava-configs" type="button" class="btn btn-primary btn-sm btn-icon mr5">
								<i class="fa fa-floppy-o"></i>
								<span>Gravar Configura&ccedil;&otilde;es</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel mb25">
		<div class="panel-heading border">
			<i class="fa fa-lock"></i>&nbsp;Senha
		</div>
		<div class="panel-body">
			<div class="row no-margin">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="col-sm-12">
							<button type="button" class="btn btn-info" onClick="$('.dv-chng-psswrd').toggle();">Alterar Senha</button>
						</div>
					</div>
					<div class="form-group dv-chng-psswrd" style="display: none;">
						<label class="col-sm-2 control-label">Nova Senha</label>
						<div class="col-sm-10">
							<input class="form-control" type="password" name="inpt-nova-senha" id="inpt-nova-senha" value="" />
						</div>
					</div>
					<div class="form-group dv-chng-psswrd" style="display: none;">
						<label class="col-sm-2 control-label">Repetir Nova Senha</label>
						<div class="col-sm-10">
							<input class="form-control" type="password" name="inpt-repetir-nova-senha" id="inpt-repetir-nova-senha" value="" />
						</div>
					</div>
					<div class="form-group dv-chng-psswrd" style="display: none;">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							<button id="btn-confirmar-senha" type="button" class="btn btn-primary btn-sm btn-icon mr5">
								<i class="fa fa-floppy-o"></i>
								<span>Confirmar Senha</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>