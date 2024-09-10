<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DetteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $articleId = $this->input("articles.{$index}.articleId");
                    $article = Article::find($articleId);
                    if ($article && $value > $article->qteStock) {
                        $fail("La quantité de vente ne peut pas dépasser la quantité en stock ({$article->qteStock}).");
                    }
                },
            ],
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . $this->input('montant', PHP_INT_MAX),
            ],
        ];
    }
}