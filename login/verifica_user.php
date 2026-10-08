<?php
/**
 * Acesso: só quem está logado
 *
 * Coloque no topo das páginas que exigem login (no lugar do functions.php,
 * que este arquivo já carrega). Quem não está logado vai para o login.
 */
require_once __DIR__ . '/../includes/functions.php';

// usuario_logado(): true se há alguém logado na sessão
if (!usuario_logado()) {
    // header("Location: ..."): manda o navegador abrir outra página (redirecionamento)
    header("Location: " . url('login/login.php'));
    exit;   // sem o exit, o PHP continuaria rodando o resto da página mesmo sem login
}
