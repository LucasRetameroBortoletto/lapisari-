<?php
/**
 * Tela · Página inicial: pilares da marca e vitrine
 *
 * Backend: index.php
 * Recebe:
 *   $lapiseiras lapiseiras a mostrar (já filtradas e ordenadas)
 *   $bitola     bitola filtrada ('' = todas)
 *   $ordem      ordenação escolhida
 *   $bitolas    opções do filtro de bitola
 *
 * Funções usadas:
 *   url()             monta o endereço de uma página do projeto (includes/config.php)
 *   formatar_bitola() "0.5" -> "0.5 mm" (views/helpers.php)
 *   plural()          quantidade + palavra no singular ou no plural: "1 modelo", "5 modelos" (views/helpers.php)
 *   count() (PHP)     quantos itens a lista tem
 */
$titulo = 'Lapisari · Lapiseiras de coleção';
$estilos = ['vitrine.css'];

// Abertura animada: a assinatura escrita à mão que vira o header, e depois o
// texto de abertura. Todo o resto está em JS e CSS (assets/js/splash-inicio.js)
$scripts_inicio = ['splash-inicio.js'];

// Opções do select "Ordenar por": valor enviado na URL => texto na tela
$ordenacoes = [
    'novidades'  => 'Novidades',
    'preco_asc'  => 'Menor preço',
    'preco_desc' => 'Maior preço',
];
?>
<main>
    <!-- Seção conceitual: os três pilares da marca -->
    <section class="conceito" aria-labelledby="conceito-titulo">
        <div class="container">
            <div class="conceito__abertura">
                <p class="sobretitulo">A casa da lapiseira</p>
                <h1 id="conceito-titulo" class="conceito__titulo">Instrumentos de escrita escolhidos como peças de relojoaria.</h1>
                <p class="conceito__texto">
                    Revendemos lapiseiras de marcas oficiais, selecionadas pelo mecanismo,
                    pelo equilíbrio na mão e pela história de cada modelo.
                </p>
            </div>

            <div class="pilares">
                <article class="pilar">
                    <p class="pilar__numero">01</p>
                    <h2 class="pilar__titulo">Curadoria Premium</h2>
                    <p class="pilar__texto">
                        Apenas marcas renomadas e modelos colecionáveis, dos clássicos do desenho
                        técnico às edições limitadas.
                    </p>
                </article>

                <article class="pilar">
                    <p class="pilar__numero">02</p>
                    <h2 class="pilar__titulo">Mecanismo &amp; Precisão</h2>
                    <p class="pilar__texto">
                        Da 0.3 à 2.0 mm, cada bitola é escolhida pela engenharia do mecanismo:
                        avanço do grafite, firmeza da ponta e constância do traço.
                    </p>
                </article>

                <article class="pilar">
                    <p class="pilar__numero">03</p>
                    <h2 class="pilar__titulo">Atendimento Especializado</h2>
                    <p class="pilar__texto">
                        Uma experiência pensada para o entusiasta da escrita, com orientação para
                        escolher a bitola, o peso e o grafite certos.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Vitrine -->
    <section class="vitrine" id="vitrine" aria-labelledby="vitrine-titulo">
        <div class="container">
            <div class="vitrine__topo">
                <div>
                    <p class="sobretitulo">Coleção</p>
                    <h2 id="vitrine-titulo" class="vitrine__titulo">Vitrine</h2>
                </div>

                <!-- GET: o filtro fica na URL (dá para recarregar ou compartilhar).
                     O #vitrine no action faz a página voltar direto para esta seção. -->
                <form class="filtros" method="get" action="<?= url('index.php') ?>#vitrine" data-envio-automatico>
                    <div class="filtros__campo">
                        <label for="filtro-bitola">Bitola</label>
                        <select name="bitola" id="filtro-bitola" class="campo__controle">
                            <option value="">Todas</option>
                            <?php foreach ($bitolas as $opcao): ?>
                                <option value="<?= $opcao ?>" <?= $opcao === $bitola ? 'selected' : '' ?>><?= formatar_bitola($opcao) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filtros__campo">
                        <label for="filtro-ordem">Ordenar por</label>
                        <select name="ordem" id="filtro-ordem" class="campo__controle">
                            <?php foreach ($ordenacoes as $chave => $rotulo): ?>
                                <option value="<?= $chave ?>" <?= $chave === $ordem ? 'selected' : '' ?>><?= $rotulo ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Sem JavaScript, este botão aplica o filtro. Com JS, ele some e
                         o filtro é aplicado assim que um select muda (assets/js/site.js). -->
                    <button type="submit" class="botao botao--contorno botao--pequeno" data-botao-filtrar>Filtrar</button>
                </form>
            </div>

            <p class="vitrine__contagem">
                <?= plural(count($lapiseiras), 'modelo', 'modelos') ?>
                <?= $bitola !== '' ? 'em ' . formatar_bitola($bitola) : '' ?>
            </p>

            <?php if (!$lapiseiras): ?>
                <!-- Vitrine vazia: com filtro, oferece voltar para todas as bitolas -->
                <div class="vitrine__vazia">
                    <p><?= $bitola !== '' ? 'Nenhuma lapiseira nesta bitola por enquanto.' : 'A vitrine ainda está vazia.' ?></p>
                    <?php if ($bitola !== ''): ?>
                        <a href="<?= url('index.php') ?>#vitrine" class="botao botao--contorno botao--pequeno">Ver todas</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="grade-produtos">
                    <?php 
                    
                    //Para cada linha incluida pelo foreach as informações são monstadas com card_produto.php

                    foreach ($lapiseiras as $lapiseira): ?>
                        <?php include __DIR__ . '/../componentes/card_produto.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
