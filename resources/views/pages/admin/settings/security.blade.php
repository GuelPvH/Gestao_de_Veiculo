<x-admin.settings.layout active="security">
    <div class="settings-grid">
        <form class="settings-column" aria-label="Configurações de segurança">
            <x-admin.settings.section-card id="password" title="Alterar Senha" subtitle="Use uma senha forte e exclusiva para sua conta" icon="bi-lock-fill">
                <x-form.field id="current-password" name="current_password" label="Senha atual">
                    <x-form.input id="current-password" name="current_password" type="password" placeholder="Digite sua senha atual" autocomplete="current-password" />
                </x-form.field>
                <x-form.field id="new-password" name="password" label="Nova senha" help="Mínimo de 8 caracteres">
                    <x-form.input id="new-password" name="password" type="password" placeholder="Crie uma nova senha" autocomplete="new-password" />
                </x-form.field>
                <x-form.field id="confirm-password" name="password_confirmation" label="Confirmar senha">
                    <x-form.input id="confirm-password" name="password_confirmation" type="password" placeholder="Repita a nova senha" autocomplete="new-password" />
                </x-form.field>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card id="two-factor" title="Autenticação em Duas Etapas" subtitle="Adicione uma camada extra de proteção ao acesso" icon="bi-shield-lock-fill" tone="purple">
                <div class="settings-option">
                    <div><h3>Aplicativo autenticador</h3><p>Use códigos temporários gerados no seu celular.</p></div>
                    <button type="button" class="btn btn-outline-primary btn-sm">Configurar</button>
                </div>
                <div class="settings-option">
                    <div><h3>Códigos de recuperação</h3><p>Gere códigos para recuperar a conta sem o dispositivo.</p></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Gerar códigos</button>
                </div>
            </x-admin.settings.section-card>

            <div class="settings-save-bar">
                <button type="button" class="btn btn-primary"><i class="bi bi-shield-check me-2" aria-hidden="true"></i>Atualizar Segurança</button>
            </div>
        </form>

        <aside class="settings-column settings-sidebar" aria-label="Sessões e segurança">
            <x-admin.settings.section-card title="Nível de Segurança" class="settings-side-card">
                <x-ui.detail-row title="Segurança média" meta="Ative a autenticação 2FA" icon="bi-shield-exclamation" tone="yellow" class="mb-3" />
                <x-ui.progress value="66" label="Nível de segurança" tone="warning" />
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Sessão Atual" class="settings-side-card">
                <x-ui.detail-row title="Chrome · Windows" meta="São Paulo, Brasil · Agora" icon="bi-laptop-fill" class="mb-3" />
                <x-ui.badge tone="success"><span class="settings-status-dot me-1"></span>Esta sessão</x-ui.badge>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Zona de Risco" class="settings-side-card border-danger-subtle">
                <p class="ui-detail-meta">Encerre todos os acessos ativos, incluindo este dispositivo.</p>
                <button type="button" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i> Encerrar sessões</button>
            </x-admin.settings.section-card>
        </aside>
    </div>
</x-admin.settings.layout>
