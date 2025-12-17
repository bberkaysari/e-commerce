<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'shipping.full_name' => ['required', 'string', 'max:255'],
            'shipping.phone' => ['nullable', 'string', 'max:50'],
            'shipping.city' => ['required', 'string', 'max:100'],
            'shipping.district' => ['nullable', 'string', 'max:100'],
            'shipping.address_line' => ['required', 'string', 'max:500'],
            'shipping.postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
