<?php
$page_title = 'Doações | Luz para os Povos Hidrolândia';
$page_css = 'doacoes.css';
require_once('includes/header.php');
require_once('includes/db_connect.php');

// Busca todas as configurações do banco
$configs = [];
try {
    $stmt = $pdo->query("SELECT config_nome, config_valor FROM configuracoes");
    while ($row = $stmt->fetch()) {
        $configs[$row['config_nome']] = $row['config_valor'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar configurações para doações: " . $e->getMessage());
}
?>

<section class="page-header">
    <div class="container">
        <h1>Apoie esta Obra</h1>
        <p>Sua contribuição nos ajuda a continuar transformando vidas em Hidrolândia e além.</p>
    </div>
</section>

<section class="donation-section">
    <div class="container">
        <div class="donation-intro">
            <h2>Como Contribuir</h2>
            <p>Agradecemos seu coração generoso em semear neste ministério. Clique em qualquer campo abaixo para copiar a informação.</p>
        </div>

        <div class="donation-methods">
            <div class="donation-card">
                <h3>Contribuição via PIX</h3>
                <p>A forma mais rápida e segura. Aponte a câmera do seu celular para o QR Code ou clique na chave para copiar.</p>
                <img src="<?php echo htmlspecialchars($configs['pix_qrcode_url'] ?? ''); ?>" alt="QR Code do PIX" class="pix-qrcode">
                <div class="pix-key copy-field" data-copy-text="<?php echo htmlspecialchars($configs['pix_chave'] ?? ''); ?>">
                    <strong>Chave PIX:</strong> <?php echo htmlspecialchars($configs['pix_chave'] ?? 'Chave não cadastrada'); ?>
                    <span class="copy-feedback"></span>
                </div>
            </div>

            <div class="donation-card">
                <h3>Transferência Bancária</h3>
                <p>Você também pode contribuir através de depósito ou transferência para nossa conta.</p>
                <ul class="bank-details">
                    <li class="copy-field" data-copy-text="<?php echo htmlspecialchars($configs['banco_nome'] ?? ''); ?>"><strong>Banco:</strong> <span><?php echo htmlspecialchars($configs['banco_nome'] ?? ''); ?></span><span class="copy-feedback"></span></li>
                    <li class="copy-field" data-copy-text="<?php echo htmlspecialchars($configs['banco_agencia'] ?? ''); ?>"><strong>Agência:</strong> <span><?php echo htmlspecialchars($configs['banco_agencia'] ?? ''); ?></span><span class="copy-feedback"></span></li>
                    <li class="copy-field" data-copy-text="<?php echo htmlspecialchars($configs['banco_conta'] ?? ''); ?>"><strong>Conta Corrente:</strong> <span><?php echo htmlspecialchars($configs['banco_conta'] ?? ''); ?></span><span class="copy-feedback"></span></li>
                    <li class="copy-field" data-copy-text="<?php echo htmlspecialchars($configs['banco_titular'] ?? ''); ?>"><strong>Titular:</strong> <span><?php echo htmlspecialchars($configs['banco_titular'] ?? ''); ?></span><span class="copy-feedback"></span></li>
                    <li class="copy-field" data-copy-text="<?php echo htmlspecialchars($configs['banco_cnpj'] ?? ''); ?>"><strong>CNPJ:</strong> <span><?php echo htmlspecialchars($configs['banco_cnpj'] ?? ''); ?></span><span class="copy-feedback"></span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>