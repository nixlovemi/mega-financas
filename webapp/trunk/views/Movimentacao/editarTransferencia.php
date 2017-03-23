<?php
$id_form = $viewModel->get("id_form");
$id_button = $viewModel->get("id_button");
$id_movimentacao = $viewModel->get("id_movimentacao");
$titulo_janela = $viewModel->get("titulo_janela");
$combo_contas_orig = $viewModel->get("combo_contas_orig");
$combo_contas_dest = $viewModel->get("combo_contas_dest");
$vencimento = $viewModel->get("vencimento");
$valor = $viewModel->get("valor");

include("incluirTransferencia.php");
?>