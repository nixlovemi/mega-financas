<?php
$erro_msg = $viewModel->get("erro_msg");
$arr_entradas = $viewModel->get("arrEntradas");
$arr_saidas = $viewModel->get("arrSaidas");
?>

<div id="dv-holder-nova-categoria">
	<a href="javascript:void(0);" class="btn btn-info btn-lg mr5 mb10" id="btn-nova-categoria">NOVA CATEGORIA</a>
	<div class="panel mb25">
		<div class="panel-heading border">
			<i class="fa fa-tags"></i>&nbsp;Categorias
		</div>
		<div class="panel-body">
	    <?php
			if ($erro_msg != "") {
				?>
      			<div class="alert alert-danger"> <?php echo $erro_msg; ?> </div>
      			<?php
			}
			else {
				?>
	      		<div class="row no-margin">
					<div class="col-lg-12">
						<div class="alert alert-success mb0">Receitas</div>
						<div class="no-more-tables">
							<?php
							if (count($arr_entradas) <= 0) {
								echo "Nenhuma entrada cadastrada =/";
							}
							else {
								?>
								<table class="col-md-12 table-bordered table-striped table-condensed cf">
								<thead class="cf">
									<tr>
										<th></th>
										<th width="60%">Descri&ccedil;&atilde;o</th>
										<th>Subcategorias</th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;
									foreach($arr_entradas as $linha){
										$vId = $linha["mc_id"];
										$vDescricao = $linha["mc_descricao"];
										$vSubcats = $linha["qt_subcat"];
										?>
										
										<tr>
											<td data-title="#">#<?php echo $i; ?></td>
											<td data-title="Descri&ccedil;&atilde;o"><?php echo $vDescricao; ?></td>
											<td data-title="Subcategorias"><?php echo $vSubcats ?></td>
											<td data-title="Alterar">
												<a id="lnk-edit-categorias" href="javascript:void(0)" data-id="<?php echo $vId; ?>">
													<i style="font-size: 18px;" class="fa fa-pencil-square-o"></i>
												</a>
											</td>
											<td data-title="Deletar">
												<a id="lnk-delete-categorias" href="javascript:void(0)" data-id="<?php echo $vId; ?>">
													<i style="font-size: 18px;" class="fa fa-trash"></i>
												</a>
											</td>
										</tr>
										
										<?php
										$i++;
									}
									?>
								</tbody>
							</table>
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<br />
				<div class="row no-margin">
					<div class="col-lg-12">
						<div class="alert alert-danger mb0">Despesas</div>
						<div class="no-more-tables">
							<?php
							if (count($arr_saidas) <= 0) {
								echo "Nenhuma entrada cadastrada =/";
							}
							else {
								?>
								<table class="col-md-12 table-bordered table-striped table-condensed cf">
								<thead class="cf">
									<tr>
										<th></th>
										<th width="60%">Descri&ccedil;&atilde;o</th>
										<th>Subcategorias</th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;
									foreach($arr_saidas as $linha){
										$vId = $linha["mc_id"];
										$vDescricao = $linha["mc_descricao"];
										$vSubcats = $linha["qt_subcat"];
										?>
										
										<tr>
											<td data-title="#">#<?php echo $i; ?></td>
											<td data-title="Descri&ccedil;&atilde;o"><?php echo $vDescricao; ?></td>
											<td data-title="Subcategorias"><?php echo $vSubcats ?></td>
											<td data-title="Alterar">
												<a id="lnk-edit-categorias" href="javascript:void(0)" data-id="<?php echo $vId; ?>">
													<i style="font-size: 18px;" class="fa fa-pencil-square-o"></i>
												</a>
											</td>
											<td data-title="Deletar">
												<a id="lnk-delete-categorias" href="javascript:void(0)" data-id="<?php echo $vId; ?>">
													<i style="font-size: 18px;" class="fa fa-trash"></i>
												</a>
											</td>
										</tr>
										
										<?php
										$i++;
									}
									?>
								</tbody>
							</table>
								<?php
							}
							?>
						</div>
					</div>
				</div>
      			<?php
			}
			?>
	  </div>
	</div>
</div>