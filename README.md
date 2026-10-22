# Lapisari

Loja virtual de lapiseiras de coleção, feita em **PHP** e **PostgreSQL**.

O cliente navega por uma vitrine com filtro por bitola, cria uma conta, entra e monta o seu carrinho. O administrador cadastra, consulta, atualiza e exclui lapiseiras (CRUD completo, com foto) e define quem é cliente e quem é administrador.

## Sumário

1. [Sobre o projeto](#1-sobre-o-projeto)
2. [Requisitos funcionais](#2-requisitos-funcionais)
3. [Regras de negócio](#3-regras-de-negócio)
4. [Requisitos não funcionais](#4-requisitos-não-funcionais)
5. [Fluxo de trabalho](#5-fluxo-de-trabalho)
6. [Banco de dados](#6-banco-de-dados)
7. [Organização do código](#7-organização-do-código)
8. [Como rodar](#8-como-rodar)

---

## 1. Sobre o projeto

A **Lapisari** é uma revenda de lapiseiras de marcas oficiais (Rotring, Pentel, Staedtler, Uni...). O site apresenta a marca, mostra o catálogo e permite que o cliente separe os modelos que quer comprar num carrinho ligado à conta dele.

O sistema tem dois tipos de usuário:

| Usuário | O que faz |
|---|---|
| **Visitante** | Vê a página inicial e a vitrine, filtra e ordena os modelos, cria uma conta. |
| **Cliente** | Tudo o que o visitante faz, mais entrar na conta e usar o carrinho. |
| **Administrador** | Tudo o que o cliente faz, mais a área de administração: cadastro, relatório, consulta, atualização e exclusão de lapiseiras, e gestão dos papéis das contas. |

### Principais funcionalidades

- **Vitrine** com filtro por bitola (0.3 a 2.0 mm) e ordenação por novidade ou preço.
- **Contas de usuário:** criar conta, entrar, sair (inclusive automaticamente ao fechar a aba).
- **Carrinho** salvo no banco: continua lá depois de sair e aparece em qualquer navegador.
- **Área do administrador:** CRUD de lapiseiras com upload de foto, relatório e gestão de usuários.
- **Página inicial animada:** a assinatura "Lapisari" é escrita à mão e vira o header, e uma linha de grafite desenhada pela rolagem liga os pilares da marca, com uma lapiseira na ponta.

### Tecnologias

| Camada | Tecnologia | Uso |
|---|---|---|
| Backend | PHP 8 (PDO) | Páginas, regras de negócio, sessão e upload |
| Banco | PostgreSQL | Contas, lapiseiras e carrinho |
| Front | HTML, CSS e JavaScript, sem framework | Telas e interações |
| Animações | GSAP 3 (ScrollTrigger, SplitText, DrawSVG) | Abertura e caminho de grafite da página inicial |
| Fontes | Google Fonts | Cormorant Garamond, Manrope e Mrs Saint Delafield |

---

## 2. Requisitos funcionais

| Código | Requisito | Quem usa | Onde |
|---|---|---|---|
| **RF01** | Exibir a vitrine com as lapiseiras disponíveis (foto, marca, modelo, bitola e preço). | Todos | `index.php` |
| **RF02** | Filtrar a vitrine por bitola e ordenar por Novidades, Menor preço ou Maior preço. O filtro fica na URL, então pode ser recarregado ou compartilhado. | Todos | `index.php` |
| **RF03** | Criar uma conta com nome, e-mail e senha. | Visitante | `login/registerUser.php` |
| **RF04** | Entrar com e-mail e senha. Depois de entrar, o header cumprimenta a pessoa pelo primeiro nome ("Olá, Ana"). | Visitante | `login/login.php` |
| **RF05** | Sair da conta pelo botão "Sair", ou automaticamente ao fechar a aba em que entrou. | Cliente, admin | `login/logout.php`, `assets/js/site.js` |
| **RF06** | Adicionar uma lapiseira ao carrinho. Adicionar de novo a mesma lapiseira soma uma unidade. | Cliente, admin | `carrinho/adicionar.php` |
| **RF07** | Ver o carrinho com a quantidade e o subtotal de cada item, o total do pedido e o número de itens. | Cliente, admin | `carrinho/index.php` |
| **RF08** | Remover uma lapiseira do carrinho. | Cliente, admin | `carrinho/remover.php` |
| **RF09** | Mostrar no header o número de itens do carrinho, em todas as páginas. | Cliente, admin | `views/layout/header.php` |
| **RF10** | Cadastrar uma lapiseira (modelo, marca, bitola, preço, foto opcional e se aparece na vitrine). | Admin | `app/create.php` |
| **RF11** | Listar todas as lapiseiras num relatório, inclusive as fora da vitrine, com o total cadastrado e quantas estão na vitrine. | Admin | `app/select.php` |
| **RF12** | Consultar uma lapiseira pelo ID e ver a ficha completa, com atalhos para editar e excluir. | Admin | `app/select_where.php` |
| **RF13** | Atualizar os dados de uma lapiseira, trocar ou remover a foto e tirá-la (ou devolvê-la) da vitrine. | Admin | `app/update.php` |
| **RF14** | Excluir uma lapiseira pelo ID, com confirmação. | Admin | `app/delete.php` |
| **RF15** | Listar as contas e trocar o papel de cada uma entre cliente e administrador. | Admin | `app/usuarios.php` |
| **RF16** | Mostrar ao administrador, na vitrine, também as lapiseiras fora da vitrine, com o selo "Fora da vitrine". | Admin | `index.php` |
| **RF17** | Mostrar mensagens de erro e de sucesso nos formulários, mantendo nos campos o que foi digitado quando há erro. | Todos | todas as telas com formulário |
| **RF18** | Exibir a abertura animada na chegada ao site e o caminho de grafite na página inicial, com a opção de desligar as animações pelo endereço (`?sem-animacao`). | Todos | `assets/js/splash-inicio.js` |

---

## 3. Regras de negócio

| Código | Regra |
|---|---|
| **RN01** | Toda conta nasce como **cliente**. O primeiro administrador é definido direto no banco; depois disso, os papéis são trocados na tela Usuários. |
| **RN02** | O e-mail é único: não podem existir duas contas com o mesmo e-mail. |
| **RN03** | O nome é obrigatório (até 60 caracteres) e a senha precisa ter pelo menos 6 caracteres. |
| **RN04** | Só quem está logado usa o carrinho. O visitante que clica em "Adicionar ao Carrinho" é levado ao login. |
| **RN05** | Só entra no carrinho uma lapiseira que existe e está na vitrine. |
| **RN06** | Uma lapiseira que sai da vitrine continua no carrinho como **"Fora de estoque"**, mas não entra no total nem no contador do header. |
| **RN07** | Uma lapiseira excluída some da vitrine e de todos os carrinhos, e a foto dela é apagada do servidor. |
| **RN08** | Bitolas aceitas: 0.3, 0.5, 0.7, 0.9, 1.3 e 2.0 mm. O preço precisa ser um número maior ou igual a zero. |
| **RN09** | A foto é opcional; quando enviada, precisa ser JPG, PNG ou WEBP de até 2 MB. Sem foto, a vitrine mostra uma imagem padrão. |
| **RN10** | O administrador não pode tirar o próprio acesso de administrador, para o sistema nunca ficar sem nenhum admin. |
| **RN11** | Excluir é sempre uma ação confirmada e enviada por formulário, nunca por um simples link. |
| **RN12** | O papel de cada conta é conferido no banco a cada página aberta: uma mudança de papel vale na hora, e uma conta apagada é deslogada. |
| **RN13** | Quem já está logado e abre a tela de login ou de criar conta volta para a vitrine. |
| **RN14** | O administrador é deslogado automaticamente depois de um tempo sem abrir nenhuma página, e a tela de login avisa que a sessão expirou. O tempo fica em `$admin_inativo_segundos`, no `includes/functions.php`. |

---

## 4. Requisitos não funcionais

| Código | Categoria | Requisito | Como é atendido |
|---|---|---|---|
| **RNF01** | Segurança | As senhas nunca são guardadas como foram digitadas. | `password_hash()` no cadastro e `password_verify()` no login. |
| **RNF02** | Segurança | Nada do que o usuário digita é executado como SQL. | Todas as consultas usam prepared statements do PDO; a ordenação é escolhida entre textos fixos. |
| **RNF03** | Segurança | Nenhum texto cadastrado é executado como código na página (XSS). | Todo texto vindo do usuário passa pela função `e()` (`htmlspecialchars`) antes de ir para o HTML. |
| **RNF04** | Segurança | O upload aceita só imagens de verdade. | O tipo é conferido pelo conteúdo do arquivo (`finfo`), não pelo nome; limite de 2 MB; o arquivo recebe um nome aleatório; no Apache, o `.htaccess` da pasta de fotos impede a execução de PHP. |
| **RNF05** | Segurança | A sessão é protegida contra roubo e contra a área do admin ficar aberta sem ninguém usando. | Novo id de sessão a cada login (`session_regenerate_id`) e logout automático do admin por inatividade. |
| **RNF06** | Controle de acesso | Páginas restritas só abrem para quem pode usá-las. | `login/verifica_admin.php` no topo das páginas do admin e `login/verifica_user.php` nas ações do carrinho. |
| **RNF07** | Integridade | Dados inválidos não chegam ao banco, mesmo que a validação do navegador seja burlada. | Validação no servidor (além do HTML) e restrições no banco: `CHECK`, `UNIQUE`, chaves estrangeiras e `ON DELETE CASCADE`. |
| **RNF08** | Usabilidade | O usuário entende o que aconteceu e não perde o que digitou. | Mensagens de erro e sucesso, campos preenchidos de volta, confirmação antes de excluir, prévia da foto e filtro aplicado assim que o select muda. |
| **RNF09** | Robustez | O site funciona sem JavaScript. | O JavaScript só melhora a experiência: o filtro tem botão próprio, os formulários enviam normalmente e, sem JS, a página abre sem animações. |
| **RNF10** | Acessibilidade | O site pode ser usado com leitor de tela e teclado. | Textos alternativos, `aria-label` onde o texto visível não basta (ex.: "Remover 800 do carrinho", "Carrinho, 3 itens"), rótulos ocultos para leitores de tela, `aria-current` na barra do admin; os títulos animados mantêm o texto completo num `aria-label`. |
| **RNF11** | Desempenho | Páginas leves, sem frameworks. | CSS separado por página; as bibliotecas de animação só são baixadas na página inicial, e só quando vão ser usadas. |
| **RNF12** | Portabilidade | Roda em rede local, sem depender da internet. | Bibliotecas copiadas para dentro do projeto (`assets/js/libs/`); só as fontes vêm do Google Fonts. O endereço base é configurável (`BASE_URL`), para rodar tanto no servidor do PHP quanto numa subpasta do Apache. |
| **RNF13** | Manutenibilidade | O código é fácil de ler e de alterar. | Backend (lógica) separado das telas (HTML) pela função `mostrar_pagina()`; partes repetidas viram componentes; todo arquivo e toda função têm comentários padronizados (PHPDoc). |
| **RNF14** | Compatibilidade | Ambiente suportado. | PHP 8 com as extensões `pdo_pgsql` e `fileinfo` (não depende do `mbstring`, que falta em muitos servidores); PostgreSQL; navegadores atuais de computador (Chrome, Edge, Firefox). |
| **RNF15** | Persistência | O carrinho não se perde ao sair da conta. | Ele fica no banco, ligado à conta, e não na sessão. |

---

## 5. Fluxo de trabalho

### 5.1 Cliente

```text
 Página inicial ──► Vitrine (filtra por bitola / ordena)
                        │
                        │ "Adicionar ao Carrinho"
                        ▼
              ┌──── está logado? ────┐
             não                    sim
              │                      │
              ▼                      ▼
         Tela de login         Item vai para o carrinho
              │                (ou soma +1) e volta à vitrine
              │ não tem conta?       │
              ▼                      │ botão "Carrinho" no header
       Criar conta ──► login         ▼
       (com o e-mail        Carrinho: itens, subtotais, total
        já preenchido)               │
                                     ├─► "Remover" um item
                                     ├─► "Continuar comprando"
                                     ▼
                         "Sair" ou fechar a aba ──► deslogado
                         (o carrinho fica salvo para a próxima vez)
```

### 5.2 Administrador

O administrador entra pela mesma tela de login. Para ele, aparece uma **barra de administração** logo abaixo do header, em todas as páginas:

```text
 [◄ Vitrine]  ADMINISTRAÇÃO   Relatório · Consultar · Atualizar · Excluir · Usuários   [+ Cadastrar Nova Lapiseira]
```

| Tela | Fluxo |
|---|---|
| **Cadastrar** | Preenche o formulário → validação → foto salva → lapiseira gravada → formulário limpo para o próximo cadastro. |
| **Relatório** | Tabela com todas as lapiseiras (ID, foto, modelo, marca, bitola, preço, situação e data de cadastro). |
| **Consultar** | Digita o ID → vê a ficha → pode ir direto para Editar ou Excluir. |
| **Atualizar** | Digita o ID (ou vem do botão Editar) → formulário preenchido → altera dados, troca ou remove a foto → salva. |
| **Excluir** | Digita o ID → confirma → lapiseira, foto e itens de carrinho apagados. |
| **Usuários** | Escolhe o papel de cada conta (Cliente ou Administrador) → salva. |

### 5.3 Como uma página funciona por dentro

Toda página segue o mesmo caminho, do pedido do navegador até o HTML pronto:

```text
 Navegador ──► página do backend (ex.: app/create.php)
                 1. confere quem pode acessar   (verifica_admin.php / verifica_user.php)
                 2. lê e valida o formulário    (ler_formulario_lapiseira, validar_lapiseira)
                 3. fala com o banco            (cadastrar, consultar, relatorio...)
                 4. mostrar_pagina('admin/cadastrar', [dados])
                        │
                        ▼
               tela (views/paginas/...)  ──►  moldura (views/layout/pagina.php)
               monta o <main> com os dados    <head> + header + conteúdo + footer
                        │
                        ▼
                 HTML completo ──► Navegador
```

As ações que não têm tela própria (adicionar e remover do carrinho, sair) fazem o trabalho e **redirecionam** para outra página. Assim, apertar F5 depois não repete a ação (padrão *POST → Redirect → GET*).

### 5.4 Página inicial e animações

```text
 Abre a página inicial
        │
        ▼
 splash-inicio.js (roda antes de a página aparecer)
        │
        ├─ ?sem-animacao na aba? ──► página parada, nada é carregado
        │
        ├─ chegou de fora do site? ─► ABERTURA: "Lapisari" escrito à mão em tela cheia
        │                             → no 1º scroll, a tela preta vira uma bolinha,
        │                               sobe e se abre no header
        │                             → o texto de abertura entra palavra por palavra
        │
        └─ sempre ─► CAMINHO DE GRAFITE: ao rolar, uma linha liga os três pilares,
                     com a lapiseira na ponta; cada pilar entra quando a linha chega nele
```

- A abertura **não** aparece para quem volta de outra página da loja, para links com `#vitrine` ou ao recarregar no meio da página.
- Para desligar as animações, abra o site com `?sem-animacao` (ex.: `index.php?sem-animacao`). Elas ficam desligadas naquela aba até ela ser fechada, ou até abrir com `?com-animacao`.

---

## 6. Banco de dados

A estrutura completa está em `database/estrutura.sql`.

```text
  usuarios                     carrinho_itens                    lapiseiras
 ┌─────────────┐          ┌────────────────────┐           ┌──────────────────┐
 │ id      PK  │◄─────────│ usuario_id   FK    │           │ id          PK   │
 │ nome        │          │ lapiseira_id FK    │──────────►│ modelo           │
 │ email UNIQUE│          │ quantidade  (> 0)  │           │ marca            │
 │ senha (hash)│          │ PK (usuario_id,    │           │ bitola  (lista)  │
 │ papel       │          │     lapiseira_id)  │           │ preco   (>= 0)   │
 └─────────────┘          └────────────────────┘           │ imagem           │
                            ON DELETE CASCADE              │ ativo            │
                            nos dois lados                 │ criado_em        │
                                                           └──────────────────┘
```

| Tabela | Guarda | Destaques |
|---|---|---|
| `usuarios` | As contas | `email` único; `senha` guarda só o hash; `papel` é `'cliente'` (padrão) ou `'admin'`. |
| `lapiseiras` | O catálogo | `bitola` só aceita a lista de bitolas; `preco >= 0`; `imagem` vazia usa a imagem padrão; `ativo = false` tira da vitrine; `criado_em` ordena as Novidades. |
| `carrinho_itens` | Uma linha por lapiseira no carrinho de cada conta | A chave (`usuario_id`, `lapiseira_id`) impede linhas repetidas; apagar a conta ou a lapiseira apaga as linhas dela. |

---

## 7. Organização do código

O projeto separa o **backend**, que decide o que fazer, das **telas**, que só mostram o resultado:

| Lado | Onde fica | O que faz |
|---|---|---|
| **Backend** | `index.php`, `app/`, `carrinho/`, `login/`, `includes/` | Recebe o pedido, valida os formulários, fala com o banco e com a sessão. |
| **Front** | `views/` (HTML), `assets/css/` e `assets/js/` | Monta a tela com os dados que o backend entrega. |

Exemplo de uma página do backend:

```php
// app/select.php
require_once __DIR__ . '/../login/verifica_admin.php';    // 1. quem pode acessar
$lapiseiras = relatorio($conexao, '', 'novidades', true); // 2. busca no banco
mostrar_pagina('admin/relatorio', [                        // 3. entrega para a tela
    'lapiseiras' => $lapiseiras,
]);
```

A tela `views/paginas/admin/relatorio.php` recebe `$lapiseiras` e monta só o conteúdo da página (o `<main>`). O `mostrar_pagina()` coloca em volta a moldura `views/layout/pagina.php`, com o `<head>`, o header e o footer, que são iguais em todas as páginas. As telas de login e de criar conta usam o cartão preto `views/layout/cartao_login.php` no lugar do header e do footer.

```text
mini-sistema/
├── index.php              vitrine (página inicial)
├── app/                   área do admin: create, select, select_where, update, delete, usuarios
├── carrinho/              página do carrinho, adicionar e remover
├── login/                 login, criar conta, sair e verificações de acesso
├── includes/
│   ├── config.php         BASE_URL, url() e sessão
│   └── functions.php      regras de negócio, banco e mostrar_pagina()
├── database/
│   ├── connect_postgres.php   conexão com o banco
│   └── estrutura.sql          criação das tabelas e dados de exemplo
├── views/
│   ├── helpers.php        e(), formatar_preco(), formatar_bitola(), icone()...
│   ├── paginas/           uma tela para cada página
│   ├── layout/            moldura da página, cartão do login, header, footer, barra do admin
│   ├── componentes/       card da vitrine, formulário de lapiseira, logotipo, desenho da lapiseira
│   └── icones/            ícones em SVG
├── assets/
│   ├── css/               base, layout, vitrine, carrinho, forms, login, splash (abertura), caminho
│   ├── js/
│   │   ├── site.js           confirmação de exclusão, filtro automático, prévia da foto, sair ao fechar a aba
│   │   ├── splash-inicio.js  decide se a abertura aparece e carrega as animações
│   │   ├── splash.js         a assinatura escrita que vira o header
│   │   ├── abertura.js       o texto de abertura
│   │   ├── caminho.js        o caminho de grafite que liga os pilares
│   │   └── libs/             GSAP e plugins (ver LEIA-ME.md)
│   └── img/               imagem padrão das lapiseiras sem foto
└── uploads/lapiseiras/    fotos enviadas pelo admin
```

### Comentários no código

Todo arquivo PHP começa com um bloco `/** ... */` que diz o que ele faz e com quem conversa:

- **Backend** (`app/`, `carrinho/`, `login/`, `index.php`): o que a página faz, quem pode acessar e qual é a `Tela:`.
- **Telas** (`views/paginas/`): qual é o `Backend:`, quais variáveis ela `Recebe:` e as `Funções usadas`.
- **Funções** (`includes/functions.php`, `views/helpers.php`): o que fazem, com `@param` e `@return`. O VS Code mostra esse texto ao passar o mouse sobre a chamada da função.

---

## 8. Como rodar

Todos os comandos abaixo podem ser **copiados e colados exatamente como estão**. Cada bloco é um comando: cole um, aperte Enter e espere terminar antes de colar o próximo (o `psql` pede a senha do banco, e uma linha colada junto seria lida como senha).

No **Windows**, use o **PowerShell** (menu Iniciar → digite *PowerShell* → Enter). No **Linux**, use o terminal.

### Escolha o seu caso

| Caso | Quando usar | Pasta do projeto | Endereço no navegador |
|---|---|---|---|
| **A** – Windows, servidor do PHP | Testar no seu computador (o mais simples) | `C:\Users\SEU_USUARIO\mini-sistema` | http://localhost:8000 |
| **B** – Linux, Apache ou nginx | Publicar num servidor | `/var/www/html/mini-sistema` | `http://IP-DO-SERVIDOR/mini-sistema/` |

Siga os passos abaixo usando só os blocos do seu caso.

### Passo 0: conferir os programas

Você precisa de **Git**, **PHP 8** (com as extensões `pdo_pgsql` e `fileinfo`) e **PostgreSQL**.

**Windows (A):** se o PowerShell disser que `php` ou `psql` "não é reconhecido", o comando abaixo encontra o `psql` na pasta padrão do PostgreSQL (vale até fechar a janela do PowerShell). Para o `php`, instale o PHP 8 e adicione a pasta dele ao PATH do Windows.

```powershell
$env:Path += ";" + (Get-ChildItem "C:\Program Files\PostgreSQL" | Sort-Object Name | Select-Object -Last 1).FullName + "\bin"
```

Para conferir, cole os três comandos abaixo. Cada um deve mostrar uma versão, e o do meio deve listar `fileinfo` e `pdo_pgsql`:

```powershell
git --version
```

```powershell
php -m | Select-String "pdo_pgsql|fileinfo"
```

```powershell
psql --version
```

**Linux (B):**

```bash
git --version
```

```bash
php -m | grep -E "pdo_pgsql|fileinfo"
```

```bash
psql --version
```

### Passo 1: baixar o projeto

O comando entra na pasta certa, baixa o projeto numa pasta chamada `mini-sistema` e entra nela.

**A – Windows, servidor do PHP:**

```powershell
cd ~; git clone https://github.com/LucasRetameroBortoletto/lapisari-.git mini-sistema; cd mini-sistema
```

**B – Linux:**

```bash
cd /var/www/html && sudo git clone https://github.com/LucasRetameroBortoletto/lapisari-.git mini-sistema && cd mini-sistema
```

O nome `mini-sistema` no fim do `git clone` importa: sem ele a pasta se chamaria `lapisari-`, e o endereço do site e o `BASE_URL` (passo 5) não combinariam.

**Os passos 2 a 5 continuam no mesmo terminal, dentro da pasta `mini-sistema`.**

> **Navegando no terminal:** `cd ..` volta para a pasta de cima, `cd ~` vai para a pasta do usuário, `pwd` mostra em que pasta você está e `ls` lista o que há nela.

### Passo 2: criar o usuário do banco

O site usa um usuário próprio, `lapisari`, em vez do `postgres` (o superusuário). O comando pede a senha nova duas vezes (anote, ela vai no passo 4) e, no Windows, depois a senha do `postgres`.

**Windows (A):**

```powershell
createuser -U postgres --pwprompt lapisari
```

**Linux (B):**

```bash
sudo -u postgres createuser --pwprompt lapisari
```

### Passo 3: criar o banco de dados

O primeiro comando cria o banco com o `lapisari` como dono. O segundo entra como `lapisari` e cria as tabelas, com 5 lapiseiras de exemplo.

**Windows (A):**

```powershell
psql -U postgres -c "CREATE DATABASE lapisari OWNER lapisari;"
```

```powershell
psql -h localhost -U lapisari -d lapisari -f database/estrutura.sql
```

**Linux (B):**

```bash
sudo -u postgres psql -c "CREATE DATABASE lapisari OWNER lapisari;"
```

```bash
psql -h localhost -U lapisari -d lapisari -f /var/www/html/mini-sistema/database/estrutura.sql
```

**Confira a saída do segundo comando:** deve aparecer `CREATE TABLE` três vezes e `INSERT 0 5`. O `psql -f` não para quando dá erro: se aparecer qualquer `ERROR` ou `FATAL`, as tabelas não foram criadas e o site vai dar erro 500. Para ter certeza, liste as tabelas (devem aparecer `usuarios`, `lapiseiras` e `carrinho_itens`, com dono `lapisari`):

```bash
psql -h localhost -U lapisari -d lapisari -c "\dt"
```

> **Pelo pgAdmin:** crie o usuário em *Login/Group Roles* → *Create* (nome `lapisari`, senha em *Definition*), crie o banco `lapisari` com *Owner* `lapisari` e, conectado como `lapisari`, rode o `database/estrutura.sql` no *Query Tool* (F5).

**Se o banco `lapisari` já existia** (o primeiro comando diz `already exists`), ele pode ser do `postgres` e não ter as tabelas, ou ter tabelas antigas do `postgres`. Nos dois casos o site dá erro 500. Faça na ordem (no Windows, troque `sudo -u postgres psql` por `psql -U postgres`):

1. Passe o banco para o `lapisari`:

```bash
sudo -u postgres psql -d lapisari -c "ALTER DATABASE lapisari OWNER TO lapisari; ALTER SCHEMA public OWNER TO lapisari;"
```

2. Passe as tabelas antigas para o `lapisari`. Se aparecer `relation "usuarios" does not exist`, as tabelas ainda não existem: tudo bem, pule para o item 3.

```bash
sudo -u postgres psql -d lapisari -c "ALTER TABLE usuarios OWNER TO lapisari; ALTER TABLE lapiseiras OWNER TO lapisari; ALTER TABLE carrinho_itens OWNER TO lapisari;"
```

3. **Rode de novo o segundo comando deste passo** (o `psql ... -f .../estrutura.sql`) e confira a saída como descrito acima. Ele cria só as tabelas que faltam, sem apagar dados.

### Passo 4: configurar a conexão com o banco

Abra o arquivo da conexão:

**Windows (A):**

```powershell
notepad database\connect_postgres.php
```

**Linux (B):**

```bash
sudo nano /var/www/html/mini-sistema/database/connect_postgres.php
```

Troque as quatro linhas do começo por estas e, na última, escreva a senha do passo 2 no lugar de `sua_senha`:

```php
$host = "localhost";
$dbname = "lapisari";
$user = "lapisari";
$pass = "sua_senha";
```

- `$host`: onde o PostgreSQL está. `localhost` é o próprio computador; se o banco estiver em outra máquina, use o IP dela.
- `$dbname`: o banco criado no passo 3.
- `$user` e `$pass`: o usuário criado no passo 2 e a senha dele.

Salve (no Notepad, Ctrl+S; no nano, Ctrl+O, Enter e Ctrl+X para sair).

### Passo 5: ajustar o endereço base (`BASE_URL`)

O `BASE_URL`, em `includes/config.php`, é a pasta do projeto no endereço do site. Se ele não combinar com o endereço, a página abre sem estilo.

**A – Windows, servidor do PHP:** não precisa mudar nada. O projeto já vem com `define('BASE_URL', '');`.

**B – Linux:** este comando faz a troca sozinho:

```bash
sudo sed -i "s|define('BASE_URL', '[^']*');|define('BASE_URL', '/mini-sistema');|" /var/www/html/mini-sistema/includes/config.php
```

### Passo 6: iniciar o site

**A – Windows, servidor do PHP:**

```powershell
php -S localhost:8000
```

Abra http://localhost:8000 no navegador. Deixe o PowerShell aberto enquanto usa o site; para parar, aperte Ctrl+C.

**B – Linux:** o Apache/nginx já está rodando. Dê ao PHP permissão de gravar na pasta de fotos (sem isso o upload de fotos falha):

```bash
sudo chown -R www-data:www-data /var/www/html/mini-sistema/uploads/lapiseiras
```

Este comando mostra o endereço do site para abrir no navegador:

```bash
echo "http://$(hostname -I | awk '{print $1}')/mini-sistema/"
```

**Deu certo se** a página inicial abre com a assinatura "Lapisari" e, descendo, a vitrine mostra as 5 lapiseiras de exemplo.

### Passo 7: criar o primeiro administrador

Crie a sua conta pelo site (botão **Entrar** → **Criar conta**). **Logo em seguida**, rode o comando abaixo: ele transforma em administradora a **última conta criada**, que é a sua. Ele pede a senha do passo 2.

**Windows (A):**

```powershell
psql -h localhost -U lapisari -d lapisari -c "UPDATE usuarios SET papel = 'admin' WHERE id = (SELECT max(id) FROM usuarios);"
```

**Linux (B):**

```bash
psql -h localhost -U lapisari -d lapisari -c "UPDATE usuarios SET papel = 'admin' WHERE id = (SELECT max(id) FROM usuarios);"
```

Abra qualquer página do site de novo: a barra de administração aparece. Depois disso, os papéis das outras contas podem ser trocados pela tela **Usuários**, nessa barra.

### Para atualizar o projeto depois

**A – Windows, servidor do PHP:**

```powershell
cd ~\mini-sistema; git pull
```

**B – Linux:**

```bash
cd /var/www/html/mini-sistema && sudo git pull
```

### Se algo der errado

| O que aparece | Causa provável | O que fazer |
|---|---|---|
| `php`, `psql` ou `git` "não é reconhecido" | O Windows não sabe onde está o programa | Veja o passo 0. Se for o `git`, instale o Git for Windows. |
| Página sem estilo, só com texto e um desenho gigante | O `BASE_URL` não combina com o endereço | Refaça o passo 5 do seu caso. |
| Mensagem começando com `Erro: SQLSTATE` | O PHP não conseguiu falar com o banco | Confira o passo 4 e se o PostgreSQL está ligado. |
| `password authentication failed for user "lapisari"` | A senha no `connect_postgres.php` não é a do usuário `lapisari` | Confira a senha do passo 4. Se esqueceu, crie outra com `sudo -u postgres psql -c "\password lapisari"`. |
| `Peer authentication failed for user "lapisari"` | O `psql` foi rodado no Linux sem o `-h localhost` | Use `-h localhost` no comando, como nos passos 3 e 7. |
| `relation "lapiseiras" does not exist` (no log) | As tabelas não foram criadas | Rode o segundo comando do passo 3 e confira a saída. |
| `permission denied for table ...` | As tabelas foram criadas pelo `postgres`, e não pelo `lapisari` | Rode o comando `ALTER ... OWNER` do fim do passo 3. |
| Erro 500 ("problema neste site") | Um erro no PHP, escondido pelo servidor | Veja a mensagem real no log, com um dos comandos abaixo. |
| "Não foi possível salvar a foto." | O PHP não pode gravar na pasta de fotos | No Linux, rode o comando `chown` do passo 6. |
| Alterações no CSS ou no JavaScript não aparecem | O navegador guardou a versão antiga | Aperte Ctrl+F5 na página. |

Log de erros do **nginx** (B):

```bash
sudo tail -n 20 /var/log/nginx/error.log
```

Log de erros do **Apache no Linux** (B):

```bash
sudo tail -n 20 /var/log/apache2/error.log
```

### Dicas

- **Apresentar sem animações:** abra o site com `?sem-animacao` no fim do endereço, por exemplo http://localhost:8000/index.php?sem-animacao. Para voltar, http://localhost:8000/index.php?com-animacao.
- **Fotos:** o tamanho recomendado é 1000 × 1000 px (quadrada), com a lapiseira centralizada e fundo transparente (PNG/WEBP) ou cinza-claro.
