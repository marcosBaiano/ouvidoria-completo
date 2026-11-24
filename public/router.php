<?php
/**
 * Router para Sistema de Ouvidoria
 * Funciona com: php -S localhost:8000 -r router.php
 */

$requested_file = $_SERVER["SCRIPT_FILENAME"];
$requested_uri = $_SERVER["REQUEST_URI"];

// Limpar qualquer whitespace anterior  
ob_clean();

// Se for um arquivo real (não é router), serve diretamente
if (file_exists($requested_file) && is_file($requested_file) && $requested_file !== __FILE__) {
	return false; // Deixar PHP servir o arquivo normal
}

// Parse da URI
$uri = parse_url($requested_uri, PHP_URL_PATH);

// Routes da API
if (strpos($uri, '/api') === 0) {
	require_once dirname(__FILE__) . '/api.php';
	exit;
}

// Rota raiz
if ($uri === '/' || $uri === '') {
	require_once dirname(__FILE__) . '/index.html';
	exit;
}

// Outras rotas servem arquivos HTML específicos ou index.html
if (file_exists(__DIR__ . $uri . '.html')) {
	require_once __DIR__ . $uri . '.html';
	exit;
}

// Fallback: serve index.html para SPA
require_once dirname(__FILE__) . '/index.html';
exit;
?>
