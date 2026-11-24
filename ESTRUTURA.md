# 📦 LISTA DE ARQUIVOS DO PROJETO

## Estrutura Completa

```
ouvidoria/
│
├─ 📁 public/                          # Arquivos públicos (Web server root)
│  ├─ index.html                       # Formulário principal de denúncia
│  ├─ dashboard.html                   # Painel administrativo
│  ├─ rastrear.html                    # Página de rastreamento de protocolo
│  ├─ api.php                          # Router principal da API
│  │
│  └─ 📁 uploads/                      # Pasta para evidências
│     └─ .gitkeep                      # Manter pasta no git
│
├─ 📁 config/
│  └─ config.php                       # Configurações da aplicação
│
├─ 📁 core/                            # Classes core reutilizáveis
│  ├─ Database.php                     # Conexão MySQL
│  ├─ Helper.php                       # Funções auxiliares
│  ├─ Response.php                     # Formatador de respostas
│  └─ Validator.php                    # Validador de dados
│
├─ 📁 models/                          # Modelos de dados
│  └─ DenunciaModel.php                # Operações de denúncias
│
├─ 📁 controllers/                     # Controladores
│  └─ DenunciaController.php           # Lógica de denúncias
│
├─ 📁 database/
│  └─ schema.sql                       # Script SQL de criação
│
├─ 📁 logs/                            # Arquivos de log
│  ├─ error.log
│  ├─ info.log
│  ├─ database.log
│  └─ debug.log
│
├─ 📄 README.md                        # Documentação completa
├─ 📄 QUICKSTART.md                    # Guia rápido de início
└─ 📄 .htaccess                        # (Opcional) Configurações Apache

```

---

## 🚀 Como Iniciar

### 1️⃣ Criar Estrutura de Pastas

```bash
# Criar diretórios
mkdir -p ouvidoria/{public/uploads,config,core,models,controllers,database,logs}
```

### 2️⃣ Copiar Todos os Arquivos

Copie os arquivos para suas respectivas pastas:

- `config/config.php` → config/
- `Database.php, Helper.php, Response.php, Validator.php` → core/
- `DenunciaModel.php` → models/
- `DenunciaController.php` → controllers/
- `index.html, dashboard.html, rastrear.html, api.php` → public/
- `schema.sql` → database/

### 3️⃣ Criar Banco de Dados

```bash
cd ouvidoria/
mysql -u root -p < database/schema.sql
```

### 4️⃣ Configurar config.php

Editar `config/config.php` com suas credenciais MySQL

### 5️⃣ Iniciar Servidor

```bash
php -S localhost:8000 -t public/
```

### 6️⃣ Acessar

- Frontend: http://localhost:8000
- Dashboard: http://localhost:8000/dashboard.html
- Rastrear: http://localhost:8000/rastrear.html

---

## 📋 Checklist de Implementação

- [ ] Criar pastas
- [ ] Copiar arquivos PHP
- [ ] Copiar arquivos HTML
- [ ] Executar schema.sql
- [ ] Configurar config.php
- [ ] Testar conexão ao banco
- [ ] Iniciar servidor PHP
- [ ] Testar formulário
- [ ] Testar API
- [ ] Verificar uploads
- [ ] Revisar logs

---

## 🔑 Arquivos Críticos

```
ESSENCIAL:
├─ config.php          (Credenciais do banco)
├─ Database.php        (Conexão ao MySQL)
├─ schema.sql          (Estrutura do banco)
└─ api.php             (Router da API)
```

---

## 🎯 Primeiros Passos após Instalação

### 1. Testar Conexão ao Banco

```bash
php -r "
require 'config/config.php';
require 'core/Database.php';
\$db = new Database();
echo 'Conexão OK';
"
```

### 2. Testar API

```bash
curl http://localhost:8000/api.php
```

### 3. Criar Denúncia Teste

```bash
curl -X POST http://localhost:8000/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "tipo": "fraude",
    "descricao": "Denúncia de teste com no mínimo vinte caracteres",
    "local": "administrativo",
    "anonimato": true
  }'
```

