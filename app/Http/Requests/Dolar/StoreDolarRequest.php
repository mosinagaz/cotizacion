<?php

namespace App\Http\Requests\Dolar;

use Illuminate\Foundation\Http\FormRequest;

class StoreDolarRequest extends FormRequest
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
        $data = $this->all();
        // Check if input is a sequential array (list of objects)
        if (is_array($data) && array_is_list($data)) {
            return [
                '*.fecha' => 'required|date|distinct|unique:dolars,fecha',
                '*.precio_compra' => 'required|numeric|min:0',
                '*.precio_venta' => 'required|numeric|min:0',
            ];
        }

        return [
            'fecha' => 'required|date|unique:dolars,fecha',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ];
    }
}
