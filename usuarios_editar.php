<?php
require_once 'config/database.php';
verificar_admin(); // BLOQUEIO: Apenas Admin entra aqui
include 'includes/header.php';

$id_usuario = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_usuario) {
    header("Location: usuarios.php?erro=ID de usuário inválido.");
    exit();
}

// 1. Busca os dados atuais do usuário
$stmt = mysqli_prepare($conn, "SELECT id, usuario, email, nivel FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($resultado);

if (!$user_data) {
    header("Location: usuarios.php?erro=Usuário não encontrado.");
    exit();
}

// Verifica se o admin está tentando editar a si mesmo para não remover a verificação de senha
$is_self_edit = ($user_data['id'] == $_SESSION['usuario_id']);
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4 text-warning"><i class="bi bi-pencil"></i> Editar Usuário: <?= htmlspecialchars($user_data['usuario']); ?></h2>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="editar_usuario">
                    <input type="hidden" name="id" value="<?= $user_data['id']; ?>"> 

                    <div class="mb-3">
                        <label class="form-label fw-bold">Login (Nome de Usuário)</label>
                        <input type="text" name="usuario" class="form-control" required placeholder="Ex: joao" value="<?= htmlspecialchars($user_data['usuario']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Ex: joao@email.com" value="<?= htmlspecialchars($user_data['email']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nova Senha (Deixe em branco para não alterar)</label>
                        <input type="password" name="senha" class="form-control" minlength="6">
                        <div class="form-text">Preencha apenas se quiser mudar a senha.</div>
                    </div>

                    <?php if (!$is_self_edit): // O admin não deve poder mudar seu próprio nível ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nível de Acesso</label>
                        <select name="nivel" class="form-select" required>
                            <option value="Comum" <?= ($user_data['nivel'] == 'Comum') ? 'selected' : ''; ?>>Comum (Só vê os próprios treinos)</option>
                            <option value="Admin" <?= ($user_data['nivel'] == 'Admin') ? 'selected' : ''; ?>>Admin (Gerencia tudo)</option>
                        </select>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info small mt-3">Para sua segurança, seu nível de acesso não pode ser alterado através desta tela.</div>
                        <input type="hidden" name="nivel" value="<?= htmlspecialchars($user_data['nivel']); ?>">
                    <?php endif; ?>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="usuarios.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>