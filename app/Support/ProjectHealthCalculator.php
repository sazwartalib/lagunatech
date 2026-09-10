<?php

namespace App\Support;

use App\Enums\ProjectHealth;
use App\Enums\ProjectStatus;
use App\Models\Project;

/**
 * Derives a project's delivery health from its status, deadline and progress.
 *
 * Rules (first match wins):
 *  - Completed / Cancelled            -> Completed
 *  - Past the target end date         -> Overdue
 *  - Deadline near and progress lags  -> At Risk
 *  - Otherwise                        -> On Track
 *
 * "Progress lags" means the elapsed share of the schedule is more than
 * 15 percentage points ahead of reported progress.
 */
class ProjectHealthCalculator
{
    public function for(Project $project): ProjectHealth
    {
        if (in_array($project->status, [ProjectStatus::Completed, ProjectStatus::Cancelled], true)) {
            return ProjectHealth::Completed;
        }

        $deadline = $project->target_end_date;

        if ($deadline === null) {
            return ProjectHealth::OnTrack;
        }

        $today = now()->startOfDay();

        if ($deadline->isBefore($today)) {
            return ProjectHealth::Overdue;
        }

        $daysToDeadline = $today->diffInDays($deadline, false);
        $progress = (int) $project->progress;

        if ($daysToDeadline <= 7 && $progress < 90) {
            return ProjectHealth::AtRisk;
        }

        $start = $project->start_date;

        if ($start !== null && $start->isBefore($deadline)) {
            $totalDays = max(1, $start->diffInDays($deadline));
            $elapsedDays = max(0, $start->diffInDays($today));
            $expectedProgress = min(100, (int) round($elapsedDays / $totalDays * 100));

            if ($expectedProgress - $progress > 15) {
                return ProjectHealth::AtRisk;
            }
        }

        return ProjectHealth::OnTrack;
    }
}
