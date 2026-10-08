<?php
/**
 * Cadastrar lapiseira · BACKEND (só admin)
 *
 * Valida o formulário, salva a foto e grava a lapiseira no banco.
 * Deu certo: o formulário volta vazio, pronto para o próximo cadastro.
 * Deu erro: o formulário volta com o que foi digitado e a lista de erros.
 *
 * Tela: views/paginas/admin/cadastrar.php
 */
require_once __DIR__ . '/../login/verifica_admin.php';

// Formulário vazio, com "Sim" já marcado em "Disponível na vitrine"
$em_branco = ['modelo' => '', 'marca' => '', 'bitola' => '', 'preco' => '', 'ativo' => 'true'];

$valores = $em_branco;
$erros = [];
$sucesso = false;

// O formulário foi enviado (só abrir a página é um GET)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // ler_formulario_lapiseira(): pega os campos do $_POST (o que faltar vira '').
    // Ficam nos campos da tela caso dê erro
    $valores = ler_formulario_lapiseira();
    $erros = validar_lapiseira($valores);   // validar_lapiseira(): lista de erros (vazia = tudo certo)

    // A foto só é salva se os outros campos estiverem certos:
    // assim não sobra no disco a foto de um cadastro recusado
    $imagem = null;
    if (count($erros) == 0) {   // count(): quantos itens a lista tem
        // salvar_imagem(): confere e grava a foto; devolve [caminho, mensagem de erro].
        // list(): separa as duas posições em $imagem e $erro_imagem
        list($imagem, $erro_imagem) = salvar_imagem($_FILES['imagem'] ?? null);
        if ($erro_imagem != '') {
            $erros[] = $erro_imagem;   // $erros[] = ... acrescenta no fim da lista
        }
    }

    if ($valores['preco'] >= 99999999) {
        $erros[] = "Não é possivel adicionar com esse valor";        
    }

    if(strlen($valores['marca']) > 45) {
        $erros[] = "Não é possivel registrar marca/modelo com mais de 50 caracteres";
    } else if (strlen($valores['modelo']) > 45) {
        $erros[] = "Não é possivel registrar marca/modelo com mais de 50 caracteres";
    }

    if (count($erros) == 0) {
        // cadastrar(): grava a lapiseira nova no banco
        cadastrar($conexao, $valores['modelo'], $valores['marca'], $valores['bitola'], $valores['preco'], $imagem, $valores['ativo']);
        $sucesso = true;
        $valores = $em_branco;   // limpa o formulário para o próximo cadastro
    }
}

// mostrar_pagina(): mostra a tela views/paginas/admin/cadastrar.php com estes dados -- é como a ponte entre o backend e frontend diretamente daqui
mostrar_pagina('admin/cadastrar', [
    'valores' => $valores,
    'erros'   => $erros,
    'sucesso' => $sucesso,
    'bitolas' => $bitolas,
]);
