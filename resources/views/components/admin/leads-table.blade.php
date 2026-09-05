@php
    $leads = [
        ['id' => '01', 'name' => 'João Silva', 'company' => 'TechBR', 'avatar' => 'joao-silva.png', 'type' => 'Sistema Web', 'typeClass' => '', 'status' => 'Novo', 'statusClass' => 'badge-blue', 'date' => '15 Jun 2025'],
        ['id' => '02', 'name' => 'Maria Santos', 'company' => 'Logística SA', 'avatar' => 'maria-santos.png', 'type' => 'Soft. Custom', 'typeClass' => '', 'status' => 'Em Análise', 'statusClass' => 'badge-yellow', 'date' => '14 Jun 2025'],
        ['id' => '03', 'name' => 'Carlos Mendes', 'company' => 'FinTech Plus', 'avatar' => 'carlos-mendes.png', 'type' => 'Dashboard BI', 'typeClass' => 'type-purple', 'status' => 'Prop. Enviada', 'statusClass' => 'badge-purple', 'date' => '13 Jun 2025'],
        ['id' => '04', 'name' => 'Ana Lima', 'company' => 'Varejo Digital', 'avatar' => 'ana-lima.png', 'type' => 'Landing Page', 'typeClass' => 'type-pink', 'status' => 'Fechado', 'statusClass' => 'badge-green', 'date' => '12 Jun 2025'],
    ];
@endphp

<article class="card dashboard-card leads-card h-100 overflow-hidden">
    <div class="card-header d-flex align-items-center justify-content-between bg-white px-4 py-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h2 class="section-title mb-0">Últimos Leads</h2>
            <span class="badge rounded-pill bg-primary-subtle text-primary">4</span>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3" style="font-size: 11px">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Novo Lead
        </button>
    </div>

    <div class="table-responsive flex-grow-1">
        <table class="table table-hover leads-table mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome &amp; Empresa</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data</th>
                    <th scope="col" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $lead)
                    <tr>
                        <td>{{ $lead['id'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('images/admin/'.$lead['avatar']) }}" alt="" class="admin-avatar-sm rounded-circle border">
                                <div>
                                    <span class="lead-name d-block">{{ $lead['name'] }}</span>
                                    <span class="lead-company d-block">{{ $lead['company'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="type-badge {{ $lead['typeClass'] }} px-2 py-1">{{ $lead['type'] }}</span></td>
                        <td><span class="soft-badge {{ $lead['statusClass'] }}">{{ $lead['status'] }}</span></td>
                        <td class="text-nowrap">{{ $lead['date'] }}</td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                <button type="button" class="btn btn-outline-light icon-button text-primary" aria-label="Visualizar {{ $lead['name'] }}">
                                    <i class="bi bi-eye-fill" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light icon-button" aria-label="Abrir documento de {{ $lead['name'] }}">
                                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white px-4 py-3 border-top">
        <small class="text-secondary" style="font-size: 10px">Mostrando 1–4 de 28 leads</small>
        <nav aria-label="Paginação dos leads">
            <ul class="pagination gap-1 mb-0">
                <li class="page-item disabled"><a class="page-link" href="#" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#" aria-current="page">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#" aria-label="Próxima"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
        </nav>
    </div>
</article>
