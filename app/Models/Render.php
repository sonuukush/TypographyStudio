<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Render extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'input_text', 'scenes_json', 'status',
        'output_file_path', 'error_message',
    ];

    protected $casts = [
        'scenes_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isProcessing(): bool { return $this->status === 'processing'; }
    public function isDone(): bool      { return $this->status === 'done'; }
    public function isFailed(): bool    { return $this->status === 'failed'; }
}
