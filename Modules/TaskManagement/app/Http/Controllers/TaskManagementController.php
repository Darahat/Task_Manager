<?php

namespace Modules\TaskManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaskManagement\Models\Task;
use Modules\TaskManagement\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TaskManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $project = null)
    {
        // If project is passed as route parameter, use it
        if ($project) {
            $projectModel = Project::forUser(Auth::id())->findOrFail($project);
            $tasks = Task::forUser(Auth::id())->forProject($projectModel->id)->with('project')->byPriority()->get();
            return view('taskmanagement::index', compact('tasks', 'projectModel'));
        }

        // Otherwise, use query parameter filtering (for backward compatibility)
        $query = Task::forUser(Auth::id())->with('project');

        // Filter by project if specified
        if ($request->has('project_id') && $request->project_id !== '') {
            if ($request->project_id === 'no_project') {
                $query->whereNull('project_id');
            } else {
                $query->forProject($request->project_id);
            }
        }

        $tasks = $query->byPriority()->get();
        $projects = Project::forUser(Auth::id())->withCount('tasks')->orderBy('name')->get();

        return view('taskmanagement::index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($project = null)
    {
         $projects = Project::forUser(Auth::id())->orderBy('name')->get();
        $selectedProject = null;

        if ($project) {
            $selectedProject = Project::forUser(Auth::id())->findOrFail($project);
        }

        return view('taskmanagement::create', compact('projects', 'selectedProject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        // Get the next priority based on project scope
        $query = Task::forUser(Auth::id());
        if ($request->project_id) {
            $query->forProject($request->project_id);
        } else {
            $query->whereNull('project_id');
        }
        $maxPriority = $query->max('priority') ?? 0;

        $task = Task::create([
            'name' => $request->name,
            'priority' => $maxPriority + 1,
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task->load('project'),
                'message' => 'Task created successfully!'
            ]);
        }

        return redirect()->route('taskmanagement.index')
            ->with('success', 'Task created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $task = Task::forUser(Auth::id())->findOrFail($id);
        return view('taskmanagement::show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $task = Task::forUser(Auth::id())->with('project')->findOrFail($id);
        $projects = Project::forUser(Auth::id())->orderBy('name')->get();
        return view('taskmanagement::edit', compact('task', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        // dd($request->all());
         $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $task = Task::forUser(Auth::id())->findOrFail($request->task_id);
        $task->update([
            'name' => $request->name,
            'project_id' => $request->project_id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task->load('project'),
                'message' => 'Task updated successfully!'
            ]);
        }

        return redirect()->route('taskmanagement.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::forUser(Auth::id())->findOrFail($id);
        $task->delete();

        // Reorder remaining tasks
        $this->reorderTasks();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);
        }

        return redirect()->route('taskmanagement.index')
            ->with('success', 'Task deleted successfully!');
    }

    /**
     * Update task priorities based on drag and drop reordering.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        $taskIds = $request->task_ids;

        // Update priorities based on the new order
        foreach ($taskIds as $index => $taskId) {
            Task::forUser(Auth::id())
                ->where('id', $taskId)
                ->update(['priority' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasks reordered successfully!'
        ]);
    }

    /**
     * Helper method to reorder tasks after deletion.
     */
    private function reorderTasks()
    {
        $tasks = Task::forUser(Auth::id())->byPriority()->get();

        foreach ($tasks as $index => $task) {
            $task->update(['priority' => $index + 1]);
        }
    }
}