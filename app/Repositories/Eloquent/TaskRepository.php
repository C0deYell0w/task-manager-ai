<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters)
    {
        $cacheKey = 'tasks_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, 60, function () use ($filters) {
            return Task::query()
                ->select(['id', 'title', 'status', 'priority', 'due_date', 'ai_summary', 'assigned_to', 'created_at'])
                ->with('assignedUser:id,name')
                ->filter($filters)
                ->latest()
                ->paginate(10);
        });
    }

    public function find(int $id)
    {
        return Task::findOrFail($id);
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update(int $id, array $data)
    {
        $task = $this->find($id);
        $task->update($data);

        return $task;
    }

    public function delete(int $id)
    {
        $task = $this->find($id);
        
        return $task->delete();
    }
}
