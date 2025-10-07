<?php
/**
 * Funções de Segurança Essenciais
 */

/**
 * Valida e move um arquivo enviado via formulário de forma segura.
 *
 * @param array $arquivo O array do arquivo de $_FILES (ex: $_FILES['imagem']).
 * @param string $diretorio_destino O caminho para a pasta de destino (ex: '../../uploads/').
 * @param array $extensoes_permitidas Array com as extensões permitidas (ex: ['jpg', 'png']).
 * @param int $tamanho_maximo_mb Tamanho máximo do arquivo em Megabytes.
 * @return array Retorna ['sucesso' => true, 'caminho_relativo' => 'caminho/salvo.jpg'] ou ['sucesso' => false, 'erro' => 'Mensagem de erro'].
 */
function salvarArquivoSeguro($arquivo, $diretorio_destino, $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'pdf'], $tamanho_maximo_mb = 5) {
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['sucesso' => false, 'erro' => 'Erro no upload. Código: ' . $arquivo['error']];
    }

    $nome_arquivo = $arquivo['name'];
    $tamanho_arquivo = $arquivo['size'];
    $caminho_temporario = $arquivo['tmp_name'];

    // 1. Validação de Tamanho
    $tamanho_maximo_bytes = $tamanho_maximo_mb * 1024 * 1024;
    if ($tamanho_arquivo > $tamanho_maximo_bytes) {
        return ['sucesso' => false, 'erro' => "O arquivo excede o tamanho máximo de {$tamanho_maximo_mb}MB."];
    }

    // 2. Validação de Extensão
    $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoes_permitidas)) {
        return ['sucesso' => false, 'erro' => 'Tipo de arquivo não permitido.'];
    }

    // 3. Verificação de tipo MIME real (mais seguro que apenas a extensão)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $caminho_temporario);
    finfo_close($finfo);

    $mime_permitidos = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm'
    ];

    if (!isset($mime_permitidos[$extensao]) || $mime_permitidos[$extensao] !== $mime_type) {
         return ['sucesso' => false, 'erro' => 'Conteúdo do arquivo parece ser inválido para a extensão fornecida.'];
    }

    // 4. Gerar nome de arquivo único e seguro para evitar conflitos e ataques
    $novo_nome = bin2hex(random_bytes(16)) . '.' . $extensao;
    $caminho_final = rtrim($diretorio_destino, '/') . '/' . $novo_nome;

    if (move_uploaded_file($caminho_temporario, $caminho_final)) {
        // Retorna o caminho relativo a partir da raiz do site
        return ['sucesso' => true, 'caminho_relativo' => str_replace('../../', '', $caminho_final)];
    }

    return ['sucesso' => false, 'erro' => 'Falha ao mover o arquivo para o destino final. Verifique as permissões da pasta.'];
}

/**
 * Gera e armazena um token CSRF na sessão do usuário.
 * @return string O token gerado.
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida o token CSRF enviado contra o armazenado na sessão.
 * Mata a execução do script se a validação falhar.
 * @param string $token_enviado O token recebido via POST ou GET.
 */
function validarTokenCSRF($token_enviado) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token_enviado)) {
        // Log do erro (idealmente)
        error_log('Falha na validação do token CSRF.');
        // Mata a execução para o usuário
        die('Acesso inválido ou formulário expirado. Por favor, tente novamente.');
    }
    // Opcional: Invalida o token após o uso para maior segurança (Single-Use Token)
    unset($_SESSION['csrf_token']);
}

?>