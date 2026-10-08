<?php
/**
 * Tela · Criar conta
 *
 * Usa o mesmo cartão preto do login (views/layout/cartao_login.php): aqui
 * entram só os erros e o formulário, que vão na coluna da direita.
 *
 * Backend: login/registerUser.php
 * Recebe:
 *   $nome, $email o que foi digitado (continua nos campos se deu erro)
 *   $erros        mensagens de erro da validação
 *
 * Funções usadas:
 *   e() escapa o texto antes de mostrar no HTML (views/helpers.php)
 */
$titulo = 'Criar conta · Lapisari';
$estilos = ['login.css'];

// Coluna da esquerda do cartão
$cartao_login = [
    'titulo'    => 'Criar conta',
    'subtitulo' => 'Para montar a sua seleção de lapiseiras',
    'convite'   => 'Já tem conta?',
    'link'      => 'login/login.php',
    'botao'     => 'Entrar',
];
?>
<?php foreach ($erros as $erro): ?>
    <p class="login__erro" role="alert"><?= e($erro) ?></p>
<?php endforeach; ?>

<form action="" method="post" class="login__formulario">
    <div class="login__campo">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" autocomplete="name" maxlength="60" required
               value="<?= e($nome) ?>">
    </div>

    <div class="login__campo">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" autocomplete="email" maxlength="120" required
               value="<?= e($email) ?>">
    </div>

    <div class="login__campo">
        <label for="password">Senha</label>
        <!-- minlength: o navegador já avisa antes de enviar (o backend confere de novo) -->
        <input type="password" name="password" id="password" autocomplete="new-password"
               minlength="6" required aria-describedby="dica-senha">
        <p class="login__dica" id="dica-senha">Mínimo de 6 caracteres.</p>
    </div>

    <button type="submit" class="login__enviar">Criar conta</button>
</form>
