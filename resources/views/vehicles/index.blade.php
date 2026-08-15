@extends('layouts.app')

@section('title', 'Frota')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Frota</h1>
            <p class="text-body-secondary mb-0">
                {{ $vehicles->count() }} {{ Str::plural('veiculo', $vehicles->count()) }} listado(s).
            </p>
        </div>

        {{-- Sem <script> inline: o Tom Select é inicializado pelo data-attribute. --}}
        <form method="GET" action="{{ route('vehicles.index') }}" class="d-flex align-items-center gap-2">
            <label for="status" class="form-label mb-0 text-nowrap">Filtrar por status</label>

            <select
                id="status"
                name="status"
                class="form-select"
                style="min-width: 16rem"
                data-tom-select='{"allowEmptyOption":true,"create":false,"placeholder":"Todos os status"}'
                onchange="this.form.submit()"
            >
                <option value="">Todos os status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($selectedStatus === $status)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <noscript>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </noscript>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover fleet-table mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Placa</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Ano</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $vehicle)
                        <tr>
                            <td><code>{{ $vehicle->plate }}</code></td>
                            <td>{{ $vehicle->brand }}</td>
                            <td>{{ $vehicle->model }}</td>
                            <td>{{ $vehicle->year }}</td>
                            <td>
                                <span class="badge {{ $vehicle->status->badgeClass() }}">
                                    {{ $vehicle->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-4">
                                Nenhum veiculo cadastrado. Rode <code>make seed</code> para popular a frota.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
