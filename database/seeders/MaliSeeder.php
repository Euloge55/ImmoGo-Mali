<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaliSeeder extends Seeder
{
    public function run(): void
    {
        // Vider les tables d'abord
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('quartiers')->truncate();
        DB::table('villes')->truncate();
        DB::table('departements')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $data = [
            // ═══════════════════════════════════
            // 1. DISTRICT DE BAMAKO
            // ═══════════════════════════════════
            'District de Bamako' => [
                'Bamako - Commune I' => [
                    'Banconi', 'Boulkassoumbougou', 'Djélibougou',
                    'Doumanzana', 'Konobougou', 'Sikoroni',
                    'Sotuba', 'Titibougou', 'N\'Tomikorobougou', 'Faladié'
                ],
                'Bamako - Commune II' => [
                    'Bozola', 'Centre commercial', 'Hippodrome',
                    'Médina-Coura', 'Missira', 'Niarela',
                    'Quinzambougou', 'TSF', 'Bagadadji', 'Bouramako'
                ],
                'Bamako - Commune III' => [
                    'Bamako-Coura', 'Daoudabougou', 'Dravéla',
                    'Hamdallaye', 'Koulouninko', 'Ouolofobougou',
                    'Sébénikoro', 'Torokorobougou', 'Zone industrielle', 'Korofina'
                ],
                'Bamako - Commune IV' => [
                    'Kalaban-Coura', 'Lafiabougou', 'Lassa',
                    'Missabougou', 'N\'Tomikorobougou', 'Quartier-Mali',
                    'Sabalibougou', 'Taliko', 'Yirimadio', 'Niamakoro'
                ],
                'Bamako - Commune V' => [
                    'ACI 2000', 'Baco-Djicoroni', 'Kalaban-Coura ACI',
                    'Magnambougou', 'Sabalibougou ACI', 'Sema',
                    'Torokorobougou', 'Djikoroni Para', 'Sogoniko', 'Garantiguibougou'
                ],
                'Bamako - Commune VI' => [
                    'Banconi', 'Faladié', 'Magnambougou',
                    'Missabougou', 'N\'Tabacoro', 'Niamana',
                    'Sénou', 'Sogoniko', 'Sokorodji', 'Yirimadio'
                ],
            ],

            // ═══════════════════════════════════
            // 2. RÉGION DE KAYES
            // ═══════════════════════════════════
            'Kayes' => [
                'Kayes' => [
                    'Kayes Centre', 'Plateau', 'Liberté',
                    'Médine', 'Diboli', 'Ambidédi',
                    'Sandaré', 'Kontéla', 'Maréna', 'Gopela'
                ],
                'Kita' => [
                    'Kita Centre', 'Djidian', 'Makano',
                    'Namala', 'Séféto', 'Toukoto',
                    'Kobiri', 'Bafing', 'Kokofata', 'Sagabari'
                ],
                'Bafoulabé' => [
                    'Bafoulabé Centre', 'Diéma', 'Mahina',
                    'Toukoto', 'Kokofata', 'Fatao',
                    'Oussoubidiagna', 'Diakon', 'Guidimé', 'Tomora'
                ],
                'Nioro du Sahel' => [
                    'Nioro Centre', 'Gadiaba', 'Guidimé',
                    'Simby', 'Sandaré', 'Yéréré',
                    'Troungoumbé', 'Korera', 'Falou', 'Diéma'
                ],
                'Yélimané' => [
                    'Yélimané Centre', 'Gory', 'Kirane',
                    'Konsiga', 'Maréna', 'Sébékoro',
                    'Troungoumbé', 'Bangassi', 'Diafounou', 'Gory Gopela'
                ],
            ],

            // ═══════════════════════════════════
            // 3. RÉGION DE KOULIKORO
            // ═══════════════════════════════════
            'Koulikoro' => [
                'Koulikoro' => [
                    'Koulikoro Centre', 'Basidiala', 'Diago',
                    'Doubabougou', 'Méguetan', 'Tienfala',
                    'Tougouni', 'N\'Gabacoro', 'Sirakorola', 'Kolokani'
                ],
                'Kati' => [
                    'Kati Centre', 'Dio', 'Kalaban-Coro',
                    'Mountougoula', 'N\'Gabacoro', 'Ouéléssébougou',
                    'Sangarébougou', 'Safo', 'Ségala', 'Torodo'
                ],
                'Banamba' => [
                    'Banamba Centre', 'Boron', 'Doura',
                    'Kolokani', 'Madina-Sacko', 'Nossombougou',
                    'Sébéla', 'Toubani', 'Toukoroba', 'Koula'
                ],
                'Dioïla' => [
                    'Dioïla Centre', 'Béléco', 'Fana',
                    'Massigui', 'Nossombougou', 'Ouéléssébougou',
                    'Ségala', 'Sibila', 'Tiéby', 'Kourouba'
                ],
                'Kangaba' => [
                    'Kangaba Centre', 'Gadougou', 'Kéniéroba',
                    'Naréna', 'Nouga', 'Siby',
                    'Benkadi', 'Keniè', 'Figuira', 'Narena'
                ],
            ],

            // ═══════════════════════════════════
            // 4. RÉGION DE SIKASSO
            // ═══════════════════════════════════
            'Sikasso' => [
                'Sikasso' => [
                    'Sikasso Centre', 'Missiri', 'Wayèrèma',
                    'Lafiabougou', 'Médine', 'N\'Golodougou',
                    'Niena', 'Sanekuy', 'Zégoua', 'Logo'
                ],
                'Koutiala' => [
                    'Koutiala Centre', 'Ballé', 'Foroba',
                    'Kaniko', 'Kouniana', 'Mourdiah',
                    'N\'Pébougou', 'Sanekuy', 'Sorobasso', 'Yorobougoula'
                ],
                'Bougouni' => [
                    'Bougouni Centre', 'Garalo', 'Koba',
                    'Koumantou', 'Manankoro', 'N\'Tjila',
                    'Ouéléssébougou', 'Sido', 'Tiéby', 'Yanfolila'
                ],
                'Kadiolo' => [
                    'Kadiolo Centre', 'Fourou', 'Kaboïla',
                    'Kolondiéba', 'Loulouni', 'Missirikoro',
                    'Nièna', 'Sola', 'Zégoua', 'Doussoudiana'
                ],
                'Yorosso' => [
                    'Yorosso Centre', 'Fonsébougou', 'Koury',
                    'Massabla', 'Niena', 'Ourikela',
                    'Péssona', 'Yorobougoula', 'Baya', 'Fakola'
                ],
            ],

            // ═══════════════════════════════════
            // 5. RÉGION DE SÉGOU
            // ═══════════════════════════════════
            'Ségou' => [
                'Ségou' => [
                    'Ségou Centre', 'Bananiba', 'Dougabougou',
                    'Farakou', 'Markala', 'Pelengana',
                    'Sébougou', 'Sossé', 'Souba', 'Tissana'
                ],
                'San' => [
                    'San Centre', 'Bla', 'Dié',
                    'Diéna', 'Dogo', 'Doumanaba',
                    'Ourikela', 'Sarro', 'Sébougou', 'Togo'
                ],
                'Niono' => [
                    'Niono Centre', 'Diabaly', 'Dogofiri',
                    'N\'Débougou', 'Nionokary', 'Siguina',
                    'Sokolo', 'Sossébougou', 'Toridaga', 'Toubacoura'
                ],
                'Bla' => [
                    'Bla Centre', 'Bananiba', 'Diaramana',
                    'Falo', 'Konobougou', 'Marka',
                    'Sarro', 'Sido', 'Sorobasso', 'Tominian'
                ],
                'Macina' => [
                    'Macina Centre', 'Boky-Wèrè', 'Diabaly',
                    'Kolongo', 'Markala', 'Saloba',
                    'San', 'Saro', 'Souba', 'Tenenkou'
                ],
            ],

            // ═══════════════════════════════════
            // 6. RÉGION DE MOPTI
            // ═══════════════════════════════════
            'Mopti' => [
                'Mopti' => [
                    'Mopti Centre', 'Gangal', 'Komoguel',
                    'Médina', 'Mossinkoré', 'Sévaré',
                    'Socoura', 'Tolober', 'Wouro', 'Pianou'
                ],
                'Djenné' => [
                    'Djenné Centre', 'Fakala', 'Femaye',
                    'Kéwa', 'Koroboro', 'Mopti-Sud',
                    'Ouro Ali', 'Sambéré', 'Sahona', 'Soufroulaye'
                ],
                'Bandiagara' => [
                    'Bandiagara Centre', 'Bankass', 'Béni',
                    'Doucombo', 'Kani-Bonzon', 'Koporo',
                    'Lowol', 'Sangha', 'Tori', 'Wakara'
                ],
                'Douentza' => [
                    'Douentza Centre', 'Dangol-Bore', 'Débéré',
                    'Dianke', 'Dialloubé', 'Hombori',
                    'Keré', 'Mondoro', 'Pérou', 'Tondogosso'
                ],
                'Tenenkou' => [
                    'Tenenkou Centre', 'Dionwari', 'Djenné-Djeno',
                    'Fatoma', 'Madiama', 'Ouro Guiré',
                    'Soye', 'Tériya Bugu', 'Toguéré Coumbé', 'Wuro Neema'
                ],
            ],

            // ═══════════════════════════════════
            // 7. RÉGION DE TOMBOUCTOU
            // ═══════════════════════════════════
            'Tombouctou' => [
                'Tombouctou' => [
                    'Tombouctou Centre', 'Abaradjou', 'Alafia',
                    'Bellafarandi', 'Djinguereber', 'Hamabangou',
                    'Kabara', 'Sankoré', 'Sareïkeyna', 'Yobu'
                ],
                'Diré' => [
                    'Diré Centre', 'Arham', 'Bourem-Inaly',
                    'Daka', 'Garbakoira', 'Haïbongo',
                    'Kirchamba', 'Saréféré', 'Tindirma', 'Tonka'
                ],
                'Goundam' => [
                    'Goundam Centre', 'Bintagoungou', 'Gargando',
                    'Issa-Bèry', 'Karmakarou', 'Kidal',
                    'Léré', 'Niafounké', 'Tilemsi', 'Tonka'
                ],
                'Niafounké' => [
                    'Niafounké Centre', 'Banikane', 'Bourem-Sidi-Amar',
                    'Fittouga', 'Goundam', 'Léré',
                    'Soumpi', 'Sounna', 'Tindirma', 'Youwarou'
                ],
            ],

            // ═══════════════════════════════════
            // 8. RÉGION DE GAO
            // ═══════════════════════════════════
            'Gao' => [
                'Gao' => [
                    'Gao Centre', 'Château', 'Farandjireye',
                    'Gadès', 'Kabara', 'Sosso-Koïra',
                    'Wabaria', 'Zouladeinit', 'N\'Tillit', 'Doro'
                ],
                'Ansongo' => [
                    'Ansongo Centre', 'Bara', 'Bentia',
                    'Bourra', 'In-Tillit', 'Ménaka',
                    'Ouatagouna', 'Talataye', 'Tessit', 'Tin-Hama'
                ],
                'Bourem' => [
                    'Bourem Centre', 'Bamba', 'Gossi',
                    'Hombori', 'In-Delimane', 'Kadji',
                    'Ouatagouna', 'Soni Ali Ber', 'Taboye', 'Temera'
                ],
            ],

            // ═══════════════════════════════════
            // 9. RÉGION DE KIDAL
            // ═══════════════════════════════════
            'Kidal' => [
                'Kidal' => [
                    'Kidal Centre', 'Abeïbara', 'Aguelhok',
                    'Anéfis', 'Essouk', 'In-Khalil',
                    'Tessalit', 'Tin-Essako', 'Timétrine', 'Tombouctou'
                ],
                'Tessalit' => [
                    'Tessalit Centre', 'Aguelhok', 'Aoukar',
                    'Bouressa', 'In-Khalil', 'Tabankort',
                    'Timétrine', 'Tinzaouatène', 'Inalogassa', 'Essouk'
                ],
            ],

            // ═══════════════════════════════════
            // 10. RÉGION DE MÉNAKA
            // ═══════════════════════════════════
            'Ménaka' => [
                'Ménaka' => [
                    'Ménaka Centre', 'Alata', 'Anderamboukane',
                    'Bourra', 'In-Delimane', 'Inékar',
                    'Talataye', 'Tidermène', 'Tin-Hama', 'Tessit'
                ],
                'Anderamboukane' => [
                    'Anderamboukane Centre', 'Alata', 'Bourra',
                    'In-Delimane', 'Inekar', 'Menaka',
                    'Talataye', 'Tidermène', 'Tin-Hama', 'Tessit'
                ],
            ],

            // ═══════════════════════════════════
            // 11. RÉGION DE TAOUDÉNI
            // ═══════════════════════════════════
            'Taoudéni' => [
                'Taoudéni' => [
                    'Taoudéni Centre', 'Araouane', 'Ber',
                    'Bintagoungou', 'Boudjbeha', 'Foum-Gleita',
                    'Goundam', 'Léré', 'Mazeras', 'Tilemsi'
                ],
                'Araouane' => [
                    'Araouane Centre', 'Ber', 'Bintagoungou',
                    'El Bour', 'Foum-Gleita', 'Mazeras',
                    'Rharous', 'Tilemsi', 'Tin-Zaouatène', 'Tombouctou'
                ],
            ],
        ];

        foreach ($data as $nomReg => $villes) {
            $regId = DB::table('departements')->insertGetId([
                'nom_departement' => $nomReg,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            foreach ($villes as $nomVille => $quartiers) {
                $villeId = DB::table('villes')->insertGetId([
                    'id_departement' => $regId,
                    'nom_ville'      => $nomVille,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                foreach ($quartiers as $nomQuartier) {
                    DB::table('quartiers')->insert([
                        'id_ville'      => $villeId,
                        'nom_quartier'  => $nomQuartier,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ ' . count($data) . ' régions du Mali créées !');
        $this->command->info('✅ ' . DB::table('villes')->count() . ' villes créées !');
        $this->command->info('✅ ' . DB::table('quartiers')->count() . ' quartiers/communes créés !');
    }
}
