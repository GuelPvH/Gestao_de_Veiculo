<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Criação de veículo.
 *
 * As regras vivem AQUI, e não dentro do controller, por três motivos:
 *
 *   1. O controller volta a ter uma responsabilidade só — orquestrar. Se as
 *      regras crescerem (e elas crescem), ele não cresce com elas.
 *   2. `$request->validated()` devolve apenas o que foi validado. Um
 *      `Vehicle::create($request->all())` aceitaria qualquer campo que o
 *      cliente inventasse.
 *   3. A autorização acontece ANTES da validação e antes de o controller ser
 *      chamado: quem não pode criar recebe 403 sem que uma regra seja avaliada.
 */
final class StoreVehicleRequest extends FormRequest
{
    /**
     * A pergunta "esta pessoa pode fazer isso?" é respondida pela Policy, nunca
     * aqui. Este método só delega — é o ponto de entrada, não a regra.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Vehicle::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'plate' => ['required', 'string', 'max:10', Rule::unique('vehicles', 'plate')],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],

            // `Rule::enum` em vez da lista de strings: um `case` novo no enum
            // passa a ser aceito sem ninguém precisar lembrar deste arquivo.
            'status' => ['sometimes', Rule::enum(VehicleStatus::class)],
        ];
    }

    /**
     * Nomes de campo em português nas mensagens de erro — o cliente da API é
     * uma tela em português, não o desenvolvedor.
     *
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
