<?php
/**
 * Tela · Excluir lapiseira
 *
 * Backend: app/delete.php
 * Recebe:
 *   $mensagem resultado da exclusão ('' antes de enviar)
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   e()   escapa o texto antes de mostrar no HTML
 *   url() monta o endereço de uma página do projeto
 */
$titulo = 'Excluir lapiseira · Lapisari';
$estilos = ['forms.css'];
?>
<main class="pagina">
    <div class="container container--estreito">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Excluir lapiseira</h1>
            <p class="pagina__texto">Informe o ID da lapiseira. A exclusão remove o modelo e a foto e não pode ser desfeita.</p>
        </div>

        <!-- data-confirmar: o site.js pergunta "tem certeza?" antes de enviar -->
        <form action="" method="post" class="formulario formulario--em-linha"
              data-confirmar="Excluir a lapiseira com este ID? Essa ação não pode ser desfeita.">
            <div class="campo">
                <label for="id">ID</label>
                <input type="number" name="id" id="id" class="campo__controle" min="1" required>
            </div>
            <input type="reset" value="Limpar" class="botao botao--texto">
            <input type="submit" value="Apagar" class="botao botao--perigo">
        </form>

        <?php if ($mensagem != ''): ?>
            <p class="resultado-vazio"><?= e($mensagem) ?> · <a href="<?= url('index.php') ?>#vitrine">Voltar à vitrine</a></p>
        <?php endif; ?>
    </div>
</main>
