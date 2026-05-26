<?php
// ================================================
// excluir.php - Remove um usuário do banco (DELETE)
// ================================================

// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// Obtém e valida o ID da URL
$id = intval($_GET['id'] ?? 0);

// Rejeita IDs inválidos (0 ou negativos)
if ($id <= 0) {
    header('Location: listar.php?msg=ID+inválido.&tipo=erro');
    exit;
}

// Exclui o usuário pelo ID usando prepared statement (evita SQL Injection)
$stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

// Verifica se algum registro foi realmente deletado
if ($stmt->affected_rows > 0) {
    header('Location: listar.php?msg=Usuário+excluído+com+sucesso!&tipo=sucesso');
} else {
    header('Location: listar.php?msg=Usuário+não+encontrado+ou+já+excluído.&tipo=erro');
}

$stmt->close();
exit; // Encerra o script após o redirecionamento
