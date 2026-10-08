<?php
/**
 * BACKEND · regras de negócio e banco de dados
 *
 * Toda página começa carregando este arquivo (direto ou pelos
 * login/verifica_*.php). Ele traz a sessão, a conexão com o banco e as
 * funções do site, nesta ordem:
 *
 *   1. Quem está usando o site (login, papel, avisos da sessão)
 *   2. CRUD de lapiseiras
 *   3. Upload da foto
 *   4. Carrinho
 *   5. Usuários (login e cadastro)
 *   6. mostrar_pagina(): a ponte entre o backend e as telas
 *
 * O HTML fica separado, na pasta views/ (o front).
 */
// require_once: carrega o arquivo uma vez só, mesmo que ele seja pedido de novo
require_once __DIR__ . '/config.php';                       // BASE_URL, url() e sessão
require_once __DIR__ . '/../database/connect_postgres.php'; // cria a $conexao com o banco
require_once __DIR__ . '/../views/helpers.php';             // formatação para as telas: e(), formatar_preco()...

// Bitolas aceitas, em mm (as mesmas do CHECK da tabela lapiseiras)
$bitolas = ['0.3', '0.5', '0.7', '0.9', '1.3', '2.0'];


// =====================================================================
// 1. Quem está usando o site
// =====================================================================

/**
 * Diz se há alguém logado (o login grava o id do usuário na sessão).
 *
 * @return bool
 */
function usuario_logado() {
    // isset(): true se a variável existe e não é null
    return isset($_SESSION['id']);
}

/**
 * Diz se quem está logado é administrador.
 *
 * O trim/strtolower aceita "Admin" ou "admin   " (com espaços sobrando),
 * caso o papel tenha sido gravado assim direto no banco.
 *
 * @return bool
 */
function usuario_admin() {
    // trim(): tira os espaços das pontas; strtolower(): deixa tudo minúsculo
    return isset($_SESSION['papel']) && strtolower(trim($_SESSION['papel'])) == 'admin';
}

// O papel (admin/cliente) e o nome são conferidos no banco a cada página aberta:
// se o papel for trocado direto no banco, a mudança vale na hora, sem sair e entrar.
if (usuario_logado()) {
    // prepare(): manda o SQL com o marcador :id para o banco preparar
    // execute(): roda o SQL, trocando :id pelo valor
    // fetch():   devolve a linha encontrada como array ['papel' => ..., 'nome' => ...], ou false
    $stmt = $conexao->prepare("SELECT papel, nome, email FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['id']]);
    $usuario_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_atual) {
        $_SESSION['papel'] = $usuario_atual['papel'];
        $_SESSION['nome'] = primeiro_nome($usuario_atual);   // primeiro_nome(): "ana souza" vira "Ana" ("Olá, Ana" no header)
    } else {
        // A conta foi apagada do banco: desloga.
        // unset(): apaga as variáveis da sessão
        unset($_SESSION['id'], $_SESSION['papel'], $_SESSION['nome'], $_SESSION['ultima_acao']);
    }
}

// Logout automático do admin depois de um tempo sem abrir nenhuma página.
// "Inatividade" aqui é o tempo entre uma página e a próxima: o PHP só fica
// sabendo de algo quando o navegador pede uma página (ler ou preencher um
// formulário sem enviar não conta como ação).
$admin_inativo_segundos = 60;   // para usar de verdade, algo como 15 * 60 (15 minutos)

if (usuario_admin()) {
    // time(): a hora atual, em segundos. Passou do limite desde a última página?
    if (isset($_SESSION['ultima_acao']) && time() - $_SESSION['ultima_acao'] > $admin_inativo_segundos) {
        // Sai aqui mesmo, e não redirecionando para o logout.php: ele também
        // carrega este arquivo, e o admin seria mandado para lá de novo, sem fim.
        unset($_SESSION['id'], $_SESSION['papel'], $_SESSION['nome'], $_SESSION['ultima_acao']);
        session_regenerate_id(true);           // id de sessão novo, como no login
        $_SESSION['sessao_expirada'] = true;   // aviso para a tela de login (login/login.php)
        header("Location: " . url('login/login.php'));
        exit;
    }
    $_SESSION['ultima_acao'] = time();   // esta página conta como uma ação
}

