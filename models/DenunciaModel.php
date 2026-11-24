<?php
/**
 * Modelo de Denúncia
 * @author Sistema de Ouvidoria
 */

class DenunciaModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Cria nova denúncia
     */
   public function create($data) {
    try {
        $insertData = [
            'id'             => $data['id'],
            'protocolo'      => $data['protocolo'],
            'tipo'           => $data['tipo'],
            'descricao'      => $data['descricao'],
            'envolvidos'     => json_encode($data['envolvidos'] ?? []),
            'local'          => $data['local'],
            'data_periodo'   => $data['data_periodo'] ?? null,
            'testemunhas'    => $data['testemunhas'] ?? null,
            'comunicado'     => ($data['comunicado'] ?? false) ? 1 : 0,
            'quem_comunicou' => $data['quem_comunicou'] ?? null,
            'anonimato'      => ($data['anonimato'] ?? true) ? 1 : 0,
            'contato'        => $data['contato'] ?? null,
            'impacto'        => json_encode($data['impacto'] ?? []),
            'ip_origem'      => $data['ip_origem'] ?? null,
            'status'         => 'registrada',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('denuncias', $insertData);
    } catch (Exception $e) {
        error_log("Erro no Model: " . $e->getMessage());
        return false;
    }
}




    /**
     * Obtém denúncia por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM denuncias WHERE id = ?";
        $result = $this->db->fetchOne($query, [$id]);

        if ($result) {
            $result['envolvidos'] = json_decode($result['envolvidos'], true);
            $result['impacto'] = json_decode($result['impacto'], true);
        }

        return $result;
    }

    /**
     * Obtém denúncia por protocolo
     */
    public function getByProtocolo($protocolo) {
        $query = "SELECT * FROM denuncias WHERE protocolo = ?";
        $result = $this->db->fetchOne($query, [$protocolo]);

        if ($result) {
            $result['envolvidos'] = json_decode($result['envolvidos'], true);
            $result['impacto'] = json_decode($result['impacto'], true);
            $result['evidencias'] = $this->getEvidencias($result['id']);
        }

        return $result;
    }

    /**
     * Lista denúncias com paginação
     */
    public function list($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT * FROM denuncias WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['tipo'])) {
            $query .= " AND tipo = ?";
            $params[] = $filters['tipo'];
            $types .= 's';
        }

        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['local'])) {
            $query .= " AND local = ?";
            $params[] = $filters['local'];
            $types .= 's';
        }

        if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
            $query .= " AND created_at BETWEEN ? AND ?";
            $params[] = $filters['data_inicio'] . ' 00:00:00';
            $params[] = $filters['data_fim'] . ' 23:59:59';
            $types .= 'ss';
        }

        $query .= " ORDER BY created_at DESC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        $types .= 'ii';

        $results = $this->db->fetchAll($query, $params, $types);

        foreach ($results as &$result) {
            $result['envolvidos'] = json_decode($result['envolvidos'], true);
            $result['impacto'] = json_decode($result['impacto'], true);
        }

        return $results;
    }

    /**
     * Conta total de denúncias
     */
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM denuncias WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['tipo'])) {
            $query .= " AND tipo = ?";
            $params[] = $filters['tipo'];
            $types .= 's';
        }

        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['local'])) {
            $query .= " AND local = ?";
            $params[] = $filters['local'];
            $types .= 's';
        }

        $result = $this->db->fetchOne($query, $params, $types);
        return $result['total'] ?? 0;
    }

    /**
     * Atualiza denúncia
     */
    public function update($id, $data) {
        $updateData = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['tipo', 'descricao', 'local', 'status'])) {
                $updateData[$key] = $value;
            }
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update('denuncias', $updateData, ['id' => $id]);
    }

    /**
     * Atualiza status
     */
    public function updateStatus($id, $status) {
        return $this->db->update('denuncias', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    /**
     * Adiciona evidência
     */
    public function addEvidence($id_denuncia, $filename, $original_name, $file_size) {
        $insertData = [
            'id' => Helper::generateUUID(),
            'id_denuncia' => $id_denuncia,
            'filename' => $filename,
            'original_name' => $original_name,
            'file_size' => $file_size,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('evidencias', $insertData);
    }

    /**
     * Obtém evidências da denúncia
     */
    public function getEvidencias($id_denuncia) {
        $query = "SELECT * FROM evidencias WHERE id_denuncia = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($query, [$id_denuncia]);
    }

    /**
     * Remove evidência
     */
    public function removeEvidence($id) {
        $evidence = $this->db->fetchOne("SELECT * FROM evidencias WHERE id = ?", [$id]);
        
        if ($evidence && file_exists(UPLOAD_DIR . $evidence['filename'])) {
            unlink(UPLOAD_DIR . $evidence['filename']);
        }

        return $this->db->delete('evidencias', ['id' => $id]);
    }

    /**
     * Adiciona comentário
     */
    public function addComment($id_denuncia, $author, $content, $is_internal = false) {
        $insertData = [
            'id' => Helper::generateUUID(),
            'id_denuncia' => $id_denuncia,
            'author' => $author,
            'content' => $content,
            'is_internal' => $is_internal ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('comentarios', $insertData);
    }

    /**
     * Obtém comentários
     */
    public function getComments($id_denuncia, $internal_only = false) {
        $query = "SELECT * FROM comentarios WHERE id_denuncia = ?";
        $params = [$id_denuncia];

        if ($internal_only) {
            $query .= " AND is_internal = 1";
        }

        $query .= " ORDER BY created_at ASC";
        return $this->db->fetchAll($query, $params);
    }

    /**
     * Gera estatísticas
     */
    public function getStatistics() {
        $stats = [];

        // Total de denúncias
        $stats['total'] = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM denuncias"
        )['count'];

        // Por tipo
        $stats['por_tipo'] = $this->db->fetchAll(
            "SELECT tipo, COUNT(*) as count FROM denuncias GROUP BY tipo ORDER BY count DESC"
        );

        // Por status
        $stats['por_status'] = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM denuncias GROUP BY status"
        );

        // Por local
        $stats['por_local'] = $this->db->fetchAll(
            "SELECT local, COUNT(*) as count FROM denuncias GROUP BY local ORDER BY count DESC"
        );

        // Anônimas vs identificadas
        $stats['anonimas'] = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM denuncias WHERE anonimato = 1"
        )['count'];

        $stats['identificadas'] = $stats['total'] - $stats['anonimas'];

        // Última denúncia
        $stats['ultima_denuncia'] = $this->db->fetchOne(
            "SELECT created_at FROM denuncias ORDER BY created_at DESC LIMIT 1"
        );

        return $stats;
    }

    /**
     * Exporta relatório
     */
    public function exportReport($filters = []) {
        return $this->list(1, 10000, $filters);
    }

    /**
     * Busca por texto
     */
    public function search($query, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $searchTerm = '%' . $query . '%';

        $sql = "SELECT * FROM denuncias 
                WHERE protocolo LIKE ? 
                OR descricao LIKE ? 
                OR testemunhas LIKE ?
                ORDER BY created_at DESC 
                LIMIT ?, ?";

        $params = [$searchTerm, $searchTerm, $searchTerm, $offset, $limit];
        $types = 'sssii';

        return $this->db->fetchAll($sql, $params, $types);
    }
        /**
     * Retorna o último erro ocorrido na classe Database
     */
    public function getLastError() {
        // Verifica se a classe Database tem um método para pegar o erro
        if (method_exists($this->db, 'getError')) {
            return $this->db->getError();
        }
        return "Erro desconhecido ao processar operação no banco de dados.";
    }

}
?>