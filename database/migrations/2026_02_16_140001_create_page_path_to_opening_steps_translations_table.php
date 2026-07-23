<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_path_to_opening_steps_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_path_to_opening_step_id');
            $table->foreign('page_path_to_opening_step_id', 'path_step_trans_step_id_fk')
                ->references('id')->on('page_path_to_opening_steps')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description');
            $table->timestamps();

            $table->unique(['page_path_to_opening_step_id', 'locale'], 'path_step_trans_step_locale_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_path_to_opening_steps_translations');
    }
};
