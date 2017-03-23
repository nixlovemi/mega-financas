<?php
$descricao = $viewModel->get("mov_descricao");
$vencimento = $viewModel->get("mov_dt_vencimento");
$valor = $viewModel->get("mov_valor");
$pagamento = $viewModel->get("mov_dt_pagamento");
$valor_pago = $viewModel->get("mov_valor_pago");
$str_parcela = $viewModel->get("str_parcela");

include("incluir.php");
?>