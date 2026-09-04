<main class="container hero-section" style="max-width: 1280px;">
    <div class="row align-items-center g-5">
        <div class="col-lg-7 d-flex flex-column align-items-start gap-4">
            <div class="badge bg-light border text-secondary rounded-pill d-inline-flex align-items-center gap-2 px-3 py-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">
                <span class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></span>
                Desenvolvimento de Software e Soluções Digitais
            </div>
            
            <h1 class="display-4 fw-bold text-dark mb-0 lh-sm">
                Transformamos<br>ideias em <br><span class="text-primary">soluções digitais</span>
            </h1>
            
            <p class="text-secondary fs-5 pe-lg-5 mb-0">
                Desenvolvemos sistemas personalizados, plataformas web e aplicativos escaláveis para impulsionar o crescimento do seu negócio com tecnologia de ponta.
            </p>
            
            <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
                <button class="btn btn-dark px-4 py-3 fw-medium d-flex align-items-center gap-2 shadow-sm">
                    Solicitar orçamento <i class="fas fa-arrow-right ms-2 fs-6"></i>
                </button>
                <button class="btn border px-4 py-3 fw-medium text-dark bg-white">
                    Conhecer nossos serviços
                </button>
            </div>
        </div>

        <div class="col-lg-5 position-relative d-none d-lg-block">
            <div class="position-absolute bg-white rounded-3 p-3 shadow-lg border" style="width: 14rem; top: 40px; left: -40px; z-index: 10;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="bg-success bg-opacity-25 rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-check text-success small"></i>
                    </div>
                    <span class="fw-semibold text-dark small">Deploy Status</span>
                </div>
                <span class="text-secondary" style="font-size: 0.75rem;">Production is online</span>
            </div>

            <div class="bg-light rounded-4 shadow-sm border overflow-hidden" style="height: 500px;">
                <div class="bg-white border-bottom d-flex align-items-center px-3 gap-2" style="height: 40px;">
                    <div class="bg-danger rounded-circle" style="width: 12px; height: 12px;"></div>
                    <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                    <div class="bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                </div>
                
                <div class="p-4 d-flex flex-column gap-3">
                    <div class="bg-white border rounded p-3 d-flex flex-column justify-content-between h-100 shadow-sm" style="height: 120px;">
                        <div class="bg-secondary bg-opacity-25 rounded mb-3" style="width: 80px; height: 12px;"></div>
                        <div class="d-flex align-items-end gap-2" style="height: 48px;">
                            <div class="bg-primary bg-opacity-25 rounded-top w-100" style="height: 40%;"></div>
                            <div class="bg-primary bg-opacity-50 rounded-top w-100" style="height: 60%;"></div>
                            <div class="bg-primary bg-opacity-25 rounded-top w-100" style="height: 30%;"></div>
                            <div class="bg-primary bg-opacity-75 rounded-top w-100" style="height: 90%;"></div>
                            <div class="bg-primary bg-opacity-50 rounded-top w-100" style="height: 50%;"></div>
                            <div class="bg-primary rounded-top w-100" style="height: 100%;"></div>
                        </div>
                    </div>
                    
                    <div class="code-editor rounded p-3 text-white" style="font-family: monospace; font-size: 0.75rem;">
                        <div class="text-secondary mb-2">// API Controller</div>
                        <div><span class="text-pink-400">const</span> <span class="text-blue-300">deployApp</span> = <span class="text-yellow-300">async</span> (req, res) => {</div>
                        <div class="ms-3"><span class="text-pink-400">try</span> {</div>
                        <div class="ms-4 text-white">const solution = await System.build({</div>
                        <div class="ms-5 text-green-300">scalable: true,</div>
                        <div class="ms-5 text-green-300">secure: true,</div>
                        <div class="ms-5 text-green-300">performance: 'optimal'</div>
                        <div class="ms-4 text-white">});</div>
                        <div class="ms-4 text-white mt-2">return res.status(200).json(solution);</div>
                        <div class="ms-3"><span class="text-blue-300">}</span> <span class="text-pink-400">catch</span> (error) {</div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <div class="bg-white border rounded p-3 flex-fill shadow-sm">
                            <div class="bg-secondary bg-opacity-25 rounded mb-2" style="width: 50px; height: 10px;"></div>
                            <div class="fw-bold fs-4 text-dark">99.9%</div>
                        </div>
                        <div class="bg-white border rounded p-3 flex-fill shadow-sm">
                            <div class="bg-secondary bg-opacity-25 rounded mb-2" style="width: 50px; height: 10px;"></div>
                            <div class="fw-bold fs-4 text-dark">Zero</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>