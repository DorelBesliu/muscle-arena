<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('members', 'amount')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('email');
        });
    }
};
