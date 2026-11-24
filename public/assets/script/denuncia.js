let etapaAtual = 1;
        let arquivosAdicionados = [];
        let envolvidosAdicionados = [];

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            atualizarBotoes();
            document.getElementById('comunicado').addEventListener('change', function() {
                document.getElementById('quemComunicadoGroup').style.display = this.checked ? 'block' : 'none';
            });
        });

        // Enviar formulário
        document.getElementById('denunciaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarDenuncia();
        });

        function mudarEtapa(direcao) {
            const totalEtapas = 4;
            const novaEtapa = etapaAtual + direcao;

            if (novaEtapa < 1 || novaEtapa > totalEtapas) return;

            if (direcao === 1 && !validarEtapaAtual()) {
                return;
            }

            if (novaEtapa === 4 && direcao === 1) {
                gerarRevisao();
            }

            etapaAtual = novaEtapa;
            mostrarEtapa();
            atualizarBotoes();
            atualizarIndicador();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function mostrarEtapa() {
            document.querySelectorAll('.form-section').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector(`[data-step="${etapaAtual}"]`).classList.add('active');
        }

        function atualizarBotoes() {
            const btnAnterior = document.getElementById('btnAnterior');
            const btnProximo = document.getElementById('btnProximo');
            const btnEnviar = document.getElementById('btnEnviar');

            btnAnterior.style.display = etapaAtual === 1 ? 'none' : 'flex';
            btnProximo.style.display = etapaAtual === 4 ? 'none' : 'flex';
            btnEnviar.style.display = etapaAtual === 4 ? 'flex' : 'none';
        }

        function atualizarIndicador() {
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active', 'completed');
                if (stepNum === etapaAtual) {
                    step.classList.add('active');
                } else if (stepNum < etapaAtual) {
                    step.classList.add('completed');
                }
            });
        }

        function validarEtapaAtual() {
            const form = document.getElementById('denunciaForm');
            const campos = form.querySelectorAll(`[data-step="${etapaAtual}"] [required]`);
            let valido = true;

            campos.forEach(campo => {
                if (!campo.value.trim()) {
                    mostrarErro(`${campo.name} é obrigatório`);
                    valido = false;
                }
            });

            return valido;
        }

        function adicionarEnvolvido() {
            const id = Date.now();
            const html = `
                <div class="envolvido-item" id="envolvido-${id}">
                    <input type="text" placeholder="Nome completo" class="envolvido-nome" value="">
                    <input type="text" placeholder="Cargo/Posição" class="envolvido-cargo" value="">
                    <input type="text" placeholder="Setor/Departamento" class="envolvido-setor" value="">
                    <button type="button" class="btn-remover" onclick="removerEnvolvido(${id})">Remover</button>
                </div>
            `;
            document.getElementById('envolvidosList').insertAdjacentHTML('beforeend', html);
            envolvidosAdicionados.push(id);
        }

        function removerEnvolvido(id) {
            document.getElementById(`envolvido-${id}`).remove();
            envolvidosAdicionados = envolvidosAdicionados.filter(e => e !== id);
        }

        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('uploadArea').classList.add('drag');
        }

        function handleDragLeave(e) {
            document.getElementById('uploadArea').classList.remove('drag');
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('uploadArea').classList.remove('drag');
            const files = e.dataTransfer.files;
            adicionarArquivos(files);
        }

        function handleFileSelect(e) {
            const files = e.target.files;
            adicionarArquivos(files);
        }

        function adicionarArquivos(files) {
            for (let file of files) {
                if (file.size > 52428800) {
                    mostrarErro(`Arquivo ${file.name} excede 50MB`);
                    continue;
                }

                const ext = file.name.split('.').pop().toLowerCase();
                if (!['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'mp4', 'avi'].includes(ext)) {
                    mostrarErro(`Tipo de arquivo não permitido: ${ext}`);
                    continue;
                }

                arquivosAdicionados.push(file);
            }
            atualizarListaArquivos();
        }

        function atualizarListaArquivos() {
            const lista = document.getElementById('arquivosList');
            lista.innerHTML = '';
            arquivosAdicionados.forEach((arquivo, index) => {
                const html = `
                    <div class="arquivo-item">
                        <span>📄 ${arquivo.name}</span>
                        <button type="button" onclick="removerArquivo(${index})" style="background: var(--danger); color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">×</button>
                    </div>
                `;
                lista.insertAdjacentHTML('beforeend', html);
            });
        }

        function removerArquivo(index) {
            arquivosAdicionados.splice(index, 1);
            atualizarListaArquivos();
        }

        function gerarRevisao() {
            const form = document.getElementById('denunciaForm');
            const formData = new FormData(form);

            const tipo = formData.get('tipo');
            const local = formData.get('local');
            const descricao = formData.get('descricao');
            const anonimato = formData.get('anonimato') === '1';

            // Coletar envolvidos
            const envolvidos = [];
            document.querySelectorAll('.envolvido-item').forEach(item => {
                const nome = item.querySelector('.envolvido-nome').value;
                const cargo = item.querySelector('.envolvido-cargo').value;
                const setor = item.querySelector('.envolvido-setor').value;
                if (nome) {
                    envolvidos.push({ nome, cargo, setor });
                }
            });

            // Coletar impactos
            const impactos = [];
            document.querySelectorAll('input[name="impacto"]:checked').forEach(cb => {
                impactos.push(cb.value);
            });

            const revisao = `
                <div style="background: white; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: var(--gray-800);">Resumo da Denúncia</h3>
                    <dl style="display: grid; grid-template-columns: 200px 1fr; gap: 15px;">
                        <dt style="font-weight: bold; color: var(--gray-600);">Tipo:</dt>
                        <dd style="color: var(--gray-800);">${this.getTipoLabel(tipo)}</dd>

                        <dt style="font-weight: bold; color: var(--gray-600);">Local:</dt>
                        <dd style="color: var(--gray-800);">${this.getLocalLabel(local)}</dd>

                        <dt style="font-weight: bold; color: var(--gray-600);">Anonimato:</dt>
                        <dd style="color: var(--gray-800);">${anonimato ? '✓ Anônima' : '✗ Identificada'}</dd>

                        <dt style="font-weight: bold; color: var(--gray-600);">Descrição:</dt>
                        <dd style="color: var(--gray-800); padding: 10px; background: var(--gray-50); border-radius: 6px; grid-column: 1/-1;">${descricao.substring(0, 200)}...</dd>

                        ${envolvidos.length > 0 ? `
                            <dt style="font-weight: bold; color: var(--gray-600);">Envolvidos:</dt>
                            <dd style="color: var(--gray-800); grid-column: 1/-1;">${envolvidos.length} pessoa(s) adicionada(s)</dd>
                        ` : ''}

                        ${impactos.length > 0 ? `
                            <dt style="font-weight: bold; color: var(--gray-600);">Impactos:</dt>
                            <dd style="color: var(--gray-800);">${impactos.join(', ')}</dd>
                        ` : ''}

                        <dt style="font-weight: bold; color: var(--gray-600);">Arquivos:</dt>
                        <dd style="color: var(--gray-800);">${arquivosAdicionados.length} arquivo(s)</dd>
                    </dl>
                </div>
            `;

            document.getElementById('revisor').innerHTML = revisao;
        }

        function getTipoLabel(tipo) {
    const tipos = {
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
    return tipos[tipo] || tipo;
}

function getLocalLabel(local) {
    const locais = {
        'producao': 'Produção',
        'expedicao': 'Expedição',
        'logistica': 'Logística',
        'administrativo': 'Administrativo',
        'externo': 'Externo',
        'outro': 'Outro'
    };
    return locais[local] || local;
}

        function enviarDenuncia() {
            const btnEnviar = document.getElementById('btnEnviar');
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="loading"></span> Enviando...';

            const form = document.getElementById('denunciaForm');
            const formData = new FormData(form);

            // Coletar envolvidos
            const envolvidos = [];
            document.querySelectorAll('.envolvido-item').forEach(item => {
                const nome = item.querySelector('.envolvido-nome').value;
                const cargo = item.querySelector('.envolvido-cargo').value;
                const setor = item.querySelector('.envolvido-setor').value;
                if (nome) {
                    envolvidos.push({ nome, cargo, setor });
                }
            });

            // Coletar impactos
            const impactos = [];
            document.querySelectorAll('input[name="impacto"]:checked').forEach(cb => {
                impactos.push(cb.value);
            });

            const payload = {
                tipo: formData.get('tipo'),
                descricao: formData.get('descricao'),
                local: formData.get('local'),
                data_periodo: formData.get('data_periodo'),
                testemunhas: formData.get('testemunhas'),
                comunicado: formData.get('comunicado') === '1',
                quem_comunicou: formData.get('quem_comunicou'),
                anonimato: formData.get('anonimato') === '1',
                contato: formData.get('contato'),
                envolvidos: envolvidos,
                impacto: impactos
            };

            fetch('/api/denuncias', {  
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            })
            
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '✓ Enviar Denúncia';

                if (data.success) {
                    document.getElementById('denunciaForm').style.display = 'none';
                    document.getElementById('sucessoDiv').style.display = 'block';
                    document.getElementById('protocoloDiv').textContent = `Protocolo: ${data.data.protocolo}`;

                    // Upload de arquivos se houver
                    if (arquivosAdicionados.length > 0) {
                        enviarArquivos(data.data.id);
                    }
                } else {
                    mostrarErro(data.message || 'Erro ao enviar denúncia');
                }
            })
            .catch(error => {
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '✓ Enviar Denúncia';
                mostrarErro('Erro ao enviar denúncia: ' + error.message);
                console.error(error);
            });
        }

        function enviarArquivos(idDenuncia) {
            arquivosAdicionados.forEach(arquivo => {
                const formData = new FormData();
                formData.append('arquivo', arquivo);

                fetch(`/api/denuncias/${idDenuncia}/evidencias`, {
                    
                    method: 'POST',
                    body: formData
                });
            });
        }

        function mostrarErro(mensagem) {
            const container = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = 'alert alert-error show';
            alert.innerHTML = `<strong>Erro:</strong> ${mensagem}`;
            container.appendChild(alert);

            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }

        function mostrarSucesso(mensagem) {
            const container = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = 'alert alert-success show';
            alert.innerHTML = `<strong>Sucesso:</strong> ${mensagem}`;
            container.appendChild(alert);

            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }