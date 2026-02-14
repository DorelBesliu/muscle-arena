<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_items')) {
            Schema::dropIfExists('subscription_items');
        }
        if (Schema::hasTable('subscriptions')) {
            Schema::dropIfExists('subscriptions');
        }
        if (Schema::hasTable('users')) {
            $columnsToDrop = array_filter(
                ['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'],
                fn (string $col) => Schema::hasColumn('users', $col)
            );
            if ($columnsToDrop !== []) {
                Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
        }
    }

    public function down(): void
    {
        // Not restoring subscription tables / customer columns
    }
};
