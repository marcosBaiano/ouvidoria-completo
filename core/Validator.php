<?php
/**
 * Classe de Validação de Dados
 * @author Sistema de Ouvidoria
 */

class Validator {
    private $errors = [];
    private $data = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Valida campo obrigatório
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field] = $message ?? "O campo '$field' é obrigatório";
        }
        return $this;
    }

    /**
     * Valida email
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "O email '$field' é inválido";
            }
        }
        return $this;
    }

    /**
     * Valida comprimento mínimo
     */
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "O campo '$field' deve ter no mínimo $length caracteres";
        }
        return $this;
    }

    /**
     * Valida comprimento máximo
     */
    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "O campo '$field' deve ter no máximo $length caracteres";
        }
        return $this;
    }

    /**
     * Valida tipo de dado
     */
    public function type($field, $type, $message = null) {
        if (!isset($this->data[$field])) {
            return $this;
        }

        $value = $this->data[$field];
        $valid = false;

        switch ($type) {
            case 'int':
            case 'integer':
                $valid = is_int($value) || ctype_digit((string)$value);
                break;
            case 'float':
            case 'double':
                $valid = is_numeric($value);
                break;
            case 'string':
                $valid = is_string($value);
                break;
            case 'boolean':
            case 'bool':
                $valid = is_bool($value) || in_array($value, ['true', 'false', 1, 0, '1', '0'], true);
                break;
            case 'array':
                $valid = is_array($value);
                break;
        }

        if (!$valid) {
            $this->errors[$field] = $message ?? "O campo '$field' deve ser do tipo $type";
        }

        return $this;
    }

    /**
     * Valida enum
     */
    public function enum($field, $values, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field] = $message ?? "O valor do campo '$field' não é válido";
        }
        return $this;
    }

    /**
     * Valida intervalo de números
     */
    public function between($field, $min, $max, $message = null) {
        if (isset($this->data[$field])) {
            $value = (float)$this->data[$field];
            if ($value < $min || $value > $max) {
                $this->errors[$field] = $message ?? "O campo '$field' deve estar entre $min e $max";
            }
        }
        return $this;
    }

    /**
     * Valida padrão regex
     */
    public function regex($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field] = $message ?? "O campo '$field' não corresponde ao padrão esperado";
        }
        return $this;
    }

    /**
     * Valida telefone brasileiro
     */
    public function phone($field, $message = null) {
        if (isset($this->data[$field])) {
            $phone = preg_replace('/\D/', '', $this->data[$field]);
            if (!preg_match('/^\d{10,11}$/', $phone)) {
                $this->errors[$field] = $message ?? "O telefone '$field' é inválido";
            }
        }
        return $this;
    }

    /**
     * Valida CPF
     */
    public function cpf($field, $message = null) {
        if (isset($this->data[$field])) {
            $cpf = preg_replace('/\D/', '', $this->data[$field]);
            
            if (strlen($cpf) != 11 || !$this->isCPFValid($cpf)) {
                $this->errors[$field] = $message ?? "O CPF '$field' é inválido";
            }
        }
        return $this;
    }

    /**
     * Valida CNPJ
     */
    public function cnpj($field, $message = null) {
        if (isset($this->data[$field])) {
            $cnpj = preg_replace('/\D/', '', $this->data[$field]);
            
            if (strlen($cnpj) != 14 || !$this->isCNPJValid($cnpj)) {
                $this->errors[$field] = $message ?? "O CNPJ '$field' é inválido";
            }
        }
        return $this;
    }

    /**
     * Valida data
     */
    public function date($field, $format = 'd/m/Y', $message = null) {
        if (isset($this->data[$field])) {
            $d = \DateTime::createFromFormat($format, $this->data[$field]);
            
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "O campo '$field' deve ser uma data válida";
            }
        }
        return $this;
    }

    /**
     * Valida arquivo
     */
    public function file($field, $maxSize = null, $allowedTypes = null) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] == UPLOAD_ERR_NO_FILE) {
            return $this;
        }

        if ($_FILES[$field]['error'] != UPLOAD_ERR_OK) {
            $this->errors[$field] = "Erro ao fazer upload do arquivo";
            return $this;
        }

        $file = $_FILES[$field];

        if ($maxSize && $file['size'] > $maxSize) {
            $this->errors[$field] = "O arquivo excede o tamanho máximo permitido";
            return $this;
        }

        if ($allowedTypes) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedTypes)) {
                $this->errors[$field] = "Tipo de arquivo não permitido";
            }
        }

        return $this;
    }

    /**
     * Verifica se há erros
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Obtém erros
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Obtém primeira mensagem de erro
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    /**
     * Valida CPF (helper)
     */
    private function isCPFValid($cpf) {
        // Validação simples de CPF
        if (strlen($cpf) != 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($i = 0; $i < $t; $i++) {
                $d += $cpf[$i] * (($t + 1) - $i);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$t] != $d) return false;
        }
        return true;
    }

    /**
     * Valida CNPJ (helper)
     */
    private function isCNPJValid($cnpj) {
        if (strlen($cnpj) != 14) return false;
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;

        for ($t = 0; $t < 2; $t++) {
            $d = 0;
            $m = ($t === 0) ? 5 : 6;
            
            for ($i = 0; $i < 8 + $t; $i++) {
                $d += $cnpj[$i] * $m;
                $m = ($m === 2) ? 9 : $m - 1;
            }
            
            $d = ((10 * $d) % 11);
            if ($d >= 10) $d = 0;
            
            if ($cnpj[8 + $t] != $d) return false;
        }
        return true;
    }
}

?>
