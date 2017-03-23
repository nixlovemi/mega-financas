<?php
$MovimentacaoTipo = $viewModel->get("MovimentacaoTipo");
$arr_movimentacoes = $viewModel->get("arr_movimentacoes");
$arr_totais = $viewModel->get("arr_totais");
$conta_descricao = $viewModel->get("conta_descricao");
$id_conta = $viewModel->get("id_conta");
$arr_contas = $viewModel->get("arr_contas");
$data_inicio = $viewModel->get("data_inicio");
$data_fim = $viewModel->get("data_fim");
$btn_topo_id = $viewModel->get("btn_topo_id");
$btn_topo_text = $viewModel->get("btn_topo_text");
$icon_topo_name = $viewModel->get("icon_topo_name");
$icon_topo_text = $viewModel->get("icon_topo_text");
$tp_movimentacao = $viewModel->get("tp_movimentacao");

// @todo nao sei pq aqui precisou fazer isso
include $_SERVER ['BIRDS_HOME'] . "views/Movimentacao/index.php";