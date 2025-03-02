<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'governrate' => 'required|string|in:الدقهلية,mansoura',
            'city' => 'required|string|in:المنصورة,sherbin,طنطا',
            'street' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'governrate.in' => 'المحافظة يجب أن تكون "الدقهلية" أو "الغربية".',
            'city.in' => 'المدينة غير صالحة، يُرجى اختيار مدينة صحيحة.',
        ];
    }
}
