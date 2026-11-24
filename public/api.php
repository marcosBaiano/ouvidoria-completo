<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Router Principal da API
 * @author Sistema de Ouvidoria
 */

// Headers necessários
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Autoloader de classes
spl_autoload_register(function ($class) {
    $dirs = [
        __DIR__ . '/../core/',
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/'
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Obter URI e método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Router simples
if (strpos($uri, '/api/denuncias') === 0) {
    require_once __DIR__ . '/../controllers/DenunciaController.php';
    $controller = new DenunciaController();
    $controller->route();
} elseif ($uri === '/api' || $uri === '/') {
    $response = new Response(200, [
        'nome' => APP_NAME,
        'versao' => APP_VERSION,
        'endpoints' => [
            'POST /api/denuncias' => 'Criar nova denúncia',
            'GET /api/denuncias' => 'Listar denúncias',
            'GET /api/denuncias/{id}' => 'Obter denúncia por ID',
            'GET /api/denuncias/protocolo/{numero}' => 'Obter denúncia por protocolo',
            'PUT /api/denuncias/{id}' => 'Atualizar denúncia',
            'POST /api/denuncias/{id}/evidencias' => 'Upload de evidência',
            'GET /api/denuncias/stats' => 'Obter estatísticas'
        ]
    ]);
  
    $response->send();
} else {
    $response = new Response(404, null, 'Endpoint não encontrado');
    $response->send();
}
?>

/**
 * Router Principal da API
 * @author Sistema de Ouvidoria
 */

// Headers necessários
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Autoloader de classes
spl_autoload_register(function ($class) {
    $dirs = [
        __DIR__ . '/../core/',
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/'
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Obter URI e método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Router simples
if (strpos($uri, '/api/denuncias') === 0) {
    require_once __DIR__ . '/../controllers/DenunciaController.php';
    $controller = new DenunciaController();
    $controller->route();
} elseif ($uri === '/api' || $uri === '/') {
    $response = new Response(200, [
        'nome' => APP_NAME,
        'versao' => APP_VERSION,
        'endpoints' => [
            'POST /api/denuncias' => 'Criar nova denúncia',
            'GET /api/denuncias' => 'Listar denúncias',
            'GET /api/denuncias/{id}' => 'Obter denúncia por ID',
            'GET /api/denuncias/protocolo/{numero}' => 'Obter denúncia por protocolo',
            'PUT /api/denuncias/{id}' => 'Atualizar denúncia',
            'POST /api/denuncias/{id}/evidencias' => 'Upload de evidência',
            'GET /api/denuncias/stats' => 'Obter estatísticas'
        ]
    ]);
  
    $response->send();
} else {
    $response = new Response(404, null, 'Endpoint não encontrado');
    $response->send();
}

?>
