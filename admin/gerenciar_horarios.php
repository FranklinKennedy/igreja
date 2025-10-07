<?php
$page_title = 'Gerenciar Horários dos Cultos';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token CSRF para o formulário e os links
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<h1 class="admin-title">Gerenciar Horários de Cultos</h1>
<p>Adicione, remova ou edite os dias e horários dos cultos que aparecem na página inicial.</p>

<div class="grid-container">
    <div class="form-container">
        <h3>Adicionar Novo Horário</h3>
        <form action="scripts/horario_save.php" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label for="dia_semana">Dia da Semana</label>
                <input type="text" id="dia_semana" name="dia_semana" placeholder="Ex: Domingo" required>
            </div>
            <div class="form-group">
                <label for="horario_descricao">Horário e Descrição</label>
                <input type="text" id="horario_descricao" name="horario_descricao" placeholder="Ex: 19h (Culto da Família)" required>
            </div>
            <div class="form-group">
                <label for="ordem">Ordem de Exibição</label>
                <input type="number" id="ordem" name="ordem" value="0" step="0.1" required>
                <small>Menor número aparece primeiro. Use 1 para Domingo, 2 para Segunda, etc.</small>
            </div>
            <button type="submit" class="btn">Adicionar Horário</button>
        </form>
    </div>
    <div class="list-container">
        <h3>Horários Atuais</h3>
        <table class="admin-table">
            <thead>
                <tr><th>Ordem</th><th>Dia</th><th>Descrição</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, dia_semana, horario_descricao, ordem FROM horarios_cultos ORDER BY ordem ASC");
                while ($horario = $stmt->fetch()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($horario['ordem']); ?></td>
                    <td><strong><?php echo htmlspecialchars($horario['dia_semana']); ?></strong></td>
                    <td><?php echo htmlspecialchars($horario['horario_descricao']); ?></td>
                    <td class="actions-cell">
                        <a href="form_horario.php?id=<?php echo $horario['id']; ?>" class="admin-action-edit">Editar</a>
                        <a href="scripts/horario_delete.php?id=<?php echo $horario['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once('includes/footer_admin.php'); ?>