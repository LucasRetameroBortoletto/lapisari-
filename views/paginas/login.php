<?php
/**
 * Tela · Login
 *
 * Fica dentro do cartão preto (views/layout/cartao_login.php): aqui entram
 * só os avisos e o formulário, que vão na coluna da direita.
 *
 * Backend: login/login.php
 * Recebe:
 *   $erro    mensagem de login recusado ('' se não houver)
 *   $sucesso aviso de conta criada ('' se não houver)
 *   $email   e-mail para deixar no campo
 *
 * Funções usadas:
 *   e() escapa o texto antes de mostrar no HTML (views/helpers.php)
 */
$titulo = 'Entrar · Lapisari';
$estilos = ['login.css'];

// Coluna da esquerda do cartão
$cartao_login = [
    'titulo'    => 'Bem-vindo',
    'subtitulo' => 'Faça login para continuar',
    'convite'   => 'Ainda não tem conta?',
    'link'      => 'login/registerUser.php',
    'botao'     => 'Criar conta',
];
?>
<?php if ($sucesso != ''): ?>
    <p class="login__sucesso" role="status"><?= e($sucesso) ?></p>
<?php endif; ?>
<?php if ($erro != ''): ?>
    <p class="login__erro" role="alert"><?= e($erro) ?></p>
<?php endif; ?>

<form action="" method="post" class="login__formulario">
    <div class="login__campo">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" autocomplete="email" required
               value="<?= e($email) ?>">
    </div>

    <div class="login__campo">
        <label for="password">Senha</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required>
    </div>

    <button type="submit" class="login__enviar">Entrar</button>
</form>
