<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'montant' => $this->montant,
            'client_id' => $this->client_id,
            'montantDu' => $this->montantDu,
            'montantRestant'=>$this->montantRestant
        ];
    }
}
