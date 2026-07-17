<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::updateOrCreate(
            ['email' => 'zoumanadiabate48@gmail.com'],
            [
                'nom_superadmin' => 'Zoumana Diabaté',
                'email'          => 'zoumanadiabate48@gmail.com',
                'mot_de_passe'   => Hash::make('2004200z'),
            ]
        );

        $this->command->info('✅ Super Admin créé : zoumanadiabate48@gmail.com / 2004200z');
    }
}
