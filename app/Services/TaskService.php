<?php

namespace App\Services;

use App\Jobs\GenerateAISummaryJob;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $repository,
        protected AIService $aiService
    ) {}

    public function getAll(array $filters)
    {
        return $this->repository->all($filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $task = $this->repository->create($data);
            
            GenerateAISummaryJob::dispatch($task);

            return $task;
        });
    }

    public function update(int $id, array $data)
    {
        $task = $this->find($id);
        
        $needsNewSummary = ($task->title !== ($data['title'] ?? $task->title)) || 
                           ($task->description !== ($data['description'] ?? $task->description));

        $updatedTask = $this->repository->update($id, $data);

        if ($needsNewSummary) {
            GenerateAISummaryJob::dispatch($updatedTask);
        }

        return $updatedTask;
    }

    public function destroy(int $id)
    {
        return $this->repository->delete($id);
    }
}
