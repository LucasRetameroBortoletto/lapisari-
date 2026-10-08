<?php
/**
 * Login · BACKEND
 *
 * Confere e-mail e senha no banco e grava na sessão quem entrou.
 * Logo depois de criar a conta, mostra o aviso "Conta criada!" com o
 * e-mail já preenchido.
 *
 * Tela: views/paginas/login.php
 */
require_once __DIR__ . '/../includes/functions.php';

// Quem já está logado não precisa ver o login.
// usuario_logado(): true se há alguém logado; header(): redireciona para a vitrine
if (usuario_logado()) {
    header("Location: " . url('index.php'));
    exit;
}

$erro = '';
$sucesso = '';
$email = '';

// $_SERVER['REQUEST_METHOD'] == "POST": o formulário foi enviado
// (só abrir a página é um GET)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email'] ?? '');            // trim(): tira os espaços das pontas
    $usuario = consultar_user($conexao, $email);     // consultar_user(): busca a conta pelo e-mail (false se não existir)

    // password_verify(): compara a senha digitada com o hash salvo no banco
    if ($usuario && password_verify($_POST['password'] ?? '', $usuario['senha'])) {
        // session_regenerate_id(): troca o id da sessão ao entrar (protege contra roubo de sessão)
        session_regenerate_id(true);

        // A sessão lembra quem entrou: a pessoa continua logada nas outras páginas
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['papel'] = $usuario['papel'];

        header("Location: " . url('index.php') . '#vitrine');
        exit;
    }

    $erro = "Usuário ou senha inválidos!!";
}

// Veio do criar conta (registerUser.php): aviso de sucesso e e-mail já no campo.
// pegar_aviso(): lê o aviso guardado na sessão e já o apaga (aparece uma vez só)
$conta_criada = pegar_aviso('conta_criada');
if ($conta_criada) {
    $sucesso = 'Conta criada! Agora é só entrar.';
    $email = $conta_criada;
}

// Veio do logout automático do admin por inatividade (includes/functions.php)
if (pegar_aviso('sessao_expirada')) {
    $erro = 'Sua sessão expirou por inatividade. Entre de novo.';
}

// mostrar_pagina(): mostra a tela views/paginas/login.php com estes dados
mostrar_pagina('login', [
    'erro'    => $erro,
    'sucesso' => $sucesso,
    'email'   => $email,
]);
