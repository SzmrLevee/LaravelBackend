<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBicycleRequest extends FormRequest
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
            'name' => 'required|string|max:80',
            'wheel_size' => 'required|numeric',
            'gears' => 'required|integer|min:1|max:30',
            'sex' => 'required|in:férfi,női,unisex',
            'type' => 'required|in:MTB,városi,országúti,cross',
            'size' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:80',
            'manufacturer_id' => 'required|exists:manufacturers,id',
        ];
    }
}
