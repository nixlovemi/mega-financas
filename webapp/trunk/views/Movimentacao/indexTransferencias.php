<?php
$erro_msg = (isset($erro_msg)) ? $erro_msg: "";
$arr_movimentacoes = (isset($arr_movimentacoes)) ? $arr_movimentacoes: $viewModel->get("arr_movimentacoes");
$arr_totais = $viewModel->get("arr_totais");
$conta_descricao = (isset($conta_descricao)) ? $conta_descricao: $viewModel->get("conta_descricao");
$arr_contas = (isset($arr_contas)) ? $arr_contas: $viewModel->get("arr_contas");
$dt_ini = (isset($data_inicio)) ? $data_inicio: $viewModel->get("data_inicio");
$dt_fim = (isset($data_fim)) ? $data_fim: $viewModel->get("data_fim");
?>

<div id="dv-holder-transferencias">
	<a href="javascript:;" class="btn btn-info btn-lg mr5 mb10" id="btn-nova-transferencia">NOVA TRANSFER&Ecirc;NCIA</a>
	<div class="row mb10" style="margin-left: 0px;">
		<div class="col-md-12">
			<div class="row">
				<span style="font-size: 85%;" class="label bg-teal"><a onClick="$('#change-conta').toggle();" href="javascript:void(0);">Conta: <?php echo $conta_descricao; ?></a></span>
				<span style="font-size: 85%;" class="label bg-teal"><a onClick="$('#change-conta').toggle();" href="javascript:void(0);">Per&iacute;odo: <?php echo strftime("%d/%b/%y", strtotime($dt_ini)); ?> - <?php echo strftime("%d/%b/%y", strtotime($dt_fim)); ?></a></span>
			</div>
		</div>
	</div>
	
	<form id="frmChangeConta" method="post" action="">
		<div id="change-conta" style="display: none; margin-left: 0px;" class="row mb10">
			<div class="col-md-2 col-xs-4" style="padding-left: 0 !important;">
				<select name="idConta" id="idConta" class="form-control">
					<?php
					foreach($arr_contas as $conta){
						$v_id = $conta["con_id"];
						$v_desc = $conta["con_nome"];
						$v_sel = ($v_id == $id_conta) ? " selected ": "";
						?>
						
						<option <?php echo $v_sel; ?> value="<?php echo $v_id; ?>"><?php echo $v_desc; ?></option>
						
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-1 col-xs-3" style="padding-left: 0 !important;">
				<input name="dtInicio" id="dtInicio" class="form-control inpt-date-picker" value="<?php echo date("d/m/Y", strtotime($dt_ini)); ?>" placeholder="Per&iacute;odo Inicial" />
			</div>
			<div class="col-md-1 col-xs-3" style="padding-left: 0 !important;">
				<input name="dtFim" id="dtFim" class="form-control inpt-date-picker" value="<?php echo date("d/m/Y", strtotime($dt_fim)); ?>" placeholder="Per&iacute;odo Final" />
			</div>
			<div class="col-md-1 col-xs-1" style="padding-left: 0 !important;">
				<button data-toggle='tooltip' data-placement='top' data-original-title='O filtro &eacute; baseado na Data de Gera&ccedil;&atilde;o da Transfer&ecirc;ncia' style="margin-top: 3px;" type="button" class="btn bg-teal btn-sm btn-icon mr5" onclick=" var vars = $('#frmChangeConta').serialize(); ajaxResposta('Movimentacao', 'indexTransferencias', vars, '.main-content'); ">
					<i class="fa fa-refresh"></i>
					<span>&nbsp;</span>
				</button>
			</div>
		</div>
	</form>
	
	<div class="panel mb25">
	  <div class="panel-heading border">
	    <i class="fa fa-external-link-square"></i>&nbsp;Transfer&ecirc;ncias
	  </div>
	  <div class="panel-body">
	    <div class="row no-margin">
	      <div class="col-lg-12">
	      	<?php
	      	if( $erro_msg != "" ){
	      		?>
	      		<div class="alert alert-danger"> <?php echo $erro_msg; ?> </div>
	      		<?php
	      	}
	      	else{
	      		?>
	      		<div class="no-more-tables">
	      			<?php
	      			if( count($arr_movimentacoes) == 0 ){
	      				echo "Nenhuma transfer&ecirc;ncia cadastrada nesse per&iacute;odo =/";
	      			}
	      			else{
	      				?>
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th>ID</th>
			        				<th>Origem</th>
			        				<th>Destino</th>
			        				<th>Data</th>
			        				<th class="numeric">Valor</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_movimentacoes as $movimentacao){
			        				$id = $movimentacao["id_movimentacao"];
			        				$origem = $movimentacao["origem"];
			        				$destino = $movimentacao["destino"];
			        				$data = ($movimentacao["data"] != "") ? date("d/m/Y", strtotime($movimentacao["data"])): "&nbsp;";
			        				$valor = (is_numeric($movimentacao["valor"])) ? "R$ " . number_format($movimentacao["valor"], 2, ",", "."): "&nbsp;";
			        				?>
			        				<tr>
			        					<td data-title="ID"><?php echo $id; ?></td>
			        					<td data-title="Origem"><?php echo $origem; ?></td>
			        					<td data-title="Destino"><?php echo $destino; ?></td>
												<td data-title="Data"><?php echo $data; ?></td>
			        					<td data-title="Valor" class="numeric"><?php echo $valor; ?></td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-edit-transferencia" href="javascript:void(0)" data-id="<?php echo $id; ?>">
				        						<i style="font-size:18px;" class="fa fa-pencil-square-o"></i>
				        					</a>
				        				</td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-delete-transferencia" href="javascript:void(0)" data-id="<?php echo $id; ?>">
				        						<i style="font-size:18px;" class="fa fa-trash"></i>
				        					</a>
				        				</td>
				        			</tr>
			        				<?php
			        			}
			        			?>
			        		</tbody>
			        	</table>
	      				<?php
	      			}
	      			?>
		        </div>
	      		<?php
	      	}
	      	?>
	      </div>
	    </div>
	  </div>

          <div style="margin-top:30px;"></div>

            <div class="row no-margin">
                <?php
                $cntContas = (isset($arr_totais["totalContas"]["qt"])) ? $arr_totais["totalContas"]["qt"]: 0;
                $vlrContas = (isset($arr_totais["totalContas"]["vlr"])) ? $arr_totais["totalContas"]["vlr"]: 0;
                ?>
                <div class="col-md-4">
                    <section class="widget btn-info text-center">
                      <div class="widget-details col-xs-6">
                        <h2 class="no-margin"><?php echo $cntContas; ?></h2>
                        <small class="text-uppercase">Total de Contas</small>
                      </div>
                      <div class="widget-details col-xs-6">
                        <h2 class="no-margin">R$ <?php echo number_format($vlrContas, 2, ",", "."); ?></h2>
                        <small class="text-uppercase">Valor Total das Contas</small>
                      </div>
                    </section>
                </div>

                <?php
                $cntPago = (isset($arr_totais["totalPago"]["qt"])) ? $arr_totais["totalPago"]["qt"]: 0;
                $vlrPago = (isset($arr_totais["totalPago"]["vlr"])) ? $arr_totais["totalPago"]["vlr"]: 0;
                ?>
                <div class="col-md-4">
                    <section class="widget btn-info text-center">
                      <div class="widget-details col-xs-6">
                        <h2 class="no-margin"><?php echo $cntPago; ?></h2>
                        <small class="text-uppercase">Contas Pagas</small>
                      </div>
                      <div class="widget-details col-xs-6">
                        <h2 class="no-margin">R$ <?php echo number_format($vlrPago, 2, ",", "."); ?></h2>
                        <small class="text-uppercase">Valor Total das Contas Pagas</small>
                      </div>
                    </section>
                </div>
            </div>
	  </div>
	</div>

	</div>
</div>