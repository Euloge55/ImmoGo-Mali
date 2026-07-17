<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonneesBaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Types de biens ──
        $types = [
            'Appartement', 'Maison', 'Villa', 'Studio',
            'Terrain', 'Bureau', 'Boutique', 'Entrepôt',
            'Duplex', 'Immeuble',
        ];

        foreach ($types as $libelle) {
            DB::table('type_biens')->insertOrIgnore([
                'libelle'    => $libelle,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('✅ ' . count($types) . ' types de biens créés.');

        // ── Modes de paiement ──
        $modes = [
            'CinetPay',
            'Orange Money',
            'Moov Money',
            'Virement bancaire',
            'Espèces',
        ];

        foreach ($modes as $nom) {
            DB::table('mode_paiements')->insertOrIgnore([
                'nom_mode_paiement' => $nom,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
        $this->command->info('✅ ' . count($modes) . ' modes de paiement créés.');
    }
}
