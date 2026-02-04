<?php
require_once 'config/database.php';
verificar_admin(); // BLOQUEIO: Apenas Admin entra aqui
include 'includes/header.php';

// Captura mensagens da URL
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';

// Busca todos os usuários
$sql = "SELECT id, usuario, email, nivel FROM usuarios ORDER BY usuario ASC";
$resultado = mysqli_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-warning"><i class="bi bi-people"></i> Gerenciar Usuários</h2>
    <a href="usuarios_cadastrar.php" class="btn btn-warning shadow-sm">
        <i class="bi bi-person-plus-fill"></i> Novo Usuário
    </a>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $msg ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $erro ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($user['usuario']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['nivel'] == 'Admin'): ?>
                                <span class="badge bg-danger">ADMIN</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Comum</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="usuarios_editar.php?   id=<?= $user['id']; ?>" 
                                class="btn btn-sm btn-outline-warning"
                                onclick="return confirm('Tem certeza que deseja editar o usuário <?= htmlspecialchars($user['usuario']); ?>?');">
                                <i class="bi bi-pencil"></i> Editar
                            </a>                            
                            <?php if ($user['id'] != $_SESSION['usuario_id']): ?>
                                <a href="processa.php?acao=excluir_usuario&id=<?= $user['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars($user['usuario']); ?>?');">
                                    <i class="bi bi-trash"></i> Excluir
                                </a>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Você</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
?>