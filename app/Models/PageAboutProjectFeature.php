<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageAboutProjectFeature extends Model
{
    protected $table = 'page_about_project_features';

    protected $fillable = ['sort_order', 'icon'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageAboutProjectFeatureTranslation::class, 'page_about_project_feature_id');
    }

    public function translation(string $locale): ?PageAboutProjectFeatureTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
