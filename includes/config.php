<?php
/**
 * Configurações gerais do site
 *
 * Carregado pelo includes/functions.php, então toda página que inclui o
 * functions.php já tem o BASE_URL, a função url() e a sessão iniciada.
 */

// Pasta do projeto dentro do servidor (a parte da URL depois do localhost):
//   php -S localhost:8000 rodado dentro desta pasta -> ''
//   Apache em http://localhost/lapisari-simples/  -> '/lapisari-simples'
// define(): cria uma constante, um valor fixo que vale no projeto inteiro
define('BASE_URL', '');

function url($caminho) {
    // str_replace(procura, troca, texto): troca todo ' ' por '%20'
    return str_replace(' ', '%20', BASE_URL . '/' . $caminho);
}

// A sessão guarda quem está logado. Ela precisa começar antes de qualquer
// HTML ser enviado, por isso é iniciada aqui, no começo de toda página.
// session_status(): diz se a sessão já começou; session_start(): começa (ou retoma) a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
