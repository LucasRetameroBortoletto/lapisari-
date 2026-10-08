-- =====================================================================
-- Lapisari · estrutura do banco (PostgreSQL)
--
-- O site NÃO executa este arquivo: rode-o uma vez, à mão, no mesmo banco
-- configurado em database/connect_postgres.php ($dbname):
--
--   psql -U SEU_USUARIO -d SEU_BANCO -f database/estrutura.sql
--
-- Pode rodar de novo sem perder dados: o IF NOT EXISTS só cria o que
-- ainda não existe, e os exemplos só entram com a tabela vazia.
-- Atenção: uma tabela que já existe NÃO é alterada. Se o seu banco é de
-- uma versão antiga do projeto, recrie as tabelas (passo 0).
-- =====================================================================


-- ---------------------------------------------------------------------
-- 0) Recomeçar do zero (opcional)
--    APAGA TODAS AS CONTAS, LAPISEIRAS E CARRINHOS. Descomente só se
--    quiser recriar as tabelas com esta estrutura.
-- ---------------------------------------------------------------------
-- DROP TABLE IF EXISTS carrinho_itens, lapiseiras, usuarios;


-- ---------------------------------------------------------------------
-- 1) Usuários: as contas de clientes e administradores
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id    SERIAL       PRIMARY KEY,
    nome  VARCHAR(60)  NOT NULL,                     -- cumprimento no header ("Olá, Ana")
    email VARCHAR(120) NOT NULL UNIQUE,              -- usado para entrar; não se repete
    senha VARCHAR(255) NOT NULL,                     -- hash do password_hash(), nunca a senha digitada
    papel VARCHAR(10)  NOT NULL DEFAULT 'cliente'    -- 'admin' vê a barra de administração
          CHECK (papel IN ('cliente', 'admin'))
);
-- senha com 255 caracteres: o password_hash() gera ~60 hoje, mas o
-- tamanho pode crescer quando o PHP adotar algoritmos novos.


-- ---------------------------------------------------------------------
-- 2) Lapiseiras: o catálogo da vitrine
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lapiseiras (
    id        SERIAL        PRIMARY KEY,
    modelo    VARCHAR(120)  NOT NULL,
    marca     VARCHAR(60)   NOT NULL,
    bitola    NUMERIC(2,1)  NOT NULL                 -- em mm; a mesma lista do $bitolas (includes/functions.php)
              CHECK (bitola IN (0.3, 0.5, 0.7, 0.9, 1.3, 2.0)),
    preco     NUMERIC(10,2) NOT NULL CHECK (preco >= 0),
    imagem    VARCHAR(255),                          -- caminho em uploads/lapiseiras/; vazio = imagem padrão
    ativo     BOOLEAN       NOT NULL DEFAULT true,   -- false = some da vitrine para os clientes
    criado_em TIMESTAMP     NOT NULL DEFAULT now()   -- usado na ordenação "Novidades"
);


-- ---------------------------------------------------------------------
-- 3) Carrinho: uma linha por lapiseira no carrinho de cada usuário
--
--    A chave primária (usuario_id, lapiseira_id) impede linhas repetidas:
--    adicionar de novo só soma 1 na quantidade (ON CONFLICT no PHP).
--    Excluir o usuário ou a lapiseira apaga as linhas dela (CASCADE).
--    Lapiseira que sai da vitrine continua aqui ("Fora de estoque").
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS carrinho_itens (
    usuario_id   INT NOT NULL REFERENCES usuarios(id)   ON DELETE CASCADE,
    lapiseira_id INT NOT NULL REFERENCES lapiseiras(id) ON DELETE CASCADE,
    quantidade   INT NOT NULL DEFAULT 1 CHECK (quantidade > 0),
    PRIMARY KEY (usuario_id, lapiseira_id)
);


-- ---------------------------------------------------------------------
-- 4) Dados de exemplo (preços fictícios)
--    Só entram se a tabela de lapiseiras estiver vazia.
-- ---------------------------------------------------------------------
INSERT INTO lapiseiras (modelo, marca, bitola, preco)
SELECT * FROM (VALUES
    ('800',                'Rotring',   0.5, 389.90),
    ('Orenz Nero',         'Pentel',    0.3, 259.00),
    ('Graph 1000',         'Pentel',    0.5, 129.90),
    ('Kuru Toga Advance',  'Uni',       0.7, 119.00),
    ('Mars Technico 780C', 'Staedtler', 2.0,  89.90)
) AS exemplos
WHERE NOT EXISTS (SELECT 1 FROM lapiseiras);


-- ---------------------------------------------------------------------
-- 5) Primeiro administrador
--    Toda conta nasce como 'cliente'. Crie a sua pelo site e rode:
-- ---------------------------------------------------------------------
-- UPDATE usuarios SET papel = 'admin' WHERE email = 'seu@email.com';
--
-- Depois disso, os outros papéis podem ser trocados pela tela Usuários.
