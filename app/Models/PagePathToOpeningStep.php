<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PagePathToOpeningStep extends Model
{
    protected $table = 'page_path_to_opening_steps';

    protected $fillable = ['status', 'sort_order'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PagePathToOpeningStepTranslation::class, 'page_path_to_opening_step_id');
    }

    public function translation(string $locale): ?PagePathToOpeningStepTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
