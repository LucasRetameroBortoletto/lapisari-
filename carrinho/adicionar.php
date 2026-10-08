<?php
/**
 * Adicionar ao carrinho · BACKEND (precisa estar logado)
 *
 * Recebe o clique em "Adicionar ao Carrinho" (formulário POST do card na
 * vitrine) e volta para a vitrine. Não tem tela própria.
 * Quem não está logado é levado ao login pelo verifica_user.php.
 */
require_once __DIR__ . '/../login/verifica_user.php';

// O formulário foi enviado (o card manda o id da lapiseira escondido)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) ($_POST['id'] ?? 0);         // (int): transforma em número ("abc" vira 0)
    $lapiseira = consultar($conexao, $id);   // consultar(): busca a lapiseira pelo id (false se não existir)

    // Só entra no carrinho uma lapiseira que existe e está na vitrine
    if ($lapiseira && $lapiseira['ativo']) {
        carrinho_adicionar($conexao, $id);   // carrinho_adicionar(): põe 1 unidade (ou soma 1, se já estava lá)
    }
}

// header(): volta para a vitrine. O contador do carrinho no header já mostra o item novo
header("Location: " . url('index.php') . '#vitrine');
exit;
