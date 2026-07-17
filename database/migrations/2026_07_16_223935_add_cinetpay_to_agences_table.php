<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->string('cinetpay_site_id')->nullable()->after('logo');
            $table->string('cinetpay_api_key')->nullable()->after('cinetpay_site_id');
            $table->string('cinetpay_env')->default('TEST')->after('cinetpay_api_key'); // TEST ou PROD
        });
    }

    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn(['cinetpay_site_id', 'cinetpay_api_key', 'cinetpay_env']);
        });
    }
};
