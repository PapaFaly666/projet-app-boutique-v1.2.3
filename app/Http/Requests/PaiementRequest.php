<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaiementRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajustez selon vos besoins d'autorisation
    }

    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
        ];
    }
}