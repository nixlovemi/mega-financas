<?php
$id = $viewModel->get("id_movimentacao");
$titulo_janela = $viewModel->get("titulo_janela");
$descricao = $viewModel->get("descricao");
$cat_tipo = $viewModel->get("cat_tipo");
$id_button = $viewModel->get("id_button");
$html_subcat = $viewModel->get("html_subcat");

$html_subcat = '
	<div class="form-group">
		<div class="panel panel-primary">
		  <div style="padding-left: 0; padding-right: 0; text-align: center;" class="panel-heading border"> Subcategorias </div>
		  <div style="padding-left: 0; padding-right: 0;" class="panel-body">
		    <div class="scrollable ps-container ps-active-y" style="max-height: 130px">
		      
		      <table width="100%">
		        <tr>
		          <td>
		          	<div class="input-group mb15">
		          		<input id="subcat-name" placeholder="Subcategoria" class="form-control br0" />
						<span class="input-group-btn"> <button data-id="'.$id.'" class="btn btn-info" id="btn-insere-subcategorias" type="button">Adicionar</button> </span>
		          	</div>
		          </td>
		        </tr>
				<tr>
					<td>
						<div id="dv-tb-subcat">'.$html_subcat.'</div>
					</td>
				</tr>
		      </table>
		      
		      <div style="left: 0px; bottom: 3px;" class="ps-scrollbar-x-rail">
		        <div style="left: 0px; width: 0px;" class="ps-scrollbar-x"></div>
		      </div>
		      <div style="top: 0px; height: 130px; right: 0px;" class="ps-scrollbar-y-rail">
		        <div style="top: 0px; height: 19px;" class="ps-scrollbar-y"></div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
';

include("incluir.php");
?>
