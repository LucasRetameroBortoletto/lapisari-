<?php
/**
 * Moldura de todas as páginas: <head>, header, conteúdo da tela e footer
 *
 * Incluída pelo mostrar_pagina() (includes/functions.php) depois que a tela
 * já montou o seu HTML. Recebe:
 *   $conteudo     HTML da tela (views/paginas/...)
 *   $titulo       texto da aba do navegador, definido pela tela
 *   $estilos      CSS extras de assets/css/, definidos pela tela (ex.: ['vitrine.css'])
 *   $scripts_inicio JS de assets/js/ que roda no <head>, antes de a página aparecer,
 *                 definido pela tela (ex.: ['splash-inicio.js'] na página inicial)
 *   $cartao_login só no login e no criar conta: a página não tem header nem
 *                 footer, só o cartão preto (views/layout/cartao_login.php)
 *
 * Funções usadas:
 *   e()           escapa o texto antes de mostrar no HTML (views/helpers.php)
 *   url()         monta o endereço de um arquivo do projeto (includes/config.php)
 *   isset() (PHP) true se a variável existe: só o login e o criar conta definem $cartao_login
 */
$estilos = $estilos ?? [];   // ??: tela sem CSS extra fica com a lista vazia
$scripts_inicio = $scripts_inicio ?? [];   // ??: tela sem script próprio fica com a lista vazia
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Lapisari') ?></title>

    <!-- Fontes: Cormorant Garamond (títulos), Manrope (textos) e Mrs Saint Delafield (rodapé) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600&family=Mrs+Saint+Delafield&display=swap">

    <!-- base.css e layout.css valem para todas as páginas; os outros, só para quem pediu -->
    <link rel="stylesheet" href="<?= url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/layout.css') ?>">
    <?php foreach ($estilos as $arquivo_css): ?>
    <link rel="stylesheet" href="<?= url('assets/css/' . $arquivo_css) ?>">
    <?php endforeach; ?>

    <!-- Scripts da própria tela, sem "defer": rodam antes de a página ser
         desenhada (a página inicial decide aqui se mostra a abertura animada) -->
    <?php foreach ($scripts_inicio as $arquivo_js): ?>
    <script src="<?= url('assets/js/' . $arquivo_js) ?>"></script>
    <?php endforeach; ?>

    <!-- Um único script, pequeno e sem bibliotecas: confirmação antes de
         excluir, filtro automático da vitrine e prévia da foto -->
    <script src="<?= url('assets/js/site.js') ?>" defer></script>
</head>
<?php if (isset($cartao_login)): ?>
<body class="pagina-login">
    <?php include __DIR__ . '/cartao_login.php'; ?>
</body>
<?php else: ?>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <?= $conteudo ?>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
<?php endif; ?>
</html>
