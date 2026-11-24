# 🚀 GUIA RÁPIDO - Sistema de Ouvidoria Web

## ⚡ Começar em 5 Minutos

### 1️⃣ **Pré-requisitos**
```bash
- PHP 7.4+ instalado
- MySQL 5.7+ rodando
- VSCode ou editor similar
```

### 2️⃣ **Criar Banco de Dados**

#### Opção A: MySQL CLI
```bash
mysql -u root -p < ouvidoria/database/schema.sql
```

#### Opção B: phpMyAdmin
1. Abrir phpMyAdmin
2. Criar novo banco: `ouvidoria_db`
3. Importar arquivo `database/schema.sql`

### 3️⃣ **Configurar Conexão**

Editar `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'ouvidoria_db');
```

### 4️⃣ **Iniciar Servidor PHP**

```bash
cd ouvidoria/
php -S localhost:8000 -t public/
```

### 5️⃣ **Acessar**

- **Frontend:** http://localhost:8000
- **Dashboard:** http://localhost:8000/dashboard.html
- **API:** http://localhost:8000/api.php

---

## 📝 Estrutura Básica

```
ouvidoria/
├── public/
│   ├── index.html          ← Formulário de denúncia
│   ├── dashboard.html      ← Painel administrativo
│   ├── api.php             ← API REST
│   └── uploads/            ← Arquivos enviados
├── config/
│   └── config.php          ← Configurações
├── core/
│   ├── Database.php        ← Conexão MySQL
│   ├── Helper.php          ← Funções úteis
│   ├── Response.php        ← Respostas JSON
│   └── Validator.php       ← Validações
├── models/
│   └── DenunciaModel.php   ← Lógica de dados
├── controllers/
│   └── DenunciaController.php ← Controllers
├── database/
│   └── schema.sql          ← Script SQL
└── logs/                   ← Arquivos de log
```

---

## 🔌 Principais Endpoints

### **Enviar Denúncia**
```bash
POST /api/denuncias
Content-Type: application/json

{
  "tipo": "fraude",
  "descricao": "Descrição aqui",
  "local": "administrativo",
  "anonimato": true
}
```

### **Listar Denúncias**
```bash
GET /api/denuncias?page=1&limit=20&tipo=fraude&status=registrada
```

### **Buscar por Protocolo**
```bash
GET /api/denuncias/protocolo/OUVI20260325140530ABC
```

### **Upload de Evidência**
```bash
POST /api/denuncias/{id}/evidencias
Content-Type: multipart/form-data

[arquivo binário]
```

### **Estatísticas**
```bash
GET /api/denuncias/stats
```

---

## 🧪 Testar com cURL

```bash
# Criar denúncia
curl -X POST http://localhost:8000/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "tipo": "fraude",
    "descricao": "Detalhes da denúncia...",
    "local": "administrativo",
    "anonimato": true
  }'

# Listar
curl "http://localhost:8000/api.php"

# Estatísticas
curl "http://localhost:8000/api.php?action=stats"
```

---

## 🔒 Segurança Essencial

1. **Alterar chaves em `config.php`:**
   ```php
   define('JWT_SECRET', 'sua_chave_super_segura_aqui');
   define('ENCRYPTION_KEY', 'chave_aes256_aqui');
   ```

2. **Criar usuário MySQL dedicado:**
   ```sql
   CREATE USER 'ouvidoria'@'localhost' IDENTIFIED BY 'senha_forte';
   GRANT ALL PRIVILEGES ON ouvidoria_db.* TO 'ouvidoria'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Permissões de pastas:**
   ```bash
   chmod 755 public/
   chmod 777 public/uploads/
   chmod 777 logs/
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

## 📁 Locais da Empresa

| Código | Local |
|--------|-------|
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

## 📈 Fluxo de Denúncia

```
1. Usuário acessa http://localhost:8000
   ↓
2. Preenche formulário multi-etapa
   ↓
3. Valida dados (PHP backend)
   ↓
4. Armazena no MySQL
   ↓
5. Gera protocolo único
   ↓
6. Retorna confirmação
   ↓
7. Usuário pode fazer upload de arquivos
   ↓
8. Acesso via protocolo ou ID
```

---

## 🛠️ Troubleshooting

### Erro: "Connection refused"
```bash
# Verificar se MySQL está rodando
sudo service mysql start

# Ou no Windows:
net start MySQL57
```

### Erro: "Database not found"
```bash
# Executar schema novamente
mysql -u root -p ouvidoria_db < database/schema.sql
```

### Erro: "Permission denied" na pasta uploads
```bash
chmod 777 public/uploads/
chmod 777 logs/
```

### API retorna erro 500
```bash
# Verificar logs
tail -f logs/error.log

# Ou
cat logs/error.log
```

### Arquivo não faz upload
1. Verificar permissões: `chmod 777 public/uploads/`
2. Verificar tamanho máximo em `config.php`
3. Verificar tipo de arquivo permitido

---

## 📱 Acessar de Outro Dispositivo

Se quiser acessar do celular na mesma rede:

```bash
# Descobrir IP do PC
ipconfig getifaddr en0  # macOS
hostname -I             # Linux
ipconfig                # Windows

# Iniciar servidor ouvindo em todas as interfaces
php -S 0.0.0.0:8000 -t public/

# Acessar do celular
http://192.168.1.100:8000  # Use seu IP
```

---

## 🔐 Variáveis Importantes

Em `config.php`, ajustar para produção:

```php
// Ambiente
define('APP_ENV', 'production');

// Banco de dados
define('DB_HOST', 'db.sua-empresa.com');
define('DB_USER', 'usuario_seguro');
define('DB_PASS', 'senha_muito_forte');

// URLs
define('APP_URL', 'https://ouvidoria.sua-empresa.com');

// Email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'seu_email@empresa.com');
define('SMTP_PASS', 'senha_app_segura');
```

---

## 💡 Dicas Importantes

1. **Sempre fazer backup do banco:**
   ```bash
   mysqldump -u root -p ouvidoria_db > backup.sql
   ```

2. **Monitorar logs regularmente:**
   ```bash
   tail -f logs/error.log
   tail -f logs/info.log
   ```

3. **Testar API com Postman:**
   - Importar endpoints
   - Testar cada rota
   - Validar respostas

4. **Usar HTTPS em produção:**
   - SSL/TLS obrigatório
   - Redirects HTTP → HTTPS

5. **Fazer backup regularmente:**
   ```bash
   # Automatizar com cron
   0 2 * * * mysqldump -u root -p ouvidoria_db > /backups/$(date +\%Y\%m\%d).sql
   ```

---

## 📞 Suporte

**Problemas?**

1. Verificar arquivo `README.md` para documentação completa
2. Revisar logs em `logs/`
3. Testar endpoints isoladamente
4. Verificar permissões de pasta
5. Validar credenciais de banco de dados

---

**Sistema de Ouvidoria Web v1.0**
Desenvolvido com segurança, privacidade e anonimato! ✅
