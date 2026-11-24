╔════════════════════════════════════════════════════════════════════════════╗
║                                                                            ║
║         🎉 SISTEMA DE OUVIDORIA WEB - GUIA COMPLETO DE INSTALAÇÃO 🎉     ║
║                                                                            ║
║                     ✅ PRONTO PARA USAR | 📦 PASTA ÚNICA                 ║
║                                                                            ║
╚════════════════════════════════════════════════════════════════════════════╝

================================================================================
📋 LEIA PRIMEIRO - ANTES DE FAZER QUALQUER COISA
================================================================================

Você recebeu um SISTEMA COMPLETO de ouvidoria corporativa em UMA ÚNICA PASTA.

Esta pasta contém TUDO que você precisa:
  ✅ Código PHP (backend)
  ✅ Páginas HTML (frontend)
  ✅ Banco de Dados (SQL)
  ✅ Documentação
  ✅ Guias de instalação

================================================================================
🎯 PASSO 1: VERIFICAR PRÉ-REQUISITOS (5 MINUTOS)
================================================================================

Você precisa ter instalado em seu computador:

✓ PHP 7.4 ou superior
  └─ Verificar: php --version

✓ MySQL 5.7 ou superior
  └─ Verificar: mysql --version

✓ VSCode (recomendado, mas opcional)
  └─ Ou qualquer editor de texto

✓ Navegador moderno (Chrome, Firefox, Edge, Safari)

SE NÃO TIVER:
  → Instale PHP: https://www.php.net/downloads
  → Instale MySQL: https://dev.mysql.com/downloads/
  → Instale VSCode: https://code.visualstudio.com/

================================================================================
🚀 PASSO 2: CRIAR BANCO DE DADOS (5 MINUTOS)
================================================================================

OPÇÃO A - COM TERMINAL (Linux/Mac/Windows):

1. Abra o terminal/cmd
2. Conecte ao MySQL:
   
   mysql -u root -p
   
   (Digite sua senha do MySQL)

3. Execute o script SQL:

   source /caminho/completo/para/SISTEMA_OUVIDORIA_COMPLETO/database/schema.sql

   Exemplo Windows:
   source C:\Users\seu_usuario\SISTEMA_OUVIDORIA_COMPLETO\database\schema.sql

   Exemplo Linux/Mac:
   source /home/seu_usuario/SISTEMA_OUVIDORIA_COMPLETO/database/schema.sql

4. Pronto! O banco foi criado.

---

OPÇÃO B - COM phpMyAdmin (Interface Gráfica):

