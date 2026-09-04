<div class="col-lg-5 bg-white position-relative d-flex flex-column justify-content-center align-items-center">
    <!-- Efeito de Fundo -->
    <div class="position-absolute opacity-25" style="width: 192px; height: 192px; right: 0; top: 0; background: radial-gradient(circle at 100% 0%, #2563eb 0%, transparent 70%);"></div>
    
    <!-- Container do Form -->
    <div class="w-100 px-4 position-relative z-1" style="max-width: 384px;">
        <div class="d-flex flex-column align-items-center gap-3 pb-4">
            <x-request-access.logo size="lg" />
            <div class="text-center">
                <h2 class="text-dark fs-4 fw-bold mb-1">Solicitar Acesso</h2>
                <p class="text-secondary small mb-0" style="max-width: 220px; margin: 0 auto;">Preencha os dados para solicitar acesso à plataforma</p>
            </div>
        </div>
        
        <!-- Formulário Laravel -->
        <form action="{{ url('/solicitar-acesso') }}" method="POST" class="d-flex flex-column mb-4">
            @csrf
            
            <x-request-access.input-field 
                id="email" 
                label="Email corporativo" 
                type="email" 
                placeholder="admin@deploy.com.br" 
                required 
            />
            
            <x-request-access.input-field 
                id="name" 
                label="Nome completo" 
                placeholder="••••••••••••" 
                required 
            />
            
            <div class="form-check d-flex align-items-center gap-2 mb-4 mt-2">
                <input class="form-check-input mt-0 rounded-1" type="checkbox" id="termos" name="termos" required>
                <label class="form-check-label text-secondary" style="font-size: 13px;" for="termos">
                    Li e aceito os termos de uso
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-4" style="border-radius: 10px;">
                Enviar Solicitação
            </button>
        </form>
        
        <x-request-access.divider text="ou" />

        <div class="text-center">
            <a href="{{ url('/login') }}" class="text-secondary text-decoration-none" style="font-size: 14px;">Já tem acesso? Fazer login</a>
        </div>
    </div>

    <!-- Footer Direito -->
    <div class="position-absolute bottom-0 w-100 text-center pb-4 z-1">
        <span class="text-secondary" style="font-size: 12px;">Deploy &copy; {{ date('Y') }} &middot; Todos os direitos reservados</span>
    </div>
</div>