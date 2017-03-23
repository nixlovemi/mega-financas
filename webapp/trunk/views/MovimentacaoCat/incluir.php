<?php
$id = (isset($id)) ? $viewModel->get("id_movimentacao"): $viewModel->get("id_movimentacao");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$descricao = (isset($descricao)) ? $descricao: $viewModel->get("descricao");
$cat_tipo = (isset($cat_tipo)) ? $cat_tipo: $viewModel->get("cat_tipo");
$arr_cat_tipo = (isset($arr_cat_tipo)) ? $arr_cat_tipo: $viewModel->get("arr_cat_tipo");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$html_subcat = (isset($html_subcat)) ? $html_subcat: "";
?>

<form id="frm-nova-categoria" class="form-horizontal bordered-group" role="form">
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
							<input class="form-control" name="inpt-mc-descricao" maxlength="50" value="<?php echo $descricao; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Tipo</label>
						<div class="col-sm-10">
							<select class="form-control" name="slct-mc-mt-id">
								<?php
								foreach($arr_cat_tipo as $linha){
									$v_mt_id = $linha["mt_id"];
									$v_mt_descricao = $linha["mt_descricao"];
									$selected = ($v_mt_id == $cat_tipo) ? " selected ": "";
									?>
									
									<option <?php echo $selected; ?> value="<?php echo $v_mt_id; ?>"><?php echo $v_mt_descricao; ?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
					<?php
					echo $html_subcat;
					?>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="hidden" name="hddn-id-movimentacao" value="<?php echo $id; ?>" />
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