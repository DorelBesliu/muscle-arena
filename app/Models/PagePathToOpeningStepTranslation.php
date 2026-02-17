<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagePathToOpeningStepTranslation extends Model
{
    protected $table = 'page_path_to_opening_steps_translations';

    protected $fillable = [
        'page_path_to_opening_step_id',
        'locale',
        'title',
        'description',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(PagePathToOpeningStep::class, 'page_path_to_opening_step_id');
    }
}
