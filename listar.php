<?php
// ================================================
// listar.php - Lista todos os usuários (READ)
// ================================================

// Inclui o arquivo de conexão com o banco
require_once '../config/database.php';

// Busca todos os usuários ordenados pelo mais recente
$resultado = $conexao->query(
    "SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-people-fill me-2"></i>Usuários Cadastrados
        </h1>
        <a href="cadastrar.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Usuário
        </a>
    </div>

    <?php
    // Exibe mensagem de retorno (sucesso ou erro) vinda da URL
    if (isset($_GET['msg'])):
        $tipo = ($_GET['tipo'] ?? 'sucesso') === 'erro' ? 'danger' : 'success';
    ?>
        <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= $tipo === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card com a tabela -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Tipo</th>
                            <th>Cadastrado em</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if ($resultado && $resultado->num_rows > 0): ?>

                            <?php while ($usuario = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td>
                                    <!-- Badge colorido conforme o tipo -->
                                    <?php if ($usuario['tipo'] === 'admin'): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-lock-fill me-1"></i>Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-person-fill me-1"></i>Usuário
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></td>
                                <td class="text-center">
                                    <!-- Link para editar o usuário -->
                                    <a href="editar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <!-- Link para excluir — pede confirmação via JavaScript -->
                                    <a href="excluir.php?id=<?= $usuario['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       title="Excluir"
                                       onclick="return confirmarExclusao()">
                                        <i class="bi bi-trash-fill"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <!-- Exibido quando não há nenhum usuário cadastrado -->
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Nenhum usuário cadastrado ainda.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript externo -->
<script src="../asstes/js/script.js"></script>
</body>
</html>
