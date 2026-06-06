<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'priority']);
        
        if (! $request->user()->isAdmin()) {
            $filters['assigned_to'] = $request->user()->id;
        }

        $tasks = $this->taskService->getAll($filters);

        return Inertia::render('Tasks/Index', [
            'tasks' => TaskResource::collection($tasks),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Task::class);

        return Inertia::render('Tasks/Create', [
            'users' => auth()->user()->isAdmin() ? User::select('id', 'name')->get() : [],
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        
        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $request->user()->id;
        }

        $this->taskService->store($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully and AI is generating a summary!');
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        $task->load('assignedUser');

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => new TaskResource($task),
            'users' => auth()->user()->isAdmin() ? User::select('id', 'name')->get() : [],
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $task->assigned_to;
        }

        $this->taskService->update($task->id, $data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $this->taskService->destroy($task->id);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
