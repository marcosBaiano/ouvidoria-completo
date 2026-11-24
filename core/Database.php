<?php
/**
 * Classe de Conexão com Banco de Dados
 * @author Sistema de Ouvidoria
 */

class Database {
    private $connection;
    private $lastQuery;
    private $lastError;
    private $lastErrorCode;

    public function __construct() {
        $this->connect();
    }

    /**
     * Estabelece conexão com banco de dados
     */
    private function connect() {
    try {
        // Suprime avisos temporariamente para não "sujar" o JSON
        mysqli_report(MYSQLI_REPORT_OFF); 
        
        $this->connection = @new mysqli(
            DB_HOST, 
            DB_USER, 
            DB_PASS, 
            DB_NAME, 
            DB_PORT
        );

        if ($this->connection->connect_error) {
            throw new Exception("Falha na conexão: " . $this->connection->connect_error);
        }

        $this->connection->set_charset(DB_CHARSET);

    } catch (Exception $e) {
        $this->lastError = $e->getMessage();
        $this->logError($e->getMessage());
        
        // Em vez de die(), enviamos um JSON de erro
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erro de Banco de Dados: " . $e->getMessage()
        ]);
        exit;
    }
}


    /**
     * Executa uma query preparada
     * @param string $query SQL query com placeholders
     * @param array $params Parâmetros para a query
     * @param string $types Tipos dos parâmetros (i=int, s=string, d=double, b=blob)
     * @return mysqli_result|false
     */
    public function query($query, $params = [], $types = '') {
        try {
            $this->lastQuery = $query;

            if (empty($params)) {
                $result = $this->connection->query($query);
            } else {
                $stmt = $this->connection->prepare($query);

                if (!$stmt) {
                    throw new Exception("Erro ao preparar query: " . $this->connection->error);
                }

                // Auto-detectar tipos se não fornecidos
                if (empty($types)) {
                    $types = $this->detectTypes($params);
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();

                $result = $stmt->get_result();
            }

            $this->lastErrorCode = $this->connection->errno;

            return $result;

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logError("Query Error: " . $e->getMessage() . " | Query: " . $query);
            return false;
        }
    }

    /**
     * Insere um registro
     * @param string $table Nome da tabela
     * @param array $data Array associativo com dados
     * @return string ID do registro inserido ou false
     */
    public function insert($table, $data) {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($values), '?');

        $query = "INSERT INTO " . $table . " (" . implode(',', $columns) . ") 
                  VALUES (" . implode(',', $placeholders) . ")";

        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            $this->lastError = $this->connection->error;
            return false;
        }

        $types = $this->detectTypes($values);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return $this->connection->insert_id;
        }

        return false;
    }

    /**
     * Atualiza registros
     * @param string $table Nome da tabela
     * @param array $data Array com dados a atualizar
     * @param array $where Array com condições WHERE
     * @return int Número de linhas afetadas
     */
    public function update($table, $data, $where) {
        $set = [];
        $values = [];

        foreach ($data as $column => $value) {
            $set[] = $column . " = ?";
            $values[] = $value;
        }

        $whereConditions = [];
        foreach ($where as $column => $value) {
            $whereConditions[] = $column . " = ?";
            $values[] = $value;
        }

        $query = "UPDATE " . $table . " SET " . implode(', ', $set) . 
                 " WHERE " . implode(' AND ', $whereConditions);

        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            $this->lastError = $this->connection->error;
            return false;
        }

        $types = $this->detectTypes($values);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    /**
     * Deleta registros
     * @param string $table Nome da tabela
     * @param array $where Array com condições WHERE
     * @return int Número de linhas afetadas
     */
    public function delete($table, $where) {
        $whereConditions = [];
        $values = [];

        foreach ($where as $column => $value) {
            $whereConditions[] = $column . " = ?";
            $values[] = $value;
        }

        $query = "DELETE FROM " . $table . " WHERE " . implode(' AND ', $whereConditions);

        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            $this->lastError = $this->connection->error;
            return false;
        }

        $types = $this->detectTypes($values);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    /**
     * Obtém um único resultado
     * @param string $query SQL query
     * @param array $params Parâmetros
     * @param string $types Tipos dos parâmetros
     * @return array|null
     */
    public function fetchOne($query, $params = [], $types = '') {
        $result = $this->query($query, $params, $types);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Obtém todos os resultados
     * @param string $query SQL query
     * @param array $params Parâmetros
     * @param string $types Tipos dos parâmetros
     * @return array
     */
    public function fetchAll($query, $params = [], $types = '') {
        $result = $this->query($query, $params, $types);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Detecta tipos de parâmetros automaticamente
     * @param array $params
     * @return string
     */
    private function detectTypes($params) {
        $types = '';

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        return $types;
    }

    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    /**
     * Confirma a transação
     */
    public function commit() {
        $this->connection->commit();
    }

    /**
     * Desfaz a transação
     */
    public function rollback() {
        $this->connection->rollback();
    }

    /**
     * Obtém último ID inserido
     */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * Obtém o último erro
     */
    public function getLastError() {
        return $this->lastError ?? $this->connection->error;
    }

    /**
     * Obtém o código do último erro
     */
    public function getLastErrorCode() {
        return $this->lastErrorCode ?? $this->connection->errno;
    }

    /**
     * Registra erro no log
     */
    private function logError($message) {
        $logFile = LOG_DIR . 'database.log';
        $timestamp = date(DATETIME_FORMAT);
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Fecha a conexão
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Destrutor
     */
    public function __destruct() {
        $this->close();
    }
}

?>
