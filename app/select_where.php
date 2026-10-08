<?php
/**
 * Consultar lapiseira pelo ID · BACKEND (só admin)
 *
 * Busca uma lapiseira e mostra a ficha com todos os dados cadastrados.
 *
 * Tela: views/paginas/admin/consultar.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

$buscou = false;      // a tela só diz "nenhuma encontrada" depois de uma busca
$lapiseira = false;

// O formulário de busca foi enviado
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $buscou = true;
    // consultar(): busca a lapiseira pelo id (false se não existir).
    // (int) transforma o id em número ("abc" vira 0)
    $lapiseira = consultar($conexao, (int) ($_POST['id'] ?? 0));
}

// mostrar_pagina(): mostra a tela views/paginas/admin/consultar.php com estes dados
mostrar_pagina('admin/consultar', [
    'buscou'      => $buscou,
    'lapiseira'   => $lapiseira,
    'id_digitado' => $_POST['id'] ?? '',   // continua no campo depois da busca
]);
