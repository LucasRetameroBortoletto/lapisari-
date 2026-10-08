<?php
/**
 * Página do carrinho · BACKEND
 *
 * Busca os itens do carrinho no banco e soma o total.
 * Quem não está logado vê o carrinho vazio.
 *
 * Tela: views/paginas/carrinho.php
 */
require_once __DIR__ . '/../includes/functions.php';

// carrinho_itens(): itens do carrinho com os dados de cada lapiseira + quantidade + subtotal
$itens = carrinho_itens($conexao);

// Total do pedido: array_column() pega o 'subtotal' de cada item e array_sum() soma tudo
$total = array_sum(array_column($itens, 'subtotal'));

// mostrar_pagina(): mostra a tela views/paginas/carrinho.php com estes dados
mostrar_pagina('carrinho', [
    'itens'      => $itens,
    'total'      => $total,
    'quantidade' => carrinho_quantidade($conexao),   // carrinho_quantidade(): unidades disponíveis no carrinho
]);
