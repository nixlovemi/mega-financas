<?php
$str_movimentacao = "lcto-cartao";
$erro_msg = (isset($erro_msg)) ? $erro_msg: $viewModel->get("erro_msg");
$dt_fatura = (isset($dt_fatura)) ? $dt_fatura: $viewModel->get("data_fatura");
$arr_mes = (isset($arr_mes)) ? $arr_mes: $viewModel->get("arr_mes");
$cartao_descricao = (isset($cartao_descricao)) ? $cartao_descricao: $viewModel->get("cartao_descricao");
$id_cartao = (isset($id_cartao)) ? $id_cartao: $viewModel->get("id_cartao");
$arr_cartao = (isset($arr_cartao)) ? $arr_cartao: $viewModel->get("arr_cartao");
$arr_movimentacoes = (isset($arr_movimentacoes)) ? $arr_movimentacoes: $viewModel->get("arr_movimentacoes");
$btn_topo_id = (isset($btn_topo_id)) ? $btn_topo_id: $viewModel->get("btn_topo_id");
$btn_topo_text = (isset($btn_topo_text)) ? $btn_topo_text: $viewModel->get("btn_topo_text");
$icon_topo_name = (isset($icon_topo_name)) ? $icon_topo_name: $viewModel->get("icon_topo_name");
$icon_topo_text = (isset($icon_topo_text)) ? $icon_topo_text: $viewModel->get("icon_topo_text");
?>

<div id="dv-holder-novo-lcto-cartao">
	<a href="javascript:;" class="btn btn-info btn-lg mr5 mb10" id="<?php echo $btn_topo_id; ?>"><?php echo $btn_topo_text; ?></a>
	<div class="row mb10" style="margin-left: 0px;">
		<div class="col-md-12">
			<div class="row">
				<span style="font-size: 85%;" class="label bg-teal"><a onClick="$('#change-conta').toggle();" href="javascript:void(0);">Cart&atilde;o: <?php echo $cartao_descricao; ?></a></span>
				<span style="font-size: 85%;" class="label bg-teal"><a onClick="$('#change-conta').toggle();" href="javascript:void(0);">Fatura: <?php echo strftime("%h-%Y", strtotime($dt_fatura)); ?></a></span>
			</div>
		</div>
	</div>
	
	<form id="frmChangeCartao" method="post" action="">
		<div id="change-conta" style="display: none; margin-left: 0px;" class="row mb10">
			<div class="col-md-2 col-xs-4" style="padding-left: 0 !important;">
				<select name="idCartao" id="idCartao" class="form-control">
					<?php
					foreach($arr_cartao as $cartao){
						$v_id = $cartao["cc_id"];
						$v_desc = $cartao["cc_descricao"];
						$v_sel = ($v_id == $id_cartao) ? " selected ": "";
						?>
						
						<option <?php echo $v_sel; ?> value="<?php echo $v_id; ?>"><?php echo $v_desc; ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-2 col-xs-3" style="padding-left: 0 !important;">
				<select name="dt_fatura" id="dt_fatura" class="form-control">
					<?php
					foreach($arr_mes as $data => $str_data){
						$v_sel = ($data == $dt_fatura) ? " selected ": "";
						?>
						
						<option <?php echo $v_sel; ?> value="<?php echo $data; ?>"><?php echo $str_data; ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-1 col-xs-1" style="padding-left: 0 !important;">
				<button style="margin-top: 3px;" type="button" class="btn bg-teal btn-sm btn-icon mr5" onclick=" var vars = $('#frmChangeCartao').serialize(); ajaxResposta('CartaoCredito', 'indexLancamentos', vars, '.main-content'); ">
					<i class="fa fa-refresh"></i>
					<span>&nbsp;</span>
				</button>
			</div>
		</div>
	</form>
	
	<div class="panel mb25">
	  <div class="panel-heading border">
	    <i class="fa <?php echo $icon_topo_name; ?>"></i>&nbsp;<?php echo $icon_topo_text; ?>
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
	      				echo "Nenhum lan&ccedil;amento cadastrado nesse per&iacute;odo =/";
	      			}
	      			else{
	      				?>
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th>ID</th>
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th>Categoria</th>
			        				<th>Gerada Em</th>
			        				<th class="numeric">Valor</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_movimentacoes as $movimentacao){
			        				$id = $movimentacao["ccm_id"];
			        				$categoria = $movimentacao["mc_descricao"];
			        				$descricao = $movimentacao["ccm_descricao"];
			        				$competencia = ($movimentacao["ccm_data"] != "") ? date("d/m/Y", strtotime($movimentacao["ccm_data"])): "&nbsp;";
			        				$valor = (is_numeric($movimentacao["ccm_valor"])) ? "R$ " . number_format($movimentacao["ccm_valor"], 2, ",", "."): "&nbsp;";
			        				$parcela = ($movimentacao["ccm_parcela"] > 0) ? $movimentacao["ccm_parcela"]: "";
			        				$tot_parcelas = ($movimentacao["tp_tot_parcelas"] > 0) ? $movimentacao["tp_tot_parcelas"]: "";
			        				$str_parcelas = ($parcela != "" && $tot_parcelas != "") ? "$parcela/$tot_parcelas": "";
			        				$link_parcelas = ($str_parcelas != "") ? "<a data-toggle='tooltip' data-placement='top' data-original-title='Exibir parcelamento' data-id='$id' id='ccm_mostra_parcelas' class='link-blue' href='javascript:;'>$descricao $str_parcelas</a>": "$descricao $str_parcelas";
			        				?>
			        				<tr>
			        					<td data-title="ID"><?php echo $id; ?></td>
			        					<td data-title="Descri&ccedil;&atilde;o"><?php echo $link_parcelas; ?></td>
			        					<td data-title="Categoria"><?php echo $categoria; ?></td>
			        					<td data-title="Gerada Em">
			        						<?php echo $competencia; ?>
			        					</td>
			        					<td data-title="Valor" class="numeric"><?php echo $valor; ?></td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-edit-<?php echo $str_movimentacao; ?>" href="javascript:void(0)" data-id="<?php echo $id; ?>">
				        						<i style="font-size:18px;" class="fa fa-pencil-square-o"></i>
				        					</a>
				        				</td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-delete-<?php echo $str_movimentacao; ?>" href="javascript:void(0)" data-id="<?php echo $id; ?>">
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
	</div>
</div>