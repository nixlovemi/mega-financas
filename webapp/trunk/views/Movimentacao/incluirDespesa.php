<?php
$id_movimentacao = (isset($id_movimentacao)) ? $id_movimentacao: $viewModel->get("id_movimentacao");
$id_form = (isset($id_form)) ? $id_form: $viewModel->get("id_form");
$id_button = (isset($id_button)) ? $id_button: $viewModel->get("id_button");
$titulo_janela = (isset($titulo_janela)) ? $titulo_janela: $viewModel->get("titulo_janela");
$combo_contas = (isset($combo_contas)) ? $combo_contas: $viewModel->get("combo_contas");
$combo_categoria = (isset($combo_categoria)) ? $combo_categoria: $viewModel->get("combo_categoria");
$combo_subcategoria = (isset($combo_subcategoria)) ? $combo_subcategoria: $viewModel->get("combo_subcategoria");

include("incluir.php");
?>