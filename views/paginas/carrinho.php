<?php
/**
 * Tela · Carrinho
 *
 * Backend: carrinho/index.php
 * Recebe:
 *   $itens      itens do carrinho (com 'disponivel' e 'subtotal')
 *   $total      soma dos subtotais
 *   $quantidade unidades disponíveis
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url()              monta o endereço de uma página do projeto
 *   imagem_lapiseira() endereço da foto, ou da imagem padrão quando não há foto
 *   e()                escapa o texto antes de mostrar no HTML
 *   formatar_bitola()  "0.5" -> "0.5 mm"
 *   formatar_preco()   129.9 -> "R$ 129,90"
 */
$titulo = 'Carrinho · Lapisari';
$estilos = ['carrinho.css'];
?>
<main class="pagina">
    <div class="container">
        <div class="pagina__topo">
            <p class="sobretitulo">Sua seleção</p>
            <h1 class="pagina__titulo">Carrinho</h1>
        </div>

        <?php if (!$itens): ?>
            <div class="carrinho-vazio">
                <p>Seu carrinho está vazio.</p>
                <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno">Ver a vitrine</a>
            </div>
        <?php else: ?>
            <!-- Duas colunas: a lista de itens e o resumo do pedido -->
            <div class="carrinho">
                <ul class="carrinho__lista">
                    <?php foreach ($itens as $item): ?>
                        <li class="item-carrinho<?= $item['disponivel'] ? '' : ' item-carrinho--indisponivel' ?>">
                            <img src="<?= imagem_lapiseira($item) ?>" alt="" class="item-carrinho__foto">
                            <div class="item-carrinho__info">
                                <p class="item-carrinho__marca"><?= e($item['marca']) ?></p>
                                <p class="item-carrinho__modelo"><?= e($item['modelo']) ?></p>
                                <p class="item-carrinho__detalhe">
                                    Bitola <?= formatar_bitola($item['bitola']) ?> ·
                                    <?= $item['quantidade'] ?> &times; <?= formatar_preco($item['preco']) ?>
                                </p>
                            </div>
                            <?php if ($item['disponivel']): ?>
                                <p class="item-carrinho__subtotal"><?= formatar_preco($item['subtotal']) ?></p>
                            <?php else: ?>
                                <!-- Saiu da vitrine: continua no carrinho, mas não conta no total -->
                                <p class="selo selo--indisponivel">Fora de estoque</p>
                            <?php endif; ?>
                            <form method="post" action="<?= url('carrinho/remover.php') ?>">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="botao botao--texto botao--pequeno"
                                        aria-label="Remover <?= e($item['modelo']) ?> do carrinho">Remover</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <aside class="resumo" aria-label="Resumo do pedido">
                    <p class="sobretitulo">Resumo</p>
                    <dl class="resumo__linhas">
                        <dt>Itens</dt>
                        <dd><?= $quantidade ?></dd>
                        <dt class="resumo__total">Total</dt>
                        <dd class="resumo__total"><?= formatar_preco($total) ?></dd>
                    </dl>
                    <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno botao--largo">Continuar comprando</a>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
