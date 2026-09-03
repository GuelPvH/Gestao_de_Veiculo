<x-admin.settings.layout active="company">
    <form class="settings-grid" aria-label="Configurações da empresa">
        <div class="settings-column">
            <x-admin.settings.section-card title="Dados da Empresa" subtitle="Informações públicas e fiscais do negócio" icon="bi-building-fill">
                <div class="settings-field">
                    <label for="company-name" class="settings-label">Razão Social</label>
                    <input id="company-name" type="text" class="form-control settings-control" value="Deploy Software House Ltda.">
                </div>
                <div class="settings-field">
                    <label for="company-trade-name" class="settings-label">Nome Fantasia</label>
                    <input id="company-trade-name" type="text" class="form-control settings-control" value="Deploy">
                </div>
                <div class="settings-field">
                    <label for="company-document" class="settings-label">CNPJ</label>
                    <input id="company-document" type="text" class="form-control settings-control" value="12.345.678/0001-90">
                </div>
                <div class="settings-field">
                    <label for="company-email" class="settings-label">E-mail Comercial</label>
                    <input id="company-email" type="email" class="form-control settings-control" value="contato@deploy.com.br">
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Endereço" subtitle="Localização principal da empresa" icon="bi-geo-alt-fill" tone="purple">
                <div class="settings-field">
                    <label for="company-zip" class="settings-label">CEP</label>
                    <input id="company-zip" type="text" class="form-control settings-control" value="01001-000">
                </div>
                <div class="settings-field">
                    <label for="company-address" class="settings-label">Endereço</label>
                    <input id="company-address" type="text" class="form-control settings-control" value="Praça da Sé, 100">
                </div>
                <div class="settings-field">
                    <label for="company-city" class="settings-label">Cidade / Estado</label>
                    <input id="company-city" type="text" class="form-control settings-control" value="São Paulo / SP">
                </div>
            </x-admin.settings.section-card>

            <div class="settings-save-bar">
                <button type="button" class="btn btn-primary"><i class="bi bi-floppy-fill me-2" aria-hidden="true"></i>Salvar Alterações</button>
            </div>
        </div>

        <aside class="settings-column settings-sidebar" aria-label="Identidade da empresa">
            <x-admin.settings.section-card title="Logotipo" class="settings-side-card">
                <div class="text-center">
                    <span class="admin-brand-mark d-inline-grid mb-3" style="width: 72px; height: 72px; font-size: 30px">D</span>
                    <strong class="d-block small mb-3">Deploy Software House</strong>
                    <button type="button" class="btn btn-primary-subtle text-primary btn-sm w-100"><i class="bi bi-cloud-arrow-up-fill me-1" aria-hidden="true"></i> Alterar Logotipo</button>
                    <small class="d-block text-secondary mt-3" style="font-size: 9px">PNG ou SVG. Recomendado 512 × 512 px.</small>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Perfil Empresarial" class="settings-side-card">
                <div class="settings-detail-row">
                    <span class="settings-detail-title d-block mb-1">Plano atual</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="settings-detail-value">Profissional</strong>
                        <span class="badge bg-primary-subtle text-primary">Ativo</span>
                    </div>
                </div>
                <div class="settings-detail-row">
                    <span class="settings-detail-title d-block mb-1">Membros da equipe</span>
                    <strong class="settings-detail-value">8 de 15 usuários</strong>
                </div>
            </x-admin.settings.section-card>
        </aside>
    </form>
</x-admin.settings.layout>
