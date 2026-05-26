<?php
// ================================================
// login.php - Tela de login do sistema
// ================================================

// Inicia a sessão para armazenar dados do usuário logado
session_start();

// Inclui a conexão com o banco de dados
require_once '../config/database.php';

$erro = ''; // Armazena mensagens de erro

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura e limpa os dados enviados
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // --- Validação dos campos ---
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';

    } else {
        // Busca o usuário pelo e-mail usando prepared statement
        $stmt = $conexao->prepare(
            "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        // Verifica se o usuário existe e se a senha confere
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido: salva dados na sessão
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Redireciona para a listagem de usuários
            header('Location: listar.php?msg=Bem-vindo,+' . urlencode($usuario['nome']) . '!&tipo=sucesso');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Usuários</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <!-- Card de login -->
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <!-- Ícone e título -->
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
                        <h2 class="h4 mt-2">Acesso ao Sistema</h2>
                        <p class="text-muted small">Informe suas credenciais para entrar</p>
                    </div>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($erro) ?>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Formulário de login -->
                    <form method="POST" action="">

                        <!-- Campo: E-mail -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="seu@email.com" required>
                            </div>
                        </div>

                        <!-- Campo: Senha -->
                        <div class="mb-4">
                            <label for="senha" class="form-label fw-semibold">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="senha" name="senha"
                                       placeholder="Sua senha" required>
                            </div>
                        </div>

                        <!-- Botão de login -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                            </button>
                        </div>

                    </form>

                </div>

                <!-- Link para cadastro -->
                <div class="card-footer text-center bg-white py-3">
                    <span class="text-muted">Não tem conta?</span>
                    <a href="cadastrar.php" class="text-decoration-none fw-semibold">Cadastre-se</a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
