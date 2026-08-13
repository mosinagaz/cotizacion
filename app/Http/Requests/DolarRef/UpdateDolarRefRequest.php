<?php

namespace App\Http\Requests\DolarRef;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDolarRefRequest extends FormRequest
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
            'fecha' => 'required|date|unique:dolar_refs,fecha,' . $this->route('dolar_ref') . ',fecha',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ];
    }
}
