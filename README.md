# 📋 Sistema de Ouvidoria Web - Documentação Completa

## 🎯 Visão Geral

Sistema web moderno, seguro e anônimo para coleta de denúncias, desenvolvido em **PHP vanilla** com **MySQL**.

**Versão:** 1.0.0  
**Linguagem:** PHP 7.4+  
**Banco de Dados:** MySQL 5.7+  
**Editor Recomendado:** VSCode

---

## 📁 Estrutura do Projeto

```
ouvidoria/
├── config/
│   └── config.php                 # Configurações principais
├── core/
│   ├── Database.php              # Classe de conexão MySQL
│   ├── Helper.php                # Funções auxiliares
│   ├── Response.php              # Classe de resposta API
│   └── Validator.php             # Validador de dados
├── models/
│   └── DenunciaModel.php         # Modelo de denúncias
├── controllers/
│   └── DenunciaController.php    # Controlador de denúncias
├── database/
│   └── schema.sql                # Script SQL de criação
├── public/
│   ├── index.html                # Frontend
│   ├── api.php                   # Router da API
│   └── uploads/                  # Pasta de evidências
├── logs/                         # Logs da aplicação
└── uploads/                      # Upload de arquivos
```

---

## 🚀 Instalação e Setup

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- VSCode (ou editor de preferência)

### Passo 1: Criar Banco de Dados

```bash
# Abrir terminal e conectar ao MySQL
mysql -u root -p

# Dentro do MySQL:
source /caminho/para/ouvidoria/database/schema.sql
```

Ou copiar o conteúdo de `database/schema.sql` e executar no phpMyAdmin.

### Passo 2: Configurar Credenciais

Editar `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'ouvidoria_db');
```

### Passo 3: Iniciar Servidor PHP

```bash
# Navegar até a pasta do projeto
cd /caminho/para/ouvidoria

# Iniciar servidor PHP integrado
php -S localhost:8000 -t public/

# Ou em outra porta se necessário
php -S 127.0.0.1:3000 -t public/
```

### Passo 4: Acessar a Aplicação

Abrir no navegador:
- **Frontend:** `http://localhost:8000`
- **API:** `http://localhost:8000/api.php`

---

## 🔌 Endpoints da API

### 1. **Criar Denúncia** (POST)

```
POST /api/denuncias
Content-Type: application/json

{
  "tipo": "assedio_moral",
  "descricao": "Descrição detalhada da denúncia...",
  "local": "administrativo",
  "data_periodo": "01/03/2026",
  "testemunhas": "João Silva, Maria Santos",
  "comunicado": false,
  "anonimato": true,
  "contato": null,
  "envolvidos": [
    {
      "nome": "Gestor X",
      "cargo": "Gerente",
      "setor": "RH"
    }
  ],
  "impacto": ["seguranca", "saude"]
}

Resposta (201):
{
  "status": 201,
  "success": true,
  "message": "Denúncia criada com sucesso",
  "data": {
    "id": "uuid-aqui",
    "protocolo": "OUVI20260325140530ABC123"
  },
  "timestamp": "2026-03-25T14:05:30+00:00"
}
```

### 2. **Listar Denúncias** (GET)

```
GET /api/denuncias?page=1&limit=20&tipo=assedio_moral&status=registrada

Resposta (200):
{
  "status": 200,
  "success": true,
  "message": "Denúncias listadas com sucesso",
  "data": {
    "dados": [
      {
        "id": "uuid",
        "protocolo": "OUVI...",
        "tipo": "assedio_moral",
        "local": "administrativo",
        "status": "registrada",
        "anonimato": true,
        "created_at": "2026-03-25 14:05:30"
      }
    ],
    "paginacao": {
      "pagina_atual": 1,
      "itens_por_pagina": 20,
      "total_itens": 150,
      "total_paginas": 8
    }
  }
}
```

**Parâmetros de Filtro:**
- `page` (int): Página atual
- `limit` (int): Itens por página (máx 100)
- `tipo` (string): Tipo de denúncia
- `status` (string): Status
- `local` (string): Local da denúncia
- `data_inicio` (string): Data início (YYYY-MM-DD)
- `data_fim` (string): Data fim (YYYY-MM-DD)

### 3. **Obter Denúncia por ID** (GET)

```
GET /api/denuncias/uuid-aqui

Resposta (200):
{
  "status": 200,
  "success": true,
  "data": {
    "id": "uuid",
    "protocolo": "OUVI...",
    "tipo": "assedio_moral",
    ...
    "evidencias": [...],
    "comentarios": [...]
  }
}
```