/**
 * Lê um aviso deixado na sessão por outra página e já o apaga, para que ele
 * apareça uma vez só (ex.: "Conta criada!" na tela de login).
 *
 * @param string $chave nome do aviso na sessão
 * @return mixed o valor guardado, ou null se não houver aviso
 */
function pegar_aviso($chave) {
    $valor = $_SESSION[$chave] ?? null;   // ??: se o aviso não existir, usa null
    unset($_SESSION[$chave]);             // unset(): apaga o aviso, para não aparecer de novo
    return $valor;
}


// =====================================================================
// 2. CRUD de lapiseiras
// =====================================================================
// Todas as consultas usam prepared statements: o SQL vai para o banco com
// marcadores (:id, :modelo...) e os valores seguem separados no execute().
// Assim, nada do que o usuário digita é lido como comando SQL.
//
//   $conexao->prepare($sql) prepara o SQL com os marcadores
//   $stmt->execute([...])   roda o SQL, trocando cada marcador pelo seu valor
//   $stmt->fetch()          devolve uma linha do resultado (ou false)
//   $stmt->fetchAll()       devolve todas as linhas do resultado (lista)
//   PDO::FETCH_ASSOC        cada linha vem como array com o nome das colunas

/**
 * Grava uma lapiseira nova.
 *
 * @param PDO         $conexao
 * @param string      $modelo
 * @param string      $marca
 * @param string      $bitola uma das $bitolas ('0.5'...)
 * @param string      $preco  número com ponto ('129.90')
 * @param string|null $imagem caminho da foto em uploads/lapiseiras/, ou null (sem foto)
 * @param string      $ativo  'true' ou 'false', vindo do formulário
 * @return void
 */
function cadastrar($conexao, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {
    $sql = "INSERT INTO lapiseiras (modelo, marca, bitola, preco, imagem, ativo)
            VALUES (:modelo, :marca, :bitola, :preco, :imagem, :ativo)";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':modelo' => $modelo, ':marca' => $marca, ':bitola' => $bitola,
                    ':preco' => $preco, ':imagem' => $imagem, ':ativo' => $ativo]);
}

/**
 * Apaga uma lapiseira. A foto no disco é apagada à parte, com apagar_imagem().
 *
 * @param PDO $conexao
 * @param int $id
 * @return void
 */
