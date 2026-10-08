<?php
/**
 * Criar conta · BACKEND
 *
 * Valida nome, e-mail e senha e grava a conta nova no banco.
 * Toda conta nasce como 'cliente'; para virar admin, troque o papel na tela
 * de Usuários (app/usuarios.php) ou direto no banco.
 *
 * Tela: views/paginas/registrar.php
 */
require_once __DIR__ . '/../includes/functions.php';

// Quem já está logado não precisa criar conta.
// usuario_logado(): true se há alguém logado; header(): redireciona para a vitrine
if (usuario_logado()) {
    header("Location: " . url('index.php'));
    exit;
}

$nome = '';
$email = '';
$erros = [];

// O formulário foi enviado (só abrir a página é um GET)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // preg_replace(): troca espaços repetidos por um só ("Ana   Souza" -> "Ana Souza")
    // trim(): tira os espaços das pontas
    $nome = trim(preg_replace('/\s+/', ' ', $_POST['nome'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';   // ??: se o campo não veio, usa texto vazio

    if ($nome == '') {
        $erros[] = 'Informe o seu nome.';
    } elseif (preg_match_all('/./us', $nome) > 60) {
        // preg_match_all('/./us'): conta as letras, acentos inclusive ("ã" conta 1).
        // (Não usa o mb_strlen() porque ele depende da extensão mbstring, que
        // não vem instalada em todo servidor: sem ela, o cadastro dava erro 500.)
        $erros[] = 'O nome pode ter no máximo 60 caracteres.';
    }
    // filter_var(..., FILTER_VALIDATE_EMAIL): confere se o texto tem formato de e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    // strlen(): tamanho do texto
    if (strlen($senha) < 6) {
        $erros[] = 'A senha precisa ter pelo menos 6 caracteres.';
    }
    // count(): quantos itens a lista tem. Só consulta o banco se o resto estiver certo.
    // consultar_user(): busca a conta pelo e-mail; se achar, o e-mail já está em uso
    if (count($erros) == 0 && consultar_user($conexao, $email)) {
        $erros[] = 'Já existe uma conta com este e-mail.';
    }

    if (count($erros) == 0) {
        // cadastrar_user(): grava a conta nova, com a senha protegida por password_hash()
        cadastrar_user($conexao, $nome, $email, $senha);

        // Vai para o login, que mostra "Conta criada!" com o e-mail já preenchido
        $_SESSION['conta_criada'] = $email;
        header("Location: " . url('login/login.php'));
        exit;
    }
}

// mostrar_pagina(): mostra a tela views/paginas/registrar.php com estes dados
mostrar_pagina('registrar', [
    'nome'  => $nome,
    'email' => $email,
    'erros' => $erros,
]);
