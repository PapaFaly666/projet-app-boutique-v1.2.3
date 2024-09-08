<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['libelle', 'prixUnitaire', 'qteStock'];

    public function scopeDisponible($query, $disponible)
    {
        if ($disponible === 'oui') {
            return $query->where('qteStock', '>', 0);  
        } elseif ($disponible === 'non') {
            return $query->where('qteStock', '=', 0);  
        }

        return $query;  
    }
}
