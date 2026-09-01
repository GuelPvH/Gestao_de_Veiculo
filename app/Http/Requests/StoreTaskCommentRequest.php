<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class StoreTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('comment', $this->route('task'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['content' => ['required', 'string', 'max:10000']];
    }
}
