<?php
$erro_msg = (isset($erro_msg)) ? $erro_msg: "";
$arr_movimentacoes = (isset($arr_movimentacoes)) ? $arr_movimentacoes: array();
$id_conta = (isset($id_conta)) ? $id_conta: "";
$arr_contas = (isset($arr_contas)) ? $arr_contas: array();
$conta_descricao = (isset($conta_descricao)) ? $conta_descricao: "";
$dt_ini = (isset($data_inicio)) ? $data_inicio: "";
$dt_fim = (isset($data_fim)) ? $data_fim: "";
$btn_topo_id = (isset($btn_topo_id)) ? $btn_topo_id: "";
$btn_topo_text = (isset($btn_topo_text)) ? $btn_topo_text: "";
$icon_topo_name = (isset($icon_topo_name)) ? $icon_topo_name: "";
$icon_topo_text = (isset($icon_topo_text)) ? $icon_topo_text: "";
$tp_movimentacao = (isset($tp_movimentacao)) ? $tp_movimentacao: "Receitas";
$str_movimentacao = ($tp_movimentacao == "Receitas") ? "receita": "despesa";
$action_change = ($tp_movimentacao == "Receitas") ? "indexReceitas": "indexDespesas";
?>

<div id="dv-holder-nova-receita">
	<a href="javascript:;" class="btn btn-info btn-lg mr5 mb10" id="<?php echo $btn_topo_id; ?>"><?php echo $btn_topo_text; ?></a>
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
				<button data-toggle='tooltip' data-placement='top' data-original-title='O filtro &eacute; baseado na Data de Gera&ccedil;&atilde;o da <?php echo $str_movimentacao; ?>' style="margin-top: 3px;" type="button" class="btn bg-teal btn-sm btn-icon mr5" onclick=" var vars = $('#frmChangeConta').serialize(); ajaxResposta('Movimentacao', '<?php echo $action_change; ?>', vars, '.main-content'); ">
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
	      				echo "Nenhuma $str_movimentacao cadastrada nesse per&iacute;odo =/";
	      			}
	      			else{
	      				?>
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th>ID</th>
			        				<!--<th>Conta</th>-->
			        				<th>Categoria</th>
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th>Parcela</th>
			        				<th>Gerada Em</th>
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
			        				$competencia = ($movimentacao["competencia"] != "") ? date("d/m/Y", strtotime($movimentacao["competencia"])): "&nbsp;";
			        				$vencimento = ($movimentacao["vencimento"] != "") ? date("d/m/Y", strtotime($movimentacao["vencimento"])): "&nbsp;";
			        				$valor = (is_numeric($movimentacao["valor"])) ? "R$ " . number_format($movimentacao["valor"], 2, ",", "."): "&nbsp;";
			        				$pagamento = ($movimentacao["pagamento"] != "") ? date("d/m/Y", strtotime($movimentacao["pagamento"])): "&nbsp;";
			        				$valor_pago = (is_numeric($movimentacao["valor_pago"])) ? "R$ " . number_format($movimentacao["valor_pago"], 2, ",", "."): "&nbsp;";
			        				
			        				$parcela = $movimentacao["parcela"];
			        				$tot_parcelas = $movimentacao["tot_parcelas"];
			        				$str_parcelas = ($parcela != "" && $tot_parcelas != "") ? "$parcela/$tot_parcelas": "&nbsp;";
			        				$link_parcelas = ($str_parcelas != "") ? "<a data-toggle='tooltip' data-placement='top' data-original-title='Exibir parcelamento' data-id='$id' id='mov_mostra_parcelas' class='link-blue' href='javascript:;'>$str_parcelas</a>": "&nbsp;";
			        				?>
			        				<tr>
			        					<td data-title="ID"><?php echo $id; ?></td>
			        					<!--<td data-title="Conta"><?php echo $conta; ?></td>-->
			        					<td data-title="Categoria"><?php echo $categoria; ?></td>
			        					<td data-title="Descri&ccedil;&atilde;o"><?php echo $descricao; ?></td>
			        					<td data-title="Parcela"><?php echo $link_parcelas; ?></td>
			        					<td data-title="Gerada Em">
			        						<a id="edita-mov-competencia" data-id="<?php echo $id; ?>" data-str-movimentacao='<?php echo $str_movimentacao; ?>' data-toggle='tooltip' data-placement='top' data-original-title='Data da gera&ccedil;&atilde;o da <?php echo $str_movimentacao; ?>' class="link-blue" href="javascript:;">
			        							<?php echo $competencia; ?>
			        						</a>
			        					</td>
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