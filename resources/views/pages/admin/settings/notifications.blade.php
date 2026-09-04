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
                        <x-form.switch name="channels[{{ $id }}]" id="channel-{{ $id }}" label="Ativar notificações por {{ $title }}" :checked="$enabled" />
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
                        <x-form.switch name="alerts[{{ $id }}]" id="alert-{{ $id }}" label="Ativar {{ $title }}" :checked="$enabled" />
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
                <x-form.select id="digest-frequency" name="digest_frequency" class="mb-3">
                    <option value="daily">Diariamente</option>
                    <option value="weekly" selected>Semanalmente</option>
                    <option value="monthly">Mensalmente</option>
                </x-form.select>
                <p class="ui-detail-meta mb-0 mt-3">O próximo resumo será enviado na segunda-feira às 08:00.</p>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Status" class="settings-side-card">
                <x-ui.detail-row title="Tudo em dia" meta="Nenhum alerta pendente" icon="bi-check-lg" tone="green" />
            </x-admin.settings.section-card>
        </aside>
    </form>
</x-admin.settings.layout>
