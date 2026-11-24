 // Mapa de cores
        const cores = {
            'assedio_moral': '#ef4444',
            'assedio_sexual': '#f97316',
            'discriminacao': '#eab308',
            'fraude': '#3b82f6',
            'conflito_interesse': '#8b5cf6',
            'conduta_inadequada': '#06b6d4',
            'furto': '#ec4899',
            'etica': '#14b8a6',
            'outro': '#6b7280'
        };

        // Carregar dados ao iniciar
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
            carregarDenuncias();
            
            // Atualizar a cada 30 segundos
            setInterval(() => {
                carregarEstatisticas();
                document.getElementById('lastUpdate').textContent = 
                    'Atualizado em ' + new Date().toLocaleTimeString('pt-BR');
            }, 30000);
        });

        function carregarEstatisticas() {
            fetch('/api.php')
                .then(r => r.json())
                .then(data => {
                    // Se for dados da API de stats, atualizar
                    if (data.data.total !== undefined) {
                        atualizarEstatisticas(data.data);
                    } else {
                        // Simular dados para demo
                        atualizarEstatisticas(dadosDemoPadroes());
                    }
                })
                .catch(err => {
                    console.log('Usando dados de demo');
                    atualizarEstatisticas(dadosDemoPadroes());
                });
        }

        function dadosDemoPadroes() {
            return {
                total: 156,
                anonimas: 124,
                identificadas: 32,
                por_tipo: [
                    { tipo: 'fraude', count: 45 },
                    { tipo: 'assedio_moral', count: 38 },
                    { tipo: 'discriminacao', count: 25 },
                    { tipo: 'conduta_inadequada', count: 20 },
                    { tipo: 'assedio_sexual', count: 15 },
                    { tipo: 'furto', count: 10 },
                    { tipo: 'outro', count: 3 }
                ],
                por_status: [
                    { status: 'registrada', count: 60 },
                    { status: 'em_analise', count: 45 },
                    { status: 'em_investigacao', count: 35 },
                    { status: 'concluida', count: 16 }
                ]
            };
        }

        function atualizarEstatisticas(stats) {
            // Atualizar cards
            document.getElementById('totalDenuncias').textContent = stats.total;
            document.getElementById('anonimas').textContent = stats.anonimas;
            document.getElementById('percentAnonimas').textContent = 
                Math.round((stats.anonimas / stats.total) * 100) + '%';

            // Contar por status
            const emAnalise = stats.por_status.find(s => s.status === 'em_analise')?.count || 0;
            const emInvestigacao = stats.por_status.find(s => s.status === 'em_investigacao')?.count || 0;

            document.getElementById('emAnalise').textContent = emAnalise;
            document.getElementById('percentEmAnalise').textContent = 
                Math.round((emAnalise / stats.total) * 100) + '%';

            document.getElementById('emInvestigacao').textContent = emInvestigacao;
            document.getElementById('percentEmInvestigacao').textContent = 
                Math.round((emInvestigacao / stats.total) * 100) + '%';

            // Atualizar gráfico de tipos
            atualizarGraficoTipos(stats.por_tipo);

            // Atualizar gráfico de status
            atualizarGraficoStatus(stats.por_status);
        }

        function atualizarGraficoTipos(tipos) {
            const maxValor = Math.max(...tipos.map(t => t.count));
            const chartTipos = document.getElementById('chartTipos');
            const listaTipos = document.getElementById('listaTipos');

            chartTipos.innerHTML = '';
            listaTipos.innerHTML = '';

            tipos.forEach(tipo => {
                const altura = (tipo.count / maxValor) * 100;
                const nomeTipo = type_label(tipo.tipo);

                const bar = document.createElement('div');
                bar.className = 'chart-bar';
                bar.style.height = altura + '%';
                bar.style.background = cores[tipo.tipo] || '#3b82f6';
                bar.innerHTML = `
                    <div class="chart-bar-value">${tipo.count}</div>
                    <div class="chart-bar-label">${nomeTipo.substring(0, 8)}</div>
                `;
                chartTipos.appendChild(bar);

                const item = document.createElement('div');
                item.className = 'pie-item';
                item.innerHTML = `
                    <div class="pie-color" style="background: ${cores[tipo.tipo] || '#3b82f6'};"></div>
                    <span class="pie-label">${nomeTipo}</span>
                    <span class="pie-value">${tipo.count}</span>
                `;
                listaTipos.appendChild(item);
            });
        }

        function atualizarGraficoStatus(status) {
            const chartStatus = document.getElementById('chartStatus');
            chartStatus.innerHTML = '';

            status.forEach(s => {
                const porcentagem = (s.count / status.reduce((a, b) => a + b.count, 0)) * 100;
                const cor = getCorStatus(s.status);

                const item = document.createElement('div');
                item.style.marginBottom = '15px';
                item.innerHTML = `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="color: var(--gray-700); font-weight: 500;">${status_label(s.status)}</span>
                        <span style="color: var(--gray-600); font-weight: 600;">${s.count} (${Math.round(porcentagem)}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${porcentagem}%; background: ${cor};"></div>
                    </div>
                `;
                chartStatus.appendChild(item);
            });
        }

        function carregarDenuncias() {
            const tipo = document.getElementById('filterTipo').value;
            const status = document.getElementById('filterStatus').value;

            let url = '/api.php?';
            if (tipo) url += 'tipo=' + tipo + '&';
            if (status) url += 'status=' + status + '&';

            // Para esta demo, usar dados locais
            const corpo = document.getElementById('corpoDenuncias');
            corpo.innerHTML = `
                <tr>
                    <td colspan="5" class="loading">Carregando denúncias...</td>
                </tr>
            `;

            // Simular carregamento
            setTimeout(() => {
                const denuncias = gerarDenunciasDemo(tipo, status);
                corpo.innerHTML = '';

                if (denuncias.length === 0) {
                    corpo.innerHTML = '<tr><td colspan="5" class="empty-state">Nenhuma denúncia encontrada</td></tr>';
                    return;
                }

                denuncias.forEach(d => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><strong>${d.protocolo}</strong></td>
                        <td>${type_label(d.tipo)}</td>
                        <td><span class="badge badge-${d.status}">${status_label(d.status)}</span></td>
                        <td>${new Date(d.created_at).toLocaleDateString('pt-BR')}</td>
                        <td>
                            <span class="badge badge-anonima">
                                ${d.anonimato ? '🔒 Anônima' : '👤 Identificada'}
                            </span>
                        </td>
                    `;
                    corpo.appendChild(row);
                });
            }, 500);
        }

        function gerarDenunciasDemo(tipoFiltro, statusFiltro) {
            const todos = [
                { protocolo: 'OUVI20260325140530ABC', tipo: 'fraude', status: 'registrada', created_at: '2026-03-25', anonimato: true },
                { protocolo: 'OUVI20260324180045XYZ', tipo: 'assedio_moral', status: 'em_analise', created_at: '2026-03-24', anonimato: true },
                { protocolo: 'OUVI20260323095200DEF', tipo: 'discriminacao', status: 'em_investigacao', created_at: '2026-03-23', anonimato: false },
                { protocolo: 'OUVI20260322143015GHI', tipo: 'fraude', status: 'concluida', created_at: '2026-03-22', anonimato: true },
                { protocolo: 'OUVI20260321120530JKL', tipo: 'assedio_sexual', status: 'registrada', created_at: '2026-03-21', anonimato: true }
            ];

            return todos.filter(d => {
                if (tipoFiltro && d.tipo !== tipoFiltro) return false;
                if (statusFiltro && d.status !== statusFiltro) return false;
                return true;
            });
        }

        function type_label(tipo) {
            const labels = {
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
            return labels[tipo] || tipo;
        }

        function status_label(status) {
            const labels = {
                'registrada': 'Registrada',
                'em_analise': 'Em Análise',
                'em_investigacao': 'Em Investigação',
                'concluida': 'Concluída',
                'arquivada': 'Arquivada'
            };
            return labels[status] || status;
        }

        function getCorStatus(status) {
            const cores = {
                'registrada': '#3b82f6',
                'em_analise': '#f59e0b',
                'em_investigacao': '#ef4444',
                'concluida': '#10b981',
                'arquivada': '#6b7280'
            };
            return cores[status] || '#3b82f6';
        }

        // Atualizar timestamp
        document.getElementById('lastUpdate').textContent = 
            'Atualizado em ' + new Date().toLocaleTimeString('pt-BR');