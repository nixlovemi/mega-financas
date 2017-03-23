<?php
$erro_msg = $viewModel->get("erro_msg");
$titulo_janela = $viewModel->get("titulo_janela");
$arr_movimentacoes = $viewModel->get("arr_movimentacoes");
$str_movimentacao = $viewModel->get("str_movimentacao");

$str_conta = (isset($arr_movimentacoes[0]["conta"])) ? $arr_movimentacoes[0]["conta"]: "";
$str_categoria = (isset($arr_movimentacoes[0]["categoria"])) ? $arr_movimentacoes[0]["categoria"]: "";
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
									<span style="font-size: 85%;" class="label bg-teal">Conta: <?php echo $str_conta; ?></span>
									<span style="font-size: 85%;" class="label bg-teal">Categoria: <?php echo $str_categoria; ?></span>
								</div>
							</div>
						</div>
	      				
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th>ID</th>
			        				<!--<th>Conta</th>
			        				<th>Categoria</th>-->
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th>Parcela</th>
			        				<th>Vencimento</th>
			        				<th class="numeric">Valor</th>
			        				<th>Pagamento</th>
			        				<th class="numeric">Valor Pago</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_movimentacoes as $movimentacao){
			        				$id = $movimentacao["id"];
			        				$conta = $movimentacao["conta"];
			        				$categoria = $movimentacao["categoria"];
			        				$descricao = $movimentacao["descricao"];
			        				$vencimento = ($movimentacao["vencimento"] != "") ? date("d/m/Y", strtotime($movimentacao["vencimento"])): "&nbsp;";
			        				$valor = (is_numeric($movimentacao["valor"])) ? "R$ " . number_format($movimentacao["valor"], 2, ",", "."): "&nbsp;";
			        				$pagamento = ($movimentacao["pagamento"] != "") ? date("d/m/Y", strtotime($movimentacao["pagamento"])): "&nbsp;";
			        				$valor_pago = (is_numeric($movimentacao["valor_pago"])) ? "R$ " . number_format($movimentacao["valor_pago"], 2, ",", "."): "&nbsp;";
			        				
			        				$parcela = $movimentacao["parcela"];
			        				$tot_parcelas = $movimentacao["tot_parcelas"];
			        				$str_parcelas = ($parcela != "" && $tot_parcelas != "") ? "$parcela/$tot_parcelas": "&nbsp;";
			        				?>
			        				<tr>
			        					<td data-title="ID"><?php echo $id; ?></td>
			        					<!--<td data-title="Conta"><?php echo $conta; ?></td>
			        					<td data-title="Categoria"><?php echo $categoria; ?></td>-->
			        					<td data-title="Descri&ccedil;&atilde;o"><?php echo $descricao; ?></td>
			        					<td data-title="Parcela"><?php echo $str_parcelas; ?></td>
			        					<td data-title="Vencimento"><?php echo $vencimento; ?></td>
			        					<td data-title="Valor" class="numeric"><?php echo $valor; ?></td>
			        					<td data-title="Pagamento"><?php echo $pagamento; ?></td>
			        					<td data-title="Valor Pago" class="numeric"><?php echo $valor_pago; ?></td>
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