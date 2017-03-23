<?php
$erro_msg = $viewModel->get("erro_msg");
$titulo_janela = $viewModel->get("titulo_janela");
$arr_movimentacoes = $viewModel->get("arr_movimentacoes");

$str_cartao = (isset($arr_movimentacoes[0]["cc_descricao"])) ? $arr_movimentacoes[0]["cc_descricao"]: "";
$str_categoria = (isset($arr_movimentacoes[0]["mc_descricao"])) ? $arr_movimentacoes[0]["mc_descricao"]: "";
?>

<div id="dv-holder-mostra-parcelamento">
	<div class="panel mb25">
	  <div class="panel-heading border">
	    <?php echo $titulo_janela; ?>
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
	      				echo "Nenhum parcelamento encontrado =/";
	      			}
	      			else{
	      				?>
	      				<div class="row mb10" style="margin-left: 0px;">
							<div class="col-md-12">
								<div class="row">
									<span style="font-size: 85%;" class="label bg-teal">Cart&atilde;o: <?php echo $str_cartao; ?></span>
									<span style="font-size: 85%;" class="label bg-teal">Categoria: <?php echo $str_categoria; ?></span>
								</div>
							</div>
						</div>
	      				
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th>ID</th>
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th>Parcela</th>
			        				<th>Vencimento</th>
			        				<th class="numeric">Valor</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_movimentacoes as $movimentacao){
			        				$id = $movimentacao["ccm_id"];
			        				$descricao = $movimentacao["ccm_descricao"];
			        				$vencimento = ($movimentacao["ccm_data"] != "") ? date("d/m/Y", strtotime($movimentacao["ccm_data"])): "&nbsp;";
			        				$mesFatura = (strlen($movimentacao["ccf_mes"]) == 1) ? "0" . $movimentacao["ccf_mes"]: $movimentacao["ccf_mes"];
			        				$anoFatura = $movimentacao["ccf_ano"];
			        				$valor = (is_numeric($movimentacao["ccm_valor"])) ? "R$ " . number_format($movimentacao["ccm_valor"], 2, ",", "."): "&nbsp;";
			        				
			        				$parcela = $movimentacao["ccm_parcela"];
			        				$tot_parcelas = $movimentacao["tp_tot_parcelas"];
			        				$str_parcelas = ($parcela != "" && $tot_parcelas != "") ? "$parcela/$tot_parcelas": "&nbsp;";
			        				?>
			        				<tr>
			        					<td data-title="ID"><?php echo $id; ?></td>
			        					<td data-title="Descri&ccedil;&atilde;o"><?php echo $descricao; ?></td>
			        					<td data-title="Parcela"><?php echo $str_parcelas; ?></td>
			        					<td data-title="Vencimento"><?php echo "$mesFatura/$anoFatura"; ?></td>
			        					<td data-title="Valor" class="numeric"><?php echo $valor; ?></td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-edit-lcto-cartao" href="javascript:void(0)" data-id="<?php echo $id;?>">
				        						<i style="font-size:18px;" class="fa fa-pencil-square-o"></i>
				        					</a>
				        				</td>
				        				<td data-title="&nbsp;" align="center">
				        					<a id="lnk-delete-lcto-cartao" href="javascript:void(0)" data-id="<?php echo $id;?>">
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