<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_contrat', 'id_mode_paiement', 'montant',
        'date_paiement', 'type_paiement', 'reference'
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat', 'id_contrat');
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class, 'id_mode_paiement', 'id_mode_paiement');
    }
}