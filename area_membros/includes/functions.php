<?php
/**
 * Capitaliza um nome próprio de forma inteligente para o português.
 * Transforma "JOSÉ DA SILVA" em "José da Silva".
 *
 * @param string $nome O nome completo a ser formatado.
 * @return string O nome formatado corretamente.
 */
function capitalizarNomeProprio($nome) {
    // Converte tudo para minúsculas para padronizar
    $nome = mb_strtolower($nome, 'UTF-8');
    
    // Capitaliza a primeira letra de cada palavra
    $nome = mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8');
    
    // Lista de palavras que devem ficar em minúsculas
    $excecoes = [' De ', ' Da ', ' Do ', ' Dos ', ' Das ', ' E '];
    $excecoes_minusculas = [' de ', ' da ', ' do ', ' dos ', ' das ', ' e '];
    
    // Substitui as exceções de volta para minúsculas
    $nome = str_replace($excecoes, $excecoes_minusculas, $nome);
    
    return $nome;
}
?>
