<?php

namespace App\Http\Requests\Ufv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUfvRequest extends FormRequest
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
            'fecha' => 'required|date|unique:ufvs,fecha,' . $this->route('ufv') . ',fecha',
            'valor' => 'required|numeric|min:0',
        ];
    }
}
