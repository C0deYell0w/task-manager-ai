<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class DashboardService
{
    /**
     * Calculate and return all necessary metrics for the dashboard view.
     * Scope the metrics to the specific user if they are not an admin.
     *
     * @param User $user The authenticated user requesting the dashboard
     * @return array The calculated statistics, priority breakdown, and AI summary metrics
     */
    public function getDashboardMetrics(User $user): array
    {
        $query = Task::query();

        if (! $user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        $totalTasks = (clone $query)->count();
        $completedTasks = (clone $query)->where('status', 'completed')->count();
        $inProgressTasks = (clone $query)->where('status', 'in_progress')->count();
        $pendingTasks = (clone $query)->where('status', 'pending')->count();

        $byPriority = [
            'high' => (clone $query)->where('priority', 'high')->count(),
            'medium' => (clone $query)->where('priority', 'medium')->count(),
            'low' => (clone $query)->where('priority', 'low')->count(),
        ];

        $aiSummariesByPriority = [
            'high' => (clone $query)->whereNotNull('ai_summary')->where('priority', 'high')->count(),
            'medium' => (clone $query)->whereNotNull('ai_summary')->where('priority', 'medium')->count(),
            'low' => (clone $query)->whereNotNull('ai_summary')->where('priority', 'low')->count(),
        ];

        return [
            'stats' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'pending' => $pendingTasks,
            ],
            'priority' => $byPriority,
            'ai_summaries' => $aiSummariesByPriority,
        ];
    }
}
