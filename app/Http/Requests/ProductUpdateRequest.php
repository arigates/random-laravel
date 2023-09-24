<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $name
 * @property mixed $min_price
 * @property mixed $max_price
 */

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'min_price' => ['required', 'numeric', 'lte:max_price'],
            'max_price' => ['required', 'numeric', 'gte:min_price'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'min_price' => intval(str_replace('.', '', $this->min_price)),
            'max_price' => intval(str_replace('.', '', $this->max_price)),
        ]);
    }
}
