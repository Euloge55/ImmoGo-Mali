<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->string('fedapay_secret_key')->nullable()->after('logo')
                  ->comment('Clé secrète FedaPay (sk_sandbox_... ou sk_live_...)');
            $table->string('fedapay_env')->default('sandbox')->after('fedapay_secret_key')
                  ->comment('sandbox ou live');
        });
    }

    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn(['fedapay_secret_key', 'fedapay_env']);
        });
    }
};