function deletar($conexao, $id) {
    $stmt = $conexao->prepare("DELETE FROM lapiseiras WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

/**
 * Busca uma lapiseira pelo id.
 *
 * @param PDO $conexao
 * @param int $id
 * @return array|false os dados da lapiseira, ou false se o id não existir
 */
function consultar($conexao, $id) {
    $stmt = $conexao->prepare("SELECT * FROM lapiseiras WHERE id = :id");
    $stmt->execute([':id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);   // uma linha só: o id não se repete
}

/**
 * Lista as lapiseiras, com filtro de bitola e ordenação.
 *
 * @param PDO    $conexao
 * @param string $bitola '' para todas, ou uma bitola ('0.5'...) para filtrar
 * @param string $ordem  'novidades', 'preco_asc' ou 'preco_desc'
 * @param bool   $todas  true mostra também as que estão fora da vitrine (só o admin usa)
 * @return array lista de lapiseiras (vazia se nenhuma combinar)
 */
function relatorio($conexao, $bitola = '', $ordem = 'novidades', $todas = false) {
    // "WHERE 1 = 1" é sempre verdadeiro: serve só para os filtros abaixo
    // entrarem todos como "AND ...", sem tratar o primeiro de forma diferente
    $sql = "SELECT * FROM lapiseiras WHERE 1 = 1";
    $parametros = [];   // valores dos marcadores; só entra o que o filtro usar

    if (!$todas) {
        $sql .= " AND ativo = true";   // .= acrescenta texto no fim do $sql
    }
    if ($bitola != '') {
        $sql .= " AND bitola = :bitola";
        $parametros[':bitola'] = $bitola;
    }

    // A ordenação não pode ser um marcador (:ordem) do prepared statement,
    // então escolhemos entre textos fixos: nada digitado pelo usuário entra aqui
    if ($ordem == 'preco_asc') {
        $sql .= " ORDER BY preco ASC";
    } elseif ($ordem == 'preco_desc') {
        $sql .= " ORDER BY preco DESC";
    } else {
        $sql .= " ORDER BY criado_em DESC, id DESC";
    }

    $stmt = $conexao->prepare($sql);
    $stmt->execute($parametros);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);   // todas as linhas encontradas
}

/**
 * Grava as alterações de uma lapiseira.
 *
 * @param PDO         $conexao
 * @param int         $id     lapiseira que está sendo alterada
 * @param string      $modelo
 * @param string      $marca
 * @param string      $bitola
 * @param string      $preco
 * @param string|null $imagem caminho da foto, ou null para ficar sem foto
 * @param string      $ativo  'true' ou 'false'
 * @return void
 */
function atualizar($conexao, $id, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {
    $sql = "UPDATE lapiseiras SET modelo = :modelo, marca = :marca, bitola = :bitola,
            preco = :preco, imagem = :imagem, ativo = :ativo WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':id' => $id, ':modelo' => $modelo, ':marca' => $marca, ':bitola' => $bitola,
                    ':preco' => $preco, ':imagem' => $imagem, ':ativo' => $ativo]);
}

/**
 * Lê os campos do formulário de lapiseira enviado por POST.
 *
 * Campo que não veio no envio (ex.: nenhuma opção de "Disponível na vitrine"
 * marcada) vira texto vazio. Assim a validação e a tela não precisam conferir
 * se cada campo existe.
 *
 * @return array modelo, marca, bitola, preco e ativo, mais o resto do $_POST (id...)
 */
function ler_formulario_lapiseira() {
    // O + junta dois arrays: as chaves do $_POST ficam, e as que faltarem
    // vêm do segundo array, com ''
    return $_POST + ['modelo' => '', 'marca' => '', 'bitola' => '', 'preco' => '', 'ativo' => ''];
}

/**
 * Confere os campos do formulário de lapiseira.
 *
 * @param array $dados campos lidos por ler_formulario_lapiseira()
 * @return string[] mensagens de erro (lista vazia = tudo certo)
 */
function validar_lapiseira($dados) {
    global $bitolas;   // global: usa a lista $bitolas criada fora da função, no topo do arquivo
    $erros = [];

    // trim(): tira os espaços das pontas, então "   " conta como vazio
    if (trim($dados['modelo']) == '') {
        $erros[] = 'Informe o modelo.';   // $erros[] = ... acrescenta no fim da lista
    }
    if (trim($dados['marca']) == '') {
        $erros[] = 'Informe a marca.';
    }
    // in_array(): true se o valor está na lista
    if (!in_array($dados['bitola'], $bitolas)) {
        $erros[] = 'Escolha uma bitola da lista.';
    }
    // is_numeric(): true se o texto é um número ("12.5" sim, "abc" não)
    if (!is_numeric($dados['preco']) || $dados['preco'] < 0) {
        $erros[] = 'Informe um preço válido.';
    }
    if ($dados['ativo'] != 'true' && $dados['ativo'] != 'false') {
        $erros[] = 'Escolha se a lapiseira aparece na vitrine.';
    }

    return $erros;
}


// =====================================================================
// 3. Upload da foto
// =====================================================================

/**
 * Salva a foto enviada no formulário, depois de conferir tipo e tamanho.
 *
 * Devolve sempre duas posições, [caminho, erro]:
 *   ['uploads/lapiseiras/abc123.jpg', '']  deu certo
 *   [null, '']                            nenhuma foto foi enviada (não é erro)
 *   [null, 'mensagem']                    foto recusada
 *
 * @param array|null $arquivo o $_FILES['imagem']
 * @return array
 */
function salvar_imagem($arquivo) {
    // Campo de arquivo vazio: a lapiseira só fica sem foto.
    // $arquivo['error'] é o código do PHP para o envio (UPLOAD_ERR_NO_FILE = nenhum arquivo)
    if (!isset($arquivo) || $arquivo['error'] == UPLOAD_ERR_NO_FILE) {
        return [null, ''];
    }

    // No máximo 2 MB (UPLOAD_ERR_INI_SIZE = passou do limite do php.ini).
    // $arquivo['size'] é o tamanho em bytes: 2 * 1024 * 1024 = 2 MB
    if ($arquivo['error'] == UPLOAD_ERR_INI_SIZE || $arquivo['size'] > 2 * 1024 * 1024) {
        return [null, 'A foto deve ter no máximo 2 MB.'];
    }
    if ($arquivo['error'] != UPLOAD_ERR_OK) {
        return [null, 'Não foi possível receber a foto.'];
    }

    // O nome do arquivo pode mentir ("virus.php" renomeado para "foto.jpg"),
    // então o finfo abre o arquivo e descobre o tipo verdadeiro pelo conteúdo.
    // tmp_name: onde o PHP guardou o arquivo enquanto o formulário é processado
    $tipos_aceitos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

    if (!isset($tipos_aceitos[$tipo])) {
        return [null, 'A foto precisa ser JPG, PNG ou WEBP.'];
    }

    // Nome novo e aleatório: não sobrescreve outra foto e não usa o nome
    // original, que vem do usuário.
    // random_bytes(16): 16 bytes sorteados; bin2hex(): vira um texto de 32 letras e números
    $nome = bin2hex(random_bytes(16)) . '.' . $tipos_aceitos[$tipo];
    $caminho = 'uploads/lapiseiras/' . $nome;

    // move_uploaded_file(): tira o arquivo da pasta temporária e grava no destino.
    // Devolve false se não conseguir (pasta sem permissão, por exemplo)
    if (!move_uploaded_file($arquivo['tmp_name'], __DIR__ . '/../' . $caminho)) {
        return [null, 'Não foi possível salvar a foto.'];
    }

    return [$caminho, ''];
}

/**
 * Apaga do disco uma foto que não é mais usada.
 *
 * @param string|null $caminho caminho salvo no banco (null = não faz nada)
 * @return void
 */
function apagar_imagem($caminho) {
    if ($caminho) {
        // basename() pega só o nome do arquivo: garante que só apagamos
        // arquivos de dentro da pasta uploads/lapiseiras
        $arquivo = __DIR__ . '/../uploads/lapiseiras/' . basename($caminho);
        // is_file(): true se o arquivo existe; unlink(): apaga o arquivo
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}


// =====================================================================
// 4. Carrinho
// =====================================================================
// O carrinho fica no banco (tabela carrinho_itens), ligado à conta: continua
// lá depois do logout e aparece em qualquer navegador em que a pessoa entrar.
// Só quem está logado usa o carrinho, então o dono é sempre $_SESSION['id'].

/**
 * Põe uma unidade da lapiseira no carrinho.
 *
 * @param PDO $conexao
 * @param int $id id da lapiseira
 * @return void
 */
function carrinho_adicionar($conexao, $id) {
    // Primeira unidade: insere a linha. Já tinha (ON CONFLICT): soma 1
    $sql = "INSERT INTO carrinho_itens (usuario_id, lapiseira_id, quantidade)
            VALUES (:usuario, :lapiseira, 1)
            ON CONFLICT (usuario_id, lapiseira_id)
            DO UPDATE SET quantidade = carrinho_itens.quantidade + 1";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':usuario' => $_SESSION['id'], ':lapiseira' => $id]);
}

/**
 * Tira a lapiseira do carrinho (todas as unidades).
 *
 * @param PDO $conexao
 * @param int $id id da lapiseira
 * @return void
 */
function carrinho_remover($conexao, $id) {
    $sql = "DELETE FROM carrinho_itens WHERE usuario_id = :usuario AND lapiseira_id = :lapiseira";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':usuario' => $_SESSION['id'], ':lapiseira' => $id]);
}

/**
 * Conta as unidades disponíveis no carrinho (o número do botão no header).
 * Itens fora de estoque continuam no carrinho, mas não entram na conta.
 *
 */
function carrinho_quantidade($conexao) {
    if (!usuario_logado()) {
        return 0;
    }

    // SUM soma as quantidades; COALESCE troca o resultado vazio (carrinho sem itens) por 0
    $sql = "SELECT COALESCE(SUM(c.quantidade), 0)
            FROM carrinho_itens c
            JOIN lapiseiras l ON l.id = c.lapiseira_id
            WHERE c.usuario_id = :usuario AND l.ativo = true";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':usuario' => $_SESSION['id']]);

    // fetchColumn(): devolve só o valor da primeira coluna (a soma); (int) o transforma em número
    return (int) $stmt->fetchColumn();
}

/**
 * Monta a lista do carrinho com os dados de cada lapiseira.
 *
 * Uma lapiseira excluída sai sozinha (ON DELETE CASCADE no banco). Uma que
 * saiu da vitrine continua na lista com 'disponivel' = false: a tela mostra
 * "Fora de estoque" e ela não soma no total.
 *
 * @param PDO $conexao
 * @return array dados da lapiseira + 'quantidade', 'disponivel' e 'subtotal'
 */
function carrinho_itens($conexao) {
    if (!usuario_logado()) {
        return [];
    }

    // JOIN junta cada linha do carrinho com os dados da lapiseira (l.* = todas as colunas dela)
    $sql = "SELECT l.*, c.quantidade
            FROM carrinho_itens c
            JOIN lapiseiras l ON l.id = c.lapiseira_id
            WHERE c.usuario_id = :usuario
            ORDER BY l.ativo DESC, l.marca, l.modelo";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([':usuario' => $_SESSION['id']]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // $indice é a posição do item na lista: é por ela que gravamos os campos novos
    foreach ($itens as $indice => $item) {
        $itens[$indice]['disponivel'] = (bool) $item['ativo'];   // (bool): vira true ou false
        $itens[$indice]['subtotal'] = $item['ativo'] ? $item['preco'] * $item['quantidade'] : 0;
    }

    return $itens;
}


// =====================================================================
// 5. Usuários (login e cadastro)
// =====================================================================

/**
 * Cria uma conta de cliente.
 *
 * A senha nunca é salva como foi digitada: o password_hash() a transforma
 * num código que não dá para desfazer. No login, o password_verify() confere.
 *
 * @param PDO    $conexao
 * @param string $nome
 * @param string $email
 * @param string $password senha digitada
 * @return void
 */
function cadastrar_user($conexao, $nome, $email, $password) {
    $sql = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";

    // password_hash(..., PASSWORD_DEFAULT): usa o algoritmo mais seguro que o PHP conhece
    $stmt = $conexao->prepare($sql);
    $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => password_hash($password, PASSWORD_DEFAULT)]);
}

