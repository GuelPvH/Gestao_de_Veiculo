<x-admin.settings.layout active="company">
    <form class="settings-grid" aria-label="Configurações da empresa">
        <div class="settings-column">
            <x-admin.settings.section-card title="Dados da Empresa" subtitle="Informações públicas e fiscais do negócio" icon="bi-building-fill">
                <x-form.field id="company-name" name="legal_name" label="Razão Social">
                    <x-form.input id="company-name" name="legal_name" value="Deploy Software House Ltda." />
                </x-form.field>
                <x-form.field id="company-trade-name" name="trade_name" label="Nome Fantasia">
                    <x-form.input id="company-trade-name" name="trade_name" value="Deploy" />
                </x-form.field>
                <x-form.field id="company-document" name="document" label="CNPJ">
                    <x-form.input id="company-document" name="document" value="12.345.678/0001-90" />
                </x-form.field>
                <x-form.field id="company-email" name="commercial_email" label="E-mail Comercial">
                    <x-form.input id="company-email" name="commercial_email" type="email" value="contato@deploy.com.br" />
                </x-form.field>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Endereço" subtitle="Localização principal da empresa" icon="bi-geo-alt-fill" tone="purple">
                <x-form.field id="company-zip" name="postal_code" label="CEP">
                    <x-form.input id="company-zip" name="postal_code" value="01001-000" />
                </x-form.field>
                <x-form.field id="company-address" name="address" label="Endereço">
                    <x-form.input id="company-address" name="address" value="Praça da Sé, 100" />
                </x-form.field>
                <x-form.field id="company-city" name="city" label="Cidade / Estado">
                    <x-form.input id="company-city" name="city" value="São Paulo / SP" />
                </x-form.field>
            </x-admin.settings.section-card>

            <div class="settings-save-bar">
                <button type="button" class="btn btn-primary"><i class="bi bi-floppy-fill me-2" aria-hidden="true"></i>Salvar Alterações</button>
            </div>
        </div>

        <aside class="settings-column settings-sidebar" aria-label="Identidade da empresa">
            <x-admin.settings.section-card title="Logotipo" class="settings-side-card">
                <div class="text-center">
                    <span class="admin-brand-mark settings-company-logo d-inline-grid mb-3">D</span>
                    <strong class="d-block small mb-3">Deploy Software House</strong>
                    <button type="button" class="btn btn-primary-subtle text-primary btn-sm w-100"><i class="bi bi-cloud-arrow-up-fill me-1" aria-hidden="true"></i> Alterar Logotipo</button>
                    <small class="settings-upload-help d-block text-secondary mt-3">PNG ou SVG. Recomendado 512 × 512 px.</small>
                </div>
            </x-admin.settings.section-card>

            <x-admin.settings.section-card title="Perfil Empresarial" class="settings-side-card">
                <x-ui.detail-row title="Plano atual">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="ui-detail-value">Profissional</strong>
                        <x-ui.badge tone="primary">Ativo</x-ui.badge>
                    </div>
                </x-ui.detail-row>
                <x-ui.detail-row title="Membros da equipe" value="8 de 15 usuários" />
            </x-admin.settings.section-card>
        </aside>
    </form>
</x-admin.settings.layout>
