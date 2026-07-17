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
            'biensDisponibles',
            'totalBiens',
            'totalDisponibles',
            'totalAgences',
            'totalClients',
            'departements',
            'typesBiens'
        ));
    }

    // ═══ LISTE DES BIENS ═══
    public function index(Request $request)
    {
        $query = Bien::with(['typeBien', 'agence', 'departement', 'ville', 'quartier']);

        if ($request->filled('id_departement')) {
            $query->where('id_departement', $request->id_departement);
        }
        if ($request->filled('id_ville')) {
            $query->where('id_ville', $request->id_ville);
        }
        if ($request->filled('id_quartier')) {
            $query->where('id_quartier', $request->id_quartier);
        }
        if ($request->filled('id_typebien')) {
            $query->where('id_typebien', $request->id_typebien);
        }
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('titre_bien', 'like', '%' . $request->q . '%')
                  ->orWhere('description_bien', 'like', '%' . $request->q . '%');
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
            ? Ville::where('id_departement', $request->id_departement)->get()
            : collect();

        return view('biens.index', compact(
            'biens', 'departements', 'typesBiens', 'villes'
        ));
    }

    // ═══ DETAIL D'UN BIEN ═══
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

    // ═══ CRÉER UN BIEN (Admin) ═══
    public function creerBien(Request $request)
    {
        $request->validate([
            'id_typebien'      => 'required|exists:type_biens,id_typebien',
            'titre_bien'       => 'required|string',
            'description_bien' => 'required|string',
            'prix'             => 'required|numeric',
            'superficie'       => 'required|numeric',
            'localisation'     => 'required|string',
            'id_departement'   => 'required|exists:departements,id_departement',
            'id_ville'         => 'required|exists:villes,id_ville',
            'id_quartier'      => 'nullable|exists:quartiers,id_quartier',
            'photos'           => 'nullable|array|max:5',
            'photos.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload des photos
        $photosPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('biens', 'public');
                $photosPaths[] = $path;
            }
        }

        Bien::create([
            'id_agence'        => session('admin')->id_agence,
            'id_admin'         => session('admin')->id_admin,
            'id_typebien'      => $request->id_typebien,
            'titre_bien'       => $request->titre_bien,
            'description_bien' => $request->description_bien,
            'prix'             => $request->prix,
            'superficie'       => $request->superficie,
            'localisation'     => $request->localisation,
            'id_departement'   => $request->id_departement,
            'id_ville'         => $request->id_ville,
            'id_quartier'      => $request->id_quartier,
            'statut'           => 'disponible',
            'photos'           => $photosPaths,
        ]);

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien créé avec succès !');
    }

    // ═══ MODIFIER UN BIEN ═══
    public function modifierBien(Request $request, $id)
    {
        $bien = Bien::where('id_bien', $id)
                    ->where('id_agence', session('admin')->id_agence)
                    ->firstOrFail();

        $request->validate([
            'titre_bien'       => 'required|string',
            'description_bien' => 'required|string',
            'prix'             => 'required|numeric',
            'superficie'       => 'required|numeric',
            'localisation'     => 'required|string',
            'id_typebien'      => 'required|exists:type_biens,id_typebien',
            'id_departement'   => 'required|exists:departements,id_departement',
            'id_ville'         => 'required|exists:villes,id_ville',
            'id_quartier'      => 'nullable|exists:quartiers,id_quartier',
            'photos'           => 'nullable|array|max:5',
            'photos.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'photos']);

        if ($request->hasFile('photos')) {
            $photosPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('biens', 'public');
                $photosPaths[] = $path;
            }
            $data['photos'] = $photosPaths;
        }

        $bien->update($data);

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien modifié avec succès !');
    }

    // ═══ MODIFIER STATUT ═══
    public function modifierStatutBien(Request $request, $id)
    {
        $bien = Bien::where('id_bien', $id)
                    ->where('id_agence', session('admin')->id_agence)
                    ->firstOrFail();

        $bien->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut modifié avec succès !');
    }

    // ═══ SUPPRIMER UN BIEN ═══
    public function supprimerBien($id)
    {
        Bien::where('id_bien', $id)
            ->where('id_agence', session('admin')->id_agence)
            ->firstOrFail()->delete();

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien supprimé avec succès !');
    }
}
