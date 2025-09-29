<?php

namespace Modules\TaskManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaskManagement\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display the project dashboard with all projects.
     */
    public function dashboard()
    {
        $projects = Project::forUser(Auth::id())->withCount('tasks')->orderBy('name')->get();
        return view('taskmanagement::projects.dashboard', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taskmanagement::projects.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $project = Project::forUser(Auth::id())->findOrFail($id);
        return view('taskmanagement::projects.edit', compact('project'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::forUser(Auth::id())->withCount('tasks')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#3B82F6',
            'user_id' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'project' => $project->load('tasks'),
                'message' => 'Project created successfully!'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $project = Project::forUser(Auth::id())->with('tasks')->findOrFail($id);

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $project = Project::forUser(Auth::id())->findOrFail($id);
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? $project->color,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'project' => $project->load('tasks'),
                'message' => 'Project updated successfully!'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::forUser(Auth::id())->findOrFail($id);

        // Move tasks to no project (set project_id to null)
        $project->tasks()->update(['project_id' => null]);

        $project->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully!'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}