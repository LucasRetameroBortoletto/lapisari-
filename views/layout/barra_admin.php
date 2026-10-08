<?php
/**
 * Barra do administrador, logo abaixo do header
 *
 * Incluída pelo header.php só quando o usuário é admin. Usa:
 *   $pagina nome da tela aberta ('relatorio', 'usuarios'...): o link dela fica destacado
 *
 * Funções usadas (views/helpers.php e includes/config.php):
 *   url()   monta o endereço de uma página do projeto
 *   icone() escreve um ícone SVG de views/icones/ (a seta de "Vitrine")
 */

// Links da barra: nome da tela => [endereço, texto do link]
$links_admin = [
    'relatorio' => ['app/select.php', 'Relatório'],
    'consultar' => ['app/select_where.php', 'Consultar'],
    'atualizar' => ['app/update.php', 'Atualizar'],
    'excluir'   => ['app/delete.php', 'Excluir'],
    'usuarios'  => ['app/usuarios.php', 'Usuários'],
];
?>
<div class="barra-admin">
    <div class="container barra-admin__conteudo">
        <!-- Volta para a vitrine da página inicial (#vitrine desce direto até ela) -->
        <a href="<?= url('index.php') ?>#vitrine" class="barra-admin__voltar">
            <?php icone('voltar'); ?>
            Vitrine
        </a>

        <span class="barra-admin__rotulo">Administração</span>

        <nav class="barra-admin__links" aria-label="Administração">
            <?php foreach ($links_admin as $chave => $link): ?>
                <!-- aria-current="page" marca o link da tela aberta (o CSS o destaca) -->
                <a href="<?= url($link[0]) ?>" <?= $pagina == $chave ? 'aria-current="page"' : '' ?>><?= $link[1] ?></a>
            <?php endforeach; ?>
        </nav>

        <a href="<?= url('app/create.php') ?>" class="botao botao--primario botao--pequeno"
           <?= $pagina == 'cadastrar' ? 'aria-current="page"' : '' ?>>+ Cadastrar Nova Lapiseira</a>
    </div>
</div>
