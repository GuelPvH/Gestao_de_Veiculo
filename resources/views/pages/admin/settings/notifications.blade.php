<x-admin.settings.layout active="notifications">
    <form class="settings-grid" aria-label="Configurações de notificações">
        <div class="settings-column">
            <x-admin.settings.section-card title="Canais de Notificação" subtitle="Escolha como deseja receber atualizações" icon="bi-bell-fill">
                @foreach ([
                    ['email', 'E-mail', 'Receba resumos e alertas importantes por e-mail', true],
                    ['browser', 'Navegador', 'Mostre notificações enquanto o painel estiver aberto', true],
                    ['mobile', 'Dispositivo móvel', 'Envie alertas para o aplicativo cadastrado', false],
                ] as [$id, $title, $description, $enabled])
                    <div class="settings-option">
                        <div><h3>{{ $title }}</h3><p>{{ $description }}</p></div>
                        <div class="form-check form-switch m-0">
                            <input id="channel-{{ $id }}" class="form-check-input" type="checkbox" role="switch" @checked($enabled) aria-label="Ativar notificações por {{ $title }}">
                        </div>
                    </div>
                @endforeach
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Tipos de Alerta" subtitle="Defina quais atividades geram uma notificação" icon="bi-ui-checks" tone="purple">
                @foreach ([
                    ['lead', 'Novos leads', 'Avise quando um novo contato solicitar orçamento', true],
                    ['project', 'Atualizações de projetos', 'Mudanças de etapa, prazo e responsável', true],
                    ['finance', 'Movimentações financeiras', 'Pagamentos recebidos e cobranças vencidas', true],
                    ['report', 'Relatórios semanais', 'Resumo de desempenho enviado toda segunda-feira', false],
                ] as [$id, $title, $description, $enabled])
                    <div class="settings-option">
                        <div><h3>{{ $title }}</h3><p>{{ $description }}</p></div>
                        <div class="form-check form-switch m-0">
                            <input id="alert-{{ $id }}" class="form-check-input" type="checkbox" role="switch" @checked($enabled) aria-label="Ativar {{ $title }}">
                        </div>
                    </div>
                @endforeach
            </x-admin.settings.section-card>

            <div class="settings-save-bar">
                <button type="button" class="btn btn-primary"><i class="bi bi-floppy-fill me-2" aria-hidden="true"></i>Salvar Preferências</button>
            </div>
        </div>

        <aside class="settings-column settings-sidebar" aria-label="Resumo das notificações">
            <x-admin.settings.section-card title="Resumo por E-mail" class="settings-side-card">
                <label for="digest-frequency" class="settings-label d-block mb-2">Frequência</label>
                <select id="digest-frequency" class="form-select settings-control mb-3">
                    <option>Diariamente</option>
                    <option selected>Semanalmente</option>
                    <option>Mensalmente</option>
                </select>
                <p class="settings-detail-meta mb-0">O próximo resumo será enviado na segunda-feira às 08:00.</p>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Status" class="settings-side-card">
                <div class="d-flex gap-2 align-items-center">
                    <span class="settings-detail-icon settings-card-icon-green"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                    <div><strong class="settings-detail-value d-block">Tudo em dia</strong><span class="settings-detail-meta">Nenhum alerta pendente</span></div>
                </div>
            </x-admin.settings.section-card>
        </aside>
    </form>
</x-admin.settings.layout>
