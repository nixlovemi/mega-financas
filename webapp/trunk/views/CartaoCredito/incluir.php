<?php
$id = (isset($id)) ? $viewModel->get("id_conta"): $viewModel->get("id");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");

$descricao = (isset($descricao)) ? $descricao: $viewModel->get("descricao");
$id_bandeira = (isset($id_bandeira)) ? $id_bandeira: $viewModel->get("id_bandeira");
$arr_bandeira = (isset($arr_bandeira)) ? $arr_bandeira: $viewModel->get("arr_bandeira");
$limite = (isset($limite)) ? $limite: $viewModel->get("limite");
$dia_fechamento = (isset($dia_fechamento)) ? $dia_fechamento: $viewModel->get("dia_fechamento");
$dia_pagamento = (isset($dia_pagamento)) ? $dia_pagamento: $viewModel->get("dia_pagamento");
?>

<form id="frm-novo-cartao" class="form-horizontal bordered-group" role="form">
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
							<input class="form-control" name="inpt-cc-descricao" maxlength="40" value="<?php echo $descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Bandeira</label>
						<div class="col-sm-10">
							<select class="form-control"  name="inpt-cc-bc-id">
								<?php
								foreach($arr_bandeira as $bandeira){
									$v_id = $bandeira["bc_id"];
									$v_desc = $bandeira["bc_descricao"];
									$sel = ($id_bandeira == $v_id) ? "selected": "";
									
									?>
									
									<option <?php echo $sel; ?> value="<?php echo $v_id; ?>"><?php echo $v_desc; ?></option>
									
									<?php
								}
								?>
								
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Limite</label>
						<div class="col-sm-10">
							<input name="cc-limite" class="form-control inpt-auto-numeric" value="<?php echo $limite; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Fechamento todo dia</label>
						<div class="col-sm-4">
							<select class="form-control"  name="cc-dia-fechamento">
								<?php
								for($i=1; $i <= 31; $i++){
									$sel = ($dia_fechamento == $i) ? "selected": "";
									?>
									
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									
									<?php
								}
								?>
							</select>
						</div>
						<label class="col-sm-2 control-label">Pagamento todo dia</label>
						<div class="col-sm-4">
							<select class="form-control"  name="cc-dia-pagamento">
								<?php
								for($i=1; $i <= 31; $i++){
									$sel = ($dia_pagamento == $i) ? "selected": "";
									?>
									
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									
									<?php
								}
								?>
							</select>
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