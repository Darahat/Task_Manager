<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard - Task Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .project-card {
            transition: all 0.3s ease;
        }
        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .project-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Project Dashboard</h1>
                        <p class="text-gray-600 mt-2">Manage your projects and tasks efficiently</p>
                    </div>
                    <button onclick="showCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Project</span>
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Projects Grid -->
            @if($projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <div class="project-card bg-white rounded-lg shadow-md overflow-hidden">
                            <!-- Project Header with Color -->
                            <div class="h-4" style="background-color: {{ $project->color }}"></div>

                            <!-- Project Content -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <span class="project-color" style="background-color: {{ $project->color }}"></span>
                                        <h3 class="text-xl font-semibold text-gray-800">{{ $project->name }}</h3>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button onclick="editProject({{ $project->id }}, '{{ $project->name }}', '{{ $project->description }}', '{{ $project->color }}')"
                                                class="text-blue-600 hover:text-blue-800 p-1 rounded transition duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteProject({{ $project->id }})"
                                                class="text-red-600 hover:text-red-800 p-1 rounded transition duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @if($project->description)
                                    <p class="text-gray-600 mb-4">{{ Str::limit($project->description, 100) }}</p>
                                @endif

                                <!-- Task Count -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span>{{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Created {{ $project->created_at->format('M d, Y') }}
                                    </div>
                                </div>

                                <!-- View Tasks Button -->
                                <a href="{{ route('taskmanagement.index', $project->id) }}"
                                   class="block w-full bg-gray-50 hover:bg-gray-100 text-center py-3 rounded-lg transition duration-200 font-medium text-gray-700">
                                    View Tasks
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-gray-400 text-8xl mb-6">📁</div>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-3">No projects yet!</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Get started by creating your first project. Organize your tasks by grouping them into projects.
                    </p>
                    <button onclick="showCreateModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg transition duration-200 inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Your First Project</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

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

    <script>
        let editingProjectId = null;

        // CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
</body>
</html>
