<?php
// ================================================
// cadastrar.php - Formulário e processamento do cadastro (CREATE)
// ================================================

// Inclui a conexão com o banco de dados
require_once '../config/database.php';

$erro = ''; // Armazena mensagens de erro do processamento

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura e limpa os dados enviados pelo formulário
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo  = $_POST['tipo']  ?? 'usuario';

    // Garante que o tipo seja um valor válido (evita dados inválidos)
    if (!in_array($tipo, ['usuario', 'admin'])) {
        $tipo = 'usuario';
    }

    // --- Validação dos campos ---

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos obrigatórios.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';

    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';

    } else {
        // Criptografa a senha com bcrypt (seguro, nunca salve senha em texto puro)
        $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

        // Usa prepared statement para evitar SQL Injection
        $stmt = $conexao->prepare(
            "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $nome, $email, $senhaCriptografada, $tipo);

        if ($stmt->execute()) {
            // Cadastro realizado: redireciona para a listagem com mensagem de sucesso
            header('Location: listar.php?msg=Usuário+cadastrado+com+sucesso!&tipo=sucesso');
            exit;
        } else {
            // Erro ao inserir (provavelmente e-mail duplicado)
            $erro = 'Erro ao cadastrar. O e-mail informado pode já estar em uso.';
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
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
            <i class="bi bi-person-plus-fill me-2"></i>Cadastrar Usuário
        </h1>
        <a href="listar.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <?php if ($erro): ?>
        <!-- Exibe a mensagem de erro, se houver -->
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($erro) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card com o formulário -->
    <div class="card shadow-sm">
        <div class="card-body p-4">

            <!-- Formulário de cadastro -->
            <form method="POST" action="" id="formCadastro">

                <!-- Campo: Nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                               placeholder="Nome completo" required>
                    </div>
                </div>

                <!-- Campo: E-mail -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="email@exemplo.com" required>
                    </div>
                </div>

                <!-- Campo: Senha -->
                <div class="mb-3">
                    <label for="senha" class="form-label fw-semibold">Senha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha"
                               placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="form-text">
                        <i class="bi bi-shield-check me-1"></i>A senha será armazenada de forma criptografada.
                    </div>
                </div>

                <!-- Campo: Tipo de usuário -->
                <div class="mb-4">
                    <label for="tipo" class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="usuario" <?= (($_POST['tipo'] ?? 'usuario') === 'usuario') ? 'selected' : '' ?>>
                                Usuário
                            </option>
                            <option value="admin" <?= (($_POST['tipo'] ?? '') === 'admin') ? 'selected' : '' ?>>
                                Admin
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Botão de envio -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Cadastrar
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
