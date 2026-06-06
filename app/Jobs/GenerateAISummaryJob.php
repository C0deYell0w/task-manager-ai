<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\AIService;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAISummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function handle(AIService $aiService, TaskRepositoryInterface $repository): void
    {
        // Don't generate if the task was already deleted
        if (! $this->task->exists) {
            return;
        }

        $summaryData = $aiService->generateSummary($this->task);

        $repository->update($this->task->id, [
            'ai_summary' => $summaryData['ai_summary'],
            'ai_priority' => $summaryData['ai_priority']
        ]);
    }
}
