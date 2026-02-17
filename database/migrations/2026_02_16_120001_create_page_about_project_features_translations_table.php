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
        Schema::create('page_about_project_features_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_about_project_feature_id');
            $table->foreign('page_about_project_feature_id', 'about_feat_trans_feature_id_fk')
                ->references('id')->on('page_about_project_features')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description');
            $table->timestamps();

            $table->unique(['page_about_project_feature_id', 'locale'], 'about_feat_trans_feature_locale_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_about_project_features_translations');
    }
};