### 4. **Obter por Protocolo** (GET)

```
GET /api/denuncias/protocolo/OUVI20260325140530ABC123
```

### 5. **Atualizar Denúncia** (PUT)

```
PUT /api/denuncias/uuid-aqui
Content-Type: application/json

{
  "status": "em_analise",
  "tipo": "assedio_moral"
}
```

### 6. **Upload de Evidência** (POST)

```
POST /api/denuncias/uuid-aqui/evidencias
Content-Type: multipart/form-data

[arquivo binário]

Resposta (200):
{
  "status": 200,
  "success": true,
  "data": {
    "arquivo": "arquivo_uuid_timestamp.jpg",
    "tamanho": "2.5 MB"
  }
}
```

### 7. **Buscar Denúncias** (GET)

```
GET /api/denuncias/buscar?q=fraude&page=1

Resposta (200):
{
  "status": 200,
  "success": true,
  "data": [...]
}
```

### 8. **Estatísticas** (GET)

```
GET /api/denuncias/stats

Resposta (200):
{
  "status": 200,
  "success": true,
  "data": {
    "total": 150,
    "anonimas": 120,
    "identificadas": 30,
    "por_tipo": [...],
    "por_status": [...],
    "por_local": [...]
  }
}
```

---

## 🔒 Segurança

### Implementações

1. **Criptografia AES-256**: Dados sensíveis
2. **SQL Injection Prevention**: Prepared statements
3. **XSS Protection**: HTML escaping
4. **HTTPS Headers**: Enviados automaticamente
5. **Anonimato**: Não armazena IP em modo anônimo
6. **LGPD Compliance**: Política de retenção de dados

### Variáveis de Segurança em `config.php`

```php
define('ENCRYPTION_KEY', 'sua_chave_aes256_aqui');
define('JWT_SECRET', 'sua_chave_jwt_aqui');
define('JWT_EXPIRATION', 3600); // 1 hora
define('SESSION_TIMEOUT', 1800); // 30 minutos
```

---

## 📊 Tipos de Denúncia

| Código | Nome |
|--------|------|
| `assedio_moral` | Assédio Moral |
| `assedio_sexual` | Assédio Sexual |
| `discriminacao` | Discriminação |
| `fraude` | Fraude |
| `conflito_interesse` | Conflito de Interesse |
| `conduta_inadequada` | Conduta Inadequada |
| `furto` | Furto |
| `etica` | Questão Ética |
| `outro` | Outro |

---

## 📍 Locais

| Código | Nome |
|--------|------|
| `producao` | Produção |
| `expedicao` | Expedição |
| `logistica` | Logística |
| `administrativo` | Administrativo |
| `externo` | Externo |
| `outro` | Outro |

---

## 🎯 Status de Denúncia

| Código | Descrição |
|--------|-----------|
| `registrada` | Recém registrada |
| `em_analise` | Sendo analisada |
| `em_investigacao` | Sob investigação |
| `concluida` | Investigação concluída |
| `arquivada` | Arquivada |

---

## 📁 Uploads de Evidências

### Tipos Permitidos
- **Imagens:** JPG, JPEG, PNG
- **Documentos:** PDF, DOC, DOCX
- **Vídeos:** MP4, AVI

### Limite
- **Máximo por arquivo:** 50MB
- **Pasta:** `public/uploads/evidencias/`

### Nomes de Arquivos
Os arquivos são salvos com nome único:
```
uniqid_timestamp.extensão
Ex: 5e8f9a2b_1700000000.pdf
```

---

## 🗄️ Estrutura do Banco de Dados

### Tabela: `denuncias`

```sql
- id (UUID)
- protocolo (string, unique)
- tipo (enum)
- descricao (text)
- envolvidos (JSON)
- local (enum)
- data_periodo (string)
- testemunhas (text)
- comunicado (boolean)
- quem_comunicou (string)
- anonimato (boolean)
- contato (string)
- impacto (JSON)
- ip_origem (string)
- status (enum)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabela: `evidencias`

```sql
- id (UUID)
- id_denuncia (FK)
- filename (string)
- original_name (string)
- file_size (bigint)
- file_type (string)
- created_at (timestamp)
```

### Tabela: `comentarios`

```sql
- id (UUID)
- id_denuncia (FK)
- author (string)
- content (text)
- is_internal (boolean)
- created_at (timestamp)
```

### Tabela: `usuarios`

```sql
- id (UUID)
- nome (string)
- email (string, unique)
- senha (string)
- cargo (string)
- departamento (string)
- role (enum: administrador, investigador, visualizador)
- ativo (boolean)
- ultimo_login (datetime)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabela: `logs_auditoria`

