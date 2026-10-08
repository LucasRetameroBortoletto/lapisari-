<?php
/**
 * Relatório · BACKEND (só admin)
 *
 * Lista todas as lapiseiras, inclusive as que estão fora da vitrine.
 * É só leitura: editar e excluir ficam nas telas Atualizar e Excluir.
 *
 * Tela: views/paginas/admin/relatorio.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

// relatorio(): lista as lapiseiras. Aqui: sem filtro de bitola (''), das mais
// novas para as mais antigas ('novidades') e todas, mesmo fora da vitrine (true)
$lapiseiras = relatorio($conexao, '', 'novidades', true);

// Quantas estão na vitrine: array_column() pega o 'ativo' (true/false) de
// cada lapiseira, array_filter() descarta os false e count() conta o que sobrou
$total_ativas = count(array_filter(array_column($lapiseiras, 'ativo')));

// mostrar_pagina(): mostra a tela views/paginas/admin/relatorio.php com estes dados
mostrar_pagina('admin/relatorio', [
    'lapiseiras'   => $lapiseiras,
    'total_ativas' => $total_ativas,
]);
