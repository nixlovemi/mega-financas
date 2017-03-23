<?php
$erro_msg = $viewModel->get("erro_msg");
$arr_cartoes = $viewModel->get("arr_CartaoCreditos");
?>

<div id="dv-holder-novo-cartao-credito">
	<a href="javascript:;" class="btn btn-info btn-lg mr5 mb10" id="btn-novo-cartao">NOVO CART&Atilde;O</a>
	<div class="panel mb25">
	  <div class="panel-heading border">
	    <i class="fa fa-university"></i>&nbsp;Meus Cart&otilde;es
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
	      			if( count($arr_cartoes) == 0 ){
	      				echo "Nenhum cart&atilde;o cadastrado =/";
	      			}
	      			else{
	      				?>
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th></th>
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th class="numeric">Limite</th>
			        				<th class="numeric">Dia Fechamento</th>
			        				<th class="numeric">Dia Pagamento</th>
			        				<th class="numeric">Pr&oacute;x. Fatura</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_cartoes as $cartao){
			        				$id = $cartao["cc_id"];
			        				$descricao = $cartao["cc_descricao"];
			        				$mini_imagem = $cartao["bc_mini_imagem"];
			        				$limite = number_format($cartao["cc_limite"], 2, ",", ".");
			        				$dia_fechamento = $cartao["cc_dia_fechamento"];
			        				$dia_pagamento = $cartao["cc_dia_pagamento"];
			        				?>
			        				
			        				<tr>
				        				<td data-title="" align="center">
				        					<img width="50" src="<?php echo $_SERVER['BIRDS_HOME_URL'] . "/$mini_imagem"; ?>" />
				        				</td>
				        				<td data-title="Descri&ccedil;&atilde;o"><?php echo $descricao; ?></td>
				        				<td data-title="Limite" class="numeric">R$ <?php echo $limite; ?></td>
				        				<td data-title="Dia Fechamento" class="numeric"><?php echo $dia_fechamento; ?></td>
				        				<td data-title="Dia Pagamento" class="numeric"><?php echo $dia_pagamento; ?></td>
				        				<td data-title="Pr&oacute;x. Fatura" class="numeric"> (fazer) </td>
				        				
				        				<td data-title="" align="center">
				        					<a id="lnk-edit-cartao" href="javascript:void(0)" data-id="<?php echo $id; ?>">
				        						<i style="font-size:18px;" class="fa fa-pencil-square-o"></i>
				        					</a>
				        				</td>
				        				<td data-title="" align="center">
				        					<a id="lnk-delete-cartao" href="javascript:void(0)" data-id="<?php echo $id; ?>">
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