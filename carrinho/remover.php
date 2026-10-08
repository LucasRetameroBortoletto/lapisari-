<?php
/**
 * Remover do carrinho · BACKEND (precisa estar logado)
 *
 * Recebe o clique em "Remover" na página do carrinho e volta para ela.
 * Não tem tela própria.
 */
require_once __DIR__ . '/../login/verifica_user.php';

// O formulário foi enviado (o botão "Remover" manda o id da lapiseira escondido)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // carrinho_remover(): tira a lapiseira do carrinho, com todas as unidades.
    // (int): transforma o id em número ("abc" vira 0)
    carrinho_remover($conexao, (int) ($_POST['id'] ?? 0));
}

header("Location: " . url('carrinho/index.php'));   // header(): volta para a página do carrinho
exit;
