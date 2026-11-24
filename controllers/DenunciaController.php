<?php
/**
 * Controlador de Denúncias
 * @author Sistema de Ouvidoria
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../models/DenunciaModel.php';

class DenunciaController {
    private $model;
    private $response;
    private $method;
    private $uri;

    public function __construct() {
        $this->model = new DenunciaModel();
        $this->response = new Response();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Router principal
     */
    public function route() {
        // Registrar nova denúncia
        if ($this->method === 'POST' && strpos($this->uri, '/api/denuncias') === 0) {
            return $this->create();
        }

        // Listar denúncias
        if ($this->method === 'GET' && $this->uri === '/api/denuncias') {
            return $this->list();
        }

        // Obter denúncia específica
        if ($this->method === 'GET' && preg_match('/\/api\/denuncias\/([a-zA-Z0-9-]+)/', $this->uri, $matches)) {
            return $this->getById($matches[1]);
        }

        // Obter por protocolo
        if ($this->method === 'GET' && preg_match('/\/api\/denuncias\/protocolo\/([A-Z0-9]+)/', $this->uri, $matches)) {
            return $this->getByProtocolo($matches[1]);
        }

        // Atualizar denúncia
        if ($this->method === 'PUT' && preg_match('/\/api\/denuncias\/([a-zA-Z0-9-]+)/', $this->uri, $matches)) {
            return $this->update($matches[1]);
        }

        // Upload de evidência
        if ($this->method === 'POST' && preg_match('/\/api\/denuncias\/([a-zA-Z0-9-]+)\/evidencias/', $this->uri, $matches)) {
            return $this->uploadEvidence($matches[1]);
        }

        // Buscar
        if ($this->method === 'GET' && preg_match('/\/api\/denuncias\/buscar/', $this->uri)) {
            return $this->search();
        }

        // Estatísticas
        if ($this->method === 'GET' && $this->uri === '/api/denuncias/stats') {
            return $this->getStats();
        }

        $this->response->notFound()->send();
    }

    /**
     * Criar nova denúncia
     */
    private function create() {
        try {
            // Obter dados JSON
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                return $this->response->badRequest('Dados inválidos')->send();
            }

            // Validação
            $validator = new Validator($input);
            $validator
                ->required('tipo')
                ->required('descricao')
                ->required('local')
                ->enum('tipo', array_keys(TIPOS_DENUNCIA))
                ->enum('local', array_keys(LOCAIS_DENUNCIA))
                ->minLength('descricao', 20);

            if ($validator->hasErrors()) {
                return $this->response
                    ->badRequest('Dados inválidos', $validator->getErrors())
                    ->send();
            }

            // Preparar dados
            $id = Helper::generateUUID();
            $protocolo = Helper::generateProtocolNumber();

            $data = [
                'id' => $id,
                'protocolo' => $protocolo,
                'tipo' => Helper::sanitize($input['tipo']),
                'descricao' => Helper::sanitize($input['descricao']),
                'envolvidos' => json_encode($input['envolvidos'] ?? []),
                'local' => Helper::sanitize($input['local']),
                'data_periodo' => Helper::sanitize($input['data_periodo'] ?? ''),
                'testemunhas' => Helper::sanitize($input['testemunhas'] ?? ''),
                'comunicado' => $input['comunicado'] ?? false,
                'quem_comunicou' => Helper::sanitize($input['quem_comunicou'] ?? ''),
                'anonimato' => Helper::shouldBeAnonymous($input['anonimato'] ?? true),
                'contato' => Helper::sanitize($input['contato'] ?? ''),
                'impacto' => json_encode($input['impacto'] ?? []),
                'ip_origem' => Helper::getClientIP(Helper::shouldBeAnonymous($input['anonimato'] ?? true))
            ];

            // Inserir denúncia
            $inserted = $this->model->create($data);
            
            if (!$inserted) {
                Helper::log("Erro ao criar denúncia no banco de dados", 'error');
                return $this->response
                    ->internalServerError('Erro ao registrar denúncia')
                    ->send();
            }

            // Registrar auditoria
            Helper::log("Denúncia criada: $protocolo", 'info');

            return $this->response
                ->created([
                    'id' => $id,
                    'protocolo' => $protocolo,
                    'mensagem' => 'Denúncia registrada com sucesso. Guarde este protocolo para acompanhamento.'
                ], 'Denúncia criada com sucesso')
                ->send();

        } catch (Exception $e) {
            Helper::log("Exceção ao criar denúncia: " . $e->getMessage(), 'error');
            return $this->response
                ->internalServerError('Erro ao processar denúncia', $e->getMessage())
                ->send();
        }
    }

    /**
     * Listar denúncias
     */
    private function list() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : ITEMS_PER_PAGE;

            $filters = [];
            if (isset($_GET['tipo'])) $filters['tipo'] = $_GET['tipo'];
            if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
            if (isset($_GET['local'])) $filters['local'] = $_GET['local'];
            if (isset($_GET['data_inicio'])) $filters['data_inicio'] = $_GET['data_inicio'];
            if (isset($_GET['data_fim'])) $filters['data_fim'] = $_GET['data_fim'];

            $denuncias = $this->model->list($page, $limit, $filters);
            $total = $this->model->count($filters);

            return $this->response
                ->success([
                    'dados' => $denuncias,
                    'paginacao' => [
                        'pagina_atual' => $page,
                        'itens_por_pagina' => $limit,
                        'total_itens' => $total,
                        'total_paginas' => ceil($total / $limit)
                    ]
                ], 'Denúncias listadas com sucesso')
                ->send();

        } catch (Exception $e) {
            Helper::log("Erro ao listar denúncias: " . $e->getMessage(), 'error');
            return $this->response
                ->internalServerError('Erro ao listar denúncias')
                ->send();
        }
    }

    /**
     * Obter denúncia por ID
     */
    private function getById($id) {
        try {
            $denuncia = $this->model->getById($id);

            if (!$denuncia) {
                return $this->response->notFound('Denúncia não encontrada')->send();
            }

            $denuncia['evidencias'] = $this->model->getEvidencias($id);
            $denuncia['comentarios'] = $this->model->getComments($id);

            return $this->response->success($denuncia)->send();

        } catch (Exception $e) {
            Helper::log("Erro ao obter denúncia: " . $e->getMessage(), 'error');
            return $this->response->internalServerError()->send();
        }
    }

    /**
     * Obter por protocolo
     */
    private function getByProtocolo($protocolo) {
        try {
            $denuncia = $this->model->getByProtocolo($protocolo);

            if (!$denuncia) {
                return $this->response->notFound('Denúncia não encontrada')->send();
            }

            return $this->response->success($denuncia)->send();

        } catch (Exception $e) {
            return $this->response->internalServerError()->send();
        }
    }

    /**
     * Atualizar denúncia
     */
    private function update($id) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input === null) {
                return $this->response->badRequest('Dados inválidos')->send();
            }

            $denuncia = $this->model->getById($id);
            if (!$denuncia) {
                return $this->response->notFound()->send();
            }

            $this->model->update($id, $input);

            return $this->response->success(null, 'Denúncia atualizada com sucesso')->send();

        } catch (Exception $e) {
            return $this->response->internalServerError()->send();
        }
    }

    /**
     * Upload de evidências
     */
    private function uploadEvidence($id_denuncia) {
        try {
            if (!isset($_FILES['arquivo'])) {
                return $this->response->badRequest('Nenhum arquivo enviado')->send();
            }

            $file = $_FILES['arquivo'];

            // Validações
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return $this->response->badRequest('Erro ao fazer upload do arquivo')->send();
            }

            if (!Helper::isValidFileSize($file['size'])) {
                return $this->response->badRequest('Arquivo excede o tamanho máximo permitido')->send();
            }

            if (!Helper::isValidFileExtension($file['name'])) {
                return $this->response->badRequest('Tipo de arquivo não permitido')->send();
            }

            // Salvar arquivo
            $safeFileName = Helper::generateSafeFileName($file['name']);
            $uploadPath = UPLOAD_DIR . $safeFileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $this->response->internalServerError('Erro ao salvar arquivo')->send();
            }

            // Registrar no banco
            $this->model->addEvidence(
                $id_denuncia,
                $safeFileName,
                $file['name'],
                $file['size']
            );

            Helper::log("Evidência enviada para denúncia $id_denuncia", 'info');

            return $this->response->success([
                'arquivo' => $safeFileName,
                'tamanho' => Helper::formatFileSize($file['size'])
            ], 'Arquivo enviado com sucesso')->send();

        } catch (Exception $e) {
            Helper::log("Erro ao fazer upload: " . $e->getMessage(), 'error');
            return $this->response->internalServerError()->send();
        }
    }

    /**
     * Buscar denúncias
     */
    private function search() {
        try {
            $q = isset($_GET['q']) ? trim($_GET['q']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            if (strlen($q) < 3) {
                return $this->response->badRequest('Digite pelo menos 3 caracteres')->send();
            }

            $results = $this->model->search($q, $page);

            return $this->response->success($results, 'Busca realizada com sucesso')->send();

        } catch (Exception $e) {
            return $this->response->internalServerError()->send();
        }
    }

    /**
     * Obter estatísticas
     */
    private function getStats() {
        try {
            $stats = $this->model->getStatistics();
            return $this->response->success($stats, 'Estatísticas obtidas com sucesso')->send();

        } catch (Exception $e) {
            return $this->response->internalServerError()->send();
        }
    }
}



?>
