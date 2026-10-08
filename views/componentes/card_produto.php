<?php
/**
 * Card de uma lapiseira na vitrine (views/paginas/inicio.php)
 *
 * Antes do include, defina:
 *   $lapiseira a lapiseira do card
 *
 * O card é igual para todos: editar e excluir ficam só nas telas do admin
 * (Atualizar, Excluir e Consultar), pela barra de administração.
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   imagem_lapiseira() endereço da foto, ou da imagem padrão quando não há foto
 *   e()                escapa o texto antes de mostrar no HTML
 *   url()              monta o endereço de uma página do projeto
 *   formatar_bitola()  "0.5" -> "0.5 mm"
 *   formatar_preco()   129.9 -> "R$ 129,90"
 */
?>
<article class="card-produto<?= $lapiseira['ativo'] ? '' : ' card-produto--inativo' ?>">
    <div class="card-produto__foto">
        <img src="<?= imagem_lapiseira($lapiseira) ?>"
             alt="<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>" loading="lazy">

        <?php if (!$lapiseira['ativo']): ?>
            <span class="selo">Fora da vitrine</span>
        <?php endif; ?>
    </div>

    <div class="card-produto__info">
        <p class="card-produto__marca"><?= e($lapiseira['marca']) ?></p>
        <h3 class="card-produto__modelo"><?= e($lapiseira['modelo']) ?></h3>
        <p class="card-produto__bitola">Bitola <?= formatar_bitola($lapiseira['bitola']) ?></p>
        <p class="card-produto__preco"><?= formatar_preco($lapiseira['preco']) ?></p>
    </div>

    <!-- Quem não está logado é levado ao login pelo carrinho/adicionar.php.
         Fora da vitrine (só o admin vê esses cards): botão desativado. -->
    <form method="post" action="<?= url('carrinho/adicionar.php') ?>" class="card-produto__acao">
        <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
        <button type="submit" class="botao botao--contorno botao--largo"
                <?= $lapiseira['ativo'] ? '' : 'disabled' ?>>Adicionar ao Carrinho</button>
    </form>
</article>
