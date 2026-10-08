<?php
/**
 * Atualizar lapiseira · BACKEND (só admin)
 *
 * Sem id: a tela pede o ID da lapiseira.
 * Com id: mostra o formulário preenchido; ao enviar, valida as alterações,
 * troca ou remove a foto se preciso e grava no banco.
 *
 * Tela: views/paginas/admin/atualizar.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

// O id chega pelo campo escondido do formulário (POST) ou pela URL
// (update.php?id=3, vindo do botão de editar).
// ??: usa o primeiro que existir; (int) transforma em número ("abc" vira 0)
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

$lapiseira = consultar($conexao, $id);   // consultar(): busca a lapiseira pelo id (false se não existir)
$erros = [];
$sucesso = false;

// Só grava se a lapiseira existe e o formulário foi enviado
if ($lapiseira && $_SERVER['REQUEST_METHOD'] == "POST") {
    $formulario = ler_formulario_lapiseira();         // ler_formulario_lapiseira(): campos do $_POST (o que faltar vira '')
    $erros = validar_lapiseira($formulario);          // validar_lapiseira(): lista de erros (vazia = tudo certo)

    // Sem foto nova, continua com a foto que já estava cadastrada
    $imagem = $lapiseira['imagem'];

    if (count($erros) == 0) {   // count(): quantos itens a lista tem
        // salvar_imagem(): confere e grava a foto nova; devolve [caminho, mensagem de erro].
        // list(): separa as duas posições em $nova_imagem e $erro_imagem
        list($nova_imagem, $erro_imagem) = salvar_imagem($_FILES['imagem'] ?? null);
        if ($erro_imagem != '') {
            $erros[] = $erro_imagem;
        } elseif ($nova_imagem) {
            // Mandou foto nova: ela substitui a antiga.
            // apagar_imagem(): apaga a foto antiga do disco
            apagar_imagem($lapiseira['imagem']);
            $imagem = $nova_imagem;
        } elseif (isset($_POST['remover_imagem'])) {
            // Marcou "Remover a foto atual" e não mandou outra: a lapiseira
            // fica sem foto (a vitrine mostra a imagem padrão)
            apagar_imagem($lapiseira['imagem']);
            $imagem = null;
        }
    }

    if (count($erros) == 0) {
        // atualizar(): grava as alterações no banco
        atualizar($conexao, $id, $formulario['modelo'], $formulario['marca'], $formulario['bitola'], $formulario['preco'], $imagem, $formulario['ativo']);
        $sucesso = true;
        $lapiseira = consultar($conexao, $id);   // busca de novo para mostrar o que foi salvo
    }
}

// Valores dos campos: o que foi digitado (se deu erro) ou o que está no banco
$valores = [];
if (count($erros) > 0) {
    $valores = $formulario;
} elseif ($lapiseira) {
    $valores = $lapiseira;
    $valores['ativo'] = $lapiseira['ativo'] ? 'true' : 'false';   // o banco devolve true/false; o formulário usa texto
}

// mostrar_pagina(): mostra a tela views/paginas/admin/atualizar.php com estes dados
mostrar_pagina('admin/atualizar', [
    'id'        => $id,
    'lapiseira' => $lapiseira,
    'valores'   => $valores,
    'erros'     => $erros,
    'sucesso'   => $sucesso,
    'bitolas'   => $bitolas,
]);
