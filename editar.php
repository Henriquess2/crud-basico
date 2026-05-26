<?php
// ================================================
// editar.php - Formulário e processamento de edição (UPDATE)
// ================================================

// Inclui a conexão com o banco de dados
require_once '../config/database.php';

// Obtém e valida o ID da URL (intval evita valores não numéricos)
$id = intval($_GET['id'] ?? 0);

// Se o ID for inválido, redireciona com erro
if ($id <= 0) {
    header('Location: listar.php?msg=ID+inválido.&tipo=erro');
    exit;
}

// Busca os dados atuais do usuário pelo ID (prepared statement)
$stmt = $conexao->prepare(
    "SELECT id, nome, email, tipo FROM usuarios WHERE id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Se não encontrou o usuário, redireciona
if (!$usuario) {
    header('Location: listar.php?msg=Usuário+não+encontrado.&tipo=erro');
    exit;
}

$erro = ''; // Armazena mensagem de erro

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura e limpa os dados do formulário
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo  = $_POST['tipo']  ?? 'usuario';
    $senha = $_POST['senha'] ?? ''; // Senha é opcional na edição

    // Garante que o tipo seja válido
    if (!in_array($tipo, ['usuario', 'admin'])) {
        $tipo = 'usuario';
    }

    // --- Validação ---

    if (empty($nome) || empty($email)) {
        $erro = 'Nome e e-mail são obrigatórios.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';

    } elseif ($senha !== '' && strlen($senha) < 6) {
        // Só valida a senha se ela foi preenchida
        $erro = 'Se informar nova senha, ela deve ter no mínimo 6 caracteres.';

    } else {

        // Se uma nova senha foi informada, inclui na atualização
        if ($senha !== '') {
            // Criptografa a nova senha
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare(
                "UPDATE usuarios SET nome=?, email=?, senha=?, tipo=? WHERE id=?"
            );
            $stmt->bind_param('ssssi', $nome, $email, $senhaCriptografada, $tipo, $id);
        } else {
            // Atualiza sem alterar a senha
            $stmt = $conexao->prepare(
                "UPDATE usuarios SET nome=?, email=?, tipo=? WHERE id=?"
            );
            $stmt->bind_param('sssi', $nome, $email, $tipo, $id);
        }

        if ($stmt->execute()) {
            // Sucesso: redireciona para a listagem
            header('Location: listar.php?msg=Usuário+atualizado+com+sucesso!&tipo=sucesso');
            exit;
        } else {
            $erro = 'Erro ao atualizar. O e-mail informado pode já estar em uso.';
        }

        $stmt->close();
    }

    // Atualiza o array local para re-exibir os dados corrigidos no formulário
    $usuario['nome']  = $nome;
    $usuario['email'] = $email;
    $usuario['tipo']  = $tipo;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
            <i class="bi bi-pencil-square me-2"></i>Editar Usuário #<?= $id ?>
        </h1>
        <a href="listar.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($erro) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card com o formulário -->
    <div class="card shadow-sm">
        <div class="card-body p-4">

            <!-- Formulário de edição preenchido com os dados atuais -->
            <form method="POST" action="" id="formEdicao">

                <!-- Campo: Nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>
                </div>

                <!-- Campo: E-mail -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                </div>

                <!-- Campo: Nova senha (opcional) -->
                <div class="mb-3">
                    <label for="senha" class="form-label fw-semibold">Nova Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha"
                               placeholder="Deixe em branco para manter a senha atual" minlength="6">
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>Preencha somente se quiser alterar a senha.
                    </div>
                </div>

                <!-- Campo: Tipo de usuário -->
                <div class="mb-4">
                    <label for="tipo" class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>
                                Usuário
                            </option>
                            <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Botão de envio -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Salvar Alterações
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript externo -->
<script src="../asstes/js/script.js"></script>
</body>
</html>
