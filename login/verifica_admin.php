<?php
/**
 * Acesso: só o administrador
 *
 * Coloque no topo das páginas do admin (cadastrar, atualizar, excluir,
 * relatório, consulta e usuários). Quem não está logado vai para o login;
 * quem está logado como cliente volta para a vitrine.
 */
require_once __DIR__ . '/verifica_user.php';   // primeiro precisa estar logado

// usuario_admin(): true se o papel de quem está logado é 'admin'
if (!usuario_admin()) {
    header("Location: " . url('index.php'));   // header(): redireciona para a vitrine
    exit;
}

