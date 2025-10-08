<?php
$page_title = 'Gerenciar Banners';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

$csrf_token = gerarTokenCSRF();
require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Banners da Home</h1>
    <a href="form_banner" class="btn admin-btn-add">Adicionar Novo Banner</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Mídia</th>
                <th>Título</th>
                <th>Dispositivo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, titulo, subtitulo, imagem_url, ativo, tipo_dispositivo FROM banners ORDER BY tipo_dispositivo, ativo DESC, id DESC");
            while ($banner = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <?php
                    if (!empty($banner['imagem_url'])) {
                        $is_video = preg_match('/\.(mp4|webm)$/i', $banner['imagem_url']);
                        if ($is_video) {
                            echo '<video src="../' . htmlspecialchars($banner['imagem_url']) . '" class="admin-table-img" preload="metadata"></video>';
                        } else {
                            echo '<img src="../' . htmlspecialchars($banner['imagem_url']) . '" alt="Imagem do Banner" class="admin-table-img">';
                        }
                    } else {
                        echo '<span>Sem Mídia</span>';
                    }
                    ?>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($banner['titulo']); ?></strong>
                    <br>
                    <small><?php echo htmlspecialchars($banner['subtitulo']); ?></small>
                </td>
                <td>
                    <strong style="text-transform: capitalize;"><?php echo htmlspecialchars($banner['tipo_dispositivo']); ?></strong>
                </td>
                <td>
                    <?php if ($banner['ativo']): ?>
                        <span class="status-active">Ativo</span>
                    <?php else: ?>
                        <span class="status-inactive">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="actions-cell">
                    <a href="form_banner?id=<?php echo $banner['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/banner_delete?id=<?php echo $banner['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('Tem certeza que deseja excluir este banner?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>