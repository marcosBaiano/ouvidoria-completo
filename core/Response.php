<?php
/**
 * Classe de Resposta da API
 * @author Sistema de Ouvidoria
 */

class Response {
    private $statusCode = 200;
    private $data = [];
    private $message = '';
    private $errors = [];

    public function __construct($statusCode = 200, $data = null, $message = '') {
        $this->statusCode = $statusCode;
        $this->data = $data ?? [];
        $this->message = $message;
    }

    /**
     * Define status code
     */
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Define dados
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * Define mensagem
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Adiciona erro
     */
    public function addError($error) {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Define múltiplos erros
     */
    public function setErrors($errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Sucesso com dados
     */
    public function success($data = null, $message = 'Sucesso') {
        $this->statusCode = 200;
        $this->data = $data;
        $this->message = $message;
        return $this;
    }

    /**
     * Criado
     */
    public function created($data = null, $message = 'Recurso criado com sucesso') {
        $this->statusCode = 201;
        $this->data = $data;
        $this->message = $message;
        return $this;
    }

    /**
     * Requisição inválida
     */
    public function badRequest($message = 'Requisição inválida', $errors = []) {
        $this->statusCode = 400;
        $this->message = $message;
        $this->errors = $errors;
        return $this;
    }

    /**
     * Não autorizado
     */
    public function unauthorized($message = 'Não autorizado') {
        $this->statusCode = 401;
        $this->message = $message;
        return $this;
    }

    /**
     * Proibido
     */
    public function forbidden($message = 'Acesso proibido') {
        $this->statusCode = 403;
        $this->message = $message;
        return $this;
    }

    /**
     * Não encontrado
     */
    public function notFound($message = 'Recurso não encontrado') {
        $this->statusCode = 404;
        $this->message = $message;
        return $this;
    }

    /**
     * Erro interno do servidor
     */
    public function internalServerError($message = 'Erro interno do servidor', $error = null) {
        $this->statusCode = 500;
        $this->message = $message;
        if ($error) {
            $this->errors[] = APP_ENV === 'development' ? $error : 'Erro ao processar a requisição';
        }
        return $this;
    }

    /**
     * Serviço indisponível
     */
    public function serviceUnavailable($message = 'Serviço indisponível') {
        $this->statusCode = 503;
        $this->message = $message;
        return $this;
    }

    /**
     * Retorna array de resposta
     */
    public function toArray() {
        $response = [
            'status' => $this->statusCode,
            'success' => $this->statusCode >= 200 && $this->statusCode < 300,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => date('c')
        ];

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    /**
     * Envia resposta JSON
     */
    public function send() {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

?>
