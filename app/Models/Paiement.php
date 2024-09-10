<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;


    protected $fillable = [
        'dette_id',
        'montant',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($paiement) {
            $paiement->date = now();
        });
    }

    public function dette()
    {
        return $this->belongsTo(Dette::class); // Un paiement appartient à une seule dette
    }


}