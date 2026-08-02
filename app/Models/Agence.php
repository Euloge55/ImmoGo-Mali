<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    protected $primaryKey = 'id_agence';
    protected $fillable = [
        'id_superadmin', 'nom_agence', 'adresse_agence',
        'tel_agence', 'email', 'logo',
        'cinetpay_site_id', 'cinetpay_api_key', 'cinetpay_env',
        'fedapay_secret_key', 'fedapay_env',
    ];

    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'id_superadmin', 'id_superadmin');
    }

    public function administrateurs()
    {
        return $this->hasMany(Administrateur::class, 'id_agence', 'id_agence');
    }

    public function biens()
    {
        return $this->hasMany(Bien::class, 'id_agence', 'id_agence');
    }
}