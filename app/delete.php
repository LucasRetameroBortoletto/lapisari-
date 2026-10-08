<?php
/**
 * Excluir lapiseira · BACKEND (só admin)
 *
 * Apaga a lapiseira do banco e a foto do disco.
 * Também recebe a exclusão feita pelo botão "Excluir" da tela de consulta.
 *
 * Tela: views/paginas/admin/excluir.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

$mensagem = '';

// A exclusão só acontece por POST (formulário), nunca por um link:
// um link pode ser aberto sem querer (ou por outro site) e apagar algo
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) ($_POST['id'] ?? 0);         // (int) transforma em número: "abc" vira 0, garantindo sempre o envio de um número
    $lapiseira = consultar($conexao, $id);   // consultar(): busca a lapiseira pelo id (false se não existir)

    if ($lapiseira) {
        deletar($conexao, $id);                // deletar(): apaga a lapiseira do banco
        apagar_imagem($lapiseira['imagem']);   // apagar_imagem(): apaga a foto do disco, que não é mais usada
        $mensagem = "Registro apagado com sucesso";
    } else {
        $mensagem = "Nenhuma lapiseira encontrada com o ID $id.";
    }
}

// mostrar_pagina(): mostra a tela views/paginas/admin/excluir.php com estes dados
mostrar_pagina('admin/excluir', [
    'mensagem' => $mensagem,
]);
