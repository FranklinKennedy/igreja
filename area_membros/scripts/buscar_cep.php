<?php
// Define o cabeçalho para indicar que a resposta é em formato JSON
header('Content-Type: application/json');

// Inclui a conexão com o banco de dados
require_once('../../includes/db_connect.php');

// Pega o CEP da URL (ex: buscar_cep.php?cep=75340000)
$cep = isset($_GET['cep']) ? $_GET['cep'] : '';

// Limpa o CEP, deixando apenas os números
$cep_limpo = preg_replace('/[^0-9]/', '', $cep);

$response = ['erro' => true]; // Resposta padrão de erro

if (strlen($cep_limpo) === 8) {
    try {
        // Formata o CEP para o padrão com hífen (ex: 75340-000) para buscar no banco
        $cep_formatado = substr($cep_limpo, 0, 5) . '-' . substr($cep_limpo, 5, 3);

        $sql = "SELECT logradouro, bairro, cidade, estado as uf FROM ceps_hidrolandia WHERE cep = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cep_formatado]);
        $data = $stmt->fetch();

        if ($data) {
            // Se encontrou, a resposta é de sucesso
            $response = $data;
            $response['erro'] = false; // Adicionamos a chave 'erro' para compatibilidade
        }

    } catch (PDOException $e) {
        // Se der erro no banco, a resposta continua como erro
        error_log($e->getMessage());
    }
}

// Imprime a resposta em formato JSON
echo json_encode($response);
?>