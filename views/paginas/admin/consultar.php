<?php
/**
 * Tela · Consultar lapiseira pelo ID
 *
 * Backend: app/select_where.php
 * Recebe:
 *   $buscou      true depois que o formulário foi enviado
 *   $lapiseira   a lapiseira encontrada, ou false
 *   $id_digitado o ID digitado (para continuar no campo)
 *
 * Funções usadas:
 *   e()                escapa o texto antes de mostrar no HTML (views/helpers.php)
 *   imagem_lapiseira() endereço da foto, ou da imagem padrão quando não há foto (views/helpers.php)
 *   formatar_bitola()  "0.5" -> "0.5 mm" (views/helpers.php)
 *   formatar_preco()   129.9 -> "R$ 129,90" (views/helpers.php)
 *   url()              monta o endereço de uma página do projeto (includes/config.php)
 *   aviso_exclusao()   pergunta "Excluir a lapiseira ...?" mostrada antes de excluir (views/helpers.php)
 *   strtotime() (PHP)  lê a data e hora que vêm do banco ("2026-10-01 14:30:00")
 *   date() (PHP)       escreve a data no formato pedido: 'd/m/Y H:i' -> "01/10/2026 14:30"
 */
$titulo = 'Consultar lapiseira · Lapisari';
$estilos = ['forms.css'];
?>
<main class="pagina">
    <div class="container container--estreito">
        <div class="pagina__topo">
            <p class="sobretitulo">Administração</p>
            <h1 class="pagina__titulo">Consultar lapiseira</h1>
            <p class="pagina__texto">Busque um modelo pelo ID para ver todos os dados cadastrados.</p>
        </div>

        <form action="" method="post" class="formulario formulario--em-linha">
            <div class="campo">
                <label for="id">ID</label>
                <input type="number" name="id" id="id" class="campo__controle" min="1" required
                       value="<?= e($id_digitado) ?>">
            </div>
            <input type="reset" value="Limpar" class="botao botao--texto">
            <input type="submit" value="Consultar" class="botao botao--primario">
        </form>

        <!--buscou relaciona a chamada do POST -->
        <?php if ($buscou && !$lapiseira): ?>
            <p class="resultado-vazio" role="status">Nenhuma lapiseira encontrada com este ID.</p>
        <?php elseif ($lapiseira): ?>
            <!-- Ficha: foto à esquerda, dados à direita -->
            <article class="ficha" aria-label="Resultado da busca">
                <img src="<?= imagem_lapiseira($lapiseira) ?>" alt="<?= e($lapiseira['marca'] . ' ' . $lapiseira['modelo']) ?>" class="ficha__foto">
                <div>
                    <p class="sobretitulo"><?= e($lapiseira['marca']) ?></p>
                    <h2 class="ficha__modelo"><?= e($lapiseira['modelo']) ?></h2>
                    <dl class="ficha__dados">
                        <dt>ID</dt>        <dd><?= $lapiseira['id'] ?></dd>
                        <dt>Bitola</dt>    <dd><?= formatar_bitola($lapiseira['bitola']) ?></dd>
                        <dt>Preço</dt>     <dd><?= formatar_preco($lapiseira['preco']) ?></dd>
                        <dt>Vitrine</dt>   <dd><?= $lapiseira['ativo'] ? 'Disponível' : 'Fora da vitrine' ?></dd>
                        <dt>Cadastro</dt>  <dd><?= date('d/m/Y H:i', strtotime($lapiseira['criado_em'])) ?></dd>
                    </dl>
                    <!-- Atalhos para as telas de atualizar e excluir, já com esta lapiseira -->
                    <div class="ficha__acoes">
                        <a href="<?= url('app/update.php?id=' . $lapiseira['id']) ?>" class="botao botao--contorno botao--pequeno">Editar</a>
                        <!-- data-confirmar: o site.js pede confirmação antes de enviar -->
                        <form method="post" action="<?= url('app/delete.php') ?>" data-confirmar="<?= aviso_exclusao($lapiseira) ?>">
                            <input type="hidden" name="id" value="<?= $lapiseira['id'] ?>">
                            <button type="submit" class="botao botao--perigo botao--pequeno">Excluir</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </div>
</main>
