<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:' . config('books.max_string_length')],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:' . config('books.max_per_page'),
            ],
        ];
    }

    public function getValidatedPerPage(): int
    {
        return (int)($this->validated()['per_page'] ?? config('books.default_per_page'));
    }

    public function getValidatedSearch(): ?string
    {
        return $this->validated()['search'] ?? null;
    }
}