### 4. Verificar Banco

```sql
SELECT * FROM denuncias;
SELECT * FROM evidencias;
```

---

## 🛠️ Configurações Importantes

### Arquivo: config/config.php

```php
// Banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'ouvidoria_db');

// Segurança
define('JWT_SECRET', 'ALTERE_ISSO_AGORA');
define('ENCRYPTION_KEY', 'CHAVE_AES256_FORTE');

// Upload
define('UPLOAD_DIR', __DIR__ . '/../uploads/evidencias/');
define('UPLOAD_MAX_SIZE', 52428800); // 50MB

// Email (para futuro)
define('MAIL_FROM', 'ouvidoria@empresa.com');
define('SMTP_HOST', 'smtp.gmail.com');
```

---

## 📱 URLs da Aplicação

| Página | URL | Descrição |
|--------|-----|-----------|
| **Frontend** | `/` | Formulário de denúncia |
| **API** | `/api.php` | Endpoints REST |
| **Dashboard** | `/dashboard.html` | Painel admin |
| **Rastrear** | `/rastrear.html` | Consultar protocolo |

---

## 🔌 Endpoints da API

```
POST   /api.php → Criar denúncia
GET    /api.php → Listar denúncias
GET    /api.php?protocolo=XXX → Buscar por protocolo
POST   /api.php?upload={id} → Upload de arquivo
GET    /api.php?stats=1 → Estatísticas
```

---

## 📊 Banco de Dados

Tabelas criadas automaticamente:
- `denuncias` - Denúncias registradas
- `evidencias` - Arquivos enviados
- `comentarios` - Comentários internos
- `usuarios` - Usuários do sistema
- `logs_auditoria` - Log de ações
- `configuracoes` - Configurações do sistema
- `notificacoes` - Notificações
- `vw_denuncias_resumo` - View resumida
- `vw_estatisticas_diarias` - View de stats

---

## 🔒 Segurança

### ✅ Implementado

- [x] Criptografia AES-256
- [x] SQL Injection prevention (prepared statements)
- [x] XSS protection (htmlspecialchars)
- [x] CSRF protection (tokens)
- [x] Anonimato garantido
- [x] HTTPS headers
- [x] Logs de auditoria
- [x] Validação de dados

### 📝 To Do

- [ ] Implementar autenticação 2FA
- [ ] Dashboard administrativo completo
- [ ] Sistema de notificação por email
- [ ] Integração com AWS S3
- [ ] Machine Learning para categorização
- [ ] API rate limiting

---

## 📈 Próximas Versões

**v1.1:**
- Autenticação de usuários
- Dashboard melhorado
- Notificações por email

**v1.2:**
- Integração com AWS
- Mobile app
- Relatórios avançados

**v2.0:**
- Machine Learning
- Análise de sentimento
- API GraphQL

---

## 🆘 Troubleshooting

### Erro: "Cannot find Database"
```bash
# Verificar se arquivo existe
ls -la core/Database.php

# Verificar se caminho está correto em require_once
```

### Erro: "Connection refused"
```bash
# MySQL não está rodando
sudo service mysql start

# Ou verificar porta
netstat -an | grep 3306
```

### Erro: "Permission denied"
```bash
# Dar permissão nas pastas
chmod 777 public/uploads/
chmod 777 logs/
chmod 644 config/config.php  # Mais seguro
```

### Erro: "404 Not Found"
```bash
# Verificar router em api.php
# Verificar estructura de pastas
# Verificar permissões de arquivo
```

---

## 📞 Suporte e Contato

**Problemas?**

1. Verificar logs em `logs/error.log`
2. Testar endpoints isoladamente
3. Validar estrutura de pastas
4. Revisar credenciais de banco

---

## 📄 Licença

Proprietário - Todos os direitos reservados.

---

**Sistema de Ouvidoria Web v1.0**
Desenvolvido com ❤️ para segurança e anonimato
