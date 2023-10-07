<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $description
 * @property string $date
 * @property string $budget
 * @property mixed $document
 * @property array $details
 */
class ActivityCreateRequest extends FormRequest
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
            'description' => ['required', 'max:255'],
            'budget' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'details' => ['required', 'array']
        ];
    }

    protected function prepareForValidation(): void
    {
        $details = $this->details;
        $newDetails = [];
        if ($details) {
            foreach ($details as $detail) {
                $price = intval(str_replace('.', '', $detail['price']));
                $detail['price'] = $price;
                $newDetails[] = $detail;
            }
        }

        $this->merge([
            'budget' => intval(str_replace('.', '', $this->budget)),
            'details' => $newDetails,
        ]);
    }
}
