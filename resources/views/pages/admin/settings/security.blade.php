<x-admin.settings.layout active="security">
    <div class="settings-grid">
        <form class="settings-column" aria-label="Configurações de segurança">
            <x-admin.settings.section-card id="password" title="Alterar Senha" subtitle="Use uma senha forte e exclusiva para sua conta" icon="bi-lock-fill">
                <div class="settings-field">
                    <label for="current-password" class="settings-label">Senha atual</label>
                    <input id="current-password" type="password" class="form-control settings-control" placeholder="Digite sua senha atual">
                </div>
                <div class="settings-field">
                    <label for="new-password" class="settings-label">Nova senha <small>Mínimo de 8 caracteres</small></label>
                    <input id="new-password" type="password" class="form-control settings-control" placeholder="Crie uma nova senha">
                </div>
                <div class="settings-field">
                    <label for="confirm-password" class="settings-label">Confirmar senha</label>
                    <input id="confirm-password" type="password" class="form-control settings-control" placeholder="Repita a nova senha">
                </div>
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
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="settings-detail-icon settings-card-icon-yellow"><i class="bi bi-shield-exclamation" aria-hidden="true"></i></span>
                    <div><strong class="settings-detail-value d-block">Segurança média</strong><span class="settings-detail-meta">Ative a autenticação 2FA</span></div>
                </div>
                <div class="progress" role="progressbar" aria-label="Nível de segurança" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100" style="height: 6px">
                    <div class="progress-bar bg-warning" style="width: 66%"></div>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Sessão Atual" class="settings-side-card">
                <div class="d-flex gap-2 mb-3">
                    <span class="settings-detail-icon settings-card-icon-blue"><i class="bi bi-laptop-fill" aria-hidden="true"></i></span>
                    <div><strong class="settings-detail-value d-block">Chrome · Windows</strong><span class="settings-detail-meta">São Paulo, Brasil · Agora</span></div>
                </div>
                <span class="badge bg-success-subtle text-success"><span class="settings-status-dot me-1"></span>Esta sessão</span>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Zona de Risco" class="settings-side-card border-danger-subtle">
                <p class="settings-detail-meta">Encerre todos os acessos ativos, incluindo este dispositivo.</p>
                <button type="button" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i> Encerrar sessões</button>
            </x-admin.settings.section-card>
        </aside>
    </div>
</x-admin.settings.layout>
