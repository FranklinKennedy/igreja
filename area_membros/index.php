<?php
$page_title = 'Minha Escala';
require_once('includes/header_membros.php');
require_once('../includes/db_connect.php');
date_default_timezone_set('America/Sao_Paulo');


// Pega o ID do membro logado na sessão
$membro_id = $_SESSION['membro_id'];

// Prepara uma consulta SQL complexa para buscar as escalas do membro
// A consulta junta 4 tabelas para pegar todas as informações necessárias
$sql = "SELECT 
            e.titulo as escala_titulo,
            e.data_escala,
            f.nome_funcao
        FROM escala_atribuicoes ea
        JOIN escalas e ON ea.escala_id = e.id
        JOIN escala_funcoes f ON ea.funcao_id = f.id
        WHERE ea.membro_id = ? AND e.data_escala >= CURDATE()
        ORDER BY e.data_escala ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$membro_id]);
$minhas_escalas = $stmt->fetchAll();

// Cria um formatador de data para o Português do Brasil (método moderno)
$formatter = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::FULL, // Formato da data (ex: domingo, 28 de setembro de 2025)
    IntlDateFormatter::SHORT, // Formato da hora (ex: 08:30)
    'America/Sao_Paulo'
);
?>

<h1 class="painel-title">Bem-vindo, <?php echo htmlspecialchars($_SESSION['membro_nome']); ?>!</h1>
<p>Aqui estão suas próximas escalas. Fique atento às datas e horários.</p>

<div class="minha-escala-container">
    <h2>Minhas Próximas Escalas</h2>

    <?php if (empty($minhas_escalas)): ?>
        <div class="escala-card-vazio">
            <p>Você não está escalado para nenhum próximo evento. Fique de olho para novidades!</p>
        </div>
    <?php else: ?>
        <?php foreach ($minhas_escalas as $escala): ?>
            <div class="escala-card">
                <div class="escala-data">
                    <?php echo ucfirst($formatter->format(strtotime($escala['data_escala']))); ?>
                </div>
                <div class="escala-info">
                    <span class="escala-titulo"><?php echo htmlspecialchars($escala['escala_titulo']); ?></span>
                    <span class="escala-funcao">Sua Função: <strong><?php echo htmlspecialchars($escala['nome_funcao']); ?></strong></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<?php require_once('includes/footer_membros.php'); ?>