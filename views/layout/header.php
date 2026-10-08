<?php
/**
 * Header fixo do site (marca, desenho da lapiseira, conta e carrinho)
 *
 * Incluído pela moldura (views/layout/pagina.php). Usa os dados que o
 * mostrar_pagina() prepara para todas as páginas:
 *   $logado, $admin      quem está vendo a página
 *   $nome_usuario        primeiro nome de quem está logado ("Olá, Ana")
 *   $quantidade_carrinho número mostrado no botão do carrinho
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url()    monta o endereço de uma página do projeto
 *   e()      escapa o texto antes de mostrar no HTML
 *   plural() quantidade + palavra no singular ou no plural ("1 item", "3 itens")
 *   icone()  escreve um ícone SVG de views/icones/ (o carrinho)
 */
?>
<header class="cabecalho">
    <a href="<?= url('index.php') ?>" class="cabecalho__marca" aria-label="Lapisari, página inicial">
        <?php
            $classe_assinatura = 'cabecalho__assinatura';
            include __DIR__ . '/../componentes/assinatura.php';
        ?>
    </a>

    <?php
        $classe_lapiseira = 'cabecalho__lapiseira';
        include __DIR__ . '/../componentes/lapiseira.php';
    ?>

    <nav class="cabecalho__acoes" aria-label="Conta e carrinho">
        <?php if ($logado): ?>
            <span class="cabecalho__ola">Olá, <?= e($nome_usuario) ?></span>
            <a href="<?= url('login/logout.php') ?>" class="botao-cabecalho">Sair</a>
        <?php else: ?>
            <a href="<?= url('login/login.php') ?>" class="botao-cabecalho botao-cabecalho--contorno">Entrar</a>
        <?php endif; ?>

        <a href="<?= url('carrinho/index.php') ?>" class="botao-cabecalho"
           aria-label="Carrinho, <?= plural($quantidade_carrinho, 'item', 'itens') ?>">
            <?php icone('carrinho'); ?>
            <span>Carrinho</span>
            <!-- Com 0 itens o contador fica apagado: só o contorno, sem fundo (contador--vazio) -->
            <span class="contador<?= $quantidade_carrinho === 0 ? ' contador--vazio' : '' ?>" aria-hidden="true"><?= $quantidade_carrinho ?></span>
        </a>
    </nav>
</header>

<?php if ($admin) include __DIR__ . '/barra_admin.php'; ?>
