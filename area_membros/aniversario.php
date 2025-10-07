<?php
$page_title = 'Aniversariantes';
require_once('includes/header_membros.php');
require_once('../includes/db_connect.php');
date_default_timezone_set('America/Sao_Paulo');

// Lógica para buscar aniversariantes do mês atual e do próximo
$mes_atual = date('m');
$proximo_mes = date('m', strtotime('+1 month'));

// A consulta busca aniversariantes cujo MÊS(data_nascimento) seja o atual OU o próximo
// E ordena por MÊS e depois por DIA
$sql = "SELECT nome_completo, data_nascimento 
        FROM membros 
        WHERE MONTH(data_nascimento) = ? OR MONTH(data_nascimento) = ?
        ORDER BY MONTH(data_nascimento), DAY(data_nascimento) ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$mes_atual, $proximo_mes]);
$aniversariantes = $stmt->fetchAll();

// Cria um formatador de data para exibir "dd de MMMM" (ex: 07 de Novembro)
$formatter = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    'America/Sao_Paulo',
    IntlDateFormatter::GREGORIAN,
    'dd \'de\' MMMM' // Formato personalizado
);
?>

<h1 class="painel-title">Aniversariantes do Mês e do Próximo Mês</h1>

<div class="table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nome Completo</th>
                <th>Data de Aniversário</th>
                <th>Idade a completar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aniversariantes)): ?>
                <tr>
                    <td colspan="3" style="text-align:center; padding: 2rem;">Nenhum aniversariante encontrado para este período.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($aniversariantes as $aniversariante): ?>
                <tr>
                    <td><?php echo htmlspecialchars($aniversariante['nome_completo']); ?></td>
                    <td><?php echo $formatter->format(strtotime($aniversariante['data_nascimento'])); ?></td>
                    <td>
                        <?php
                            // --- LÓGICA PARA CALCULAR A IDADE ---
                            $data_nasc = new DateTime($aniversariante['data_nascimento']);
                            $hoje = new DateTime('today');
                            $idade = $data_nasc->diff($hoje)->y;
                            echo $idade + 1; // Mostra a idade que a pessoa irá completar
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once('includes/footer_membros.php'); ?>