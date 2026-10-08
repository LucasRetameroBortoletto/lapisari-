<?php
/**
 * FRONT · funções usadas pelas telas
 *
 * Só formatam dados para mostrar no HTML: não acessam o banco nem a sessão.
 * São carregadas pelo includes/functions.php, então toda página já as tem.
 */

/**
 * Escapa um texto antes de mostrar no HTML.
 * Se alguém cadastrar "<script>" como nome, ele aparece como texto e não é executado.
 *
 * @param string|null $texto
 * @return string
 */
function e($texto) {
    // htmlspecialchars(): troca < > & " ' por códigos (&lt; &gt;...) que o navegador
    // mostra como texto. ENT_QUOTES inclui as aspas simples; ?? '' trata o null como texto vazio
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formata um preço em reais. Exemplo: 129.9 -> "R$ 129,90"
 *
 * @param float|string $valor
 * @return string
 */
function formatar_preco($valor) {
    // number_format(valor, casas decimais, separador decimal, separador de milhar)
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata uma bitola em milímetros. Exemplo: "0.5" -> "0.5 mm"
 *
 * @param float|string $bitola
 * @return string
 */
function formatar_bitola($bitola) {
    // Sempre uma casa decimal com ponto: 2 vira "2.0"
    return number_format($bitola, 1, '.', '') . ' mm';
}

/**
 * Escreve a quantidade com a palavra no singular ou no plural.
 * Exemplo: plural(1, 'item', 'itens') -> "1 item"; plural(3, 'item', 'itens') -> "3 itens"
 *
 * @param int    $quantidade
 * @param string $singular
 * @param string $plural
 * @return string
 */
function plural($quantidade, $singular, $plural) {
    // condição ? se verdadeiro : se falso
    return $quantidade . ' ' . ($quantidade == 1 ? $singular : $plural);
}

/**
 * Endereço da foto da lapiseira, ou da imagem padrão quando ela não tem foto.
 *
 * @param array $lapiseira linha da tabela lapiseiras (usa 'imagem')
 * @return string
 */
function imagem_lapiseira($lapiseira) {
    if ($lapiseira['imagem']) {
        return url($lapiseira['imagem']);   // url(): monta o endereço a partir da pasta do projeto
    }
    return url('assets/img/produto-sem-foto.svg');
}

/**
 * Pergunta mostrada antes de excluir uma lapiseira, já escapada para ir no
 * atributo data-confirmar (o site.js mostra a pergunta antes de enviar).
 *
 * @param array $lapiseira linha da tabela lapiseiras (usa 'marca' e 'modelo')
 * @return string
 */
function aviso_exclusao($lapiseira) {
    // e(): as aspas do texto viram &quot; e não quebram o atributo do HTML
    return e('Excluir a lapiseira "' . $lapiseira['marca'] . ' ' . $lapiseira['modelo'] . '"? Essa ação não pode ser desfeita.');
}

/**
 * Escreve um ícone de views/icones/ direto no HTML.
 * Fica no HTML (e não como <img>) para o ícone pegar a cor do texto
 * (stroke="currentColor"). Exemplo: icone('carrinho')
 *
 * @param string $nome nome do arquivo .svg, sem a extensão
 * @return void
 */
function icone($nome) {
    readfile(__DIR__ . '/icones/' . $nome . '.svg');   // readfile(): lê o arquivo e já o escreve na página
}
