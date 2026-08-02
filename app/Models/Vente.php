<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $primaryKey = 'id_vente';
    protected $fillable = [
        'id_contrat', 'montant_total_vente',
        'date_reserv_vente', 'date_limite_solde_vente'
    ];

    protected $casts = [
        'date_reserv_vente'       => 'datetime',
        'date_limite_solde_vente' => 'datetime',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat', 'id_contrat');
    }
}