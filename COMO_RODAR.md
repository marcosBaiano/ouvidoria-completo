# Como Rodar o Sistema de Ouvidoria

## Opção 1: XAMPP (RECOMENDADO)

1. Instale XAMPP em `C:\xampp`
2. Copie a pasta do projeto para `C:\xampp\htdocs\ouvidoria`
3. Abra o Painel de Controle do XAMPP
4. Inicie o módulo **Apache**
5. Abra http://localhost/ouvidoria/public/denunciak.html no navegador

## Opção 2: PHP Built-in Server (LIMITADO)

### Problema Conhecido
O servidor built-in do PHP tem limitações com routing para APIs. Use XAMPP em produção.

### Para testar (não recomendado):
```bash
cd c:\www\SISTEMA_OUVIDORIA_COMPLETO\public
php -S localhost:8000
```

Acesse: http://localhost:8000

**Limitação**: A API `/api/denuncias` pode não funcionar perfeitamente.

## Opção 3: Nginx (AVANÇADO)

Configure um bloco server no nginx.conf:

```nginx
server {
    listen 8000;
    server_name localhost;
    root c:/www/SISTEMA_OUVIDORIA_COMPLETO/public;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api/ {
        try_files $uri @api;
    }

    location @api {
        rewrite ^/api/(.*)$ /api.php?path=$1 last;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Configurações Obrigatórias

### 1. Banco de Dados MySQL
```bash
mysql -u root -p < database/schema.sql
```

Credenciais em `config/config.php`:
- User: `root`
- Password: `3512`
- Database: `ouvidoria_db`

Se sua senha é diferente, atualize em `config/config.php`.

### 2. Permissões de Pastas
Garanta que o usuário do servidor web tenha permissão de escrita em:
- `/logs/`
- `/uploads/evidencias/`

### 3. Extensões PHP Obrigatórias
- `mysqli` (para banco de dados)
- `openssl` (para criptografia)
- `json` (para APIs)

## Testando a API Direto

```bash
# Criar denúncia
curl -X POST http://localhost:8000/api/denuncias \
  -H "Content-Type: application/json" \
  -d '{"tipo":"fraude","descricao":"Descrição detalhada aqui","local":"administrativo"}'

# Listar denúncias
curl http://localhost:8000/api/denuncias

# Obter estatísticas
curl http://localhost:8000/api/denuncias/stats
```

## Troubleshooting

### Erro: "Access denied for user 'root'@'localhost'"
Atualize a senha em `config/config.php` para a sua senha MySQL real.

### Erro: "Cannot modify header information - headers already sent"
Verifique se há espaços em branco ou BOM no início de arquivos PHP.

### API retorna HTML em vez de JSON
Certifique-se de estar usando XAMPP ou Nginx. O servidor built-in do PHP é limitado.

## Estrutura do Projeto

```
├── public/              # Documentroot do servidor
│   ├── index.html      # Página inicial (SPA)
│   ├── denunciak.html  # Formulário de denúncia
│   ├── router.php      # Router para PHP built-in server
│   ├── api.php         # Endpoint principal da API
│   ├── .htaccess       # Rules para Apache
│   └── assets/         # CSS, JS, imagens
├── config/
│   └── config.php      # Configurações da aplicação
├── models/
│   └── DenunciaModel.php
├── controllers/
│   └── DenunciaController.php
├── core/               # Classes base (Database, Response, Helper, etc)
├── database/
│   └── schema.sql      # Estrutura do banco de dados
└── logs/               # Arquivos de log (criados automaticamente)
```

## Configurações Recomendadas para Produção

- Use um certificado SSL/TLS
- Configure um proxy reverso com Nginx
- Use um gerenciador de processos como PM2 ou Supervisor
- Implemente rate limiting
- Use um firewall WAF (Web Application Firewall)
- Configure backups regulares do banco de dados
