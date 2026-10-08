<?php
/**
 * Página inicial (vitrine) · BACKEND
 *
 * Lê os filtros da URL e busca as lapiseiras no banco.
 * O admin também vê as que estão fora da vitrine; o cliente, não.
 *
 * Tela: views/paginas/inicio.php
 */
require_once __DIR__ . '/includes/functions.php';

// Os filtros vêm da URL: index.php?bitola=0.5&ordem=preco_asc
// isset(): o filtro veio na URL? in_array(): a bitola está na lista das aceitas?
$bitola = '';
if (isset($_GET['bitola']) && in_array($_GET['bitola'], $bitolas)) {
    $bitola = $_GET['bitola'];   // só aceita uma bitola da lista
}

// ??: sem ordem na URL, usa 'novidades'.
// Qualquer texto serve: a relatorio() só reconhece as ordenações que conhece
$ordem = $_GET['ordem'] ?? 'novidades';

// mostrar_pagina(): mostra a tela views/paginas/inicio.php com estes dados
mostrar_pagina('inicio', [
    // relatorio(): lista as lapiseiras com o filtro e a ordem escolhidos.
    // usuario_admin(): true para o admin, que também vê as que estão fora da vitrine
    'lapiseiras' => relatorio($conexao, $bitola, $ordem, usuario_admin()),
    'bitola'     => $bitola,
    'ordem'      => $ordem,
    'bitolas'    => $bitolas,
]);
