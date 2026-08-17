<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Atualização de veículo.
 *
 * Espelha o StoreVehicleRequest com duas diferenças que valem entender:
 *
 *   - `sometimes` em vez de `required`: atualização parcial é válida: quem
 *     manda só `brand` não deveria ser obrigado a reenviar a placa.
 *   - O `unique` da placa ignora o próprio registro, senão salvar um veículo
 *     sem mexer na placa falharia contra ele mesmo.
 */
final class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $vehicle = $this->route('vehicle');

        // O route binding já resolveu o model (ou devolveu 404 antes de chegar
        // aqui). O `instanceof` existe para a análise estática: `route()`
        // devolve `mixed`, e uma Policy que recebe `mixed` não compila.
        return $vehicle instanceof Vehicle && Gate::allows('update', $vehicle);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $vehicle = $this->route('vehicle');

        return [
            'plate' => [
                'sometimes', 'string', 'max:10',
                Rule::unique('vehicles', 'plate')
                    ->ignore($vehicle instanceof Vehicle ? $vehicle->id : null),
            ],
            'brand' => ['sometimes', 'string', 'max:50'],
            'model' => ['sometimes', 'string', 'max:50'],
            'year' => ['sometimes', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'status' => ['sometimes', Rule::enum(VehicleStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'plate' => 'placa',
            'brand' => 'marca',
            'model' => 'modelo',
            'year' => 'ano',
            'status' => 'situação',
        ];
    }
}