```sql
- id (UUID)
- usuario_id (FK)
- acao (string)
- tabela (string)
- registro_id (string)
- detalhes (JSON)
- ip_origem (string)
- user_agent (text)
- created_at (timestamp)
```

---

## 🛠️ Funções Principais

### Helper::generateUUID()
Gera UUID v4 para IDs únicos

### Helper::generateProtocolNumber()
Gera número de protocolo: `OUVI20260325140530ABC123`

### Helper::sanitize()
Sanitiza entrada para prevenir XSS

### Helper::encrypt() / decrypt()
Criptografia AES-256

### Helper::hashPassword() / verifyPassword()
Hash seguro com bcrypt

### Helper::generateJWT() / validateJWT()
Gerenciamento de tokens JWT

### Validator
Validação completa de dados:
```php
$validator = new Validator($data);
$validator
    ->required('tipo')
    ->email('contato')
    ->minLength('descricao', 20)
    ->cpf('cpf')
    ->phone('telefone');

if ($validator->hasErrors()) {
    $errors = $validator->getErrors();
}
```

### Database
Operações de banco:
```php
$db = new Database();

// Query
$db->query("SELECT * FROM denuncias WHERE status = ?", ['registrada']);

// Insert
$db->insert('denuncias', $data);

// Update
$db->update('denuncias', $data, ['id' => $id]);

// Delete
$db->delete('denuncias', ['id' => $id]);

// Fetch
$result = $db->fetchOne("SELECT * FROM denuncias WHERE id = ?", [$id]);
$results = $db->fetchAll("SELECT * FROM denuncias");

// Transactions
$db->beginTransaction();
$db->insert('denuncias', $data1);
$db->insert('evidencias', $data2);
$db->commit();
```

---

## 🔍 Logs

Os logs são salvos em `logs/`:

- `error.log` - Erros do sistema
- `info.log` - Informações gerais
- `database.log` - Erros de banco de dados
- `debug.log` - Informações de debug

```php
Helper::log("Mensagem", "info");
Helper::log("Erro", "error");
```

---

## 🧪 Testando a API

### Com cURL

```bash
# Criar denúncia
curl -X POST http://localhost:8000/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "tipo": "fraude",
    "descricao": "Descrição detalhada...",
    "local": "administrativo",
    "anonimato": true
  }'

# Listar
curl http://localhost:8000/api.php?action=list

# Buscar
curl "http://localhost:8000/api.php?action=search&q=fraude"
```

### Com Postman

1. Importar endpoints
2. Set `Body` → JSON
3. Adicionar dados
4. Enviar

---

## 📈 Estatísticas e Relatórios

Acesse via endpoint `/api/denuncias/stats`:

- Total de denúncias
- Distribuição por tipo
- Distribuição por status
- Distribuição por local
- Denúncias anônimas vs identificadas
- Última denúncia registrada

---

## 🔄 Fluxo Típico

1. **Usuário acessa** o frontend em `index.html`
2. **Preenche formulário** multi-etapa
3. **Envia requisição POST** para `/api/denuncias`
4. **PHP valida** dados
5. **Armazena** no MySQL
6. **Gera protocolo** único
7. **Recebe confirmação** com número de protocolo
8. **Pode fazer upload** de evidências depois

---

## 🚨 Tratamento de Erros

Todos os endpoints retornam:

```json
{
  "status": 400,
  "success": false,
  "message": "Descrição do erro",
  "errors": ["Campo tipo é obrigatório"],
  "timestamp": "2026-03-25T14:05:30+00:00"
}
```

Códigos HTTP:
- `200` - Sucesso
- `201` - Criado
- `400` - Requisição inválida
- `401` - Não autorizado
- `403` - Proibido
- `404` - Não encontrado
- `500` - Erro servidor
- `503` - Serviço indisponível

---

## 📝 Próximas Funcionalidades

- [ ] Dashboard de análise
- [ ] Autenticação de usuários
- [ ] Sistema de comentários internos
- [ ] Notificações por email
- [ ] Exportação de relatórios (PDF/Excel)
- [ ] Integração com AWS S3
- [ ] API de consulta de protocolo
- [ ] Mobile app
- [ ] Busca full-text
- [ ] Machine learning para categorização

---

## 🤝 Suporte

Para dúvidas ou problemas:

1. Verificar logs em `logs/`
2. Testar endpoints com Postman
3. Validar dados no banco

---

## 📄 Licença

Sistema proprietário. Todos os direitos reservados.

---

**Desenvolvido com ❤️ para garantir ética e transparência**
