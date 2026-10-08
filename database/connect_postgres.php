<?php
/**
 * Conexão com o banco (PostgreSQL)
 *
 * Carregado pelo includes/functions.php: toda página já tem a variável
 * $conexao pronta para usar nas consultas.
 * A estrutura das tabelas está em database/estrutura.sql.
 */

$host = "192.168.10.34";
$dbname = "lapisari";
$user = "postgres";
$pass = "Lucas_210978";

// try/catch: se algo dentro do try der erro, o PHP pula para o catch em vez de parar com erro
try {
    // new PDO(): abre a conexão. "pgsql:" diz que o banco é PostgreSQL
    $conexao = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();   // getMessage(): o motivo do erro (senha errada, banco fora do ar...)
    exit;   // sem conexão nenhuma página funciona, então paramos aqui
}
