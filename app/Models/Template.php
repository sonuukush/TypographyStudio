<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name', 'slug', 'font_family', 'primary_color', 'secondary_color',
        'background_color', 'animation_type', 'config_json', 'preview_thumbnail',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'config_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function renders(): HasMany
    {
        return $this->hasMany(Render::class);
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
