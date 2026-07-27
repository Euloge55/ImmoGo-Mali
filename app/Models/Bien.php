<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    protected $primaryKey = 'id_bien';

    protected $fillable = [
        'id_agence',
        'id_admin',
        'id_typebien',
        'titre_bien',
        'description_bien',
        'prix',
        'superficie',
        'localisation',
        'statut',
        'type_contrat',
        'nb_mois_avance',
        'caution_eau',
        'caution_electricite',
        'photos',
        'id_departement',
        'id_ville',
        'id_quartier',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class, 'id_agence', 'id_agence');
    }

    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'id_admin', 'id_admin');
    }

    public function typeBien()
    {
        return $this->belongsTo(TypeBien::class, 'id_typebien', 'id_typebien');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'id_bien', 'id_bien');
    }

    public function favoris()
    {
        return $this->hasMany(Favoris::class, 'id_bien', 'id_bien');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'id_departement', 'id_departement');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville', 'id_ville');
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class, 'id_quartier', 'id_quartier');
    }
}
