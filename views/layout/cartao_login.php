<?php
/**
 * Cartão preto do login e do criar conta, em duas colunas
 *
 * À esquerda: a marca, o título e o convite para a outra tela.
 * À direita: o conteúdo da tela (avisos e formulário).
 *
 * Incluído pela moldura (views/layout/pagina.php). Recebe:
 *   $conteudo     HTML da tela, que vai na coluna da direita
 *   $cartao_login textos da coluna da esquerda, definidos pela tela:
 *                   'titulo'    título grande ("Bem-vindo")
 *                   'subtitulo' frase abaixo do título
 *                   'convite'   pergunta acima do botão ("Ainda não tem conta?")
 *                   'link'      página do botão (ex.: 'login/registerUser.php')
 *                   'botao'     texto do botão ("Criar conta")
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url()   monta o endereço de uma página do projeto
 *   e()     escapa o texto antes de mostrar no HTML
 *   icone() escreve um ícone SVG de views/icones/ (a seta de "Voltar à loja")
 */
?>
<main class="login">
    <div class="login__cartao">
        <div class="login__apresentacao">
            <a href="<?= url('index.php') ?>" class="login__marca" aria-label="Lapisari, página inicial">
                <?php
                    $classe_assinatura = 'login__assinatura';
                    include __DIR__ . '/../componentes/assinatura.php';
                ?>
            </a>
            <h1 class="login__titulo"><?= e($cartao_login['titulo']) ?></h1>
            <p class="login__subtitulo"><?= e($cartao_login['subtitulo']) ?></p>

            <footer class="login__rodape">
                <p class="login__convite"><?= e($cartao_login['convite']) ?></p>
                <a href="<?= url($cartao_login['link']) ?>" class="login__botao-secundario"><?= e($cartao_login['botao']) ?></a>
                <a href="<?= url('index.php') ?>" class="login__voltar">
                    <?php icone('voltar'); ?>
                    Voltar à loja
                </a>
            </footer>
        </div>

        <div class="login__area-formulario">
            <?= $conteudo ?>
        </div>
    </div>
</main>
