<div class="col-lg-7 bg-slate-900 position-relative overflow-hidden d-flex flex-column justify-content-between text-white">
    <!-- Efeitos de Fundo -->
    <div class="position-absolute start-0 top-0 w-100 h-100 opacity-10"></div>
    <div class="position-absolute rounded-circle opacity-25" style="width: 500px; height: 500px; left: -80px; top: -120px; background: radial-gradient(circle, #2563eb 0%, transparent 70%);"></div>
    <div class="position-absolute rounded-circle opacity-25" style="width: 384px; height: 384px; left: 524px; top: 600px; background: radial-gradient(circle, #1d4ed8 0%, transparent 70%);"></div>
    
    <!-- Header -->
    <div class="p-5 d-flex align-items-center gap-3">
        <x-request-access.logo size="sm" />
        <div class="fs-5 fw-bold">Deploy</div>
    </div>
    
    <!-- Conteúdo -->
    <div class="px-5 pb-5 mb-auto d-flex flex-column gap-4" style="margin-top: 15%; margin-left: 5%;">
        <div>
            <div class="bg-slate-800 border border-slate-700 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                <div class="bg-primary rounded-circle" style="width: 6px; height: 6px;"></div>
                <span class="text-slate-400" style="font-size: 12px; font-weight: 500;">Plataforma de Gestão</span>
            </div>
        </div>
        <div>
            <h1 class="fw-bold mb-3" style="font-size: 2.5rem; line-height: 1.2;">Solicite seu acesso<br/>com segurança</h1>
            <p class="text-slate-400 fs-6 mb-0" style="max-width: 420px; line-height: 1.5;">Uma plataforma completa para gerenciar leads, projetos, serviços e finanças da sua software house.</p>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="px-5 py-4">
        <span class="text-slate-400" style="font-size: 12px;">Deploy &copy; {{ date('Y') }} &middot; Todos os direitos reservados</span>
    </div>
</div>