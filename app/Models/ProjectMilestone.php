<?php

namespace App\Models;

use App\Enums\MilestoneStatus;
use Database\Factories\ProjectMilestoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    /** @use HasFactory<ProjectMilestoneFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'progress',
        'due_date',
        'completed_at',
        'position',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MilestoneStatus::class,
            'progress' => 'integer',
            'due_date' => 'date',
            'completed_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'milestone_id');
    }
}
