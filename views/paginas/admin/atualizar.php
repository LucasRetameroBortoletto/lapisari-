<?php
/**
 * Tela · Atualizar lapiseira
 *
 * Backend: app/update.php
 * Recebe:
 *   $id        id pedido (0 = nenhum)
 *   $lapiseira a lapiseira encontrada, ou false
 *   $valores   valores dos campos (do banco, ou o que foi digitado se deu erro)
 *   $erros     mensagens de erro da validação
 *   $sucesso   true quando a alteração acabou de ser salva
 *   $bitolas   opções do select de bitola
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url() monta o endereço de uma página do projeto
 *   e()   escapa o texto antes de mostrar no HTML
 */
$titulo = 'Atualizar lapiseira · Lapisari';
$estilos = ['forms.css'];
?>
<main class="pagina">
    <div class="container container--estreito">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Atualizar lapiseira</h1>
        </div>

        <?php if (!$lapiseira): ?>
            <!-- Nenhuma lapiseira escolhida (ou id inexistente): pede o ID.
                 GET: o id vai na URL, igual ao botão "Editar" da tela de consulta. -->
            <form action="" method="get" class="formulario formulario--em-linha">
                <div class="campo">
                    <label for="id">ID da lapiseira</label>
                    <input type="number" name="id" id="id" class="campo__controle" min="1" required>
                </div>
                <input type="submit" value="Buscar" class="botao botao--primario">
            </form>
            <?php if ($id > 0): ?>
                <p class="resultado-vazio">Nenhuma lapiseira encontrada com o ID <?= $id ?>.</p>
            <?php endif; ?>

        <?php else: ?>
            <!-- enctype multipart: obrigatório para o formulário conseguir enviar arquivos (a foto) -->
            <form action="" method="post" enctype="multipart/form-data" class="formulario">
                <!-- O id vai escondido: diz ao backend qual lapiseira está sendo alterada -->
                <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">

                <?php if ($sucesso): ?>
                    <p class="alerta alerta--sucesso">Alteração realizada com sucesso!!</p>
                <?php endif; ?>

                <?php foreach ($erros as $erro): ?>
                    <p class="alerta alerta--erro"><?= e($erro) ?></p>
                <?php endforeach; ?>

                <?php
                    $imagem_atual = $lapiseira['imagem'];   // mostra a foto atual e a opção de removê-la

                    //nessa "tela" possuio todos os campos de preenchimento. Esta é compartilhada com atualizar e cadastrar # .php
                    include __DIR__ . '/../../componentes/form_lapiseira.php';
                ?>

                <div class="formulario__acoes">
                    <a href="<?= url('index.php') ?>#vitrine" class="botao botao--texto">Voltar à vitrine</a>
                    <input type="reset" value="Desfazer alterações" class="botao botao--texto">
                    <input type="submit" value="Atualizar" class="botao botao--primario">
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>
