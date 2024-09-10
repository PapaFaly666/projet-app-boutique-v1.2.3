<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'montant', 'date','montant','montantDu','montantRestant'];

    public function client(){
        return $this->belongsTo(Client::class);  // Client est une relation 1:N avec la table clients. On utilise le nom de la table dans la méthode belongsTo()
    }


    public function articles(){
        return $this->belongsToMany(Article::class,'article_dette')
            ->withPivot('quantite')
            ->withPivot('prix')
            ->withTimestamps()
        ;
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class); // Une dette peut avoir plusieurs paiements
    }
}
