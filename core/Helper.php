<?php
/**
 * Classe com Funções Auxiliares
 * @author Sistema de Ouvidoria
 */

class Helper {

    /**
     * Gera um UUID v4
     */
    public static function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Gera um número aleatório para protocolo
     */
    public static function generateProtocolNumber() {
        return 'OUVI' . date('YmdHis') . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Sanitiza entrada de dados
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida email
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida URL
     */
    public static function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Criptografa dados com AES-256
     */
    public static function encrypt($data) {
        $cipher = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, ENCRYPTION_KEY, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    /**
     * Descriptografa dados
     */
    public static function decrypt($data) {
        $cipher = 'aes-256-cbc';
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length($cipher));
        $encrypted = substr($data, openssl_cipher_iv_length($cipher));
        
        return openssl_decrypt($encrypted, $cipher, ENCRYPTION_KEY, 0, $iv);
    }

    /**
     * Hash seguro com password_hash
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Gera token JWT
     */
    public static function generateJWT($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;
        $payload = json_encode($payload);

        $header_encoded = self::base64url_encode($header);
        $payload_encoded = self::base64url_encode($payload);

        $signature = hash_hmac(
            'sha256',
            $header_encoded . '.' . $payload_encoded,
            JWT_SECRET,
            true
        );
        $signature_encoded = self::base64url_encode($signature);

        return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }

    /**
     * Valida JWT
     */
    public static function validateJWT($token) {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        $header = json_decode(self::base64url_decode($parts[0]), true);
        $payload = json_decode(self::base64url_decode($parts[1]), true);
        $signature = $parts[2];

        // Verificar assinatura
        $valid_signature = hash_hmac(
            'sha256',
            $parts[0] . '.' . $parts[1],
            JWT_SECRET,
            true
        );
        $valid_signature_encoded = self::base64url_encode($valid_signature);

        if ($signature !== $valid_signature_encoded) {
            return false;
        }

        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Base64 URL encode
     */
    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    public static function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 4 - strlen($data) % 4));
    }

    /**
     * Formata data para o formato brasileiro
     */
    public static function formatDate($date) {
        if (!$date) return '';
        $timestamp = strtotime($date);
        return $timestamp ? date(DATE_FORMAT, $timestamp) : $date;
    }

    /**
     * Formata data e hora
     */
    public static function formatDateTime($datetime) {
        if (!$datetime) return '';
        $timestamp = strtotime($datetime);
        return $timestamp ? date(DATETIME_FORMAT, $timestamp) : $datetime;
    }

    /**
     * Formata moeda brasileira
     */
    public static function formatCurrency($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formata arquivo de upload
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Valida extensão de arquivo
     */
    public static function isValidFileExtension($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, UPLOAD_ALLOWED_TYPES);
    }

    /**
     * Valida tamanho de arquivo
     */
    public static function isValidFileSize($fileSize) {
        return $fileSize <= UPLOAD_MAX_SIZE;
    }

    /**
     * Gera nome seguro para arquivo
     */
    public static function generateSafeFileName($originalName) {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . strtolower($ext);
    }

    /**
     * Registra ação no log
     */
    public static function log($message, $level = 'info') {
        $timestamp = date(DATETIME_FORMAT);
        $logFile = LOG_DIR . strtolower($level) . '.log';
        $logMessage = "[$timestamp] [$level] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Valida IP anônimo
     */
    public static function shouldBeAnonymous($isAnonymous) {
        return $isAnonymous === true || $isAnonymous === 'true' || $isAnonymous === 1 || $isAnonymous === '1';
    }

    /**
     * Obtém IP do cliente (respeitando anonimato)
     */
    public static function getClientIP($anonymous = false) {
        if ($anonymous) {
            return 'ANÔNIMO';
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'DESCONHECIDO';
        }
    }

    /**
     * Converte array em JSON seguro
     */
    public static function toJson($data, $prettyPrint = false) {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($prettyPrint) {
            $options |= JSON_PRETTY_PRINT;
        }
        return json_encode($data, $options);
    }

    /**
     * Decodifica JSON com tratamento de erro
     */
    public static function fromJson($json) {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $data;
    }
}

?>
