<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            // Conditions de location (optionnelles)
            $table->unsignedTinyInteger('nb_mois_avance')
                  ->nullable()->default(null)
                  ->after('type_contrat')
                  ->comment('Nombre de mois d\'avance exigés');

            $table->decimal('caution_eau', 12, 2)
                  ->nullable()->default(null)
                  ->after('nb_mois_avance')
                  ->comment('Caution eau (CFA)');

            $table->decimal('caution_electricite', 12, 2)
                  ->nullable()->default(null)
                  ->after('caution_eau')
                  ->comment('Caution électricité (CFA)');
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['nb_mois_avance', 'caution_eau', 'caution_electricite']);
        });
    }
};
