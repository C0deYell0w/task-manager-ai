<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Inject the TaskService dependency.
     */
    public function __construct(
        protected TaskService $taskService,
        protected UserService $userService
    ) {}

    /**
     * Display a paginated list of tasks, applying filters and user scoping.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'priority']);
        
        if (! $request->user()->isAdmin()) {
            $filters['assigned_to'] = $request->user()->id;
        }

        $tasks = $this->taskService->getAll($filters);

        return Inertia::render('Tasks/Index', [
            'tasks' => \App\Http\Resources\TaskListResource::collection($tasks),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        Gate::authorize('create', \App\Models\Task::class);

        return Inertia::render('Tasks/Create', [
            'users' => $this->userService->getAdminDropdownUsers(),
        ]);
    }

    /**
     * Store a newly created task in storage and dispatch AI summary job.
     *
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        
        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $request->user()->id;
        }

        $this->taskService->store($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully and AI is generating a summary!');
    }

    /**
     * Display the specified task details.
     *
     * @param Task $task
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $task = $this->taskService->find($id);
        Gate::authorize('view', $task);

        $task->load('assignedUser');

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param Task $task
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $task = $this->taskService->find($id);
        Gate::authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => new TaskResource($task),
            'users' => $this->userService->getAdminDropdownUsers(),
        ]);
    }

    /**
     * Update the specified task in storage.
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = $this->taskService->find($id);
        
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $task->assigned_to;
        }

        $this->taskService->update($task->id, $data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Synchronously refresh the AI summary for a given task.
     *
     * @param Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshSummary($id)
    {
        $task = $this->taskService->find($id);
        Gate::authorize('update', $task);

        try {
            \App\Jobs\GenerateAISummaryJob::dispatchSync($task);
            return back()->with('success', 'AI Summary refreshed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $task = $this->taskService->find($id);
        Gate::authorize('delete', $task);

        $this->taskService->destroy($task->id);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
