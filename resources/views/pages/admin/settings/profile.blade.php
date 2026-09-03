<x-admin.settings.layout active="profile">
    <div class="settings-grid">
        <form class="settings-column" aria-label="Configurações do perfil">
            <x-admin.settings.section-card title="Informações Pessoais" subtitle="Atualize seus dados pessoais" icon="bi-person-fill">
                <div class="settings-field">
                    <label for="profile-name" class="settings-label">Nome Completo</label>
                    <div class="position-relative">
                        <i class="bi bi-person-fill settings-input-icon" aria-hidden="true"></i>
                        <input id="profile-name" type="text" class="form-control settings-control" value="Admin User">
                    </div>
                </div>
                <div class="settings-field">
                    <label for="profile-email" class="settings-label">E-mail</label>
                    <div class="position-relative">
                        <i class="bi bi-envelope-fill settings-input-icon" aria-hidden="true"></i>
                        <input id="profile-email" type="email" class="form-control settings-control pe-5" value="admin@deploy.com.br">
                        <span class="badge bg-success-subtle text-success position-absolute top-50 end-0 translate-middle-y me-2 fw-medium">Verificado</span>
                    </div>
                </div>
                <div class="settings-field">
                    <label for="profile-phone" class="settings-label">Telefone</label>
                    <div class="position-relative">
                        <i class="bi bi-telephone-fill settings-input-icon" aria-hidden="true"></i>
                        <input id="profile-phone" type="tel" class="form-control settings-control" value="+55 (11) 99999-0000">
                    </div>
                </div>
                <div class="settings-field">
                    <label for="profile-role" class="settings-label">Cargo</label>
                    <div class="position-relative">
                        <i class="bi bi-briefcase-fill settings-input-icon" aria-hidden="true"></i>
                        <input id="profile-role" type="text" class="form-control settings-control" value="Administrador">
                    </div>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Preferências" subtitle="Personalize sua experiência" icon="bi-sliders" tone="purple">
                <div class="settings-field">
                    <label for="profile-language" class="settings-label">Idioma <small>Idioma da interface</small></label>
                    <select id="profile-language" class="form-select settings-control">
                        <option selected>Português (BR)</option>
                        <option>English (US)</option>
                        <option>Español</option>
                    </select>
                </div>
                <div class="settings-field">
                    <label for="profile-timezone" class="settings-label">Fuso Horário <small>Hora local</small></label>
                    <select id="profile-timezone" class="form-select settings-control">
                        <option selected>America/São_Paulo (UTC-3)</option>
                        <option>America/Manaus (UTC-4)</option>
                        <option>UTC</option>
                    </select>
                </div>
                <fieldset class="settings-field">
                    <legend class="settings-label">Tema <small>Aparência visual</small></legend>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn settings-choice active"><i class="bi bi-sun-fill me-2" aria-hidden="true"></i>Claro</button>
                        <button type="button" class="btn settings-choice"><i class="bi bi-moon-fill me-2" aria-hidden="true"></i>Escuro</button>
                    </div>
                </fieldset>
            </x-admin.settings.section-card>

            <div class="settings-save-bar">
                <button type="button" class="btn btn-primary"><i class="bi bi-floppy-fill me-2" aria-hidden="true"></i>Salvar Alterações</button>
            </div>
        </form>

        <aside class="settings-column settings-sidebar" aria-label="Resumo do perfil">
            <x-admin.settings.section-card title="Foto de Perfil" class="settings-side-card">
                <div class="text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ asset('images/admin/admin-user.png') }}" alt="Foto de Admin User" class="settings-profile-avatar rounded-circle border">
                        <span class="settings-avatar-action"><i class="bi bi-camera-fill" aria-hidden="true"></i></span>
                    </div>
                    <strong class="d-block small">Admin User</strong>
                    <span class="d-block text-secondary mb-3" style="font-size: 10px">Administrador</span>
                    <label for="profile-photo" class="btn btn-primary-subtle text-primary btn-sm w-100 mb-2">
                        <i class="bi bi-cloud-arrow-up-fill me-1" aria-hidden="true"></i> Fazer Upload
                    </label>
                    <input id="profile-photo" type="file" class="visually-hidden" accept="image/png,image/jpeg,image/gif">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-trash-fill me-1" aria-hidden="true"></i> Remover Foto</button>
                    <small class="d-block text-secondary mt-3" style="font-size: 9px">JPG, PNG ou GIF. Máximo 5MB.</small>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Atividade da Conta" class="settings-side-card">
                <div class="settings-detail-row">
                    <div class="d-flex gap-2 align-items-center mb-1">
                        <span class="settings-detail-icon settings-card-icon-blue"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                        <span class="settings-detail-title">Último acesso</span>
                    </div>
                    <div class="ps-5 settings-detail-value">15 Jun 2025, 09:42</div>
                    <div class="ps-5 settings-detail-meta">São Paulo, Brasil</div>
                </div>
                <div class="settings-detail-row">
                    <div class="d-flex gap-2 align-items-center mb-1">
                        <span class="settings-detail-icon settings-card-icon-green"><i class="bi bi-calendar-check-fill" aria-hidden="true"></i></span>
                        <span class="settings-detail-title">Conta criada</span>
                    </div>
                    <div class="ps-5 settings-detail-value">01 Jan 2024</div>
                    <div class="ps-5 settings-detail-meta">Há 18 meses</div>
                </div>
                <div class="settings-detail-row">
                    <div class="d-flex gap-2 align-items-center mb-1">
                        <span class="settings-detail-icon settings-card-icon-yellow"><i class="bi bi-laptop-fill" aria-hidden="true"></i></span>
                        <span class="settings-detail-title">Sessões ativas</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2 ps-5">
                        <div>
                            <div class="settings-detail-value">1 dispositivo</div>
                            <div class="settings-detail-meta">Chrome · Windows</div>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-medium"><span class="settings-status-dot me-1"></span>Ativo</span>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-3"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i> Encerrar todas as sessões</button>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Acesso Rápido" class="settings-side-card">
                <a href="{{ route('admin.settings.security') }}#password" class="settings-quick-link">
                    <span><i class="bi bi-lock-fill me-2" aria-hidden="true"></i>Alterar Senha</span><i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="{{ route('admin.settings.security') }}#two-factor" class="settings-quick-link">
                    <span><i class="bi bi-shield-fill me-2" aria-hidden="true"></i>Autenticação 2FA</span>
                    <span><span class="badge bg-warning-subtle text-warning-emphasis fw-medium me-1">Desativado</span><i class="bi bi-chevron-right" aria-hidden="true"></i></span>
                </a>
                <a href="{{ route('admin.settings.notifications') }}" class="settings-quick-link">
                    <span><i class="bi bi-bell-fill me-2" aria-hidden="true"></i>Notificações</span><i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
            </x-admin.settings.section-card>
        </aside>
    </div>
</x-admin.settings.layout>
