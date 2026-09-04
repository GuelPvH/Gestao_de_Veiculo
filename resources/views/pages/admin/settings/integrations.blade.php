<x-admin.settings.layout active="integrations">
    <div class="settings-grid">
        <div class="settings-column">
            <x-admin.settings.section-card title="Integrações Disponíveis" subtitle="Conecte as ferramentas usadas pela sua equipe" icon="bi-plug-fill">
                @foreach ([
                    ['google', 'bi-google', 'Google Workspace', 'Sincronize agenda, e-mail e arquivos da equipe.', 'Conectado', true],
                    ['whatsapp', 'bi-whatsapp', 'WhatsApp Business', 'Centralize conversas de leads e clientes.', 'Conectar', false],
                    ['slack', 'bi-slack', 'Slack', 'Receba eventos de projetos nos seus canais.', 'Conectar', false],
                    ['github', 'bi-github', 'GitHub', 'Acompanhe entregas e atividades dos repositórios.', 'Conectar', false],
                ] as [$id, $icon, $title, $description, $action, $connected])
                    <div class="settings-option">
                        <div class="d-flex gap-3 align-items-center">
                            <span class="settings-integration-logo"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
                            <div><h3>{{ $title }}</h3><p>{{ $description }}</p></div>
                        </div>
                        @if ($connected)
                            <button type="button" class="btn btn-success-subtle text-success btn-sm"><span class="settings-status-dot me-1"></span>{{ $action }}</button>
                        @else
                            <button type="button" class="btn btn-outline-primary btn-sm">{{ $action }}</button>
                        @endif
                    </div>
                @endforeach
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Webhooks" subtitle="Envie eventos do sistema para aplicações externas" icon="bi-broadcast-pin" tone="purple">
                <x-form.field id="webhook-url" name="webhook_url" label="URL de destino">
                    <x-form.input id="webhook-url" name="webhook_url" type="url" placeholder="https://exemplo.com/webhooks/deploy" />
                </x-form.field>
                <x-form.field id="webhook-event" name="webhook_event" label="Evento">
                    <x-form.select id="webhook-event" name="webhook_event">
                        <option value="lead.created" selected>Lead criado</option>
                        <option value="project.updated">Projeto atualizado</option>
                        <option value="payment.confirmed">Pagamento confirmado</option>
                    </x-form.select>
                </x-form.field>
                <div class="settings-save-bar mt-4"><button type="button" class="btn btn-primary">Adicionar Webhook</button></div>
            </x-admin.settings.section-card>
        </div>

        <aside class="settings-column settings-sidebar" aria-label="Resumo das integrações">
            <x-admin.settings.section-card title="Status das Conexões" class="settings-side-card">
                <x-ui.detail-row title="Integrações ativas" value="1 de 4" />
                <x-ui.detail-row title="Última sincronização" value="Hoje, 09:42" />
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Chave da API" class="settings-side-card">
                <p class="ui-detail-meta">Use esta chave apenas em ambientes seguros.</p>
                <div class="input-group input-group-sm mb-2">
                    <input id="api-key" type="password" class="form-control" value="chave_oculta" readonly aria-label="Chave da API">
                    <button type="button" class="btn btn-outline-secondary" data-copy-target="#api-key" aria-label="Copiar chave"><i class="bi bi-copy" aria-hidden="true"></i></button>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm w-100">Gerar nova chave</button>
            </x-admin.settings.section-card>

            <div class="alert alert-primary border-0 small mb-0" role="note">
                <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>As conexões exibidas são apenas uma demonstração visual.
            </div>
        </aside>
    </div>
</x-admin.settings.layout>
