<?php
$page_title = 'Gerenciar Funções da Escala';
require_once('includes/header_membros.php');
if ($nivel_acesso != 1) { die('Acesso negado.'); }

require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');
date_default_timezone_set('America/Sao_Paulo');

$csrf_token = gerarTokenCSRF();
?>

<h1 class="painel-title">Gerenciar Funções</h1>
<p>Aqui você pode adicionar ou remover as tarefas que aparecerão ao montar uma escala.</p>

<div class="grid-container">
    <div class="form-container">
        <h3>Adicionar Nova Função</h3>
        <form action="scripts/funcao_save.php" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label for="nome_funcao">Nome da Função</label>
                <input type="text" id="nome_funcao" name="nome_funcao" required>
            </div>
            <button type="submit" class="btn">Adicionar Função</button>
        </form>
    </div>
    <div class="list-container">
        <h3>Funções Existentes</h3>
        <table class="admin-table">
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, nome_funcao FROM escala_funcoes ORDER BY nome_funcao ASC");
                while ($funcao = $stmt->fetch()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($funcao['nome_funcao']); ?></td>
                    <td style="text-align: right;">
                        <a href="scripts/funcao_delete.php?id=<?php echo $funcao['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once('includes/footer_membros.php'); ?>