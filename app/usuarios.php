<?php
/**
 * Usuários · BACKEND (só admin)
 *
 * Lista todas as contas e troca o papel de cada uma (cliente/admin).
 *
 * Tela: views/paginas/admin/usuarios.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

$mensagem = '';
$erro = '';

// O "Salvar" de alguma linha foi clicado (cada linha da tabela é um formulário)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int) ($_POST['id'] ?? 0);   // (int): transforma em número ("abc" vira 0)
    $papel = $_POST['papel'] ?? '';    // ??: se o campo não veio, usa texto vazio

    if ($papel != 'cliente' && $papel != 'admin') {
        // Só aceita os dois papéis que existem (o banco também recusaria outro)
        $erro = 'Papel inválido.';
    } elseif ($id == $_SESSION['id'] && $papel != 'admin') {
        // O admin não pode tirar o próprio acesso: o sistema poderia ficar sem nenhum admin
        $erro = 'Você não pode remover o seu próprio acesso de administrador.';
    } else {
        atualizar_papel($conexao, $id, $papel);   // atualizar_papel(): grava o papel novo no banco
        $mensagem = 'Papel atualizado com sucesso!';
    }
}

// mostrar_pagina(): mostra a tela views/paginas/admin/usuarios.php com estes dados
mostrar_pagina('admin/usuarios', [
    'usuarios' => listar_usuarios($conexao),   // listar_usuarios(): todas as contas, em ordem de e-mail
    'mensagem' => $mensagem,
    'erro'     => $erro,
    'meu_id'   => $_SESSION['id'],   // para marcar a própria conta com "Você"
]);
