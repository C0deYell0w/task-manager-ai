<?php

namespace App\Services;

use App\Jobs\GenerateAISummaryJob;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * Inject required dependencies.
     */
    public function __construct(
        protected TaskRepositoryInterface $repository,
        protected AIService $aiService
    ) {}

    /**
     * Retrieve all tasks based on the provided filters.
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters)
    {
        return $this->repository->all($filters);
    }

    /**
     * Find a task by its ID.
     *
     * @param int $id
     * @return Task
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Store a new task and dispatch the AI summary job.
     *
     * @param array $data
     * @return Task
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $task = $this->repository->create($data);
            
            GenerateAISummaryJob::dispatch($task);

            return $task;
        });
    }

    /**
     * Update an existing task and conditionally dispatch a new AI summary job.
     *
     * @param int $id
     * @param array $data
     * @return Task
     */
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

    /**
     * Delete a task by its ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function destroy(int $id)
    {
        return $this->repository->delete($id);
    }
}
