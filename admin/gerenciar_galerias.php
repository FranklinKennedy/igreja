<?php
$page_title = 'Gerenciar Galerias de Fotos';
require_once('includes/check_login.php');
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');

// Gera o token CSRF para os links de exclusão
$csrf_token = gerarTokenCSRF();

require_once('includes/header_admin.php');
?>

<div class="container">
    <h1 class="admin-title">Gerenciar Galerias</h1>
    <a href="form_galeria" class="btn admin-btn-add">Criar Nova Galeria</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagem de Capa</th>
                <th>Título da Galeria</th>
                <th>Data</th>
                <th>Qtd. de Fotos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT g.id, g.titulo, g.data_galeria, g.imagem_capa_url, COUNT(f.id) as total_fotos
                    FROM galerias g
                    LEFT JOIN fotos f ON g.id = f.galeria_id
                    GROUP BY g.id, g.titulo, g.data_galeria, g.imagem_capa_url
                    ORDER BY g.data_galeria DESC";
            
            $stmt = $pdo->query($sql);

            while ($galeria = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <?php if (!empty($galeria['imagem_capa_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($galeria['imagem_capa_url']); ?>" alt="Imagem de Capa" class="admin-table-img">
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($galeria['titulo']); ?></strong></td>
                <td><?php echo date('d/m/Y', strtotime($galeria['data_galeria'])); ?></td>
                <td><?php echo $galeria['total_fotos']; ?></td>
                <td class="actions-cell">
                    <a href="gerenciar_fotos?galeria_id=<?php echo $galeria['id']; ?>" class="admin-action-manage">Gerenciar Fotos</a>
                    <a href="form_galeria?id=<?php echo $galeria['id']; ?>" class="admin-action-edit">Editar</a>
                    <a href="scripts/galeria_delete?id=<?php echo $galeria['id']; ?>&token=<?php echo $csrf_token; ?>" class="admin-action-delete" onclick="return confirm('ATENÇÃO: Excluir uma galeria também apagará TODAS as fotos dentro dela. Deseja continuar?');">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_admin.php'); ?>