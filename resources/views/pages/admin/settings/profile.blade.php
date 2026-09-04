<x-admin.settings.layout active="profile">
    <div class="settings-grid">
        <form class="settings-column" aria-label="Configurações do perfil">
            <x-admin.settings.section-card title="Informações Pessoais" subtitle="Atualize seus dados pessoais" icon="bi-person-fill">
                <x-form.field id="profile-name" name="name" label="Nome Completo">
                    <x-form.input id="profile-name" name="name" value="Admin User" icon="bi-person-fill" />
                </x-form.field>
                <x-form.field id="profile-email" name="email" label="E-mail">
                    <div class="position-relative">
                        <x-form.input id="profile-email" name="email" type="email" value="admin@deploy.com.br" icon="bi-envelope-fill" class="pe-5" />
                        <x-ui.badge tone="success" class="position-absolute top-50 end-0 translate-middle-y me-2">Verificado</x-ui.badge>
                    </div>
                </x-form.field>
                <x-form.field id="profile-phone" name="phone" label="Telefone">
                    <x-form.input id="profile-phone" name="phone" type="tel" value="+55 (11) 99999-0000" icon="bi-telephone-fill" />
                </x-form.field>
                <x-form.field id="profile-role" name="role" label="Cargo">
                    <x-form.input id="profile-role" name="role" value="Administrador" icon="bi-briefcase-fill" />
                </x-form.field>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Preferências" subtitle="Personalize sua experiência" icon="bi-sliders" tone="purple">
                <x-form.field id="profile-language" name="language" label="Idioma" help="Idioma da interface">
                    <x-form.select id="profile-language" name="language">
                        <option value="pt-BR" selected>Português (BR)</option>
                        <option value="en-US">English (US)</option>
                        <option value="es">Español</option>
                    </x-form.select>
                </x-form.field>
                <x-form.field id="profile-timezone" name="timezone" label="Fuso Horário" help="Hora local">
                    <x-form.select id="profile-timezone" name="timezone">
                        <option value="America/Sao_Paulo" selected>America/São_Paulo (UTC-3)</option>
                        <option value="America/Manaus">America/Manaus (UTC-4)</option>
                        <option value="UTC">UTC</option>
                    </x-form.select>
                </x-form.field>
                <fieldset class="settings-field">
                    <legend class="settings-label">Tema <small>Aparência visual</small></legend>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn settings-choice active" data-theme-value="light" aria-pressed="true"><i class="bi bi-sun-fill me-2" aria-hidden="true"></i>Claro</button>
                        <button type="button" class="btn settings-choice" data-theme-value="dark" aria-pressed="false"><i class="bi bi-moon-fill me-2" aria-hidden="true"></i>Escuro</button>
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
                        <x-ui.avatar :src="asset('images/admin/admin-user.png')" alt="Foto de Admin User" size="lg" />
                        <span class="settings-avatar-action"><i class="bi bi-camera-fill" aria-hidden="true"></i></span>
                    </div>
                    <strong class="d-block small">Admin User</strong>
                    <span class="settings-role d-block text-secondary mb-3">Administrador</span>
                    <label for="profile-photo" class="btn btn-primary-subtle text-primary btn-sm w-100 mb-2">
                        <i class="bi bi-cloud-arrow-up-fill me-1" aria-hidden="true"></i> Fazer Upload
                    </label>
                    <input id="profile-photo" name="photo" type="file" class="visually-hidden" accept="image/png,image/jpeg,image/gif">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-trash-fill me-1" aria-hidden="true"></i> Remover Foto</button>
                    <small class="settings-upload-help d-block text-secondary mt-3">JPG, PNG ou GIF. Máximo 5MB.</small>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Atividade da Conta" class="settings-side-card">
                <x-ui.detail-row title="Último acesso" value="15 Jun 2025, 09:42" meta="São Paulo, Brasil" icon="bi-clock-history" />
                <x-ui.detail-row title="Conta criada" value="01 Jan 2024" meta="Há 18 meses" icon="bi-calendar-check-fill" tone="green" />
                <x-ui.detail-row title="Sessões ativas" icon="bi-laptop-fill" tone="yellow">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <div class="ui-detail-value">1 dispositivo</div>
                            <div class="ui-detail-meta">Chrome · Windows</div>
                        </div>
                        <x-ui.badge tone="success"><span class="settings-status-dot me-1"></span>Ativo</x-ui.badge>
                    </div>
                </x-ui.detail-row>
                <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-3"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i> Encerrar todas as sessões</button>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Acesso Rápido" class="settings-side-card">
                <a href="{{ route('admin.settings.security') }}#password" class="settings-quick-link">
                    <span><i class="bi bi-lock-fill me-2" aria-hidden="true"></i>Alterar Senha</span><i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="{{ route('admin.settings.security') }}#two-factor" class="settings-quick-link">
                    <span><i class="bi bi-shield-fill me-2" aria-hidden="true"></i>Autenticação 2FA</span>
                    <span><x-ui.badge tone="warning" class="me-1">Desativado</x-ui.badge><i class="bi bi-chevron-right" aria-hidden="true"></i></span>
                </a>
                <a href="{{ route('admin.settings.notifications') }}" class="settings-quick-link">
                    <span><i class="bi bi-bell-fill me-2" aria-hidden="true"></i>Notificações</span><i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
            </x-admin.settings.section-card>
        </aside>
    </div>
</x-admin.settings.layout>
