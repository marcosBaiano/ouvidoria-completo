 const tiposLabel = {
            'assedio_moral': 'Assédio Moral',
            'assedio_sexual': 'Assédio Sexual',
            'discriminacao': 'Discriminação',
            'fraude': 'Fraude',
            'conflito_interesse': 'Conflito de Interesse',
            'conduta_inadequada': 'Conduta Inadequada',
            'furto': 'Furto',
            'etica': 'Questão Ética',
            'outro': 'Outro'
        };

        const locaisLabel = {
            'producao': 'Produção',
            'expedicao': 'Expedição',
            'logistica': 'Logística',
            'administrativo': 'Administrativo',
            'externo': 'Externo',
            'outro': 'Outro'
        };

        const statusLabel = {
            'registrada': 'Registrada',
            'em_analise': 'Em Análise',
            'em_investigacao': 'Em Investigação',
            'concluida': 'Concluída',
            'arquivada': 'Arquivada'
        };

        const statusCores = {
            'registrada': 'badge-registrada',
            'em_analise': 'badge-analise',
            'em_investigacao': 'badge-investigacao',
            'concluida': 'badge-concluida',
            'arquivada': 'badge-registrada'
        };

        function buscarDenuncia() {
            const protocolo = document.getElementById('protocolo').value.trim();

            if (!protocolo) {
                mostrarErro('Digite o protocolo ou ID da denúncia');
                return;
            }

            const btn = document.querySelector('button');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';

            // Tentar buscar pela API
            fetch(`/api.php?protocolo=${encodeURIComponent(protocolo)}`)
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';

                    if (data.success && data.data) {
                        exibirDenuncia(data.data);
                    } else {
                        // Usar dados de demo
                        const denunciaDemo = gerarDemoPorProtocolo(protocolo);
                        if (denunciaDemo) {
                            exibirDenuncia(denunciaDemo);
                        } else {
                            mostrarErro('Denúncia não encontrada. Verifique o protocolo e tente novamente.');
                        }
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';

                    // Em modo demo, gerar dados
                    const denunciaDemo = gerarDemoPorProtocolo(protocolo);
                    if (denunciaDemo) {
                        exibirDenuncia(denunciaDemo);
                    } else {
                        mostrarErro('Erro ao buscar denúncia. Tente novamente.');
                    }
                });
        }

        function gerarDemoPorProtocolo(protocolo) {
            const demos = [
                {
                    id: '550e8400-e29b-41d4-a716-446655440001',
                    protocolo: 'OUVI20260325140530ABC',
                    tipo: 'fraude',
                    descricao: 'Suspeita de alteração de registros financeiros em departamento administrativo...',
                    local: 'administrativo',
                    data_periodo: '01/03/2026 a 15/03/2026',
                    testemunhas: 'João Silva, Maria Santos',
                    anonimato: true,
                    contato: null,
                    envolvidos: [
                        { nome: 'Gestor X', cargo: 'Gerente', setor: 'Financeiro' }
                    ],
                    created_at: '2026-03-25 14:05:30',
                    updated_at: '2026-03-25 18:30:00',
                    status: 'em_analise',
                    evidencias: [
                        { original_name: 'documento_1.pdf', file_size: 2048576 },
                        { original_name: 'planilha_suspeita.xlsx', file_size: 1048576 }
                    ]
                },
                {
                    id: '550e8400-e29b-41d4-a716-446655440002',
                    protocolo: 'OUVI20260324180045XYZ',
                    tipo: 'assedio_moral',
                    descricao: 'Relatos de assédio moral por supervisor direto...',
                    local: 'producao',
                    data_periodo: 'Últimas 2 semanas',
                    testemunhas: 'Pedro Costa, Ana Lima',
                    anonimato: true,
                    contato: null,
                    envolvidos: [
                        { nome: 'Supervisor Y', cargo: 'Supervisor', setor: 'Produção' }
                    ],
                    created_at: '2026-03-24 18:00:45',
                    updated_at: '2026-03-24 19:45:00',
                    status: 'em_investigacao',
                    evidencias: []
                }
            ];

            return demos.find(d => d.protocolo === protocolo.toUpperCase());
        }

        function exibirDenuncia(denuncia) {
            // Limpar alertas
            document.getElementById('alertError').classList.remove('show');
            document.getElementById('alertInfo').classList.remove('show');

            // Mostrar detalhes
            document.getElementById('detalhes').classList.add('show');
            document.getElementById('dicaPrivacidade').style.display = 'block';

            // Preencher informações básicas
            document.getElementById('protocoloHeader').textContent = denuncia.protocolo;
            document.getElementById('dataRegistro').textContent = formatarData(denuncia.created_at);
            document.getElementById('badgeStatus').innerHTML = 
                `<span class="badge ${statusCores[denuncia.status]}">${statusLabel[denuncia.status]}</span>`;

            // Aba Resumo
            document.getElementById('tipoDenuncia').textContent = tiposLabel[denuncia.tipo] || denuncia.tipo;
            document.getElementById('localDenuncia').textContent = locaisLabel[denuncia.local] || denuncia.local;
            document.getElementById('anonimato').textContent = denuncia.anonimato ? '✓ Anônima' : '✗ Identificada';
            document.getElementById('periodoOcorrido').textContent = denuncia.data_periodo || 'Não especificado';
            document.getElementById('descricao').textContent = denuncia.descricao;

            // Pessoas envolvidas
            const envolvidosHtml = denuncia.envolvidos && denuncia.envolvidos.length > 0
                ? denuncia.envolvidos.map(e => `
                    <div style="padding: 10px 0; border-bottom: 1px solid var(--gray-200);">
                        <div style="font-weight: 600; color: var(--gray-800);">${e.nome || 'Não identificado'}</div>
                        <div style="font-size: 0.9rem; color: var(--gray-600);">${e.cargo || ''} - ${e.setor || ''}</div>
                    </div>
                `).join('')
                : '<p style="color: var(--gray-500);">Nenhuma pessoa identificada</p>';
            document.getElementById('envolvidos').innerHTML = envolvidosHtml;

            // Aba Status
            document.getElementById('statusAtual').innerHTML = 
                `<div class="badge ${statusCores[denuncia.status]}">${statusLabel[denuncia.status]}</div>`;
            document.getElementById('ultimaAtualizacao').textContent = formatarDataHora(denuncia.updated_at);

            // Timeline
            gerarTimeline(denuncia.status);

            // Evidências
            const evidenciasHtml = denuncia.evidencias && denuncia.evidencias.length > 0
                ? `
                    <h3 style="margin-bottom: 15px;">Arquivos Enviados</h3>
                    ${denuncia.evidencias.map((e, i) => `
                        <div class="evidencia-item">
                            <div class="evidencia-info">
                                <div class="evidencia-nome">📄 ${e.original_name}</div>
                                <div class="evidencia-tamanho">${formatarTamanho(e.file_size)}</div>
                            </div>
                        </div>
                    `).join('')}
                `
                : '<p style="color: var(--gray-500); text-align: center; padding: 30px;">Nenhuma evidência enviada</p>';
            document.getElementById('evidenciasContainer').innerHTML = evidenciasHtml;
        }

        function gerarTimeline(statusAtual) {
            const statusos = [
                { status: 'registrada', label: 'Denúncia Registrada', descricao: 'Sua denúncia foi recebida' },
                { status: 'em_analise', label: 'Em Análise', descricao: 'Sendo analisada pela equipe' },
                { status: 'em_investigacao', label: 'Em Investigação', descricao: 'Investigação em andamento' },
                { status: 'concluida', label: 'Concluída', descricao: 'Investigação finalizada' }
            ];

            const statusIndex = statusos.findIndex(s => s.status === statusAtual);
            const timelineHtml = statusos.map((s, i) => `
                <div class="timeline-item ${i <= statusIndex ? 'completed' : ''}">
                    <div class="timeline-content">
                        <div class="timeline-status">${s.label}</div>
                        <div class="timeline-date">${s.descricao}</div>
                    </div>
                </div>
            `).join('');

            document.getElementById('timeline').innerHTML = timelineHtml;
        }

        function alterarAba(abaName) {
            // Remover classe active de todos os botões e conteúdos
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Adicionar classe active
            event.target.classList.add('active');
            document.getElementById(`aba${capitalize(abaName)}`).classList.add('active');
        }

        function formatarData(data) {
            if (!data) return '';
            const d = new Date(data);
            return d.toLocaleDateString('pt-BR');
        }

        function formatarDataHora(data) {
            if (!data) return '';
            const d = new Date(data);
            return d.toLocaleDateString('pt-BR') + ' às ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }

        function formatarTamanho(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unitIndex = 0;
            
            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }
            
            return size.toFixed(2) + ' ' + units[unitIndex];
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function mostrarErro(mensagem) {
            const alert = document.getElementById('alertError');
            alert.textContent = mensagem;
            alert.classList.add('show');

            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        // Permitir buscar com Enter
        document.getElementById('protocolo').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarDenuncia();
            }
        });

        // Focar no campo ao carregar
        document.getElementById('protocolo').focus();