/**
 * Busca uma conta pelo e-mail.
 *
 * @param PDO    $conexao
 * @param string $email
 * @return array|false id, nome, email, senha (hash) e papel, ou false se não existir
 */
function consultar_user($conexao, $email) {
    $stmt = $conexao->prepare("SELECT id, nome, email, senha, papel FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);   // uma linha só: o e-mail não se repete (UNIQUE)
}

/**
 * Lista todas as contas (para a tela de usuários do admin).
 *
 * @param PDO $conexao
 * @return array id, email e papel de cada conta, em ordem de e-mail
 */
function listar_usuarios($conexao) {
    $stmt = $conexao->prepare("SELECT id, email, papel FROM usuarios ORDER BY email");
    $stmt->execute();   // sem marcadores: nada para trocar

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Troca o papel de uma conta.
 *
 * @param PDO    $conexao
 * @param int    $id
 * @param string $papel 'cliente' ou 'admin'
 * @return void
 */
function atualizar_papel($conexao, $id, $papel) {
    $stmt = $conexao->prepare("UPDATE usuarios SET papel = :papel WHERE id = :id");
    $stmt->execute([':id' => $id, ':papel' => $papel]);
}

/**
 * Como cumprimentar a pessoa no header ("Olá, Ana"): o primeiro nome cadastrado.
 * Contas sem nome usam o começo do e-mail ("ana.souza@..." vira "Ana").
 *
 * @param array $usuario linha da tabela usuarios (com 'nome' e 'email')
 * @return string
 */
function primeiro_nome($usuario) {
    $nome = trim($usuario['nome'] ?? '');
    if ($nome == '') {
        // preg_split(): corta o texto onde houver @ . _ + ou -, e [0] pega o primeiro pedaço
        $nome = preg_split('/[@._+-]/', $usuario['email'])[0];
    }
    $nome = preg_split('/\s+/', $nome)[0];   // corta nos espaços: fica só o primeiro nome
    return ucfirst($nome);                   // ucfirst(): primeira letra maiúscula ("ana" -> "Ana")
}


// =====================================================================
// 6. Ponte entre o backend e as telas (views/)
// =====================================================================
// Cada página faz o trabalho de backend (ler o formulário, validar, falar
// com o banco) e termina chamando mostrar_pagina() com os dados prontos.
// A tela só monta o HTML com esses dados: não acessa o banco nem a sessão.

/**
 * Mostra uma tela de views/paginas/, entregando os dados para ela.
 *
 * Cada chave de $dados vira uma variável na tela: ['lapiseiras' => ...] vira
 * $lapiseiras. A tela escreve só o próprio conteúdo; a moldura
 * (views/layout/pagina.php) coloca em volta o <head>, o header e o footer,
 * que são iguais em todas as páginas.
 *
 * Exemplo: mostrar_pagina('admin/relatorio', ['lapiseiras' => $lapiseiras]);
 *
 * @param string $tela  caminho dentro de views/paginas/, sem o .php
 * @param array  $dados variáveis para a tela
 * @return void
 */
function mostrar_pagina($tela, $dados = []) {
    global $conexao;   // global: usa a $conexao criada fora da função (connect_postgres.php)

    // Dados que o header precisa em todas as páginas
    $pagina = basename($tela);   // basename(): 'admin/relatorio' vira 'relatorio' (destaca o link na barra do admin)
    $logado = usuario_logado();
    $admin = usuario_admin();
    $nome_usuario = $_SESSION['nome'] ?? '';
    $quantidade_carrinho = carrinho_quantidade($conexao);

    extract($dados);   // extract(): cada chave do array vira uma variável ($lapiseiras, $erros...)

    // 1. A tela monta o seu HTML e define $titulo e $estilos para a moldura.
    //    ob_start() guarda tudo o que seria enviado ao navegador, e
    //    ob_get_clean() devolve esse HTML como texto. Assim o <head> da
    //    moldura pode sair antes dele.
    ob_start();
    include __DIR__ . '/../views/paginas/' . $tela . '.php';
    $conteudo = ob_get_clean();

    // 2. A moldura escreve a página completa, com o $conteudo no meio
    include __DIR__ . '/../views/layout/pagina.php';
}
