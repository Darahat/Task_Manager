@extends('layouts.app')

@section('title', 'Project Dashboard')

@section('header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Project Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage your projects and tasks efficiently</p>
    </div>
    <button onclick="showCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span>Create Project</span>
    </button>
</div>
@endsection
@section('content')
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Project Selection Dropdown -->
    @if($projects->count() > 0)
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select a Project</h3>
                <div class="space-y-4">
                    <select id="projectSelect" onchange="goToProject()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                        <option value="">Choose a project...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" data-color="{{ $project->color }}">
                                {{ $project->name }} ({{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Selected Project Info -->
                    <div id="projectInfo" class="hidden bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-3 mb-2">
                            <span id="projectColorIndicator" class="w-4 h-4 rounded-full"></span>
                            <span id="projectName" class="font-medium text-gray-800"></span>
                        </div>
                        <p id="projectDescription" class="text-gray-600 text-sm mb-3"></p>
                        <button onclick="goToSelectedProject()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition duration-200">
                            View Tasks
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="text-gray-400 text-8xl mb-6">📁</div>
            <h3 class="text-2xl font-semibold text-gray-600 mb-3">No projects yet!</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Get started by creating your first project. Organize your tasks by grouping them into projects.
            </p>
            <button onclick="showCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg transition duration-200 inline-flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Create Your First Project</span>
            </button>
        </div>
    @endif

    <!-- Create/Edit Project Modal -->
    <div id="projectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Create New Project</h2>
            <form id="projectForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="mb-4">
                    <label for="projectName" class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
                    <input type="text" id="projectName" name="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter project name" required>
                </div>

                <div class="mb-4">
                    <label for="projectDescription" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="projectDescription" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter project description"></textarea>
                </div>

                <div class="mb-6">
                    <label for="projectColor" class="block text-sm font-medium text-gray-700 mb-2">Project Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="projectColor" name="color" value="#3B82F6"
                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer">
                        <div class="grid grid-cols-6 gap-2">
                            <button type="button" onclick="setColor('#3B82F6')" class="w-6 h-6 rounded-full" style="background-color: #3B82F6"></button>
                            <button type="button" onclick="setColor('#EF4444')" class="w-6 h-6 rounded-full" style="background-color: #EF4444"></button>
                            <button type="button" onclick="setColor('#10B981')" class="w-6 h-6 rounded-full" style="background-color: #10B981"></button>
                            <button type="button" onclick="setColor('#F59E0B')" class="w-6 h-6 rounded-full" style="background-color: #F59E0B"></button>
                            <button type="button" onclick="setColor('#8B5CF6')" class="w-6 h-6 rounded-full" style="background-color: #8B5CF6"></button>
                            <button type="button" onclick="setColor('#EC4899')" class="w-6 h-6 rounded-full" style="background-color: #EC4899"></button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideModal()"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                        Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endsection

@push('styles')
<style>
    #projectSelect {
         background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>
@endpush

@push('scripts')
<script>
    let editingProjectId = null;
    let projectsData = @json($projects);

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Dropdown functionality
    function goToProject() {
        const select = document.getElementById('projectSelect');
        const selectedId = select.value;

        if (selectedId) {
            const selectedProject = projectsData.find(p => p.id == selectedId);
            if (selectedProject) {
                // Show project info
                document.getElementById('projectInfo').classList.remove('hidden');
                document.getElementById('projectColorIndicator').style.backgroundColor = selectedProject.color;
                document.getElementById('projectName').textContent = selectedProject.name;
                document.getElementById('projectDescription').textContent = selectedProject.description || 'No description available';
            }
        } else {
            document.getElementById('projectInfo').classList.add('hidden');
        }
    }

    function goToSelectedProject() {
        const select = document.getElementById('projectSelect');
        const selectedId = select.value;

        if (selectedId) {
            window.location.href = `/projects/${selectedId}/tasks`;
        }
    }

    function showCreateModal() {
        editingProjectId = null;
        document.getElementById('modalTitle').textContent = 'Create New Project';
        document.getElementById('projectForm').action = '{{ route("projects.store") }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('projectName').value = '';
        document.getElementById('projectDescription').value = '';
        document.getElementById('projectColor').value = '#3B82F6';
        document.getElementById('projectModal').classList.remove('hidden');
        document.getElementById('projectModal').classList.add('flex');
        document.getElementById('projectName').focus();
    }

    function editProject(id, name, description, color) {
        editingProjectId = id;
        document.getElementById('modalTitle').textContent = 'Edit Project';
        document.getElementById('projectForm').action = `/projects/${id}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('projectName').value = name;
        document.getElementById('projectDescription').value = description || '';
        document.getElementById('projectColor').value = color;
        document.getElementById('projectModal').classList.remove('hidden');
        document.getElementById('projectModal').classList.add('flex');
        document.getElementById('projectName').focus();
    }

    function hideModal() {
        document.getElementById('projectModal').classList.add('hidden');
        document.getElementById('projectModal').classList.remove('flex');
        editingProjectId = null;
    }

    function setColor(color) {
        document.getElementById('projectColor').value = color;
    }

    function deleteProject(id) {
        if (confirm('Are you sure you want to delete this project? All tasks will be moved to "No Project".')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/projects/${id}`;
            form.innerHTML = `
                @csrf
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('projectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideModal();
        }
    });
</script>
@endpush
