<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'governate' => 'required|string',
            'city' => 'required|string',
            'street' => 'required|string|max:255',
            'building_no' => 'required|string|max:10',
            'phone' => 'required|string|regex:/^01[0-2,5]{1}[0-9]{8}$/',
            'pieces' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:3072',
        ];
    }
    public function messages()
{
    return [
        'phone.required' => 'رقم الهاتف مطلوب',
        'phone.regex' => 'رقم الهاتف غير صالح، يجب أن يبدأ بـ 01 ويليه 9 أرقام',
        'building_no.required' => 'رقم المبنى مطلوب',
        'building_no.max' => 'رقم المبنى لا يجب أن يتجاوز 10 أحرف',
        'image.required' => 'الصورة مطلوبة',
        'images.image' => 'يجب أن تكون الصورة من نوع صالح',
        'images.max' => 'يجب ألا تتجاوز الصورة 3 ميجا',
        'pieces.integer' => 'يجب أن يكون عدد القطع رقمًا صحيحًا',
    ];
}
}
