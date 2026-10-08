<?php
/**
 * Tela · Cadastrar lapiseira
 *
 * Backend: app/create.php
 * Recebe:
 *   $valores valores dos campos (vazios, ou o que foi digitado se deu erro)
 *   $erros   mensagens de erro da validação
 *   $sucesso true quando a lapiseira acabou de ser cadastrada
 *   $bitolas opções do select de bitola
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url() monta o endereço de uma página do projeto
 *   e()   escapa o texto antes de mostrar no HTML
 */
$titulo = 'Cadastrar lapiseira · Lapisari';
$estilos = ['forms.css'];
?>
<main class="pagina">
    <div class="container container--estreito">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Cadastrar lapiseira</h1>
            <p class="pagina__texto">Preencha os dados do modelo. Se ele estiver disponível, aparece na vitrine assim que for salvo.</p>
        </div>

        <!-- enctype multipart: obrigatório para o formulário conseguir enviar arquivos (a foto) -->
        <form action="" method="post" enctype="multipart/form-data" class="formulario">

            <?php if ($sucesso): ?>
                <p class="alerta alerta--sucesso">
                    Lapiseira cadastrada com sucesso! <a href="<?= url('index.php') ?>#vitrine">Ver na vitrine</a>
                </p>
            <?php endif; ?>

            <?php foreach ($erros as $erro): ?>
                <p class="alerta alerta--erro"><?= e($erro) ?></p>
            <?php endforeach; ?>

           
            <?php
            //nessa "tela" possuio todos os campos de preenchimento. Esta é compartilhada com atualizar e cadastrar # .php
            include __DIR__ . '/../../componentes/form_lapiseira.php'; ?>

            <div class="formulario__acoes">
                <input type="reset" value="Limpar" class="botao botao--texto">
                <input type="submit" value="Cadastrar" class="botao botao--primario">
            </div>
        </form>
    </div>
</main>
