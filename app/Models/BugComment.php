<?php

namespace App\Models;

use Database\Factories\BugCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugComment extends Model
{
    /** @use HasFactory<BugCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'bug_id',
        'user_id',
        'body',
    ];

    public function bug(): BelongsTo
    {
        return $this->belongsTo(Bug::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
