<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\TypeBien;
use App\Models\Departement;
use App\Models\Ville;
use App\Models\Quartier;
use Illuminate\Http\Request;

class BienWebController extends Controller
{
    // ═══ PAGE D'ACCUEIL ═══
    public function home()
    {
        $biensDisponibles = Bien::with(['typeBien', 'agence', 'departement', 'ville'])
                                ->where('statut', 'disponible')
                                ->latest()
                                ->take(6)
                                ->get();

        $totalBiens       = Bien::count();
        $totalDisponibles = Bien::where('statut', 'disponible')->count();
        $totalAgences     = \App\Models\Agence::count();
        $totalClients     = \App\Models\Client::count();

        $departements = Departement::orderBy('nom_departement')->get();
        $typesBiens   = TypeBien::all();

        return view('home', compact(
            'biensDisponibles', 'totalBiens', 'totalDisponibles',
            'totalAgences', 'totalClients', 'departements', 'typesBiens'
        ));
    }

    // ═══ LISTE DES BIENS ═══
    public function index(Request $request)
    {
        $query = Bien::with(['typeBien', 'agence', 'departement', 'ville', 'quartier']);

        if ($request->filled('id_departement')) $query->where('id_departement', $request->id_departement);
        if ($request->filled('id_ville'))        $query->where('id_ville', $request->id_ville);
        if ($request->filled('id_quartier'))     $query->where('id_quartier', $request->id_quartier);
        if ($request->filled('id_typebien'))     $query->where('id_typebien', $request->id_typebien);
        if ($request->filled('prix_min'))        $query->where('prix', '>=', $request->prix_min);
        if ($request->filled('prix_max'))        $query->where('prix', '<=', $request->prix_max);
        if ($request->filled('statut'))          $query->where('statut', $request->statut);
        if ($request->filled('type_contrat'))    $query->where('type_contrat', $request->type_contrat);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('titre_bien', 'like', "%$q%")
                    ->orWhere('description_bien', 'like', "%$q%");
            });
        }

        if ($request->filled('tri_prix')) {
            $query->orderBy('prix', $request->tri_prix);
        }

        $query->orderByRaw("FIELD(statut, 'disponible', 'reserve', 'loue', 'vendu')");

        $biens        = $query->paginate(9);
        $departements = Departement::orderBy('nom_departement')->get();
        $typesBiens   = TypeBien::all();

        $villes = $request->filled('id_departement')
            ? Ville::where('id_departement', $request->id_departement)->orderBy('nom_ville')->get()
            : collect();

        return view('biens.index', compact('biens', 'departements', 'typesBiens', 'villes'));
    }

    // ═══ DÉTAIL D'UN BIEN ═══
    public function show($id)
    {
        $bien = Bien::with([
            'typeBien', 'agence', 'departement',
            'ville', 'quartier', 'administrateur'
        ])->where('id_bien', $id)->firstOrFail();

        $biensSimilaires = Bien::with(['typeBien', 'ville'])
                               ->where('id_typebien', $bien->id_typebien)
                               ->where('id_bien', '!=', $id)
                               ->where('statut', 'disponible')
                               ->take(3)
                               ->get();

        return view('biens.show', compact('bien', 'biensSimilaires'));
    }
}
