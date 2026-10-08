<?php
/**
 * Sair · BACKEND
 *
 * Apaga a sessão (a pessoa deixa de estar logada) e volta para a vitrine.
 * O carrinho continua salvo no banco para a próxima vez.
 * Não tem tela própria.
 */
require_once __DIR__ . '/../includes/functions.php';

$_SESSION = [];      // esvazia os dados da sessão (id, papel, nome)
session_destroy();   // session_destroy(): apaga a sessão no servidor

header("Location: " . url('index.php'));   // header(): redireciona para a vitrine
exit;
