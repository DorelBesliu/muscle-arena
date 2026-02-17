<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageAboutProjectFeatureTranslation extends Model
{
    protected $table = 'page_about_project_features_translations';

    protected $fillable = [
        'page_about_project_feature_id',
        'locale',
        'title',
        'description',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(PageAboutProjectFeature::class, 'page_about_project_feature_id');
    }
}
