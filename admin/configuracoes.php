<?php
$page_title = 'Configurações Gerais';
// O 'check_login.php' já inicia a sessão segura
require_once('includes/check_login.php');
// Incluímos as funções de segurança para gerar o token
require_once('../includes/security_functions.php');
require_once('../includes/db_connect.php');
require_once('includes/header_admin.php');

// Busca todas as configurações e as coloca em um array fácil de usar
$configs = [];
$stmt = $pdo->query("SELECT config_nome, config_valor FROM configuracoes");
while ($row = $stmt->fetch()) {
    $configs[$row['config_nome']] = $row['config_valor'];
}

// Gera o token de segurança para o formulário
$csrf_token = gerarTokenCSRF();
?>

<div class="container">
    <h1 class="admin-title">Configurações Gerais do Site</h1>
    <p>Altere as informações abaixo para atualizar dados importantes que aparecem em todo o site.</p>

    <form action="scripts/configuracoes_save" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <h3>Informações de Contato</h3>
        <div class="form-group">
            <label for="endereco">Endereço Completo</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($configs['endereco'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="telefone">Telefone Principal (para exibição)</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($configs['telefone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email_contato">E-mail de Contato</label>
            <input type="email" id="email_contato" name="email_contato" value="<?php echo htmlspecialchars($configs['email_contato'] ?? ''); ?>">
        </div>

        <hr class="separator">

        <h3>Links das Redes Sociais</h3>
        <div class="form-group">
            <label for="link_whatsapp">Link do WhatsApp (URL completa)</label>
            <input type="url" id="link_whatsapp" name="link_whatsapp" value="<?php echo htmlspecialchars($configs['link_whatsapp'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="link_instagram">Link do Instagram (URL completa)</label>
            <input type="url" id="link_instagram" name="link_instagram" value="<?php echo htmlspecialchars($configs['link_instagram'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="link_facebook">Link do Facebook (URL completa)</label>
            <input type="url" id="link_facebook" name="link_facebook" value="<?php echo htmlspecialchars($configs['link_facebook'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="link_youtube">Link do YouTube (URL completa)</label>
            <input type="url" id="link_youtube" name="link_youtube" value="<?php echo htmlspecialchars($configs['link_youtube'] ?? ''); ?>">
        </div>

        <hr class="separator">

        <h3>Informações de Doação</h3>
        <div class="form-group">
            <label for="pix_chave">Chave PIX (Ex: CNPJ, Email, Telefone)</label>
            <input type="text" id="pix_chave" name="pix_chave" value="<?php echo htmlspecialchars($configs['pix_chave'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="pix_qrcode">Imagem do QR Code do PIX</label>
            <input type="file" id="pix_qrcode" name="pix_qrcode" accept="image/png, image/jpeg, image/webp">
            <?php if (!empty($configs['pix_qrcode_url'])): ?>
                <p>Imagem atual: <img src="../<?php echo htmlspecialchars($configs['pix_qrcode_url']); ?>" alt="QR Code atual" style="max-width: 150px; margin-top: 10px;"></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="banco_nome">Nome do Banco</label>
            <input type="text" id="banco_nome" name="banco_nome" value="<?php echo htmlspecialchars($configs['banco_nome'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banco_agencia">Agência</label>
            <input type="text" id="banco_agencia" name="banco_agencia" value="<?php echo htmlspecialchars($configs['banco_agencia'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banco_conta">Conta Corrente</label>
            <input type="text" id="banco_conta" name="banco_conta" value="<?php echo htmlspecialchars($configs['banco_conta'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banco_titular">Nome do Titular da Conta</label>
            <input type="text" id="banco_titular" name="banco_titular" value="<?php echo htmlspecialchars($configs['banco_titular'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banco_cnpj">CNPJ</label>
            <input type="text" id="banco_cnpj" name="banco_cnpj" value="<?php echo htmlspecialchars($configs['banco_cnpj'] ?? ''); ?>">
        </div>

        <button type="submit" class="btn">Salvar Configurações</button>
    </form>
</div>

<?php require_once('includes/footer_admin.php'); ?>