1. Abra phpMyAdmin (geralmente: http://localhost/phpmyadmin)
2. Faça login
3. Vá em: Importar (ou Import)
4. Selecione o arquivo: database/schema.sql
5. Clique em: Importar
6. Pronto!

================================================================================
⚙️ PASSO 3: CONFIGURAR O SISTEMA (2 MINUTOS)
================================================================================

1. Abra o arquivo: config/config.php
   (com seu editor de texto ou VSCode)

2. Procure pelas linhas:

   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ouvidoria_db');

3. Edite com suas credenciais MySQL:

   Exemplo:
   define('DB_HOST', 'localhost');
   define('DB_USER', 'seu_usuario_mysql');
   define('DB_PASS', 'sua_senha_mysql');
   define('DB_NAME', 'ouvidoria_db');

4. Salve o arquivo (Ctrl+S ou Cmd+S)

5. PRONTO! O sistema está configurado.

================================================================================
🌐 PASSO 4: INICIAR O SERVIDOR PHP (1 MINUTO)
================================================================================

WINDOWS:
1. Pressione: Windows + R
2. Digite: cmd
3. Navegue até a pasta:
   cd C:\Users\seu_usuario\SISTEMA_OUVIDORIA_COMPLETO
   (ou o caminho onde está a pasta)
4. Digite:
   php -S localhost:8000 -t public/
5. Você verá:
   [Thu Apr 26 15:00:00 2026] PHP 7.4.0 Server running...
   Listening on http://localhost:8000

---

LINUX/MAC:
1. Abra o terminal
2. Navegue até a pasta:
   cd /caminho/para/SISTEMA_OUVIDORIA_COMPLETO
3. Digite:
   php -S localhost:8000 -t public/
4. Você verá a mesma mensagem

================================================================================
🌍 PASSO 5: ACESSAR O SISTEMA (1 MINUTO)
================================================================================

Abra seu navegador e acesse:

🏠 HOME (COMECE AQUI):
   http://localhost:8000/home.html
   └─ Veja a apresentação completa do sistema

📝 FORMULÁRIO DE DENÚNCIA:
   http://localhost:8000/index.html
   └─ Crie uma denúncia de teste

🔍 RASTREAMENTO:
   http://localhost:8000/rastrear.html
   └─ Acompanhe denúncias pelo protocolo

📊 DASHBOARD:
   http://localhost:8000/dashboard.html
   └─ Veja estatísticas e análises

🔌 API:
   http://localhost:8000/api.php
   └─ Endpoints da API

================================================================================
✅ PASSO 6: TESTAR O SISTEMA (5 MINUTOS)
================================================================================

1. Vá em: http://localhost:8000/home.html
   └─ Leia a apresentação

2. Clique em: "Fazer Denúncia"
   └─ Ou acesse: http://localhost:8000/

3. Preencha o formulário:
   ├─ ETAPA 1: Tipo, Local, Descrição
   ├─ ETAPA 2: Pessoas envolvidas, Impactos
   ├─ ETAPA 3: Upload de arquivos (opcional)
   └─ ETAPA 4: Revisão e confirmação

4. Clique em: "Enviar Denúncia"

5. PARABÉNS! Você receberá um PROTOCOLO único
   └─ Exemplo: OUVI20260325140530ABC123

6. Guarde este protocolo

7. Vá em: http://localhost:8000/rastrear.html

8. Digite o protocolo e clique em "Buscar"

9. Veja o status da sua denúncia!

================================================================================
📊 PASSO 7: VERIFICAR NO BANCO DE DADOS (OPCIONAL)
================================================================================

Para confirmar que os dados foram armazenados:

1. Abra o MySQL:
   mysql -u root -p

2. Selecione o banco:
   USE ouvidoria_db;

3. Veja as denúncias:
   SELECT * FROM denuncias;

4. Veja as evidências:
   SELECT * FROM evidencias;

5. Veja tudo com mais detalhe:
   SELECT * FROM denuncias\G

================================================================================
📖 DOCUMENTAÇÃO DISPONÍVEL NESTA PASTA
================================================================================

LEIA PRIMEIRO:
  📄 LEIA-ME-PRIMEIRO.md
     └─ Resumo executivo (10 minutos de leitura)

PARA ENTENDER A ESTRUTURA:
  📄 ESTRUTURA-VISUAL.md
     └─ Árvore visual completa do projeto

PARA COMEÇAR RÁPIDO:
  📄 QUICKSTART.md
     └─ Guia rápido (5 minutos)

PARA DETALHES TÉCNICOS:
  📄 README.md
     └─ Documentação técnica completa (250+ linhas)

PARA ENTENDER TUDO:
  📄 INICIO.md
     └─ Guia completo de inicialização (500+ linhas)

PARA SABER A ORGANIZAÇÃO:
  📄 ESTRUTURA.md
     └─ Detalhamento de pastas

ÍNDICE RÁPIDO:
  📄 INDEX.txt
     └─ Tudo em um arquivo simples

================================================================================
🎯 ENDPOINTS DA API (PARA DESENVOLVEDORES)
================================================================================

POST /api.php
  └─ Criar nova denúncia
     Body (JSON): { tipo, descricao, local, anonimato, ... }

GET /api.php
  └─ Listar denúncias com paginação
     Params: ?page=1&limit=20&tipo=fraude&status=registrada

GET /api.php?protocolo=OUVI20260325140530ABC
  └─ Buscar denúncia específica

GET /api.php?stats=1
  └─ Obter estatísticas gerais

POST /api.php (multipart/form-data)
  └─ Upload de evidência

================================================================================
🔒 SEGURANÇA - IMPORTANTE!
================================================================================

ANTES DE COLOCAR EM PRODUÇÃO, ALTERE:

1. Em config/config.php:

   define('JWT_SECRET', 'ALTERE_ISSO_AGORA');
   define('ENCRYPTION_KEY', 'CHAVE_FORTE_AQUI');

2. Crie senhas fortes para:
   └─ Usuário MySQL
   └─ Usuário admin do sistema

3. Use HTTPS em produção (não HTTP)

4. Fazer backup regular do banco:
   mysqldump -u root -p ouvidoria_db > backup.sql

================================================================================
❓ ERROS COMUNS E SOLUÇÕES
================================================================================

ERRO: "PHP não é reconhecido como comando"
  → PHP não está instalado ou não está no PATH
  → Instale PHP: https://www.php.net/downloads

ERRO: "Connection refused"
  → MySQL não está rodando
  → Inicie MySQL:
    Windows: Services > MySQL > Start
    Linux: sudo service mysql start
    Mac: brew services start mysql

ERRO: "Database not found"
  → Execute schema.sql novamente
  → Verificar se está usando o banco certo

ERRO: "Permission denied" em uploads/
  → Dar permissão:
    chmod 777 public/uploads/

ERRO: "500 Internal Server Error"
  → Verificar logs/error.log
  → Revisar config.php
  → Verificar conexão ao banco

ERRO: "404 Not Found"
  → Verifique a estrutura de pastas
  → Certifique-se que está acessando em localhost:8000

================================================================================
✨ TUDO PRONTO! PRÓXIMAS AÇÕES
================================================================================

1. ✅ Criar banco de dados (schema.sql)
2. ✅ Configurar config.php
3. ✅ Iniciar servidor PHP
4. ✅ Acessar http://localhost:8000/home.html
5. ✅ Criar uma denúncia teste
6. ✅ Rastrear a denúncia
7. ✅ Ver no dashboard
8. ✅ Explorar todas as funcionalidades!

================================================================================
🎓 PRÓXIMOS PASSOS RECOMENDADOS
================================================================================

IMEDIATO:
  1. Ler LEIA-ME-PRIMEIRO.md (5 min)
  2. Seguir este guia (10 min)
  3. Testar o sistema (5 min)

CURTO PRAZO:
  1. Ler README.md para entender endpoints
  2. Testar API com cURL ou Postman
  3. Explorar dashboard
  4. Criar denúncias teste

MÉDIO PRAZO:
  1. Configurar email (SMTP em config.php)
  2. Fazer backup automático do banco
  3. Monitorar logs
  4. Treinar usuários

PRODUÇÃO:
  1. Mudar para HTTPS
  2. Alterar todas as chaves de segurança
  3. Aumentar limite de upload se necessário
  4. Fazer backups regulares

================================================================================
💬 DÚVIDAS?
================================================================================

LEIA ESTES ARQUIVOS:
  1. LEIA-ME-PRIMEIRO.md     (Resumo rápido)
  2. QUICKSTART.md            (Começar em 5 min)
  3. README.md                (Documentação técnica)
  4. INICIO.md                (Guia completo)

TESTE COM CURL:
  curl http://localhost:8000/api.php

USE POSTMAN:
  1. Importe os endpoints
  2. Teste cada rota
  3. Analise as respostas

VERIFIQUE LOGS:
  cat logs/error.log

================================================================================
🎉 PARABÉNS!
================================================================================

Seu sistema de ouvidoria está 100% pronto para usar!

Você tem tudo nesta pasta:
  ✅ PHP backend completo
  ✅ HTML frontend moderno
  ✅ Banco de dados MySQL
  ✅ API REST funcionando
  ✅ Documentação completa

Desenvolvido com SEGURANÇA, PRIVACIDADE e ANONIMATO garantidos! ⚖️✅

================================================================================
📞 RESUMO RÁPIDO
================================================================================

Banco:    ouvidoria_db
User:     root
DB:       MySQL
Host:     localhost
Port:     3306

Sistema:  http://localhost:8000/home.html
API:      http://localhost:8000/api.php

Arquivo config: config/config.php
Arquivo banco:  database/schema.sql

================================================================================

🚀 ESTÁ TUDO PRONTO! DIVIRTA-SE! 🎉

Sistema de Ouvidoria Web v1.0 | Status: ✅ PRONTO | Desenvolvido: 2026
