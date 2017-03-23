<?php
$erro_msg = $viewModel->get("erro_msg");
$arr_contas = $viewModel->get("arr_contas");
?>

<div id="dv-holder-nova-conta">
	<a href="javascript:;" class="btn btn-info btn-lg mr5 mb10" id="btn-nova-conta">NOVA CONTA</a>
	<div class="panel mb25">
	  <div class="panel-heading border">
	    <i class="fa fa-university"></i>&nbsp;Minhas Contas
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
	      			if( count($arr_contas) == 0 ){
	      				echo "Nenhuma conta cadastrada =/";
	      			}
	      			else{
	      				?>
	      				<table class="col-md-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				<th></th>
			        				<th>Descri&ccedil;&atilde;o</th>
			        				<th class="numeric">Saldo Inicial</th>
			        				<th class="numeric">Saldo Atual</th>
			        				<th></th>
			        				<th></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<?php
			        			foreach($arr_contas as $conta){
			        				$id = $conta["con_id"];
			        				$cor = $conta["con_cor"];
			        				$descricao = $conta["con_nome"];
			        				$saldo_inicial = number_format($conta["con_saldo_inicial"], 2, ",", ".");
			        				?>
			        				<tr>
				        				<td data-title="" align="center"><span class="circle" style="background-color: #<?php echo $cor; ?>;"></span></td>
				        				<td data-title="Descri&ccedil;&atilde;o"><?php echo $descricao; ?></td>
				        				<td data-title="Saldo Inicial" class="numeric">R$ <?php echo $saldo_inicial; ?></td>
				        				<td data-title="Saldo Atual" class="numeric"> (fazer) </td>
				        				<td data-title="" align="center">
				        					<a id="lnk-edit-conta" href="javascript:void(0)" data-id="<?php echo $id; ?>">
				        						<i style="font-size:18px;" class="fa fa-pencil-square-o"></i>
				        					</a>
				        				</td>
				        				<td data-title="" align="center">
				        					<a id="lnk-delete-conta" href="javascript:void(0)" data-id="<?php echo $id; ?>">
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