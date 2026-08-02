<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $primaryKey = 'id_contrat';
    protected $fillable = [
        'id_client', 'id_bien',
        'type_contrat', 'statut_contrat', 'date_location'
    ];

    protected $casts = [
        'date_location' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'id_bien', 'id_bien');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id_contrat', 'id_contrat');
    }

    public function vente()
    {
        return $this->hasOne(Vente::class, 'id_contrat', 'id_contrat');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_contrat', 'id_contrat');
    }

    public function calculerSolde() {}
}